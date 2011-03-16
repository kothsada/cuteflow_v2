<?php
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
	include_once ('../pages/CCirculation.inc.php');
	include_once ("../pages/version.inc.php");
    
    $objMyCirculation = new CCirculation();
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
			
			$strQuery = "SELECT * FROM `cf_circulationhistory` WHERE nCirculationFormId=$nCirculationId";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrSendingDateResult = mysql_fetch_array($nResult);
					$strMySendingDate = $arrSendingDateResult["dateSending"];
					$nCurCircHistoryID = $arrSendingDateResult["nID"];
					$strSendingDate = convertDateFromDB($strMySendingDate);							
				}
			}
			
			//-----------------------------------------------
			//--- get current template id
			//-----------------------------------------------
			
			$strQuery = "SELECT nTemplateId FROM `cf_formslot` WHERE nID=$nSlotId";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCurrentTemplateIDResult = mysql_fetch_array($nResult);
					$strCurrentTemplateID = $arrCurrentTemplateIDResult[0];				
				}
			}

			//-----------------------------------------------
			//--- get all formslot names and ids
			//-----------------------------------------------
			
			$strQuery = "SELECT * FROM `cf_formslot` WHERE nTemplateId=$strCurrentTemplateID ORDER BY nSlotNumber ASC";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				while ($row = mysql_fetch_array($nResult,MYSQL_ASSOC))
				{
					$arrCurrentFormSlotID[] = $row["nID"];	
					$arrCurrentFormSlotName[] = $row["strName"];
					$arrCurrentFormSlot[$row["nID"]] = $row["strName"];
					$arrFormSlots [] = $row;
				}
			}
			
			//-----------------------------------------------
			//--- get all field ids
			//-----------------------------------------------
			
			foreach($arrCurrentFormSlotID as $myFSID)
			{
				$strQuery = "SELECT nFieldId FROM `cf_slottofield` WHERE nSlotId=$myFSID GROUP BY nFieldId ASC";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					while ($row = mysql_fetch_array($nResult))
					{
						$arrAllFieldIDs[] = $row["nFieldId"];	
					}
				}
			}			

			//-----------------------------------------------
			//--- get all field names
			//-----------------------------------------------
			
			foreach($arrAllFieldIDs as $myFID)
			{
				$strQuery = "SELECT * FROM `cf_inputfield` WHERE nID=$myFID ORDER BY nID ASC";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					while ($row = mysql_fetch_array($nResult,MYSQL_ASSOC))
					{
						$arrAllFieldNames[] = $row["strName"];	
						$arrFieldIDtoFieldName["$myFID"] = $row["strName"];
						$arrAllInputFields[] = $row;
					}
				}
			}	
			
			//-----------------------------------------------
			//--- get all field values
			//-----------------------------------------------
			
			foreach($arrAllFieldIDs as $myFID)
			{
				$strQuery = "SELECT * FROM `cf_fieldvalue` WHERE nInputFieldId=$myFID ORDER BY nID ASC";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					while ($row = mysql_fetch_array($nResult))
					{
						$arrFieldIDtoFieldValue["$myFID"] = $row["strFieldValue"];	
					}
				}
			}
		}
	}
	
	$nCirculationHistoryId;
	
	$nCounterOut = 0;
	foreach ($arrFormSlots as $arrCurFormSlot)
	{
		$nCurFormSlotID		= $arrCurFormSlot['nID'];
		$strCurFormSlotName	= $arrCurFormSlot['strName'];
		$nCurTemplateID		= $arrCurFormSlot['nTemplateId'];
		$nCurnSlotNumber	= $arrCurFormSlot['nSlotNumber'];
		$nCounter 			= 0;
		
		$strMessage_MIDDLE = $strMessage_MIDDLE."<tr><td colspan=\"3\" height=\"5\" style=\"border-top: 1px solid #999999; background: #aaaaaa;\" align=\"center\">$strCurFormSlotName</td></tr>";
		
		$strQuery = "SELECT * FROM `cf_slottofield` WHERE nSlotId = '$nCurFormSlotID' ORDER BY nPosition ASC";
		$nResult = mysql_query($strQuery, $nConnection);
		if ($nResult)
		{
			while ($row = mysql_fetch_array($nResult,MYSQL_ASSOC))
			{
				$arrAllFieldsOfCurSlot[$nCounterOut][$nCounter]	= $row;
				$nCounter++;
			}
		}
		$arrAllSlottofield = $arrAllFieldsOfCurSlot[$nCounterOut];
		
		foreach($arrAllSlottofield as $arrCurSlottofield)
		{
			$nCurFieldID 	= $arrCurSlottofield['nFieldId'];
			
			$strQuery = "SELECT * FROM `cf_inputfield` WHERE nID = '$nCurFieldID' ORDER BY nID ASC";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				$arrCurInputField = mysql_fetch_array($nResult,MYSQL_ASSOC);
			}
			
			$strQuery = "SELECT * FROM `cf_fieldvalue` WHERE nInputFieldId = '$nCurFieldID' AND nCirculationHistoryId = '$nCirculationHistoryId' AND nSlotId = '$nCurFormSlotID' ORDER BY nInputFieldId ASC";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				$arrCurFieldValue = mysql_fetch_array($nResult,MYSQL_ASSOC);
			}
			
			$strMessage_MIDDLE = $strMessage_MIDDLE.
								"<tr><td width=\"35%\" valign=\"top\" align=\"left\" style=\"border-bottom: 1px solid #aaa;\">".$arrCurInputField['strName']."</td>
								<td width=\"65%\" valign=\"top\" align=\"left\" style=\"border-bottom: 1px solid #aaa;\">";
			
			switch($arrCurInputField['nType'])
			{
				case '1':
					if ($arrCurFieldValue['strFieldValue']!='')					
					{
						$arrValue = split('rrrrr',$arrCurFieldValue['strFieldValue']);
								
						$strValueText 	= $arrValue[0];
						$REG_Text		= $arrValue[1];
						$strMessage_MIDDLE = $strMessage_MIDDLE.$strValueText."</td></tr>";
					}
					else
					{
						$arrValue = split('rrrrr',$arrCurInputField['strStandardValue']);
								
						$strValueText 	= $arrValue[0];
						$REG_Text		= $arrValue[1];
						$strMessage_MIDDLE = $strMessage_MIDDLE.$strValueText."</td></tr>";
					}
					break;	
				case '2':
					if ($arrCurFieldValue['strFieldValue']!='')					
					{
						if ($arrCurFieldValue['strFieldValue']=='on')
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" checked disabled>'."</td></tr>";
						}
						else
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" disabled>'."</td></tr>";
						}
					}
					else
					{
						if ($arrCurInputField['strStandardValue']=='on')
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" checked disabled>'."</td></tr>";
						}
						else
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" disabled>'."</td></tr>";
						}						
					}
					break;	
				case '3':
					if ($arrCurFieldValue['strFieldValue']!='')
					{						
						$arrValue = split('xx',$arrCurFieldValue['strFieldValue']);
								
						$nNumGroup 	= $arrValue[1];
						
						$arrValue1 = split('rrrrr',$arrValue[2]);
						
						$strFieldValue = $arrValue1[0];
					}
					else
					{						
						$arrValue = split('xx',$arrCurInputField['strStandardValue']);
								
						$nNumGroup 	= $arrValue[1];
						
						$arrValue1 = split('rrrrr',$arrValue[2]);
						
						$strFieldValue = $arrValue1[0];
					}
					
					$strMessage_MIDDLE = $strMessage_MIDDLE.$strFieldValue."</td></tr>";
					break;	
				case '4':
					if ($arrCurFieldValue['strFieldValue']!='')
					{						
						$arrValue = split('xx',$arrCurFieldValue['strFieldValue']);
								
						$nDateGroup 	= $arrValue[1];						
						$arrValue2 = split('rrrrr',$arrValue[2]);						
						$strValueDate 	= $arrValue2[0];
						
						$strFieldValue = $arrValue2[0];
					}
					else
					{
						$arrValue = split('xx',$arrCurInputField['strStandardValue']);
								
						$nDateGroup 	= $arrValue[1];						
						$arrValue2 = split('rrrrr',$arrValue[2]);						
						$strValueDate 	= $arrValue2[0];
						
						$strFieldValue = $arrValue2[0];
					}
					$strMessage_MIDDLE = $strMessage_MIDDLE.$strFieldValue."</td></tr>";
					break;	
				case '5':
					if ($arrCurFieldValue['strFieldValue']!='')					{
						
						$strMessage_MIDDLE = $strMessage_MIDDLE.$arrCurFieldValue['strFieldValue']."</td></tr>";
					}
					else
					{
						$strMessage_MIDDLE = $strMessage_MIDDLE.$arrCurInputField['strStandardValue']."</td></tr>";
					}
					break;	
				case '6':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$strValue = $arrCurFieldValue['strFieldValue'];
					}
					else
					{
						$strValue = $arrRow['strStandardValue'];
					}
					
					$arrSplit = split('---',$strValue);
					
					$arrInputFieldValues = $objMyCirculation->getInputFieldValue($arrCurInputField['nID']);
										
					
					$nMax = sizeof($arrInputFieldValues);
					for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
					{
						$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
					
						if ($nCurState) 
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="radio" checked disabled> '.$arrInputFieldValues[$nMyIndex]."<br>";
						}
						else
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="radio" disabled> '.$arrInputFieldValues[$nMyIndex]."<br>";
						}
					}
					
					$strMessage_MIDDLE = $strMessage_MIDDLE."</td></tr>";
					break;
				case '7':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$strValue = $arrCurFieldValue['strFieldValue'];
					}
					else
					{
						$strValue = $arrRow['strStandardValue'];
					}
					
					$arrSplit = split('---',$strValue);
					
					$arrInputFieldValues = $objMyCirculation->getInputFieldValue($arrCurInputField['nID']);
										
					
					$nMax = sizeof($arrInputFieldValues);
					for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
					{
						$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
					
						if ($nCurState) 
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" checked disabled>  '.$arrInputFieldValues[$nMyIndex]."<br>";
						}
						else
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<input type="checkbox" disabled>  '.$arrInputFieldValues[$nMyIndex]."<br>";
						}
					}
					$strMessage_MIDDLE = $strMessage_MIDDLE."</td></tr>";
					break;
				case '8':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$strValue = $arrCurFieldValue['strFieldValue'];
					}
					else
					{
						$strValue = $arrRow['strStandardValue'];
					}
					
					$arrSplit = split('---',$strValue);
					
					$arrInputFieldValues = $objMyCirculation->getInputFieldValue($arrCurInputField['nID']);
										
					$strMessage_MIDDLE = $strMessage_MIDDLE.'<select name="nCombobox" size="1" disabled>';
					$nMax = sizeof($arrInputFieldValues);
					for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
					{
						$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
					
						if ($nCurState) 
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<option selected value="'.$nMyIndex.'">'.$arrInputFieldValues[$nMyIndex].'</option>';
						}
						else
						{
							$strMessage_MIDDLE = $strMessage_MIDDLE.'<option value="'.$nMyIndex.'">'.$arrInputFieldValues[$nMyIndex].'</option>';
						}
					}
					$strMessage_MIDDLE = $strMessage_MIDDLE.'</select></td></tr>';
					break;
				case '9':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$arrValue 		= split('rrrrr',$arrCurFieldValue['strFieldValue']);								
						$arrSplit = split('---',$arrValue[0]);
					}
					else
					{
						$arrValue 		= split('rrrrr',$arrCurInputField['strStandardValue']);								
						$arrSplit = split('---',$arrValue[0]);
					}
					if ($arrSplit[3] != '')
					{
						$nNumberOfUploads 	= $arrSplit[1];
						$strDirectory		= $arrSplit[2].'_'.$nNumberOfUploads;
						$strFilename		= $arrSplit[3];
						$strUploadPath 		= $CUTEFLOW_SERVER.'/upload/';
						$strLink			= $strUploadPath.$strDirectory.'/'.$strFilename;
						
						$strMessage_MIDDLE = $strMessage_MIDDLE."<a href=\"$strLink\" target=\"_blank\">$strFilename</a>";
					}
					$strMessage_MIDDLE = $strMessage_MIDDLE.'</td></tr>';
					break;
			}
		}
		$nCounterOut++;
		$strMessage_MIDDLE = $strMessage_MIDDLE."<tr><td colspan=\"2\" height=\"12\"></td></tr>";
	}


//init vars
$CurLang = $_REQUEST["language"];
$SENDER = $arrSenderDetails[0].", ".$arrSenderDetails[1];
$SENDDATE = $strSendingDate."\n";

$strParams					= 'cpid='.$Circulation_cpid.'&language='.$CurLang;
$strEncyrptedParams			= $objURL->encryptURL($strParams);
$strEncryptedBrowserview	= $CUTEFLOW_SERVER.'/pages/editworkflow_standalone.php?key='.$strEncyrptedParams;

$strMessage_TOP = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<title></title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$DEFAULT_CHARSET\">
	<style>
		body, table, td, tr
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 9pt;
		}
		a
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 9pt;
			text-decoration: none;
		}
		a:hover
		{ text-decoration: underline; }
		.BorderRed
		{ border: 1px solid Red; }
		.BorderGray
		{ border: 1px solid Gray; }
		.FormInput
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 8pt;
			border: 1px solid #B8B8B8;
		}
		.Button
		{
			font-size: 8pt;
			border: 1px solid #C10000;
			color: Black;
			padding: 2px 2px 2px 2px;
		}
		.note
		{
			padding-left : 2px;
			padding-top  : 2px;
			border-width : 1px;
			border-color : #B0B0AF;
			border-style : solid;
			background-color: #FCFBE9;
			font-size: 8pt;
		}
		.table_header
		{
			background-color: #8e8f90; 
			color: #fff; 
			font-size: 12px; 
			font-weight: bold;
		}
		.mandatory
		{ font-weight: bold; }
	</style>
</head>
<body bgcolor=\"#ffffff\">
	<br>
	<br>
	<div align=\"center\">
		
		<table width=\"90%\" style=\"border: 1px solid #c8c8c8; background: #efefef;\" cellspacing=\"0\" cellpadding=\"3\">
			<tr>
				<td colspan=\"2\" align=\"left\" class=\"table_header\" style=\"border-bottom: 3px solid #ffa000;\">
					$MAIL_HEADER_PRE $Circulation_Name
				</td>
			</tr>
			<tr>
				<td colspan=\"2\" align=\"left\" valign=\"top\">
					$Circulation_AdditionalText
				</td>
			</tr>
			<tr>
				<td colspan=\"2\" style=\"border-top:1px solid Gray;\" height=\"10px\">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=\"2\" align=\"left\" valign=\"top\">$CIRCDETAIL_SENDER $SENDER</td>
			</tr>
			<tr>
				<td colspan=\"2\" align=\"left\" valign=\"top\">$CIRCDETAIL_SENDDATE $SENDDATE</td>
			</tr>
			<tr><td><br></td></tr>
			<tr>
				<td  colspan=\"2\" class=\"note\" style=\"background-color:white;\">$MAIL_LINK_DESCRIPTION
				<a href=\"$strEncryptedBrowserview\">$EMAIL_BROWSERVIEW</a>
				</td>
			</tr>
			<tr><td><br></td></tr>
			<tr>
				<td colspan=\"2\" align=\"left\" valign=\"top\">$MAIL_ADDITION_INFORMATIONS</td>
			</tr>
			<tr><td><center>
				<table style=\"background: #fff; border: 1px solid silver; width: 98%;\" cellpadding=\"2\" cellspacing=\"0\">";
									
$strMessage_BOTTOM = "
			</table><br><br>
			</td></tr></table>
			
		<br><br>
		<strong style=\"font-size:8pt;font-weight:normal\">powered by</strong><br>
		<a href=\"http://cuteflow.org\" target=\"_blank\"><img src=\"$CUTEFLOW_SERVER/images/cuteflow_logo_small.png\" border=\"0\" /></a><br>
		<strong style=\"font-size:8pt;font-weight:normal\">Version $CUTEFLOW_VERSION</strong><br>
			
</div>
</body>
</html>";

$strMessage = $strMessage_TOP.$strMessage_MIDDLE.$strMessage_BOTTOM;

?>