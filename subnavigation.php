<?php 

class subnavigation {
	
	var $subnavipoints;

	function subnavigation(){
	}
	
	function getContent($page){
		$this->setSubnavipoints($page);
		$content = "";
		$content .= '
			<div class="navi" id="subnavi">
				'.$this->getAllLis().'
			</div>
		';
		return $content;
	}
	
	function getAllLis(){
		if(is_array($this->subnavipoints)){
			$content = "";
			$content .= '<ul id="zweiteebene">';
				if($this->subnavipoints){
					foreach($this->subnavipoints as $point){
						$content .= '
						<li><a href="'.$point["url"].'">'.$point["name"].'</a></li>
						';
					}
				}
			$content .= '</ul>';
			return $content;
		}
		return;
	}
	
	function setSubnavipoints($page){
		$subnavipoints = array();
		
		if($page == "auswertung" || $page == "Auswertung"){
			$subnavipoints["Auswertung"]["name"] = "Cashflow-Verlauf";
			$subnavipoints["Auswertung"]["url"] = "cashflowverlauf.php";
			$subnavipoints["Gesamtdetaill"]["name"] = "Gesamt Kategorieauswertung";
			$subnavipoints["Gesamtdetaill"]["url"] = "auswertungdetaillgesamt.php";
			$subnavipoints["Monatdetaill"]["name"] = "Monatliche Kategorieauswertung";
			$subnavipoints["Monatdetaill"]["url"] = "auswertungdetaillmonat.php";
			$subnavipoints["Report"]["name"] = "Report";
			$subnavipoints["Report"]["url"] = "report.php";
		}
		if($page == "konto"){
			$subnavipoints["kontobearbeiten"]["name"] = "Konten bearbeiten";
			$subnavipoints["kontobearbeiten"]["url"] = "kontenbearbeiten.php";
			$subnavipoints["neueskonto"]["name"] = "Neues Konto anlegen";
			$subnavipoints["neueskonto"]["url"] = "neueskonto.php";
		}
		if($page == "kategorien"){
			$subnavipoints["kategorienhinzufuegen"]["name"] = "Kategorien hinzuf&uuml;gen";
			$subnavipoints["kategorienhinzufuegen"]["url"] = "neue_kategorien.php";
			$subnavipoints["kategorienbearbeiten"]["name"] = "Kategorien bearbeiten";
			$subnavipoints["kategorienbearbeiten"]["url"] = "kategorienbearbeiten.php";
		}
		
		$this->subnavipoints = $subnavipoints;
	}
}

?>