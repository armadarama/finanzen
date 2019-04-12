<?php

require_once("showKategorien.php");
include ("login/checkuser.php"); 

class eingang {

	var $anzahl;
	var $seitenname;

	function eingang(){
		$this->seitenname = "Cashflow";
		$this->getAnzahl();
		$content = $this->content();
		echo $content;
	}
	
	function content(){
//		$url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$navigation = new navigation();
		$content = '
			<html>
				<header>
					<title>'.$this->seitenname.'</title>
					<link rel="stylesheet" type="text/css" href="styles.css">
					<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
					<script type="text/javascript" src="js/addData.js"></script>
				</header>
				
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$this->getForm().'
				</body>
			</html>
			';
		
		//$content = "test";
		return $content;
	}
	
	function getAnzahl(){
		$this->anzahl = "1";
		if(isset($_POST["anzahl"]))
			$this->anzahl = $_POST["anzahl"];
	}
	
	function getForm(){
		$kategorie = new showKategorien();
		$contentClass = new contentClass();
		$content .= '<div class="div" id="eintraege">';
		$content .= '<form id="eintraege" action="speichern.php" method="post">';
		$content .= '<div id="submitup" class="submit"><input type="submit" name="submit" value="abschicken"></div>';
		$content .= '<div id="wrapfieldset"';
		$content .= '<div id="fieldsets">';
		for($anzahl=1;$anzahl<=1;$anzahl++){
			$content .= $contentClass->getFieldset($anzahl);
		}
		$content .= '</div>';
		$content .= '<div id="addDatensatz"></div></div>';
		$content .=	'
			<div id="submitdown" class="submit"> 
				<input id="anzahlhiddenfield" type="hidden" name="anzahl" value="'.$this->anzahl.'">
				<input type="submit" name="submit" value="abschicken">
			</div>
			</form>
			</div>
		';
		return $content;
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($sqlquery);
		$sql->close();
		return $res;
	}
	
}

$eingang = new eingang();

?>