<?php

class CCirculation
{
	function CCirculation()
	{
		
	}
	
	function getRadioGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter)
	{
		$strBuffer = '';
		
		$arrSplit = split('---',$strValue);
		
		$arrInputFieldValues = $this->getInputFieldValue($nInputfieldID);
		
		$nMax = sizeof($arrInputFieldValues);
		
		for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
		{
			$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
			
			$strBuffer = $strBuffer.'<input type="radio" name="'.$keyId.'_nRadiogroup_'.$nRunningCounter.'" value="'.$nMyIndex.'"';
			if(!$bIsEnabled) 
			{ 
				$strBuffer = $strBuffer.' disabled';
			}
			if ($nCurState) 
			{
				$strBuffer = $strBuffer.' checked';
			}
			
			$strBuffer = $strBuffer.'>'.$arrInputFieldValues[$nMyIndex].'<br>';
			if ($bIsEnabled) 
			{ // slot is allowed to edit
				$strBuffer = $strBuffer.'<input type="hidden" name="RBName_'.$keyId.'_nRadiogroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$arrInputFieldValues[$nMyIndex].'">'; 
			}
		}
		return $strBuffer;
	}
	
	function getCheckBoxGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter)
	{
		$strBuffer = '';
		
		$arrSplit = split('---',$strValue);
		
		$arrInputFieldValues = $this->getInputFieldValue($nInputfieldID);
		
		$nMax = sizeof($arrInputFieldValues);
		for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
		{
			$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'

			$strBuffer = $strBuffer.'<input type="checkbox" name="'.$keyId.'_nCheckboxGroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="1"';
			if(!$bIsEnabled) 
			{ // slot is allowed to edit
				$strBuffer = $strBuffer.' disabled'; 
			}
			if ($nCurState) 
			{ 
				$strBuffer = $strBuffer.' checked'; 
			}
			
			$strBuffer = $strBuffer.'>'.$arrInputFieldValues[$nMyIndex].'<br>';
			if ($bIsEnabled) 
			{ // slot is allowed to edit
				$strBuffer = $strBuffer.'<input type="hidden" name="CBName_'.$keyId.'_nCheckboxGroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$arrInputFieldValues[$nMyIndex].'">';
			}
		}
		
		return $strBuffer;
	}
	
	function getComboBoxGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter)
	{
		$strBuffer = '';
		
		$arrSplit = split('---',$strValue);
		
		$arrInputFieldValues = $this->getInputFieldValue($nInputfieldID);
		
		
		$strBuffer = $strBuffer.'<select name="'.$keyId.'_nComboboxV_'.$nRunningCounter.'" id="'.$keyId.'_nComboboxV_'.$nRunningCounter.'" size="1"';
		if(!$bIsEnabled) 
		{ 
			$strBuffer = $strBuffer.' disabled'; 
		}		
		$strBuffer = $strBuffer.'>';
		
		$nMax = sizeof($arrInputFieldValues);
		for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
		{
			$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'

			if ($nCurState)
			{
				$strBuffer = $strBuffer.'<option value="'.$nMyIndex.'" selected>'.$arrInputFieldValues[$nMyIndex].'</option>';
			}
			else
			{
				$strBuffer = $strBuffer.'<option value="'.$nMyIndex.'">'.$arrInputFieldValues[$nMyIndex].'</option>';
			}
		}
		$strBuffer .= '</select>';
		
		if ($bIsEnabled) 
		{ // slot is allowed to edit
			for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
			{
				$nCurState 	= $arrSplit[$nMyIndex];		// state of Radiobutton either '0' or '1'
				
				$strBuffer = $strBuffer.'<input type="hidden" name="COMBOName_'.$keyId.'_nCombobox_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$arrInputFieldValues[$nMyIndex].'">';			
			}
		}
		
		return $strBuffer;
	}
	
	function getInputFieldValue($nInputfieldID)
	{
		$strQuery 	= "SELECT strStandardValue FROM cf_inputfield WHERE nID = '$nInputfieldID' LIMIT 1";
		$nResult 	= @mysql_query($strQuery);
		
		$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC);
				
		$arrSplit = split('---',$arrRow['strStandardValue']);
		
		$nMax = $arrSplit[1]+1;
		
		for ($nMyIndex = 1; $nMyIndex < $nMax; $nMyIndex++)
		{
			$splitIndex = $nMyIndex + $nMyIndex;
			$retArrIndex = $nMyIndex - 1;
			
			$arrInputFieldValues[$retArrIndex] = $arrSplit[$splitIndex];
		}
		return $arrInputFieldValues;
	}
	
	
	/**
	**	@param $nCirculationFormID
	*	@return the circulationhistory ID
	*/
	function getMaxCirculationHistoryID($nCirculationFormID)
	{
		$strQuery 	= "SELECT MAX(nID) FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID'";
		$nResult 	= @mysql_query($strQuery);
		
		$arrRow = mysql_fetch_array($nResult);
		return $arrRow[0]; // returns the circulationhistory ID		
	}
	
	/**
	*	@param $nCirculationFormID
	*	@return the circulationForm
	*/
	function getCirculationForm($nCirculationFormID)
	{
		$strQuery 	= "SELECT * FROM cf_circulationform WHERE nID = '$nCirculationFormID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
		
		return mysql_fetch_array($nResult, MYSQL_ASSOC);
	}
	
	/**
	*	adds a new Circulation Form
	*	@param $strCirculationName, $nMailinglistID, $nSenderID, $SuccessMail, $SuccessArchive
	*/
	function addCirculationForm($strCirculationName, $nMailinglistID, $nSenderID, $SuccessMail, $SuccessArchive, $SuccessDelete, $bAnonymize)
	{
		global $_REQUEST;
		
		$dateSending = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		
		$nEndAction = 0;
		
		if ($SuccessMail != 0) $nEndAction += $SuccessMail;
		if ($SuccessArchive == 'on') $nEndAction += 2;
		if ($SuccessDelete == 'on') $nEndAction += 4;
		
		// check the hook CF_ENDACTION
			$endActions	= $this->getExtensionsByHookId('CF_ENDACTION');
			if ($endActions)
			{
				foreach ($endActions as $endAction)
				{
					$params = $this->getEndActionParams($endAction);
					
					if ($_REQUEST[$params['checkboxName']] == 'on') $nEndAction += $params['hookValue'];
				}
			}
		
		
		$strQuery 	= "INSERT INTO cf_circulationform values (null, '$nSenderID', '$strCirculationName', '$nMailinglistID', 0, '$nEndAction', 0, $bAnonymize)";
		$nResult 	= mysql_query($strQuery) or die(mysql_error()."- $strQuery");
		
		$strQuery 	= "SELECT MAX(nID) FROM cf_circulationform WHERE bDeleted = 0";
		$nResult 	= @mysql_query($strQuery);
		
		$arrRow 	= @mysql_fetch_array($nResult);
		
		return $arrRow[0]; //returns the circulationform ID
	}
	
	/**
	*	adds a new Circulation History
	*	@param $nCirculationFormID, $strAdditionalText
	*/
	function addCirculationHistory($nCirculationFormID, $strAdditionalText)
	{	
		$dateSending = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		
		$strQuery 	= "INSERT INTO cf_circulationhistory VALUES(null, 1, '$dateSending', '$strAdditionalText', $nCirculationFormID)";
		$nResult 	= @mysql_query($strQuery);
		
		$strQuery 	= "SELECT MAX(nID) FROM cf_circulationhistory";
		$nResult 	= @mysql_query($strQuery);
		
		$arrRow = mysql_fetch_array($nResult);
		return $arrRow[0]; // returns the circulationhistory ID		
	}
	
	/**
	*	@param $nCirculationProcessID
	*	@return the circulationhistory ID
	*/
	function getCirculationHistoryID($nCirculationProcessID)
	{
		$strQuery 	= "SELECT nCirculationHistoryId FROM cf_circulationprocess WHERE nID = '$nCirculationProcessID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
		
		$arrResult = mysql_fetch_array($nResult, MYSQL_ASSOC);
		
		return $arrResult['nCirculationHistoryId'];
	}
	
	/**
	*	@param $nCirculationFormID, $nCirculationHistoryID
	*	@return the circulation process informations
	*/
	function getCirculationProcess($nCirculationFormID, $nCirculationHistoryID)
	{
		$strQuery 	= "	SELECT *
						FROM cf_circulationprocess
						WHERE nCirculationFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID' AND nIsSubstitiuteOf = '0'
						ORDER BY dateInProcessSince ASC;";
		$nResult 	= mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	/**
	*	@param $nCirculationFormID, $nCirculationHistoryID, $tsMyDateInProcessSince
	*/
	function getLaterEntries($nCirculationFormID, $nCirculationHistoryID, $tsMyDateInProcessSince)	
	{
		$strQuery 	= "	SELECT *
						FROM cf_circulationprocess
						WHERE nCirculationFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID' AND dateInProcessSince > '$tsMyDateInProcessSince'
						ORDER BY dateInProcessSince ASC;";
		$nResult 	= mysql_query($strQuery) or die(mysql_error());
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	/**
	*	sets the current station
	*	@param $nMyCirculationProcessID
	*/
	function setCurrentStation($nMyCirculationProcessID)
	{
		$strQuery = "UPDATE `cf_circulationprocess` SET	`dateInProcessSince`	= '".time()."',
														`nDecissionState` 		= '0',
														`dateDecission` 		= '0'
														WHERE `nID` = '$nMyCirculationProcessID' LIMIT 1 ;";
		$result		= mysql_query($strQuery) or die (mysql_error());
	}
	
	/**
	*	sets the current station state to skipped
	*	@param $nCURCirculationProcessID
	*/
	function setStationToSkipped($nCURCirculationProcessID)
	{
		$strQuery = "UPDATE `cf_circulationprocess` SET	`nDecissionState` 		= '4',
														`dateDecission` 		= '".(time()-60)."'
														WHERE `nID` = '$nCURCirculationProcessID' LIMIT 1 ;";
		$result		= mysql_query($strQuery) or die (mysql_error());
	}
	
	/**
	*	adds a new Circulation Process
	*	@param $nCirculationFormID, $nCirculationHistoryID, $nCurSlotID, $nCurUserID, $tsDateInProcessSince, $tsDateDecission
	*/
	function addCirculationProcess($nCirculationFormID, $nCirculationHistoryID, $nCurSlotID, $nCurUserID, $tsDateInProcessSince, $tsDateDecission)
	{
		$strQuery 	= "INSERT INTO `cf_circulationprocess` ( `nID` , `nCirculationFormId` , `nSlotId`, `nUserId` , `dateInProcessSince` , `nDecissionState`, `dateDecission` , `nIsSubstitiuteOf` , `nCirculationHistoryId`)
									VALUES ( NULL , '$nCirculationFormID' , '$nCurSlotID', '$nCurUserID', '$tsDateInProcessSince', '4', '$tsDateDecission', '0', '$nCirculationHistoryID');";
		$result		= mysql_query($strQuery) or die (mysql_error());
	}
	
	/**
	*	adds a new Circulation Process
	*	@param $nCirculationFormID, $nCirculationHistoryID, $nCurSlotID, $nCurUserID, $tsDateInProcessSince
	*/
	function addCurCirculationProcess($nCirculationFormID, $nCirculationHistoryID, $nCurSlotID, $nCurUserID, $tsDateInProcessSince)
	{
		$strQuery 	= "INSERT INTO `cf_circulationprocess` ( `nID` , `nCirculationFormId` , `nSlotId`, `nUserId` , `dateInProcessSince` , `nDecissionState`, `dateDecission` , `nIsSubstitiuteOf` , `nCirculationHistoryId`)
									VALUES ( NULL , '$nCirculationFormID' , '$nCurSlotID', '$nCurUserID', '$tsDateInProcessSince', '0', '0', '0', '$nCirculationHistoryID');";
		$result		= mysql_query($strQuery) or die (mysql_error());
	}
	
	/**
	*	@param $nCirculationProcessID
	*	@return the circulation process informations
	*/
	function getMyCirculationProcess($nCirculationProcessID)
	{
		$strQuery 	= "SELECT * FROM cf_circulationprocess WHERE nID = '$nCirculationProcessID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);

		return mysql_fetch_array($nResult, MYSQL_ASSOC);
	}
	
	/**
	*	@param $nDELCirculationProcessID
	*/
	function deleteMyCirculationProcess($nDELCirculationProcessID)
	{
		$strQuery 	= "DELETE FROM cf_circulationprocess WHERE nID = '$nDELCirculationProcessID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
	}
	
	/**
	*	@param $nCirculationHistoryID
	*	@return the circulation history informations
	*/
	function getCirculationHistory($nCirculationHistoryID)
	{
		$strQuery 	= "SELECT * FROM cf_circulationhistory WHERE nID = '$nCirculationHistoryID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);

		return mysql_fetch_array($nResult, MYSQL_ASSOC);
	}
	
	/**
	*	@return the input fields
	*/
	function getAllInputFields()
	{
		$strQuery 	= "SELECT * FROM cf_inputfield ORDER BY strName;";
		$nResult 	= @mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	/**
	*	@return the input fields
	*/
	function getMyInputFields()
	{
		$strQuery 	= "SELECT * FROM cf_inputfield WHERE nType <> '4' AND nType <> '9' AND nType <> '7' ORDER BY strName;";
		$nResult 	= @mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	/**
	*	@param $nCirculationFormID, $nCirculationHistoryID
	*	@return the field values
	*/
	function getFieldValues($nCirculationFormID, $nCirculationHistoryID)
	{
        $strQuery 	= "SELECT * FROM cf_fieldvalue WHERE nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'";
		$nResult 	= @mysql_query($strQuery);
		
		while (	$arrRow = mysql_fetch_array($nResult))
		{
			$arrValues[$arrRow['nInputFieldId'].'_'.$arrRow['nSlotId'].'_'.$arrRow['nFormId']] = $arrRow;
		}
		
		return $arrValues;
	}
	
	/**
	*	@param $nCirculationFormID, $nCirculationHistoryID, $nInputFieldID
	*	@return the field values
	*/
	function getMyFieldValue($nCirculationFormID, $nCirculationHistoryID, $nInputFieldID)
	{
        $strQuery 	= "SELECT * FROM cf_fieldvalue WHERE nInputFieldId = '$nInputFieldID' AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'";
		
        $nResult 	= mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		
		$strFieldValue 	= $arrRows[0]['strFieldValue'];
		$nFieldType 	= $this->getFieldType($nInputFieldID);
		
		if ($nFieldType == 1)
		{
			$arrValue = split('rrrrr',$strFieldValue);
			return  $arrValue[0];
		}
		else if ($nFieldType == 2)
		{
			if ($strFieldValue != "on")
			{
				return 0;
			}
			else
			{
				return 1;
			}
		}
		else if ($nFieldType == 3)
		{
			$arrValue = split('xx',$strFieldValue);								
			$nNumGroup 	= $arrValue[1];														
			$arrValue1 = split('rrrrr',$arrValue[2]);														
			$strMyValue	= $arrValue1[0];

			return $strMyValue;
		}
		else if ($nFieldType == 5)
		{
			return  $strFieldValue;
		}
		else if ($nFieldType == 6)
		{
			$arrRBGroup = '';
			$arrGroup = '';
			
			$arrSplit = split('---',$strFieldValue);
			
			$arrRBGroup[]	= $arrSplit['2'];
			$arrGroup[]		= $arrSplit['3'];
			$arrRBGroup[]	= $arrSplit['4'];
			$arrGroup[]		= $arrSplit['5'];
			$arrRBGroup[]	= $arrSplit['6'];
			$arrGroup[]		= $arrSplit['7'];
			$arrRBGroup[]	= $arrSplit['8'];
			$arrGroup[]		= $arrSplit['9'];
			$arrRBGroup[]	= $arrSplit['10'];
			$arrGroup[]		= $arrSplit['11'];
			$arrRBGroup[]	= $arrSplit['12'];
			$arrGroup[]		= $arrSplit['13'];
																
			for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
			{
				$CurStrRBGroup 	= $arrRBGroup[$nMyIndex];	// content of corresponding Radiobutton
				$CurRBGroup 	= $arrGroup[$nMyIndex];		// state of Radiobutton either '0' or '1'
				
				if ($CurRBGroup)
				{ 
					return $CurStrRBGroup;
				}														
			}	
		}
		elseif($nFieldType == 8)
		{
			$arrComboGroup = '';
			$arrGroup = '';
			
			$arrSplit = split('---',$strFieldValue);
																
			$arrComboGroup[]	= $arrSplit['2'];
			$arrGroup[]			= $arrSplit['3'];
			$arrComboGroup[]	= $arrSplit['4'];
			$arrGroup[]			= $arrSplit['5'];
			$arrComboGroup[]	= $arrSplit['6'];
			$arrGroup[]			= $arrSplit['7'];
			$arrComboGroup[]	= $arrSplit['8'];
			$arrGroup[]			= $arrSplit['9'];
			$arrComboGroup[]	= $arrSplit['10'];
			$arrGroup[]			= $arrSplit['11'];
			$arrComboGroup[]	= $arrSplit['12'];
			$arrGroup[]			= $arrSplit['13'];
									
			for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
			{
				$CurStrCBGroup 	= $arrComboGroup[$nMyIndex];	
				$CurCBGroup 	= $arrGroup[$nMyIndex];
				
				if ($CurCBGroup)
				{
					return $CurStrCBGroup;
				}
			}
		}
	}
	
	function getMyFilters($nID)
	{
		$strQuery 	= "SELECT * FROM cf_filter WHERE nUserID = '$nID'";
		$nResult 	= @mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}		
		return $arrRows;
	}
	
	function getFilter($nID)
	{
		$strQuery 	= "SELECT * FROM cf_filter WHERE nID = '$nID' LIMIT 1";
		$nResult 	= @mysql_query($strQuery);
		
		return mysql_fetch_array($nResult, MYSQL_ASSOC);
	}
	
	/**
	*	@param $nInputFieldID
	*	@return the field type
	*/
	function getFieldType($nInputFieldID)
	{
		$strQuery 	= "SELECT nType FROM cf_inputfield WHERE nID = '$nInputFieldID'";
		$nResult 	= @mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		
		return $arrRows[0]['nType'];
	}
	
	/**
	*	@return the Users
	*/
	function getUsers()
	{
		$strQuery = "SELECT * FROM cf_user  WHERE bDeleted <> 1 ORDER BY strUserId;";
		$nResult = @mysql_query($strQuery);
		if ($nResult)
		{
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrUsers[$arrRow["nID"]] = $arrRow;						
			}
		}
		
		return $arrUsers;
	}
	
	/**
	*	@param $nUserID
	*	@return the username
	*/
	function getUsername($nUserID)
	{
		$strQuery = "SELECT * FROM cf_user WHERE nID = '$nUserID' LIMIT 1;";
		$nResult = @mysql_query($strQuery);
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows[0]['strUserId']." (".$arrRows[0]['strLastName'].", ". $arrRows[0]['strFirstName'].")";
	}
		
	/**
	*	@return the Users
	*/
	function getAllUsers($include_deleted = true)
	{
		if ($include_deleted) {
			$strQuery = "SELECT * FROM cf_user ORDER BY strUserId ASC;";	
		}
		else {
			$strQuery = "SELECT * FROM cf_user WHERE bDeleted=0 ORDER BY strUserId ASC;";
		}
		
		$nResult = @mysql_query($strQuery);
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$arrRow['nID']] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	function getAllTemplates()
	{
		$strQuery = "SELECT * FROM cf_formtemplate WHERE bDeleted=0 ORDER BY strName ASC;";
		$nResult = @mysql_query($strQuery);
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	function getTemplate($nID)
	{
		$strQuery 	= "SELECT * FROM cf_formtemplate WHERE nID = '$nID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
		
		$arrResult	= mysql_fetch_array($nResult, MYSQL_ASSOC);
		return $arrResult;
	}
	
	function getWholeTime($nCirculationFormID)
	{
		$strQuery 	= "SELECT dateSending FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
		
		$arrRow		= mysql_fetch_array($nResult, MYSQL_ASSOC);
		
		$tsDateSending 	= $arrRow['dateSending'];
		$tsNow			= time();
		
		$secDif = $tsNow - $tsDateSending;
		
		if ($secDif > 86400)
		{
			$daysDif = (int) ($secDif / (60 * 60 * 24));
			return $daysDif;
		}
		else
		{
			return 0;
		}		
	}
	
	/**
	*	@param $nID
	*	@return the mailinglist
	*/
	function getMailinglist($nID)
	{
		$strQuery 	= "SELECT * FROM cf_mailinglist WHERE nID = '$nID' LIMIT 1;";
		$nResult 	= @mysql_query($strQuery);
		
		return mysql_fetch_array($nResult, MYSQL_ASSOC);
	}
	
	/**
	*	@return the mailinglists
	*/
	function getAllMailingLists()
	{
		$strQuery 	= "SELECT * FROM cf_mailinglist WHERE bIsEdited = '0' ORDER BY strName ASC;";
		$nResult 	= @mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;
				$nIndex++;						
			}
		}
		return $arrRows;
	}
	
	/**
	*	@param $nTemplateID
	*	@return the formslots
	*/
	function getFormslots($nTemplateID)
	{
		$strQuery = "SELECT * FROM cf_formslot WHERE nTemplateID = '$nTemplateID'  ORDER BY nSlotNumber ASC";
		$nResult = mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult))
			{
				$arrSlots[$nIndex] = $arrRow;
				
				$nIndex++;
			}
		}
		
		return $arrSlots;
	}
	
	/**
	*	@param $start, $sortby, $sortDirection, $archivemode, $nShowRows, $bFilterOn
	*	@return the Circulation Overview
	*/
	function getCirculationOverview($start, $sortby, $sortDirection, $archivemode, $nShowRows, $bFilterOn, $FILTER_Name = '', $FILTER_Sender = false, $FILTER_Mailinglist = false, $FILTER_Station = false, $FILTER_DAYS_IN_PROGRESS_START = '', $FILTER_DAYS_IN_PROGRESS_END = '', $FILTER_DATE_START = '', $FILTER_DATE_END = '', $FILTER_TEMPLATE = false, $FILTER_CUSTOM = false)
	{
		global $_REQUEST;
		
		if ($FILTER_CUSTOM)
		{
			$strFilterCustomQuery = "";
			
			
			// only filter the first entry
			// TODO
			$arrFilter = $FILTER_CUSTOM[0];
				
			$nInputfieldId 	= $arrFilter['nInputFieldID'];
			$strOperator 	= $arrFilter['strOperator'];
			$strValue		= $arrFilter['strValue'];
			
			if (($nInputfieldId != '') && ($strOperator != '') && ($strValue != ''))
			{
				$strFilterCustomQuery .= " AND nInputFieldId = '$nInputfieldId' AND strFieldValue LIKE '%$strValue%'";
			}
		}
		
		if (($FILTER_DATE_START != '') || ($FILTER_DATE_END != ''))
		{
			$FILTER_DATE = 1;
			$arrDateStart 	= explode(".", $FILTER_DATE_START);
			$arrDateEnd 	= explode(".", $FILTER_DATE_END);
			
			$tsDateMin  	= @mktime(0,0,0,$arrDateStart[1],$arrDateStart[0],$arrDateStart[2]);
			if ($FILTER_DATE_START == '')
			{
				$tsDateMin = -9999999999;
			}
			
			$tsDateMax  	= @mktime(0,0,0,$arrDateEnd[1],$arrDateEnd[0],$arrDateEnd[2]);
			if ($FILTER_DATE_END == '')
			{
				$tsDateMax = 9999999999;
			}
		}
		
		$secondsDay		= (24 * 60 * 60);
		
		if (($FILTER_DAYS_IN_PROGRESS_START != '') || ($FILTER_DAYS_IN_PROGRESS_END != ''))
		{
			$FILTER_DIP = 1;
			$givenMin = $FILTER_DAYS_IN_PROGRESS_START;
			$givenMax = $FILTER_DAYS_IN_PROGRESS_END;
			
			$tsDIPMin = time() - ($givenMin * $secondsDay);
			if ($FILTER_DAYS_IN_PROGRESS_START == '')
			{
				$tsDIPMin = 9999999999;
			}
			
			$tsDIPMax = time() - ($givenMax * $secondsDay);
			if ($FILTER_DAYS_IN_PROGRESS_END == '')
			{
				$tsDIPMax = -9999999999;
			}
		}
		
		$nNumberOfRows = $nShowRows;
		$start--;
		if ($FILTER_CUSTOM)
		{
			$start = 0;
			$nNumberOfRows = 50;
		}
		switch($sortby)
		{
			case 'COL_CIRCULATION_NAME':
				$strQuery 	= "		SELECT";
				if ($FILTER_DATE || $FILTER_DIP || $FILTER_CUSTOM)
				{
					$strQuery .= " DISTINCT";
				}
				$strQuery .= "				cf.*
									FROM cf_circulationform cf";
				if ($FILTER_Station || $FILTER_DIP || $FILTER_DATE || $FILTER_CUSTOM)
				{
					$strQuery .= "	INNER JOIN cf_circulationprocess cp
									ON cp.nCirculationFormId = cf.nID";
				}
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE || $FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nID = cp.nCirculationHistoryId";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_fieldvalue fv
									ON fv.nCirculationHistoryId = ch.nID
									$strFilterCustomQuery";
				}
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				
				$strQuery .= " WHERE cf.bIsArchived = '$archivemode' AND bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= " 	ORDER BY cf.strName $sortDirection";
				if (!$bFilterOn || $FILTER_CUSTOM)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_STATION':
				$strQuery 	= "SELECT DISTINCT cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_circulationprocess cp
								ON cf.nID = cp.nCirculationFormId AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
				if ($_REQUEST['bOwnCirculations'])
				{
					$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nID = cp.nCirculationHistoryId";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				$strQuery .= "	INNER JOIN cf_user u
								ON cp.nUserId = u.nID";
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				$strQuery .= " WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= " 	ORDER BY u.strUserId $sortDirection";
				if (!$bFilterOn)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_PROCESS_DAYS':
				$mySortDirection = 'DESC'; //
				if ($sortDirection == 'DESC')
				{
					$mySortDirection = 'ASC'; // upside down
				}
				
				$strQuery 	= "SELECT MAX(cp.dateInProcessSince) as tsDateInProcessSince, cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_circulationprocess cp
								ON cf.nID = cp.nCirculationFormId";
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE || $FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nID = cp.nCirculationHistoryId";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_fieldvalue fv
									ON fv.nCirculationHistoryId = ch.nID
									$strFilterCustomQuery";
				}
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= "	GROUP BY cf.nID
								ORDER BY tsDateInProcessSince $mySortDirection";
				if (!$bFilterOn || $FILTER_CUSTOM)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_PROCESS_START':
				$strQuery 	= "SELECT DISTINCT";
				//if ($FILTER_DATE || $FILTER_TEMPLATE|| $FILTER_DIP || $FILTER_Mailinglist || $FILTER_CUSTOM)
				//{
				//	$strQuery .= " DISTINCT";
				//}
				$strQuery .= " 		cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_circulationprocess cp
								ON cf.nID = cp.nCirculationFormId";
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				$strQuery .= "	INNER JOIN cf_circulationhistory ch
								ON ch.nID = cp.nCirculationHistoryId AND ch.nCirculationFormId = cf.nID";
				if ($FILTER_DATE)
				{
					$strQuery .= " 	AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_fieldvalue fv
									ON fv.nCirculationHistoryId = ch.nID
									$strFilterCustomQuery";
				}
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND cp.nUserId = '$FILTER_Station'";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= " 	ORDER BY ch.dateSending $sortDirection";
				if (!$bFilterOn || $FILTER_CUSTOM)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_SENDER':
				$strQuery	= "SELECT";
				if ($FILTER_DATE || $FILTER_DIP)
				{
					$strQuery .= " DISTINCT";
				}
				$strQuery .= "	cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_user u
								ON cf.nSenderId = u.nID";
				if ($FILTER_Station || $FILTER_DIP || $FILTER_DATE)
				{
					$strQuery .= "	INNER JOIN cf_circulationprocess cp
									ON cp.nCirculationFormId = cf.nID";
				}
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nID = cp.nCirculationHistoryId";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= "	ORDER BY u.strUserId $sortDirection";
				if (!$bFilterOn)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_MAILLIST':
				$mySortDirection = 'DESC'; //
				if ($sortDirection == 'DESC')
				{
					$mySortDirection = 'ASC'; // upside down
				}
				$strQuery 	= "SELECT m.strName as strMaillistName, cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_mailinglist m
								ON m.nID = cf.nMailinglistId";
				if ($FILTER_Mailinglist)
				{	// extended Filter is active
					$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
				}
				if ($FILTER_Station || $FILTER_DIP || $FILTER_DATE)
				{
					$strQuery .= "	INNER JOIN cf_circulationprocess cp
									ON cp.nCirculationFormId = cf.nID";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nCirculationFormId = cf.nID AND ch.nID = cp.nCirculationHistoryId
									AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_TEMPLATE)
				{
					$strQuery .= "	INNER JOIN cf_formtemplate t
									ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND cp.nUserId = '$FILTER_Station'";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= "	GROUP BY cf.nID
								ORDER BY strMaillistName $sortDirection";
				if (!$bFilterOn)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			case 'COL_CIRCULATION_TEMPLATE':
				$strQuery = "SELECT";
				if ($FILTER_DATE || $FILTER_DIP)
				{
					$strQuery .= " DISTINCT";
				}
				$strQuery .= " cf.*
							FROM cf_circulationform cf
							INNER JOIN cf_mailinglist m
							ON cf.nMailinglistId = m.nID";
				if ($FILTER_Mailinglist)
				{	// extended Filter is active
					$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
				}
				$strQuery .= "	INNER JOIN cf_formtemplate t
								ON t.nID = m.nTemplateId";
				if ($FILTER_TEMPLATE)
				{
					$strQuery .= "	AND t.nID = '$FILTER_TEMPLATE'";
				}
				if ($FILTER_Station || $FILTER_DIP || $FILTER_DATE)
				{
					$strQuery .= "	INNER JOIN cf_circulationprocess cp
									ON cp.nCirculationFormId = cf.nID";
				}
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " 	INNER JOIN cf_circulationhistory ch
									ON ch.nID = cp.nCirculationHistoryId";
				}
				if ($FILTER_DATE)
				{
					$strQuery .= " AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if ($FILTER_Sender != false)
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= "	ORDER BY t.strName $sortDirection";
				if (!$bFilterOn)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
			
			case 'COL_CIRCULATION_WHOLETIME':
				$mySortDirection = 'DESC'; //
				if ($sortDirection == 'DESC')
				{
					$mySortDirection = 'ASC'; // upside down
				}
				$strQuery 	= "SELECT";
				if ($FILTER_DATE || $FILTER_TEMPLATE || $FILTER_DIP || $FILTER_Mailinglist || $FILTER_CUSTOM)
				{ 
					$strQuery .= " DISTINCT";
				}
				$strQuery .= " cf.*
								FROM cf_circulationform cf
								INNER JOIN cf_circulationprocess cp
								ON cf.nID = cp.nCirculationFormId";
				if ($FILTER_Station || $FILTER_DIP)
				{
					$strQuery .= " AND (cp.nDecissionState = '0' OR cp.nDecissionState = '2' OR cp.nDecissionState = '16')";
					if ($_REQUEST['bOwnCirculations'])
					{
						$strQuery .= " AND cp.nDecissionState <> 16 AND cp.nDecissionState <> 2";
					}
				}
				if ($FILTER_DIP)
				{
					$strQuery .= " AND cp.dateInProcessSince < '$tsDIPMin' AND cp.dateInProcessSince > '$tsDIPMax'";
				}
				if ($FILTER_TEMPLATE || $FILTER_Mailinglist)
				{
					$strQuery .= "	INNER JOIN cf_mailinglist m
									ON cf.nMailinglistId = m.nID";
					if ($FILTER_Mailinglist)
					{
						$strQuery .= " AND m.strName = '$FILTER_Mailinglist'";
					}
					if ($FILTER_TEMPLATE)
					{
						$strQuery .= "	INNER JOIN cf_formtemplate t
										ON t.nID = m.nTemplateId AND t.nID = '$FILTER_TEMPLATE'";
					}
				}
				$strQuery .= "	INNER JOIN cf_circulationhistory ch
								ON ch.nID = cp.nCirculationHistoryId AND ch.nCirculationFormId = cf.nID";
				if ($FILTER_DATE)
				{
					$strQuery .= " 	AND ch.dateSending > '$tsDateMin' AND ch.dateSending < '$tsDateMax'";
				}
				if ($FILTER_CUSTOM)
				{
					$strQuery .= " 	INNER JOIN cf_fieldvalue fv
									ON fv.nCirculationHistoryId = ch.nID
									$strFilterCustomQuery";
				}
				$strQuery .=	" WHERE cf.bIsArchived = '$archivemode' AND cf.bDeleted = 0";
				if ($FILTER_Name != '')
				{	// extended Filter is active
					$strQuery .= " AND cf.strName LIKE '".$FILTER_Name."%'";
				}
				if ($FILTER_Station)
				{
					$strQuery .= " AND (cp.nUserId = '$FILTER_Station' OR (cp.nUserId = -2 AND cf.nSenderId = $FILTER_Station) )";
				}
				if (($FILTER_Sender != false))
				{	// extended Filter is active
					$strQuery .= " AND cf.nSenderId = '$FILTER_Sender'";
				}
				$strQuery .= "	ORDER BY ch.dateSending $mySortDirection";
				if (!$bFilterOn || $FILTER_CUSTOM)
				{
					$strQuery .= " LIMIT $start, $nNumberOfRows";
				}
				break;
		}
		
		$nResult 	= mysql_query($strQuery) or die(mysql_error());
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;					
				$nIndex++;
			}
		}
		
		return $arrRows;
	}
	
	function cmpCirculations($arr1, $arr2)
	{
		return @strcmp($arr1['strCurStation'], $arr2['strCurStation']);
	}
	
	/**
	*	@param $nFormId
	*	@return the Circ History
	*/
	function getMaxHistoryData($nFormId)
	    {
	    	$arrResult = array();
	    	
	        $query = "SELECT MAX(nID) FROM `cf_circulationhistory` WHERE `nCirculationFormId`=".$nFormId;
	        $nResult = mysql_query($query);
	
	        if ($nResult)
	        {
	            if (mysql_num_rows($nResult) > 0)
	            {
	                $arrRow = mysql_fetch_array($nResult);
	                
	                if ($arrRow)
	                {
	                    return $arrRow[0];
	                }           
	            }   
	        }
	    }
		function getMaxProcessId($nHistoryId)
	    {
	        $query = "SELECT MAX(nID) FROM `cf_circulationprocess` WHERE `nCirculationHistoryId`=".$nHistoryId;
	        $nResult = mysql_query($query);
	
	        if ($nResult)
	        {
	            if (mysql_num_rows($nResult) > 0)
	            {
	                $arrRow = mysql_fetch_array($nResult);
	                
	                if ($arrRow)
	                {
	                    $nMaxId = $arrRow[0];
	                    return $nMaxId;
	                }           
	            }   
	        }
	    }
		function getProcessInformation($nMaxId)
	    {
	        $query = "SELECT * FROM `cf_circulationprocess` WHERE `nID`=".$nMaxId;
	        $nResult = mysql_query($query);
	
	        if ($nResult)
	        {
	            if (mysql_num_rows($nResult) > 0)
	            {
	                $arrRow = mysql_fetch_array($nResult);
	                
	                if ($arrRow)
	                {
	                    return $arrRow;
	                }           
	            }   
	        }        
	    }
	
	/**
	*	@param $nCirculationFormID
	*	@return the decissionstate
	*/
	function getDecissionState($nCirculationFormID)
	{		
		$arrHistoryData 		= $this->getMaxHistoryData($nCirculationFormID);
		$nMaxId 				= $this->getMaxProcessId($arrHistoryData);
        $arrProcessInformation 	= $this->getProcessInformation($nMaxId);
        
        if (($arrProcessInformation["nDecissionState"] == 0) || ($arrProcessInformation["nDecissionState"] == 8) )
		{
			$tsDateInProcessSince = $arrProcessInformation["dateInProcessSince"];
			$tsNow = time();
			$diff = (int)((($tsNow-$tsDateInProcessSince))/(24*60*60));
		}
		else
		{
			$diff = '-';
		}
        			
		$arrResults['nDecissionState']	= $arrProcessInformation["nDecissionState"];
		$arrResults['nDaysInProgress']	= $diff;
		if ($arrProcessInformation["nUserId"] != -2)
		{
			$arrResults['strCurStation']	= $this->getUsername($arrProcessInformation["nUserId"]);
			$arrResults['nCurStationID']	= $arrProcessInformation["nUserId"];
		}
		else
		{
			$strQuery = "SELECT nSenderId FROM cf_circulationform WHERE nID = '".$arrProcessInformation['nCirculationFormId']."' LIMIT 1;";
			$nResult = mysql_query($strQuery);
			$arrResult = mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			$nCurUserId = $arrResult['nSenderId'];
			
			$arrResults['strCurStation']	= $this->getUsername($nCurUserId);
			$arrResults['nCurStationID']	= $nCurUserId;
		}
			
		return $arrResults;
	}
	
	/**
	*	@param $nCirculationFormID
	*	@return the starting date
	*/
	function getStartDate($nCirculationFormID)
	{
		$strQuery = 	"SELECT MAX(dateSending) as 'tsDateSending'
						FROM cf_circulationhistory
						WHERE nCirculationFormId = '$nCirculationFormID'
						GROUP BY nCirculationFormId;";
		$nResult = mysql_query($strQuery);
		
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;					
				$nIndex++;
			}
			
			return date($GLOBALS['DATE_FORMAT'], $arrRows[0]['tsDateSending']);
		}
	}
	
	/**
	*	@param $nCirculationFormID
	*	@return the sender
	*/
	function getSender($nCirculationFormID)
	{
		$strQuery	= "SELECT u.strUserId as 'strSender'
						FROM cf_circulationform c
						INNER JOIN cf_user u
						ON c.nSenderId = u.nID
						WHERE c.nID = '$nCirculationFormID'
						LIMIT 1;";
		$nResult = mysql_query($strQuery);
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;					
				$nIndex++;
			}
			
			return $arrRows[0]['strSender'];
		}
	}
	
	function getSenderDetails($nCirculationFormID)
	{
		$strQuery	= "SELECT u.*
						FROM cf_circulationform c
						INNER JOIN cf_user u
						ON c.nSenderId = u.nID
						WHERE c.nID = '$nCirculationFormID'
						LIMIT 1;";
		$nResult = mysql_query($strQuery);
		if ($nResult)
		{
			$nIndex = 0;
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows[$nIndex] = $arrRow;					
				$nIndex++;
			}
			
			return $arrRows[0];
		}
	}
	
	/**
	*	@param $nMyCirculationFormID, $nMyMailinglistID
	*	@return the width
	*/
	function getWidth($nMyCirculationFormID, $nMyMailinglistID)
	{		
		//-----------------------------------------------
		//--- get the circulation process
		//-----------------------------------------------
		$strQuery = "SELECT MAX(nCirculationHistoryId)
						FROM cf_circulationprocess
						WHERE nCirculationFormId = '$nMyCirculationFormID'";
		$nResult 	= @mysql_query($strQuery);
		if ($nResult)
		{
			$myArrRow 					= mysql_fetch_array($nResult);
			$nMaxCirculationHistoryId 	= $myArrRow[0];
		}
		
		
		$query = "	SELECT * 
					FROM cf_circulationprocess
					WHERE nCirculationHistoryId = '$nMaxCirculationHistoryId'
					AND dateDecission <> '0'
					AND nIsSubstitiuteOf < '1'
					AND nIsSubstitiuteOf > '-2'
					ORDER BY nID;";
						
						
		$nResult = @mysql_query($query) or die (mysql_error());
		
		if ($nResult)
		{
			$nMyIndex = 0;
			$arrCirculationprocess = array();
			while ($arrCurRow = mysql_fetch_row($nResult))
			{
				$arrCirculationprocess[$nMyIndex]["nID"] 					= $arrCurRow['0'];
				$arrCirculationprocess[$nMyIndex]["nCirculationFormId"] 	= $arrCurRow['1'];
				$arrCirculationprocess[$nMyIndex]["nSlotId"]				= $arrCurRow['2'];
				$arrCirculationprocess[$nMyIndex]["nUserId"] 				= $arrCurRow['3'];
				$arrCirculationprocess[$nMyIndex]["dateInProcessSince"]		= $arrCurRow['4'];
				$arrCirculationprocess[$nMyIndex]["nDecissionState"] 		= $arrCurRow['5'];
				$arrCirculationprocess[$nMyIndex]["dateDecission"]			= $arrCurRow['6'];
				$arrCirculationprocess[$nMyIndex]["nIsSubstituteOf"] 		= $arrCurRow['7'];
				$arrCirculationprocess[$nMyIndex]["nCirculationHistoryId"] 	= $arrCurRow['8'];
				
				$nMyIndex++;
			}
		}
		
		//-----------------------------------------------
        //--- get the slottouser
        //-----------------------------------------------	            
        $strQuery = "SELECT COUNT(*) as allUsers FROM cf_slottouser WHERE nMailingListId = '$nMyMailinglistID' ORDER BY nID ASC";
		$nResult = mysql_query($strQuery);
		
		if ($nResult)
		{
			$resultRow = mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			if ($resultRow)
			{
				//Amount of all Users of the current Circulation
				$nAllUsers = $resultRow['allUsers'];
			}
		}
		
		//Amount of Substitute Persons
		$nSubstitutePersons = 0;
		$nMax = count($arrCirculationprocess);
		for ($nMyIndex = 0; $nMyIndex < $nMax; $nMyIndex++)
		{
			$arrCurCirculationprocess = $arrCirculationprocess[$nMyIndex];
			if (($arrCurCirculationprocess['nDecissionState']==0))
			{
				$nSubstitutePersons++;
			}
		}
		
		//Current Position
		$nMyCurPosition = (count($arrCirculationprocess)- $nSubstitutePersons);
		//echo "MyCurPosition: $nMyCurPosition <br>";
		//echo "nAllUsers: $nAllUsers <br><br>";

		if ($nAllUsers > 0)
		{
			//if ($nMyCurPosition == $nAllUsers) $nMyCurPosition--;
			$width = (int) ((($nMyCurPosition)/$nAllUsers)*100);
		}    
	
		if (($nAllUsers < 1) || ($width > 100) || ($width < 0))
		{
			$width = -1;
		}
		
		$arrProgressbar['width'] = $width;
		
		if($width < 33)
		{
			$arrProgressbar['color'] = '#ef0000';
		}
		else
		{
			if($width < 66)
			{
				$arrProgressbar['color'] = '#ffa000';
			}
			else
			{
				$arrProgressbar['color'] = '#00ef00';
			}
		}
		
		return $arrProgressbar;
	}
	
	/**
	 * @param $archivemode
	 * @return amount of circulations
	 */
	function getAmountOfAllCirculations($archivemode)
	{
		$strQuery = "SELECT COUNT(nID) as 'nMyResult' FROM cf_circulationform WHERE bIsArchived = '$archivemode' AND bDeleted = 0;";
		$nResult = mysql_query($strQuery);
		
		if ($nResult)
		{
			while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrRows = $arrRow;
			}
			return $arrRows['nMyResult'];
		}
	}
	
	
	function getUserById($user_id = false)
	{
		if ($user_id)
		{
			$strQuery 	= "SELECT * FROM cf_user WHERE nID = '$user_id' LIMIT 1;";
			$nResult 	= mysql_query($strQuery);
			$arrResult	= mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			return $arrResult;
		}
	}
	
	function filterUsers($filter = false)
	{
		if ($filter != 'false')
		{
			$arrSplit = explode(" ", $filter);
			
			$strQuery = "SELECT user_id FROM cf_user_index";
			
			
			if ($arrSplit[0] != '')
			{
				$strQuery .= " WHERE";
			}
			
			$nMax = sizeof($arrSplit);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$strSplit = $arrSplit[$nIndex];
				
				if ($strSplit != '')
				{
					if ($nIndex > 0) $strQuery .= " AND";
					$strQuery .= " `index` LIKE '%".$strSplit."%'";
				}
			}
			
			$strQuery .= " ORDER BY `index` ASC";
			$nResult 	= mysql_query($strQuery) or die (mysql_error());
			
			if ($nResult)
			{
				while($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
				{
					$arrResult[] =  $arrRow;
				}
				return $arrResult;
			}
		}
	}
	
	/**
	 * returns the substitutes of a user
	 *
	 * @param Integer $user_id
	 * @return Array $result
	 */
	function getSubstitutes($user_id = false)
	{
		if ($user_id != 'false')
		{
			$strQuery 	= "SELECT substitute_id FROM cf_substitute WHERE user_id = '$user_id' ORDER BY position ASC";
			$nResult 	= mysql_query($strQuery) or die (mysql_error());
			
			if ($nResult)
			{
				while($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
				{
					$arrResult[] =  $arrRow;
				}
				return $arrResult;
			}
		}
		return false;
	}
	
	/**
	 * returns an array containing the Extension Objects with the Extension details
	 * returns only the Extensions of the corresponding hookId
	 *
	 * @param String $hookId
	 * @return Array
	 */
	function getExtensionsByHookId($hookId = false)
	{
		if ($hookId)
		{
			// first scan the directory for extensions
			$direcories		= array();
			$extensions		= array();
			$addedIndex		= 0;
			$rootPath 		= '../extensions/';
			$definitionFile = 'extdef.xml';
			
			$directory	= opendir($rootPath);
			while (false !== ($file = readdir ($directory)))
			{
				if (($file != '.') && ($file != '..') && ($file != '.svn'))
				{
					$direcories[] = $file;
				}
			}
			closedir($directory);
			
			// go on if there are extensions
			if (sizeof($direcories))
			{
				// read the xml definition files
				$max = sizeof($direcories);
				for ($index = 0; $index < $max; $index++)
				{
					$directory 		= $direcories[$index].'/';
					$curFilename	= $rootPath.$directory.$definitionFile;
					
					$Xml = @simplexml_load_file($curFilename);
					if ($Xml)
					{
						$isActive	= $Xml->attributes()->isActive;
						$curHookId	= $Xml->hook->attributes()->id;
						
						if (($hookId == $curHookId) && ($isActive == 'true'))
						{	// save the extension details to the array only if it's hook matches and it's active
							if ($hookId != 'CF_ENDACTION')
							{
								$extensions[$addedIndex]['path'] 		= $rootPath.$directory;
								$extensions[$addedIndex]['Extension'] 	= $Xml;
								$addedIndex++;
							}
							else
							{	
								$params	= $Xml->hook->param;
								foreach ($params as $param)
								{
									$name	= (String) $param->attributes()->name;
									$value	= (String) $param->attributes()->value;
									$hookParams[$name] = $value;
								}
								
								$extensions[$hookParams['position']]['path'] 		= $rootPath.$directory;
								$extensions[$hookParams['position']]['Extension'] 	= $Xml;
							}
						}
					}
				}
				if ($hookId == 'CF_ENDACTION') ksort($extensions);
				
				if ($extensions) return $extensions;
			}
		}
		return false;
	}
	
	function getMenuGroupExtensions($menuGroup = false, $extensions = false)
	{
		if ($menuGroup && $extensions)
		{
			$menuGroupExtensions 	= array();
			$addedIndex 			= 0;
			
			$max = sizeof($extensions);
			for ($index = 0; $index < $max; $index++)
			{
				$Extension 	= $extensions[$index]['Extension'];
				$hooks 		= $Extension->hook;
				$addGroup 	= false;
				
				$max2 = sizeof($hooks);
				for ($index2 = 0; $index2 < $max2; $index2++)
				{
					$hook 			= $hooks[$index2];
					$curMenuGroup	= $hook->group;
					$splitCurGroup	= split('_', $curMenuGroup);
					
					if ($menuGroup == $curMenuGroup)
					{
						$addGroup = true;
					}
					elseif(($splitCurGroup[0] != 'CF') && ($menuGroup == 'CF_GROUP_USERDEFINED'))
					{	// in this case it's a userdefined menugroup 
						$addGroup = true;
					}
				}
				
				if ($addGroup)
				{
					$menuGroupExtensions[$addedIndex] = $extensions[$index];
					$addedIndex++;
				}
			}
			if ($menuGroupExtensions) return $menuGroupExtensions;
		}
		return false;
	}
	
	function getExtensionParams($hook = false)
	{
		global $_REQUEST;
		
		if ($hook)
		{
			$params = $hook->param;
			
			$max2 = sizeof($params);
			for ($index2 = 0; $index2 < $max2; $index2++)
			{
				$param 	= $params[$index2];
				$name 	= $param->attributes()->name;
				$value 	= $param->attributes()->value;
				
				if ($index2 < 1) $destinationParams .= '?'.$name.'='.$value;
				if ($index2 > 0) $destinationParams .= '&'.$name.'='.$value;
			}
			
			if ($destinationParams != '')
			{
				// replace the Placeholders
				$destinationParams = str_replace('CF_LANGUAGE', $_REQUEST['language'], $destinationParams);
				
				return $destinationParams;
			}
		}
		return false;
	}
	
	function getEndActionParams($endAction = false)
	{
		if ($endAction)
		{	
			$params	= $endAction['Extension']->hook->param;
			$path	= $endAction['path'];
			
			foreach ($params as $param)
			{
				$name	= (String) $param->attributes()->name;
				$value	= (String) $param->attributes()->value;
				
				if ($name == 'filename') $value = $path.$value;
				$hookParams[$name] = $value;
			}
			
			if ($hookParams) return $hookParams;
		}
	}
	
	function getEndActions($endActions = false)
	{
		if ($endActions)
		{
			$runningIndex	= 0;
			foreach ($endActions as $endAction)
			{
				$curEndAction	= $endAction['Extension'];				
				$path			= $endAction['path'];
				$hook			= $curEndAction->hook;
				$params			= $hook->param;
				
				foreach ($params as $param)
				{
					$name	= (String) $param->attributes()->name;
					$value	= (String) $param->attributes()->value;
					
					$curEndActions[$runningIndex][$name] = $path.$value;
				}
				
				$runningIndex++;
			}
			if ($curEndActions) return $curEndActions;
		}
	}
	
	function getUserdefinedGroups($extensions = false)
	{
		if ($extensions)
		{
			$addedIndex 		= 0;
			$userDefinedGroups	= array();
			$existingGroups 	= array();
			
			$max = sizeof($extensions);
			for ($index = 0; $index < $max; $index++)
			{
				$Extension	= $extensions[$index]['Extension'];
				$hooks 		= $Extension->hook;
				
				$max2 = sizeof($hooks);
				for ($index2 = 0; $index2 < $max2; $index2++)
				{
					$hook 		= $hooks[$index2];
					$curGroup	= ucwords($hook->group);
					$splitGroup	= split('_', $curGroup);
					
					if ($splitGroup[0] != 'CF')
					{
						if ($existingGroups[$curGroup] == '')
						{
							$userDefinedGroups[$addedIndex] = $curGroup;
							$existingGroups[$curGroup]		= 'false';
							$addedIndex++;
						}
					}
				}
			}
			
			return $userDefinedGroups;
		}
	}
	
}	
?>
