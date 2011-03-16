<?php
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
	include_once ("../pages/version.inc.php");
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			//-----------------------------------------------
			//--- get the senders userid
			//-----------------------------------------------
			
			$strQuery = "SELECT nSenderId FROM `cf_circulationform` WHERE nID=$nCirculationId";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrSenderID = mysql_fetch_array($nResult);		
					$nSenderID = $arrSenderID["nSenderId"];
				}
			}
			
			//-----------------------------------------------
			//--- get sender details
			//-----------------------------------------------				
			
			$strQuery = "SELECT strLastName, strFirstName FROM `cf_user` WHERE nID=$nSenderID";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				while ($row = mysql_fetch_array($nResult))
				{
					$arrSenderDetails[] = $row["strLastName"];
					$arrSenderDetails[] = $row["strFirstName"];
				}
			}
			
			//-----------------------------------------------
			//--- get the sending date
			//-----------------------------------------------
			
			$strQuery = "SELECT dateSending FROM `cf_circulationhistory` WHERE nCirculationFormId=$nCirculationId";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrSendingDateResult = mysql_fetch_array($nResult);
					$strMySendingDate = $arrSendingDateResult["dateSending"];
					$strSendingDate = convertDateFromDB($strMySendingDate);							
				}
			}
		}
	}

//init vars
$CurLang = $_REQUEST["language"];
$SENDER = $arrSenderDetails[0].", ".$arrSenderDetails[1];
$SENDDATE = $strSendingDate."\n";

$strParams					= 'cpid='.$Circulation_cpid.'&language='.$CurLang;
$strEncyrptedParams			= $objURL->encryptURL($strParams);
$strEncryptedBrowserview	= $CUTEFLOW_SERVER.'/pages/editworkflow_standalone.php?key='.$strEncyrptedParams;

$strMessage = "
$MAIL_HEADER_PRE $Circulation_Name
$CIRCDETAIL_DESCRIPTION $Circulation_AdditionalText\n
$CIRCDETAIL_SENDER	$SENDER 
$CIRCDETAIL_SENDDATE	$SENDDATE\n\n
$MAIL_LINK_DESCRIPTION
$strEncryptedBrowserview\n
\n\n$strMessage\npowered by\nCuteflow v $CUTEFLOW_VERSION";
?>