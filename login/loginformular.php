<?php 
require_once("../navigation.php");

class loginformular{

	function loginformular (){
		session_start ();
		echo $this->getContent();
	}

	function getContent(){
		$navigation = new navigation();
		
		$fehler = '';
		if (isset ($_REQUEST["fehler"])){ 
			$fehler = "Die Zugangsdaten waren ung&uuml;ltig."; 
		}
		$content .= '
		<html> 
			<head> 
				<title>Login</title> 
				<link rel="stylesheet" type="text/css" href="../styles.css">
			</head> 
	
			<body> 
			'.$navigation->getContent("Login","login").'
			<div id="login" class="div">
			<form action="login.php" method="post"> 
				<p>Name: <input type="text" name="name" size="20"></p> 
				<p>Kennwort: <input type="password" name="pwd" size="20"></p> 
				<p><input type="submit" value="Login"></p>
			</form> 
			</div>
			'.$fehler.'
			</body> 
		</html>
		';
		return $content;
	}

}

$loginformular = new loginformular();

?>