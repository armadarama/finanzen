<?php 

// Seite zum Hinzufügen von neuen Kategorien wie zb. Nahrungsmittel, Tanken, Party etc.
include ("login/checkuser.php"); 
require_once("subnavigation.php");

class neuekategorien{
	
	var $subnavi;
	
	var $seitenname;

	function neuekategorien(){
		$this->seitenname = "Kategorie hinzuf&uuml;gen";
		$this->subnavi = new subnavigation();
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$content = '
		<html>
			<header>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
			</header>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$this->subnavi->getContent("kategorien").'
				<div class="div" id="kategorien">
				<form action="kategorienspeichern.php" method="post">
					<label for="kategorie">Neue Kategorie</label><input id="kategorie" name="kategorie" type="text" size="30">
					<select name="art" size="1">
						<option value="einnahmen">Einnahmen</option>
						<option selected value="ausgaben">Ausgaben</option>
					</select> 
					<p><input type="submit" value="anlegen"></p>
				</form>
				</div>
			</body>
		</html>
		';
		return $content;
	}
}
$kategorien = new neuekategorien();

?>