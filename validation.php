class validation{
	var $errorfield = array();
	
	function isEmpty($f,$v){
		if(empty($v)){
			$this->errorfield[] = $f;
			return true;
		}
	}
	
	function isAlpha($f,$v){
		if(!$this->isEmpty($f,$v) && !preg_match("|^[[:alpha:] /-]+$|",$v)){
			$this->errorfield[] = $f;
		}
	}
	
	function isAlnum($f,$v){
		if(!$this->isEmpty($f,$v) && !preg_match("|^[[:alnum:]\. -]+$|",$v)){
			$this->errorfield[] = $f;
		}
	}
	
	function isEmail($f,$v){
		if(!$this->isEmpty($f,$v) && !preg_match("|^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)?(\.[_a-zA-Z0-9-]{2,4})$|",$v)){
			$this->errorfield[] = $f;
		}
	}
	
	function isNumeric($f,$v,$empty=false){
		if(($empty == false)){
			if(!$this->isEmpty($f,$v) && !preg_match("|^\d+$|",$v)){
				$this->errorfield[] = $f;
			}
		}else{
			if(!empty($v) && !preg_match("|^\d+$|",$v)){
				$this->errorfield[] = $f;
			}
		}
	}
}

$valid = new validation;