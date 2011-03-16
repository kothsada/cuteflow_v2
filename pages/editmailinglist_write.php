<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
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
	
	require_once '../language_files/language.inc.php';
	require_once '../config/config.inc.php';
?>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			location.href = "showmaillist.php?language=<?php echo $_REQUEST["language"];?>&sort=<?php echo $_REQUEST["sort"];?>&start=<?php echo $_REQUEST["start"];?>";
		}
	//-->
	</script>
</head>
<html>
<body onLoad="siteLoaded()">
	<?php
		//-----------------------------------------------
    	//--- get all slots for the given template
        //-----------------------------------------------
		$arrSlots = array();
		$arrSlotRelations = array();
		
        $strQuery = "SELECT * FROM cf_formslot WHERE nTemplateID=".$_REQUEST["templateid"]."  ORDER BY nSlotNumber ASC";
    	$nResult = mysql_query($strQuery, $nConnection);
    	if ($nResult)
    	{
    		if (mysql_num_rows($nResult) > 0)
    		{
    			while (	$arrRow = mysql_fetch_array($nResult))
    			{
    				$arrSlots[] = $arrRow;
					$arrSlotRelations[] = array();
    			}
    		}
    	}	
    				
		//-----------------------------------------------
		//--- create the array with all slot to user 
		//--- relations
		//-----------------------------------------------
		while(list($key, $value) = each($_REQUEST))
		{
			$arrKeyValue = explode ("_", $value);
			
			if (sizeof($arrKeyValue) == 3)
			{
				//--- we have there a slot to user relation
				//                SlotId           Position           UserId
				$arrSlotRelations[$arrKeyValue[0]][$arrKeyValue[2]] = $arrKeyValue[1];
			}
		}
		
		//-----------------------------------------------
		//--- Create permission value
		//-----------------------------------------------
		$permissions = 0;
		if ($_REQUEST['permission']['delete']['admin'] == 1) {
			$permissions |= 1;
		}
		if ($_REQUEST['permission']['archive']['admin'] == 1) {
			$permissions |= 2;
		}
		if ($_REQUEST['permission']['stop']['admin'] == 1) {
			$permissions |= 4;
		}
		if ($_REQUEST['permission']['details']['admin'] == 1) {
			$permissions |= 8;
		}
		
		if ($_REQUEST['permission']['delete']['allowedsender'] == 1) {
			$permissions |= 16;
		}
		if ($_REQUEST['permission']['archive']['allowedsender'] == 1) {
			$permissions |= 32;
		}
		if ($_REQUEST['permission']['stop']['allowedsender'] == 1) {
			$permissions |= 64;
		}
		if ($_REQUEST['permission']['details']['allowedsender'] == 1) {
			$permissions |= 128;
		}
		
		if ($_REQUEST['permission']['delete']['allsender'] == 1) {
			$permissions |= 256;
		}
		if ($_REQUEST['permission']['archive']['allsender'] == 1) {
			$permissions |= 512;
		}
		if ($_REQUEST['permission']['stop']['allsender'] == 1) {
			$permissions |= 1024;
		}
		if ($_REQUEST['permission']['details']['allsender'] == 1) {
			$permissions |= 2048;
		}
		
		if ($_REQUEST['permission']['delete']['receiver'] == 1) {
			$permissions |= 4096;
		}
		if ($_REQUEST['permission']['archive']['receiver'] == 1) {
			$permissions |= 8192;
		}
		if ($_REQUEST['permission']['stop']['receiver'] == 1) {
			$permissions |= 16384;
		}
		if ($_REQUEST['permission']['details']['receiver'] == 1) {
			$permissions |= 32768;
		}
		
		//-----------------------------------------------			
		//--- write to database
		//-----------------------------------------------
		//--- cf_mailinglist
		if ($_REQUEST["listid"] == -1)
		{
			$strQuery = "INSERT INTO cf_mailinglist values(null, '".$_REQUEST["strName"]."', ".$_REQUEST["templateid"].", '0', '0', '0', {$permissions})";
			$nResult = mysql_query($strQuery, $nConnection) or die(mysql_error());
			
			$strQuery = "SELECT MAX(nID) FROM cf_mailinglist";
			$nResult = mysql_query($strQuery, $nConnection);
			
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrRow = mysql_fetch_array($nResult);
					$listid = $arrRow[0];
				}
			}
		}
		else
		{
			$strQuery = "UPDATE cf_mailinglist SET strName='".$_REQUEST["strName"]."', nTemplateId=".$_REQUEST["templateid"].", permissions={$permissions} WHERE nID=".$_REQUEST["listid"];
			$nResult = mysql_query($strQuery, $nConnection);
			$listid = $_REQUEST["listid"];
		}
		
		// Save allowed sender 
		$allowed_sender = explode('&&&', $_REQUEST['allowed_sender']);
		
		$strQuery = "DELETE FROM cf_allowed_sender WHERE mailinglist_id=".$listid;
		$nResult = mysql_query($strQuery, $nConnection);
		
		foreach ($allowed_sender as $sender) {
			$query = "INSERT INTO cf_allowed_sender VALUES({$listid}, {$sender})";
			$nResult = mysql_query($query, $nConnection);	
		}
		
		//--- cf_slottouser
		foreach ($arrSlots as $arrSlot)
		{
			//--- first delete all entries for this slot
			$strQuery = "DELETE FROM cf_slottouser WHERE nMailingListId = ".$listid." AND nSlotId=".$arrSlot["nID"];
			$nResult = mysql_query($strQuery, $nConnection);					
			
			//--- After that insert all slot to user relations for this slot
			if ($arrSlotRelations[$arrSlot['nID']])
			{					
				foreach ($arrSlotRelations[$arrSlot['nID']] as $nPos=>$nUserId)
				{
					$strQuery = "INSERT INTO cf_slottouser values (null, ".$arrSlot["nID"].", ".$listid.", $nUserId, $nPos)";
					$nResult = mysql_query($strQuery, $nConnection);
				}
			}
		}
	?> 
</body>
</html>
