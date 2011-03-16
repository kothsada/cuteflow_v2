<?php
	include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');
	
	$nCurInputFieldID = $_REQUEST['nCurInputFieldID'];
	$nCurRunningID = $_REQUEST['nCurRunningID'];
	
	$objCCirculation = new CCirculation();
	
	$nCurType = $objCCirculation->getFieldType($nCurInputFieldID);
	
	if (($nCurType == '6') || ($nCurType == '8'))
	{
		$strQuery 	= "SELECT * FROM cf_inputfield WHERE nID = '$nCurInputFieldID'";
		$nResult 	= @mysql_query($strQuery);
		
		$arrCurInputField = mysql_fetch_array($nResult, MYSQL_ASSOC);
		
		$strCurStdValue = $arrCurInputField['strStandardValue'];
		
		switch ($nCurType)
		{
			case '6':
				$arrSplit = split('---',$strCurStdValue);
				
				$nSplitRunningNumber = 0;
				$nMax = sizeof($arrSplit);
				for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
				{									
					$arrGroup[$nSplitRunningNumber] 	= $arrSplit[$nIndex+1];
					$arrRBGroup[$nSplitRunningNumber] 	= $arrSplit[$nIndex];
					
					$nSplitRunningNumber++;
				}
				
				?>				
					<select name="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" id="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" style="width: 155px; font-family: arial; font-size: 12px;">				
				<?php												
				for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
				{
					$CurStrRBGroup 	= $arrRBGroup[$nMyIndex];	// content of corresponding Radiobutton
					$CurRBGroup 	= $arrGroup[$nMyIndex];		// state of Radiobutton either '0' or '1'
					?>																							
					<option value="<?php echo $CurStrRBGroup; ?>" <?php if ($CurRBGroup) { echo "selected"; } ?>>
					<?php echo $CurStrRBGroup; ?>
					</option>
					<?php													
				}
				
				?>				
					</select>				
				<?php
				break;
			case '8':
				$arrSplit = split('---',$strCurStdValue);
				
				$nSplitRunningNumber = 0;
				$nMax = sizeof($arrSplit);
				for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
				{									
					$arrGroup[$nSplitRunningNumber] 	= $arrSplit[$nIndex+1];
					$arrRBGroup[$nSplitRunningNumber] 	= $arrSplit[$nIndex];
					
					$nSplitRunningNumber++;
				}
				
				?>				
					<select name="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" id="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" style="width: 155px; font-family: arial; font-size: 12px;">				
				<?php												
				for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
				{
					$CurStrRBGroup 	= $arrRBGroup[$nMyIndex];	// content of corresponding Radiobutton
					$CurRBGroup 	= $arrGroup[$nMyIndex];		// state of Radiobutton either '0' or '1'
					?>																							
					<option value="<?php echo $CurStrRBGroup; ?>" <?php if ($CurRBGroup) { echo "selected"; } ?>>
					<?php echo $CurStrRBGroup; ?>
					</option>
					<?php													
				}
				
				?>				
					</select>				
				<?php
				break;
		}
	}
	elseif ($nCurType == '2')
	{
		?>
		<input type="checkbox" name="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" id="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" style="padding-left: 20px; padding-right: 20px;">
		<?php
	}
	else
	{
		?>
		<input type="text" name="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" id="FILTERCustom_Value--<?php echo $nCurRunningID; ?>" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
		<?php
	}

?>
