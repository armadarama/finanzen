<?php 

class sql {

	var $dbhost;
	var $dbuser;
	var $dbpass;
	var $sqlcon;
	
	function sql($dbname){
		$dbname = "unwichtig und wird hier nicht weiter verwendet, sonder nur drin gelassen, um fehler zu vermeiden";
		$dbname = "armadac_finanz";
		$this->dbhost = 'sql515.your-server.de';
		$this->dbuser = 'armadac_finanz';
		$this->dbpass = 'f2GGXMT3LUyKA8Kf';
		$this->sqlcon = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $dbname);
		if ($this->sqlcon->connect_errno) {
			die("Failed to connect to MySQL: (" . $this->sqlcon->connect_errno . ") " . $this->sqlcon->connect_error);
		}
		// $this->sqlcon = mysql_connect($this->dbhost, $this->dbuser, $this->dbpass) or die ('Error connecting to mysql');
		// $dbcon = mysql_select_db($dbname) or die("Auswahl der Datenbank fehlgeschlagen");
	}
	
	function sql_res($sql_query){
		$res = $this->sqlcon->query($sql_query) or die("Anfrage fehlgeschlagen: (" . $mysqli->errno . ") " . $mysqli->error);
		return $res;
	}
	
	function close(){
		// Schließen der Verbinung
		// mysql_close($this->sqlcon);
	}
}

?>