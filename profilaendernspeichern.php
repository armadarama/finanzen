<?php 

include ("login/checkuser.php"); 

class profilaendernspeichern{

	var $uid;
	var $nickname;
	var $kennwort;
	var $nachname;
	var $vorname;

	function profilaendernspeichern(){
		$this->seitenname = "Profil gespeichert";
		$this->uid = $_SESSION["user_id"];
		$this->getPOST();
		echo $this->getContent();
	}

	function getPOST(){
		var_dump($_POST);
	}
	
	function getContent(){
		$navigation = new navigation();
		$content .= '
			<html>
				<header>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
				</header>
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$this->getForm().'
				</body>
			</html>
		';
		return $content;
	}
	
	function getForm(){
		$content .= '
			<div class=" div profil">
				<label>gespeichert</label>
			</div>
		';
		return $content;
	}
}

$profil = new profilaendernspeichern();

?>