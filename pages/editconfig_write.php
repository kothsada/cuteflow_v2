<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/ldap_common.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
   	<link rel="stylesheet" href="format.css" type="text/css">
   	<style>
		.table_header
		{
			background-color: #A0A0A0; 
			color: White; 
			font-size: 8pt; 
			font-weight: bold;
		}
		.table_header2
		{
			background-color: Red; 
			color: White; 
			font-size: 8pt; 
			font-weight: bold;
		}
	</style>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			var strParams	= "language=<?php echo $_REQUEST['language']; ?>&saved=true";
			inpdata	= strParams;
			encodeblowfish();
			location.href = "editconfig.php?key=" + outdata;
		}
	//-->
	</script>
</head>
<body onLoad="siteLoaded()">
<div align="left" width="100%">
<?php

$arrCols = array (	'NAME'		=> 1,
					'STATION'	=> 1,
					'DAYS'		=> 1,
					'START'		=> 1,
					'SENDER'	=> 1,
					'MAILLIST'	=> 1,
					'TEMPLATE'	=> 1,
					'WHOLETIME'	=> 1,
					'FIELD1'	=> 1,
					'FIELD2'	=> 1
					);

$strNewColsOrder = '';
while(list($key, $value) = each($_REQUEST))
{
	switch($key)
	{
		case 'CB_NAME':
			$strNewColsOrder = $strNewColsOrder.'NAME---1---';
			$arrCols['NAME'] = 0;
			break;
		case 'CB_STATION':
			$strNewColsOrder = $strNewColsOrder.'STATION---1---';
			$arrCols['STATION'] = 0;
			break;
		case 'CB_DAYS':
			$strNewColsOrder = $strNewColsOrder.'DAYS---1---';
			$arrCols['DAYS'] = 0;
			break;
		case 'CB_START':
			$strNewColsOrder = $strNewColsOrder.'START---1---';
			$arrCols['START'] = 0;
			break;
		case 'CB_SENDER':
			$strNewColsOrder = $strNewColsOrder.'SENDER---1---';
			$arrCols['SENDER'] = 0;
			break;
		case 'CB_MAILLIST':
			$strNewColsOrder = $strNewColsOrder.'MAILLIST---1---';
			$arrCols['MAILLIST'] = 0;
			break;
		case 'CB_TEMPLATE':
			$strNewColsOrder = $strNewColsOrder.'TEMPLATE---1---';
			$arrCols['TEMPLATE'] = 0;
			break;
		case 'CB_WHOLETIME':
			$strNewColsOrder = $strNewColsOrder.'WHOLETIME---1---';
			$arrCols['WHOLETIME'] = 0;
			break;
		case 'CB_FIELD1':
			$strNewColsOrder = $strNewColsOrder.'F1/'.$_REQUEST['field1-id'].'---1---';
			$arrCols['FIELD1'] = 0;
			break;
		case 'CB_FIELD2':
			$strNewColsOrder = $strNewColsOrder.'F2/'.$_REQUEST['field2-id'].'---1---';
			$arrCols['FIELD2'] = 0;
			break;
	}
}

asort($arrCols);

$nMax = sizeof($arrCols);
$nRunningNumber = 1;
while(list($key, $value) = each($arrCols))
{
	if ($value)
	{
		switch($key)
		{
			case 'NAME':
				$strNewColsOrder = $strNewColsOrder.'NAME---0---';
				break;
			case 'STATION':
				$strNewColsOrder = $strNewColsOrder.'STATION---0---';
				break;
			case 'DAYS':
				$strNewColsOrder = $strNewColsOrder.'DAYS---0---';
				break;
			case 'START':
				$strNewColsOrder = $strNewColsOrder.'START---0---';
				break;
			case 'SENDER':
				$strNewColsOrder = $strNewColsOrder.'SENDER---0---';
				break;
			case 'MAILLIST':
				$strNewColsOrder = $strNewColsOrder.'MAILLIST---0---';
				break;
			case 'TEMPLATE':
				$strNewColsOrder = $strNewColsOrder.'TEMPLATE---0---';
				break;
			case 'WHOLETIME':
				$strNewColsOrder = $strNewColsOrder.'WHOLETIME---0---';
				break;
			case 'FIELD1':
				$strNewColsOrder = $strNewColsOrder.'F1/'.$_REQUEST['field1-id'].'---0---';
				break;
			case 'FIELD2':
				$strNewColsOrder = $strNewColsOrder.'F2/'.$_REQUEST['field2-id'].'---0---';
				break;
		}
	}
	
	if ($nRunningNumber == $nMax)
	{		
		$nLength = strlen($strNewColsOrder);
		
		$nNewLength = $nLength - 3;
		
		substr($strNewColsOrder,0,$nNewLength);
		
		$strNewColsOrder = substr($strNewColsOrder,0,$nNewLength);
	}	
	$nRunningNumber++;
}

$myOrder = $strNewColsOrder;

$mySort = $_REQUEST["strIN_Def_Sort"];

switch ($_REQUEST["strIN_Email_Format"])
{
	case "TEXT":
		$mailFormat = "PLAIN";
		break;
	case "HTML":
		$mailFormat = "HTML";
		break;
	default:
		$mailFormat = "HTML";
		break;
}
switch ($_REQUEST["strIN_Email_Value"])
{
	case "NONE":
		$mailValues = "NONE";
		break;
	case "VALUES":
		$mailValues = "VALUES";
		break;
	case "IFRAME":
		$mailValues = "IFRAME";
		break;
	default:
		$mailValues = "IFRAME";
		break;		
}

//Substitute Person vars
$strMySP_Unit 	= $_REQUEST['strIN_Subtitute_Person_Unit'];
$nMySP_Hours 	= $_REQUEST['strIN_Subtitute_Person_Value'];

if ($_REQUEST["IN_UnencryptedRequest"])
{
	$_REQUEST["IN_UnencryptedRequest"] = 1;
}
else
{
	$_REQUEST["IN_UnencryptedRequest"] = 0;
}

$strQuery = "UPDATE `cf_config` SET 	`strCF_Server` 				= '".$_REQUEST["strIN_CF_Server"]."',
										`strSMTP_use_auth` 			= '".$_REQUEST["bRB_SMTP_use_auth"]."',
										`strSMTP_server` 			= '".$_REQUEST["strIN_SMTP_Server"]."',
										`strSMTP_port` 				= '".$_REQUEST["strIN_SMTP_port"]."',
										`strSMTP_userid` 			= '".$_REQUEST["strIN_SMTP_userid"]."',
										`strSMTP_pwd` 				= '".$_REQUEST["strIN_SMTP_pwd"]."',
										`strDefLang` 				= '".$_REQUEST['strIN_DefLang']."',
										`strSysReplyAddr` 			= '".$_REQUEST["strIN_SysReplyAddr"]."',
										`strMailAddTextDef` 		= '".$_REQUEST["strIN_MailAddTextDef"]."',
										`bDetailSeperateWindow` 	= '".$_REQUEST["bOpenInSeperateWin"]."',
										`bShowPosMail` 				= '".$_REQUEST["bShowPosInMail"]."',
										`bFilter_AR_Wordstart` 		= '".$_REQUEST["bAutoRegWordStart"]."',
										`strEmail_Values` 			= '$mailValues',
										`strEmail_Format` 			= '$mailFormat',
										`nDelay_norm` 				= '".$_REQUEST["nIN_Delay_norm"]."',
										`nDelay_interm` 			= '".$_REQUEST["nIN_Delay_interm"]."',
										`nDelay_late` 				= '".$_REQUEST["nIN_Delay_late"]."',
										`nSubstitutePerson_Hours` 	= '$nMySP_Hours',
										`strSubstitutePerson_Unit` 	= '$strMySP_Unit',
										`strDefSortCol` 			= '$mySort',
										`strCirculation_cols` 		= '$myOrder',
										`strSortDirection`			= '".$_REQUEST["strIN_SortDirection"]."',
										`nShowRows`					= '".$_REQUEST["IN_strShowRows"]."',
										`nAutoReload`				= '".$_REQUEST["strIN_AutoReload"]."',
										`bAllowUnencryptedRequest`	= '".$_REQUEST["IN_UnencryptedRequest"]."',
										`UserDefined1_Title`		= '".$_REQUEST["IN_userdefined1_title"]."',
										`UserDefined2_Title`		= '".$_REQUEST["IN_userdefined2_title"]."',
										`strDateFormat`				= '".$_REQUEST["IN_Date_Format"]."',
										`strMailSendType`			= '".$_REQUEST["mail_send_type"]."',
										`strMtaPath`				= '".$_REQUEST["mta_path"]."',
										`strSlotVisibility` 		= '".$_REQUEST["slot_visibility"]."',
										`strSmtpEncryption`			= '".$_REQUEST["SMTP_encryption"]."',
										`bSendWorkflowMail`			= '".($_REQUEST["IN_SendWorkflowMail"] ? 1 : 0)."',
										`bSendReminderMail`			= '".($_REQUEST["IN_SendReminderMail"] ? 1 : 0)."'
										WHERE `nConfigID` = 1 LIMIT 1 ;";

	mysql_query($strQuery, $nConnection);

	//Get all LDAP Values and write them to the config file
	//$passedArray = $_POST;
	$passedArray =	array('auth_method'		=> $_POST["auth_method"],
						  "ldap_host"		=> $_POST["ldap_host"],
						  "ldap_domain"		=> $_POST["ldap_domain"],
						  "ldap_binddn"		=> $_POST["ldap_binddn"],
						  "ldap_bindpwd"	=> $_POST["ldap_bindpwd"],
						  "ldap_rootdn"		=> $_POST["ldap_rootdn"],
						  "ldap_searchattr"	=> $_POST["ldap_searchattr"],
						  "ldap_fname"		=> $_POST["ldap_fname"],
						  "ldap_lname"		=> $_POST["ldap_lname"],
						  "ldap_uname"		=> $_POST["ldap_uname"],
						  "ldap_email_add"	=> $_POST["ldap_email_add"],
						  "ldap_office"		=> $_POST["ldap_office"],
						  "ldap_phone"		=> $_POST["ldap_phone"],
						  "ldap_context"	=> $_POST["ldap_context"],
						  "default_level"	=> $_POST["default_level"]);
	writeConfigFile($passedArray, "../config/");
?>
</div>

</body>
</html>