<?php
	/** Copyright (c) Timo Haberkern. All rights reserved.
	*
	* Redistribution and use in source and binary forms, with or without 
	* modification, are permitted provided that the following conditions are met:
	* 
	*  o Redistributions of source code must retain the above copyright notice, 
	*    this list of conditions and the following disclaimer. 
	*     
	*  o Redistributions in binary form must reproduce the above copyright notice, 
	*    this list of conditions and the following disclaimer in the documentation 
	*    and/or other materials provided with the distribution. 
	*     
	*  o Neither the name of Timo Haberkern nor the names of 
	*    its contributors may be used to endorse or promote products derived 
	*    from this software without specific prior written permission. 
	*     
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
	* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
	* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
	* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
	* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
	* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
	* OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
	* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
	* OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
	* EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	*/
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';

	$nAccessLevel = 1;
	
	if (isset($_REQUEST["UserAccessLevel"]))
	{
		$nAccessLevel = $_REQUEST['UserAccessLevel'];
	}
	
	$arrSubstitutes;
	
	foreach ($_REQUEST as $key => $value)
	{
		$arrSplit = split('_', $key);
		if ($arrSplit[0] == 'SubstituteName')
		{
			if ($arrSplit[1] > 0 || $arrSplit[1] == -3)
			{
				$arrSubstitutes[] = $arrSplit[1];
			}
		}
	}
	
	$_REQUEST['SubstitudeId'] = 0;
	if (sizeof($arrSubstitutes) > 0)
	{
		$_REQUEST['SubstitudeId'] = -2;
	}
	
	switch ($_REQUEST["strIN_Email_Format"])
	{
		case 'TEXT':
			$mailFormat = "PLAIN";
			break;
		case 'HTML':
			$mailFormat = "HTML";
			break;	
	}


	switch ($_REQUEST["strIN_Email_Value"])
	{
		case 'NONE':
			$mailValues = "NONE";
			break;
		case 'VALUES':
			$mailValues = "VALUES";
			break;
		case 'IFRAME':
			$mailValues = "IFRAME";
			break;		
	}
	
	if ($_REQUEST['IN_bIndividualSubsTime'])
	{
		$_REQUEST['IN_bIndividualSubsTime'] = 0;
	}
	else
	{
		$_REQUEST['IN_bIndividualSubsTime'] = 1;
	}
	
	if ($_REQUEST['IN_bIndividualEmail'])
	{
		$_REQUEST['IN_bIndividualEmail'] = 0;
	}
	else
	{
		$_REQUEST['IN_bIndividualEmail'] = 1;
	}
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		//--- get maximum count of users
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			if ($_REQUEST['userid'] != -1)
			{
				$strQuery 	= "DELETE FROM cf_substitute WHERE user_id = '".$_REQUEST['userid']."'";
				$nResult 	= mysql_query($strQuery, $nConnection) or die ($strQuery.mysql_error());
			}
			
			// build the index string
			$strIndex = $_REQUEST['strLastName'].' '.
						$_REQUEST['strFirstName'].' '.
						$_REQUEST['strEMail'].' '.
						$_REQUEST['UserName'].' '.
						$_REQUEST['IN_street'].' '.
						$_REQUEST['IN_country'].' '.
						$_REQUEST['IN_zipcode'].' '.
						$_REQUEST['IN_city'].' '.
						$_REQUEST['IN_phone_main1'].' '.
						$_REQUEST['IN_phone_main2'].' '.
						$_REQUEST['IN_phone_mobile'].' '.
						$_REQUEST['IN_fax'].' '.
						$_REQUEST['IN_organisation'].' '.
						$_REQUEST['IN_department'].' '.
						$_REQUEST['IN_cost_center'].' '.
						$_REQUEST['IN_userdefined1_value'].' '.
						$_REQUEST['IN_userdefined2_value'];
			
			if ($_REQUEST["userid"] == -1)
			{	// add new user
				$query = "INSERT INTO cf_user values (	null,
														'".$_REQUEST['strLastName']."',
														'".$_REQUEST['strFirstName']."',
														'".$_REQUEST['strEMail']."',
														'$nAccessLevel',
														'".$_REQUEST['UserName']."',
														'".md5($_REQUEST['Password1'])."',
														'$mailFormat',
														'$mailValues',
														'".$_REQUEST['SubstitudeId']."',
														0,
														0,
														'".$_REQUEST['IN_street']."',
														'".$_REQUEST['IN_country']."',
														'".$_REQUEST['IN_zipcode']."',
														'".$_REQUEST['IN_city']."',
														'".$_REQUEST['IN_phone_main1']."',
														'".$_REQUEST['IN_phone_main2']."',
														'".$_REQUEST['IN_phone_mobile']."',
														'".$_REQUEST['IN_fax']."',
														'".$_REQUEST['IN_organisation']."',
														'".$_REQUEST['IN_department']."',
														'".$_REQUEST['IN_cost_center']."',
														'".$_REQUEST['IN_userdefined1_value']."',
														'".$_REQUEST['IN_userdefined2_value']."',
														'".$_REQUEST['strIN_Subtitute_Person_Value']."',
														'".$_REQUEST['strIN_Subtitute_Person_Unit']."',
														'".$_REQUEST['IN_bIndividualSubsTime']."',
														'".$_REQUEST['IN_bIndividualEmail']."'
														)";	
				$nResult = @mysql_query($query, $nConnection);
				
				// get the User Id
				$strQuery = "SELECT MAX(nID) as max_id FROM cf_user";
				$nResult = mysql_query($strQuery, $nConnection);
				$arrResult = mysql_fetch_array($nResult, MYSQL_ASSOC);
				
				$nUserId = $arrResult['max_id'];
				
				// write the index String
				$strQuery = "INSERT INTO cf_user_index values (	'$nUserId',
																'$strIndex')";
				$nResult = mysql_query($strQuery, $nConnection);
			}
			else
			{	// update existing user
				$query = "UPDATE cf_user SET 	strLastName		= '".$_REQUEST['strLastName']."',
												strFirstName	= '".$_REQUEST['strFirstName']."',
												strEMail		= '".$_REQUEST['strEMail']."',
												nAccessLevel	= '".$nAccessLevel."',
												strUserId		= '".$_REQUEST['UserName']."',
												nSubstitudeId	= '".$_REQUEST['SubstitudeId']."',
												strEmail_Format	= '".$mailFormat."',
												strEmail_Values	= '".$mailValues."',
												strStreet		= '".$_REQUEST['IN_street']."',
												strCountry		= '".$_REQUEST['IN_country']."',
												strZipcode		= '".$_REQUEST['IN_zipcode']."',
												strCity			= '".$_REQUEST['IN_city']."',
												strPhone_Main1	= '".$_REQUEST['IN_phone_main1']."',
												strPhone_Main2	= '".$_REQUEST['IN_phone_main2']."',
												strPhone_Mobile	= '".$_REQUEST['IN_phone_mobile']."',
												strFax			= '".$_REQUEST['IN_fax']."',
												strOrganisation	= '".$_REQUEST['IN_organisation']."',
												strDepartment	= '".$_REQUEST['IN_department']."',
												strCostCenter	= '".$_REQUEST['IN_cost_center']."',
												UserDefined1_Value	= '".$_REQUEST['IN_userdefined1_value']."',
												UserDefined2_Value	= '".$_REQUEST['IN_userdefined2_value']."',
												nSubstituteTimeValue	= '".$_REQUEST['strIN_Subtitute_Person_Value']."',
												strSubstituteTimeUnit		= '".$_REQUEST['strIN_Subtitute_Person_Unit']."',
												bUseGeneralSubstituteConfig = '".$_REQUEST['IN_bIndividualSubsTime']."',
												bUseGeneralEmailConfig 		= '".$_REQUEST['IN_bIndividualEmail']."'
												";
				
				if ($_REQUEST["Password1"] != "unchanged")
				{
					$query .= ", strPassword	= '".md5($_REQUEST["Password1"])."'";	
				}
				
				$query .= " WHERE nID = '".$_REQUEST["userid"]."' LIMIT 1;";
				$nResult = mysql_query($query, $nConnection);
				
				// write the index String
				$strQuery = "UPDATE cf_user_index SET `index` = '$strIndex' WHERE user_id = '".$_REQUEST['userid']."' LIMIT 1";
				$nResult = mysql_query($strQuery, $nConnection) or die(mysql_error());
			}
			
			if ($_REQUEST['userid'] == -1)
			{
				$strQuery 	= "SELECT MAX(nID) FROM cf_user LIMIT 1;";
				$nResult 	= mysql_query($strQuery);
				
				$arrRow = mysql_fetch_array($nResult);
				$_REQUEST['userid'] = $arrRow[0]; 
			}
			
			$nMax = sizeof($arrSubstitutes);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$strQuery 	= "INSERT INTO cf_substitute VALUES (NULL, '".$_REQUEST['userid']."', '".$arrSubstitutes[$nIndex]."', '$nIndex')";
				$nResult 	= mysql_query($strQuery, $nConnection) or die ($strQuery.mysql_error());
			}
		}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	<script language="JavaScript">
		function onLoad()
		{
			<?php echo "location.href=\"showuser.php?language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\"";?>
		}
	</script>
</head>
<body onLoad="onLoad()">

</body>
