<?php 

include ("login/checkuser.php"); 

class kategorieauswertung {
	
	var $uid;
	var $kategorien;
	var $arten;
	var $eintraege;
	var $summen;
	var $gesamtsumme;
	var $prozent;
	var $global;
	var $aggregation;
	
	function kategorieauswertung($aggregation, $eintraege="", $arten=""){
		$this->uid = $_SESSION["user_id"];
		$this->global = new globalefunktionen();
		$this->getKategorien();
		$this->aggregation = $aggregation;
		$this->arten = $arten;
		$this->eintraege = $eintraege;
		$this->getSummen();
	}
	
	function throwSummen(){
		return $this->summen;
	}
	
	function getContent(){
		foreach($this->arten as $art){
			$content .='
				<div id="Kategorienauswertung-'.$art.'" class="kategorienauswertung">
				<h3>'.$this->global->htmlValidate($art).'</h3>
				<table class="sortable" border="1">
					<thead>
					<tr>
						<th>Kategorie</th>
						<th>Betrag</th>
						<th>Prozentualer Anteil</th>
			';
			
			if($this->aggregation == "gesamt"){
				$content .= '
					<th>Durchschnittlicher Betrag</th>
					<th>Maximum</th>
					<th>Minimum</th>
				';
			}
			$content .='
					</tr>
					</thead>
					<tbody>
					'.$this->getEachRow($art).'
					</tbody>
				</table>
				</div>
			';
		}
		return $content;
	}
	
	function getEachRow($art){
		if(is_array($this->summen)){
			foreach($this->summen as $kategorien){
				if(is_array($kategorien)){
					foreach($kategorien as $summe){
						$importantclass = "";
						if($summe["prozent"] >= 50){
							$importantclass = "hauptkategorie";
						}
						if($summe["art"] == "einnahmen"){
							$contenteinnahmen .= '<tr>';
							$contenteinnahmen .= '<td class='.$importantclass.'>'.$this->global->htmlValidate($summe["kategorie"]).'</td>';
							$contenteinnahmen .= '<td class="green '.$importantclass.'">'.$this->global->htmlValidate($summe["betrag"]).'</td>';
							$contenteinnahmen .= '<td class='.$importantclass.'>'.$summe["prozent"].'</td>';
							if($this->aggregation == "gesamt"){
								$contenteinnahmen .= '<td class="'.$importantclass.'">'.$this->global->htmlValidate($summe["maximum"]).'</td>';
								$contenteinnahmen .= '<td class="'.$importantclass.'">'.$this->global->htmlValidate($summe["minimum"]).'</td>';
							}
							$contenteinnahmen .= '</tr>';
						}else if($summe["art"] == "ausgaben"){
							$contentausgaben .= '<tr>';
							$contentausgaben .= '<td class='.$importantclass.'>'.$this->global->htmlValidate($summe["kategorie"]).'</td>';
							$contentausgaben .= '<td class="red '.$importantclass.'">'.$this->global->htmlValidate($summe["betrag"]).'</td>';
							$contentausgaben .= '<td class='.$importantclass.'>'.$summe["prozent"].'</td>';
							if($this->aggregation == "gesamt"){
								$contentausgaben .= '<td class="'.$importantclass.'">'.$this->global->htmlValidate($summe["maximum"]).'</td>';
								$contentausgaben .= '<td class="'.$importantclass.'">'.$this->global->htmlValidate($summe["minimum"]).'</td>';
							}
							$contentausgaben .= '</tr>';
						}
					}
				}
			}
		}
		if($art == "einnahmen")
			return $contenteinnahmen;
		else if($art == "ausgaben")
			return $contentausgaben;
	}
	
	
	/*
	$this->eintrage:
	array(2) {
	  ["einnahmen"]=>
	  array(218) {
	    ["art"]=>
	    string(9) "einnahmen"
	    [0]=>
	    array(7) {
	      ["betrag"]=>
	      string(7) "5241.25"
	      ["konto"]=>
	      string(9) "Girokonto"
	      ["kategorie"]=>
	      string(23) "ArmA Services Einnahmen"
	      ["zweckquelle"]=>
	      string(52) "URANO INFORMATIONSSYSTEME G GUTSCHRIFT RG 2012-12001"
	      ["datum"]=>
	      string(10) "2013-01-11"
	      ["fix"]=>
	      string(8) "Variabel"
	      ["periode"]=>
	      string(0) ""
	    }
	    [1]=>...
		
		
	Ergebnis $summen:
	array(2) {
		  ["einnahmen"]=>
		  array(6) {
		    ["ArmA Services Einnahmen"]=>
		    array(6) {
		      ["anzahl"]=>
		      int(3)
		      ["minimum"]=>
		      string(3) "116"
		      ["maximum"]=>
		      string(7) "5241.25"
		      ["kategorie"]=>
		      string(23) "ArmA Services Einnahmen"
		      ["art"]=>
		      string(9) "einnahmen"
		      ["betrag"]=>
		      float(6107.25)
		    }
		    ["Gehalt"]=>
}
	*/
	function getSummen(){
		$hochzaehlen;
		if(is_array($this->eintraege)){
			foreach($this->eintraege as $art => $eintraegeEinnahmenOderAusgaben){
				if(is_array($eintraegeEinnahmenOderAusgaben)){
					foreach($eintraegeEinnahmenOderAusgaben as $eintrag){
						if(is_array($this->kategorien[$art])){
							foreach($this->kategorien[$art] as $kategorie){
								if($kategorie == $eintrag["kategorie"]){
									$summen[$art][$kategorie]["anzahl"] ++;
									if($summen[$art][$kategorie]["anzahl"] == 1){
										$summen[$art][$kategorie]["minimum"] = $eintrag["betrag"];
										$summen[$art][$kategorie]["maximum"] = $eintrag["betrag"];
									}
									if($eintrag["betrag"] < $summen[$art][$kategorie]["minimum"]){
										$summen[$art][$kategorie]["minimum"] = $eintrag["betrag"];
									}
									if($eintrag["betrag"] > $summen[$art][$kategorie]["maximum"]){
										$summen[$art][$kategorie]["maximum"] = $eintrag["betrag"];
									}
									$summen[$art][$kategorie]["kategorie"] = $kategorie;
									$summen[$art][$kategorie]["art"] = $art;
									$summen[$art][$kategorie]["betrag"] += $eintrag["betrag"];
								}
							}
						}
					}
				}
			}
		}
		$this->summen = $summen;
		if(is_array($summen)){
			foreach($summen as $arten){
				if(is_array($arten)){
					foreach ($arten as $keyArt => $summe){
						$this->gesamtsumme[$summe["art"]] += $summe["betrag"];
					}
				}
			}
		}
		$this->getProzentualenAnteil();
		
		
	}
	
	function getProzentualenAnteil(){
		if(is_array($this->summen)){
			foreach($this->summen as $arten){
				if(is_array($arten)){
					foreach($arten as $summe){
						$prozent = $summe["betrag"] / $this->gesamtsumme[$summe["art"]];
						$prozent = $prozent*100;
						$prozent = number_format($prozent,2)." %";
						if($prozent < 0.01)
							$prozent = "< 0.01 %";
						$prozent = str_replace(".",",",$prozent);
						$this->summen[$summe["art"]][$summe["kategorie"]]["prozent"] = $prozent;
					}
				}
			}
		}
	}
	
	function getKategorien($anzahl=""){
		$sql = new sql("blabla");
		$res = $sql->sql_res($this->selectKategorien());
		$sql->close();
		if(mysql_num_rows($res) > 0){
			while ($row = mysql_fetch_assoc($res)) {
				if($row["art"] == "einnahmen"){
					$kategorien[$row["art"]]["art"] = $row["art"];
					$kategorien[$row["art"]][] = $row['kategorie'];
				}else if($row["art"] == "ausgaben"){
					$kategorien[$row["art"]]["art"] = $row["art"];
					$kategorien[$row["art"]][] = $row['kategorie'];
				}
			}
		}
		$this->kategorien = $kategorien;
	}
	
	function selectKategorien(){
		$sqlquery = 'Select kategorie, art FROM kategorien where uid='.$this->uid.' ORDER BY kategorie';
		return $sqlquery;
	}
}
?>