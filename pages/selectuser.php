<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
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
			if (document.forms.BrowseUser.Substitude.options.selectedIndex != -1)
			{
				nID = document.forms.BrowseUser.Substitude.options[document.forms.BrowseUser.Substitude.options.selectedIndex].value;
				strUserName = document.forms.BrowseUser.Substitude.options[document.forms.BrowseUser.Substitude.options.selectedIndex].innerHTML;
				
				//if (nID == -3) nID = -333;
				
				opener.SetUser(nID, strUserName, <?php echo $_REQUEST['nId'] ?>);
					
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

	<form action="" id="BrowseUser">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
					<?php echo $USER_SELECT_FORM_HEADER;?>
				</td>
			</tr>
			<tr>
				<td style="padding: 8px 4px 8px 4px;">
					<select id="Substitude" class="FormInput" size="7" style="width:250px;">
    					<option value="-3"><?php echo $SELF_DELEGATE_USER ?></option>	
						<?php
    						//--- open database
                        	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
                        	
                        	if ($nConnection)
                        	{
                        		//--- get maximum count of users
                        		if (mysql_select_db($DATABASE_DB, $nConnection))
                        		{
                        			//--- read the values of the user
                    				$strQuery = "SELECT * FROM cf_user  WHERE bDeleted <> 1 ORDER BY strLastName ASC";
                    				$nResult = mysql_query($strQuery, $nConnection);
                            
                            		if ($nResult)
                            		{
                            			if (mysql_num_rows($nResult) > 0)
                            			{
                            				while (	$arrRow = mysql_fetch_array($nResult))
                            				{
                         						?>
                         						<option value="<?php echo $arrRow['nID'] ?>"><?php echo $arrRow['strLastName'].', '.$arrRow['strFirstName'] ?></option>
                         						<?php   					
                            				}
                            			}
                            		}
                          		}
                        	}
    					?>
						<option value="0">-</option>
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
