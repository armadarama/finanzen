<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: text/html; charset=utf-8");
require_once("importVerknuepfungen.php");
require_once("sql_class/sql_con.php");
require_once("import/permutation.php");
require_once("import/getHSBCFile.php");
require_once("import/getRaibaFile.php");
require_once("import/getHVBFile.php");
require_once("import/getSql.php");
require_once("import/checkData.php");
require_once("import/PhpTemplate.php");
require_once("import/intelligenz.php");
require_once("import/showKategorienImport.php");

//  Importiert die CSV-Datei der Raiffeisenbank Großostheim in die Finanzen-DB  \\
//------------------------------------------------------------------------------\\
//------------------------------------------------------------------------------\\
//------------------------------------------------------------------------------\\

class Import {

	private $content;
	private $sql;
	private $timeA;
	private $timeB;
	private $showUploadForm = false;
	private $account = "";
	
	function startImport(){
		$this->timeA = microtime(true);
		$this->content = "";
		// SQL Verbindung herstellen
		$this->sql = new sql("blabla");
		$target_path = $this->uploadFile();
		if($target_path == false){
			return $this->content;
		}else{
			return $this->getFile($target_path);
		}
	}

	function uploadFile(){
		// Wenn keine Datei hochgeladen gib Upload aus;
		if(@$_REQUEST["filename"] == NULL){
			if(@$_FILES['uploadedfile']['name'] == NULL){
				$this->showUploadForm = true;
				$tpl = new PhpTemplate(dirname(__FILE__).'/template.phtml');
				$this->content .= $tpl->render(array(
					"showUploadForm" => $this->showUploadForm,
				));
				$target_path = false;
			// Wenn Datei angegeben wurde,  lade Datei hoch und zeige Inhalte an
			}elseif($_FILES['uploadedfile']['name'] != NULL){
				// Neue Daten aus Datei exportieren
				$target_path = "import/uploads/";

				$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
					$this->content .= "Die Datei ".  basename( $_FILES['uploadedfile']['name']). 
					" wurde erfolgreich hochgeladen.<br><br>";
				}else{
					$this->content .= "Beim Hochladen der Datei trat ein Fehler auf, bitte versuchen Sie es erneut!";
				}
				$this->account = $_REQUEST['account'];
			}
		// Wenn Kategorien ausgewählt sind speichere Inhalte
		}else{
			$target_path = $_REQUEST["filename"];
		}
		return $target_path;
	}
	
	function getFile($target_path){
		$account = $this->account;
		if($account == "raiba_giro"){
			$getFile = new getRaibaFile();
		}elseif($account == "hsbc_debit" || $account == "hsbc_savings"){
			$getFile = new getHSBCFile();
		}
		$getFile->startGettingFile($target_path, $this->sql);

		$neueDatenGesamt = $getFile->getDatenArray();
		$endsaldo = $getFile->getEndsaldo();
		
		// Prüfen welche Daten aus der CSV Datei bereits in der DB sind
		if($account == "raiba_giro"){
			$neueDatenAusgemistet = checkData($neueDatenGesamt, $this->sql);
		}elseif($account == "hsbc_debit" || $account == "hsbc_savings"){
			$neueDatenAusgemistet = $neueDatenGesamt;
		}
		$anzahlAusgemisteterDaten = count($neueDatenAusgemistet);

		$save = false;
		$neueEintraege = array();
		$bestehendeCSVImportErfolgreich = false;
		$gesamtZahl = 0;


		// Wenn Speichermodus und raiba_giro, ansonsten kann der Schritt mit der Auswahl
		// 	der Kategorien nicht ausgefuehrt werden. Fuer HSBC Konten kann der folgende Code
		// 	allerdings direkt ausgefuehrt werden.
		if((bool)@$_POST["save"] && $account == "raiba_giro" || $account != "raiba_giro"){
			$save = true;
			$postDaten = array();
			$vorhandeneCSVDaten = array();
			
			// Daten aus Post holen, wenn raiba_giro
			if($account == "raiba_giro"){
				foreach($_POST as $key => $value){ 
					if($key == "save")continue;
					if($key == "filename")continue;
					$key = explode("-",$key);
					$id = $key[0];
					$attribut = $key[1];
					$postDaten[$id][$attribut] = trim($value);
				}
				if(count($postDaten)<1){
					$this->content .= "Es gibt keine Daten zum absenden...";
					return $this->content;
				}
				// Informationen zusammenführen
				$neueDatenGesamtTemp = array();
				foreach($neueDatenAusgemistet as $neuerCSVEintrag){
					$id = (int)$neuerCSVEintrag["id"];
					if(!array_key_exists($id, $postDaten)){continue;}
					$neuerCSVEintrag = array_merge($neuerCSVEintrag, $postDaten[$id]);
					
					// Prüfen welche Daten laut HTML Formular bereits in der DB sind
					if(@$neuerCSVEintrag["vorhanden"] == "vorhanden"){
						$vorhandeneCSVDaten[$id] = $neuerCSVEintrag;
					}else{
						// Wenn keine Kategorie angegeben ist, wird nichts unternommen
						if(@$neuerCSVEintrag["kategorie"] != ""){
							$neueEintraege[$id] = $neuerCSVEintrag;
						}
					}
				}
			}else{
				$neueEintraege = $neueDatenAusgemistet;
			}

			
			
			// Vorhandene Daten in DB Tabelle csvimport speichern.
			if(count($vorhandeneCSVDaten)>0 && $account == "raiba_giro"){
				$bestehendeCSVImportErfolgreich = insertVorhandeneCsv($vorhandeneCSVDaten, $this->sql, $account);
			}
			
			// Neue Einträge in die DB speichern
			$gesamtZahl = insertData($neueEintraege, $this->sql, $account);
			if(count($neueEintraege)>0 ){
				$neueCSVImportErfolgreich = insertVorhandeneCsv($neueEintraege, $this->sql, $account);
			}
			
			// Prüft die Kontostände
			$newEndsaldo = getNewEndsaldo($this->sql, $account);
			$this->content .= "Kontostand laut ".$account.": ".$endsaldo."<br>";
			$this->content .= "Kontostand laut Finanzeprogramm: ".$newEndsaldo."<br>"."<br>";
			if($endsaldo != $newEndsaldo){
				"Etwas ist schief gelaufen. Der neue Kontostand ist nicht korrekt! Eventuell wurde ein Datensatz aufGrund einer bereits vorhandenen Id nicht eingetragen.";
			}
		}


		// restliche Daten aufführen mit checkbox, welche Daten eingepflegt werden sollen
		// 			bzw. bereits eingepflegt wurden.
		// alle Checkboxen sind vorausgewählt
		// ---
		// Es gibt noch eine Verwendungszweckfeld, das mit daten befüllt wird, aber änderbar ist
		// Geänderte Verwendungszwecke werden in einer eigenen Tabelle gespeichert
		// ---
		// Zusätzlich gibt es eine Kategorie die Ausgewählt werden muss
		// ---
		// Dadruch wird ein Gedächtnis kreiert
		// Tabellenfelder: Ursprung - Neu - Kategorie
		
		// Durch diese Variable werden nicht mehr als 25 Daten aufgelistet und somit ein Überlauf von 100en von Daten ausgeschlossen;
		$neueDatenAusgemistetMitUeberlaufAbsicherung = array();
		$countHinzugefuegteDaten = 0;
		if($account == "raiba_giro"){
			
			if(!$save && !$this->showUploadForm){
				$kategorie = new showKategorienImport();
				$kategorie->setUid($_SESSION["user_id"]);
				$resKategorien = $this->sql->sql_res($kategorie->selectKategorien());
				if(mysql_num_rows($resKategorien) > 0){
					while ($row = mysql_fetch_assoc($resKategorien)) {
						$kategorieRows[] = $row;
					}
				}
				foreach($neueDatenAusgemistet as $key => $value){
					$countHinzugefuegteDaten++;
					$verwZweck = $value[getEmpfaenger()]." ".$value[getVerwendungszweck()];
					$intelligenz = new Intelligenz();
					
					
				// Die nächsten beiden Zeilen müssen einkommentiert werden, wenn die Intelligenz laufen soll
					$vermKategorie = $intelligenz->kategorieVermuten($verwZweck, $this->sql);
					$value["vermuteteKategorie"] = $vermKategorie;
					if(@$value["vermuteteKategorie"] == NULL){
						$value["vermuteteKategorie"] = "";
					}
					$value["kategorieContent"] = $kategorie->getKategorienOnly($value["vermuteteKategorie"], $value["id"],$value["SH"], $kategorieRows);
					$neueDatenAusgemistetMitUeberlaufAbsicherung[$key] = $value;
					
					
					// Nach mehr als 4 Sekunden Skript abbrechen
					$this->timeC = microtime(true);
					$ac = $this->timeC-$this->timeA;
					if($ac > 2.5){
						break;
					}
				}
			}
		}
		
		$tpl = new PhpTemplate(dirname(__FILE__).'/template.phtml');
		$this->content .= $tpl->render(array(
			"showUploadForm" => $this->showUploadForm,
			"target_path" => $target_path,
			"daten" => $neueDatenAusgemistetMitUeberlaufAbsicherung,
			"save" => $save,
			"hits" => $gesamtZahl,
			"anzahlNeu" => $anzahlAusgemisteterDaten,
			"angezeigteDatensaetze" => $countHinzugefuegteDaten,
		));

		// SQL Verbindung trennen
		$this->sql->close();

		$this->timeB = microtime(true);
		$this->content .= $this->timeB-$this->timeA;
		return $this->content;
	}
}
