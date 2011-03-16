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
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once '../lib/datetime.inc.php';
	require_once 'send_circulation.php';
	
	$nCirculationFormId 	= strip_tags($_REQUEST['circid']);
	$nCirculationProcessId 	= strip_tags($_REQUEST['cpid']);
	
	// get all entries from the current circulation process
	$strQuery 	= "SELECT *
					FROM cf_circulationprocess
					WHERE nCirculationFormId = '$nCirculationFormId'
					ORDER BY dateInProcessSince DESC;";
	$nResult 	= mysql_query($strQuery);
	
	if ($nResult)
	{
		while ($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
		{
			$arrCirculationProcesses[] = $arrRow;		
		}
	}
	
	if ($arrCirculationProcesses[0]['nIsSubstitiuteOf'] != 0)
	{	// current station is a substitute
		// go through the array till we find the first entry with a decission state different from "8"
		// if we found this the entry BEFORE the one we found is the user we're looking for
	
		$nMax = sizeof($arrCirculationProcesses);
		for ($nIndex = 1; $nIndex < $nMax; $nIndex++)
		{
			$arrCirculationProcess = $arrCirculationProcesses[$nIndex];
			
			if ($arrCirculationProcess[nDecissionState] != 8)
			{	// found the entry AFTER the user we search for
				$arrCPResult = $arrCirculationProcesses[($nIndex-1)];
				
				// let's end the search
				$nIndex = $nMax;
			}
		}
	}
	else
	{	// current station is no substitute
		$arrCPResult = $arrCirculationProcesses[0];
	}
	
	$nCirculationHistoryId 	= $arrCPResult['nCirculationHistoryId'];
	$nSlotId 				= $arrCPResult['nSlotId'];
	$nUserId				= $arrCPResult['nUserId'];
	
	// we need the ID of the Mailinglist

	$strQuery 	= "SELECT nMailingListId
					FROM cf_circulationform
					WHERE nID = '$nCirculationFormId' LIMIT 1;";
	$nResult 	= mysql_query($strQuery);
	
	if ($nResult)
	{
		$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC);
		$nMailinglistId = $arrRow['nMailingListId'];
	}
	
	// get the next User
	$arrNextUser = getNextUserInList($nUserId, $nMailinglistId, $nSlotId);
	
	// send the message
	if ($arrNextUser[0] != '')
	{
		// set current user state to "skipped"
		$strQuery = "	UPDATE cf_circulationprocess 
						SET nDecissionState = '4',
							dateDecission = '$TStoday' 
						WHERE nID = '$nCirculationProcessId'";
		mysql_query($strQuery, $nConnection);
		
		// send
		sendToUser($arrNextUser[0], $nCirculationFormId, $arrNextUser[1], 0, $nCirculationHistoryId);
		
		if ($arrNextUser[2] !== false) {
			// Slot has changed
			// Send a notification if this is wished
							
			$strQuery = "SELECT * FROM cf_circulationform WHERE nID=".$nCirculationFormId;
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrRow = mysql_fetch_array($nResult);
					
					//print_r($arrRow);
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
		$strQuery = "SELECT * FROM cf_circulationform WHERE nID=".$arrCirculationProcesses["nCirculationFormId"];
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
?>
<html>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
		$saved=true;
	?>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			var strParams	= "circid=<?php echo $_REQUEST["circid"];?>&language=<?php echo $_REQUEST["language"];?>&sortby=<?php echo $_REQUEST["sortby"];?>&start=<?php echo $_REQUEST["start"];?>";
			inpdata	= strParams;
			encodeblowfish();
			location.href = "circulation_detail.php?key=" + outdata;
		}
	//-->
	</script>
</head>
<body onLoad="siteLoaded()">
</body>
</html>