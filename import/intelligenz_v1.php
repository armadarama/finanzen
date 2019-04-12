<?php

class Intelligenz_v1 {
	function kategorieVermuten($verwZweck, $sql){
		$expl = explode(" ", $verwZweck);

		$whereClause = "";
		$countExpl = count($expl);
		$counter = 0;
		foreach($expl as $val){
			$counter++;
			if($this->checkBlackList($val)){
				continue;
			}
			$whereClause .= "ursprung LIKE '%$val%'";
			if($counter < $countExpl){
				$whereClause .= " OR ";
			}
		}
		$selectQuery = "
			SELECT *, COUNT(*) 
			FROM `intelligenz` 
			WHERE ($whereClause) 
			Group By kategorie 
			ORDER By COUNT(*) DESC 
			LIMIT 1";
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