<?php

function getVerwendungszweck(){
	switch ($_SESSION["user_id"]) {
	    case 1:
	       return "Vorgang/Verwendungszweck";
	        break;
	    case 2:
	       return "Verwendungszweck";
	        break;
	}
}

function getBuchungstag(){
	switch ($_SESSION["user_id"]) {
	    case 1:
	       return "Buchungstag";
	        break;
	    case 2:
	       return "Buchungsdatum";
	        break;
	}
}

function getUmsatz(){
	switch ($_SESSION["user_id"]) {
	    case 1:
	       return "Umsatz";
	        break;
	    case 2:
	       return "Betrag";
	        break;
	}
}

function getEmpfaenger(){
	switch ($_SESSION["user_id"]) {
	    case 1:
	       return "Empfänger/Zahlungspflichtiger";
	        break;
	    case 2:
	       return "Empfaenger";
	        break;
	}
}

function getKonto($account){
	$account_id=0;
	if($account == "raiba_giro"){
		$account_id = 1;
	}elseif($account == "hsbc_debit"){
		$account_id = 63;
	}elseif($account == "hsbc_savings"){
		$account_id = 62;
	}elseif($account == "geldbeutel"){
		$account_id = 8;
	}
	return $account_id;
}

?>