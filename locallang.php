<?php 

/* Language labels for plugin "tx_tmpnewhire_pi1" */

class local_lang{
	
	static public $lang = Array(
		'default' => Array (
		////// Mein Profil //////
			'leer'  =>  " ",
			'fehlerdivuser'  =>  "Benutzername: Sie haben keine g�ltige Email-Adresse eingegeben.",
			'fehlerdivuser2'  =>  "Benutzername: Ihre Emailadresse wurde nicht ver�ndert, da sie Ihre alte Emailadresse eingegeben haben.",
			'fehlerdivuser3'  =>  "Benutzername: Ihre Emailadresse wurde nicht ver�ndert, da diese bereits in einem anderen Profil existiert.",
			'fehlerdivpass'  =>  "Passwort: Das Password wurde nicht ge�ndert; es muss mindestens 6 Zeichen, 1 Kleinbuchstaben, 1 Gro�buchstaben und 1 Zahl enthalten.",
			'fehlerdivpass2'  =>  "Passwort: Ihre Passwort wurde nicht ver�ndert, da Sie kein Passwort eingegeben haben.",
			'fehlerdivpass3'  =>  "Altes Passwort: Das eingegebene Passwort stimmt nicht mit dem alten &uuml;berein.",
			'fehlerdivpass4'  =>  "Passwort wiederholen: Bitte geben Sie zwei Mal das selbe Passwort ein.",
			'matchdivuser'  =>  "Benutzername: Ihre Emailadresse wurde ge�ndert.",
			'matchdivpass'  =>  "Passwort: Ihr Passwort wurde ge�ndert.",
		)
	);
	
	static function getLocalLang($value,$sprache = 'default'){
		return self::$lang[$sprache][$value];
	}
}

?>