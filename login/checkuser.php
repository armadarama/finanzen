<?php 
/* Diese folgende Zeile führt dazu, dass, da die checkuser.php auf allen Seiten eingebunden wird, 
 alle Seiten wissen, dass sie die Zahlen in Euro angeben sollen, 
 sofern die Zahl mit money_format('%.2n',$value) umgewandelt wird. */
setlocale(LC_MONETARY, 'it_IT'); 
/* <-- ! --> */

/* Hier werden alle globalen Klassen eingebunden */
set_include_path("/volume1/web/finanzen-beta/");
require_once("globalefunktionen.php");
require_once("navigation.php");
require_once("sql_class/sql_con.php");
require_once("datumSelectFelder.php");
require_once("kategorieauswertung.php");
require_once("kontencashflow.php");
require_once("showKategorien.php");
require_once("contentClass.php");

session_start (); 
	if (!isset ($_SESSION["user_id"])) {
		header ("Location: login/loginformular.php"); 
	}
	
	$verfallszeit = 1800; # Sekunden dauert es bis man wieder automatisch ausgeloggt wird, sollte man nichts getan haben
	$neu = time();
	
	if (! isset($_SESSION['letzter_kontakt']))
	  $_SESSION['letzter_kontakt'] = $neu;
	
	if ($neu - $_SESSION['letzter_kontakt'] > $verfallszeit){
		# Session Daten löschen
			$_SESSION = array();
		# Keks nicht vergessen
		if (isset($_COOKIE[session_name()])) {
				setcookie(session_name(), '', time()-42000, '/');
			# Löschen der Session
				session_destroy();
				header ("Location: login/loginformular.php"); 
				exit;
		}
	}
#Wer hier noch da ist, ist entweder neu oder hat weniger als 5' getrödelt
$_SESSION['letzter_kontakt'] = $neu;

?>