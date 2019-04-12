<?php
class timestampnow{
	function timestampnow(){
		echo "Timestamp von jetzt: ";
		echo $timestamp = time();
	}
}
$now = new timestampnow();
?>