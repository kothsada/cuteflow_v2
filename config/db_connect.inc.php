<?php
$connection = @mysql_pconnect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD)
							or die("Cant connect to database -A");
		
$db			= @mysql_select_db($DATABASE_DB, $connection)
							or die("Cant connect to database -B");
?>
