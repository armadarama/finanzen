<?php 

class user{

	// K L A S S E N - E I G E N S C H A F T E N

		private $nickname;
		private $kennwort;
		private $email;
		private $uid;

	// K O N S T R U K T O R
		
		function user($uid=""){
			$this->uid = $_SESSION["user_id"];
			$sql = new sql("blabla");
			$query = "SELECT nickname, kennwort, email FROM benutzerdaten WHERE id=".$this->uid;
			$res= $sql->sql_res($query);
			if(mysqli_num_rows($res) > 0){
				while ($row = mysqli_fetch_assoc($res)) {
					$this->nickname = $row["nickname"];
					$this->kennwort = $row["kennwort"];
					$this->email = $row["email"];
				}
			}
			$sql->close();
		}

	// G E T - F U N K T I O N E N 
		
		function getEmail(){
			return $this->email;
		}
		
		function getUsername(){
			return $this->nickname;
		}
		
		function getPassword(){
			return $this->kennwort;
		}
		
		function getUserId(){
			return $this->uid;
		}
		
	// S E T - F U N K T I O N E N 
		
		private function update($sqlquery){
			$sql = new sql("blabla");
			$res= $sql->sql_res($sqlquery);
			$sql->close();
			if($res == 0)
				return false;
			return true;
		}
		
		function setUsername($value){
			if($this->checkUsername($value)){
				if ($this->update("update benutzerdaten set nickname = '".$value."' where id='".$this->getUserId()."'")){
					$this->nickname = $value;
					return true;
				}
			}
			return false;
		}
		
		function setPassword($value){
			if($this->checkPassword($value)){
				if ($this->update("update benutzerdaten set kennwort = '".$value."' where id='".$this->getUserId()."'")){
					$this->Password = $value;
					return true;
				}
			}
			return false;
		}
		
	// C H E C K - F U N K T I O N E N 
		
		private function checkUsername($value){
			if($value == "" || $value == NULL || !is_string($value)){
				return false;
			}else if($this->existUsername($value) == TRUE){
				return FALSE;
			}else return TRUE;
		}
		
		public function existUsername($value){
			$sql = new sql("blabla");
			$sqlquery = "SELECT nickname FROM benutzerdaten WHERE nickname = '".$value."' AND id=".$this->getUserId();
			$res= $sql->sql_res($sqlquery);
			$row = mysqli_affected_rows();
			$sql->close();
			if($row > 0){
				return TRUE;
			}else return FALSE;
		}
		
		private function checkPassword($value){
			if($value == "")
				return FALSE;
			return TRUE;
		}
		
		public function existPassword($value){
			$sql = new sql("blabla");
			$sqlquery = "SELECT kennwort FROM benutzerdaten WHERE kennwort = '".$value."' AND id=".$this->getUserId();
			$res= $sql->sql_res($sqlquery);
			$row = mysqli_affected_rows();
			$sql->close();
			if($row > 0){
				return TRUE;
			}else return FALSE;
		}
		
}
?>