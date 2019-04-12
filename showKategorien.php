<?php 

include ("login/checkuser.php"); 
class showKategorien{

	var $uid;

	function showKategorien(){
		$this->uid = $_SESSION["user_id"];
	}

	function getKategorien($anzahl="", $multi="", $selectAnzahl=1, $ersteRowFrei=1){
		$sql = new sql("blabla");
		$resKategorien = $sql->sql_res($this->selectKategorien());
		$resKonten = $sql->sql_res($this->selectKonten());
		$sql->close();
		$content .= $this->listKategorien($anzahl, $resKategorien, $multi, $selectAnzahl, $ersteRowFrei);
		$content .= $this->listKonten($anzahl, $resKonten, $multi, $selectAnzahl, $ersteRowFrei);
		return $content;
	}

	function getKonten($anzahl="", $multi="", $selectAnzahl=1, $ersteRowFrei=1){
		$sql = new sql("blabla");
		$resKonten = $sql->sql_res($this->selectKonten());
		$sql->close();
		$content .= $this->listKonten($anzahl, $resKonten, $multi, $selectAnzahl, $ersteRowFrei);
		return $content;
	}
	
	private function listKategorien($anzahl, $res, $multiSelect, $selectAnzahl, $ersteRowFrei){
		if($multiSelect == 1)
			$multiselect = " multiple";
		$content .= '<div id="showkategorien" class="show">';
		$content .= '<label>Kategorie</label>';
		$content .= '<select name="'.$anzahl.'kategorie" size="'.$selectAnzahl.'"'.$multiselect.'>';
		if($ersteRowFrei == 1)
			$contenteinnahmen .= '<option value=""> </option>';
		$contenteinnahmen .= '<optgroup label="Einnahmen">';
		$contentausgaben .= '<optgroup label="Ausgaben">';
		if(mysqli_num_rows($res) > 0){
			while ($row = mysql_fetch_assoc($res)) {
				if($row["art"] == "einnahmen"){
					$value = " [+]";
					$contenteinnahmen .= '<option value="'.$row['kategorie'].','.$row['art'].'">'.$row['kategorie'].$value.'</option>';
				}else if($row["art"] == "ausgaben"){
					$value = " [-]";
					$contentausgaben .= '<option value="'.$row['kategorie'].','.$row['art'].'">'.$row['kategorie'].$value.'</option>';
				}
			}
		}
		$content .= $contenteinnahmen;
		$content .= $contentausgaben;
		$content .= "</select>";
		$content .= "</div>";
		return $content;
	}

	private function listKonten($anzahl, $res, $multiSelect, $selectAnzahl, $ersteRowFrei, $ueberweisungskonto = false){
		if($ueberweisungskonto){
			$kontoName = "&Uuml;berweisungskonto";
			$kontoNameSelect = "uekonto";
			$showkonten = "showuekonten";
		} else {
			$kontoName = "Konto";
			$kontoNameSelect = "konto";
			$showkonten = "showkonten";
		}
		if($multiSelect == 1)
			$multiselect = " multiple";
		$content .= '<div id="'.$showkonten.'" class="show">';
		$content .= '<label>'.$kontoName.'</label>';
		$content .= '<select name="'.$anzahl.$kontoNameSelect.'" size="'.$selectAnzahl.'"'.$multiselect.'>';
		if(mysqli_num_rows($res) > 0){
			while ($row = mysql_fetch_assoc($res)) {
					$content .= '<option value="'.$row['kontoname'].'-'.$row['id'].'">'.$row['kontoname'].'</option>';
			}
		}
		$content .= $contenteinnahmen;
		$content .= $contentausgaben;
		$content .= "</select>";
		$content .= "</div>";
		return $content;
	}
	
	private function selectKategorien(){
		$sqlquery = 'Select kategorie, art, aktiv FROM kategorien where aktiv=1 AND uid='.$this->uid.' ORDER by kategorie';
		return $sqlquery;
	}
	
	private function selectKonten(){
		$sqlquery = 'Select id, kontoname, kontostand FROM konten where uid='.$this->uid.' ORDER by kontoname ASC';
		return $sqlquery;
	}
	
}
?>