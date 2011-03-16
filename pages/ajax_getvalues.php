<?php
include	('../language_files/language.inc.php');
header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");
$nMailinglistID = $_REQUEST["nMailinglistID"];

echo '<?xml version="1.0" encoding="'.$DEFAULT_CHARSET.'"?>';

include	('../config/config.inc.php');
include	('../config/db_connect.inc.php');
include_once ('CCirculation.inc.php');

$objMyCirculation 	= new CCirculation();
		
$arrMailinglist 	= $objMyCirculation->getMailinglist($nMailinglistID);									// corresponding mailinglist
$nFormTemplateID 	= $arrMailinglist['nTemplateId'];														// FormTemplate ID

$arrSlots			= $objMyCirculation->getFormslots($nFormTemplateID);									// corresponding formslots
// ??
$strQuery 	= "SELECT MAX(nID) FROM cf_circulationform WHERE bDeleted = 0";
$nResult 	= @mysql_query($strQuery);	
$arrRow 	= @mysql_fetch_array($nResult);
$nCirculationFormID	= (int) ($arrRow[0] +1); 
// ??


if (sizeof($arrSlots) != 0)
{
	$strResult_Top = '
	<table width="100%" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
	    <tr>
	        <td colspan="4" class="table_header" style="border-bottom: 3px solid #ffa000;">
	            '.$INSTALL_STEP.' 3/3: '.$EDIT_CIRCULATION_EDIT_VALUES_HEAD.'
	        </td>
	    </tr>
	    <tr>
			<td height="10">
			</td>
		</tr>
	    <tr>						
			<td>
				<input type="radio" name="changeValues" id="changeValues" value="0" checked onClick="document.getElementById(\'layer3\').style.display = \'block\';">'.$EDITCIRC_DEF.'<br>
				<input type="radio" name="changeValues" id="changeValues" value="1" onClick="showValues();">'.$EDITCIRC_ADA.'
			</td>
		</tr>
		<tr>
			<td height="10">
			</td>
		</tr>';
	
	$strResult_Middle = '';
	foreach ($arrSlots as $arrSlot)
	{	
		$strResult_Middle = $strResult_Middle.'
		<tr>        
        <td style="border-top: 1px solid Silver;">
            <table>';
        
		$strQuery 	= "SELECT * FROM cf_inputfield INNER JOIN cf_slottofield ON cf_inputfield.nID = cf_slottofield.nFieldId WHERE cf_slottofield.nSlotId = ".$arrSlot["nID"]."  ORDER BY cf_slottofield.nPosition ASC";
		$nResult 	= mysql_query($strQuery, $nConnection);
			if ($nResult)
      	{
   			if (mysql_num_rows($nResult) > 0)
				{
				$nRunningCounter = 1;
              	while (	$arrRow = mysql_fetch_array($nResult))
   				{
   					$strResult_Middle = $strResult_Middle.'
   					<td class="mandatory" width="200px" valign="top">'.$arrRow["strName"].':</td>
   					<td width="250px" valign="top">';
   					
   					$keyId = $arrRow["nFieldId"]."_".$arrSlot["nID"]."_".$nCirculationFormID;
					if ($arrRow["nType"] == 1)
					{
						$arrValue = split('rrrrr',$arrRow['3']);

						$strFieldValue 	= $arrValue[0];																					
						$REG_Text		= $arrValue[1];
						$strResult_Middle = $strResult_Middle.'
						<input style="width:220px;" class="FormInput" type="text" name="'.$keyId.'_1'.'" id="'.$keyId.'_1'.'" value="'.$strFieldValue.'">';
					
						if ($REG_Text != '')
						{
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="'.$keyId.'_REG" value="'.$REG_Text.'">';
							
						}																																										
					}
					else if ($arrRow["nType"] == 2)
					{
						$strResult_Middle = $strResult_Middle."
						<input type=\"checkbox\" name=\"".$keyId.'_2'."\" value=\"on\">";
					}
					else if ($arrRow["nType"] == 3)
					{																					
						$arrMyValue = split('xx',$arrRow['3']);
						$strMyValue = $arrMyValue['2'];

						$arrValue3 = split('rrrrr',$strMyValue);
						$strFieldValue 	= $arrValue3[0];
						$REG_Number		= $arrValue3[1];
						
						$strResult_Middle = $strResult_Middle."
						<input class=\"FormInput\" type=\"text\" name=\"".$keyId.'_3_'.$arrMyValue['1']."\" id=\"".$keyId.'_3_'.$arrMyValue['1']."\" value=\"".$strFieldValue."\"><br>";
																											
						switch ($arrMyValue['1'])
						{
							case '0':
								$strResult_Middle = $strResult_Middle."
								($FIELD_NUMTYPE_NOREGEX)";
								break;
							case '1':
								$strResult_Middle = $strResult_Middle."
								($FIELD_NUMTYPE_POSITIVE)";
								break;
							case '2':
								$strResult_Middle = $strResult_Middle."
								($FIELD_NUMTYPE_NEGATIVE)";
								break;
							case '3':
								break;
						}
						if ($REG_Number != '')
						{
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="'.$keyId.'_REG" value="'.$REG_Number.'">';
						}
					}
					else if ($arrRow["nType"] == 4)
					{
						$arrMyValue = split('xx',$arrRow['3']);
						$strMyValue = $arrMyValue['2'];
						
						$arrValue3 = split('rrrrr',$strMyValue);
							
						$strFieldValue 	= $arrValue3[0];
						$REG_Date		= $arrValue3[1];
						
						$strResult_Middle = $strResult_Middle."
						<input class=\"FormInput\" type=\"text\" name=\"".$keyId.'_4_'.$arrMyValue['1']."\" id=\"".$keyId.'_4_'.$arrMyValue['1']."\" value=\"".$strFieldValue."\"><br>";
						
						switch ($arrMyValue['1'])
						{
							case '0':
								break;
							case '1':
								$strResult_Middle = $strResult_Middle."(dd-mm-yyyy)";
								break;
							case '2':
								$strResult_Middle = $strResult_Middle."(mm-dd-yyyy)";
								break;
							case '3':
								$strResult_Middle = $strResult_Middle."(yyyy-mm-dd)";
								break;
						}
						if ($REG_Date != '')
						{
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="'.$keyId.'_REG" value="'.$REG_Date.'">';
						}
					}
					else if ($arrRow["nType"] == 5)
					{
						$strResult_Middle = $strResult_Middle.'
						<textarea Name="'.$keyId.'_5" id="'.$keyId.'_5" class="FormInput" style="width:250px; height: 100px;">'.$arrRow['3'].'</textarea>';
						
					}
					else if ($arrRow["nType"] == 6)
					{
						$arrRBGroup = '';
						$arrGroup = '';
						
						$arrSplit = split('---',$arrRow['3']);
																											
						$nSplitRunningNumber = 0;
						$nMax = sizeof($arrSplit);
						for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
						{									
							$arrGroup[$nSplitRunningNumber]	= $arrSplit[$nIndex+1];
							$arrRBGroup[$nSplitRunningNumber]		= $arrSplit[$nIndex];
							
							$nSplitRunningNumber++;
						}
																																																									
						for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
						{
							$CurStrRBGroup 	= $arrRBGroup[$nMyIndex];	// content of corresponding Radiobutton
							$CurRBGroup 	= $arrGroup[$nMyIndex];		// state of Radiobutton either '0' or '1'
							
							$strResult_Middle = $strResult_Middle.'
							<input type="radio" name="'.$keyId.'_nRadiogroup_'.$nRunningCounter.'" id="'.$keyId.'_nRadiogroup_'.$nRunningCounter.'" value="'.$nMyIndex.'"'; 
							if ($CurRBGroup) 
							{ 
								$strResult_Middle = $strResult_Middle.'checked'; 
							}										
							$strResult_Middle = $strResult_Middle.'>'.
							$CurStrRBGroup.'<br>
							<input type="hidden" name="RBName_'.$keyId.'_nRadiogroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$CurStrRBGroup.'">';										
						}																					
					}
					else if ($arrRow["nType"] == 7)
					{
						$arrCBGroup	= '';
						$arrGroup	= '';
						
						$arrSplit = split('---',$arrRow['3']);																						
						
						$nSplitRunningNumber = 0;
						$nMax = sizeof($arrSplit);
						for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
						{									
							$arrGroup[$nSplitRunningNumber]		= $arrSplit[$nIndex+1];
							$arrCBGroup[$nSplitRunningNumber]	= $arrSplit[$nIndex];
							
							$nSplitRunningNumber++;
						}
						
						for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
						{
							$CurStrCBGroup 	= $arrCBGroup[$nMyIndex];	
							$CurCBGroup 	= $arrGroup[$nMyIndex];
							
							$strResult_Middle = $strResult_Middle.'
							<input type="checkbox" name="'.$keyId.'_nCheckboxGroup_'.$nRunningCounter.'_'.$nMyIndex.'" id="'.$keyId.'_nCheckboxGroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="1"';
							
							if ($CurCBGroup) 
							{ 
								$strResult_Middle = $strResult_Middle.'checked'; 
							} 
							
							$strResult_Middle = $strResult_Middle.'>'.
							$CurStrCBGroup.'<br>';
							
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="CBName_'.$keyId.'_nCheckboxGroup_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$CurStrCBGroup.'">';										
						}
					}
					elseif($arrRow["nType"] == 8)
					{
						$arrComboGroup = '';
						$arrGroup = '';
						
						$arrSplit = split('---',$arrRow['strStandardValue']);
																			
						$nSplitRunningNumber = 0;
						$nMax = sizeof($arrSplit);
						for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
						{									
							$arrGroup[$nSplitRunningNumber]			= $arrSplit[$nIndex+1];
							$arrComboGroup[$nSplitRunningNumber]	= $arrSplit[$nIndex];
							
							$nSplitRunningNumber++;
						}
												
						$strResult_Middle = $strResult_Middle.'
						<select name="'.$keyId.'_nComboboxV_'.$nRunningCounter.'" id="'.$keyId.'_nComboboxV_'.$nRunningCounter.'" size="1">';
						
						for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
						{
							$CurStrCBGroup 	= $arrComboGroup[$nMyIndex];	
							$CurCBGroup 	= $arrGroup[$nMyIndex];
							
							if ($CurCBGroup)
							{
								$strResult_Middle = $strResult_Middle.'
								<option value="'.$nMyIndex.'" selected>'.$CurStrCBGroup.'</option>';
								
							}
							else
							{
								$strResult_Middle = $strResult_Middle.'
								<option value="'.$nMyIndex.'">'.$CurStrCBGroup.'</option>';
							}
						}
						$strResult_Middle = $strResult_Middle.'</select>';
						
						for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
						{
							$CurStrCBGroup 	= $arrComboGroup[$nMyIndex];	
							$CurCBGroup 	= $arrGroup[$nMyIndex];
							
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="COMBOName_'.$keyId.'_nCombobox_'.$nRunningCounter.'_'.$nMyIndex.'" value="'.$CurStrCBGroup.'">';
						}
					}
					elseif($arrRow["nType"] == 9)
					{						
						$arrValue = split('rrrrr',$arrRow['3']);																				
						$REG_File		= $arrValue[1];
						if ($REG_File != '')
						{
							$strResult_Middle = $strResult_Middle.'
							<input type="hidden" name="'.$keyId.'_REG" id="'.$keyId.'_REG" value="'.$REG_File.'">';
						}		
						
						$strResult_Middle = $strResult_Middle.'
						<input type="file" Name="'.$keyId.'_9" id="'.$keyId.'_9">';
					}
					$strResult_Middle = $strResult_Middle.'</td>';
														
					if ($nRunningCounter % 2 == 0)
					{
						$strResult_Middle = $strResult_Middle.'
						</tr><tr><td height="10"></td></tr><tr>';
					}
					else
					{
						$strResult_Middle = $strResult_Middle.'
						<td width="10px">&nbsp;</td>';
					}
					
					$nRunningCounter++;
   				}
   				$strResult_Middle = $strResult_Middle.'
   				</tr><tr><td height="10"></td></tr><tr>';
				}
      	}
      	$strResult_Middle = $strResult_Middle.'
      	</table>
		</td>
		</tr>';
	}
	$strResult_Bottom = '
	<tr>
		<td colspan="8" style="border-top: 1px solid #ffa000; padding: 6px 0px 4px 0px;" align="right">
			<table width="100%">
				<tr>
					<td align="left" width="25%">
						<input type="button" class="button" value="<< '.$BTN_BACK.'" onClick="showStep2();">
					</td>
					<td align="right" width="25%">
						<input type="button" class="button" value="'.$BTN_CANCEL.'" onClick="location=\'showcirculation.php?language='.$_REQUEST['language'].'&archivemode=0&start=1&bFirstStart=true\'">
					</td>
					<td align="left" width="25%">
						<input type="submit" name="step3" class="button" value="'.$BTN_COMPLETE.'">
					</td>
					<td align="right" width="25%">
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';				
}

$strResult = $strResult_Top.$strResult_Middle.$strResult_Bottom;
echo $strResult;

?>