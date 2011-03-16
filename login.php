<?php
	session_start();

	require_once 'config/config.inc.php';
	require_once 'config/db_config.inc.php';
	require_once 'language_files/language.inc.php';
	require_once 'config/ldap_common.php';
	
	$LDAP = getLDAP('config/');
	
	//--- check the user name and password
	$bAccessAllowed = false;
	
	if (($LDAP['auth_method'] == 'HYBRID') || ($LDAP['auth_method'] == 'HYBRID_AD')) //Check both LDAP and/ or database for authentication
	{
		//Check LDAP for the user
		//Check user credentials against LDAP
		$check = ldapAuthenticate($_REQUEST['UserId'], $_REQUEST['Password'], false);
	
		if ($check['passed']) //The credentials were verified
		{
			//See if user is in the database
			$db = checkDB($_REQUEST['UserId'], $_REQUEST['Password']);
			if ($db[success]) //The user was added or verified and updated against the database
			{
				if ( ($db[level] == 2) || ($db[level] == 4) || ($db[level] == 8) || ($db[level] == 1))
				{
					$bAccessAllowed = true;
				}
			}
			else
			{
				//LDAP Credentials failed
				$bAccessAllowed = false;
			}
	 
		}
		else
		{
			//LDAP Credentials failed
			$bAccessAllowed = false;
		}
	 
		//Check Database for the user
		if ($bAccessAllowed == false) //The user could not be verified via LDAP
		{
			//Check the local database
			$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
			if ($nConnection)
			{
				if (mysql_select_db($DATABASE_DB, $nConnection))
				{
					$strMd5Password = md5($_REQUEST["Password"]);

					$query = sprintf("SELECT * FROM cf_user WHERE strUserId='%s' AND strPassword='%s'",
            						mysql_real_escape_string($_REQUEST["UserId"]),
            						mysql_real_escape_string($strMd5Password));
            
					$nResult = mysql_query($query, $nConnection);
	
					if ($nResult)
					{
						if (mysql_num_rows($nResult) > 0)
						{		
							$arrRow = mysql_fetch_array($nResult);
						
							if ($arrRow)
							{
								$db["nID"] 		= $arrRow["nID"];
								$db["level"] 	= $arrRow["nAccessLevel"];
							}
	
							if ( ($db["level"] == 2) || ($db["level"] == 4) || ($db["level"] == 8) || ($db["level"] == 1))
							{
								$bAccessAllowed = true;	
							}
						}
					}
				}
				else 
				{
					// DB Error
				}
			}
		}
	}
	else
	{
		if ($LDAP[auth_method] == "" || $LDAP[auth_method] == "DB" || $_REQUEST["UserId"] == "admin") //DB
		{
			$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
			if ($nConnection)
			{
				if (mysql_select_db($DATABASE_DB, $nConnection))
				{
					$strMd5Password = md5($_REQUEST["Password"]);

					$query = sprintf("SELECT * FROM cf_user WHERE strUserId='%s' AND strPassword='%s'",
            						mysql_real_escape_string($_REQUEST["UserId"]),
            						mysql_real_escape_string($strMd5Password));
            						
					$nResult = mysql_query($query, $nConnection);
	
					if ($nResult)
					{
						if (mysql_num_rows($nResult) > 0)
						{		
							$arrRow = mysql_fetch_array($nResult);
						
							if ($arrRow)
							{
								$db["nID"] = $arrRow["nID"];
								$db["level"] =$arrRow["nAccessLevel"];
							}
	
							if ( ($db["level"] == 2) || ($db["level"] == 4) || ($db["level"] == 8) || ($db["level"] == 1))
							{
								$bAccessAllowed = true;	
							}
						}
					}
				}
				else 
				{
					//echo "DB not selected:".mysql_error()."<br>";
				}
			}
		} 
		else //LDAP
		{
			//Check user credentials against LDAP
			$check = ldapAuthenticate($_REQUEST['UserId'], $_REQUEST['Password'], false);
			
			if ($check['passed']) //The credentials were verified
			{
				//See if user is in the database
				$db = checkDB($_REQUEST['UserId'], $_REQUEST['Password']);
				if ($db[success]) //The user was added or verified and updated against the database
				{
					if ( ($db[level] == 2) || ($db[level] == 4) || ($db[level] == 8) || ($db[level] == 1))
					{
						$bAccessAllowed = true;
					}
				}
				else
				{
					//LDAP Credentials failed
					$bAccessAllowed = false;
				}
		 
			}
			else
			{
				//LDAP Credentials failed
				$bAccessAllowed = false;
			}
			
			
		}
	}
	
	$_SESSION['SESSION_CUTEFLOW_USERNAME']		= $_REQUEST['UserId'];
	$_SESSION['SESSION_CUTEFLOW_USERID'] 		= $db[nID];
	$_SESSION['SESSION_CUTEFLOW_PASSWORD'] 		= md5($_REQUEST['Password']);
	$_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'] 	= $db['level'];
	
	if ($bAccessAllowed == false)
	{
		session_unset();   //--- Unset session variables.
		session_destroy(); //--- End Session we created earlier.
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		<head>
			<?php 
				echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
			?>
			<title></title>
			<link rel="stylesheet" href="pages/format.css" type="text/css">
		</head>
		<body>
			<table width="100%" height="100%">
				<tr>
					<td align="center" valign="middle">
						<table width="300px" class="note">
							<tr>
								<td valign="top">
									<img src="images/stop2.png" height="48" width="48" alt="Stop">
								</td>
								<td>
									<?php echo $LOGIN_FAILURE;?><br>
									<br>
									<a href="javascript:history.back();"><?php echo $BTN_BACK;?></a>
								</td>
							</tr>
						</table>
					</td>
				</tr>		
			</table>	
		</body>
		</html>
		<?php
	}
	else
	{
		?>
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
		<html>
			<head>
				<?php 
					echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
				?>
				<title></title>
				<link rel="stylesheet" href="format.css" type="text/css">
				<script src="lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
				<script src="lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
				<script language="JavaScript">
				<!--					
					inpdata	= 'language=<?php echo $_REQUEST["language"];?>';
					encodeblowfish();
					encoded = outdata;
					
					parent.location.href = 'frame.php?key=' + encoded;
				//-->
				</script>
			</head>
			<body>
			</body>
		</html>
		<?php
	}
?>