<?php 

// Seite zum Hinzufügen von neuen Kategorien wie zb. Nahrungsmittel, Tanken, Party etc.
require_once("subnavigation.php");
include ("login/checkuser.php");

class kontospeichern{

	var $kontostand;
	var $kontoname;
	var $uid;
	var $seitenname;
	var $global;

	function kontospeichern(){
		$this->global = new globalefunktionen();
		$this->seitenname = "Konto hinzuf&uuml;gen";
		if($_POST != false && $_POST["neueskonto"] != ""){
			$this->getPost();
			$this->sqlcon($this->insert());
			$fehler = 'Konto: "'.$this->kontoname.'" wurde mit einem Betrag von "'.$this->global->htmlValidate($this->kontostand).'" erfolgreich angelegt.';
		}else if($_POST == false){
		}else{
			$fehler =  "Kontoname darf nicht leer sein!";
		}
		echo $this->getContent($fehler);
	}
	
	function getContent($fehler = ""){
		$navigation = new navigation();
		$subnavigation = new subnavigation();
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
				'.$subnavigation->getContent("konto").'
				'.$fehler.'
			</body>
		</html>
		';
		return $content;
	}
	
	function getPost(){
		$this->uid = $_SESSION["user_id"];
		$this->kontoname = $_POST["neueskonto"];
		$this->validateBetrag();
	}
	
	function validateBetrag(){
		$betrag = $_POST["kontostand"];
		if($betrag == ""){
			$this->kontostand = 0;
		}else{
			$this->kontostand = $this->numeralValidation($betrag);
		}
	}
	
	function numeralValidation($betrag){
		$betrag = str_replace(",",".",$betrag);
		$betrag = floatval($betrag);
		$betrag = round($betrag, 2);
		return $betrag;
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($sqlquery);
		$sql->close();
	}
	
	function insert(){
		$sqlquery = '
			INSERT INTO 
				konten(
					uid,
					kontoname, 
					kontostand)
				VALUES(
					'.$this->uid.',
					"'.$this->kontoname.'",
					"'.$this->kontostand.'")';
		return $sqlquery;
	}
	
}
$konto = new kontospeichern();

?>