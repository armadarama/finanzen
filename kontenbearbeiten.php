<?php

// Seite zum Hinzufügen von neuen Kategorien wie zb. Nahrungsmittel, Tanken, Party etc.
include ("login/checkuser.php"); 
require_once("subnavigation.php");

class kontenbearbeiten{
	
	var $subnavi;
	var $uid;
	var $seitenname;
	var $global;
	var $fehler;

	function kontenbearbeiten(){
		$this->global = new globalefunktionen();
		$this->uid = $_SESSION["user_id"];
		$this->seitenname = "Konten bearbeiten";
		$this->subnavi = new subnavigation();
		$this->saveChanges();
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$content = '
		<html>
			<header>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
				<script type="text/javascript">
				function ask_first(link, question)
				{
					if (typeof(question) == "undefined")
						question = "Soll die Aktion wirklich durchgefuehrt werden?"
					return window.confirm(question);
				}
				</script>
			</header>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$this->subnavi->getContent("konto").'
				<div class="div" id="konten">
				'.$this->getForm().'
				</div>
			</body>
		</html>
		';
		return $content;
	}
	
	function getForm(){
		$konten = $this->listKonten();
		$content .= '
			<table border="1">
				<tr>
					<th>Name</th>
					<th>Kontostand</th>
					<th>Namen &auml;ndern</th>
					<th>Kontostand &auml;ndern</th>
					<th>L&ouml;schen</th>
				</tr>';
		if(is_array($konten)){
			foreach($konten as $id => $konto){
				
				if($konto["kontostand"]>=0){
					$classredgreen = "green";
				}else{
					$classredgreen = "red";
				}
				$kontoname = $konto["kontoname"];
				$content .= '<tr>';
				$content .= '<td>'.$this->global->htmlValidate($kontoname).'</td>';
				$content .= '<td class="'.$classredgreen.'">'.$this->global->htmlValidate($konto["kontostand"]).'</td>';
				$content .= '<td>'.$this->button($id, $kontoname, "aendern").'</td>';
				$content .= '<td>'.$this->button($id, $kontoname, "kontostand aendern").'</td>';
				$content .= '<td>'.$this->button($id, $kontoname, "loeschen").'</td>';
				$content .= '</tr>';
			}
		}
		$content .= '
			</table>';
		return $content;
	}
	
	function button($id, $konto, $aktion){
		$content .= '
		<form action="kontenbearbeiten.php" method="get">';
		
		if($aktion == "aendern"){
			$text = "'Soll das Konto `".$this->global->htmlValidate($konto)."` wirklich umbenannt werden?'";
			$content .= '
				<input name="konto" type="text" size="30" maxlength="30">
				<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'">
			';
		}
		if($aktion == "kontostand aendern"){
			$text = "'Soll der Kontostand des Kontos `".$this->global->htmlValidate($konto)."` wirklich geaendert werden?'";
			$content .= '
				<input name="kontostand" type="text" size="15" maxlength="15">
				<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'"><br>
				'.$this->fehler.'
			';
		}
		$content .='
			<input name="id" type="hidden" value="'.$id.'">
			<input name="aktion" type="hidden" value="'.$aktion.'">';
		
		if($aktion == "loeschen"){
			$text = "'Soll das Konto `".$this->global->htmlValidate($konto)."` wirklich geloescht werden?'";
			$content .= '<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'">';
		}
		$content .= '</form>';
		return $content;
	}
	
	function saveChanges(){
		$id = $_GET["id"];
		if($_GET["aktion"] == "loeschen"){
			$sqlquery = "DELETE FROM konten WHERE id = ".$id;
			$this->sqlcon($sqlquery);
		}else if($_GET["aktion"] == "aendern"){
			$name = $_GET["konto"];
			$sqlquery = "UPDATE konten SET kontoname = '".$name."' WHERE id =".$id;
			$this->sqlcon($sqlquery);
		}else if($_GET["aktion"] == "kontostand aendern"){
			$kontostand = $_GET["kontostand"];
			$kontostand = str_replace(",",".",$kontostand);
			if(is_numeric($kontostand)){
				$kontostand = round($kontostand, 2);
				$sqlquery = "UPDATE konten SET kontostand = '".$kontostand."' WHERE id =".$id;
				$this->sqlcon($sqlquery);
			}else{
				$this->fehler = '<label class="red">Bitte eine Zahl eingeben!</label>';
			}
		}
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($sqlquery);
		$sql->close();
		return $res;
	}
	
	function listKonten(){
		$sqlquery = "SELECT id, kontoname, kontostand FROM konten WHERE uid=".$this->uid;
		$res = $this->sqlcon($sqlquery);
		while($row = mysql_fetch_assoc($res)){
			$id = $row["id"];
			$konto = $row["kontoname"];
			$konten[$id]["kontoname"] = $row["kontoname"];
			$konten[$id]["kontostand"] = $row["kontostand"];
		}
		natcasesort($konten);
		return $konten;
	}
}
$kontenbearbeiten = new kontenbearbeiten();

?>