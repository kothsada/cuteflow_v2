<?php
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
						<span class="small"><?php echo "$INSTALL_STEP"; ?> 3/5<br>
						<?php echo $INSTALL_STEP1 ?> >> <?php echo $INSTALL_STEP2 ?> >> <span class="mandatory"><?php echo $INSTALL_STEP3 ?></span>
						</span>
					</td>
				</tr>
				</table>
			</div>
			
			<div class="content_border">
				<span class="underline"><?php echo $INSTALL_HEAD3_4 ?></span>
				<div class="content">
		
		<?php
		
			//Creating Database and tables
			echo "<div class=\"check\">";
			echo "$INSTALL_DB_CONNECT <b>$DATABASE_HOST</b>...";
			
			if ($_REQUEST[strIN_CF_Server]!="")
			{
				$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD) or die (mysql_error());
				if ($nConnection)
				{
					echo "<span class=\"check_ok\">OK</span></div>";
					$bNoConnectionHost = false;
					
					echo "<div class=\"check\">";
					echo "$INSTALL_DB_CONNECT_DB <b>$DATABASE_DB</b>...";			
					if (mysql_select_db($DATABASE_DB, $nConnection))
					{
						echo "<span class=\"check_ok\">OK</span></div>";
						$bNoDBConnection = false;				
						
						echo "<div class=\"check\">";
						echo "$INSTALL_DB_SMTP ...";	
						
						$strQuery = "UPDATE `cf_config` SET 	`strCF_Server` 		= '".$_REQUEST['strIN_CF_Server']."',
																`strSMTP_use_auth` 	= '".$_REQUEST['bRB_SMTP_use_auth']."',
																`strSMTP_server` 	= '".$_REQUEST['strIN_SMTP_Server']."',
																`strSMTP_port` 		= '".$_REQUEST['strIN_SMTP_port']."',
																`strSMTP_userid` 	= '".$_REQUEST['strIN_SMTP_userid']."',
																`strSMTP_pwd` 		= '".$_REQUEST['strIN_SMTP_pwd']."',
																`strMailSendType`	= '".$_REQUEST['mail_send_type']."',
																`strMtaPath`		= '".$_REQUEST['mta_path']."',
																`strSmtpEncryption`	= '".$_REQUEST['SMTP_encryption']."'
																WHERE `nConfigID` 	= 1 LIMIT 1 ;";
						
																
						if (mysql_query($strQuery, $nConnection))
						{
							echo "<span class=\"check_ok\">OK</span></div>";
							$bNoSettingsWritten = false;
						}
						else
						{
							echo "<span class=\"check_error\">failed.</span></div>";
							$bNoSettingsWritten = true;
						}
					}
					else
					{
						echo "<span class=\"check_error\">failed: $INSTALL_ERROR_CONNECT</span></div>";
						$bNoDBConnection = true;
					}
				}
				else
				{
					echo "<span class=\"check_error\">failed: $INSTALL_ERROR_CONNECT</span></div>";
					$bNoConnectionHost = true;
				}	
			}
			else
			{
					echo "<span class=\"check_error\">failed: $INSTALL_ERROR_CFSERVER</span></div>";
					$bNoConnectionHost = true;
			}
			
			
			if(!$bNoConnectionHost&&!$bNoDBConnection&&!$bNoSettingsWritten)
			{
				echo "<br><div class=\"check\"><b>$INSTALL_SMTP_SUC</b></div>";
			}
			
			if($bNoConnectionHost||$bNoDBConnection||$bNoSettingsWritten)
			{
				echo "<br><div class=\"check\"><b>$INSTALL_TRY_AGAIN</b></div>";
			}
			
			echo "</div></div>";
				
			echo "<form method=\"post\" action=\"settings_server.php\" name=\"left\"><div class=\"bottom_left\">";
				echo "<input type=\"hidden\" name=\"strIN_CF_Server\" value=\"".$_REQUEST["strIN_CF_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_Server\" value=\"".$_REQUEST["strIN_SMTP_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_port\" value=\"".$_REQUEST["strIN_SMTP_port"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_userid\" value=\"".$_REQUEST["strIN_SMTP_userid"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_pwd\" value=\"".$_REQUEST["strIN_SMTP_pwd"]."\">";
				echo "<input type=\"hidden\" name=\"bRB_SMTP_use_auth\" value=\"".$_REQUEST["bRB_SMTP_use_auth"]."\">";
				
				echo "<input type=\"submit\" value=\"$INSTALL_BUTT_BAC\" class=\"button_prev\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
			echo "</div></form>";
			
			echo "<form method=\"post\" action=\"settings_testdata.php\" name=\"right\"><div class=\"bottom_right\">";
			echo "<div class=\"bottom_right\">";
			if(!$bNoConnectionHost&&!$bNoDBConnection&&!$bNoSettingsWritten)
			{
				echo "<input type=\"hidden\" name=\"strIN_CF_Server\" value=\"".$_REQUEST["strIN_CF_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_Server\" value=\"".$_REQUEST["strIN_SMTP_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_port\" value=\"".$_REQUEST["strIN_SMTP_port"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_userid\" value=\"".$_REQUEST["strIN_SMTP_userid"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_pwd\" value=\"".$_REQUEST["strIN_SMTP_pwd"]."\">";
				echo "<input type=\"hidden\" name=\"bRB_SMTP_use_auth\" value=\"".$_REQUEST["bRB_SMTP_use_auth"]."\">";
				echo "<input type=\"submit\" value=\"$INSTALL_BUTT_CON\" class=\"button_next\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
			}
			echo "</div></form>";
		?>
		
		</div>
	</center>
	
</body>
</html>