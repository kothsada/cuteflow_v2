<?php
	require_once '../config/config.inc.php';
	require_once '../install/new_ver.inc.php';
	
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			$strQuery = "SELECT * FROM cf_config WHERE nConfigId = '1';";

			$result		= mysql_query($strQuery) or die (mysql_error());
			
			$arrResult = mysql_fetch_array($result);
			
			
			$_REQUEST['language'] = $arrResult['strDefLang'];		
			$strCurVersion = $arrResult["strVersion"];
		}
	}
	if($_REQUEST['language']=='')
	{
		$_REQUEST['language'] = 'de';
	}
	
	require_once '../language_files/language.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
</head>
<body>
<center>
<div class="border_content">

	<div class="top">
		<div class="top_left">
			Cuteflow Update
		</div>
					
		<div class="top_right">
			<a href="http://cuteflow.org" target="_blank"><img src="../images/cuteflow_logo_small.png" border="0" /></a><br>
			<strong style="font-size:8pt;font-weight:normal">Version <?php echo $nNewVersion ?></strong>
		</div>
	</div>
		
	<div class="step">
		<table width="100%" height="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span class="small"><br>
				new version: <?php echo $nNewVersion ?>
				</span>
			</td>
		</tr>
		</table>		
	</div>
	
	<form method="post" action="checksystem.php">
	
		<div class="content_border">
		
			<span class="underline">Cuteflow Version <?php echo $nNewVersion ?></span>
			
			<div class="content" id="a">
				<br>
				<table align="center">
				<tr>
					<td colspan="2" style="font-weight: bold; text-decoration: underline;">This Update only works with Cuteflow v2.11.0</td>
				</tr>
				</table>
				<br>	
					<?php
					echo "<div class=\"check\">Your Cuteflow Version:";
					
					if ($strCurVersion < '2.11')
					{
						echo "<span class=\"check_error\">Error: Cuteflow v2.10.x or lower detected. You can only update from 2.11.x</span></div>";
						$bVerCheck = true;
					}
					else
					{
						echo "<span class=\"check_ok\">OK (2.11.0 or greater)</span></div>";
						$bVerCheck = false;
					}
					?>
			</div>
	
		</div>
		
		<div class="bottom_left">
			
		</div>
		
		<div class="bottom_right">
			<input type="submit" value="<?php echo $INSTALL_BUTT_CON; ?>" class="button_next">
		</div>
		<input type="hidden" name="language" value="<?php echo $_REQUEST['language']; ?>">
	</form>

</div>
</center>
</body>
</html>