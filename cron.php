<?php
ini_set('max_execution_time',3600);
ini_set('memory_limit', '1024M');
$host=$_SERVER["HTTP_HOST"];
if(strpos($host,"localhost") === false){
	$includepath=$_SERVER['DOCUMENT_ROOT'].'/wp-load.php';	
} else {
	$includepath=$_SERVER['DOCUMENT_ROOT'].'/wordpress/wp-load.php';
}	
require($includepath);
BCAPICHECKER_event_exe();
?>