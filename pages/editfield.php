<?php
	/** Copyright (c) Timo Haberkern. All rights reserved.
	*
	* Redistribution and use in source and binary forms, with or without 
	* modification, are permitted provided that the following conditions are met:
	* 
	*  o Redistributions of source code must retain the above copyright notice, 
	*    this list of conditions and the following disclaimer. 
	*     
	*  o Redistributions in binary form must reproduce the above copyright notice, 
	*    this list of conditions and the following disclaimer in the documentation 
	*    and/or other materials provided with the distribution. 
	*     
	*  o Neither the name of Timo Haberkern nor the names of 
	*    its contributors may be used to endorse or promote products derived 
	*    from this software without specific prior written permission. 
	*     
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
	* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
	* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
	* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
	* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
	* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
	* OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
	* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
	* OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
	* EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	*/
	
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
		function validate(objForm)
		{
			var objForm = document.forms["EditField"];
			objForm.strName.required = 1;
			objForm.strName.err = "<?php echo $FIELD_NEW_ERROR_NAME;?>";
			
			bResult = jsVal(objForm);
			
			return bResult;
		}
		
		function browsePlaceholders()
		{
			url="selectplaceholder.php?language=<?php echo $_REQUEST["language"];?>";
			open(url,"BrowsePlaceholder","width=300,height=190,status=no,menubar=no,resizable=no,scrollbars=no");		
		}
		
		function insertPlaceholder(Value)
		{
			document.getElementById('StdValue_Text').value = Value;
			document.getElementById('ReadOnly').checked = true;
		}
		
		var strMyErrorMSG = "<?php echo $JS_PRESET_ERROR; ?>";
		
		function changeRadioGroup(strAction)
		{
			
			if ((nRadioGroups == 2) && (strAction == 'delete'))
			{
				return false;
			}
			
			var arrRadioButtons = new Array();
			
			// read the current values
			var nMax = nRadioGroups;
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strInputFieldName = 'strRadiogroup' + nIndex;

				var arrInner = [document.EditField.nRadiogroup[nIndex].checked, document.getElementById(strInputFieldName).value];
				arrRadioButtons[nIndex] = arrInner;
			}
			
			switch(strAction)
			{	// either reduce the amount of fields or add a field
				case 'add':
					var arrInner = [false, ''];
					arrRadioButtons[nMax] = arrInner;
					nMax++;
					nRadioGroups++;
					break;
				case 'delete':
					nMax--;
					nRadioGroups--;
					break;
			}
			
			var strNewContent = '';
			
			// write the new values
			strNewContent += '<table cellpadding="2" cellspacing="0">';
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				bChecked = arrRadioButtons[nIndex][0];
				strValue = arrRadioButtons[nIndex][1];
				strInputFieldName = 'strRadiogroup' + nIndex;
				
				strNewContent += '	<tr>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="radio" name="nRadiogroup" id="nRadiogroup" value="' + nIndex + '"';
				
				if (bChecked)
				{
					var checkedOne = nIndex;
				}
				
				strNewContent += '				>';
				strNewContent += '		</td>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="text" class="InputText" style="width: 250px;" name="' + strInputFieldName + '" id="' + strInputFieldName + '" value="' + strValue + '">';
				strNewContent += '		</td>';
				if (nIndex == 0)
				{
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_add.gif" onClick="changeRadioGroup(\'add\');">';
					strNewContent += '	</td>';
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_remove.gif" onClick="changeRadioGroup(\'delete\');">';
					strNewContent += '	</td>';
				}
				strNewContent += '</tr>';
			}			
			
			strNewContent += '</table>';
			
			document.getElementById('div_radiogroup').innerHTML = strNewContent;
			
			parsedCheckedOne = parseInt(checkedOne);
			if (parsedCheckedOne == checkedOne)
			{
				document.EditField.nRadiogroup[checkedOne].checked = true;
			}
			document.getElementById('Radio_nAmount').value = nRadioGroups;
		}
		
		function changeCheckboxGroup(strAction)
		{
			
			if ((nCheckboxGroups == 2) && (strAction == 'delete'))
			{
				return false;
			}
			
			//var arrRadioButtons = new Array();
			var arrCheckboxes = new Array();
			
			// read the current values
			var nMax = nCheckboxGroups;
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strCheckboxName = 'nCheckboxGroup' + nIndex;
				strInputFieldName = 'strCheckboxGroup' + nIndex;
				
				var arrInner = [document.getElementById(strCheckboxName).checked, document.getElementById(strInputFieldName).value];
				arrCheckboxes[nIndex] = arrInner;
			}
			
			switch(strAction)
			{	// either reduce the amount of fields or add a field
				case 'add':
					var arrInner = [false, ''];
					arrCheckboxes[nMax] = arrInner;
					nMax++;
					nCheckboxGroups++;
					break;
				case 'delete':
					nMax--;
					nCheckboxGroups--;
					break;
			}
			
			var strNewContent = '';
			
			// write the new values
			strNewContent += '<table cellpadding="2" cellspacing="0">';
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				bChecked = arrCheckboxes[nIndex][0];
				strValue = arrCheckboxes[nIndex][1];
				strCheckboxName = 'nCheckboxGroup' + nIndex;
				strInputFieldName = 'strCheckboxGroup' + nIndex;
				
				strNewContent += '	<tr>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="checkbox" name="' + strCheckboxName + '" id="' + strCheckboxName + '" value="1"';
				
				if (bChecked)
				{
					//var checkedOne = nIndex;
					strNewContent += ' checked';
				}
				
				strNewContent += '				>';
				strNewContent += '		</td>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="text" class="InputText" style="width: 250px;" name="' + strInputFieldName + '" id="' + strInputFieldName + '" value="' + strValue + '">';
				strNewContent += '		</td>';
				if (nIndex == 0)
				{
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_add.gif" onClick="changeCheckboxGroup(\'add\');">';
					strNewContent += '	</td>';
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_remove.gif" onClick="changeCheckboxGroup(\'delete\');">';
					strNewContent += '	</td>';
				}
				strNewContent += '</tr>';
			}			
			
			strNewContent += '</table>';
			
			document.getElementById('div_checkboxgroup').innerHTML = strNewContent;
			document.getElementById('Checkbox_nAmount').value = nCheckboxGroups;
		}
		
		function changeComboGroup(strAction)
		{
			
			if ((nComboboxGroups == 2) && (strAction == 'delete'))
			{
				return false;
			}
			
			var arrComboboxes = new Array();
			
			// read the current values
			var nMax = nComboboxGroups;
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strInputFieldName = 'strCombogroup' + nIndex;
				
				var arrInner = [document.EditField.nCombobox[nIndex].checked, document.getElementById(strInputFieldName).value];
				arrComboboxes[nIndex] = arrInner;
			}
			
			switch(strAction)
			{	// either reduce the amount of fields or add a field
				case 'add':
					var arrInner = [false, ''];
					arrComboboxes[nMax] = arrInner;
					nMax++;
					nComboboxGroups++;
					break;
				case 'delete':
					nMax--;
					nComboboxGroups--;
					break;
			}
			
			var strNewContent = '';
			
			// write the new values
			strNewContent += '<table cellpadding="2" cellspacing="0">';
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				bChecked = arrComboboxes[nIndex][0];
				strValue = arrComboboxes[nIndex][1];
				strInputFieldName = 'strCombogroup' + nIndex;
				
				strNewContent += '	<tr>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="radio" name="nCombobox" id="nCombobox" value="' + nIndex + '"';
				
				if (bChecked)
				{
					var checkedOne = nIndex;
				}
				
				strNewContent += '				>';
				strNewContent += '		</td>';
				strNewContent += '		<td valign="top" align="left">';
				strNewContent += '			<input type="text" class="InputText" style="width: 250px;" name="' + strInputFieldName + '" id="' + strInputFieldName + '" value="' + strValue + '">';
				strNewContent += '		</td>';
				if (nIndex == 0)
				{
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_add.gif" onClick="changeComboGroup(\'add\');">';
					strNewContent += '	</td>';
					strNewContent += '	<td valign="top" align="left" style="cursor: pointer;">';
					strNewContent += '		<img src="../images/edit_remove.gif" onClick="changeComboGroup(\'delete\');">';
					strNewContent += '	</td>';
				}
				strNewContent += '</tr>';
			}			
			
			strNewContent += '</table>';
			
			document.getElementById('div_combogroup').innerHTML = strNewContent;
			
			parsedCheckedOne = parseInt(checkedOne);
			if (parsedCheckedOne == checkedOne)
			{
				document.EditField.nCombobox[checkedOne].checked = true;
			}
			document.getElementById('Combobox_nAmount').value = nComboboxGroups;
		}
		
	//-->
	</script>
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>
	<script src="javascript.js" type="text/javascript"></script>
</head>
<?php
	$strName = "";
	$nType = 1;
	$strValue = "";
	$bReadOnly = 0;
	
	// set the empty Arrays of Radio/Checkbox/Combobox - Groups
	$nMax = 6;
	for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
	{
		$arrRadioGroup[$nIndex]['checked'] 		= 0;
		$arrRadioGroup[$nIndex]['value'] 		= '';
		
		$arrCheckboxGroup[$nIndex]['checked'] 	= 0;
		$arrCheckboxGroup[$nIndex]['value'] 	= '';
		
		$arrComboboxGroup[$nIndex]['checked'] 	= 0;
		$arrComboboxGroup[$nIndex]['value'] 	= '';
	}
	
	include_once ("../config/config.inc.php");

	if (-1 != $fieldid)
	{
    	//--- open database
    	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
    	
    	if ($nConnection)
    	{
    		//--- get maximum count of users
    		if (mysql_select_db($DATABASE_DB, $nConnection))
    		{
    			//--- read the values of the user
				$strQuery = "SELECT * FROM cf_inputfield WHERE nID = ".$_REQUEST["fieldid"];
				$nResult = mysql_query($strQuery, $nConnection);
        
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
        				while (	$arrRow = mysql_fetch_array($nResult))
        				{
        					$strName 		= $arrRow["strName"];
							$nType	 		= $arrRow["nType"];
							$strValue 		= $arrRow["strStandardValue"];
							$bReadOnly 		= $arrRow["bReadOnly"];
							$strValidate 	= $arrRow["strValidate"];
							$strBgColor 	= $arrRow['strBgColor'];
        				}
        				
        				switch ($nType)
						{
							case '1':
								$arrValue = split('rrrrr',$strValue);
								
								$strValueText 	= $arrValue[0];
								$REG_Text		= $arrValue[1];
								break;	
							case '2':
								$strFieldValue = $strValue;
								break;	
							case '3':
								$arrValue = split('xx',$strValue);
								
								$nNumGroup 	= $arrValue[1];
								
								$arrValue1 = split('rrrrr',$arrValue[2]);
								
								$strValueNumber	= $arrValue1[0];
								$REG_Number		= $arrValue1[1];
								break;	
							case '4':
								$arrValue = split('xx',$strValue);
								
								$nDateGroup 	= $arrValue[1];
								
								$arrValue2 = split('rrrrr',$arrValue[2]);
								
								$strValueDate 	= $arrValue2[0];
								$REG_Date		= $arrValue2[1];
								break;	
							case '5':
								$strFieldValue = $strValue;
								break;	
							case '6':
								unset($arrRadioGroup);
								$arrSplit = split('---',$strValue);
								
								$nSplitRunningNumber = 0;
								$nMax = sizeof($arrSplit);
								for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
								{									
									$arrRadioGroup[$nSplitRunningNumber]['checked'] = $arrSplit[$nIndex+1];
									$arrRadioGroup[$nSplitRunningNumber]['value'] 	= $arrSplit[$nIndex];
									
									$nSplitRunningNumber++;
								}
								break;
							case '7':
								unset($arrCheckboxGroup);
								$arrSplit = split('---',$strValue);
								
								$nSplitRunningNumber = 0;
								$nMax = sizeof($arrSplit);
								for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
								{									
									$arrCheckboxGroup[$nSplitRunningNumber]['checked'] 	= $arrSplit[$nIndex+1];
									$arrCheckboxGroup[$nSplitRunningNumber]['value'] 	= $arrSplit[$nIndex];
									
									$nSplitRunningNumber++;
								}
								break;
							case '8':
								unset($arrComboboxGroup);
								$arrSplit = split('---',$strValue);
								
								$nSplitRunningNumber = 0;
								$nMax = sizeof($arrSplit);
								for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
								{									
									$arrComboboxGroup[$nSplitRunningNumber]['checked'] 	= $arrSplit[$nIndex+1];
									$arrComboboxGroup[$nSplitRunningNumber]['value'] 	= $arrSplit[$nIndex];
									
									$nSplitRunningNumber++;
								}
								break;
							case '9':
								$arrValue = split('rrrrr',$strValue);
								
								$strValueFile 	= $arrValue[0];
								$REG_File		= $arrValue[1];
								break;	
						}
        						
        			}
        			else
        			{
        				$nType = '';	
        			}
        		}
    		}
    	}
	}
?>
	<script language="javascript">
	<!--
		var nRadioGroups 	= <?php echo sizeof($arrRadioGroup) ?>;
		var nCheckboxGroups = <?php echo sizeof($arrCheckboxGroup) ?>;
		var nComboboxGroups = <?php echo sizeof($arrComboboxGroup) ?>;
	//-->
	</script>
<body><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_FIELDS;?>
</span><br><br>

	<form action="writefield.php" id="EditField" name="EditField" onSubmit="return validate_newfield();" method="post">
		<table width="450" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
			<tr>
				<td colspan="2" class="table_header">
					<?php echo $FIELD_EDIT_HEADLINE;?>
				</td>
			</tr>
			<tr><td colspan="2" height="10px"></td></tr>
			
            <tr>
				<td width="120"><?php echo $FIELD_EDIT_NAME;?></td>
				<td><input id="strName" Name="strName" type="text" class="InputText" style="width:250px;" value="<?php echo $strName;?>"></td>
			</tr>
            <tr>
				<td><?php echo $FIELD_EDIT_TYPE;?></td>
				<td>
				<select id="nType" name="nType" class="FormInput" onChange="change_field_type();">
					<option value="1" <?php if ($nType == 1) echo "selected";?>><?php echo $FIELD_TYPE_TEXT;?></option>
					<option value="2" <?php if ($nType == 2) echo "selected";?>><?php echo $FIELD_TYPE_BOOLEAN;?></option>
					<option value="3" <?php if ($nType == 3) echo "selected";?>><?php echo $FIELD_TYPE_DOUBLE;?></option>
					<option value="4" <?php if ($nType == 4) echo "selected";?>><?php echo $FIELD_TYPE_DATE;?></option>
					<option value="5" <?php if ($nType == 5) echo "selected";?>><?php echo $FIELD_TYPE_LARGETEXT;?></option>
					<option value="6" <?php if ($nType == 6) echo "selected";?>><?php echo $FIELD_TYPE_RADIOGROUP;?></option>
					<option value="7" <?php if ($nType == 7) echo "selected";?>><?php echo $FIELD_TYPE_CHECKBOXGROUP;?></option>
					<option value="8" <?php if ($nType == 8) echo "selected";?>><?php echo $FIELD_TYPE_COMBOBOX;?></option>
					<option value="9" <?php if ($nType == 9) echo "selected";?>><?php echo $FIELD_TYPE_FILE;?></option>
				</select>
			</tr>
			<tr>
				<td><?php echo $FIELD_EDIT_COLOR;?>:</td>
				<td>#<input name="field_color" id="field_color" type="text" class="InputText" style="width:250px;" value="<?php echo $strBgColor;?>"/></td>
			</tr>
			
			<tr><td colspan="2" height="10px"></td></tr>
			
			<tr><td colspan="2">				
			
			<table id="settings_text" style="display:<?php if(($nType=='1')||($nType<'1')) { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td width="120" valign="middle"><?php echo $FIELD_EDIT_STDVALUE;?></td>
    				<td valign="bottom">
    					<input type="text" Name="StdValue_Text" id="StdValue_Text" class="InputText" style="width:220px;" value="<?php echo $strValueText;?>">    					
    				</td>
    				<td valign="middle">
    					<img title="<?php echo escapeDouble($INSERT_PLACEHOLDER);?>" src="../images/grid_insert_row_style_2_16.gif" style="margin-left: 4px; height: 16px; border: 1px solid #666; background: #eeeeee; cursor: pointer;" onClick="browsePlaceholders();">
    				</td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
				<tr>
    				<td width="120" valign="top">
    					<?php echo $FIELD_EDIT_REGEX;?>:
    				</td>
    				<td>
    					<input type="text" class="InputText" <?php if($REG_Text=='') { echo "style=\"width: 220px; background: #dddddd;\""; } else { echo "style=\"width: 220px;\""; } ?> name="REG_Text" id="REG_Text" onFocus="this.style.background='#ffffff';" onBlur="checkInput(this);" value="<?php echo $REG_Text; ?>">
    				</td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_1" <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_checkbox" style="display:<?php if($nType=='2') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px; width: 100%;">
    			<tr>
    				<td align="left">
    					<input type="hidden" Name="StdValue_Checkbox" id="StdValue_Checkbox" value="0">
    				</td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    	        <tr>
    				<td width="120"><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td align="left"><input type="checkbox" id="ReadOnly" name="ReadOnly_2"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_number" style="display:<?php if($nType==3) { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td width="120" valign="top"><?php echo $FIELD_EDIT_STDVALUE;?></td>
    				<td><input type="text" Name="StdValue_Number" id="StdValue_Number" class="InputText" style="width:150px;" value="<?php echo $strValueNumber;?>"></td>
    			</tr>
				<tr><td colspan="2" height="10px"></td></tr>
    	        <tr>
    	        	<td colspan="2">
    	        		<input type="radio" name="IN_regex" id="IN_regex" value="0" <?php if (($nNumGroup == 0)||($nNumGroup == "")) { echo "checked"; } ?>> <?php echo $FIELD_NUMTYPE_NOREGEX; ?><br>
					    <input type="radio" name="IN_regex" id="IN_regex" value="1" <?php if ($nNumGroup == 1) { echo "checked"; } ?>> <?php echo $FIELD_NUMTYPE_POSITIVE; ?><br>
					    <input type="radio" name="IN_regex" id="IN_regex" value="2" <?php if ($nNumGroup == 2) { echo "checked"; } ?>> <?php echo $FIELD_NUMTYPE_NEGATIVE; ?><br>						    
						<input type="radio" name="IN_regex" id="IN_regex" value="3" <?php if ($nNumGroup == 3) { echo "checked"; } ?>> <input type="text" class="InputText" <?php if($REG_Number=='') { echo "style=\"width: 200px; background: #dddddd;\""; } else { echo "style=\"width: 200px;\""; } ?> name="REG_Number" id="REG_Number" onFocus="this.style.background='#ffffff';" onBlur="checkInput(this);" value="<?php echo $REG_Number; ?>"> (<?php echo $FIELD_EDIT_REGEX;?>)
					</td>
    	        </tr>
    	        <tr><td colspan="2" height="10px"></td></tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_3"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_date" style="display:<?php if($nType=='4') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td width="120" valign="top"><?php echo $FIELD_DATE_FORMAT;?></td>
    				<td>
    					<select id="nDateFormat" name="nDateFormat" class="FormInput">
							<option value="1" <?php if ($nDateGroup == 1) echo "selected";?>>dd-mm-yyyy</option>
							<option value="2" <?php if ($nDateGroup == 2) echo "selected";?>>mm-dd-yyyy</option>
							<option value="3" <?php if ($nDateGroup == 3) echo "selected";?>>yyyy-mm-dd</option>
						</select>
    				</td>
    			</tr>
    			<tr>
    				<td width="120" valign="top"><?php echo $FIELD_EDIT_STDVALUE;?></td>
    				<td><input type="text" Name="StdValue_Date" id="StdValue_Date" class="InputText" style="width:103px;" value="<?php echo $strValueDate;?>"></td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
				<tr>
    				<td width="120" valign="top">
    					<?php echo $FIELD_EDIT_REGEX;?>:
    				</td>
    				<td>
    					<input type="text" class="InputText" <?php if($REG_Date=='') { echo "style=\"width: 220px; background: #dddddd;\""; } else { echo "style=\"width: 220px;\""; } ?> name="REG_Date" id="REG_Date" onFocus="this.style.background='#ffffff';" onBlur="checkInput(this);" value="<?php echo $REG_Date; ?>">
    				</td>
    			</tr>
    			<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_4"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_largetext" style="display:<?php if($nType=='5') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td width="120" valign="top"><?php echo $FIELD_EDIT_STDVALUE;?></td>
    				<td><textarea Name="StdValue_LargeText" id="StdValue_LargeText" class="InputText" style="width:250px; height: 100px;"><?php echo $strValue;?></textarea></td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_5"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_radiobutton" style="display:<?php if($nType=='6') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td valign="top" align="left" colspan="2">(<?php echo $FIELD_EDIT_VALIDVALUES;?>)</td>	    				
    			</tr>
    			<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    			<tr>
    	        	<td colspan="2">
    	        		
    	        		<div id="div_radiogroup">
    					<table cellpadding="2" cellspacing="0">
    					
	    					<?php
	    	        		$nMax = sizeof($arrRadioGroup);
							for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
							{
								$arrRadioButton = $arrRadioGroup[$nIndex];
								$bChecked = $arrRadioButton['checked'];
								$strValue = $arrRadioButton['value'];
								$strInputFieldName = 'strRadiogroup'.$nIndex;
								?>
								<tr>
									<td valign="top" align="left">
										<input type="radio" name="nRadiogroup" id="nRadiogroup" value="<?php echo $nIndex ?>" <?php if ($bChecked) echo 'checked' ?>>
									</td>
									<td valign="top" align="left">
										<input type="text" class="InputText" style="width: 250px;" name="<?php echo $strInputFieldName ?>" id="<?php echo $strInputFieldName ?>" value="<?php echo $strValue ?>">
									</td>
									<?php if ($nIndex == 0): ?>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_add.gif" onClick="changeRadioGroup('add');">
										</td>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_remove.gif" onClick="changeRadioGroup('delete');">
										</td>
									<?php endif ?>
								</tr>
								<?php
							}
							?>
							
    	        		</table>
    	        		</div>
    	        		
					</td>
    	        </tr>
    	        <tr><td colspan="2" height="10px"></td></tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td align="left"><input type="checkbox" id="ReadOnly" name="ReadOnly_6"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_checkboxgroup" style="display:<?php if($nType=='7') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td valign="top" align="left" colspan="2">(<?php echo $FIELD_EDIT_VALIDVALUES;?>)</td>	    				
    			</tr>
    			<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    			<tr>
    	        	<td colspan="2">
    	        	
    	        		<div id="div_checkboxgroup">
    					<table cellpadding="2" cellspacing="0">
    					
	    					<?php
	    	        		$nMax = sizeof($arrCheckboxGroup);
							for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
							{
								$arrCheckbox = $arrCheckboxGroup[$nIndex];
								$bChecked = $arrCheckbox['checked'];
								$strValue = $arrCheckbox['value'];
								$strCheckboxName 	= 'nCheckboxGroup'.$nIndex;
								$strInputFieldName 	= 'strCheckboxGroup'.$nIndex;
								?>
								<tr>
									<td valign="top" align="left">
										<input type="checkbox" name="<?php echo $strCheckboxName ?>" id="<?php echo $strCheckboxName ?>" value="1" <?php if ($bChecked) echo 'checked' ?>>
									</td>
									<td valign="top" align="left">
										<input type="text" class="InputText" style="width: 250px;" name="<?php echo $strInputFieldName ?>" id="<?php echo $strInputFieldName ?>" value="<?php echo $strValue ?>">
									</td>
									<?php if ($nIndex == 0): ?>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_add.gif" onClick="changeCheckboxGroup('add');">
										</td>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_remove.gif" onClick="changeCheckboxGroup('delete');">
										</td>
									<?php endif ?>
								</tr>
								<?php
							}
							?>
							
    	        		</table>
    	        		</div>

					</td>
    	        </tr>
    	        <tr><td colspan="2" height="10px"></td></tr>
    	        <tr>
    				<td width="120"><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_7"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_combobox" style="display:<?php if($nType=='8') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td valign="top" align="left" colspan="2">(<?php echo $FIELD_EDIT_VALIDVALUES;?>)</td>	    				
    			</tr>
    			<tr>
					<td colspan="2" height="10px"></td>
				</tr>
    			<tr>
    	        	<td colspan="2">
    	        		
    	        		<div id="div_combogroup">
    					<table cellpadding="2" cellspacing="0">
    					
	    					<?php
	    	        		$nMax = sizeof($arrComboboxGroup);
							for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
							{
								$arrCombobox = $arrComboboxGroup[$nIndex];
								$bChecked = $arrCombobox['checked'];
								$strValue = $arrCombobox['value'];
								$strInputFieldName = 'strCombogroup'.$nIndex;
								?>
								<tr>
									<td valign="top" align="left">
										<input type="radio" name="nCombobox" id="nCombobox" value="<?php echo $nIndex ?>" <?php if ($bChecked) echo 'checked' ?>>
									</td>
									<td valign="top" align="left">
										<input type="text" class="InputText" style="width: 250px;" name="<?php echo $strInputFieldName ?>" id="<?php echo $strInputFieldName ?>" value="<?php echo $strValue ?>">
									</td>
									<?php if ($nIndex == 0): ?>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_add.gif" onClick="changeComboGroup('add');">
										</td>
										<td valign="top" align="left" style="cursor: pointer;">
											<img src="../images/edit_remove.gif" onClick="changeComboGroup('delete');">
										</td>
									<?php endif ?>
								</tr>
								<?php
							}
							?>
							
    	        		</table>
    	        		</div>
					</td>
    	        </tr>
    	        <tr><td colspan="2" height="10px"></td></tr>
    	        <tr>
    				<td width="120"><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_8"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
			<table id="settings_file" style="display:<?php if($nType=='9') { echo "block"; } else { echo "none"; } ?>; border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;">
    			<tr>
    				<td width="120" valign="top"><?php echo $FIELD_EDIT_STDVALUE;?></td>
    				<td><?php echo $FIELD_EDIT_FILE_INFO;?></td>
    			</tr>
				<tr>
					<td colspan="2" height="10px"></td>
				</tr>
				<tr>
    				<td width="120" valign="top">
    					<?php echo $FIELD_EDIT_REGEX;?>:
    				</td>
    				<td>
    					<input type="text" class="InputText" <?php if($REG_File=='') { echo "style=\"width: 220px; background: #dddddd;\""; } else { echo "style=\"width: 220px;\""; } ?> name="REG_File" id="REG_File" onFocus="this.style.background='#ffffff';" onBlur="checkInput(this);" value="<?php echo $REG_File; ?>">
    				</td>
    			</tr>
    	        <tr>
    				<td><?php echo $FIELD_EDIT_READONLY;?></td>
    				<td><input type="checkbox" id="ReadOnly" name="ReadOnly_9"  <?php echoCheckedReadOnly($bReadOnly);?>>&nbsp;</td>
    			</tr>
			</table>
			
		</table>
		
		<table cellspacing="0" cellpadding="3" align="left" width="450">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_CANCEL;?>" onclick="history.back()">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $USER_EDIT_ACTION;?>" class="Button">
			</td>
		</tr>
		</table>
		
	<input type="hidden" name="Combobox_nAmount" id="Combobox_nAmount" value="<?php echo sizeof($arrComboboxGroup) ?>">
	<input type="hidden" name="Checkbox_nAmount" id="Checkbox_nAmount" value="<?php echo sizeof($arrCheckboxGroup) ?>">
	<input type="hidden" name="Radio_nAmount" id="Radio_nAmount" value="<?php echo sizeof($arrRadioGroup) ?>">
	<input type="hidden" value="<?php echo $_REQUEST["fieldid"];?>" id="fieldid" name="fieldid">
	<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
	<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sort" name="sort">
	<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">
	</form>


	
</body>
</html>

<?php		
	function echoCheckedReadOnly($bReadOnly)
	{
		if ($bReadOnly == 1)
		{
			echo "CHECKED";	
		}	
	}
?>