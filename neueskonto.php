<?php 

// Seite zum Hinzufügen eines neuen "Kontos"
require_once("subnavigation.php");
include ("login/checkuser.php"); 

class neueskonto{

	var $seitenname;
 
	function neueskonto(){
		$this->seitenname = "Neues Konto hinzuf&uuml;gen";
		echo $this->getContent();
	}
	
	function getContent(){
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
				'.$subnavigation->getContent("konto").'
				<div class="div" id="neueskonto">
				<form action="kontenspeichern.php" method="post">
					<div id="neueskontodiv" class="kontodiv"><label class="neueskonto" for="neueskontoinp">Neues Konto anlegen </label><input class="neueskonto" id="neueskontoinp" name="neueskonto" type="text" size="30"></div>
					<div id="kontostanddiv" class="kontodiv"><label for="kontostand">Anf&auml;nglicher Kontostand </label><input id="kontostand" name="kontostand" type="text" size="30"></div>
					<p><input type="submit" value="anlegen"></p>
				</form>
				</div>
			</body>
		</html>
		';
		return $content;
	}
}
$neueskonto = new neueskonto();

?>