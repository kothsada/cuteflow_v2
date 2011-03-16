<?php
	include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');
	
	$nCurUserID = $_REQUEST['nCurCuserID'];
	
	$objMyCirculation 	= new CCirculation();				
	$arrFilters 	= $objMyCirculation->getMyFilters($nCurUserID);		// corresponding mailinglist
?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td style="padding: 6px 4px 8px 4px;">
			<table cellpadding="1" cellspacing="0" width="155" style="border: 1px solid #999; background-color: #ffffff;">
				<?php
				
				$nMax = sizeof($arrFilters);
				for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
				{
					$arrCurFilter 	= $arrFilters[$nIndex];
					
					$nCurFilterID 	= $arrCurFilter['nID'];
					$strCurLabel 	= $arrCurFilter['strLabel'];
					
					$curColor = '#eeeeee';
					if ($nIndex % 2 == 0)
					{
						$curColor = '#ffffff';
					}
					
					?>
					<tr bgcolor="<?php echo $curColor; ?>">
						<td style="padding-left: 2px; cursor:pointer;" onClick="changeFilter('<?php echo $nCurFilterID; ?>')">
							<?php echo $strCurLabel; ?>
						</td>
						<td width="20" align="right">
							<a href="javascript:deleteFilter('<?php echo $nCurFilterID; ?>')"><img src="../images/edit_remove.gif" border="0"></a>							
						</td>
					</tr>
					
					<?php
				}
				?>
			</table>
		</td>
	</tr>
</table>