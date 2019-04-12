<?php 

/* Language labels for plugin "tx_tmpnewhire_pi1" */

class local_lang{
	
	static public $lang = Array(
		'default' => Array (
		////// Mein Profil //////
			'leer'  =>  " ",
			'fehlerdivuser'  =>  "Benutzername: Sie haben keine gültige Email-Adresse eingegeben.",
			'fehlerdivuser2'  =>  "Benutzername: Ihre Emailadresse wurde nicht verändert, da sie Ihre alte Emailadresse eingegeben haben.",
			'fehlerdivuser3'  =>  "Benutzername: Ihre Emailadresse wurde nicht verändert, da diese bereits in einem anderen Profil existiert.",
			'fehlerdivpass'  =>  "Passwort: Das Password wurde nicht geändert; es muss mindestens 6 Zeichen, 1 Kleinbuchstaben, 1 Großbuchstaben und 1 Zahl enthalten.",
			'fehlerdivpass2'  =>  "Passwort: Ihre Passwort wurde nicht verändert, da Sie kein Passwort eingegeben haben.",
			'fehlerdivpass3'  =>  "Altes Passwort: Das eingegebene Passwort stimmt nicht mit dem alten &uuml;berein.",
			'fehlerdivpass4'  =>  "Passwort wiederholen: Bitte geben Sie zwei Mal das selbe Passwort ein.",
			'matchdivuser'  =>  "Benutzername: Ihre Emailadresse wurde geändert.",
			'matchdivpass'  =>  "Passwort: Ihr Passwort wurde geändert.",
		)
	);
	
	static function getLocalLang($value,$sprache = 'default'){
		return self::$lang[$sprache][$value];
	}
}

?>