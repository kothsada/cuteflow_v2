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
	session_start();
	
	require_once '../language_files/language.inc.php';
	require_once '../config/config.inc.php';
	require_once '../lib/datetime.inc.php';
	require_once '../lib/viewutils.inc.php';
	require_once '../config/db_connect.inc.php';
    require_once 'CCirculation.inc.php';
    require_once 'placeholder_tags.php';
    require_once 'placeholder_tags_addtext.php';
    
	$nMailinglistID			= $_REQUEST['listid'];
	$strCirculationName		= $_REQUEST['strCirculationName'];
	$strAdditionalText		= $_REQUEST['strAdditionalText'];
	$language 				= $_REQUEST['language'];
	$start 					= 1;
	
	if ($_REQUEST['SuccessMail'] == 'on') {
		$SuccessMail = 0;
		
		foreach ($_REQUEST['NotificationType'] as $type) {
			$SuccessMail |= $type;
		}
	}
	$SuccessArchive			= $_REQUEST['SuccessArchive'];
	$SuccessDelete			= $_REQUEST['SuccessDelete'];
	$bAnonymize				= $_REQUEST['Anonymize'] == 1 ? 1 : 0;
	$nSenderID 				= $_SESSION['SESSION_CUTEFLOW_USERID'];
	$objCirculation			= new CCirculation();
	
	$nMax = sizeof($arrPlaceholdersAddText);
	for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
	{
		$strPlaceholderAT = $arrPlaceholdersAddText[$nIndex];
		$arrSplit 		= split('%', $strPlaceholderAT);
		$strReplace		= getPlaceholderContent($arrSplit[1]);
		
		$strAdditionalText = str_replace($strPlaceholderAT, $strReplace, $strAdditionalText);
	}
	
	$objMyCirculation 	= new CCirculation();
			
	$arrMailinglist 	= $objMyCirculation->getMailinglist($nMailinglistID);									// corresponding mailinglist
	$nFormTemplateID 	= $arrMailinglist['nTemplateId'];
	
	if ($_REQUEST['bRestart'])
	{
		$nCirculationFormID = $_REQUEST['cfid'];
		
		//-----------------------------------------
		//--- Write next history
		//-----------------------------------------
		$strQuery = "SELECT MAX(nRevisionNumber) FROM cf_circulationhistory WHERE nCirculationFormId=".$nCirculationFormID;
		$nResult = mysql_query($strQuery, $nConnection);
		
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$arrRow = mysql_fetch_array($nResult);
				$nRevisionNumber = $arrRow[0] +1;
			}
		}
		$dateSending = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		
		// get History Id
		$strQuery = "SELECT nID FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID' AND nRevisionNumber = '".($nRevisionNumber-1)."'";
		$nResult = mysql_query($strQuery, $nConnection);
		
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$arrRow = mysql_fetch_array($nResult);
				$nOldHistoryId = $arrRow[0];
			}
		}
		
		$strQuery = "INSERT INTO cf_circulationhistory VALUES(null, $nRevisionNumber, '$dateSending', '$strAdditionalText', $nCirculationFormID)";
		mysql_query($strQuery, $nConnection);
		
		$strQuery = "SELECT MAX(nID) FROM cf_circulationhistory";
		$nResult = mysql_query($strQuery, $nConnection);
		
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$arrRow = mysql_fetch_array($nResult);
				$nCirculationHistoryID = $arrRow[0];
			}
		}
		// - - - - - - - - - - - - START RESTART OPTIONS - - - - - - - - - - - - 

		if ($_REQUEST['MailingList'][0] != 0)
		{	// User has decided to start the circulation from a chosen Station - and not from beginning
			$arrSplit = split('---', $_REQUEST['MailingList']);
			
			$nChoosenSlotId 	= $arrSplit[1];
			$nChoosenUserId 	= $arrSplit[2];
			$nChoosenPosition	= $arrSplit[3];
			
			$arrSender			= $objCirculation->getSenderDetails($nCirculationFormID);
			$arrMailinglist 	= $objCirculation->getMailinglist($nMailinglistID);
			$nFormTemplateID 	= $arrMailinglist['nTemplateId'];
			$arrUsers			= $objCirculation->getUsers();
			$arrSlots			= $objCirculation->getFormslots($nFormTemplateID);
			$tsNow				= time();
			$tsDelayed			= mktime(date("H")-2,date("i"),date("s"),date("m"), date("d"), date("Y"));
			
			$nMax = sizeof($arrSlots);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$arrSlot = $arrSlots[$nIndex];
				$nSlotID = $arrSlot['nID'];
				$strSlot = $arrSlot['strName'];
				
				$strQuery 	= "SELECT * FROM cf_slottouser WHERE nMailingListId = '$nMailinglistID' AND nSlotId = '$nSlotID' ORDER BY nPosition ASC";
				$nResult 	= mysql_query($strQuery, $nConnection);

				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$bSearchStation = true;
						while (($arrRow = mysql_fetch_array($nResult)) && ($bSearchStation))
						{
							$arrUser = $arrUsers[$nUserID];
							
							$nUserID 	= $arrRow['nUserId'];
							$nPosition 	= $arrRow['nPosition'];
							
							if ($nUserID != -2)
							{
								$arrUserDetails	= $objCirculation->getUserById($nUserID);
							}
							else
							{
								$arrUserDetails	= $arrSender;
							}
							
							$nUserID = $arrUserDetails['nID'];
							
							// check if we have reached the selected Station
							if (($nSlotID == $nChoosenSlotId) && ($nUserID == $nChoosenUserId) && ($nPosition == $nChoosenPosition))
							{	// the current User is the selected Station
								
								// this will be the next user
								$arrNextUser[0] = $nUserID;
								$arrNextUser[1] = $nSlotID;
								
								// stop the search cause we found it
								$bSearchStation = false;
								$nIndex = $nMax;
							}
							else
							{	// the current user is before the selected Station
								$strQuery = "INSERT INTO cf_circulationprocess VALUES(	null,
																						'$nCirculationFormID',
																						'$nSlotID',
																						'$nUserID',
																						'$tsDelayed',
																						'4',
																						'".($tsDelayed+20)."',
																						'0',
																						'$nCirculationHistoryID',
																						0)";
								mysql_query($strQuery, $nConnection);
								$tsDelayed += 50;
							}
						}
					}
				}
			}
		}
		
		if ($_REQUEST['bUseLatestValues'] == 'on')
		{
			$nNewCirculationHistoryID = $nCirculationHistoryID;
			$nOldCirculationHistoryID = $nOldHistoryId;
			
			$query 		= "SELECT * FROM cf_fieldvalue WHERE nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nOldCirculationHistoryID'";
			$result		= mysql_query($query, $nConnection);
			while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$arrFieldvalues[] = $row;
			}
			
			$nMax = sizeof($arrFieldvalues);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$arrFieldvalue = $arrFieldvalues[$nIndex];
				
				$nInputFieldId 			= $arrFieldvalue['nInputFieldId'];
				$strFieldValue 			= $arrFieldvalue['strFieldValue'];
				$nSlotId 				= $arrFieldvalue['nSlotId'];
				$nFormId 				= $arrFieldvalue['nFormId'];
				$nCirculationHistoryId 	= $nCirculationHistoryID;
				
				$query = "INSERT INTO cf_fieldvalue VALUES(	null,
															'$nInputFieldId',
															'$strFieldValue',
															'$nSlotId',
															'$nFormId',
															'$nNewCirculationHistoryID')";
				echo $query.'<br>';
				mysql_query($query, $nConnection);
			}
		}
		
		// - - - - - - - - - - - - END RESTART OPTIONS - - - - - - - - - - - -
		
	}
	else
	{
		//--- write form
			
			$nCirculationFormID = $objMyCirculation->addCirculationForm($strCirculationName, $nMailinglistID, $nSenderID, $SuccessMail, $SuccessArchive, $SuccessDelete, $bAnonymize);	
	
		
		//--- Write the starting history
		
			$nCirculationHistoryID = $objMyCirculation->addCirculationHistory($nCirculationFormID, $strAdditionalText);
	}
	
	
	//-----------------------------------------
	//--- write the attachments
	//-----------------------------------------
	$strFolderName = "../attachments/cf_$nCirculationFormID/";
	@mkdir($strFolderName);
	
	$strFolderName = $strFolderName.time()."/";
	@mkdir($strFolderName);
	
	if ($_FILES["attachment1"]["name"] != "")
	{
		@move_uploaded_file($_FILES["attachment1"]["tmp_name"], $strFolderName.$_FILES["attachment1"]["name"]);
		$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment1"]["name"]."', ".$nCirculationHistoryID.")";
		@mysql_query($strQuery);
	}
	if ($_FILES["attachment2"]["name"] != "")
	{
		@move_uploaded_file($_FILES["attachment2"]["tmp_name"], $strFolderName.$_FILES["attachment2"]["name"]);
		$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment2"]["name"]."', ".$nCirculationHistoryID.")";
		@mysql_query($strQuery);
	}
	if ($_FILES["attachment3"]["name"] != "")
	{
		@move_uploaded_file($_FILES["attachment3"]["tmp_name"], $strFolderName.$_FILES["attachment3"]["name"]);
		$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment3"]["name"]."', ".$nCirculationHistoryID.")";
		@mysql_query($strQuery);
	}
	if ($_FILES["attachment4"]["name"] != "")
	{
		@move_uploaded_file($_FILES["attachment4"]["tmp_name"], $strFolderName.$_FILES["attachment4"]["name"]);
		$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment4"]["name"]."', ".$nCirculationHistoryID.")";
		@mysql_query($strQuery);
	}
	
	// - - - - - - - - - - - - START STANDARDVALUES - - - - - - - - - - - - 
	if ($_REQUEST['bUseLatestValues'] != 'on')
	{
		$arrFormSlots	= $objMyCirculation->getFormslots($nFormTemplateID);
		
		$nMax = sizeof($arrFormSlots);
		$nMyIndex = 0;
		for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
		{
			$nCurFormSlotID = $arrFormSlots[$nIndex]['nID'];
			
			$strQuery	= "SELECT * FROM cf_slottofield WHERE nSlotId = '$nCurFormSlotID';";	
			$result		= @mysql_query($strQuery);
			
			while ($arrRow = mysql_fetch_row($result))
			{
				$arrInputFieldIDs[$nMyIndex]['nInputFieldID'] 	= $arrRow[2];
				$arrInputFieldIDs[$nMyIndex]['nFormSlotID'] 	= $nCurFormSlotID;	
				$nMyIndex++;				
			}
		}
		
		$nMax = sizeof($arrInputFieldIDs);
		for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
		{
			$nCurInputFieldID 	= $arrInputFieldIDs[$nIndex]['nInputFieldID'];
			$nCurFormSlotID		= $arrInputFieldIDs[$nIndex]['nFormSlotID'];
			
			$strQuery 			= "SELECT * FROM cf_inputfield WHERE nID = '$nCurInputFieldID' LIMIT 1;";
			$nResult 			= @mysql_query($strQuery);
			$arrCurInputField 	= @mysql_fetch_array($nResult,MYSQL_ASSOC);
			
			$strCurStandardValue = $arrCurInputField['strStandardValue'];
			
			$nPHMax = sizeof($arrPlaceholders);
			for ($nPHIndex = 0; $nPHIndex < $nPHMax; $nPHIndex++)
			{
				$strCurPlaceholder = $arrPlaceholders[$nPHIndex];
				
				if($strCurStandardValue == $strCurPlaceholder)
				{
					$arrSplit = split('%', $strCurPlaceholder);
									
					$strCurStandardValue = replaceMyPlaceholder($arrSplit[1]);
					
					$nPHIndex = $nPHMax;
				}
			}
			
			$nMyFieldType = $objMyCirculation->getFieldType($nCurInputFieldID);
			
			$arrSplit = '';
			$strNewStdValue = '';
			if (($nMyFieldType == 6) || ($nMyFieldType == 7) || ($nMyFieldType == 8))
			{
				$arrSplit = split('---', $strCurStandardValue);
				
				for ($nNewIndex = 3; $nNewIndex < sizeof($arrSplit); $nNewIndex = ($nNewIndex + 2))
				{
					if ($arrSplit[$nNewIndex] == '')
					{
						$strNewStdValue = $strNewStdValue.'0---';
					}
					else
					{
						$strNewStdValue = $strNewStdValue.$arrSplit[$nNewIndex].'---';
					}
				}
				$strQuery 	= "INSERT INTO cf_fieldvalue values( null, '$nCurInputFieldID', '$strNewStdValue', '$nCurFormSlotID', '$nCirculationFormID' , '$nCirculationHistoryID' )";
				$nResult 	= @mysql_query($strQuery);
			}
			else
			{
				$strQuery 	= "INSERT INTO cf_fieldvalue values( null, '$nCurInputFieldID', '$strCurStandardValue', '$nCurFormSlotID', '$nCirculationFormID' , '$nCirculationHistoryID' )";
				$nResult 	= @mysql_query($strQuery);
			}			
		}
	}
	// - - - - - - - - - - - - END STANDARDVALUES - - - - - - - - - - - -


	if ($_REQUEST['step1'] == '') //button 'step1' hasn't been pressed - so it must be step 2 or 3
	{
		$nIndexMailinglist 	= 0;
		$nIndexValues 		= 0;
		
		while(list($key, $value) = each($_REQUEST))
		{
			$arrCurKey = split('_', $key);			
			
			if ($arrCurKey[3] == 'MAILLIST')
			{
				$arrValues_MailingList[$nIndexMailinglist]['key'] 	= $arrCurKey[0].'_'.$arrCurKey[1].'_'.$arrCurKey[2];
				$arrValues_MailingList[$nIndexMailinglist]['value']	= $value;
				$nIndexMailinglist++;
			}
			else
			{
				$arrValues_Values[$nIndexValues]['key']		= $key;
				$arrValues_Values[$nIndexValues]['value']	= $value;
				$nIndexValues++;
			}
		}
		
		// - - - - - - - - - - - - START MAILINGLIST - - - - - - - - - - - -

		if ($_REQUEST['changeMailinglist'])
		{
			$nOldMailinglistID 	= $arrMailinglist['nID'];
			$strMailinglistName	= $arrMailinglist['strName'];
			$bIsDefault 		= $arrMailinglist['bIsDefault'];
			
			$strQuery = "INSERT INTO cf_mailinglist values( null, '$strMailinglistName', '$nFormTemplateID', '1', 0, 0)";
			$nResult = mysql_query($strQuery) or die(mysql_error());
			
			$strQuery = "SELECT MAX(nID) FROM cf_mailinglist";
			$nResult = mysql_query($strQuery) or die(mysql_error());
			
			$arrRow = mysql_fetch_array($nResult);
			$nNewMailinglistID = $arrRow[0];						// the new Mailinglist ID
			$nMailinglistID = $nNewMailinglistID;
			
			$strQuery = "UPDATE cf_circulationform SET nMailinglistId = '$nMailinglistID' WHERE nID = '$nCirculationFormID'";
			$nResult = mysql_query($strQuery) or die(mysql_error());	
			
			//-----------------------------------------------
			//--- get all slots for the given template
	        //-----------------------------------------------
			$arrSlots 			= array();
			$arrSlotRelations 	= array();
			
	        $strQuery 	= "SELECT * FROM cf_formslot WHERE nTemplateID = '$nFormTemplateID'  ORDER BY nSlotNumber ASC";
			$nResult 	= @mysql_query($strQuery);	
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{
						$arrSlots[] = $arrRow;
					}
				}
			}	
						
			//-----------------------------------------------
			//--- create the array with all slot to user 
			//--- relations
			//-----------------------------------------------
			foreach ($arrValues_MailingList as $arrCurMailinglist)
			{
				$arrKeyValue = explode ("_", $arrCurMailinglist['value']);
				$arrSlotRelations[$arrKeyValue[0]][$arrKeyValue[2]] = $arrKeyValue[1];
			}
			
			//--- cf_slottouser
			foreach ($arrSlots as $arrSlot)
			{
				//--- first delete all entries for this slot
				$strQuery 	= "DELETE FROM cf_slottouser WHERE nMailingListId = '$nNewMailinglistID' AND nSlotId=".$arrSlot["nID"];
				$nResult 	= mysql_query($strQuery) or die(mysql_error()."1<br> $strQuery <br>");	
				
				//--- After that insert all slot to user relations for this slot
				$slot_id = $arrSlot['nID'];
				if (array_key_exists($slot_id, $arrSlotRelations))
				{					
					foreach ($arrSlotRelations[$arrSlot['nID']] as $nPos=>$nUserId)
					{
						$strQuery 	= "INSERT INTO cf_slottouser values (null, ".$arrSlot["nID"].", '$nNewMailinglistID', $nUserId, $nPos)";
						$nResult 	= mysql_query($strQuery) or die(mysql_error()."2<br> $strQuery <br>");	
					}
				}
			}
		}
		
		// - - - - - - - - - - - - END MAILINGLIST - - - - - - - - - - - - 
		
		if ($_REQUEST['step2'] == '') //button 'step1' and 'step2' hasn't been pressed - so it must be step3
		{
			if ($_REQUEST['changeValues'])
			{
			
				// - - - - - - - - - - - - START EDITEDVALUES - - - - - - - - - - - - 
				$arrRBOverview;					
				function addRB($RBGroup, $strMyName, $nMyState, $nFieldId, $nSlotId, $nFormId)
				{
					global $arrRBOverview;
					
					$arrRBOverview[$RBGroup][] = array( 'strMyName' => $strMyName, 
														'nMyState' => $nMyState,
														'nFieldId' => $nFieldId,
														'nSlotId' => $nSlotId,
														'nFormId' => $nFormId
														 );
				}
				
				$arrCBOverview;					
				function addCB($CBGroup, $strMyName, $nMyState, $nFieldId, $nSlotId, $nFormId)
				{
					global $arrCBOverview;
					$arrCBOverview[$CBGroup][] = array( 'strMyName' => $strMyName, 
														'nMyState' => $nMyState,
														'nFieldId' => $nFieldId,
														'nSlotId' => $nSlotId,
														'nFormId' => $nFormId
														 );
				}
				
				$arrCOMBOOverview;					
				function addCOMBO($ComboGroup, $strMyName, $nMyState, $nFieldId, $nSlotId, $nFormId)
				{
					global $arrCOMBOOverview;
					
					$arrCOMBOOverview[$ComboGroup][] = array( 'strMyName' => $strMyName, 
															'nMyState' => $nMyState,
															'nFieldId' => $nFieldId,
															'nSlotId' => $nSlotId,
															'nFormId' => $nFormId
															 );
				}
				
				while(list($key, $value) = each($_FILES)) // uploading files
				{
					$arrValues = explode("_", $key);
					
					$nFieldId 	= $arrValues[0];
					$nSlotId 	= $arrValues[1];
					$nFormId	= $arrValues[2];
					
					$strMyFile = $_FILES[$key]['name'];
					
					$nMyKey = $nFieldId.'_'.$nSlotId.'_'.$nFormId;
					
					$curKey = $nMyKey.'_REG';
					$myREGEX = $_REQUEST[$curKey];
					
					if(($strMyFile != '') && ($strMyFile != 'attachment1') && ($strMyFile != 'attachment2') && ($strMyFile != 'attachment3') && ($strMyFile != 'attachment4'))
					{				
						$nNumberOfUpload = 1;
						$value			= '---'.$nNumberOfUpload.'---'.$nSlotId.'_'.$nFormId.'_'.$nCirculationHistoryID.'---'.$strMyFile.'rrrrr'.$myREGEX;
						
						$uploaddir = '../upload/'.$nSlotId.'_'.$nFormId.'_'.$nCirculationHistoryID.'_'.$nNumberOfUpload.'/';
						@mkdir($uploaddir);
						
						$uploadfile = $uploaddir.$strMyFile;
						
						move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile);
						
						$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[0]." AND nSlotId=".$arrValues[1]." AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'; ";
						@mysql_query($strQuery, $nConnection);
					}					
				}
				
				
				$nCrazyMax = sizeof($arrValues_Values);
				for ($nCrazyIndex = 0; $nCrazyIndex < $nCrazyMax; $nCrazyIndex++)
				{
					$arrCrazyValues = $arrValues_Values[$nCrazyIndex];
					
					$value 	= $arrCrazyValues['value'];
					$key 	= $arrCrazyValues['key'];
					
					$arrValues = explode("_", $key);
					if (sizeof($arrValues) > 2)
					{
						if ($arrValues[0] == 'RBName')
						{
							$nFieldId	= $arrValues[1];
							$nSlotId 	= $arrValues[2];
							$nFormId	= $arrValues[3];
							
							$nRBGroupID	= $arrValues[5];
							$nPosition 	= $arrValues[6];
							
							$nMyGroupID = $nFieldId.'_'.$nSlotId.'_'.$nFormId;
							
							$strMyKey = 'RBName_'.$nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nRadiogroup_'.$nRBGroupID.'_'.$nPosition;
							$strReq = $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nRadiogroup_'.$nRBGroupID;
							$strValue = $_REQUEST["$strMyKey"];
							
							$arrRBContent[] = array ( 'strMyKey' => $strMyKey, 'strMyValue' => $strValue );
							
							$strState = $_REQUEST[$strReq];
							addRB($nMyGroupID, $strValue, $strState, $nFieldId, $nSlotId, $nFormId);
						}							
						elseif ($arrValues[0] == 'CBName')
						{
							$nFieldId	= $arrValues[1];
							$nSlotId 	= $arrValues[2];
							$nFormId	= $arrValues[3];
							
							$nCBGroupID	= $arrValues[5];
							$nPosition 	= $arrValues[6];
							
							$nMyGroupID = $nFieldId.'_'.$nSlotId.'_'.$nFormId;
							
							$strMyKey = 'CBName_'.$nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nCheckboxGroup_'.$nCBGroupID.'_'.$nPosition;
							$strReq = $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nCheckboxGroup_'.$nCBGroupID.'_'.$nPosition;
							$strValue = $_REQUEST["$strMyKey"];
							
							$arrCBContent[] = array ( 'strMyKey' => $strMyKey, 'strMyValue' => $strValue );
							$strState = $_REQUEST[$strReq];
							addCB($nMyGroupID, $strValue, $strState, $nFieldId, $nSlotId, $nFormId);
						}
						elseif ($arrValues[0] == 'COMBOName')
						{
							$nFieldId	= $arrValues[1];
							$nSlotId 	= $arrValues[2];
							$nFormId	= $arrValues[3];
							
							$nRBGroupID	= $arrValues[5];
							$nPosition 	= $arrValues[6];
							
							$nMyGroupID = $nFieldId.'_'.$nSlotId.'_'.$nFormId;
							
							$strMyKey = 'COMBOName_'.$nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nCombobox_'.$nRBGroupID.'_'.$nPosition;
							$strReq = $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_nComboboxV_'.$nRBGroupID;
							$strValue = $_REQUEST["$strMyKey"];
							
							$arrRBContent[] = array ( 'strMyKey' => $strMyKey, 'strMyValue' => $strValue );
							
							$strState = $_REQUEST[$strReq];
							addCOMBO($nMyGroupID, $strValue, $strState, $nFieldId, $nSlotId, $nFormId);
						}
						else
						{								
							//--- Test if value already exists
							$nFieldId 	= $arrValues[0];
							$nSlotId 	= $arrValues[1];
							$nFormId 	= $arrValues[2];
							$nFieldType = $arrValues[3];
							$nFieldContentType = $arrValues[4];
							
							$nMyKey = $nFieldId.'_'.$nSlotId.'_'.$nFormId;
							switch ($nFieldType)
							{
								case '1':
									$curKey = $nMyKey.'_REG';
									$myREGEX = $_REQUEST[$curKey];
									$NoEdit = 0;
									
									$nPHMax = sizeof($arrPlaceholders);
									for ($nPHIndex = 0; $nPHIndex < $nPHMax; $nPHIndex++)
									{
										$strCurPlaceholder = $arrPlaceholders[$nPHIndex];
										
										if($value == $strCurPlaceholder)
										{											
											$arrSplit = split('%', $strCurPlaceholder);
								
											$value = replaceMyPlaceholder($arrSplit[1]);
											
											$nPHIndex = $nPHMax;
											$NoEdit = 1;
										}
									}
									
									if (!$NoEdit)
									{
										if ($myREGEX!='')
										{
											$value	= $value.'rrrrr'.$myREGEX;
										}
										else
										{
											$value = $value;
										}
									}
									break;
								case '2':
									$value	= $value;
									break;	
								case '3':
									$curKey = $nMyKey.'_REG';
									$myREGEX = $_REQUEST[$curKey];
									if ($myREGEX!='')
									{
										$value	= 'xx'.$nFieldContentType.'xx'.$value.'rrrrr'.$myREGEX;
									}
									else
									{
										$value	= 'xx'.$nFieldContentType.'xx'.$value;
									}								
									break;
								case '4':
									$curKey = $nMyKey.'_REG';
									$myREGEX = $_REQUEST[$curKey];
									if ($myREGEX!='')
									{
										$value 	= 'xx'.$nFieldContentType.'xx'.$value.'rrrrr'.$myREGEX;
									}
									else
									{
										$value 	= 'xx'.$nFieldContentType.'xx'.$value;
									}
									break;
							}
							
							if (($nFieldType == 1) || ($nFieldType == 2) || ($nFieldType == 3) || ($nFieldType == 4) || ($nFieldType == 5))
							{
								$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[0]." AND nSlotId=".$arrValues[1]." AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'; ";
						   		mysql_query($strQuery, $nConnection);
							}
						}					
					}
				}
				
				$strCrazyValue = '';
				if (sizeof($arrRBOverview) > 0)
				{
					foreach($arrRBOverview as $arrCurRBOverview)
					{
						$nAmount = sizeof($arrCurRBOverview);
						$strCrazyValue	= '---'.$nAmount;
						$nCounter = 0;
						
						foreach($arrCurRBOverview as $arrCurRBEntries)
						{
							$strCurName = $arrCurRBEntries['strMyName'];
							$nCurState	= 0;
							
							if ($arrCurRBEntries['nMyState'] == $nCounter)
							{
								$nCurState = 1;
							}
							$nFieldId	= $arrCurRBEntries['nFieldId'];
							$nSlotId	= $arrCurRBEntries['nSlotId'];
							$nFormId	= $arrCurRBEntries['nFormId'];
							
							$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
							$nCounter++;
						}
						
						$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'; ";
						mysql_query($strQuery, $nConnection);
					}
				}
				
				$strCrazyValue = '';
				if (sizeof($arrCBOverview) > 0)
				{
					foreach($arrCBOverview as $arrCurRBOverview)
					{
						$nAmount = sizeof($arrCurRBOverview);
						$strCrazyValue	= '---'.$nAmount;
						
						foreach($arrCurRBOverview as $arrCurRBEntries)
						{
							$strCurName = $arrCurRBEntries['strMyName'];
							$nCurState	= 0;
							if ($arrCurRBEntries['nMyState'] == '1')
							{
								$nCurState = 1;
							}
							$nFieldId	= $arrCurRBEntries['nFieldId'];
							$nSlotId	= $arrCurRBEntries['nSlotId'];;
							$nFormId	= $arrCurRBEntries['nFormId'];;
							
							$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
						}
						
						$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID';";
						mysql_query($strQuery, $nConnection);
					}
				}
				
				$strCrazyValue = '';
				if (sizeof($arrCOMBOOverview) > 0)
				{
					foreach($arrCOMBOOverview as $arrCurCOMBOOverview)
					{
						$nAmount = sizeof($arrCurCOMBOOverview);
						$strCrazyValue	= '---'.$nAmount;
						$nCounter = 0;
						
						foreach($arrCurCOMBOOverview as $arrCurCOMBOEntries)
						{
							$strCurName = $arrCurCOMBOEntries['strMyName'];
							$nCurState	= 0;
							if ($arrCurCOMBOEntries['nMyState'] == $nCounter)
							{
								$nCurState = 1;
							}
							$nFieldId	= $arrCurCOMBOEntries['nFieldId'];
							$nSlotId	= $arrCurCOMBOEntries['nSlotId'];;
							$nFormId	= $arrCurCOMBOEntries['nFormId'];;
							
							$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
							$nCounter++;
						}
						
						$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'; ";
						mysql_query($strQuery, $nConnection);
					}
				}
				/*
				echo '<pre>';
				print_r($arrRBOverview);
				echo '</pre>';
				
				echo '<pre>';
				print_r($arrCBOverview);
				echo '</pre>';
				
				echo '<pre>';
				print_r($arrCOMBOOverview);
				echo '</pre>';
				*/
				// - - - - - - - - - - - - END EDITEDVALUES - - - - - - - - - - - -
			}
		}
	}
	
	include ('send_circulation.php');
	
	if (($_REQUEST['bRestart']) && ($_REQUEST['MailingList'][0] != 0))
	{	// User has decided to start the circulation from a chosen Station - and not from beginning
		// arrNextUser already exists
		sendToUser($arrNextUser[0], $nCirculationFormID, $arrNextUser[1], 0, $nCirculationHistoryID);
	}
	else
	{
		$arrNextUser = getNextUserInList(-1, $nMailinglistID, -1);
		sendToUser($arrNextUser[0], $nCirculationFormID, $arrNextUser[1], 0, $nCirculationHistoryID);
	}
?>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			location.href = "showcirculation.php?language=<?php echo $language;?>&sort=<?php echo $sort;?>&start=<?php echo $start;?>&archivemode=<?php echo $archivemode;?>&bFirstStart=true";
		}
	//-->
	</script>
</head>
<html>
<body onLoad="siteLoaded()">
</body>