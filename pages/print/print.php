<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php  
	require_once '../../config/config.inc.php';
	//$objURL->setPassword($URL_ENCODING_PASSWORD);
	
	if ($_REQUEST['key'] == '')
	{
		while(list($key, $value) = each($_REQUEST))
		{
			if ($key != "show")
			{
				if ($key == "anSize")
				{
					//--- php obscurity: an urlencoded qoute (") is decoded as /"
					$strURL = $strURL."&$key=".urlencode(stripslashes($value));
				}
				else
				{
					$strURL = $strURL."&$key=".urlencode($value);
				}
			}
		}
		$strURL	= $objURL->encryptURL($strURL);
	}
	else
	{
		$strURL = $_REQUEST['key'];
	}
	
?>
<html>
<head>
	<title></title>
</head>
<frameset rows="40,*" framespacing="0" border="0" frameborder="0">
	<frame name="Toolbar" src="printbar.php?key=<?php echo $strURL;?>&language=<?php echo $_REQUEST['language']; ?>" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize>
	<frame name="Main" src="../circulation_detail.php?key=<?php echo $strURL;?>&view=print">" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto">
</frameset>

</html>
