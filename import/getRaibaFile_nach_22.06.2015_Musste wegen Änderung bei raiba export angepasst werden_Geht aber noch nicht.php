<?php

class getRaibaFile {
	private $endsaldo;
	private $datenArray;
	
	function getDatenArray(){
		return $this->datenArray;
	}
	
	function getEndsaldo(){
		return $this->endsaldo;
	}

	function startGettingFile($dateiname){
		// Datei laden
		$file = @fopen($dateiname, "r");

		// Variablen initalisieren
		$endsaldo = 0;
		$attribute = array();
		$daten = array();

		// Datei auslesen
		if ($file) {
			$counter = 0;
			
			$datenArrAusDatei = array();
			$datenContentAusDatei = "";
			
			// Endsaldo und Attribute bestimmen
			while (($buffer = fgets($file, 4096*4)) !== false) {
				$zeile = explode(";",$buffer);
				if($counter == 0){
					if($zeile[0]  == "Endsaldo"){
						$endsaldo = $zeile[1];
						$endsaldo = str_replace(".","",$endsaldo);
						$endsaldo = (float)str_replace(",",".",$endsaldo);
					}else{
						echo "<p>Die Datei entspricht nicht dem richtigen Format!</p>";
						echo "<p>Der Vorgang wird abgebrochen.</p>";
						fclose($file);
						exit;
					}
				}elseif($counter == 1){
					$attribute = $zeile;
					$attribute[13] = "SH";
				}elseif($counter > 1){
					$datenArrAusDatei[] = $buffer;
					// TEST ABBRUCH
					if($counter < 0){
						break;
					}
					// TEST ABBRUCH
					
				}
				$counter++;
			}
			$this->endsaldo = $endsaldo;
			fclose($file);
		}

		// Die CSV Datei der raiba ist defekt. Es sind \n vorhanden, die nicht dahingehören
		// Daher wird im Folgenden die CSV in ein Array mit Strings sortiert, in der alle unnötigen \n
		// 		entfernt wurden, um mit im Nachhinein explode(...) ordentlich arbeiten zu können.
		// Unvollständige Datensätze werden nicht gespeichert.
		$countZeilen = count($datenArrAusDatei);
		$datenUeberarbeitet = array();
		$neueZeile = "";
		$neueZeileUnvollstaendig = true;
		for($i = 0; $i < $countZeilen; $i++){
			$zeile = $datenArrAusDatei[$i];
			$zeileArr = explode(";",$datenArrAusDatei[$i]);
			$anzahlZeile = count($zeileArr);
			
			
			if($anzahlZeile<13){
				$neueZeile .= $zeile;
				$neueZeile = str_replace("\n"," ",$neueZeile);
			}else{
				$datenUeberarbeitet[] = $zeile;
				continue;
			}
			
			$anzahlNeueZeile = count(explode(";",$neueZeile));
			if($anzahlNeueZeile<13){
				$neueZeileUnvollstaendig = true;
			}elseif($anzahlNeueZeile==13){
				$datenUeberarbeitet[] = trim($neueZeile);
				$neueZeile = "";
			}
		}

		// 
		$datenInArray = array();
		$datenInArrayAusgaben = array();
		$datenInArrayEinnahmen = array();
		foreach($datenUeberarbeitet as $key => $zeile){
			$tempZeile = explode(";",$zeile);
			$zeileArr = array();
			// Keys durch Worte aus $attribute ersetzen
			foreach($tempZeile as $key => $value){
				$zeileArr[$attribute[$key]] = trim(str_replace("\"","",$value));
			}
			// Daten eine ID geben: "Datum in Zahlen ohne punkt"."ganzzahlicher Betrag"
			$zeileArr["id"] = $this->createZeilenId($zeileArr);
			$datenInArray[] = $zeileArr;
			// Daten in Einnahmen und Ausgaben unterteilen
			/*if($zeileArr["SH"] == "S"){
				$datenInArrayAusgaben[] = $zeileArr;
			}elseif($zeileArr["SH"] == "H"){
				$datenInArrayEinnahmen[] = $zeileArr;
			}*/
		}
		//$datenInArray["einnahmen"] = $datenInArrayEinnahmen;
		//$datenInArray["ausgaben"] = $datenInArrayAusgaben;
		$this->datenArray = $datenInArray;
	}

	private function createZeilenId($zeileArr){
		$bt = new DateTime($zeileArr[getBuchungstag()]);
		$btY = str_replace("20","",date_format($bt, 'Y'));
		$btm = date_format($bt, 'm');
		$btd = date_format($bt, 'd');
		
		$SH = 0;
		if($zeileArr["SH"] == "S"){
			$SH = 1;
		}
		
		$um = (int)$zeileArr[getUmsatz()];
		$um = str_replace(".","",$um);
		$um = (int)(float)str_replace(",",".",$um);
		
		$ktnr = (int)$zeileArr["Konto-Nr."];
		$blz = (int)$zeileArr["BLZ"];
		$randometer = $this->quersumme($btY.$btm.$btd)+$this->quersumme($um)+$this->quersumme($ktnr)+$this->quersumme($blz);

		while($randometer>99){
			$randometer = $randometer - 90;
		}
		$id = $btY.$btm.$btd.$SH.($randometer);
		return $id;
	}

	function quersumme ( $digits ){
	// Typcast falls Integer uebergeben
		$strDigits = ( string ) $digits;
		for( $intCrossfoot = $i = 0; $i < strlen ( $strDigits ); $i++ ){
			$intCrossfoot += $strDigits{$i};
		}
		return $intCrossfoot;
	} 
}




