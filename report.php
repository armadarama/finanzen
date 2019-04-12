<?php

require_once("subnavigation.php");
include ("login/checkuser.php"); 

class report {

	var $uid;
	var $seitenname;
	var $global;

	function report(){
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
		$datumSelectFeld = new datumSelectFelder();
		$showKategorien = new showKategorien();
		$content .= '
		<div class="div reportanfrage">
		<form action="reportauswertung.php" methode="get">
			'.$datumSelectFeld->getContent("von").$datumSelectFeld->getContent("bis").'
			'.$showKategorien->getKategorien("", 1, 5, 0).'
			'.$this->getFormArten().'
			<input class="input" type="submit" value=" Absenden ">
		</form>
		</div>
		';
		return $content;
	}
	
	function getFormArten(){
		$arten = $this->global->getArten();
		$content .= '<div class="arten">';
		foreach($arten as $art){
			$content .= '<input type="radio" name="art" value="'.$art.'">'.$this->global->htmlValidate($art);
		}
		$content .= '</div>';
		return $content;
	}
}
$report = new report();
?>