<?php

function checkData($neueDatenGesamt, $sql){
	//Prüfen ob neue Daten schon vorher eingetragen wurden
	$bestehendeCsvDatenTemp = getSqlCsv($neueDatenGesamt, $sql);
	$bestehendeCsvDaten = array();
	if($bestehendeCsvDatenTemp != false){
		foreach($bestehendeCsvDatenTemp as $value){
			$bestehendeCsvDaten[(int)$value["id"]] = (int)$value["id"];
		}
	}
	
	// Bestehende Daten aus der neueDatenGesamt entfernen
	$neueDatenAusgemistet = array();
	foreach($neueDatenGesamt as $neu){
		if(!(in_array($neu["id"], $bestehendeCsvDaten))){
			$neueDatenAusgemistet[] = $neu;
		}
	}
	
	return $neueDatenAusgemistet;
}

function getNewEndsaldo($sql, $account){
	$account_id = getKonto($account);
	$data = getSqlQuery("Select kontostand From konten WHERE id = ".$account_id, $sql);
	return $data[0]["kontostand"];
}