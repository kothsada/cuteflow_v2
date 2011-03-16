<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
	// clear $_REQUEST to ensure that only the encryptet "key" is used
	foreach ($_GET as $key => $value)
	{
		if($key != 'key')
		{
			$_GET[$key]		= '';
		}
	}
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
    require_once '../lib/datetime.inc.php';
	require_once '../pages/send_circulation.php';
	require_once '../pages/CCirculation.inc.php';
				
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	$nConnection2 = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			mysql_select_db($DATABASE_DB, $nConnection2);
			
			//-----------------------------------------------
			//--- Write user inputs to database
			//-----------------------------------------------
			if ($_REQUEST["Answer"] == "false") //decline content
			{
				//--- write the form values to db
				$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrProcessInfo = mysql_fetch_array($nResult);
					}
				}
				
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
					function addCOMBO($RBGroup, $strMyName, $nMyState, $nFieldId, $nSlotId, $nFormId)
					{
						global $arrCOMBOOverview;
						
						$arrCOMBOOverview[$RBGroup][] = array( 'strMyName' => $strMyName, 
																'nMyState' => $nMyState,
																'nFieldId' => $nFieldId,
																'nSlotId' => $nSlotId,
																'nFormId' => $nFormId
																 );
					}
					
					while(list($key, $value) = each($_REQUEST))
					{
						$arrRBContent;
						$arrCBContent;
												
						$arrValues = explode("_", $key);
						if (sizeof($arrValues) > 1)
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
							elseif ($arrValues[0] == 'FILEName')
							{		
								$nFieldId	= $arrValues[1];
								$nSlotId 	= $arrValues[2];
								$nFormId	= $arrValues[3];
								$strMyKey	= $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_9';
								$strMyREGKey= $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_REG';
								$myREGEX = $_REQUEST[$strMyREGKey];
								
								
								$nNumberOfUploads = ($arrValues[4]+1);
								$strMyFile = $_FILES[$strMyKey]['name'];
								if($strMyFile != '')
								{		
									$value			= '---'.$nNumberOfUploads.'---'.$nSlotId.'_'.$nFormId.'_'.$arrProcessInfo["nCirculationHistoryId"].'---'.$strMyFile.'rrrrr'.$myREGEX;
									
									$uploaddir = '../upload/'.$nSlotId.'_'.$nFormId.'_'.$arrProcessInfo["nCirculationHistoryId"].'_'.$nNumberOfUploads.'/';
									@mkdir($uploaddir);
									
									$uploadfile = $uploaddir.$strMyFile;
									
									@move_uploaded_file($_FILES[$strMyKey]['tmp_name'], $uploadfile);
									
									$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
									$nResult = mysql_query($strQuery, $nConnection);
									
									if ($nResult)
							   		{
							   			if (mysql_num_rows($nResult) > 0)
										{
											$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[1]." AND nSlotId=".$arrValues[2]." AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
										}
										else
										{
											$strQuery = "INSERT INTO cf_fieldvalue values(null, ".$arrValues[1].", '$value', ".$arrValues[2].", ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
										}
									}
									mysql_query($strQuery, $nConnection);
								}
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
										if ($myREGEX!='')
										{
											$value	= $value.'rrrrr'.$myREGEX;
										}
										else
										{
											$value = $value;
										}
										break;
									case '2xx':
										$arrMyKey = split('xx',$key);
										
										$strMyKey = $arrMyKey[0];
										
										if ($_REQUEST[$strMyKey] == 'on')
										{
											$value	= 'on';
										}
										else
										{
											$value	= '';
											
											$anotherKey = $strMyKey.'_hidden';
											if ($_REQUEST[$anotherKey] == 'on')
											{
												$value	= 'on';
											}
										}
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
									case '5':
										$value = str_replace("\"", "\\\"", $value);
										$value = str_replace("'", "\\'", $value);
										break;
								}
								
								if (($nFieldType == 1) || ($nFieldType == '2xx') || ($nFieldType == 3) || ($nFieldType == 4) || ($nFieldType == 5))
								{
									$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
									$nResult = mysql_query($strQuery, $nConnection);
									
									if ($nResult)
							   		{
							   			if (mysql_num_rows($nResult) > 0)
										{
											$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[0]." AND nSlotId=".$arrValues[1]." AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
										}
										else
										{
											$strQuery = "INSERT INTO cf_fieldvalue values(null, ".$arrValues[0].", '$value', ".$arrValues[1].", ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
										}
									}
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
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
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
								$nSlotId	= $arrCurRBEntries['nSlotId'];;
								$nFormId	= $arrCurRBEntries['nFormId'];;
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
								$nCounter++;
							}
							
							$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							$nResult = mysql_query($strQuery, $nConnection);
							
							
							if ($nResult)
					   		{
					   			if (mysql_num_rows($nResult) > 0)
								{
									$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
								}
								else
								{
									$strQuery = "INSERT INTO cf_fieldvalue values(null, '$nFieldId', '$strCrazyValue', '$nSlotId', ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
								}
							}
							mysql_query($strQuery, $nConnection);
						}
					}
					
					$strCrazyValue = '';
					if (sizeof($arrCBOverview) > 0)
					{
						foreach($arrCBOverview as $arrCurRBOverview)
						{
							$nAmount = sizeof($arrCurRBOverview);
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
							
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
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
							}
							
							$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							$nResult = mysql_query($strQuery, $nConnection);
							
							if ($nResult)
					   		{
					   			if (mysql_num_rows($nResult) > 0)
								{
									$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
								}
								else
								{
									$strQuery = "INSERT INTO cf_fieldvalue values(null, '$nFieldId', '$strCrazyValue', '$nSlotId', ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
								}
							}
							mysql_query($strQuery, $nConnection);
						}
					}
					
					$strCrazyValue = '';
					if (sizeof($arrCOMBOOverview) > 0)
					{
						foreach($arrCOMBOOverview as $arrCurCOMBOOverview)
						{
							$nAmount = sizeof($arrCurCOMBOOverview);
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
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
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
								$nCounter++;
							}
							
							$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							$nResult = mysql_query($strQuery, $nConnection);
							
							
							if ($nResult)
					   		{
					   			if (mysql_num_rows($nResult) > 0)
								{
									$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
								}
								else
								{
									$strQuery = "INSERT INTO cf_fieldvalue values(null, '$nFieldId', '$strCrazyValue', '$nSlotId', ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
								}
							}
							mysql_query($strQuery, $nConnection);
						}
					}
				
				
				
				//--- send done email to sender if wanted
												
				$strQuery = "UPDATE cf_circulationprocess SET nDecissionState=2, dateDecission='$TStoday' WHERE nID=".$_REQUEST["cpid"];
				mysql_query($strQuery, $nConnection);
				
				$strQuery = "SELECT nEndAction, nSenderId, strName FROM cf_circulationform WHERE nID=".$arrProcessInfo["nCirculationFormId"];
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrRow = mysql_fetch_array($nResult);
												
						$nEndAction = $arrRow["nEndAction"];
						$nSenderId = $arrRow["nSenderId"];
						$strCircName = $arrRow["strName"];
						
						sendMessageToSender($nSenderId, $arrProcessInfo["nUserId"], "done", $strCircName, "REJECT", $_REQUEST["cpid"]);						
					}
				}
			}
			else //accept content
			{
				//--- get the current decission state
				$strQuery = "SELECT nDecissionState FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrProcessInfo = mysql_fetch_array($nResult);
						
//						echo $arrProcessInfo["nDecissionState"];
						if ($arrProcessInfo["nDecissionState"] != 0)
						{
							$bAlreadySend = true;
						}
						else
						{
							$bAlreadySend = false;
						}
					}
				}
				
				if ($bAlreadySend == false)
				{
					$strQuery = "UPDATE cf_circulationprocess SET nDecissionState=1, dateDecission='$TStoday'  WHERE nID=".$_REQUEST["cpid"];
					mysql_query($strQuery, $nConnection);
	
					$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
					$nResult = mysql_query($strQuery, $nConnection);
					if ($nResult)
					{
						if (mysql_num_rows($nResult) > 0)
						{
							$arrProcessInfo = mysql_fetch_array($nResult);
						}
					}
					
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
					function addCOMBO($RBGroup, $strMyName, $nMyState, $nFieldId, $nSlotId, $nFormId)
					{
						global $arrCOMBOOverview;
						
						$arrCOMBOOverview[$RBGroup][] = array( 'strMyName' => $strMyName, 
																'nMyState' => $nMyState,
																'nFieldId' => $nFieldId,
																'nSlotId' => $nSlotId,
																'nFormId' => $nFormId
																 );
					}
					
					while(list($key, $value) = each($_REQUEST))
					{
						$arrRBContent;
						$arrCBContent;
												
						$arrValues = explode("_", $key);
						if (sizeof($arrValues) > 1)
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
							elseif ($arrValues[0] == 'FILEName')
							{
								$nFieldId	= $arrValues[1];
								$nSlotId 	= $arrValues[2];
								$nFormId	= $arrValues[3];
								$strMyKey	= $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_9';
								$strMyREGKey= $nFieldId.'_'.$nSlotId.'_'.$nFormId.'_REG';
								$myREGEX = $_REQUEST[$strMyREGKey];
								
								
								$nNumberOfUploads = ($arrValues[4]+1);
								$strMyFile = $_FILES[$strMyKey]['name'];
								if($strMyFile != '')
								{		
									$value			= '---'.$nNumberOfUploads.'---'.$nSlotId.'_'.$nFormId.'_'.$arrProcessInfo["nCirculationHistoryId"].'---'.$strMyFile.'rrrrr'.$myREGEX;
									
									$uploaddir = '../upload/'.$nSlotId.'_'.$nFormId.'_'.$arrProcessInfo["nCirculationHistoryId"].'_'.$nNumberOfUploads.'/';
									@mkdir($uploaddir);
									
									$uploadfile = $uploaddir.$strMyFile;
									
									@move_uploaded_file($_FILES[$strMyKey]['tmp_name'], $uploadfile);
									
									$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
									$nResult = mysql_query($strQuery, $nConnection);
									
									if ($nResult)
							   		{
							   			if (mysql_num_rows($nResult) > 0)
										{
											$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[1]." AND nSlotId=".$arrValues[2]." AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
										}
										else
										{
											$strQuery = "INSERT INTO cf_fieldvalue values(null, ".$arrValues[1].", '$value', ".$arrValues[2].", ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
										}
									}
									mysql_query($strQuery, $nConnection);
								}
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
										if ($myREGEX!='')
										{
											$value	= $value.'rrrrr'.$myREGEX;
										}
										else
										{
											$value = $value;
										}
										break;
									case '2xx':
										$arrMyKey = split('xx',$key);
										
										$strMyKey = $arrMyKey[0];
										
										if ($_REQUEST[$strMyKey] == 'on')
										{
											$value	= 'on';
										}
										else
										{
											$value	= '';
											
											$anotherKey = $strMyKey.'_hidden';
											if ($_REQUEST[$anotherKey] == 'on')
											{
												$value	= 'on';
											}
										}
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
									case '5':
										$value = str_replace("\"", "\\\"", $value);
										$value = str_replace("'", "\\'", $value);
										break;
								}
								
								if (($nFieldType == 1) || ($nFieldType == '2xx') || ($nFieldType == 3) || ($nFieldType == 4) || ($nFieldType == 5))
								{
									$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
									$nResult = mysql_query($strQuery, $nConnection);
									
									if ($nResult)
							   		{
							   			if (mysql_num_rows($nResult) > 0)
										{
											$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[0]." AND nSlotId=".$arrValues[1]." AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
										}
										else
										{
											$strQuery = "INSERT INTO cf_fieldvalue values(null, ".$arrValues[0].", '$value', ".$arrValues[1].", ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
										}
									}
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
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
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
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
								$nCounter++;
							}
							
							$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							$nResult = mysql_query($strQuery, $nConnection);
							
							
							if ($nResult)
					   		{
					   			if (mysql_num_rows($nResult) > 0)
								{
									$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
								}
								else
								{
									$strQuery = "INSERT INTO cf_fieldvalue values(null, '$nFieldId', '$strCrazyValue', '$nSlotId', ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
								}
							}
							mysql_query($strQuery, $nConnection);
						}
					}
					
					$strCrazyValue = '';
					if (sizeof($arrCBOverview) > 0)
					{
						foreach($arrCBOverview as $arrCurRBOverview)
						{
							$nAmount = sizeof($arrCurRBOverview);
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
							
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
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
							}
							
							$strQuery = "SELECT nID FROM cf_fieldvalue WHERE nInputFieldId=$nFieldId AND nSlotId=$nSlotId AND nFormId=$nFormId AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							$nResult = mysql_query($strQuery, $nConnection);
							
							if ($nResult)
					   		{
					   			if (mysql_num_rows($nResult) > 0)
								{
									$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
								}
								else
								{
									$strQuery = "INSERT INTO cf_fieldvalue values(null, '$nFieldId', '$strCrazyValue', '$nSlotId', ".$arrProcessInfo["nCirculationFormId"].",".$arrProcessInfo["nCirculationHistoryId"].")";
								}
							}
							mysql_query($strQuery, $nConnection);
						}
					}
					
					$strCrazyValue = '';
					if (sizeof($arrCOMBOOverview) > 0)
					{
						foreach($arrCOMBOOverview as $arrCurCOMBOOverview)
						{
							$nAmount = sizeof($arrCurCOMBOOverview);
							//$strCrazyValue	= '---'.$nAmount;
							$strCrazyValue = '';
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
								
								//$strCrazyValue = $strCrazyValue.'---'.$strCurName.'---'.$nCurState;
								$strCrazyValue = $strCrazyValue.$nCurState.'---';
								$nCounter++;
							}
							
							$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$strCrazyValue' WHERE nInputFieldId= '$nFieldId' AND nSlotId= '$nSlotId' AND nFormId=".$arrProcessInfo["nCirculationFormId"]." AND nCirculationHistoryId=".$arrProcessInfo["nCirculationHistoryId"];
							mysql_query($strQuery, $nConnection);
						}
					}
					
/////////////////////////////////////////////////////////////////////////////////////////////////////					
					
					//-----------------------------------------------
					//--- send mail to next user in list
					//-----------------------------------------------
					$strQuery = "SELECT * FROM cf_mailinglist INNER JOIN cf_circulationform ON cf_mailinglist.nID = cf_circulationform.nMailingListId WHERE cf_circulationform.nID=".$arrProcessInfo["nCirculationFormId"];
					$nResult = mysql_query($strQuery, $nConnection);
					if ($nResult)
					{
						if (mysql_num_rows($nResult) > 0)
						{
							$arrRow = mysql_fetch_array($nResult);
							
							$nListId = $arrRow[0];
						}
					}
					
					
					
					$arrCirculationProcess 	= $arrProcessInfo;
					$nCirculationProcessId 	= $arrCirculationProcess['nID'];
					$nCirculationFormId 	= $arrCirculationProcess['nCirculationFormId'];
					$nSlotId			 	= $arrCirculationProcess['nSlotId'];
					$nUserId 				= $arrCirculationProcess['nUserId'];
					$nIsSubtituteOf	 		= $arrCirculationProcess['nIsSubstitiuteOf'];
					$nCirculationHistoryId 	= $arrCirculationProcess['nCirculationHistoryId'];
					$dateInProcessSince		= $arrCirculationProcess['dateInProcessSince'];
					
					// get the Position in current Slot
					$query 		= "SELECT nMailingListId FROM cf_circulationform WHERE nID = '$nCirculationFormId' LIMIT 1;";
					$result 	= mysql_query($query, $nConnection);
					$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
					$nMailingListId = $arrResult['nMailingListId'];
					
					$query 		= "SELECT * FROM cf_slottouser WHERE nSlotId = '$nSlotId' AND nMailingListId = '$nMailingListId' AND nUserId = '$nUserId' LIMIT 1;";
					$result 	= mysql_query($query, $nConnection);
					$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
					
					if ($nIsSubtituteOf == 0)
					{	// the current user is no substitute
						if ($arrResult['nID'] == '')
						{	// it's the sender of the circulation!!!
							$arrNextUser = getNextUserInList(-2, $nListId, $nSlotId);
						}
						else
						{
							$arrNextUser = getNextUserInList($nUserId, $nListId, $nSlotId);
						}
					}
					else
					{	// user is a substitute
						// let's see who this substitute belongs to
						// it's NOT saved in "nIsSubstituteOf" -.-
			
						$strQuery 	= "SELECT MAX(dateInProcessSince) as nMaxDateInProcessSince FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormId' AND nIsSubstitiuteOf = '0' AND dateInProcessSince < '$dateInProcessSince' LIMIT 1;";
						$result 	= mysql_query($strQuery, $nConnection);
						$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
						
						$strQuery 	= "SELECT nUserId FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormId' AND dateInProcessSince = '".$arrResult['nMaxDateInProcessSince']."' LIMIT 1;";
						$result 	= mysql_query($strQuery, $nConnection);
						$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
						
						$nSubsUserId = $arrResult['nUserId'];
						
						$arrNextUser = getNextUserInList($nSubsUserId, $nListId, $nSlotId);
					}
					
					if ($arrNextUser[0] != "")
					{
						if ($arrNextUser[0] == -2)
						{	// let's get the Sender User ID
							$objCirculation	= new CCirculation();
							$arrSender 		= $objCirculation->getSenderDetails($nCirculationFormId);
							$arrNextUser[0] = $arrSender['nID'];
						}
						
						sendToUser($arrNextUser[0], $arrProcessInfo["nCirculationFormId"], $arrNextUser[1], 0, $arrProcessInfo["nCirculationHistoryId"]);
						
						if ($arrNextUser[2] !== false) {
							// Slot has changed
							// Send a notification if this is wished
											
							$strQuery = "SELECT * FROM cf_circulationform WHERE nID=".$arrProcessInfo["nCirculationFormId"];
							$nResult = mysql_query($strQuery, $nConnection);
							if ($nResult)
							{
								if (mysql_num_rows($nResult) > 0)
								{
									$arrRow = mysql_fetch_array($nResult);
									
									$nSenderId		= $arrRow["nSenderId"];
									$strCircName	= $arrRow["strName"];
									$nEndAction		= $arrRow["nEndAction"];
								}
							}
							
							$strQuery = "SELECT * FROM cf_formslot WHERE nID=".$arrNextUser[2];
							$nResult = mysql_query($strQuery, $nConnection);
							if ($nResult)
							{
								if (mysql_num_rows($nResult) > 0)
								{
									$arrRow = mysql_fetch_array($nResult);
									$slotname = $arrRow['strName'];
								}
							}
							
							if ( ($nEndAction & 8) == 8 ) {
								sendMessageToSender($nSenderId, $arrProcessInfo["nUserId"], "done", $strCircName, "ENDSLOT", $_REQUEST["cpid"], $slotname);
							}
						}
					}
					else
					{
						//--- send done email to sender if wanted
						$strQuery = "SELECT * FROM cf_circulationform WHERE nID=".$arrProcessInfo["nCirculationFormId"];
						$nResult = mysql_query($strQuery, $nConnection);
						if ($nResult)
						{
							if (mysql_num_rows($nResult) > 0)
							{
								$arrRow = mysql_fetch_array($nResult);
								
								$nEndAction		= $arrRow["nEndAction"];
								$nSenderId		= $arrRow["nSenderId"];
								$strCircName	= $arrRow["strName"];
								
								
								// check the hook CF_ENDACTION
									$circulation 	= new CCirculation();
									$endActions		= $circulation->getExtensionsByHookId('CF_ENDACTION');
									
									if ($endActions)
									{
										foreach ($endActions as $endAction)
										{
											$params		= $circulation->getEndActionParams($endAction);
											$hookValue	= (int) $params['hookValue'];
											
											if (($nEndAction & $hookValue) == $hookValue)
											{
												require_once $params['filename'];
											}
										}
									}
								
								$nShouldArchived 	= $nEndAction & 2;
								$nShouldMailed 		= $nEndAction & 1;
								$nShouldDeleted 	= 4;
								
								if ($nShouldMailed == 1)
								{
									sendMessageToSender($nSenderId, $arrProcessInfo["nUserId"], "done", $strCircName, "SUCCESS", $_REQUEST["cpid"]);
								}
								
								if ($nShouldArchived == 2)
								{	// archive the circulation
									$strQuery = "UPDATE cf_circulationform SET bIsArchived=1 WHERE nID=".$arrProcessInfo["nCirculationFormId"];
									mysql_query($strQuery, $nConnection);
								}
								elseif ($nShouldDeleted & $nEndAction)
								{	// delete circulation
									$query = "UPDATE cf_circulationform SET bDeleted = 1 WHERE nID = ".$arrProcessInfo['nCirculationFormId'];
									mysql_query($query, $nConnection);
								}
							}
						}
					}
				}
			}
		}
	}	
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="<?php echo $CUTEFLOW_SERVER;?>/pages/format.css" type="text/css">
</head>
<body>
	<br>
	<br>
	<div align="center">
		<table class="note" width="350px" border="0">
			<tr>
				<td valign="top"><img src="<?php echo $CUTEFLOW_SERVER;?>/images/stop2.png" height="48" width="48" alt="stop2"></td>
				<td>
					<?php 
						if ($bAlreadySend == 1)
						{
							echo $MAIL_CONTENT_SENT_ALREADY;
						}
						else
						{
							if ($_REQUEST["Answer"] == "true")
							{
								echo $MAIL_ACK;
							}
							else
							{
								echo $MAIL_NACK;
							}
						}
					?>
				</td>
			</tr>
			<tr>
				<td style="height:10px" colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<?php 
					$strParams				= 'language='.$_REQUEST["language"].'&start=1&archivemode=0&bFirstStart=true&bOwnCirculations=1';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= '/pages/showcirculation.php?key='.$strEncyrptedParams;
				?>
				<td colspan="2" align="right"><a href="<?php echo $CUTEFLOW_SERVER.$strEncryptedLinkURL;?>">&gt;&gt;</a></td>
			</tr>
		</table>	
	</div>
</body>
</html>
