<?php

require_once("graph.php");

class testing {
	var $uid;
	var $global;

	function testing(){
		$this->uid = $_SESSION["user_id"];
		$this->global = new globalefunktionen();
		$res = $this->sqlabfrage();
		foreach($res as $key => $result){
			while ($row = mysqli_fetch_assoc($result)) {
				$monat = substr_replace($row["datum"], '', 7,9);
				$betrag = (int)$row["betrag"];
				if($key == "ausgaben")
					$betrag = $betrag*(-1);
				$monatlich[$monat] = $monatlich[$monat] + $betrag;
			}
		}
		ksort($monatlich);
		foreach($monatlich as $key => $value){
			$key = $this->datumFormat($key);
			$monatlichArray[$key] = $value;
		}
		
		foreach($monatlichArray as $key => $value){
			$summe = $summe + $value;
			$gesamtArray[$key] = $summe;
		}
		
		$array["Monatlicher Umsatz"] = $monatlichArray;
		$array["Gesamtumsatz"] = $gesamtArray;
		
		$graph = new graph("name", "line", $array);
		echo $graph->output(700);
	}
	
	function datumFormatGraph($datum){
		$datum = substr_replace($datum, '', 7,9);
		$jahr = substr_replace($datum, '', 4,7);
		$monat = substr_replace($datum, '', 0,5);
		$monat = $this->global->intToMonth($monat);
		if($monat == "Jan")
			$monat = substr_replace($monat, $jahr." ", 0,0);
		return $monat;
	}
	
// SQL FUNKTIONEN
	function sqlAbfrageGraph(){
		$sql = new sql("blabla");
		$res = array();
		$res["einnahmen"] = $sql->sql_res("SELECT betrag, datum FROM einnahmen WHERE uid=".$this->uid);
		$res["ausgaben"] = $sql->sql_res("SELECT betrag, datum FROM ausgaben WHERE uid=".$this->uid);
		$sql->close();
		return $res;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
//	function testing(){
//		$graph = new  ezcGraphPieChart(); 
//		$graph->title = 'Access statistics';
//		
//		$graph->data['Access statistics'] = new ezcGraphArrayDataSet( array(
//		'Mozilla' => 19113,
//		'Explorer' => 10917,
//		'Opera' => 1464,
//		'Safari' => 652,
//		'Konqueror' => 474,
//		) );
//		$graph->data['Access statistics']->highlight['Opera'] = true;
//		$graph->data['Access statistics']->highlight['Safari'] = true;
//		$graph->data['Access statistics']->highlight['Explorer'] = true;
//		
//		$graph->renderToOutput( 800, 600); 
//		echo $this->testi();
//	}
	


//	function testinsg(){
//		// Create a new line chart
//		$chart = new ezcGraphLineChart();
//		//		Add data to line chart
//		$chart->data['sample dataset'] = new ezcGraphArrayDataSet(
//		array(
//			'Jan' => 1.2,
//			'Feb' => 43.2,
//			'Mar' => -34.14,
//			'Apr' => 65,
//			'May' => 123,
//			)
//		);
//		echo $timestamp = time();
//		echo "\n";
//		$chart->render( 1200, 600, "ichgebsdir.bla");
//		echo $timestamp = time();
//		echo "\n";
//		$content = '
//		<object data="ichgebsdir.bla" type="image/svg+xml">
//			You need a browser capeable of SVG to display this image.
//		</object>';
//		echo $content;
//		echo $timestamp = time();
//		echo "\n";
//		
//	}
	
	
}
$test = new testing();
?>