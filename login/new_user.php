<?php 

require_once("../navigation.php");
require_once("../sql_class/sql_con.php");
require_once("../globalefunktionen.php");

class newuser {

	var $nickname;
	var $email;
	var $kennwort;
	var $kennwortwiederholen;
	var $contentAngelegt;
	var $seitenname;
	var $global;
	private $hashcode;
	var $id;
	var $geldBeutelKontoName = "Geldbeutel";
	var $abhebenKategorieName = "Abheben";
	var $ueberweiseKategorieName = "Überweisen";
	
	function newuser(){ 
		$this->global = new globalefunktionen();
		$this->seitenname = "Neuen User anlegen";
		if($_POST != false){
			if($this->getPost() == true){
				if($this->kennwort != $this->kennwortwiederholen){
					$this->contentAngelegt = "Sie haben Ihr Kennwort 2 mal unterschiedlich eingegeben.";
				}else{
					$this->simulateHashcode();
					$this->sqlcon();
					$this->sendMail();
				}
			}else{
				$this->contentAngelegt = "Alle Felder m&uuml;ssen ausgef&uuml;llt werden.";
			}
		}
		echo $this->getContent();
		
	}

	function sendMail(){
		$this->toAdmin();
		$this->toUser();
	}
	
	function toAdmin(){
		$url = "http://test.tmp.de/bilfingerberger/typo3conf/ext/tmp_feed/";
//		$url = "www.armadarama.dyndns.org/finanzen/";
		
		$mailtext = '<html>
						<head>
								<title>Neuer User - Finanzen-Page</title>
						</head>
						<body>
							<h2>Ein Neuer User wurde auf der Finanzenseite angelegt</h2>
							
							<table border="1">
								<tr>
									<th>Nickname</th>
									<th>Vorname</th>
									<th>Nachname</th>
								</tr>
								<tr>
									<td>'.$this->global->htmlValidate($this->nickname).'</td>
									<td>'.$this->global->htmlValidate($this->vorname).'</td>
									<td>'.$this->global->htmlValidate($this->nachname).'</td>
								</tr>
							</table> 	
								<p> Um diesen User freizuschalten 
									<a href="'.$url.'userfreischalten.php?id='.$this->getId().'&hashcode='.$this->getHashcode().'">hier<a>
									klicken.</p>
						</body>
					</html>';
		
		$empfaenger = "dennis.sepeur@web.de"; //Mailadresse
		$absender	= "dennis.sepeur@web.de";
		$betreff	= "Neuer User - Finanzen-Page";
		
		$header	= "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
		
		$header .= "From: $absender\r\n";
		$header .= "Reply-To: $absender\r\n";
		// $header .= "Cc: $cc\r\n";	// falls an CC gesendet werden soll
		$header .= "X-Mailer: PHP ". phpversion();
		
		mail( $empfaenger,$betreff,$mailtext,$header);
		return TRUE;
	}
	
	function getId(){
		return $this->id;
	}
	
	function getHashcode(){
		return $this->hashcode;
	}
	
	function simulateHashcode(){
		$timestamp1 = time();
		$hash1 = $timestamp1 * rand(254,800)*0.1*(0.6/rand(460,1230));
		$timestamp2 = time();
		$hash2 = $timestamp2 * rand(654,800)*0.6*(0.2/rand(100,200));
		$hashcode = $hash1 + $hash2;
		$hashcode = str_replace(".","",$hashcode);
		$hashcode = str_replace(rand(1,9),"a",$hashcode);
		$hashcode = str_replace(rand(1,9),"/",$hashcode);
		$this->hashcode = $hashcode;
	}
	
	function toUser(){
		return TRUE;
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$sql->sql_res($this->insert());
		$this->id = mysqli_insert_id();
		
		$sql->sql_res($this->insertGeldbeutelKonto());
		$sql->sql_res($this->insertDefaultKategorieAbheben());
		$sql->sql_res($this->insertDefaultKategorieUeberweisen());
		
		$this->contentAngelegt = "<label>Danke für die Registrierung.</label>";
		$sql->close();
		return;
	}

	function insert(){
		$sqlquery = '
				INSERT INTO 
					benutzerdaten(
						nickname, 
						kennwort, 
						email, 
						hashcode)
					VALUES(
						"'.$this->nickname.'",
						"'.$this->kennwort.'",
						"'.$this->email.'",
						"'.$this->getHashcode().'")';
		return $sqlquery;
	}
	
	function insertGeldbeutelKonto(){
		$sqlquery = '
				INSERT INTO 
					konten(
						uid, 
						kontostand, 
						kontoname
						)
					VALUES(
						"'.$this->id.'",
						"0",
						"'.$this->geldBeutelKontoName.'"
						)';
		return $sqlquery;
	}
	
	function insertDefaultKategorieAbheben(){
		$sqlquery = '
				INSERT INTO 
					kategorien(
						uid, 
						kategorie,
						art, 
						aktiv
						)
					VALUES(
						"'.$this->id.'",
						"'.$this->abhebenKategorieName.'",
						"ausgaben",
						"1"
						);';
		return $sqlquery;
	}
	
	function insertDefaultKategorieUeberweisen(){
		$sqlquery = '
				INSERT INTO 
					kategorien(
						uid, 
						kategorie,
						art, 
						aktiv
						)
					VALUES(
						"'.$this->id.'",
						"'.$this->ueberweiseKategorieName.'",
						"ausgaben",
						"1"
						);';
		return $sqlquery;
	}
	
	function getPost(){
		$this->nickname = $_POST["nickname"];
		$this->kennwort = $_POST["kennwort"];
		$this->kennwortwiederholen = $_POST["kennwortwiederholen"];
		$this->email = $_POST["email"];
		if($this->nickname == '' || $this->kennwort == '' || $this->kennwortwiederholen == '' || $this->email == ''){
			return false;
		}else{
			return true;
		}
	}
	
	function getContent(){
		$navi = new navigation();
		$content .= '
		
		<html>
		<header>
		<title>'.$this->seitenname.'</title>
		<link rel="stylesheet" type="text/css" href="../styles.css">
		</header>
		<body>
			'.$navi->getContent($this->seitenname, "login").'
			<div class="div" id="newuser">
			'.$this->contentAngelegt.'
			<form action="new_user.php" method="post">
				<p>Nickname:<br><input name="nickname" type="text" size="30" maxlength="30"></p>
				<p>Email-Adresse:<br><input name="email" type="text" size="30" maxlength="40"></p></br>
				<p>Kennwort:<br><input name="kennwort" type="password" size="30" maxlength="30"></p>
				<p>Kennwort wiederholen:<br><input name="kennwortwiederholen" type="password" size="30" maxlength="30"></p>
				<p><input type="submit" value="anlegen"></p>
			</form>
			</div>
		</body>
		</html>
		';
		return $content;
	}
}

$newuser = new newuser();

?>