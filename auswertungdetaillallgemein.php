<?php 

require_once("subnavigation.php");
require_once("graph.php");
include ("login/checkuser.php"); 

class detaillauswertung {
	
	var $uid;
	var $res;
	var $eintraege;
	var $perioden;
	var $arten;
	var $summe;
	var $art;
	var $seitenname;
	var $global;
	var $url;
	var $aggregation;
	var $monatsanzeige;
	var $kategorieauswertung;
	var $graphPies;
	var $gesamtUmsatzAnzeige;
	
	function detaillauswertung($aggregation="", $seitenname=""){
		$this->url =(isset($_SERVER['HTTPS'])?'https':'http').'://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$this->global = new globalefunktionen();
		$this->aggregation = $aggregation;
		$this->seitenname = $seitenname;
		$this->perioden = $this->global->getPerioden();
		$this->arten = $this->global->getArten();
		$this->uid = $_SESSION["user_id"];
		$this->getDetaill();
		$this->kategorieauswertung = new kategorieauswertung($this->aggregation, $this->eintraege, $this->arten);
		$this->createGraph();
		$content = $this->content();
		echo $content;
	}
	
	function content(){
		$navigation = new navigation();
		$subnavigation = new subnavigation();
		$content = '
			<html>
				<header>
					<title>'.$this->seitenname.'</title>
					<link rel="stylesheet" type="text/css" href="styles.css">
					<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
					<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">
						$(document).ready(function(){ 
					        $(".sortable").tablesorter(); 
					    });
					</script>
				</header>
				
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$subnavigation->getContent("auswertung").'
					'.$this->getForm().'
				</body>
			</html>
			';
		return $content;
	}
	
	function getForm(){
		$datumselect = new datumSelectFelder();
		$content .= '<div class="div" id="auswertungdetaill">';
		if($this->aggregation == "monat"){
			$date = new DateTime();
			$todayMonth = date_format($date, 'm');
			$todayYear = date_format($date, 'Y');
			$prevMonth = (int)$todayMonth - 1;
			if($this->gesamtUmsatzAnzeige < 0)
				$classUmsatz = "red";
			else if($this->gesamtUmsatzAnzeige >= 0)
				$classUmsatz = "green";
				
			$content .='
			<div id="up">
				<div id="monatsauswahl" class="monatsanzeige">
				<form class="auswertungdetaill" action="'.$this->url.'" method="post">
					'.$datumselect->getContent("",0,1,1,"","Monat auswählen", "input").'
				</form>
				</div>
				<div id="monatsanzeige" class="monatsanzeige">
					<h3 class="monat">'.$this->monatsanzeige.'</h3>
					<h3 class="monat">//</h3>
					<h3 class="lab">Monatsumsatz: </h3>
					<h1 class="'.$classUmsatz.'">'.$this->global->htmlValidate($this->gesamtUmsatzAnzeige).'</h3>
				</div>
			</div>
				';
			
			$content .= '<div id="kategorisierungGraph'.$this->aggregation.'" class="kategorisierungGraph">';
			if(is_array($this->graphPies)){
				foreach($this->graphPies as $art => $graph){
					$content .='
							<div id="kategorisierungGraph'.$art.'" class="graph">'.$graph.'</div>
						';
				}
			}
			$content .= '</div>';
		}
		$content .= '
			<div id="kategroisierung" class="div auswertungdetaill">
				<h2>Auflistung der Kategorien</h2>
				<div id="kategorisierungString>'.$this->kategorieauswertung->getContent().'</div>';
		
		if($this->aggregation != "monat"){
			$content .= '<div id="kategorisierungGraph'.$this->aggregation.'" class="kategorisierungGraph">';
			if(is_array($this->graphPies)){
				foreach($this->graphPies as $art => $graph){
					$content .='
							<div id="kategorisierungGraph'.$art.'" class="graph">'.$graph.'</div>
						';
				}
			}
			$content .= '</div>';
		}
		
		
		
		
		
		
		
		if($this->aggregation == "monat"){
			$content .='
				<div id="detaillauflistung" class="div auswertungdetaill">
					<h2>Detaillansicht der Kategorien</h2>
					'.$this->listAllFixeEinnahmenInTable().'
				</div>
			';
		}
		$content .= '</div>';
		return $content;
	}
	
	function listAllFixeEinnahmenInTable(){
		if(is_array($this->eintraege)){
			foreach($this->eintraege as $periodenArrayInclEintraege){
				if($periodenArrayInclEintraege["art"] == "einnahmen"){
					$zweckquelle = "Quelle";
					$class = "green";
				}else if($periodenArrayInclEintraege["art"] == "ausgaben"){
					$zweckquelle = "Zweck";
					$class = "red";
				}
				$art = $this->global->htmlValidate($periodenArrayInclEintraege["art"]);
				$content .= '<div class="arttable" id="div-'.$art.'">';
				$content .= '<h3>Liste der '.$art.'</h3>';
				$content .= '<table class="sortable" id="table'.$art.'" border="1">';
				$content .= '
				<thead>
					<tr>
						<th>Betrag</th>
						<th>Konto</th>
						<th>Kategorie</th>
						<th>'.$zweckquelle.'</th>
						<th>Datum</th>
						<th>Kostenart</th>
						<th>Periode</th>
					</tr>
				</thead>
				';
				$content .= '<tbody>';
				if(is_array($periodenArrayInclEintraege)){
					foreach($periodenArrayInclEintraege as $eintrag){
						if(is_array($eintrag)){
							$content .= '<tr>';
							foreach($eintrag as $row){
								if($row != "taeglich" && $row != "woechentlich" && $row != "monatlich" && $row != "jaehrlich"){
									if(is_numeric($row)){
										$classrg = $class;
									}else if(!is_numeric($row)){
										$classrg = "";
									}
								}
								if($row != ""){
									$content .= '<td class="'.$classrg.'">'.$this->global->htmlValidate($row).'</td>';
								}else if($row == "")
									$content .= '<td class="'.$classrg.'">&nbsp;</td>';
							}
							$content .= '</tr>';
						}
					}
				}
				$content .= '</tbody>';
				$content .= '</table>';
				$content .= '</div>';
			}
		}
		return $content;
	}
	
	function getDetaill(){
		$this->sqlcon();
		$eintraege = array();
		if(is_array($this->res)){
			foreach($this->res as $res){
				if(mysqli_num_rows($res["res"]) > 0){
					while ($row = mysqli_fetch_assoc($res["res"])) {
						$eintrag["betrag"] = $row["betrag"];
						$eintrag["konto"] = $row["kontoname"];
						$eintrag["kategorie"] = $row["kategorie"];
						if($res["art"] == "einnahmen"){
							$eintrag["zweckquelle"] = $row["quelle"];
						}else if($res["art"] == "ausgaben"){
							$eintrag["zweckquelle"] = $row["zweck"];
						}
						$eintrag["datum"] = $row["datum"];
						switch ($row["fix"]) {
							case 0:
									$eintrag["fix"] = "Variabel";
									break;
							case 1:
									$eintrag["fix"] = "Fix";
									break;
						}
						$eintrag["periode"] = $row["periode"];
						if($res["art"] == "einnahmen"){
							$eintraege[$res["art"]]["art"] = $res["art"];
							$eintraege[$res["art"]][] = $eintrag;
						}else if($res["art"] == "ausgaben"){
							$eintraege[$res["art"]]["art"] = $res["art"];
							$eintraege[$res["art"]][] = $eintrag;
						}
					}
				}
				$this->eintraege = $eintraege;
			}
		}
	}
	
	function sqlcon(){
		$sql = new sql("blabla");
		$res = array();
		foreach($this->arten as $art){
			$res[$art]["art"] = $art;
			$res[$art]["res"] = $sql->sql_res($this->sqlquery($art));
		}
		$this->res = $res;
		$sql->close();
	}
	
	function sqlquery($art = ""){
		if($art == "einnahmen"){
			$zweckquelle = "quelle";
		}else if($art == "ausgaben"){
			$zweckquelle = "zweck";
		}
		if($this->aggregation == "monat"){
			if($_POST["month"] != ""){
				$monat = $this->getSelectedMonatQuery();
			}else{
				$monat = $this->global->getThismonatQuery();
				$this->monatsanzeige = $this->global->getThismonatQuery("monatsanzeige");
			}
		}
		$sqlquery = 'Select betrag, konten.kontoname,'.$art.'.konto, '.$zweckquelle.',kategorie, datum, fix, periode FROM ('.$art.',konten) WHERE konten.id='.$art.'.konto AND '.$monat.' '.$art.'.uid='.$this->uid.' ORDER BY kategorie';
		return $sqlquery;
	}

	function getSelectedMonatQuery(){
		$selectedmonth = $this->getTimePost();
		$nextmonat = $selectedmonth + 100;
		$monatselect = '(datum >= "'.$selectedmonth.'" AND datum < "'.$nextmonat.'") AND';
		return $monatselect;
	}
	
	function getTimePost(){
		$selectedmonth["month"] = $_POST["month"];
		if($selectedmonth["month"] < 10){
			$selectedmonth["month"] = "0".$selectedmonth["month"];
		}
		if($_POST["year"] == ""){
			$datetime = new DateTime("",new DateTimeZone('Europe/Berlin'));
			$thisyear = $datetime->format('Y');
			$selectedmonth["year"] = $thisyear;
		}else
			$selectedmonth["year"] = $_POST["year"];
		$day = "01";
		$month = $selectedmonth["year"].$selectedmonth["month"].$day;
		$this->monatsanzeige = $this->global->intToMonth($selectedmonth["month"])." ".$selectedmonth["year"];
		return $month;
	}
	
	function createGraph(){
		$summen = $this->kategorieauswertung->throwSummen();
		$anzahl;
		if(is_array($summen)){
			foreach($summen as $art => $array){
				if(is_array($array)){
					foreach($array as $kategorie => $daten){
						$prozent = $daten["prozent"];
						$prozent = str_replace(' %','',$prozent);
						$prozent = str_replace(',','.',$prozent);
						$prozent = (double)$prozent;
						$betrag = $daten["betrag"];
						//$betrag = $betrag*100;
						
						if($prozent > 1){
							$value = $betrag;
							$anzahl++;
							$kategorie = $this->global->htmlValidate($kategorie, "ae");
							if($value > $groessterWert[$art] ){
								$groessterWert[$art] = $value;
								$groesster[$art] = array();
								$groesster[$art][] = $kategorie;
							}else if($value == $groessterWert[$art]){
								$groesster[$art][] = $kategorie;
							}
							$value = str_replace(' %', '', $value);
							$value = str_replace(',', '.', $value);
							$value = (double)$value;
							$kategories = $this->global->htmlValidate($kategorie);
							$graph[$art][$kategorie] = $value;
						}else if($prozent <= 1){
							$graph[$art]["< 1.0%"] = $betrag;
							$kleinerEinProzent[] = $kategorie; // müssen noch unter dem Graphen angegeben werden!! 
						}
						$gesamtWerte[$art] = $gesamtWerte[$art] + $betrag;
					}
				}
			}
		}
		$this->gesamtUmsatzAnzeige = $gesamtWerte["einnahmen"] - $gesamtWerte["ausgaben"];
		$graphPies = array();
		if(is_array($graph)){
			foreach($graph as $art => $array){
				$graphPies[$art] = new graph($this->global->htmlValidate($art." = ".$gesamtWerte[$art]), "pie", $array, $groesster[$art], "",$this->global->htmlValidate($art));
				$graphPies[$art] = $graphPies[$art]->output(550,400);
			}
			$this->graphPies = $graphPies;
		}
	}
}

?>