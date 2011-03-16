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
				
				var nUserID 	= Value;
				
				opener.changeCurrentStation_Subs(nUserID);
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
						<select id="MailingList" class="FormInput" size="10" style="width:250px;">
        					<?php
        					$strQuery = "SELECT * FROM cf_user  WHERE bDeleted <> 1 ORDER BY strLastName ASC";
            				$nResult = mysql_query($strQuery, $nConnection);
                    
                    		if ($nResult)
                    		{
                    			if (mysql_num_rows($nResult) > 0)
                    			{
                    				while (	$arrRow = mysql_fetch_array($nResult))
                    				{
                 						echo "<option value=\"".$arrRow["nID"]."\">".$arrRow["strUserId"]." (".$arrRow["strLastName"].", ".$arrRow["strFirstName"].")</option>";   					
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
