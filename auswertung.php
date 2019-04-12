<?php

require_once("subnavigation.php");
require_once("graph.php");
include ("login/checkuser.php"); 

class auswertung {

	var $anzahl;
	var $uid;
	var $res;
	var $artmonatarray;
	var $gesamtsummen;
	var $gesamtDifferenz;
	var $monatsDifferenz;
	var $seitenname;
	var $global;
	var $graphFlow;
	var $graphDetaill;
	var $graphBoolean;
	var $verteilerContent;
	var $umsatzHighlight;
	var $datumSelect;
	var $uebersprungen; // Dieses Array beinhaltet die Anzahl und den Gesamtbetrag der übersprungenen Werte. Wann Werte übersprungen werden, steht ca. Zeile 180

	function auswertung($graphBoolean=true){
		$this->graphBoolean = $graphBoolean;
		$this->global = new globalefunktionen();
		$this->seitenname = "Auswertung";
		$this->uid = $_SESSION["user_id"];
		$this->getDatumPost();
		$this->sqlcon();
		$this->fetchEinnahmen();
		$this->differenzBerechnen();
		if($graphBoolean == true)
			$this->createUmsatzHighlight();
			$this->createGraph();
		if($graphBoolean == false)
			$this->createVerteiler();
		$content = $this->content();
		echo $content;
	}
	
	function content(){
//		var_dump($_SESSION["user_id"]);
//		$url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$navigation = new navigation();
		$subnavigation = new subnavigation();
		$content = '
			<html>
				<header>
					<title>'.$this->seitenname.'</title>
					<link rel="stylesheet" type="text/css" href="styles.css">
				</header>
				
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$subnavigation->getContent($this->seitenname).'
					'.$this->getForm().'
				</body>
			</html>
			';
		return $content;
	}
	
	function getForm(){
		$content .= '<div class="div" id="auswertung">
			<div class="auswertungtabelle">
			<table border="1">
				<tr>
					<th></th>
					<th>Einnahmen</th>
					<th>Ausgaben</th>
					<th>Umsatz</th>
				</tr>
				'.$this->getTabelle().'
			</table>
			</div>';
		if($this->graphBoolean == true){
			$content .= $this->umsatzHighlight;
			$content .='
				<div class="auswertungsgraphen">'.$this->graphFlow->output(1200, 500).'</div>
				<div class="auswertungsgraphen">'.$this->graphDetaill->output(1200, 500).'</div>';
		}else{
			$content .= $this->verteilerContent;
		}
			
		$content .='
		</div>
		';
		return $content;
	}
	
	function getDatumPost(){
		$vonday = $_POST["vonday"];
		$vonmonth = $_POST["vonmonth"];
		$vonyear = $_POST["vonyear"];
		$bisday = $_POST["bisday"];
		$bismonth = $_POST["bismonth"];
		$bisyear = $_POST["bisyear"];
		
		$this->datumSelect = "";
		if($vonyear != ""){
			$this->datumSelect = $this->global->datumToQuery($vonday,$vonmonth,$vonyear,$bisday,$bismonth,$bisyear);
			if($this->datumSelect != "")
				$this->datumSelect = $this->datumSelect." AND";
		}
	}
	
	function createUmsatzHighlight(){
		$datumSelectFeld = new datumSelectFelder();
		$content .= '<form action="" method="post">';
		for($i = 1; $i <= 2; $i++){
			$name = "von";
			$submit = "";
			if($i == 2){
				$name = "bis";
				$submit = "submit";
			}
			$content .= $datumSelectFeld->getContent($name,1,1,1, "", "datum", $submit);
		}
		$content .= '</form>';
		$this->umsatzHighlight = $content;
	}
	
	function getTabelle(){
		$summe = $this->gesamtsummen;
		if($this->gesamtDifferenz >= 0){
			$classgesamt = "green";
		}else if($this->gesamtDifferenz < 0){
			$classgesamt = "red";
		}
		$content .= '
			<tr>
				<td>Gesamtumsatz</td>
				<td class="green">'.$this->global->htmlValidate($summe["einnahmen."]["summe"]).'</td>
				<td class="red">'.$this->global->htmlValidate($summe["ausgaben."]["summe"]).'</td>
				<td class="'.$classgesamt.'">'.$this->global->htmlValidate($this->gesamtDifferenz).'</td>
			</tr>';
		if($this->monatsDifferenz >= 0){
			$classmonat = "green";
		}else if($this->monatsDifferenz < 0){
			$classmonat= "red";
		}
		$content .='
			<tr>
				<td>Monatsumsatz</td>
				<td class="green">'.$this->global->htmlValidate($summe["einnahmen.monat"]["summe"]).'</td>
				<td class="red">'.$this->global->htmlValidate($summe["ausgaben.monat"]["summe"]).'</td>
				<td class="'.$classmonat.'">'.$this->global->htmlValidate($this->monatsDifferenz).'</td>
			</tr>
		';
		return $content;
	}
	
	function differenzBerechnen(){
		$summe = $this->gesamtsummen;
		$this->gesamtDifferenz = $summe["einnahmen."]["summe"] - $summe["ausgaben."]["summe"];
		$this->monatsDifferenz = $summe["einnahmen.monat"]["summe"] - $summe["ausgaben.monat"]["summe"];
	}
	
	function fetchEinnahmen(){
		if(is_array($this->res)){
			foreach($this->res as $res){
				$name = $res["name"];
				if(mysql_num_rows($res["res"]) > 0){
					while ($row = mysql_fetch_assoc($res["res"])) {
						$summe = $summe + $row["betrag"];
					}
				}
				$eintraege[$name]["name"] = $name;
				$eintraege[$name]["summe"] = $summe;
				$summe = 0;
			}
			$this->gesamtsummen = $eintraege;
		}
	}
	
	function setArray(){
		$artmonat = array();
		$artmonat["art"][] = "einnahmen";
		$artmonat["art"][] = "ausgaben";
		$artmonat["monat"][] = "monat";
		$artmonat["monat"][] = "";
		$this->artmonatarray = $artmonat;
	}
	
	function sqlcon(){
		$this->setArray();
		$sqlquerys = array();
		$sql = new sql("blabla");
		if(is_array($this->artmonatarray["art"])){
			foreach ($this->artmonatarray["art"] as $art){
				if(is_array($this->artmonatarray["monat"])){
					foreach($this->artmonatarray["monat"] as $monat){
						$sqlquerys[$art.".".$monat]["art"] = $art;
						$sqlquerys[$art.".".$monat]["monat"] = $monat;
					}
				}
			}
		}
		$res = array();
		if(is_array($sqlquerys)){
			foreach($sqlquerys as $sqlquery){
				$art = $sqlquery["art"];
				$monat = $sqlquery["monat"];
				$res[$art.".".$monat]["name"] = $art.".".$monat;
				$res[$art.".".$monat]["res"] = $sql->sql_res($this->sqlQuery($monat, $art));
			}
		}
		$this->res = $res;
		$sql->close();
	}
	
	function sqlQuery($monat="", $art){
		if($monat == "monat")
			$monat = $this->global->getThismonatQuery();
		$sqlquery = "SELECT betrag FROM ".$art." where $monat uid=".$this->uid;
		return $sqlquery;
	}
	
	function createGraph(){
		$res = $this->sqlAbfrageGraph();#
		
		$groesstesJahr = 0;
		$groessterMonat = 0;
		$kleinsterMonat = 12;
		
		$datetime = new DateTime("",new DateTimeZone('Europe/Berlin'));
		$thisyear = (int)$datetime->format('Y');
		$kleinstesJahr = $thisyear;
		$jahrevorher = $thisyear - 30;
		$jahrespaeter = $thisyear + 3;
		
		$uebersprungen = array();
		$uebersprungen["anzahl"] = 0;
		
		if(is_array($res)){
			foreach($res as $art => $result){
				if(mysql_num_rows($result) > 0){
					while ($row = mysql_fetch_assoc($result)) {
						$monat = substr_replace($row["datum"], '', 7,9); // schneidet die Tage ab
						$jahr = substr_replace($monat, '', 4,7); // zeigt nur das Jahr an
						$jahr = (int)$jahr;
						
						$betrag = (int)$row["betrag"];
						
						// Falls das Datum des Datensatzes über 30 Jahre in der Vergangenheit oder über 3 Jahre in der Zukunft liegt, 
						// wird dieser vernachlässigt, um dauerschleifen zu vermeiden
						if($jahrevorher > $jahr || $jahrespaeter < $jahr){
							$uebersprungen["anzahl"] = $uebersprungen["anzahl"] + 1;
							$uebersprungen["betrag"] = $uebersprungen["betrag"] + $betrag;
							continue;
						}
						if($jahr == $groesstesJahr){
							$nurmonat = substr_replace($monat, '', 0,5);
							if($nurmonat > $groessterMonat)
								$groessterMonat = $nurmonat;
						}
						if($jahr > $groesstesJahr){
							$groesstesJahr = $jahr;
							$groessterMonat = 0;
							$nurmonat = substr_replace($monat, '', 0,5);
							if($nurmonat > $groessterMonat)
								$groessterMonat = $nurmonat;
						}
						if($jahr <= $kleinstesJahr){
							$kleinstesJahr = $jahr;
							$nurmonat = substr_replace($monat, '', 0,5);
							if($nurmonat <= $kleinsterMonat)
								$kleinsterMonat = $nurmonat;
						}
						
						$cashflowBetrag = $betrag;
						if($art == "Ausgaben")
							$betrag = $betrag*(-1);
						$monatlich[$monat] = $monatlich[$monat] + $betrag; // Dieses Array ist für den gesamten geldfluss
						$cashflow[$art][$monat] = $cashflow[$art][$monat] + $cashflowBetrag; // Dieses Arrray ist für die Einnahmen und Ausgaben
					}
				}
			}
		}
//		var_dump($uebersprungen);
	// Dieses Array ist für den gesamten geldfluss
		if(is_array($monatlich)){
			$monatlich = $this->fillArrayWithMonths($monatlich, $kleinstesJahr, $groesstesJahr, $kleinsterMonat,$groessterMonat);
			ksort($monatlich);
			foreach($monatlich as $key => $value){
//				$key = $this->datumFormatGraph($key);
				$monatlichArray[$key] = $value;
			}
			$array["Monatlich"] = $monatlichArray;
		}
		
		if(is_array($monatlichArray)){
			foreach($monatlichArray as $key => $value){
				$summe = $summe + $value;
				$gesamtArray[$key] = $summe;
			}
			$array["Gesamtumsatz"] = $gesamtArray;
		}
		
		$this->graphFlow = new graph("auswertungCashflow", "line", $array);
		
	// Dieses Arrray ist für die Einnahmen und Ausgaben
		if(is_array($cashflow)){
			foreach($cashflow as $art => $array){
				$array = $this->fillArrayWithMonths($array, $kleinstesJahr, $groesstesJahr, $kleinsterMonat,$groessterMonat);
				ksort($array);
				if(is_array($array)){
					foreach($array as $key => $value){
//						$key = $this->datumFormatGraph($key);
						$cashflowArray[$art][$key] = $value;
					}
				}
			}
		}
		$this->graphDetaill = new graph("auswertungDetaill", "line", $cashflowArray, "", "render");
	}
	
	function fillArrayWithMonths($array, $kleinstesJahr, $groesstesJahr, $kleinsterMonat, $groessterMonat){
		//var_dump($array);
		for($jahr=$kleinstesJahr; $jahr <=$groesstesJahr; $jahr ++ ){
			if($kleinstesJahr != $groesstesJahr){
				if($jahr < $groesstesJahr && $jahr > $kleinsterMonat){
					for($monat=1;$monat <=12;$monat++){
						if($monat < 10){
							$monat = (int)$monat;
							$monat = "0".$monat;
						}						$datum = $jahr."-".$monat;
						if($array[$datum] == NULL){
							$array[$datum] = 0; 
						}
					}
				}else if($jahr == $groesstesJahr){
					for($monat=1;$monat <=$groessterMonat;$monat++){
						if($monat < 10){
							$monat = (int)$monat;
							$monat = "0".$monat;
						}
						$datum = $jahr."-".$monat;
						if($array[$datum] == NULL){
							$array[$datum] = 0; 
						}
					}
				}else if($jahr == $kleinstesJahr){
					for($monat=$kleinsterMonat;$monat <= 12;$monat++){
						if($monat < 10){
							$monat = (int)$monat;
							$monat = "0".$monat;
						}
						$datum = $jahr."-".$monat;
						if($array[$datum] == NULL){
							$array[$datum] = 0; 
						}
					}
				}
			}else{
				for($monat=$kleinsterMonat;$monat <=$groessterMonat;$monat++){
					if($monat < 10){
						$monat = (int)$monat;
						$monat = "0".$monat;
					}
					$datum = $jahr."-".$monat;
					if($array[$datum] == NULL){
						$array[$datum] = 0; 
					}
				}
			}
		}
		return $array;
	}
	
	function datumFormatGraph($datum){
		$datum = substr_replace($datum, '', 7,9);
		$jahr = substr_replace($datum, '', 4,7);
		$monat = substr_replace($datum, '', 0,5);
		$monat = $this->global->intToMonth($monat);
		if($monat == "Jan")
			$monat = substr_replace($monat, $jahr." ", 0,0);
		return $monat;
	}
	
// SQL FUNKTIONEN
	function sqlAbfrageGraph(){
		$sql = new sql("blabla");
		$res = array();
		$res["Einnahmen"] = $sql->sql_res("SELECT betrag, datum FROM einnahmen WHERE ".$this->datumSelect." uid=".$this->uid);
		$res["Ausgaben"] = $sql->sql_res("SELECT betrag, datum FROM ausgaben WHERE ".$this->datumSelect." uid=".$this->uid);
		$sql->close();
		return $res;
	}
	
// VERTEILER FUNKTION
	function createVerteiler(){
		// TABELLE MIT 2 spalten 2 zeilen erstellen. dadrin divs
		// Array erstellen 
		$content .= '<div id="verteiler"><table>';
		for($zeile=1; $zeile <= 2; $zeile++){
			if($zahl % 2 == 0)
				$classRow = "";
			$content .= '<tr class="'.$zeile.'">';
			for($spalte=1; $spalte <= 3; $spalte++){
				$classColumn = "links";
				if($zahl % 2 == 0)
					$classColumn = "rechts";
				$content .= '<td class="'.$classColumn.'">';
				$content .= $this->createSeitenElemente($zeile,$spalte);
				$content .= '</td>';
			}
			$content .= '</tr>';
		}
		$content .='</table></div>'; 
		$this->verteilerContent = $content;
	}
	
	function createSeitenElemente($zeile, $spalte){
		$seite = $this->createSeite($zeile,$spalte);
		if($seite == false)
			return;
		$content .='
			<a href="'.$seite["url"].'">
				<div class="weiterleitpage">
					<h2>'.$seite["name"].'</h2>
					<a class="bild" href="'.$seite["url"].'">
						<img height="200" width="230" src="'.$seite["bild"].'" alt="Bild kann nicht angezeitg werden">
					</a>
				</div>
			</a>';
		return $content;
	}
	
	function createSeite($zeile,$spalte){
		$seite = array();
		$name = "";
		$bild = "";
		if($zeile == 1 && $spalte == 1){
			$name = "Cashflow-Verlauf";
			$bild = "img/verlauf.jpg";
			$url = "cashflowverlauf.php";
		}
		if($zeile == 1 && $spalte == 2){
			$name = "Gesamt Kategorieauswertung";
			$bild = "img/kuchen.jpg";
			$url = "auswertungdetaillgesamt.php";
		}
		if($zeile == 1 && $spalte == 3){
			$name = "Monatliche Kategorieauswertung";
			$bild = "img/kuchen.jpg";
			$url = "auswertungdetaillmonat.php";
		}
		if($zeile == 2 && $spalte == 1){
			$name = "Report";
			$bild = "bildadresse";
			$url = "report.php";
		}
		if($zeile == 2 && $spalte == 2){
			$name = "Fixer Cashflow";
			$bild = "bildadresse";
			$url = "auswertungfix.php";
		}
		
		if($name == "" && $bild == "")
			return $false;
		$seite["name"] = $name;
		$seite["bild"] = $bild;
		$seite["url"] = $url;
		return $seite;
	}
}
?>