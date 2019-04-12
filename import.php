<?php 

// Seite zum Hinzufügen eines neuen "Kontos"
require_once("subnavigation.php");
include ("login/checkuser.php"); 
require_once("import/import.php");

class importContent{

	var $seitenname;
 
	function importContent(){
		$this->seitenname = "Import";
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$subnavigation = new subnavigation();
		$import = new Import();
		$importContent = $import->startImport();
		$content = '
		<html>
			<header>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
			</header>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$subnavigation->getContent("konto").'
				<div class="div">
				'.$importContent.'
				</div>
			</body>
		</html>
		';
		return $content;
	}
}
$importContent = new importContent();

?>