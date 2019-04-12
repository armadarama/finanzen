<?php

// QUERYS //
function getSqlCsv($neueDatenGesamt, $sql){
	$csvQuery = "Select id From csvimport WHERE id IN (";
	$counter = 0;
	foreach($neueDatenGesamt as $neueDatei){
		if($counter > 0){
			$csvQuery .= ",";
		}
		$csvQuery .= $neueDatei["id"];
		$counter++;
	}
	$csvQuery .= ')';
	return getSqlQuery($csvQuery, $sql);
}

function insertVorhandeneCsv($vorhandeneCSVDaten, $sql, $account){
	$sql = new sql("blabla");
	$csvInsertQuery = '
		INSERT INTO 
			csvimport
				(
				id,
				user_id,
				konto_id,
				sh,
				verwendungszweck
				)
			VALUES';
	$counterCSVImport = 0;
	foreach($vorhandeneCSVDaten as $id => $value){
		if($counterCSVImport > 0){
			$csvInsertQuery .= ',';
		}
		$csvInsertQuery .= '
			(
			'.mysql_real_escape_string($id).',
			'.$_SESSION["user_id"].',
			'.getKonto($account).',
			"'.mysql_real_escape_string($value["SH"]).'",
			"'.mysql_real_escape_string($value["kategorie"]).'"
			)';
		$counterCSVImport++;
	}
	//var_dump($csvInsertQuery);
	$res = $sql->sql_res($csvInsertQuery);
	return $res;
}

function insertData($neueEintraege, $sql, $account){
	$geldbeutelKonto = getKonto("geldbeutel");
	$account_id = getKonto($account);
	$hsbc_debit_id = getKonto("hsbc_debit");
	$hsbc_savings_id = getKonto("hsbc_savings");
	
	if($account == "raiba_giro"){
		$insertQueryEinnahmen = '
			INSERT INTO 
				einnahmen(
					uid,
					betrag,
					konto,
					quelle, 
					kategorie,
					fix,
					periode,
					Datum)
				VALUES
		';
		$insertQueryAusgaben = '
			INSERT INTO 
				ausgaben(
					uid,
					betrag,
					konto,
					zweck, 
					kategorie,
					fix,
					periode,
					Datum)
				VALUES
		';
		$insertQueryIntelligenz = '
			INSERT INTO 
				intelligenz(
					ursprung,
					neu,
					kategorie
					)
				VALUES
		';
		
		$einzahlungsBetragGirokonto = 0;
		$auszahlungsBetragGirokonto = 0;
		$einzahlungsBetragGeldbeutel = 0;
		$gesamtCounter = 0;
		$einzahlCounter = 0;
		$auszahlCounter = 0;
		
		foreach($neueEintraege as $id => $data){
			$betrag = $data[getUmsatz()];
			$betrag = str_replace(".","",$betrag);
			$betrag = (float)str_replace(",",".",$betrag);
			$data["betrag"] = $betrag;		
			
			// Intelligenz query erstellen
			if($gesamtCounter > 0){
				$insertQueryIntelligenz .= ',';
			}
			$insertQueryIntelligenz .= '(
						"'.mysql_real_escape_string($data[getEmpfaenger()]."\n".$data[getVerwendungszweck()]).'",
						"'.mysql_real_escape_string($data["neuerverwendungszweck"]).'",
						"'.mysql_real_escape_string($data["kategorie"]).'")';
			
			// Nach Kategorie ein und auszahlungenquery erstellen
			if($data["kategorie"] == "Abheben" && $_SESSION["user_id"] == 1) {
				$einzahlungsBetragGeldbeutel += $betrag;
				$auszahlungsBetragGirokonto += $betrag;
			} else {
				$datum = getDatum($data["datum"]);
				// Wenn Einnahmen, dann zu einnahmenquery
				// else ausgabenquery
				if($data["SH"] == "H"){
					$einzahlungsBetragGirokonto += $betrag;
					$einzahlCounter++;
					if($einzahlCounter > 1){
						$insertQueryEinnahmen .= ',';
					}
					$insertQueryEinnahmen .= '(
							'.$_SESSION["user_id"].',
							'.$data["betrag"].',
							'.$account_id.',
							"'.mysql_real_escape_string($data["neuerverwendungszweck"]).'",
							"'.mysql_real_escape_string($data["kategorie"]).'",
							0,
							"",
							'.mysql_real_escape_string($datum).')';
				}else if($data["SH"] == "S"){
					$auszahlungsBetragGirokonto += $betrag;
					$auszahlCounter++;
					if($auszahlCounter > 1){
						$insertQueryAusgaben .= ',';
					}
					$insertQueryAusgaben .= '(
							'.$_SESSION["user_id"].',
							'.$data["betrag"].',
							'.$account_id.',
							"'.mysql_real_escape_string($data["neuerverwendungszweck"]).'",
							"'.mysql_real_escape_string($data["kategorie"]).'",
							0,
							"",
							'.mysql_real_escape_string($datum).')';
				}
			}
			$gesamtCounter++;
		}
		// abs == Betrag von Wert
		$umsatzBetragGirokontoOhneAbs = $einzahlungsBetragGirokonto - $auszahlungsBetragGirokonto;
		$umsatzBetragGirokonto = abs($umsatzBetragGirokontoOhneAbs);
		if($umsatzBetragGirokontoOhneAbs >= 0){
			$plusminus = "+";
		}else{
			$plusminus = "-";
		}
		
		$umsatzBetragGirokontoQuery = "UPDATE konten SET kontostand = kontostand ".$plusminus." ".$umsatzBetragGirokonto." WHERE `konten`.`id` = ".$account_id;
		$umsatzBetragGeldbeutelQuery = "UPDATE konten SET kontostand = kontostand + ".$einzahlungsBetragGeldbeutel." WHERE `konten`.`id` = ".$geldbeutelKonto;
		
		if($einzahlCounter>0)
			//var_dump($insertQueryEinnahmen);
			$res = $sql->sql_res($insertQueryEinnahmen);
		if($auszahlCounter>0)
			//var_dump($insertQueryAusgaben);
			$res = $sql->sql_res($insertQueryAusgaben);
		if($gesamtCounter>0){
			//var_dump($insertQueryIntelligenz);
			$res = $sql->sql_res($insertQueryIntelligenz);
			if($umsatzBetragGirokonto != 0)
				//var_dump($umsatzBetragGirokontoQuery);
				$res = $sql->sql_res($umsatzBetragGirokontoQuery);
			if($einzahlungsBetragGeldbeutel != 0)
				//var_dump($umsatzBetragGeldbeutelQuery);
				$res = $sql->sql_res($umsatzBetragGeldbeutelQuery);
		}
	}elseif($account == "hsbc_debit" || $account == "hsbc_savings"){
		$insertQueryEinnahmen = '
			INSERT INTO 
				einnahmen(
					uid,
					Datum,
					kategorie,
					quelle, 
					betrag,
					konto)
				VALUES
		';
		$insertQueryAusgaben = '
			INSERT INTO 
				ausgaben(
					uid,
					Datum,
					kategorie,
					zweck, 
					betrag,
					konto)
				VALUES
		';
		
		if($account == "hsbc_debit"){
			$aktives_konto = "hsbc_debit";
			$gegen_konto = "hsbc_savings";
		}else{
			$aktives_konto = "hsbc_savings";
			$gegen_konto = "hsbc_debit";
		}
		
		$geldtransfers = array();
		$geldtransfers["hsbc_savings"] = 0;
		$geldtransfers["hsbc_debit"] = 0;
		$geldtransfers["geldbeutel"] = 0;
		$auszahlungsBetragTransfer = 0;
		$einzahlungsBetrag = 0;
		$auszahlungsBetrag = 0;
		$einzahlungsBetragGeldbeutel = 0;
		$gesamtCounter = 0;
		$einzahlCounter = 0;
		$auszahlCounter = 0;

		foreach($neueEintraege as $id => $eintrag){
			// Nach Kategorie ein und auszahlungenquery erstellen
			$betrag = $eintrag["betrag"];
			if($eintrag["kategorie"] == "TRANSFER_HSBC_SAVINGS" && $account == "hsbc_debit") {
			// 	// var_dump($eintrag);
			// 	$geldtransfers["hsbc_savings"] += $betrag;
			// 	$auszahlungsBetragTransfer += $betrag;
			// } elseif($eintrag["kategorie"] == "TRANSFER_TO_GELDBEUTEL") {
			// 	$geldtransfers["geldbeutel"] += $betrag;
			// 	$auszahlungsBetragTransfer += $betrag;
			} else {
				$datum = getDatum($eintrag["datum"]);
				
				// Wenn Einnahmen, dann zu einnahmenquery
				// else ausgabenquery
				if($eintrag["SH"] == "H"){
					$einzahlungsBetrag += $betrag;
					$einzahlCounter++;
					if($einzahlCounter > 1){
						$insertQueryEinnahmen .= ',';
					}
					$insertQueryEinnahmen .= '(
						'.$_SESSION["user_id"].',
						'.mysql_real_escape_string($datum).',
						"'.mysql_real_escape_string($eintrag["kategorie"]).'",
						"USA '.mysql_real_escape_string($eintrag["kategorie"]).'",
						'.$eintrag["betrag"].',
						'.mysql_real_escape_string($account_id).')';
				}else if($eintrag["SH"] == "S"){
					$auszahlungsBetrag += $betrag;
					$auszahlCounter++;
					if($auszahlCounter > 1){
						$insertQueryAusgaben .= ',';
					}
					$insertQueryAusgaben .= '(
						'.$_SESSION["user_id"].',
						'.mysql_real_escape_string($datum).',
						"'.mysql_real_escape_string($eintrag["kategorie"]).'",
						"USA '.mysql_real_escape_string($eintrag["kategorie"]).'",
						'.$eintrag["betrag"].',
						'.mysql_real_escape_string($account_id).')';
				}
			}
			$gesamtCounter++;
		}
		// abs == Betrag von Wert
		$umsatzBetragOhneAbs = $einzahlungsBetrag - $auszahlungsBetrag - $auszahlungsBetragTransfer;
		$umsatzBetrag = abs($umsatzBetragOhneAbs);
		if($umsatzBetragOhneAbs >= 0){
			$plusminus = "+";
		}else{
			$plusminus = "-";
		}
		
		$umsatzBetragQuery = "UPDATE konten SET kontostand = kontostand ".$plusminus." ".$umsatzBetrag." WHERE `konten`.`id` = ".$account_id;
		
		$geldtransferQueries = array();
		foreach($geldtransfers as $gt_account => $gt_betrag){
			$geldtransferQueries[] = "UPDATE konten SET kontostand = kontostand + ".$gt_betrag." WHERE `konten`.`id` = ".getKonto($gt_account);
		}
		
		if($einzahlCounter>0){
			// var_dump($insertQueryEinnahmen);
			$res = $sql->sql_res($insertQueryEinnahmen);
		}
		if($auszahlCounter>0){
			// var_dump($insertQueryAusgaben);
			$res = $sql->sql_res($insertQueryAusgaben);
		}
		if($gesamtCounter>0){
			if($umsatzBetrag != 0){
				// var_dump($umsatzBetragQuery);
				$res = $sql->sql_res($umsatzBetragQuery);
			}
			foreach($geldtransferQueries as $id => $gt_query){
				// var_dump($gt_query);
				$res = $sql->sql_res($gt_query);
			}
		}
	}
	return $gesamtCounter;
}

// Datum
function getDatum($datum){
	$dateArr = explode(".",$datum);
	$day = $dateArr[0];
	$month = $dateArr[1];
	$year = $dateArr[2];
	$datetime = new DateTime($day.'.'.$month.'.'.$year,new DateTimeZone('Europe/Berlin'));
	$datum = $datetime->format('Ymd');
	return $datum;
}


// SQL VERBINDUNG ÜBER QUERY

function getSqlQuery($sqlQuery, $sql){
	$result = $sql->sql_res($sqlQuery);
	if(mysql_num_rows($result) > 0){
		$arr = array();
		while($row = mysql_fetch_assoc($result)){
			$arr[] = $row;
		}
		return $arr;
	}else{
		return false;
	}
}

function simpleQuery($sqlQuery, $sql){
	$result = $sql->sql_res($sqlQuery);
	return $result;
}