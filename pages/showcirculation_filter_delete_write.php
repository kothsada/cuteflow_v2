<?php
	include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	
	$nFilterID = $_REQUEST['nFilterID'];
	
	$strQuery 	= "DELETE FROM cf_filter WHERE nID = '$nFilterID'";
	$nResult 	= @mysql_query($strQuery);
?>
