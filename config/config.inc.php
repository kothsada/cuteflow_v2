<?php
	@ini_set('display_errors', FALSE);

	//checking if file exists
	$path = '';
	$file = 'config/db_config.inc.php';
	if (file_exists($file))
	{
		$path = '';
	}
	else
	{
		$file = '../config/db_config.inc.php';
		if (file_exists($file))
		{
			$path = '../';
		}
		else
		{
			$file = '../../config/db_config.inc.php';
			if (file_exists($file))
			{
				$path = '../../';
			}
		}
	}
	
	require_once $path.'config/db_config.inc.php';
	require_once $path.'lib/RPL/Encryption/Blowfish.class.php';
	require_once $path.'lib/RPL/Encryption/Url.class.php';
	
	/**
	 * Automatic Class including
	 *
	 * @param String $class
	 */
	function __autoload($class)
	{
		global $path;
		
		$file = str_replace('_', '/', $class).'.php';
		require_once $path.'classes/'.$file;
	}

	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
            $query = "SELECT * from cf_config";
			$nResult = mysql_query($query, $nConnection);
			
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0) 
				{
					$arrConfig = mysql_fetch_array($nResult);				
				}
			}
			
			//--- server settings
			$CUTEFLOW_SERVER 		= $arrConfig["strCF_Server"];
			$SMTP_USE_AUTH 			= $arrConfig["strSMTP_use_auth"];		//--- "y" for using smtp authentification otherwise ""
			$SMTP_SERVER 			= $arrConfig["strSMTP_server"];
			$SMTP_PORT 				= $arrConfig["strSMTP_port"];
			$SMTP_USERID 			= $arrConfig["strSMTP_userid"];
			$SMTP_PWD 				= $arrConfig["strSMTP_pwd"];
			$SHOWROWS 				= $arrConfig["nShowRows"];
			$AUTO_RELOAD_SEC 		= $arrConfig["nAutoReload"];
			$URL_ENCODING_PASSWORD 	= $arrConfig["strUrlPassword"];
			$URL_ENCODING_TS 		= $arrConfig["tsLastUpdate"];
			$ARR_COL_SPLIT 			= split('---', $arrConfig['strCirculation_cols']);
			
			
			$objURL = new RPL_Encryption_Url();
			$objURL->setPassword($URL_ENCODING_PASSWORD);
			$objURL->checkBoxes($path.'lib/RPL/Encryption/boxes.js', $URL_ENCODING_TS);
						
			if ($_REQUEST['key'] != '')
			{
				$objURL->setParams($_REQUEST['key']);
			}
			
			
			//--- Substitute person
			$SUBTITUTE_PERSON_UNIT 	= $arrConfig['strSubstitutePerson_Unit'];
			$SUBTITUTE_PERSON_VALUE	= $arrConfig['nSubstitutePerson_Hours'];
			switch($arrConfig['strSubstitutePerson_Unit'])
			{
				case 'DAYS':
					$SUBSTITUTE_PERSON_MINUTES = $arrConfig['nSubstitutePerson_Hours'] * 24 * 60;
					break;
				case 'HOURS':
					$SUBSTITUTE_PERSON_MINUTES = $arrConfig['nSubstitutePerson_Hours'] * 60;
					break;
				case 'MINUTES':
					$SUBSTITUTE_PERSON_MINUTES = $arrConfig['nSubstitutePerson_Hours'];
					break;
			}
			
			
			$arrColsSplit = split('---', $arrConfig['strCirculation_cols']);
			
			$nColsRunningNumber = 0;
			$nAllColsRunningNumber = 0;
			$nColsMax = sizeof($arrColsSplit);
			for ($nColsIndex = 0; $nColsIndex < $nColsMax; $nColsIndex++)
			{
				$strCurCol 	= $arrColsSplit[$nColsIndex];
				$nColsIndex = $nColsIndex + 1;
				$bActive 	= $arrColsSplit[$nColsIndex];
				
				switch($strCurCol)
				{
					case 'NAME':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']		= 'COL_CIRCULATION_NAME';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_NAME;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_NAME';
						}
						break;
					case 'STATION':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_STATION';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_STATION;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_STATION';
						}
						break;
					case 'DAYS':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_PROCESS_DAYS';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_DAYS;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_PROCESS_DAYS';
						}
						break;
					case 'START':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_PROCESS_START';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_START;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_PROCESS_START';
						}
						break;
					case 'SENDER':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_SENDER';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_SENDER;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_SENDER';
						}
						break;
					case 'MAILLIST':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_MAILLIST';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_MAILLIST;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_MAILLIST';
						}
						break;
					case 'TEMPLATE':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_TEMPLATE';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_TEMPLATE;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_TEMPLATE';
						}
						break;
					case 'WHOLETIME':
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_WHOLETIME';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_WHOLETIME;
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= 'COL_CIRCULATION_WHOLETIME';
						}
						break;
					default:
						$parts = explode('/', $strCurCol);
						
						$field_name = $parts[0] == 'F1' ? 'COL_CIRCULATION_FIELD1' : 'COL_CIRCULATION_FIELD2';
						$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= $field_name;
						$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $parts[1];
						if ($bActive)
						{
							$arrCirculation_Cols[] 		= $field_name;
						}
						
						break;
				}
				if ($bActive)
				{				
					$nColsRunningNumber++;
				}
				$arrCONF_AllCols[$nAllColsRunningNumber]['bActive']	= $bActive;
				$nAllColsRunningNumber++;
			}
			
			$TStoday =  mktime(date("H"),date("i"),date("s"),date("m"), date("d"), date("Y"));
			$TSsendSP = mktime(date("H"),date("i")-$SUBSTITUTE_PERSON_MINUTES,date("s"),date("m"), date("d"), date("Y"));
			
			//--- mail informations
			$SYSTEM_REPLY_ADDRESS = $arrConfig["strSysReplyAddr"];
			$MAIL_ADDTIONALTEXT_DEFAULT = $arrConfig["strMailAddTextDef"];
			
			//--- gui settings
			$DEFAULT_LANGUAGE				 = $arrConfig["strDefLang"];
			$OPEN_DETAILS_IN_SEPERATE_WINDOW = $arrConfig["bDetailSeperateWindow"];
			$DEFAULT_SORT_COL 				= $arrConfig["strDefSortCol"];
			$SHOW_POSITION_IN_MAIL 			= $arrConfig["bShowPosMail"];
			$FILTER_AUTO_REGEX_WORDSTART 	= $arrConfig["bFilter_AR_Wordstart"];
			$EMAIL_FORMAT 					= $arrConfig["strEmail_Format"];
			$EMAIL_VALUES 					= $arrConfig["strEmail_Values"];
			$CIRCULATION_COLUMNS 			= $arrCirculation_Cols;
			$SORTDIRECTION					= $arrConfig["strSortDirection"];
			$ALLOW_UNENCRYPTED_REQUEST		= $arrConfig['bAllowUnencryptedRequest'];
			$USERDEFINED_TITLE1				= $arrConfig['UserDefined1_Title'];
			$USERDEFINED_TITLE2				= $arrConfig['UserDefined2_Title'];
			$DATE_FORMAT					= $arrConfig['strDateFormat'];
						
			$SEND_WORKFLOW_MAIL				= $arrConfig['bSendWorkflowMail'];
			$SEND_REMINDER_MAIL				= $arrConfig['bSendReminderMail'];
			
			//--- days of delay
		   	$DELAY_NORMAL = $arrConfig["nDelay_norm"];
		   	$DELAY_INDERMIDIATE = $arrConfig["nDelay_interm"];
		   	$DELAY_LATE = $arrConfig["nDelay_late"];
		   	
		   	$SLOT_VISIBILITY = $arrConfig['strSlotVisibility'];
		   	
		   	$MAIL_SEND_TYPE = $arrConfig['strMailSendType'];
		   	$MTA_PATH = $arrConfig['strMtaPath'];
		   	
		   	$SMPT_ENCRYPTION = $arrConfig['strSmtpEncryption'];
		   	
		   	//$CUTEFLOW_VERSION = $arrConfig["strVersion"];

		   	// User Timeout = 10 minutes
			$USER_TIMEOUT = 60*10;
			
			if ($_SESSION['SESSION_CUTEFLOW_USERID'] != '')
			{	// user is logged in - let's update the user's last action
				$strQuery 	= "UPDATE cf_user SET tsLastAction = '".time()."' WHERE nID = '".$_SESSION['SESSION_CUTEFLOW_USERID']."' LIMIT 1;";
				$nResult 	= mysql_query($strQuery, $nConnection) or die(mysql_error());
			}
		}
	}
?>