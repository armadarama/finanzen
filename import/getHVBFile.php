<?php

class getHVBFile {
	private $endsaldo;
	private $datenArray;
	
	function getDatenArray(){
		return $this->datenArray;
	}
	
	function getEndsaldo(){
		return false;
	}

	function startGettingFile($dateiname){
		// Datei laden
		$file = @fopen($dateiname, "r");

		// Variablen initalisieren
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
					$attribute = $zeile;
				}elseif($counter > 0){
					$datenArrAusDatei[] = $buffer;
					// TEST ABBRUCH
					if($counter < 0){
						break;
					}
					// TEST ABBRUCH
					
				}
				$counter++;
			}
			fclose($file);
		}
		
		foreach($attribute as $key => $value){
			$attribute[$key] = trim($value);
		}
		
		//  Attribute werden den Werten zugeordnet
		$datenInArray = array();
		foreach($datenArrAusDatei as $key => $zeile){
			$tempZeile = explode(";",$zeile);
			$zeileArr = array();
			// Keys durch Worte aus $attribute ersetzen
			$empfaenger1 = "";
			$empfaenger2 = "";
			foreach($tempZeile as $key => $value){
				$repairedValue = trim(str_replace("\"","",$value));
				if($attribute[$key] == "Empfaenger 1"){
					$empfaenger1 = $repairedValue;
					continue;
				}
				if($attribute[$key] == "Empfaenger 2"){
					$empfaenger2 = $repairedValue;
					$zeileArr["Empfaenger"] = trim($empfaenger1." ".$empfaenger2);
					continue;
				}
				if($attribute[$key] == "Betrag"){
					if($value!=str_replace("-","",$repairedValue)){
						$zeileArr["SH"] = "S";
					}else{
						$zeileArr["SH"] = "H";
					}
					$repairedValue = str_replace("-","",$repairedValue);
				}
				$zeileArr[$attribute[$key]] = $repairedValue;
			}
			// Daten eine ID geben: "Datum in Zahlen ohne punkt"."ganzzahlicher Betrag"
			$zeileArr["id"] = $this->createZeilenId($zeileArr);
			$datenInArray[] = $zeileArr;
		}
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
		
		$um = $zeileArr[getUmsatz()];
		$um = str_replace(",","",$um);
		$strlenUm = strlen($um);
		$um = substr($um, $strlenUm-3, $strlenUm);  
		
		$id = $btY.$btm.$btd.$SH.$um;
		return $id;
	}
}




