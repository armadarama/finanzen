<?php 

include ("login/checkuser.php"); 
//set_include_path( "/ezcomponents/");
require_once("ezcomponents/Base/src/ezc_bootstrap.php");

class graph{
	

// VARIABLEN
	var $graph;
	var $name;
	var $artDesGraphen;
	var $render;
	var $titel;
	
	
// KONSTRUKTOR
	function graph($name, $artDesGraphen, $array, $groessterWert="", $render="", $titel=""){
		$this->uid = $_SESSION["user_id"];
		$this->name = $name;
		if($titel == "")
			$this->titel = $name;
		if($titel != "")
			$this->titel = $titel;
		$this->render = $render;
		$this->artDesGraphen = $artDesGraphen;
		if($artDesGraphen == "line"){
			$this->graphLineChart($array);
		}else if($artDesGraphen == "pie"){
			$this->graphPie($array, $name, $groessterWert);
		}
	}
	
	
// GRAPHENFUNKTIONEN
	function graphPie($array, $name, $groessterWert){
		$graph = new  ezcGraphPieChart(); 
		$graph->title = $name;
		$graph->data[$name] = new ezcGraphArrayDataSet($array);
		if(is_array($groessterWert)){
			foreach($groessterWert as $key => $kategorie){
				$graph->data[$name]->highlight[$kategorie] = true;
			}
		}
		$this->graph = $graph;
	}
	
	function graphLineChart($array){
		$chart = new ezcGraphLineChart();
		if(is_array($array)){
			foreach($array as $key => $value){
				$chart->data[$key] = new ezcGraphArrayDataSet($value);
//				$chart->data[$key]->symbol = ezcGraph::BULLET; 
			}
		}
		$this->graph = $chart;
	}
	
	function fuerJedenGraphEineEigeneFunktinon(){
	
	}
	
	
// FORMATIERUNGSFUNKTIONEN
	function format($render){
		// Hier kommen die üblichen Formatierungen für größe des Bildes, Legende etc. rein
		if($this->artDesGraphen == "pie"){
			$this->graph->renderer = new  ezcGraphRenderer3d();
			
			$this->graph->renderer->options->moveOut = .1;
			
			$this->graph->renderer->options->pieChartOffset = 63;
			
			$this->graph->renderer->options->pieChartGleam = .3;
			$this->graph->renderer->options->pieChartGleamColor = '#FFFFFF';
			
			$this->graph->renderer->options->pieChartShadowSize = 5;
			$this->graph->renderer->options->pieChartShadowColor = '#000000';
			
			$this->graph->renderer->options->pieChartSymbolColor = '#55575388';
			
	//		$this->graph->renderer->options->pieChartHeight = 5;
			$this->graph->renderer->options->pieChartRotation = .9; 
			
			$this->graph->legend = false;
		}
		if($this->artDesGraphen == "line"){
		$this->graph->renderer = new  ezcGraphRenderer2d();
			$this->graph->xAxis->axisLabelRenderer  = new ezcGraphAxisRotatedLabelRenderer();
			$this->graph->xAxis->axisLabelRenderer->angle = 5;
			$this->graph->xAxis->axisSpace = .2;
			
			if($this->render == "render")
				$this->graph->options->fillLines = 230;
				
			$this->graph->renderer->options->legendSymbolGleam = .5;
			$this->graph->renderer->options->legendSymbolGleamSize = .9;
			$this->graph->renderer->options->legendSymbolGleamColor = '#FFFFFF';
			
			$this->graph->legend->position  = ezcGraph::TOP;
			$this->graph->legend->landscapeSize = .05;
			$this->graph->legend->spacing = .136; 
		}
	}
	
	
// OUTPUT
	function output($breite=500, $hoehe=400){
		$this->format($render);
		$titel = $this->titel.$this->uid;
		$this->graph->render($breite, $hoehe, "svg/".$titel.".svg");
		$breite = $breite + 1; // Wenn hier nicht + 1 steht werden unnötige Scrollbalken in der Grafik angezeigt.
		$hoehe = $hoehe + 1; // Wenn hier nicht + 1 steht werden unnötige Scrollbalken in der Grafik angezeigt.
		
		// if IE funktioniert noch nicht!!!!!
		$content = '
		<!--[if IE]>
			<embed src="'.$titel.'.svg"></embed>
		<![endif]-->
		<![if !IE]>
			<object width="'.$breite.'" heigth="'.$hoehe.'" data="svg/'.$titel.'.svg" type="image/svg+xml">
				You need a browser capeable of SVG to display this image.
			</object>
		<![endif]>
		';
		return $content;
	}
	
	
}
?>