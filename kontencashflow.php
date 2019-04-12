<?php 

class kontencashflow{

	function kontencashflow(){
	}

	function abbuchen($betrag,$kontoid){
		$query = "UPDATE konten SET kontostand = kontostand - ".$betrag." WHERE `konten`.`id` = ".$kontoid;
		return $query;
	}

	function einzahlen($betrag,$kontoid){
		$query = "UPDATE konten SET kontostand = kontostand + ".$betrag." WHERE `konten`.`id` = ".$kontoid;
		return $query;
	}
}
?>