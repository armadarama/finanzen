<?php 

class globalefunktionen {
	
	function getThismonatQuery($getHtml=""){
		$datetime = new DateTime($day.'.'.$monat.'.'.$year,new DateTimeZone('Europe/Berlin'));
		$today = $datetime->format('Ymd');
		$thismonat = $datetime->format('Ym');
		$nextmonat = $thismonat + 1;
		$thismonat = $thismonat."01";
		$nextmonat = $nextmonat."01";
		$monatselect = '(datum >= "'.$thismonat.'" AND datum < "'.$nextmonat.'") AND';
		if($getHtml == "monatsanzeige"){
			$monatsanzeige = $datetime->format('M Y');
			return $monatsanzeige;
		}
		return $monatselect;
	}

	function htmlValidate($value, $art=""){
		if(is_numeric($value)){
			$value = money_format('%.2n',$value);
		}else if($art == "ae"){
			$value = ucfirst($value);
			$value = str_replace('ä','ae',$value);
			$value = str_replace('ö','oe',$value);
			$value = str_replace('ü','ue',$value);
			$value = str_replace('Ä','Ae',$value);
			$value = str_replace('Ö','Oe',$value);
			$value = str_replace('Ü','Ue',$value);
			$value = str_replace('ß','ss',$value);
		}else{
			$value = ucfirst($value);
			//$value = str_replace('ae','&auml;',$value);
			//$value = str_replace('oe','&ouml;',$value);
			//$value = str_replace('ue','&uuml;',$value);
			$value = str_replace('ä','&auml;',$value);
			$value = str_replace('ö','&ouml;',$value);
			$value = str_replace('ü','&uuml;',$value);
			//$value = str_replace('Ae','&Auml;',$value);
			//$value = str_replace('Oe','&Ouml;',$value);
			//$value = str_replace('Ue','&Uuml;',$value);
			$value = str_replace('Ä','&Auml;',$value);
			$value = str_replace('Ö','&Ouml;',$value);
			$value = str_replace('Ü','&Uuml;',$value);
			$value = str_replace('ß','&#223;',$value);
		}
		return $value;
	}
	
	function intToMonth($int){
		$int = (int)$int;
		switch ($int){
			case 1:
				$monat = "Jan";
				break;
			case 2:
				$monat = "Feb";
				break;	
			case 3:
				$monat = "Mar";
				break;	
			case 4:
				$monat = "Apr";
				break;	
			case 5:
				$monat = "May";
				break;	
			case 6:
				$monat = "Jun";
				break;	
			case 7:
				$monat = "Jul";
				break;	
			case 8:
				$monat = "Aug";
				break;		
			case 9:
				$monat = "Sep";
				break;	
			case 10:
				$monat = "Oct";
				break;	
			case 11:
				$monat = "Nov";
				break;	
			case 12:
				$monat = "Dez";
				break;	
		}
		return $monat;
	}
	
	function getPerioden(){
		$perioden = array();
		$perioden[] = "taeglich";
		$perioden[] = "woechentlich";
		$perioden[] = "monatlich";
		$perioden[] = "jaehrlich";
		return $perioden;
	}
	
	function getArten(){
		$arten = array();
		$arten[] = "einnahmen";
		$arten[] = "ausgaben";
		return $arten;
	}
	
	function datumToQuery($vontag, $vonmonat, $vonjahr, $bistag, $bismonat, $bisjahr,$tabelle=""){
		if($vonjahr == $bisjahr){
			if($vonmonat == "" && $bismonat == ""){
				$select = "";
				return $select;
			}
		}
		if($vonmonat != ""){
			if($vontag == "")
				$vontag = "1";
		}else if($vonmonat == ""){
			$vonmonat = "1";
			$vontag = "1";
		}
		if($bismonat != ""){
			if($bistag == "")
				$bistag = "31";
		}else if($bismonat == ""){
			$bismonat = "12";
			$bistag = "31";
		}
		
		$vondatum = $vonjahr."-".$vonmonat."-".$vontag;
		$bisdatum = $bisjahr."-".$bismonat."-".$bistag;
		if($tabelle != "")
			$tabelle = $tabelle.".";
		$select = "(".$tabelle."datum >= '".$vondatum."' AND ".$tabelle."datum <= '".$bisdatum."')";
		return $select;
	}
}

?>