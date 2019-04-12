<?php 

require_once ("user.php");
require_once ("Validate/Validate.php");
require_once ("locallang.php");

class output{
	
	private $user;
	
	function output(&$user){
		$this->user = $user;
	}
	
	// G E T - F O R M
		
		function getForm(){
		
			// F E H L E R T E X T 
				
				$komplettleer ='';
				
				$leer ='
						<label class="fehler">'.local_lang::getLocalLang('leer').'</label>
				';
				
				$fehlerdivuser = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivuser').'</label>
				';
				
				$fehlerdivuser2 = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivuser2').'</label>
				';
				
				$fehlerdivuser3 = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivuser3').'</label>
				';
				
				$fehlerdivpass = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivpass').'</label>
				';
				
				$fehlerdivpass2 = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivpass2').'</label>
				';
				
				$fehlerdivpass3 = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivpass3').'</label>
				';
				
				$fehlerdivpass4 = '
						<label class="fehler">'.local_lang::getLocalLang('fehlerdivpass4').'</label>
				';
				
				$matchdivuser = '
						<label class="match">'.local_lang::getLocalLang('matchdivuser').'</label>
				';
				
				$matchdivpass = '
						<label class="match">'.local_lang::getLocalLang('matchdivpass').'</label>
				';
			
			$username = $_POST["user"];
			$oldpass = $_POST["oldpass"];
			$newpass = $_POST["newpass"];
			$reenterpass = $_POST["reenterpass"];
			
			$fehleruserclass = '';
			$fehleroldpassclass = '';
			$fehlernewpassclass = '';
			$fehlerreenterpassclass = '';
			
			
			if(count($_POST) == 0){
				$fehler1 = $komplettleer;
				$fehler2 = $komplettleer;
				$divumdiefehler1und2 = '';
				$divumdiefehler1und2ende = '';
			}else {
				if($this->user->setUsername($username)){
					$fehler1 = $matchdivuser;
					$divumdiefehler1und2 = '<div class="fehler">';
					$divumdiefehler1und2ende = '</div>';
				}else if($username == $this->user->getUsername()){
					$fehler1 = $fehlerdivuser2;
					$divumdiefehler1und2 = '<div class="fehler">';
					$divumdiefehler1und2ende = '</div>';
					$fehleruserclass  = " fehlertext";
				}else if($this->user->existUsername($username)){
					$fehler1 = $fehlerdivuser3;
					$divumdiefehler1und2 = '<div class="fehler">';
					$divumdiefehler1und2ende = '</div>';
					$fehleruserclass  = " fehlertext";
				}else{
					$fehler1 = $fehlerdivuser;
					$divumdiefehler1und2 = '<div class="fehler">';
					$divumdiefehler1und2ende = '</div>';
					$fehleruserclass  = " fehlertext";
				}
				
				if ($this->user->existPassword($oldpass)){
					if($newpass == $reenterpass){
						if ($this->user->setPassword($newpass)){
							$fehler2 = $matchdivpass;
							$divumdiefehler1und2 = '<div class="fehler">';
							$divumdiefehler1und2ende = '</div>';
						}else if($this->user->existPassword($newpass)){
							$fehler2 = $fehlerdivpass2;
							$divumdiefehler1und2 = '<div class="fehler">';
							$divumdiefehler1und2ende = '</div>';
							$fehlernewpassclass = " fehlertext";
						}else {
							$fehler2 = $fehlerdivpass;
							$divumdiefehler1und2 = '<div class="fehler">';
							$divumdiefehler1und2ende = '</div>';
							$fehlernewpassclass = " fehlertext";
						}
					}else{
						$fehler2 = $fehlerdivpass4;
							$divumdiefehler1und2 = '<div class="fehler">';
							$divumdiefehler1und2ende = '</div>';
						$fehlerreenterpassclass =" fehlertext";
					}
				}else{
					$fehler2 = $fehlerdivpass3;
					$divumdiefehler1und2 = '<div class="fehler">';
					$divumdiefehler1und2ende = '</div>';
					$fehleroldpassclass = " fehlertext";
				}
			}
				
			$content = '
				<div class="profil">
					<div id="newhireoben">
						<form action="http://test.tmp.de/bilfingerberger/index.php?id=28" method="post">
								'.$divumdiefehler1und2.'
									'.$fehler1.'
									'.$fehler2.'
								'.$divumdiefehler1und2ende.'
							<div id="newhireinput" class="newhireinput">
								<div class="labels">
									<label for="user" class="newhiretext'.$fehleruserclass.'">Benutzername:</label>
									<label for="oldpass" class="newhiretext'.$fehleroldpassclass.'">Altes Passwort:</label>
									<label for="newpass" class="newhiretext'.$fehlernewpassclass.'">Neues Passwort:</label>
									<label for="reenterpass" class="newhiretext'.$fehlerreenterpassclass.'">Neues Passwort wiederholen:</label>
								</div>
								<div class="inputs">
									<input id="user" name="user" class="newhireinput" type="text" size="35" value="'.$this->user->getUsername().'"/><br/>
									<input id="oldpass" name="oldpass" class="newhireinput" type="password" size="35"/>
									<input id="newpass" name="newpass" class="newhireinput" type="password" size="35"/>
									<input id="reenterpass" name="reenterpass" class="newhireinput" type="password" size="35"/>
								</div>
							</div>
							<div class="submitwrap">
								
								<ul class="override normal">
									<li><span>ändern</span></li>
								</ul>
								<input class="submit newhiresubmit" type="submit" value="ändern"/>
								
								
							</div>
						</form>
					</div>
					<div id="newhireunten">
						<p>
							<label class="bluearrow">Name: </label>
							<label id="name">'.$this->user->getVorname().' '.$this->user->getNachname().'</label>
						</p>
						<p>
							<label class="bluearrow">Arbeitsbeginn: </label>
							<label>'.$this->user->getArbeitsbeginn().'</label>
						</p>
					</div>
				</div>
			';
			return $content;
		}
}
?>