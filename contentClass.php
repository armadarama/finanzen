<?php
require_once("showKategorien.php");

class contentClass {
	
	function getFieldset($anzahl){
		$kategorie = new showKategorien();
		$content .= '
			<fieldset class="datensatz">
				<legend>Datensatz '.$anzahl.'</legend>
				<p class="betrag"><label>Betrag</label><input name="'.$anzahl.'betrag" type="text" size="8" maxlength="9">&#8364;</p>
				<p class="zweckquelle"><label>Zweck/Quelle</label><input name="'.$anzahl.'ZweckQuelle" type="text" size="30"></p>
				<p>
				'.$kategorie->getKategorien($anzahl).'
				</p>
				'.$this->getTimestampform($anzahl).'
				
			</fieldset>';
		return $content;
	}
	
	function getFieldsetSonderzeichen($anzahl){
		$content = $this->getFieldset($anzahl);
		$content = $this->changeSonderzeichen($content);
		return $content;
	}
	
	function changeSonderzeichen($content){
		$letters = array('ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß');
		$htmlletters   = array('&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;');
		$output  = str_replace($letters, $htmlletters, $content);
		return $output;
	}
	
	function getUeberweisungsKontoField(){
		$kategorie = new showKategorien();
		$uekontoField = $kategorie->getKonten();
	}
	
	function getTimestampform($anzahl){
		$date = new DateTime();
		$date->setTimestamp(time());
		$currentDay = (int)$date->format('d');
		$currentMonth = (int)$date->format('m');
		$currentYear = (int)$date->format('Y');
		
		$contentdropdown .= "<div id='timestamp' class='timestamp'>";
		$contentdropdown .= "<fieldset>";
		$contentdropdown .= "<legend>Datum</legend>";
/*	 T A G	 D R O P D O W N	 B O X	 */
		$contentdropdown .= '<div class="dropdownbox">';
		$contentdropdown .= '<label>Tag</label>';
		$contentdropdown .= '<select class="timestamp" name="'.$anzahl.'day" size="1">';
		$contentdropdown .= '<option> </option>';
		for($day=1;$day<=31;$day++){
			if($day == $currentDay)
				$selected = " selected";
			else
				$selected = "";
			$contentdropdown .= '<option'.$selected.'>'.$day.'</option>';
		}
		$contentdropdown .= '</select>';
		$contentdropdown .= '</div>';
		
/*	 M O N A T	 D R O P D O W N	 B O X	 */
		$contentdropdown .= '<div class="dropdownbox">';
		$contentdropdown .= '<label>Monat</label>';
		$contentdropdown .= '<select class="timestamp" name="'.$anzahl.'month" size="1">';
		$contentdropdown .= '<option> </option>';
		for($month=1;$month<=12;$month++){
			if($month == $currentMonth)
				$selected = " selected";
			else
				$selected = "";
			$contentdropdown .= '<option'.$selected.'>'.$month.'</option>';
		}
		$contentdropdown .= '</select>';
		$contentdropdown .= '</div>';
		
/*	 J A H R	 D R O P D O W N	 B O X	 */
		$contentdropdown .= '<div class="dropdownbox">';
		$contentdropdown .= '<label>Jahr</label>';
		$jahrvon= $currentYear - 3;
		
		$contentdropdown .= '<select class="timestamp" name="'.$anzahl.'year" size="1">';
		$contentdropdown .= '<option> </option>';
		for($year=$currentYear;$year>=$jahrvon;$year--){
			if($year == $currentYear)
				$selected = " selected";
			else
				$selected = "";
			$contentdropdown .= '<option'.$selected.'>'.$year.'</option>';
		}
		$contentdropdown .= '</select>';
		$contentdropdown .= '</div>';
		$contentdropdown .= '</fieldset>';
		$contentdropdown .= '</div>';
	
		return $contentdropdown;
	}
}

$request = $_POST;
if($request["jscontent"] == "getFieldset"){
	$contentClass = new contentClass();
	$anzahl = $request["anzahl"];
	$content = $contentClass->getFieldsetSonderzeichen($anzahl);
	echo $content;
}

if($request["jscontent"] == "getUEKonten"){
	$contentClass = new contentClass();
	//$nr = // Nummer des Datensatzes HIER HABE ICH AUFGEHÖRT!!
	//$content = $contentClass->getUeberweisungsKontoFieldnr();
	echo $content;
}
?>