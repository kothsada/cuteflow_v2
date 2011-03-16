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
	
	$FIELDS = "\n$MAIL_ADDITION_INFORMATIONS\n\n\n";
	
	$nCounterOut = 0;
	foreach ($arrFormSlots as $arrCurFormSlot)
	{
		$nCurFormSlotID		= $arrCurFormSlot['nID'];
		$strCurFormSlotName	= $arrCurFormSlot['strName'];
		$nCurTemplateID		= $arrCurFormSlot['nTemplateId'];
		$nCurnSlotNumber	= $arrCurFormSlot['nSlotNumber'];
		$nCounter 			= 0;
		
		$FIELDS = $FIELDS.'Slot: "'.$strCurFormSlotName."\"\n".'-------------------------'."\n";
		
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
			
			$FIELDS = $FIELDS.$arrCurInputField['strName'].": ";
			
			switch($arrCurInputField['nType'])
			{
				case '1':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$arrValue = split('rrrrr',$arrCurFieldValue['strFieldValue']);						
						$FIELDS = $FIELDS.$arrValue[0]."\n";
					}
					else
					{
						$arrValue = split('rrrrr',$arrCurInputField['strStandardValue']);
						$FIELDS = $FIELDS.$arrValue[0]."\n";
					}
					break;	
				case '2':
					if ($arrCurFieldValue['strFieldValue']!='')					
					{
						if ($arrCurFieldValue['strFieldValue']=='on')
						{
							$FIELDS = $FIELDS.'[X]'."\n";
						}
						else
						{
							$FIELDS = $FIELDS.'[ ]'."\n";
						}	
					}
					else
					{
						if ($arrCurInputField['strStandardValue']=='on')
						{
							$FIELDS = $FIELDS.'[X]'."\n";
						}
						else
						{
							$FIELDS = $FIELDS.'[ ]'."\n";
						}						
					}
					break;	
				case '3':
					if ($arrCurFieldValue['strFieldValue']!='')
					{						
						$arrValue = split('xx',$arrCurFieldValue['strFieldValue']);
						$arrValue1 = split('rrrrr',$arrValue[2]);
						$strFieldValue	= $arrValue1[0];
					}
					else
					{
						$arrValue = split('xx',$arrCurInputField['strStandardValue']);
						$arrValue1 = split('rrrrr',$arrValue[2]);
						$strFieldValue	= $arrValue1[0];
					}					
					$FIELDS = $FIELDS.$strFieldValue."\n";
					break;	
				case '4':
					if ($arrCurFieldValue['strFieldValue']!='')
					{						
						$strValue = $arrCurFieldValue['strFieldValue'];
						$arrValue = split('xx',$strValue);
						$arrValue2 = split('rrrrr',$arrValue[2]);
						$strFieldValue 	= $arrValue2[0];
					}
					else
					{
						$strValue = $arrCurInputField['strStandardValue'];
						$arrValue = split('xx',$strValue);
						$arrValue2 = split('rrrrr',$arrValue[2]);
						
						$strFieldValue 	= $arrValue2[0];
					}
					$FIELDS = $FIELDS.$strFieldValue."\n";
					break;	
				case '5':
					if ($arrCurFieldValue['strFieldValue']!='')
					{
						$FIELDS = $FIELDS."\n".$arrCurFieldValue['strFieldValue']."\n\n";
					}
					else
					{
						$FIELDS = $FIELDS."\n".$arrCurInputField['strStandardValue']."\n\n";
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
							$FIELDS = $FIELDS."\n".'(X) '.$arrInputFieldValues[$nMyIndex];
						}
						else
						{ 
							$FIELDS = $FIELDS."\n".'( ) '.$arrInputFieldValues[$nMyIndex];
						}
					}
					
					$FIELDS = $FIELDS."\n\n";
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
							$FIELDS = $FIELDS."\n".'[X] '.$arrInputFieldValues[$nMyIndex];
						}
						else
						{ 
							$FIELDS = $FIELDS."\n".'[ ] '.$arrInputFieldValues[$nMyIndex];
						}
					}
					
					$FIELDS = $FIELDS."\n\n";			
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
										
					
					$nMax = sizeof($arrInputFieldValues);
					for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
					{
						$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
					
						if ($nCurState) 
						{ 
							$strMyValue = $arrInputFieldValues[$nMyIndex];
						}
					}
					$FIELDS = $FIELDS.' ['.$strMyValue.']'."\n";
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
						
						$FIELDS = $FIELDS.$strLink;
					}
					$FIELDS = $FIELDS."\n";
					break;
			}
		}		
		$nCounterOut++;
		$FIELDS = $FIELDS.'-------------------------'."\n\n\n";
	}


//init vars
$CurLang = $_REQUEST["language"];
$SENDER = $arrSenderDetails[0].", ".$arrSenderDetails[1];
$SENDDATE = $strSendingDate."\n";

$strParams					= 'cpid='.$Circulation_cpid.'&language='.$CurLang;
$strEncyrptedParams			= $objURL->encryptURL($strParams);
$strEncryptedBrowserview	= $CUTEFLOW_SERVER.'/pages/editworkflow_standalone.php?key='.$strEncyrptedParams;

$strMailTop ="
$MAIL_HEADER_PRE $Circulation_Name
$CIRCDETAIL_DESCRIPTION $Circulation_AdditionalText\n
$CIRCDETAIL_SENDER	$SENDER 
$CIRCDETAIL_SENDDATE	$SENDDATE\n
$MAIL_LINK_DESCRIPTION
$strEncryptedBrowserview\n\n";
							
$strMailBottom = "\n$strMessage\npowered by\nCuteflow v $CUTEFLOW_VERSION";

$strMessage = $strMailTop.$FIELDS.$strMailBottom;
?>