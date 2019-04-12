<?php 
require_once("../sql_class/sql_con.php");
class login {

	var $res;

	function login(){
		session_start ();
		$this->sqlcon();
		$this->session();
	}

	function session(){
		if (mysqli_num_rows ($this->res) > 0) {
			// Benutzerdaten in ein Array auslesen. 
			$data = mysqli_fetch_array ($this->res); 
		
			// Sessionvariablen erstellen und registrieren 
			$_SESSION["user_id"] = $data["id"]; 
			$_SESSION["user_nickname"] = $data["nickname"]; 
			$_SESSION["user_email"] = $data["email"]; 
		
			header ("Location: ../index.php"); 
		}
		else {
			header ("Location: loginformular.php?fehler=1"); 
		}
	}
	
	function sqlcon($sqlquery=""){
		$sql = new sql("blabla");
		$this->res = $sql->sql_res($this->select());
		$sql->close();
		return $res;
	}

	function select(){
		$sqlquery .=	"SELECT ". 
		"id, nickname, email ". 
		"FROM ". 
		"benutzerdaten ". 
		"WHERE ". 
		"(nickname like '".$_REQUEST["name"]."') AND ". 
		"(kennwort = '".$_REQUEST["pwd"]."') AND".
		" aktiv=1";
		return $sqlquery;
	}
}

$login = new login();


?>