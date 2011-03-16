<?php

	include	('../config/config.inc.php');
	include	('../language_files/language.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');	
	
	$nCirculationFormID		= $_REQUEST['nCirculationFormID'];
	$nCirculationProcessID	= $_REQUEST['nCirculationProcessID'];
	$language				= $_REQUEST['strLanguage'];
	$nMailinglistID			= $_REQUEST['nMailinglistID'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script language="JavaScript">
	<!--
		function doOk()
		{
			if (document.getElementById('MailingList').value != 0)
			{
				var Value = document.getElementById('MailingList').value;
				
				arrValues = Value.split('---');
				
				var nMyIndex	= arrValues[0];
				var nSlotID 	= arrValues[1];
				var nUserID 	= arrValues[2];
				var nPosition 	= arrValues[3];
				
				opener.changeCurrentStation(nMyIndex, nSlotID, nUserID, nPosition);
				window.close();
			}	
			else
			{
				alert ('<?php echo str_replace("'", "\'", $USER_SELECT_NO_SELECT);?>');				
			}
		}
	//-->
	</script>	
</head>
<body topmargin="0" leftmargin="0" style="margin-top: 5px;">
	<div align="center">
		<form action="" id="BrowseMailingList">
    		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center">
    			<tr>
    				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
						<?php echo $CHOOSE_STATION;?>
					</td>
    			</tr>
				<tr>
					<td style="padding: 8px 4px 8px 4px;">
						<?php
							$objMyCirculation 	= new CCirculation();				
		
							$arrMailinglist 	= $objMyCirculation->getMailinglist($nMailinglistID);		// corresponding mailinglist
							$nFormTemplateID 	= $arrMailinglist['nTemplateId'];							// FormTemplate ID
							
							$arrUsers			= $objMyCirculation->getUsers();
							
							$arrSlots			= $objMyCirculation->getFormslots($nFormTemplateID);		// corresponding formslots
						?>
						<select id="MailingList" class="FormInput" size="10" style="width:250px;">
        					<?php
        					$nMyIndex = 0;
        					$nMy2Index = 0;
        					foreach ($arrSlots as $arrSlot)
							{
								if ($nMyIndex == 0)
								{
									echo "<option value=\"0\" selected>- - - ".$arrSlot["strName"]." - - -</option>";
									$nMyIndex = 1;
								}
								else
								{
									echo "<option value=\"0\">- - - ".$arrSlot["strName"]." - - -</option>";
								}
								
								$nSlotID = $arrSlot["nID"];
								
								$strQuery = "SELECT * FROM cf_slottouser WHERE nMailingListId = '$nMailinglistID' AND nSlotId=".$arrSlot["nID"]." ORDER BY nPosition ASC";
					    		$nResult = mysql_query($strQuery, $nConnection);
					    		if ($nResult)
					    		{
					    			if (mysql_num_rows($nResult) > 0)
					    			{
					    				while (	$arrRow = mysql_fetch_array($nResult))
					    				{
											$arrUser = $arrUsers[$nUserID];
											
											$nUserID 	= $arrRow['nUserId'];
											if ($nUserID != -2)
											{
												$strUser	= $objMyCirculation->getUsername($nUserID);
												/*echo "<pre>";
												print_r($objMyCirculation);
												echo "</pre>";*/
											}
											else
											{
												$strUser	= $SELF_DELEGATE_USER;
											}
											
											$nPosition 	= $arrRow['nPosition'];
											
											$nCurKey = $nMy2Index.'---'.$nSlotID.'---'.$nUserID.'---'.$nPosition;
											
											echo "<option value=\"$nCurKey\">$strUser</option>";
											$nMy2Index++;
					    				}
					    			}
					    		}
							}
							?>
						</select>
					</td>
				</tr>
    		</table>
    		
    		<table cellspacing="0" cellpadding="3" align="center" width="260">
			<tr>
				<td align="left">
					<input type="button" value="<?php echo $BTN_CANCEL;?>" class="Button" onClick="window.close()">
				</td>
				<td align="right">
					<input type="button" value="<?php echo $BTN_OK;?>" class="Button" onClick="doOk()">
				</td>
			</tr>
			</table>
		</form>
	</div>
</body>
</html>
