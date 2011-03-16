<?php
$var = time() + (60*60*24*7*4);
if ($_REQUEST['nReloadTimeout'] == 'true')
{
	setcookie('nReloadTimeout', 'false', $var);
}
else
{
	setcookie('nReloadTimeout', 'true', $var);
}
?>
