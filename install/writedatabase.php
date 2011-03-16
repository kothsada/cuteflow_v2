<?php
	@copy('default_config.inc.php', '../config/config.inc.php');
	
	require_once '../language_files/language.inc.php';
	require_once 'new_ver.inc.php';
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
					<strong style="font-size:8pt;font-weight:normal">Version <?php echo $nNewVersion ?></strong>
				</div>
			</div>
				
			<div class="step">
				
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<span class="small"><?php echo "$INSTALL_STEP"; ?> 3/5<br>
						<?php echo $INSTALL_STEP1 ?> >&gt; <?php echo $INSTALL_STEP2 ?> >&gt; <span class="mandatory"><?php echo $INSTALL_STEP3 ?></span>
						</span>
					</td>
				</tr>
				</table>
			</div>
			
			<div class="content_border">
				<span class="underline"><?php echo $INSTALL_HEAD3_2 ?></span>
				<div class="content">
		
		<?php
			
			//reading default DB settings
			echo "<br><div class=\"check\">";
			echo $INSTALL_DB_READ." ...";
			$fpCheckDB = fopen ("default_db_config.inc.php","r");
			$arrDBAccess = array();
			for ($nIndex=0; $nIndex <=7; $nIndex++) 
			{	
				$arrDBAccess[] = fgets($fpCheckDB,50);	
			} 	
			fclose($fpCheckDB);
			echo "<span class=\"check_ok\">OK</span></div>";
			
			//preparing the new DB settings
			
			$nCurrent_Row = 0;
			$arrNewConfig = array();
			foreach ($arrDBAccess as $strCurRow)
			{
				if ($nCurrent_Row==0||$nCurrent_Row==1||$nCurrent_Row==2||$nCurrent_Row==7)
				{
					$arrNewConfig[] = $strCurRow;
				}
				if ($nCurrent_Row == 3)
				{
					$arrNewConfig[] = str_replace('DEFHOST', $_REQUEST['strIN_Host'], $strCurRow);
				}
				if ($nCurrent_Row == 4)
				{
					$arrNewConfig[] = str_replace('DEFDB', $_REQUEST['strIN_DB'], $strCurRow);
				}
				if ($nCurrent_Row == 5)
				{
					$arrNewConfig[] = str_replace('DEFPWD', $_REQUEST['strIN_Pwd'], $strCurRow);
				}
				if ($nCurrent_Row == 6)
				{
					$arrNewConfig[] = str_replace('DEFUSERID', $_REQUEST['strIN_UserID'], $strCurRow);
				}
				$nCurrent_Row++;
			}
			
			//writing new DB settings
			echo "<div class=\"check\">";
			echo $INSTALL_DB_WRITE." ...";
			
			$fp = fopen('../config/db_config.inc.php', 'w');
			$nError = 0;
			foreach ($arrNewConfig as $newLine)
			{
				$bFP = @fwrite ($fp,$newLine);
				if (!$bFP)
				{
					$nError++;
				}
			}
			fclose($fp);
			
			if ($nError>0)
			{
				echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'config'</span></div>";
				$bWriteConfigError = true;
			}
			else
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bWriteConfigError = false;
			}
			
			//Creating Database and tables
			echo "<div class=\"check\">";
			echo "$INSTALL_DB_CONNECT <b>$DATABASE_HOST</b>...";
			
			require_once '../config/db_config.inc.php';
			
			$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD) or die (mysql_error());
				if ($nConnection)
				{
					echo "<span class=\"check_ok\">OK</span></div>";
					$bNoConnectionHost = false;
					
					$fp = fopen('cuteflow_dump.txt', 'r');
					
					$query_createDB = "CREATE DATABASE `$DATABASE_DB`";
					$nResult = mysql_query($query_createDB, $nConnection);
					
					echo "<div class=\"check\">";
					echo "$INSTALL_DB_DB <b>$DATABASE_DB</b>...";
					
					if (mysql_select_db($DATABASE_DB, $nConnection))
					{
						// create random password
							$strBlowfishPassword = '';				
							$arrLetters = array ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 	'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
													 												'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
							$nLetters = sizeof($arrLetters);				
							$nMax = 8;
							for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
							{
								$strBlowfishPassword .= $arrLetters[rand(1, $nLetters)];
							}
							$strBlowfishPassword = md5($strBlowfishPassword);
						
						
						echo "<span class=\"check_ok\">OK</span></div>";
						$bDBNotInstalled = false;
						echo "<div class=\"check\">";
						echo "$INSTALL_DB_TABLES ...";
						while (!feof($fp))
						{
							$strCurRow = fgets($fp,4096);
							
							if (($strCurRow[0] != '-') && (strlen($strCurRow) > 2))
							{
								$nResult_DB = mysql_query($strCurRow, $nConnection);
							}
						}
						fclose($fp);
						echo "<span class=\"check_ok\">OK</span></div>";
						
						echo "<div class=\"check\">";
						echo "$INSTALL_DB_WRITECONFIG ...";
						
						$strQuery = "UPDATE cf_config SET	strDefLang		= '".$_REQUEST['language']."',
															strEmail_Format	= 'HTML',
															strEmail_Values	= 'IFRAME',
															strVersion		= '$nNewVersion',
															strUrlPassword	= '$strBlowfishPassword',
															tsLastUpdate	= '".time()."',
															UserDefined1_Title = 'user-defined1',
															UserDefined2_Title = 'user-defined2'
															WHERE nConfigID = '1';";
						@mysql_query($strQuery, $nConnection);
						echo "<span class=\"check_ok\">OK</span></div>";
					}
					else
					{
						echo "<span class=\"check_error\">error: $INSTALL_ERROR_INSTDB</span></div>";
						$bDBNotInstalled = true;
					}
				}
				else
				{
					echo "<span class=\"check_error\">error: $INSTALL_ERROR_CONNECT</span></div>";
					$bNoConnectionHost = true;
				}
				
			if(!$bWriteConfigError&&!$bNoConnectionHost&&!$bDBNotInstalled)
			{
				echo "<br><div class=\"check\"><b>$INSTALL_DB_SUCCESS.</b></div>";
			}
			
			if($bWriteConfigError||$bNoConnectionHost||$bDBNotInstalled)
			{
				echo "<br><div class=\"check\"><b>$INSTALL_TRY_AGAIN.</b></div>";
			}
			echo "</div></div>";
				
			echo "<form method=\"post\" action=\"settings_database.php\" name=\"left\"><div class=\"bottom_left\">";
				echo "<input type=\"hidden\" name=\"strIN_Host\" value=\"".$_REQUEST["strIN_Host"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_DB\" value=\"".$_REQUEST["strIN_DB"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_Pwd\" value=\"".$_REQUEST["strIN_Pwd"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_UserID\" value=\"".$_REQUEST["strIN_UserID"]."\">";
				
				echo "<input type=\"submit\" value=\"$INSTALL_BUTT_BAC\" class=\"button_prev\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
			echo "</div></form>";
			
			echo "<form method=\"post\" action=\"settings_server.php\" name=\"right\"><div class=\"bottom_right\">";
			echo "<div class=\"bottom_right\">";
			if(!$bWriteConfigError&&!$bNoConnectionHost&&!$bDBNotInstalled)
			{
				echo "<input type=\"hidden\" name=\"strIN_Host\" value=\"".$_REQUEST["strIN_Host"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_DB\" value=\"".$_REQUEST["strIN_DB"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_Pwd\" value=\"".$_REQUEST["strIN_Pwd"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_UserID\" value=\"".$_REQUEST["strIN_UserID"]."\">";
				echo "<input type=\"submit\" value=\"$INSTALL_BUTT_CON\" class=\"button_next\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
			}
			echo "</div></form>";
		?>
		
		</div>
	</center>
</body>
</html>