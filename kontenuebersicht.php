<?php 

// Seite zum Hinzufügen eines neuen "Kontos"
require_once("subnavigation.php");
include ("login/checkuser.php"); 

class kontenuebersicht{

	var $res;
	var $uid;
	var $eintraege;
	var $seitenname;
	var $global;

	function kontenuebersicht(){
		$this->global = new globalefunktionen();
		$this->seitenname = "Konten&uuml;bersicht";
		$this->uid = $_SESSION["user_id"];
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$subnavigation = new subnavigation();
		$content = '
		<html>
			<head>
				<title>'.$this->seitenname.'</title>
				<link rel="stylesheet" type="text/css" href="styles.css">
					<script type="text/javascript" src="js/jquery-1.6.4.min.js"></script>
					<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">
						$(document).ready(function(){ 
					        $(".sortable").tablesorter(); 
					    });
					</script>
			</head>
			<body>
				'.$navigation->getContent($this->seitenname).'
				'.$subnavigation->getContent("konto").'
				<div class="div" id="kontenuebersicht">
				'.$this->getKontenuebersicht().'
				</div>
			</body>
		</html>
		';
		return $content;
	}
	
	function getKontenuebersicht(){
		$this->listKonten();
		
		$gesamtBetrag = 0;
		$gesamtBetragClass = "";
		
		if(is_array($this->eintraege)){
			foreach($this->eintraege as $eintrag){
				$gesamtBetrag += $eintrag["kontostand"];
			}
			if($gesamtBetrag >= 0){
				$gesamtBetragClass = "green";
			}else if($eintrag["kontostand"] < 0){
				$gesamtBetragClass = "red";
			}
		}
		
		$content .= '
		<div style="width: 100%;">
			<table>
					<thead>
					<tr>
						<th>Gesamtbetrag</th>
						<td><span class="'.$gesamtBetragClass.'">'.money_format('%.2n',$gesamtBetrag).'</span></th>
					</tr>
					</thead>
			</table>
		</div>
		';
		
		$content .= '
			<table class="sortable" border="1">
				<thead>
				<tr>
					<th>Name</th>
					<th>Kontostand</th>
				</tr>
				</thead>';
		if(is_array($this->eintraege)){
			$content .= '<tbody>';
			foreach($this->eintraege as $eintrag){
				if($eintrag["kontostand"] >= 0){
					$class = "green";
				}else if($eintrag["kontostand"] < 0){
					$class = "red";
				}
				$content .= '<tr>';
				$content .= '<td>'.$this->global->htmlValidate($eintrag["kontoname"]).'</td>';
				$content .= '<td class='.$class.'>'.money_format('%.2n',$eintrag["kontostand"]).'</td>';
				$content .= '</tr>';
			}
		}
		$content .= '
			</tbody></table>';
		return $content;
	}
	
	function listKonten(){
		$this->sqlcon();
		$eintraege = array();
		if(mysqli_num_rows($this->res) > 0){
			while ($row = mysqli_fetch_assoc($this->res)) {
				$eintrag["kontoname"] = $row["kontoname"];
				$eintrag["kontostand"] = $row["kontostand"];
				$eintraege[] = $eintrag;
			}
		}
		$this->eintraege = $eintraege;
	}
	
	function sqlcon(){
		$sql = new sql("blabla");
		$this->res = $sql->sql_res($this->sqlquery());
		$sql->close();
	}
	
	function sqlquery(){
		$sqlquery = 'Select kontoname, kontostand FROM konten WHERE uid='.$this->uid." ORDER BY kontoname";
		return $sqlquery;
	}
}
$kontenuebersicht = new kontenuebersicht();

?>