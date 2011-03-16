<?php
session_start();

if ( (!isset($_SESSION['SESSION_CUTEFLOW_USERNAME'])) | (!isset($_SESSION['SESSION_CUTEFLOW_PASSWORD'])) )
{
	//--- no user logged in, so go to login-mask
	header("Location: index.php");
}

require_once 'config/config.inc.php';

$strParams				= 'language='.$_REQUEST['language'];
$strEncyrptedParams		= $objURL->encryptURL($strParams);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>CuteFlow</title>
	<link rel="stylesheet" href="pages/format.css" type="text/css">
</head>

	<frameset rows="60,*" framespacing="0" border="0" frameborder="0">
		<frame name="Header" src="pages/header.php?key=<?php echo $strEncyrptedParams ?>" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize>
		<frameset cols="205,*" frameborder="0" framespacing="0" border="0">
		    <frame name="frame_menu" src="pages/menu.php?key=<?php echo $strEncyrptedParams ?>" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0" noresize>
	    	<?php 
    		$strParams				= 'language='.$_REQUEST["language"].'&start=1&archivemode=0&bFirstStart=true&bOwnCirculations=1';
			$strEncyrptedParams		= $objURL->encryptURL($strParams);
			$strEncryptedLinkURL	= 'pages/showcirculation.php?key='.$strEncyrptedParams;
			?>
			<frame name="frame_details" src="<?php echo $strEncryptedLinkURL ?>" frameborder="0" scrolling="Auto" marginwidth="0" marginheight="0">
		</frameset>
	</frameset>
</html>