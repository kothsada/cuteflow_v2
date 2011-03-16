<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
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
			document.location.href="showtemplates.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"];?>&sortby=<?php echo $_REQUEST["sortby"];?>";
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
				//--- we have there a slot to field relation
				//                SlotId           Position           FieldId
				$arrSlotRelations[$arrKeyValue[0]][$arrKeyValue[2]] = $arrKeyValue[1];
			}
		}
			
		//-----------------------------------------------			
		//--- write to database
		//-----------------------------------------------
		//--- cf_slottofield
		foreach ($arrSlots as $arrSlot)
		{
			//--- first delete all entries for this slot
			$strQuery = "DELETE FROM cf_slottofield WHERE nSlotId=".$arrSlot["nID"];
			$nResult = mysql_query($strQuery, $nConnection);					
			
			//--- After that insert all slot to user relations for this slot
			if ($arrSlotRelations[$arrSlot['nID']])
			{				
				foreach ($arrSlotRelations[$arrSlot['nID']] as $nPos=>$nFieldId)
				{
					$strQuery = "INSERT INTO cf_slottofield values (null, ".$arrSlot["nID"].", $nFieldId, $nPos)";
					$nResult = mysql_query($strQuery, $nConnection);
				}
			}
		}
	?> 
</body>
</html>
