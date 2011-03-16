<?php
	if (!$_REQUEST['bRBInstall_TestData'])
	{
		header("Location:startcuteflow.php?language=$_REQUEST[language]");
	}
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../pages/version.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
</head>
<body>
<center>
<div class="border_content">

	<div class="top">
		<div class="top_left">
			<?php echo $INSTALL_HEAD ?>
		</div>
					
		<div class="top_right">
			<a href="http://cuteflow.org" target="_blank"><img src="../images/cuteflow_logo_small.png" border="0" /></a><br>
			<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION ?></strong>
		</div>
	</div>
		
	<div class="step">
		
		<table width="100%" height="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span class="small"><?php echo $INSTALL_STEP ?> 4/5<br>
				<?php echo $INSTALL_STEP1 ?> >&gt; <?php echo $INSTALL_STEP2 ?> >&gt; <?php echo $INSTALL_STEP3 ?> >> <span class="mandatory"><?php echo $INSTALL_STEP4 ?></span>
				</span>
			</td>
		</tr>
		</table>
	</div>
	
	<div class="content_border">
		<span class="underline"><?php echo "$INSTALL_HEAD4"; ?></span>
		<div class="content">

<?php	
	
	if ($_REQUEST['bRBInstall_TestData'])
	{
		//Creating Database and tables
		echo "<div class=\"check\">";
		echo "$INSTALL_DB_CONNECT <b>$DATABASE_HOST</b>...";
		
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		
		if ($nConnection)
		{
			echo "<span class=\"check_ok\">OK</span></div>";
			$bNoConnectionHost = false;
			
			echo "<div class=\"check\">";
			echo "$INSTALL_TD...";
			
			if (mysql_select_db($DATABASE_DB, $nConnection))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bTDNotInstalled = false;
				
				@$fp = fopen('cuteflow_dump_testdata.txt', 'r');
				while (!feof($fp))
				{
					$strCurRow = fgets($fp, 1600);
					if (($strCurRow[0] != '-') && (strlen($strCurRow) > 2))
					{
						$strCurRow 	= str_replace('default@default.de', $_REQUEST['strIN_TestData_Email'], $strCurRow);
						$nResult_DB = mysql_query($strCurRow, $nConnection);
					}
				}
				fclose($fp);
				
				
				//??
				$strQuery = "UPDATE `cf_user` SET 	`strEMail` = '$_REQUEST[strIN_TestData_Email]'
													WHERE `nID` = '1' LIMIT 1 ;";
				@mysql_query($strQuery, $nConnection);
				//??
			}
			else
			{
				echo "<span class=\"check_error\">failed.</span></div>";
				$bTDNotInstalled = true;
			}
		}
		else
		{
			echo "<span class=\"check_error\">failed: $INSTALL_ERROR_CONNECT</span></div>";
			$bNoConnectionHost = true;
		}
		
		if(!$bNoConnectionHost&&!$bTDNotInstalled)
		{
			echo "<br><div class=\"check\"><b>$INSTALL_TD_SUC</b></div>";
		}
		
		if($bNoConnectionHost||$bTDNotInstalled)
		{
			echo "<br><div class=\"check\"><b>$INSTALL_TRY_AGAIN</b></div>";
		}
		echo "</div></div>";
			
		echo "<form method=\"post\" action=\"settings_testdata.php\" name=\"left\"><div class=\"bottom_left\">";
			echo "<input type=\"hidden\" name=\"bRBInstall_TestData\" value=\"".$_REQUEST["bRBInstall_TestData"]."\">";
			echo "<input type=\"hidden\" name=\"strIN_TestData_Email\" value=\"".$_REQUEST["strIN_TestData_Email"]."\">";
			echo "<input type=\"submit\" value=\"$INSTALL_BUTT_BAC\" class=\"button_prev\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
		echo "</div></form>";
		
		echo "<form method=\"post\" action=\"startcuteflow.php\" name=\"right\"><div class=\"bottom_right\">";
		echo "<div class=\"bottom_right\">";
		if(!$bNoConnectionHost&&!$bTDNotInstalled)
		{
			echo "<input type=\"submit\" value=\"$INSTALL_BUTT_CON\" class=\"button_next\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
		}
		echo "</div></form>";
	}
?>

</div>
</center>
</body>
</html>