<?php
/*$arrSearch = array("a", "b", "c", "d", "e", "f"); 

$s = new schleifenMonster();
for($i = count($arrSearch);$i >= 1;$i--){
	$s->start($i, $arrSearch);
}*/
class Permutation {

	public $ergebnis = array();
	public $arrSearch; 
	public $anzahlArray;
	public $schleifenEbene = 0;
	public $maximaleSchleifenAnzahl;
	public $vorigeBuchstaben = array();
	public $zaehler = 0;
	
	function start($anzahlSchleifen, $zuBehandelndesArray){
		$this->ergebnis = array();
		$this->arrSearch = $zuBehandelndesArray;
		$this->maximaleSchleifenAnzahl = $anzahlSchleifen;
		$this->anzahlArray = count($this->arrSearch);
		$this->schleife(-1, array());
	}
	
	public function getErgebnis(){
		return $this->ergebnis;
	}
	
	function schleife($vorherigerBuchstabe, $vorigeBuchstaben){
		$this->schleifenEbene++;
		// HIER IST EIN FEHLER
		for($jetzigerBuchstabe = $vorherigerBuchstabe + 1; $jetzigerBuchstabe < $this->anzahlArray; $jetzigerBuchstabe++){
			
			// Wenn noch nicht die letzte Ebene
			if($this->schleifenEbene < $this->maximaleSchleifenAnzahl){
				$vorigeBuchstaben[$this->schleifenEbene] = $jetzigerBuchstabe;
				$this->schleife($jetzigerBuchstabe, $vorigeBuchstaben);
				
			// Wenn die letzte Ebene
			}else if($this->schleifenEbene == $this->maximaleSchleifenAnzahl){
				// Gib alle Buchstaben aus
				$this->zaehler++;
				foreach($vorigeBuchstaben as $value){
					$this->ergebnis[$this->zaehler][] = $this->arrSearch[$value];
					//echo $this->arrSearch[$value]." ";
				}
				$this->ergebnis[$this->zaehler][] = $this->arrSearch[$jetzigerBuchstabe];
				//echo $this->arrSearch[$jetzigerBuchstabe]."<br>";
				
			// Wenn weder noch, dann Fehler
			}else{
				echo "Error";
				break;
			}
		}
		$this->schleifenEbene--;
	}
}



