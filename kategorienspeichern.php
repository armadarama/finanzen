<?php 

// Seite zum Hinzufügen von neuen Kategorien wie zb. Nahrungsmittel, Tanken, Party etc.
require_once("navigation.php");
include ("login/checkuser.php"); 

class kategorienspeichern{

	var $kategorie;
	var $art;
	var $uid;
	var $seitenname;

	function kategorienspeichern(){
		$this->seitenname = "Kategorie hinzuf&uuml;gen";
		if($_POST != false && $_POST["kategorie"] != ""){
			$this->getPost();
			$this->sqlcon($this->insert());
			$fehler = 'Kategorie "'.$_POST["kategorie"].'" wurde erfolgreich unter "'.$_POST["art"].'" angelegt.';
		}else if($_POST == false){
		}else{
			$fehler =  "Kategorie darf nicht leer sein!";
		}
		echo $this->getContent($fehler);
	}
	
	function getContent($fehler = ""){
		$navigation = new navigation();
		$fehler = '
			<div id="fehler">
				<label id="fehler">
				'.$fehler.'
				</label>
			</div>
		';
		$content = '
		<html>
			<header>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
			</header>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$fehler.'
			</body>
		</html>
		';
		return $content;
	}
	
	function getPost(){
		$this->uid = $_SESSION["user_id"];
		$this->kategorie = $_POST["kategorie"];
		$this->art = $_POST["art"];
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($sqlquery);
		$sql->close();
	}
	
	function insert(){
		$sqlquery = '
			INSERT INTO 
				kategorien(
					uid,
					kategorie,
					art,
					aktiv)
				VALUES(
					'.$this->uid.',
					"'.$this->kategorie.'",
					"'.$this->art.'",
					1)';
		return $sqlquery;
	}
	
}
$kategorien = new kategorienspeichern();

?>