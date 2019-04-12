<?php

class getHSBCFile {
	private $endsaldo;
	private $datenArray;
	
	function getDatenArray(){
		return $this->datenArray;
	}
	
	function getEndsaldo(){
		return $this->endsaldo;
	}

	function startGettingFile($dateiname, $sql){
		// Datei laden
		$file = @fopen($dateiname, "r");

		// Variablen initalisieren
		$endsaldo = 0;
		$attribute = array();
		$daten = array();

		// Datei auslesen
		if ($file) {
			$counter = 0;
			$endsaldo = 0;
			$datenArrAusDatei = array();
			
			// Attribute speichern
			while (($buffer = fgets($file, 4096*4)) !== false) {
				$zeile = explode(",",$buffer);
				if($counter == 0){
					$attribute = $zeile;
				}elseif($counter > 0){
					$datenArrAusDatei[] = $zeile;
				}
				$counter++;
			}
			$this->endsaldo = $endsaldo;
			fclose($file);
		}


		// Create datenArray
		$alle_kategorien = array();
		$datenInArray = array();
		$neuerEintragEinnahmen = array();
		$neuerEintragAusgaben = array();
		foreach($datenArrAusDatei as $zeilen_id => $zeile){
			$neuerEintrag = array();
			$lastEntryEinnahmen = true;
			foreach($zeile as $key_id => $wert){
				$attribut = trim($attribute[$key_id]);
				$wert = trim($wert);
				if($attribut == "einnahmen" || $attribut == "ausgaben"){
					if($attribut == "einnahmen"){
						if($wert != ""){
							$neuerEintrag["betrag"] = (float)str_replace(",",".",$wert);
							$neuerEintrag["SH"] = "H";
						} 
					}elseif($attribut == "ausgaben"){
						if($wert != ""){
							$neuerEintrag["betrag"] = (float)str_replace(",",".",$wert);
							$neuerEintrag["SH"] = "S";
						}
					}
				}else{
					$neuerEintrag[$attribut] = $wert;
				}

				if($attribut == "kategorie"){
					$alle_kategorien[] = $wert;
				}
			}
			$datenInArray[$this->createZeilenId($neuerEintrag)] = $neuerEintrag;
		}

		if ($this->checkKatgorien($sql, $alle_kategorien)){
			$this->datenArray = $datenInArray;
		}else{
			$this->datenArray = false;
		}
	}

	private function checkKatgorien($sql, $kategorien){
		$allGood = true;
		$noRowProcessed = true;
		foreach($kategorien as $key => $kategorie){
			$sqlquery = "SELECT kategorie FROM kategorien WHERE aktiv=1 AND kategorie in ('".mysql_real_escape_string($kategorie)."')";
			$res = $sql->sql_res($sqlquery);
			if(mysql_num_rows($res) < 1){
				print $kategorie." ist keine vorhandene Kategorie in der datenbank.";
				print "<br />";
				$allGood = false;
			}
		}

		if($allGood){
			return true;
		}else{
			$sqlquery = "SELECT kategorie FROM kategorien WHERE aktiv=1 ORDER BY kategorie";
			$res = $sql->sql_res($sqlquery);
			print "<br />";
			print "<br />";
			print "Folgende Kategorien stehen zur verfuegung";
			print "<br />";
			print "<br />";
			while($row = mysql_fetch_assoc($res)){
				print $row["kategorie"]."<br />";
			}
			print "<br />";
			print "<br />";
			return false;
		}
	}

	private function createZeilenId($zeileArr){
		$bt = new DateTime($zeileArr["datum"]);
		$btY = str_replace("20","",date_format($bt, 'Y'));
		$btm = date_format($bt, 'm');
		$btd = date_format($bt, 'd');
		
		$SH = 0;
		if($zeileArr["SH"] == "S"){
			$SH = 1;
		}
		
		$um = (int)$zeileArr["betrag"];
		$um = str_replace(".","",$um);
		$um = (int)(float)str_replace(",",".",$um);
		
		$randometer = $this->quersumme($btY.$btm.$btd)+$this->quersumme($um);

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




