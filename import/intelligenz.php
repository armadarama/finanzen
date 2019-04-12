<?php

class Intelligenz {
	function kategorieVermuten($verwZweck, $sql){
		// Verwendungszweck in einzelne Worte gliedern
		$worteDesVerwZwecks = explode(" ", $verwZweck);
		
		// unnütze Worte entfernen
		$tempWorteDesVerwZwecks = array();
		foreach($worteDesVerwZwecks as $key => $value){
			if($this->checkBlackList($value))continue;
			$repairedValue = $value;
			$tempWorteDesVerwZwecks[] = $repairedValue;
		}
		$worteDesVerwZwecks = $tempWorteDesVerwZwecks;
		
		// $data vordefinieren
		$data = array(
			0 => array(
				"kategorie" => "",
			),
		);
		
		//Kategorien mit den meisten vorkommenden Worten im Verwendungszweck auswählen.
		//Kategorie auswählen, die am häufigsten genannt wurde.
		
		$anzahlWorte = count($worteDesVerwZwecks);
		$whereClause = "";
		foreach($worteDesVerwZwecks as $wortNummer => $Wort){	
			$whereClause .= 'CAST((LENGTH(ursprung) - LENGTH(REPLACE(ursprung, "'.$Wort.'", ""))) / LENGTH("'.$Wort.'") AS UNSIGNED)';
			if($wortNummer < $anzahlWorte-1)
				$whereClause .= " + ";
		}
		
		// TABLE DROPEN
		$dropQuery = '
				DROP TEMPORARY TABLE IF EXISTS temptablename1;
		';
		$res = simpleQuery($dropQuery, $sql);
		
		// TABLE CREATEN und beste Kategorie wählen
		$createQuery = '
				CREATE TEMPORARY TABLE temptablename1 
					SELECT
						kategorie,
						(
						'.$whereClause.'
						) AS counter
					FROM intelligenz
					HAVING counter > 0
					ORDER BY counter DESC;
				';
		
		$res = simpleQuery($createQuery, $sql);
		
		// Kategorie ausgeben
		$selectQuery = '
			SELECT kategorie, counter, COUNT(*)
			FROM temptablename1
			GROUP BY kategorie, counter 
			ORDER BY counter DESC, COUNT(*) DESC
			LIMIT 1;
		';
		
		$data = getSqlQuery($selectQuery, $sql);
		return $data[0]["kategorie"];
	}

	// Bestimmte Begriffe werden in der Zuordnung von Verwendungszweck zu Kategorie nicht beachtet, da sie das Ergebnis verfälschen
	function checkBlackList($val){
		if($val == "") return true;
		if($val == " ") return true;
		if(is_numeric($val)) return true;
		return false;
	}
}
?>