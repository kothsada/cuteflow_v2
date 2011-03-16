<?php
	require_once '../language_files/language.inc.php';
	require_once '../config/config.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once '../lib/datetime.inc.php';
	require_once 'send_circulation.php';
	require_once 'CCirculation.inc.php';
	
	$language				= $_REQUEST['language'];
	
	$nCirculationFormID			= $_REQUEST['nCURCirculationFormID'];
	$nCURCirculationProcessID	= $_REQUEST['nCURCirculationProcessID'];
	$nCURMailinglistID			= $_REQUEST['nCURMailinglistID'];

	$nUserID			= $_REQUEST['nUserID']; // Id of selected User
	
	$objMyCirculation 		= new CCirculation();				
	
	$nCirculationHistoryID	= $objMyCirculation->getCirculationHistoryID($nCURCirculationProcessID);	
	
	//-----------------------------------------------
	//--- set user state to "skipped"
	//-----------------------------------------------
	$strQuery = "UPDATE cf_circulationprocess set nDecissionState = 8, dateDecission = '$TStoday' WHERE nID = '$nCURCirculationProcessID'";
	mysql_query($strQuery, $nConnection);		
	
	
	// - get the UserId of the current Station
	$arrCURCirculationProcess = $objMyCirculation->getMyCirculationProcess($nCURCirculationProcessID);
	$nCURStation_UserID = $arrCURCirculationProcess['nUserId'];
	$nSlotID = $arrCURCirculationProcess['nSlotId'];
	
	
	
	
	// send
	sendToUser($nUserID, $nCirculationFormID, $nSlotID, $nCURCirculationProcessID, $nCirculationHistoryID);
	
	
	//?
	//$strQuery 	= "INSERT INTO cf_circulationprocess VALUES(null, '$nCirculationFormID', '$nSlotID', '$nUserID', time(), 0, '$nCURStation_UserID', '$nCirculationHistoryID')";
	//echo "Query: ".$strQuery."<br>";
	//mysql_query($strQuery, $nConnection);	
	

	?>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script language="javascript">
		var strParams	= "circid=<?php echo $nCirculationFormID; ?>&language=<?php echo $language; ?>";
		inpdata	= strParams;
		encodeblowfish();
		location.href = "circulation_detail.php?key=" + outdata;
	</script>