<?php 
header("Content-Type: text/html; charset=utf-8");
class navigation {
	
	var $navipoints;

	function navigation(){
	}
	
	function getContent($pagename="", $page=""){
		$this->setNavipoints($page);
		$content = "";
		$content .= '
			<div class="navi" id="navi">
				'.$this->getHeader($pagename).'
				'.$this->getAllLis().'
			</div>
		';
		return $content;
	}
	
	function getAllLis(){
		$content = "";
		$content .= '<ul id="ersteebene">';
			if(is_array($this->navipoints)){
				foreach($this->navipoints as $point){
					$content .= '
					<li><a href="'.$point["url"].'">'.$point["name"].'</a></li>
					';
				}
			}
		$content .= '</ul>';
		return $content;
	}
	
	function setNavipoints($page=""){
		$navipoints = array();
		if($page == "login"){
			$navipoints["Login"]["name"] = "Login";
			$navipoints["Login"]["url"] = "loginformular.php";
			$navipoints["Registrierung"]["name"] = "Registrierung";
			$navipoints["Registrierung"]["url"] = "new_user.php";
		}else{
			$navipoints["Cashflow"]["name"] = "Cashflow";
			$navipoints["Cashflow"]["url"] = "index.php";
			//$navipoints["fixekosten"]["name"] = "Fixen Cashflow hinzuf&uuml;gen";
			//$navipoints["fixekosten"]["url"] = "fixekosten.php";
			$navipoints["Kategorie"]["name"] = "Kategorien";
			$navipoints["Kategorie"]["url"] = "neue_kategorien.php";
			$navipoints["Auswertung"]["name"] = "Auswertung";
			$navipoints["Auswertung"]["url"] = "auswertungverteiler.php";
			$navipoints["Konten"]["name"] = "Konten&uuml;bersicht";
			$navipoints["Konten"]["url"] = "kontenuebersicht.php";
			if($_SESSION["user_id"] == "1" || $_SESSION["user_id"] == "2"){
				$navipoints["Import"]["name"] = "Import";
				$navipoints["Import"]["url"] = "import.php";
			}
			$navipoints["profil"]["name"] = "Profil bearbeiten";
			$navipoints["profil"]["url"] = "profilaendern.php";
			$navipoints["Logout"]["name"] = "Logout";
			$navipoints["Logout"]["url"] = "login/logout.php";
		}
		
		$this->navipoints = $navipoints;
	}
	
	function getHeader($page){
		$content = "";
		$content .= '
			<div id="header" class="header">
			<h1>'.$page.'</h1>
			</div>
			';
		return $content;
	}
}

?>