<?php
class showKategorienImport{

	var $user_id;
	
	function setUid($user_id){
		$this->user_id = $user_id;
	}
	
	function getKategorienOnly($vermKat="", $anzahl="", $geldfluss="", $kategorieRows, $multi="", $selectAnzahl=1){
		$content = $this->listKategorien($vermKat, $anzahl, $geldfluss, $kategorieRows, $multi, $selectAnzahl);
		return $content;
	}
	
	private function listKategorien($vermKat="", $anzahl, $geldfluss="", $kategorieRows, $multiSelect = 0, $selectAnzahl){
		if($multiSelect == 1)
			$multiselect = " multiple";
		else
			$multiselect = "";
		$content = "";
		$content .= '<p id="showkategorien" class="show">';
		$content .= '<span>Kategorie</span>';
		$content .= '<select name="'.$anzahl.'-kategorie" size="'.$selectAnzahl.'"'.$multiselect.'>';
		$contenteinnahmen = "";
		$contenteinnahmen .= '<option value=""> </option>';
		if($geldfluss == "H")$contenteinnahmen .= '<optgroup label="Einnahmen">';
		$contentausgaben = "";
		if($geldfluss == "S")$contentausgaben .= '<optgroup label="Ausgaben">';
		$c = 0;
		foreach($kategorieRows as $row){
			$c++;
			$selected = "";
			if($vermKat == $row['kategorie']){
				$selected = 'selected = "selected"';
			}
			if($row["art"] == "einnahmen" && $geldfluss == "H"){
				$value = " [+]";
				//if($c == 19)$selected = 'selected = "selected"';
				$contenteinnahmen .= '<option '.$selected.' value="'.$row['kategorie'].'">'.$row['kategorie'].$value.'</option>';
			}else if($row["art"] == "ausgaben" && $geldfluss == "S"){
				$value = " [-]";
				//if($c == 3)$selected = 'selected = "selected"';
				$contentausgaben .= '<option '.$selected.' value="'.$row['kategorie'].'">'.$row['kategorie'].$value.'</option>';
			}
		}
		$content .= $contenteinnahmen;
		$content .= $contentausgaben;
		$content .= "</select>";
		$content .= "</p>";
		return $content;
	}
	
	function selectKategorien(){
		$sqlquery = 'Select kategorie, art, aktiv FROM kategorien where aktiv=1 AND uid='.$this->user_id.' ORDER by kategorie';
		return $sqlquery;
	}
	
}
?>