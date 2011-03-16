<?php
	require_once '../language_files/language.inc.php';
	require_once '../config/config.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once '../lib/datetime.inc.php';
	require_once 'send_circulation.php';
	require_once 'CCirculation.inc.php';	
?>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
<?php
	$language				= $_REQUEST['language'];
	
	$nCirculationFormID			= $_REQUEST['nCURCirculationFormID'];
	$nCURCirculationProcessID	= $_REQUEST['nCURCirculationProcessID'];
	$nCURMailinglistID			= $_REQUEST['nCURMailinglistID'];
	
	$nMySlotID		= $_REQUEST['nSlotID'];
	$nMyUserID		= $_REQUEST['nUserID'];
	$nMyPosition	= $_REQUEST['nPosition'];
	
	$nUserID		= $_REQUEST['nUserID'];
	
	$objMyCirculation 		= new CCirculation();				
	
	$nCirculationHistoryID	= $objMyCirculation->getCirculationHistoryID($nCURCirculationProcessID);
	$arrCirculationProcess	= $objMyCirculation->getCirculationProcess($nCirculationFormID, $nCirculationHistoryID);
	
	
	$nMyIndex = $_REQUEST['nMyIndex'];
	
	if ($arrCirculationProcess[$nMyIndex] != '') // if true the selected new station is before the current station or the same
	{
		$arrMyCirculationProcess 	= $arrCirculationProcess[$nMyIndex];
		$nMyCirculationProcessID 	= $arrMyCirculationProcess['nID'];
		$tsMyDateInProcessSince		= $arrMyCirculationProcess['dateInProcessSince'];
		
		if ($nMyCirculationProcessID != $nCURCirculationProcessID) // if true the selected station is not the current one
		{
			$arrLaterEntries	= $objMyCirculation->getLaterEntries($nCirculationFormID, $nCirculationHistoryID, $tsMyDateInProcessSince);
						
			$nMax = sizeof($arrLaterEntries);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$arrCurLaterEntry 			= $arrLaterEntries[$nIndex];
				$nDELCirculationProcessID 	= $arrCurLaterEntry['nID'];
				
				$objMyCirculation->deleteMyCirculationProcess($nDELCirculationProcessID);
			}
			
			$arrCurInfos = $objMyCirculation->getMyCirculationProcess($nMyCirculationProcessID);
			$objMyCirculation->deleteMyCirculationProcess($nMyCirculationProcessID);
			
			sendToUser($arrCurInfos['nUserId'], $nCirculationFormID, $arrCurInfos['nSlotId'], 0, $nCirculationHistoryID, time());
			
			
			?>
			<script language="javascript">
				var strParams	= 'circid=<?php echo $nCirculationFormID; ?>&language=<?php echo $language; ?>';
				inpdata	= strParams;
				encodeblowfish();
				location.href = "circulation_detail.php?key=" + outdata;
			</script>
			<?php
		}
		else // false: the selected station is the current station - CyA!
		{
			?>
			<script language="javascript">
				var strParams	= 'circid=<?php echo $nCirculationFormID; ?>&language=<?php echo $language; ?>';
				inpdata	= strParams;
				encodeblowfish();
				location.href = "circulation_detail.php?key=" + outdata;
			</script>
			<?php
		}
	}
	else // false: the selected station is after the current station
	{
		$arrMailinglist 	= $objMyCirculation->getMailinglist($nCURMailinglistID);		// corresponding mailinglist
		$nFormTemplateID 	= $arrMailinglist['nTemplateId'];							// FormTemplate ID
		
		$arrUsers			= $objMyCirculation->getUsers();
		$arrSlots			= $objMyCirculation->getFormslots($nFormTemplateID);		// corresponding formslots
		
		$nMyIndex = 0;
		foreach ($arrSlots as $arrSlot)
		{		
			$nSlotID = $arrSlot['nID'];
			
			$strQuery = "SELECT *
						FROM cf_slottouser
						WHERE nMailingListId = '$nCURMailinglistID' AND nSlotId = '$nSlotID'
						ORDER BY nPosition ASC";
						
			$nResult = mysql_query($strQuery) or die(mysql_error());
			if ($nResult)
			{
				$nIndex = 0;
				while (	$arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
				{
					$arrRows[$nIndex] = $arrRow;

					$arr1337[$nMyIndex]['nSlotID'] = $arrRow['nSlotId'];
					$arr1337[$nMyIndex]['nUserID'] = $arrRow['nUserId'];
					$arr1337[$nMyIndex]['nPosition'] = $arrRow['nPosition'];
					$nMyIndex++;
					$nIndex++;						
				}
			}
		}
		
		$nStart = sizeof($arrCirculationProcess);
		$nMax = 1000;
		
		$tsDateInProcessSince 	= time();
		$tsDateDecission		= time()+2;
		
		$objMyCirculation->setStationToSkipped($nCURCirculationProcessID);
		for ($nIndex = $nStart; $nIndex < $nMax; $nIndex++)
		{
			$arrCurPosition 	= $arr1337[$nIndex];
			$nCurSlotID			= $arrCurPosition['nSlotID'];
			$nCurUserID			= $arrCurPosition['nUserID'];
			$nCurPosition		= $arrCurPosition['nPosition'];
			
			if (($nCurSlotID == $nMySlotID) && ($nCurUserID == $nMyUserID) && ($nCurPosition == $nMyPosition))
			{
				sendToUser($nCurUserID, $nCirculationFormID, $nCurSlotID, $nCURCirculationProcessID, $nCirculationHistoryID, $tsDateInProcessSince);
				$nIndex = 10000;
			}
			else
			{
				$objMyCirculation->addCirculationProcess($nCirculationFormID, $nCirculationHistoryID, $nCurSlotID, $nCurUserID, $tsDateInProcessSince, $tsDateDecission);
			}
			$tsDateInProcessSince++;
			$tsDateDecission++;
		}
		?>
		<script language="javascript">
			var strParams	= 'circid=<?php echo $nCirculationFormID; ?>&language=<?php echo $language; ?>';
			inpdata	= strParams;
			encodeblowfish();
			location.href = "circulation_detail.php?key=" + outdata;
		</script>
		<?php
	}
?>
