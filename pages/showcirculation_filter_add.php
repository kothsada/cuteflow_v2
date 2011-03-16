<?php
	include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');

	$nCurUserID = $_REQUEST['nCurCuserID'];
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
			if (document.getElementById('IN_strFilterLabel').value != 0)
			{
				var Value 		= document.getElementById('IN_strFilterLabel').value;
				var nCurUserID 	= <?php echo $nCurUserID; ?>
				
				opener.saveFilter(Value, nCurUserID);
				window.close();
			}	
			else
			{
				alert ('<?php echo str_replace("'", "\'", $FILTER_LABEL_NO_LABEL);?>');				
			}
		}
	//-->
	</script>	
</head>
<body topmargin="0" leftmargin="0" style="margin-top: 5px;">
<table height="100%"><tr><td valign="middle">
	<div align="center">
		<form action="" id="BrowseMailingList">
    		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center" width="270">
    			<tr>
    				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
						<?php echo $FILTER_SAVE_HEADER;?>
					</td>
    			</tr>
    			<tr><td height="10"></td></tr>
				<tr>
					<td style="padding: 8px 4px 8px 4px;">
						<?php echo $FILTER_LABEL; ?>: <input type="text" name="IN_strFilterLabel" id="IN_strFilterLabel" value="" class="FormInput" style="width: 180px;">
					</td>
				</tr>
				<tr><td height="15"></td></tr>
    		</table>
    		
    		<table cellspacing="0" cellpadding="3" align="center" width="260">
			<tr>
				<td align="left">
					<input type="button" value="<?php echo $BTN_CANCEL; ?>" class="Button" onClick="window.close()">
				</td>
				<td align="right">
					<input type="button" value="<?php echo $BTN_OK; ?>" class="Button" onClick="doOk()">
				</td>
			</tr>
			</table>
		</form>
	</div>
</td></tr></table>
</body>
</html>
