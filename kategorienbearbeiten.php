<?php

// Seite zum Hinzufügen von neuen Kategorien wie zb. Nahrungsmittel, Tanken, Party etc.
include ("login/checkuser.php"); 
require_once("subnavigation.php");

class kategorienbearbeiten{
	
	var $subnavi;
	var $uid;
	var $seitenname;
	var $global;

	function kategorienbearbeiten(){
		$this->global = new globalefunktionen();
		$this->uid = $_SESSION["user_id"];
		$this->seitenname = "Kategorie bearbeiten";
		$this->subnavi = new subnavigation();
		$this->saveChanges();
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$content = '
		<html>
			<head>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
				<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
				<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
				<script type="text/javascript">
					$(document).ready(function(){ 
						$(".sortable").tablesorter(); 
					});
				</script>
				<script type="text/javascript">
				function ask_first(link, question)
				{
					if (typeof(question) == "undefined")
						question = "Soll die Aktion wirklich durchgefuehrt werden?"
					return window.confirm(question);
				}
				</script>
			</head>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$this->subnavi->getContent("kategorien").'
				<div class="div" id="kategorien">
				'.$this->getForm().'
				</div>
			</body>
		</html>
		';
		return $content;
	}
	
	function getForm(){
		$kategorien = $this->listKategorien();
		if(is_array($kategorien)){
			foreach($kategorien as $art => $kategoriearray){
				$content .= '
					<div style="display:block;float:left;">
					<h2>'.$this->global->htmlValidate($art).'</h2>
					<table class="sortable" border="1">
						<thead>
						<tr>
							<th>Kategorie</th>
							<th>Namen &auml;ndern</th>
							<th>Status</th>
						</tr>
						</thead>';
				if(is_array($kategoriearray)){
					$content .='<tbody>';
					foreach($kategoriearray as $kategorie => $value){
						$id = $value["id"];
						$aktiv = $value["aktiv"];
						$class = "";
						if($aktiv == 0)
							$class = "inaktiv";
						$content .= '<tr>';
						$content .= '<td class="'.$class.'">'.$this->global->htmlValidate($kategorie).'</td>';
						$content .= '<td>'.$this->button($id, $kategorie, "aendern").'</td>';
						if($aktiv == 0)
							$content .= '<td>'.$this->button($id, $kategorie, "aktivieren").'</td>';
						else if($aktiv == 1)
							$content .= '<td>'.$this->button($id, $kategorie, "deaktivieren").'</td>';
						$content .= '</tr>';
					}
					$content .='</tbody>';
				}
				$content .= '</table></div>';
			}
		}
		return $content;
	}
	
	function button($id, $kategorie, $aktion){
		$content .= '
		<form action="kategorienbearbeiten.php" method="get">';
		
		if($aktion == "aendern"){
			$text = "'Soll die Kategorie `".$this->global->htmlValidate($kategorie)."` wirklich umbenannt werden?'";
			$content .= '
				<input name="kategorie" type="text" size="30" maxlength="30">
				<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'">
			';
		}
		
		$content .='
			<input name="id" type="hidden" value="'.$id.'">
			<input name="aktion" type="hidden" value="'.$aktion.'">';
		
		if($aktion == "aktivieren"){
			$text = "'Soll die Kategorie `".$this->global->htmlValidate($kategorie)."` wirklich aktiviert werden?'";
			$content .= '<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'">';
		}
		if($aktion == "deaktivieren"){
			$text = "'Soll die Kategorie `".$this->global->htmlValidate($kategorie)."` wirklich deaktiviert werden?'";
			$content .= '<input onclick="return ask_first(this, '.$text.');" type="submit" value="'.$this->global->htmlValidate($aktion).'">';
		}
		$content .= '</form>';
		return $content;
	}
	
	function saveChanges(){
		$id = $_GET["id"];
		if($_GET["aktion"] == "aktivieren"){
			$sqlquery = "UPDATE kategorien SET aktiv = 1 WHERE id =".$id;
			$this->sqlcon($sqlquery);
		}else if($_GET["aktion"] == "deaktivieren"){
			$sqlquery = "UPDATE kategorien SET aktiv = 0 WHERE id =".$id;
			$this->sqlcon($sqlquery);
		}else if($_GET["aktion"] == "aendern"){
			$name = $_GET["kategorie"];
			$sqlquery = "UPDATE kategorien SET kategorie = '".$name."' WHERE id =".$id;
			$this->sqlcon($sqlquery);
		}
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($sqlquery);
		$sql->close();
		return $res;
	}
	
	function listKategorien(){
		$sqlquery = "SELECT id, kategorie, art, aktiv FROM kategorien WHERE uid=".$this->uid;
		$res = $this->sqlcon($sqlquery);
		if(mysql_num_rows($res) > 0){
			while($row = mysql_fetch_assoc($res)){
				$kategorien[$row["art"]][$row["kategorie"]]["id"] = $row["id"];
				$kategorien[$row["art"]][$row["kategorie"]]["aktiv"] = $row["aktiv"];
			}
		}
		if(is_array($kategorien)){
			foreach($kategorien as $art => $kategorie){
				natcasesort($kategorie);
				$kategorien[$art] = $kategorie;
			}
		}
		return $kategorien;
	}
}
$kategorienbearbeiten = new kategorienbearbeiten();

?>