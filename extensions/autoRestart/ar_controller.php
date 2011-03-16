<?php
	$hookValue			= $params['hookValue'];	

	$oldCfId			= $arrRow['nID'];
	$senderId			= $arrRow['nSenderId'];
	$circulationTitle	= $arrRow['strName'];
	$mailinglistId		= $arrRow['nMailingListId'];
	$endAction			= $arrRow['nEndAction'] - $hookValue;
	
	$cpId				= $arrProcessInfo['nID'];
	$oldChId			= $arrProcessInfo['nCirculationHistoryId'];
	$slotId				= $arrProcessInfo['nSlotId'];
	$userId				= $arrProcessInfo['nUserId'];
	$dateInProcessSince	= $arrProcessInfo['dateInProcessSince'];
	$decissionState		= $arrProcessInfo['nDecissionState'];
	$dateDecission		= $arrProcessInfo['dateDecission'];
	
	function arGetInputfield($inputfieldId = false)
	{
		if ($inputfieldId)
		{
			$query	= "SELECT * FROM cf_inputfield WHERE nID = '$inputfieldId' LIMIT 1;";
			$result = mysql_query($query);
			$result	= @mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($result) return $result;
		}
	}
	
	// read cf_circulationhistory
	$query	= "SELECT * FROM cf_circulationhistory WHERE nCirculationFormId = '$oldCfId' LIMIT 1;";
	$result = mysql_query($query);
	$circulationHistory	= @mysql_fetch_array($result, MYSQL_ASSOC);
	
	// write table cf_circulationform
	$query	= "INSERT INTO cf_circulationform values (null, '$senderId', '$circulationTitle', '$mailinglistId', 0, '$endAction', 0)";
	$result	= @mysql_query($query);
	
	// get the circulationform Id
	$query	= "SELECT MAX(nID) as cfId FROM cf_circulationform WHERE bDeleted = 0";
	$result	= @mysql_query($query);
	$row 	= @mysql_fetch_array($result, MYSQL_ASSOC);
	$cfId	= $row['cfId'];
	
	// write table cf_circulationhistory
	$query	= "INSERT INTO cf_circulationhistory values (null, 1, ".time().", '".$circulationHistory['strAdditionalText']."', '$cfId')";
	$result	= @mysql_query($query);
	
	// get the circulationhistory Id
	$query	= "SELECT MAX(nID) as chId FROM cf_circulationhistory";
	$result	= @mysql_query($query);
	$row 	= @mysql_fetch_array($result, MYSQL_ASSOC);
	$chId	= $row['chId'];
	
	
	$fieldvalues = $circulation->getFieldValues($oldCfId, $oldChId);
	foreach ($fieldvalues as $key => $value)
	{
		$inputfieldId	= $value['nInputFieldId'];
		$inputfield		= arGetInputfield($inputfieldId);
		$fieldValue		= $inputfield['strStandardValue'];
		$split			= explode('_', $key);
		$slotId			= $split[1];
		
		$query	= "INSERT INTO cf_fieldvalue values (null, '$inputfieldId', '$fieldValue', '$slotId', '$cfId', '$chId')";
		$result	= @mysql_query($query);
	}
	
	// send the circulation to the first receiver
	require_once '../pages/send_circulation.php';
	
	$arrNextUser = getNextUserInList(-1, $mailinglistId, -1);
	sendToUser($arrNextUser[0], $cfId, $arrNextUser[1], 0, $chId);
?>