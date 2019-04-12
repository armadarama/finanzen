<?php

require_once("subnavigation.php");
include ("login/checkuser.php"); 

class reportauswertung {

	var $uid;
	var $seitenname;
	var $global;

	function reportauswertung(){
		$this->global = new globalefunktionen();
		$this->seitenname = "Report";
		$this->uid = $_SESSION["user_id"];
		$content = $this->content();
		echo $content;
	}
	
	function content(){
//		$url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$navigation = new navigation();
		$subnavigation = new subnavigation();
		$content = '
			<html>
				<header>
					<title>'.$this->seitenname.'</title>
					<link rel="stylesheet" type="text/css" href="styles.css">
				</header>
				
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$subnavigation->getContent("auswertung").'
					'.$this->getForm().'
				</body>
			</html>
			';
		return $content;
	}
	
	function getForm(){
		$this->throwGET();
		$this->sqlconnection();
		return "";
	}
	
// wirft was falsches aus wegen der Datumsanzeige. <2010 und >2010 zugleich geht nicht. Es muss im Select immer ein monat ausgewählt werden! Tag wird auf 1 gesetzt wenn nichts ausgewählt ist!
	function throwGET(){
		$vonday = $_GET["vonday"];
		$vonmonth = $_GET["vonmonth"];
		$vonyear = $_GET["vonyear"];
		$bisday = $_GET["bisday"];
		$bismonth = $_GET["bismonth"];
		$bisyear = $_GET["bisyear"];
		
		$kategorien = explode(",",$_GET["kategorie"]);
		$konten = explode(",",$_GET["konto"]);
		foreach($konten as $konto){
			$value = explode('-',$konto);
			$kontenarray[$value["1"]]["kontoname"] = $value["0"];
			$kontenarray[$value["1"]]["kontoid"] = $value["1"];
			$kontoids[] = $value["1"];
		}
		$kontoids = implode(",", $kontoids);
		$art = $_GET["art"]; 
		$datumSelect = $this->global->datumToQuery($vonday,$vonmonth,$vonyear,$bisday,$bismonth,$bisyear,$art);
		$vondatum = $vonyear."-".$vonmonth."-".$vonday;
		$bisdatum = $bisyear."-".$bismonth."-".$bisday;
		
		$sqlquery = "SELECT * FROM (".$art.",konten) WHERE ".$datumSelect." AND konten.id=(".$kontoids.") AND ".$art.".uid=".$this->uid;
		var_dump($sqlquery);
	}
	
	function sqlconnection(){
		$sql = new sql("blabla");
//		$this->res = $sql->sql_res();
		$sql->close();
	}
}
$reportauswertung = new reportauswertung();
?>