<?PHP
/*****************************************************/
/*****************************************************/
/**													**/
/**		Christopher A. Usey							**/
/**		April 2007									**/
/**		Copyright 2007								**/	
/**													**/
/*****************************************************/
/*****************************************************/


/*
FUNCTION: WriteConfigFile()
DESC: This function will gather all passed information and
write it to the LDAPConfig.php file
PARAMS: $assoc_array (array of values to write), $path (path to the LDAPConfig file)
RETURNS: Bool representing success or failure
*/
function writeConfigFile($assoc_array, $path)
{
	//SET config file path
	$LDAPConfigPath = $path."LDAPConfig.php";
	
	//Start building the file
	$content = "; <?php exit; ?> \n\n";
	
	//Get all variables that were passed from the form
	foreach ($assoc_array as $key => $value)
	{
		if ($key != "m")
		{
		$content .= "{$key} = \"{$value}\"\n";
		}
	}
	
	//Write the file and pass a bool that signifies sucess or failure
	$path = $LDAPConfigPath;
	if ($fh = fopen($path, 'w'))
	{
			if (fwrite($fh, $content))
			{
				fclose($fh);
				return true;
			}
			else
			{
				fclose($fh);
				return false;
			}
	}
	else
	{
		return false;
	}

}

/*
Function Name = ldapAuthenticate()
Desc = This function will authenticate against information
found in LDAPConfig.php.
Params = $id (user id), $password (password)
Returns: array($LDAPInfo)containing values retrieved from LDAP and / or passed
variable to determine sucessful credentaials
*/
function ldapAuthenticate($id, $password)
{
	//Get LDAP Config values
	$ldap			= getLDAP("config/");
	$auth_method	= $ldap["auth_method"];
	$ldap_host		= $ldap["ldap_host"];
	$ldap_domain	= $ldap["ldap_domain"];
	$ldap_binddn	= $ldap["ldap_binddn"];
	$ldap_bindpwd	= $ldap["ldap_bindpwd"];
	$ldap_searchattr= $ldap["ldap_searchattr"];
	$ldap_fname		= $ldap["ldap_fname"];
	$ldap_lname		= $ldap["ldap_lname"];
	$ldap_uname		= $ldap["ldap_uname"];
	$ldap_email_add	= $ldap["ldap_email"];
	$ldap_office	= $ldap["ldap_office"];
	$ldap_phone		= $ldap["ldap_phone"];
	$ldap_context	= $ldap["ldap_context"];
	$ldap_rootdn	= $ldap["ldap_rootdn"];
	$default_level  = $ldap["default_level"];
	
	$connection = @ldap_connect($ldap_host)  or die('Could not connect to LDAP server.');
	ldap_set_option ($connection, LDAP_OPT_PROTOCOL_VERSION, 3); 
	ldap_set_option ($connection, LDAP_OPT_REFERRALS, 0); 
	
	//Cant connect to ldap
	if (!$connection)
	{
		$LDAPInfo['passed'] = false;
		return $LDAPInfo;
	}

	//Connection made -- bind and get dn for username
	$ldapbind = ldap_bind($connection, $ldap_binddn, $ldap_bindpwd);
	
	//Check to make sure we are bound
	if (!$ldapbind)
	{
		ldap_close($connection);
		$LDAPInfo['passed'] = false;
		return $LDAPInfo;
	}
	
	$filter	= $ldap_searchattr.'='.$id;
	$sr		= ldap_search($connection, $ldap_rootdn, $filter);

	//Make sure only ONE result was returned
	if (ldap_count_entries($connection,$sr) != 1)
	{
		ldap_close($connection);
		$LDAPInfo['passed'] = false;
		return $LDAPInfo;
	}
	
	$info = ldap_get_entries($connection, $sr);

	//Now, try to rebind with their full dn and password
	if (($auth_method == 'AD') || ($auth_method == 'HYBRID_AD'))
	{
		$userbind = $id.'@'.$ldap_domain;
	}
	else
	{
		$userbind= $info[0][$ldap_context];
	}
	//Make sure a password was sent
	if (!isset($password) || $password != '')
	{	
		$ldapbind = ldap_bind($connection, $userbind, $password);
		
		if (!$ldapbind)
		{
			ldap_close($connection);
			$LDAPInfo['passed'] = false;
			return $LDAPInfo;
		}
		
		$LDAPInfo['passed'] 	= true;
		$LDAPInfo['fname'] 		= $info[0][$ldap_fname][0];
		$LDAPInfo['lname']		= $info[0][$ldap_lname][0];
		$LDAPInfo['uname']		= $info[0][$ldap_uname][0];
		$LDAPInfo['email']		= $info[0][$ldap_email_add][0];
		$LDAPInfo['password']	= md5($password);
		$LDAPInfo['office']		= $info[0][$ldap_office][0];
		$LDAPInfo['phone']		= $info[0][$ldap_phone][0];
		$LDAPInfo['default_level']= $default_level;
		return $LDAPInfo;
	}
	else
	{
		ldap_close($connection);
		$LDAPInfo["passed"] = false;
		return $LDAPInfo;
	}
}

/*
Function Name = getLDAP()
Desc = This function will parse the LDAPConfig.php file.
Returns = Array with LDAP values parsed from the config file.
Params = $path (path to the LDAPConfig file)
*/
function getLDAP($path)
{
	//SET config file path
	$LDAPConfigPath = $path."LDAPConfig.php";
		
	//Check existance of the file
	if (file_exists($LDAPConfigPath))
	{
		//Parse the config file
		$LDAPconfig = parse_ini_file($LDAPConfigPath);
		
		//Store into array and return the array
		$LDAP["auth_method"]	  = $LDAPconfig[auth_method];
		$LDAP["ldap_host"]		  = $LDAPconfig[ldap_host];
		$LDAP["ldap_domain"]	  = $LDAPconfig[ldap_domain];
		$LDAP["ldap_binddn"]	  = $LDAPconfig[ldap_binddn];
		$LDAP["ldap_bindpwd"]	  = $LDAPconfig[ldap_bindpwd];
		$LDAP["ldap_rootdn"]	  = $LDAPconfig[ldap_rootdn];
		$LDAP["ldap_searchattr"]  = $LDAPconfig[ldap_searchattr];
		$LDAP["ldap_fname"] 	  = $LDAPconfig[ldap_fname];
		$LDAP["ldap_lname"] 	  = $LDAPconfig[ldap_lname];
		$LDAP["ldap_uname"] 	  = $LDAPconfig[ldap_uname];
		$LDAP["ldap_email"]		  = $LDAPconfig[ldap_email_add];
		$LDAP["ldap_office"] 	  = $LDAPconfig[ldap_office];
		$LDAP["ldap_phone"] 	  = $LDAPconfig[ldap_phone];
		$LDAP["ldap_context"]  	  = $LDAPconfig[ldap_context];
		$LDAP["default_level"] 	  = $LDAPconfig[default_level];
			
		//Return array
		return $LDAP;
	}	
}

/*
Function Name = checkDB()
Desc = This function will check to see if a LDAP user
is present in the database and if not add or update his information
Params = $LDAP as array
*/
function checkDB($username, $password)
{
	GLOBAL $DATABASE_HOST, $DATABASE_DB, $DATABASE_PWD, $DATABASE_UID;
	
	require_once 'db_connect.inc.php';
	
	$LDAP = ldapAuthenticate($username, $password);
	
	$strMd5Password = md5($password);
	$userID			= $username;
	
	$query 			= "select * from cf_user where strUserId = '$userID' and bDeleted = 0";
	$nResult 		= mysql_query($query, $connection);

	//Check if any results were returned
	if ($nResult)
	{
		if (mysql_num_rows($nResult) > 0) //The user has been found
		{		
			$arrRow = mysql_fetch_array($nResult);
			if ($arrRow)
			{
				$nID = $arrRow['nID'];
				$level = $arrRow['nAccessLevel'];
				//Update Users Info to keep it current
				$query = "UPDATE `cf_user` SET 	strLastName 	= '".$LDAP['lname']."',
												strFirstName	= '".$LDAP['fname']."'";
				
				if ($LDAP['email'] != '') 		$query .=	", strEMail			= '".$LDAP['email']."'";
				if ($LDAP['password'] != '') 	$query .=	", strPassword		= '".$LDAP['password']."'";
				if ($LDAP['phone'] != '') 		$query .=	", strPhone_Main1	= '".$LDAP['phone']."'";
				if ($LDAP['office'] != '') 		$query .=	", strOrganisation	= '".$LDAP['office']."'";
				
				$query .=	"WHERE strUserId	= '".$LDAP['uname']."' and bDeleted = 0";
				
				$nResult = @mysql_query($query, $connection);
				if ($nResult) //The update was processed
				{
					// now write the user_index
					// first read the user details
					$strQuery 	= "SELECT * FROM cf_user WHERE strUserId = '".$LDAP['uname']."' and bDeleted = 0 LIMIT 1;";
					$nResult 	= @mysql_query($strQuery, $connection);
					$arrResult	= mysql_fetch_array($nResult, MYSQL_ASSOC);
					
					// build the index string
					$strIndex = $arrResult['strLastName'].' '.
								$arrResult['strFirstName'].' '.
								$arrResult['strEMail'].' '.
								$arrResult['strUserId'].' '.
								$arrResult['strStreet'].' '.
								$arrResult['strCountry'].' '.
								$arrResult['strZipcode'].' '.
								$arrResult['strCity'].' '.
								$arrResult['strPhone_Main1'].' '.
								$arrResult['strPhone_Main1'].' '.
								$arrResult['strPhone_Mobile'].' '.
								$arrResult['strFax'].' '.
								$arrResult['strOrganisation'].' '.
								$arrResult['strDepartment'].' '.
								$arrResult['strCostCenter'].' '.
								$arrResult['UserDefined1_Value'].' '.
								$arrResult['UserDefined2_Value'];
					
					// write the index String
					$strQuery = "REPLACE INTO cf_user_index values ('".$arrResult['nID']."','".$strIndex."')";

					$nResult = mysql_query($strQuery, $connection) or die(mysql_error());
								
					$arrDB["success"] 	= true;
					$arrDB["level"] 	= $level;
					$arrDB["nID"] 		= $nID;
					return $arrDB;
				}
				else
				{
					echo "Error cannot update user information -- contact your administrator";
					exit;
				}
			}
		}
		else //The user is not in the database -- Add the user
		{			
			$query = "INSERT INTO `cf_user` VALUES	(NULL,
													 '".$LDAP['lname']."',
													 '".$LDAP['fname']."',
													 '".$LDAP['email']."',
													 '".$LDAP['default_level']."',
													 '".$LDAP['uname']."',
													 '".$LDAP['password']."',
													 'PLAIN',
													 'NONE',
													 0,
													 0,
													 0,
													 '',
													 '',
													 '',
													 '',
													 '".$LDAP['phone']."',
													 '',
													 '',
													 '',
													 '".$LDAP['office']."',
													 '',
													 '',
													 '',
													 '',
													 '1',
													 'DAYS',
													 1,
													 1
														)";
			
			$nResult 		= mysql_query($query, $connection);
			
			// now write the user_index
			// first read the user details
			$strQuery 	= "SELECT * FROM cf_user WHERE strUserId = '".$LDAP['uname']."' and bDeleted = 0 LIMIT 1;";
			$nResult 	= @mysql_query($strQuery, $connection);
			$arrResult	= mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			// build the index string
			$strIndex = $arrResult['strLastName'].' '.
						$arrResult['strFirstName'].' '.
						$arrResult['strEMail'].' '.
						$arrResult['strUserId'].' '.
						$arrResult['strStreet'].' '.
						$arrResult['strCountry'].' '.
						$arrResult['strZipcode'].' '.
						$arrResult['strCity'].' '.
						$arrResult['strPhone_Main1'].' '.
						$arrResult['strPhone_Main1'].' '.
						$arrResult['strPhone_Mobile'].' '.
						$arrResult['strFax'].' '.
						$arrResult['strOrganisation'].' '.
						$arrResult['strDepartment'].' '.
						$arrResult['strCostCenter'].' '.
						$arrResult['UserDefined1_Value'].' '.
						$arrResult['UserDefined2_Value'];
			
			// write the index String
			$strQuery = "INSERT INTO cf_user_index values ( '".$arrResult['nID']."','".$strIndex."')";

			$nResult = @mysql_query($strQuery, $connection);
			
			
			
			$level 			= $LDAP[default_level];
			$arrDB['success']	= true;
			$arrDB['level'] 	= $level;
			$arrDB['nID'] 		= mysql_insert_id(); 
			return $arrDB;
		}
	}
}
?>