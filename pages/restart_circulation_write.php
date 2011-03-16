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
	
	//--- write circulation to database
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			session_start();
			$nSenderId = $_SESSION["SESSION_CUTEFLOW_USERID"];
			$dateSending = mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"));
		
			$nEndAction = 0;
			
			if ($_REQUEST['SuccessMail'] == 'on') $nEndAction += 1;
			if ($_REQUEST['SuccessArchive'] == 'on') $nEndAction += 2;
			if ($_REQUEST['SuccessDelete'] == 'on') $nEndAction += 4;
			
			$cfid = $_REQUEST['circid'];
			
			//-----------------------------------------
			//--- Write next history
			//-----------------------------------------
			$strQuery = "SELECT MAX(nRevisionNumber) FROM cf_circulationhistory WHERE nCirculationFormId=".$cfid;
			$nResult = mysql_query($strQuery, $nConnection);
			
			if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
   				{
   					$arrRow = mysql_fetch_array($nResult);
   					$nRevisionNumber = $arrRow[0] +1;
   				}
    		}
			
			$strQuery = "INSERT INTO cf_circulationhistory VALUES(null, $nRevisionNumber, '$dateSending', '".$_REQUEST["strAdditionalText"]."', $cfid)";
			mysql_query($strQuery, $nConnection);
			
			$strQuery = "SELECT MAX(nID) FROM cf_circulationhistory";
			$nResult = mysql_query($strQuery, $nConnection);
			
			if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
   				{
   					$arrRow = mysql_fetch_array($nResult);
					$chid = $arrRow[0];
				}
			}			
			
			//-----------------------------------------
			//--- write the attachments
			//-----------------------------------------
			$strFolderName = "../attachments/cf_$cfid/";
			@mkdir($strFolderName);
			
			$strFolderName = $strFolderName.time()."/";
			@mkdir($strFolderName);
			
			if ($_FILES["attachment1"]["name"] != "")
			{
				move_uploaded_file($_FILES["attachment1"]["tmp_name"], $strFolderName.$_FILES["attachment1"]["name"]);
				$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment1"]["name"]."', ".$chid.")";
				mysql_query($strQuery, $nConnection);				
			}
			if ($_FILES["attachment2"]["name"] != "")
			{
				move_uploaded_file($_FILES["attachment2"]["tmp_name"], $strFolderName.$_FILES["attachment2"]["name"]);
				$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment2"]["name"]."', ".$chid.")";
				mysql_query($strQuery, $nConnection);				
			}
			if ($_FILES["attachment3"]["name"] != "")
			{
				move_uploaded_file($_FILES["attachment3"]["tmp_name"], $strFolderName.$_FILES["attachment3"]["name"]);
				$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment3"]["name"]."', ".$chid.")";
				mysql_query($strQuery, $nConnection);				
			}
			if ($_FILES["attachment4"]["name"] != "")
			{
				move_uploaded_file($_FILES["attachment4"]["tmp_name"], $strFolderName.$_FILES["attachment4"]["name"]);
				$strQuery = "INSERT INTO cf_attachment values (null, '$strFolderName".$_FILES["attachment4"]["name"]."', ".$chid.")";
				mysql_query($strQuery, $nConnection);				
			}
			
			
			
			//inserting standard values
					
			$nCirculationHistoryID 	= $chid;
			$nCirculationFormID 	= $cfid;
			$nMailinglistID			= $_REQUEST['listid'];
			
			$strQuery 	= "SELECT * FROM cf_mailinglist WHERE nID = '$nMailinglistID' LIMIT 1;";
			$nResult 	= mysql_query($strQuery, $nConnection);
			$arrMailinglist = mysql_fetch_array($nResult,MYSQL_ASSOC);
							
			$nFormTemplateID = $arrMailinglist['nTemplateId'];
			
			$strQuery	= "SELECT * FROM cf_formslot WHERE nTemplateId = '$nFormTemplateID' ORDER BY nSlotNumber ASC;";	
			$result		= mysql_query($strQuery) or die (mysql_error());
			
			$nIndex = 0;
			while ($arrRow = mysql_fetch_row($result))
			{
				$arrFormSlots[$nIndex]['nID'] 			= $arrRow[0];
				$arrFormSlots[$nIndex]['strName'] 		= $arrRow[1];
				$arrFormSlots[$nIndex]['nTemplateId'] 	= $arrRow[2];
				$arrFormSlots[$nIndex]['nSlotNumber'] 	= $arrRow[3];
				
				$nIndex++;
			}
			
			$nMax = sizeof($arrFormSlots);
			$nMyIndex = 0;
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$nCurFormSlotID = $arrFormSlots[$nIndex]['nID'];
				
				$strQuery	= "SELECT * FROM cf_slottofield WHERE nSlotId = '$nCurFormSlotID';";	
				$result		= mysql_query($strQuery) or die (mysql_error());
				
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
				$nResult 			= mysql_query($strQuery, $nConnection);
				$arrCurInputField 	= mysql_fetch_array($nResult,MYSQL_ASSOC);
				
				$strCurStandardValue	= $arrCurInputField['strStandardValue'];
				
				$strQuery = "INSERT INTO cf_fieldvalue values( null, '$nCurInputFieldID', '$strCurStandardValue', '$nCurFormSlotID', '$nCirculationFormID' , '$nCirculationHistoryID' )";
				$nResult = mysql_query($strQuery, $nConnection);			
			}
		}
	}
	
	if ($_REQUEST['EditValues'])
	{
		?>
		<script language="JavaScript">
		<!--
			location.href="restart_circulation_values.php?chid=<?php echo $chid; ?>&cfid=<?php echo $cfid; ?>&listid=<?php echo $_REQUEST["listid"]; ?>&language=<?php echo $_REQUEST["language"];?>&sort=<?php echo $_REQUEST["sort"];?>&start=<?php echo $_REQUEST["start"];?>&archivemode=<?php echo $_REQUEST["archivemode"];?>";
		//-->
		</script>
		<?php
		die;
	}
	
	include_once ("send_circulation.php");
	
	$arrNextUser = getNextUserInList(-1, $_REQUEST["listid"], -1);
	
	sendToUser($arrNextUser[0], $cfid, $arrNextUser[1], 0, $chid);
?>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			location.href = "showcirculation.php?language=<?php echo $_REQUEST["language"];?>&sort=<?php echo $_REQUEST["sort"];?>&start=<?php echo $_REQUEST["start"];?>&archivemode=<?php echo $_REQUEST["archivemode"];?>";
		}
	//-->
	</script>
</head>
<html>
<body onLoad="siteLoaded()">
</body>