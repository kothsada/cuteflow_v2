<?php
	/** Copyright (c) 2003, 2004 EMEDIA OFFICE GmbH. All rights reserved.
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
	*  o Neither the name of EMEDIA OFFICE GmbH nor the names of 
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
    include ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
    include ("../lib/datetime.inc.php");
	
	$nCirculationFormID 	= $_REQUEST['nCirculationFormID'];
	$nCirculationHistoryID 	= $_REQUEST['nCirculationHistoryID']; 
	$cfid = $nCirculationFormID;
	$chid = $nCirculationHistoryID;
		
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	$nConnection2 = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			mysql_select_db($DATABASE_DB, $nConnection2);
									
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
				
				$nSlotId 	= $arrValues[1];
				$nFormId	= $arrValues[2];
				
				$strMyFile = $_FILES[$key]['name'];
				if($strMyFile != '')
				{				
					$nNumberOfUpload = 1;
					$value			= '---'.$nNumberOfUpload.'---'.$nSlotId.'_'.$nFormId.'_'.$nCirculationHistoryID.'---'.$strMyFile;
					
					$uploaddir = '../upload/'.$nSlotId.'_'.$nFormId.'_'.$nCirculationHistoryID.'_'.$nNumberOfUpload.'/';
					@mkdir($uploaddir);
					
					$uploadfile = $uploaddir.$strMyFile;
					
					move_uploaded_file($_FILES[$key]['tmp_name'], $uploadfile);
					
					$strQuery = "UPDATE cf_fieldvalue SET strFieldValue='$value' WHERE nInputFieldId=".$arrValues[0]." AND nSlotId=".$arrValues[1]." AND nFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCirculationHistoryID'; ";
					mysql_query($strQuery, $nConnection);
				}					
			}
			
			while(list($key, $value) = each($_REQUEST))
			{
				$arrRBContent;
				$arrCBContent;
				
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
								$value	= $value;
								break;
							case '2':
								$value	= $value;
								break;	
							case '3':
								$value	= 'xx'.$nFieldContentType.'xx'.$value;
								break;
							case '4':
								$value 	= 'xx'.$nFieldContentType.'xx'.$value;
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
			
			include ("send_circulation.php");
	
			$arrNextUser = getNextUserInList(-1, $_REQUEST["listid"], -1);
			
			sendToUser($arrNextUser[0], $cfid, $arrNextUser[1], 0, $chid);			
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
	<!--
		function siteLoaded()
		{
			location.href = "showcirculation.php?language=<?php echo $_REQUEST['language']; ?>&start=1&archivemode=0";
		}
	//-->
	</script>
</head>
<body onLoad="siteLoaded()">
</body>
</html>