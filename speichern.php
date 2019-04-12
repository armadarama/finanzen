<?php 
include ("login/checkuser.php"); 

class save{
	var $betrag;
	var $zweck;
	var $kategorie;
	var $datum;
	var $art;
	var $anzahl;
	var $gliedern;
	var $seitenname;
	
	function save(){
		$this->uid = $_SESSION["user_id"];
		$this->seitenname = "Eintraege speichern";
		$this->getAnzahl();
		$this->getPost();
		$this->sqlcon();
		echo $this->getContent();
	}
	
	function getContent(){
		$navigation = new navigation();
		$content .= '
			<html>
				<header>
					<title>'.$this->seitenname.'</title>
					<link rel="stylesheet" type="text/css" href="styles.css">
				</header>
				
				<body>
					'.$navigation->getContent($this->seitenname).'
					'.$this->answer().'
				</body>
			</html>
		';
		return $content;
	}
	
	function answer(){
		$eintrag = "Der Eintrag wurde";
		if($this->anzahl >= 2)
			$eintrag = "Die ".$this->anzahl." Eintr&auml;ge wurden";
		$content .= '<div id="speicherncontent">
			<p>'.$eintrag.' gespeichert</p>
			</div>
		';
		return $content;
	}
	
	function sqlcon(){
		$sql = new sql("blabla"); // hier wird die Datenbankabfrage geöffnet
		$kontencashflow = new kontencashflow();
		for($anzahl=1;$anzahl<=$this->anzahl;$anzahl++){
			if($this->gliedern[$anzahl]["kategorie"] == "Abheben" || $this->gliedern[$anzahl]["kategorie"] == "Überweisen"){
				$uebetrag = $this->gliedern[$anzahl]["betrag"];
				$gbid = $this->getGeldbeutelKonto();
				$sql->sql_res($kontencashflow->einzahlen($uebetrag,$gbid));
				$sql->sql_res($kontencashflow->abbuchen($uebetrag,$this->gliedern[$anzahl]["kontoid"]));
			} else {
				$res = $sql->sql_res($this->insert($anzahl));	// Hier werden alle SQL-Querys ausgeführt
				if($this->gliedern[$anzahl]["art"] == "einnahmen"){
					$sql->sql_res($kontencashflow->einzahlen($this->gliedern[$anzahl]["betrag"],$this->gliedern[$anzahl]["kontoid"]));
				}else if($this->gliedern[$anzahl]["art"] == "ausgaben"){
					$sql->sql_res($kontencashflow->abbuchen($this->gliedern[$anzahl]["betrag"],$this->gliedern[$anzahl]["kontoid"]));
				}
			}
		}
		$sql->close(); // Hier wird die Datenbankabfrage wieder geschlossen
	}
	
	function getGeldbeutelKonto(){
		$sql = new sql("blabla"); // hier wird die Datenbankabfrage geöffnet
		$stm = '
			SELECT id 
			FROM konten 
			WHERE kontoname = "Geldbeutel"
			AND	uid = "'.$this->uid.'"
		';
		$res = $sql->sql_res($stm);
		$id ="";
		if(mysqli_num_rows($res) > 0){
			while ($row = mysqli_fetch_assoc($res)) {
				$id = $row["id"];
			}
		}
		return $id;
	}
	
	function insert($anzahl){
		$sql = new sql("blabla"); // hier wird die Datenbankabfrage geöffnet
		$kontencashflow = new kontencashflow();
		if($this->gliedern[$anzahl]["art"] == "einnahmen"){
			$zweckquelle = "quelle";
		}else if($this->gliedern[$anzahl]["art"] == "ausgaben"){
			$zweckquelle = "zweck";
		}
		$sqlquery = '
			INSERT INTO 
				'.$this->gliedern[$anzahl]["art"].'(
					uid,
					betrag,
					konto,
					'.$zweckquelle.', 
					kategorie,
					fix,
					Datum)
				VALUES(
					'.$this->uid.',
					'.$this->gliedern[$anzahl]["betrag"].',
					"'.$this->gliedern[$anzahl]["kontoid"].'",
					"'.$this->gliedern[$anzahl]["ZweckQuelle"].'",
					"'.$this->gliedern[$anzahl]["kategorie"].'",
					0,
					'.$this->gliedern[$anzahl]["datum"].')';
		return $sqlquery;
	}
	
	function getAnzahl(){
		$this->anzahl = $_POST["anzahl"];
	}
	
	function getPost(){
		$this->gliedern = array();
		$this->gliedern["uid"] = $_SESSION["user_id"];
		for($anzahl=1;$anzahl<=$this->anzahl;$anzahl++){
			$data = array();
			$data["betrag"] = $this->validateBetrag($anzahl);
			$data["ZweckQuelle"] = $_POST[$anzahl."ZweckQuelle"];
			$data = $this->validateKonto($anzahl, $data);
			$data = $this->getKategorieUndArt($anzahl, $data);
			$data["datum"] = $this->timestamp($anzahl);
			if($data["betrag"] != false && $data["kategorie"] != false)
				$this->gliedern[$anzahl] = $data;
		}
	}
	
	function validateKonto($anzahl, $data){
		$value = explode('-',$_POST[$anzahl."konto"]);
		$data["konto"] = $value["0"];
		$data["kontoid"] = $value["1"];
		return $data;
	}
	
	function getKategorieUndArt($anzahl, $data){
		$value = explode(',',$_POST[$anzahl."kategorie"]); // in "kategorie" sind die informationen von der kategorie und der art enthalten, daher müssen diese hier getrennt werden
		$data["kategorie"] = $value["0"];
		$data["art"] = $value["1"];
		if($data["kategorie"] == "")
			$data = false;
		return $data;
	}
	
	function validateBetrag($anzahl){
		$betrag = $_POST[$anzahl."betrag"];
		if($betrag == ""){
			return false;
		}else{
			return $this->numeralValidation($betrag);
		}
	}
	
	function numeralValidation($betrag){
		$betrag = str_replace(",",".",$betrag);
		$betrag = floatval($betrag);
		$betrag = round($betrag, 2);
		return $betrag;
	}
	
	function timestamp($anzahl){
		$day = $_POST[$anzahl."day"];
		$month = $_POST[$anzahl."month"];
		$year = $_POST[$anzahl."year"];
		
		if ($day == "" || $month == "" || $year == ""){
			$datum = "NOW()";
		}else{
			$datetime = new DateTime($day.'.'.$month.'.'.$year,new DateTimeZone('Europe/Berlin'));
			$datum = $datetime->format('Ymd');
		}
		return $datum;
	}
}

$save = new save();

?>