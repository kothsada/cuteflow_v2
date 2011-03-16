<?php
	include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');
	
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
			if (document.getElementById('IN_nFilterID').value != 0)
			{
				var nFilterID = document.getElementById('IN_nFilterID').value;				
				
				opener.deleteFilter(nFilterID);
				window.close();
			}	
			else
			{
				alert ('<?php echo str_replace("'", "\'", $FILTER_SELECT_NO_SELECT);?>');				
			}
		}
	//-->
	</script>	
</head>
<body topmargin="0" leftmargin="0" style="margin-top: 5px;">
<table height="100%"><tr><td valign="middle">
	<div align="center">
		<form action="" id="BrowseMailingList">
    		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center">
    			<tr>
    				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
						<?php echo $FILTER_LOAD_FILTER;?>
					</td>
    			</tr>
				<tr>
					<td style="padding: 8px 4px 8px 4px;">
						<?php
							$objMyCirculation 	= new CCirculation();				
		
							$arrFilters 	= $objMyCirculation->getMyFilters($nCurUserID);		// corresponding mailinglist
						?>
						<select id="IN_nFilterID" class="FormInput" size="10" style="width:250px;">
        					<?php
        					
        					$nMax = sizeof($arrFilters);
        					for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
							{
								$arrCurFilter 	= $arrFilters[$nIndex];
								
								$nCurFilterID 	= $arrCurFilter['nID'];
								$strCurLabel 	= $arrCurFilter['strLabel'];
								
								if ($nIndex == 0)
								{
									echo "<option value=\"0\" selected>- - - $FILTER_CHOOSE_FILTER - - -</option>";
								}
								
								echo "<option value=\"$nCurFilterID\">$strCurLabel</option>";
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
</td></tr></table>
</body>
</html>
