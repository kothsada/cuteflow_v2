<?php
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
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
			if (document.getElementById('Placeholder').value != 0)
			{
				var Value = document.getElementById('Placeholder').value;
								
				opener.insertPlaceholder(Value);
				window.close();
			}
			else
			{
				alert ("Please choose first.");				
			}
		}

	//-->
	</script>	
</head>
<body topmargin="0" leftmargin="0" style="margin-top: 5px;">
<?php
require_once 'placeholder_tags_addtext.php';
?>
	<form action="" id="BrowseUser">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
					<?php echo $SELECT_PLACEHOLDER;?>
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 4px 8px 4px;">
					<select id="Placeholder" class="FormInput" size="5" style="width:250px;">
    					<?php
    						
    						if (sizeof($arrPlaceholdersAddText) > 0)
    						{
    							$nMax = sizeof($arrPlaceholdersAddText);
    							for($nIndex = 0; $nIndex < $nMax; $nIndex++)
    							{    							
    								echo "<option value=\"".$arrPlaceholdersAddText[$nIndex]."\">".$arrPlaceholdersAddText[$nIndex]."</option>";
    							}
    						}
    						else
    						{
    							echo "<option value=\"0\">No Placeholder available</option>";
    						}   					
                           
    					?>
					</select>
				</td>
			</tr>
		</table>
		
		<table cellspacing="0" cellpadding="3" align="center" width="260">
		<tr>
			<td align="left">
				<input type="button" value="<?php echo $BTN_CANCEL;?>" class="Button" onClick="window.close();">
			</td>
			<td align="right">
				<input type="button" value="<?php echo $BTN_OK;?>" class="Button" onClick="doOk()">
			</td>
		</tr>
		</table>
	</form>
</body>
</html>
