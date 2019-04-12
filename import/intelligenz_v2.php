<?php

class Intelligenz {
	function kategorieVermuten($verwZweck, $sql){
		//if($verwZweck != "GUTSCHRIFT NEBENKOSTEN NOVEMBER")return false;
		// Verwendungszweck in einzelne Worte gliedern
		$worteDesVerwZwecks = explode(" ", $verwZweck);
		
		// Alle Möglichkeiten ohne Relevanz der Reihenfolge wird erstellt
		$permutation = new Permutation();
		$kombinationen = array();
		$start = count($worteDesVerwZwecks);
		$end = 1;
		for($anzahlDerVerknüpftenWorte = $start; $anzahlDerVerknüpftenWorte >= $end; $anzahlDerVerknüpftenWorte--){
			$permutation->start($anzahlDerVerknüpftenWorte, $worteDesVerwZwecks);
			$tempKombination = $permutation->getErgebnis();
			
			$kombination = array();
			// Wenn nurnoch einzelne Worte verwendet werden, prüfe Blacklist
			if($anzahlDerVerknüpftenWorte == 1){
				foreach($worteDesVerwZwecks as $val){
					if(!$this->checkBlackList($val)){
						$kombination[] = $val;
					}
				}
			}else{
				$kombination = $tempKombination;
			}
			$kombinationen[$anzahlDerVerknüpftenWorte] = $kombination;
		}
		
		$countExpl = count($worteDesVerwZwecks);
		$counter = 0;
		// Gehe von den Kombinationen mit der höchsten Wortanzahl  bis zu den Kombinationen mit nur 1 Wortanzahl
		// Prüfe ob bei einer Anzahl von Kombinationsworten eine Kategorie zurückgegeben wird
		// Wenn ja brich die Schleife ab und gib das Ergebnis zurück
		$t = 0;
		$selectQuery = "";
		$counterC = 0;
		foreach($kombinationen as $anzahlWorte => $kombinationenProAnzahlWorte){
			$counterC++;
			$counterA = 0;
			$whereClause = "";
			foreach($kombinationenProAnzahlWorte as $keyKombination => $kombination){
				$counterA++;
				$counterB = 0;
				$countExpl = count($kombination);
				$whereClause .= " (";
				
				// Wenn ein Array vorhanden
				if(is_array($kombination)){
					foreach($kombination as $val){
						$counterB++;
						$whereClause .= "ursprung LIKE '%$val%'";
						if($counterB < $countExpl){
							$whereClause .= " AND ";
						}
					}
				}else if(is_string($kombination)){
					$whereClause .= "ursprung LIKE '%$kombination%'";
				}
				// Wenn String
				
				$whereClause .= ") ";
				if($counterA < count($kombinationenProAnzahlWorte)){
					$whereClause .= " OR ";
				}
			}
			
			$selectQuery .= "
				SELECT *, COUNT(*) AS 'AnzahlErgebnis', $anzahlWorte AS 'AnzahlWorte'
				FROM `intelligenz` 
				WHERE $whereClause
				Group By kategorie 
				";
			if($counterC < count($kombinationen)){
				$selectQuery .= ' UNION ';
			}
		}
		
		$selectQuery .= '
			ORDER BY AnzahlWorte DESC, AnzahlErgebnis DESC
			LIMIT 1
		';
		
		$data = getSqlQuery($selectQuery, $sql);
		
		return $data[0]["kategorie"];
	}

	// Bestimmte Begriffe werden in der Zuordnung von Verwendungszweck zu Kategorie nicht beachtet, da sie das Ergebnis verfälschen
	function checkBlackList($val){
		if($val == "") return true;
		if($val == " ") return true;
		if(strpos($val,"rechn")!==false) return true;
		if(strpos($val,"Rechn")!==false) return true;
		if(strpos($val,"RECHN")!==false) return true;
		if(strpos($val,"rechnung")!==false) return true;
		if(strpos($val,"Rechnung")!==false) return true;
		if(strpos($val,"RECHNUNG")!==false) return true;
		if(strpos($val,"einkauf")!==false) return true;
		if(strpos($val,"Einkauf")!==false) return true;
		if(strpos($val,"EINKAUF")!==false) return true;	
		if(strpos($val,"verwendet")!==false) return true;
		if(strpos($val,"Verwendet")!==false) return true;
		if(strpos($val,"VERWENDET")!==false) return true;
		if(strpos($val,"tan")!==false) return true;
		if(strpos($val,"Tan")!==false) return true;
		if(strpos($val,"TAN")!==false) return true;
		if(strpos($val,"ueberweisung")!==false) return true;
		if(strpos($val,"Ueberweisung")!==false) return true;
		if(strpos($val,"UEBERWEISUNG")!==false) return true;
		if(strpos($val,"artikelnr")!==false) return true;
		if(strpos($val,"Artikelnr")!==false) return true;
		if(strpos($val,"ARTIKELNR")!==false) return true;
		if(strpos($val,"lastschrift")!==false) return true;
		if(strpos($val,"Lastschrift")!==false) return true;
		if(strpos($val,"LASTSCHRIFT")!==false) return true;
		if(strpos($val,"rechnungsnr")!==false) return true;
		if(strpos($val,"Rechnungsnr")!==false) return true;
		if(strpos($val,"RECHNUNGSNR")!==false) return true;
		if(strpos($val,"EC-KARTE")!==false) return true;
		if(strpos($val,"ec-karte")!==false) return true;
		//if(strpos($val,"EC")!==false) return true;
		//if(strpos($val,"ec")!==false) return true;
		return false;
	}
}
?>