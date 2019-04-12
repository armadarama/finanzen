<?php 

require_once("sql_class/sql_con.php");
	
class userfreischalten{

	var $id;
	var $hashcode;

	function userfreischalten(){
		$this->getGET();
		$this->sqlcon();
	}
	
	function sqlcon(){
		$sql = new sql("blabla");
		$sqlquery = "UPDATE benutzerdaten SET aktiv = 1 WHERE id = ".$this->id." AND hashcode = '".$this->hashcode."'";
		$res = $sql->sql_res($sqlquery);
		if(mysql_affected_rows() > 0){
			echo "Der User mit der id ".$this->id." wurde freigeschaltet";
		}
		$sql->close();
		return TRUE;
	}
	
	function getGET(){
		$this->id = $_GET["id"];
		$this->hashcode = $_GET["hashcode"];
	}
}

$freischalten = new userfreischalten();

?>