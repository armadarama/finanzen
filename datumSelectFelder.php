<?php 

include ("login/checkuser.php"); 

class datumSelectFelder {

	var $day;
	var $month;
	var $year;
	var $global;

	function datumSelectFelder(){
		$this->global = new globalefunktionen();
	}

	function getContent( $namedesdivs="", $day=1,$month=1,$year=1, $anzahl="", $header="datum", $input=""){
		$this->getOpinions($day,$month,$year);
		return $this->getTimestampform($anzahl, $header, $input, $namedesdivs);
	}

	function getOpinions($day="",$month="",$year=""){
		if($day != "")
			$this->day = 1;
		else
			$this->day = 0;
		if($month != "")
			$this->month = 1;
		else
			$this->month = 0;
		if($year != "")
			$this->year = 1;
		else
			$this->year = 0;
	}
	
	function getTimestampform($anzahl, $header, $submit="", $namedesdivs){
		$contentdropdown .= "<div class='timestamp ".$namedesdivs."'>";
		$contentdropdown .= "<fieldset>";
		$contentdropdown .= "<legend>".$this->global->htmlValidate($namedesdivs)." ".$this->global->htmlValidate($header)."</legend>";
/*	 T A G	 D R O P D O W N	 B O X	 */
		if($this->day == 1){
			$contentdropdown .= '<div class="dropdownbox '.$namedesdivs.'">';
			$contentdropdown .= '<label>Tag</label>';
			$contentdropdown .= '<select class="timestamp" name="'.$anzahl.$namedesdivs.'day" size="1">';
			$contentdropdown .= '<option> </option>';
			for($day=1;$day<=31;$day++){
				$selectedDay = "";
				if($_REQUEST[$anzahl.$namedesdivs.'day'] == $day)
					$selectedDay = " selected";
				$contentdropdown .= '<option'.$selectedDay.'>'.$day.'</option>';
			}
			$contentdropdown .= '</select>';
			$contentdropdown .= '</div>';
		}
/*	 M O N A T	 D R O P D O W N	 B O X	 */
		if($this->month == 1){
			$contentdropdown .= '<div class="dropdownbox '.$namedesdivs.'">';
			$contentdropdown .= '<label>Monat</label>';
			$contentdropdown .= '<select class="timestamp" name="'.$anzahl.$namedesdivs.'month" size="1">';
			$contentdropdown .= '<option> </option>';
			for($month=1;$month<=12;$month++){
				$selectedMonth = "";
				$date = new DateTime();
				$todayMonth = date_format($date, 'm');
				if($_REQUEST[$anzahl.$namedesdivs.'month'] != NULL){
					if($_REQUEST[$anzahl.$namedesdivs.'month'] == $month){
						$selectedMonth = " selected";
					}
				}else if($todayMonth == $month){
					$selectedMonth = " selected";
				}
				$contentdropdown .= '<option'.$selectedMonth.'>'.$month.'</option>';
			}
			$contentdropdown .= '</select>';
			$contentdropdown .= '</div>';
		}
/*	 J A H R	 D R O P D O W N	 B O X	 */
		if($this->year == 1){
			$contentdropdown .= '<div class="dropdownbox '.$namedesdivs.'">';
			$contentdropdown .= '<label>Jahr</label>';
			$aktuellerZeitpunkt = new DateTime(date('Y-m-d'),new DateTimeZone('Europe/Berlin'));
			$aktuellesJahr = $aktuellerZeitpunkt->format('Y');
			$jahrvon= $aktuellesJahr - 3;
			
			$contentdropdown .= '<select class="timestamp" name="'.$anzahl.$namedesdivs.'year" size="1">';
			for($year=$aktuellesJahr;$year>=$jahrvon;$year--){
				$selectedYear = "";
				if($_REQUEST[$anzahl.$namedesdivs.'year'] == $year)
					$selectedYear = " selected";
				$contentdropdown .= '<option'.$selectedYear.'>'.$year.'</option>';
			}
			$contentdropdown .= '</select>';
			$contentdropdown .= '</div>';
		}
		if($submit != "")
			$contentdropdown .= '<input type=submit value="'.$this->global->htmlValidate("Monat auswählen").'">';
		$contentdropdown .= '</fieldset>';
		$contentdropdown .= '</div>';
	
		return $contentdropdown;
	}
}


?>