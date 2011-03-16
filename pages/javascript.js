function validate_newfield()
{	
	var strChoice 	= document.getElementById('nType').value;
	var	strName		= document.getElementById('strName').value;
	var Error 		= "";
	var strMyError	= strMyErrorMSG;
		
	switch(strChoice)
	{
		case '1':
			break;
		case '2':
			break;
		case '3':
			var strStandardValue = document.getElementById("StdValue_Number").value;
			
			if (document.EditField.IN_regex[0].checked == true)
			{
				var valNumber = /[^0-9\-\,\.]/i;
			}
			if (document.EditField.IN_regex[1].checked == true)
			{
				var valNumber = /[^0-9\-\,\.]/i;			
				if (strStandardValue < 0)
				{
					Error = strMyError + '. (Number)';
				}
			}
			if (document.EditField.IN_regex[2].checked == true)
			{
				var valNumber = /[^0-9\-\,\.]/i;
				if (strStandardValue > 0)
				{
					Error = strMyError + '. (Number)';
				}
			}
			
			var bValstrStandardValue = valNumber.test(strStandardValue);
			
			if (bValstrStandardValue == true)
			{
				Error = strMyError + '. (Number)';
			}
			
			if (strStandardValue == '')
			{
				Error = strMyError + '. (Number)';
			}
			
			break;		
		case '4':
			var strStandardValue = document.getElementById("StdValue_Date").value;
			
			var strDateFormat 	= document.getElementById('nDateFormat').value;
			var strDateREG 		= document.getElementById('REG_Date').value;
			if (strDateREG == '')
			{
				switch(strDateFormat)
				{
					case '1':
						var valDate = /[0-3]{1}[0-9]{1}[\.\-]{1}[01]{1}[0-9]{1}[\.\-]{1}[0-9]{4}/;
						if (strStandardValue != '')
						{
							var strSplitter = strStandardValue.search(/\./);
							if (strSplitter != -1)
							{
								var arrDate = strStandardValue.split('.');
							}
							else
							{
								var arrDate = strStandardValue.split('-');
							}
							
							if (arrDate[0]>31)
							{
								var bValstrDateValue = false;
							}
							if (arrDate[1]>12)
							{
								var bValstrDateValue = false;
							}
						}						
						break;
					case '2':
						var valDate = /[01]{1}[0-9]{1}[\.\-]{1}[0-3]{1}[0-9]{1}[\.\-]{1}[0-9]{4}/;
						if (strStandardValue != '')
						{
							var strSplitter = strStandardValue.search(/\./);
							if (strSplitter != -1)
							{
								var arrDate = strStandardValue.split('.');
							}
							else
							{
								var arrDate = strStandardValue.split('-');
							}
							
							if (arrDate[1]>31)
							{
								var bValstrDateValue = false;
							}
							if (arrDate[0]>12)
							{
								var bValstrDateValue = false;
							}
						}
						break;
					case '3':
						var valDate = /[0-9]{4}[\.\-]{1}[01]{1}[0-9]{1}[\.\-]{1}[0-3]{1}[0-9]{1}/;
						if (strStandardValue != '')
						{
							var strSplitter = strStandardValue.search(/\./);
							if (strSplitter != -1)
							{
								var arrDate = strStandardValue.split('.');
							}
							else
							{
								var arrDate = strStandardValue.split('-');
							}
							
							if (arrDate[2]>31)
							{
								var bValstrDateValue = false;
							}
							if (arrDate[1]>12)
							{
								var bValstrDateValue = false;
							}
						}
						break;
				}
			}
			var bValstrDateFormat = valDate.test(strStandardValue);
			
			if (strStandardValue != '')
			{
				if ( (bValstrDateFormat == false) || (bValstrDateValue == false) )
				{
					Error = strMyError + '. (Date)';
				}
			}
			
			break;
		case '5':
			break;		
		case '6':
			var arrRadio = document.getElementsByName('nRadiogroup');
			var nMax = nRadioGroups;
			var nCountEntries = 0;
			
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				bChecked = arrRadio[nIndex].checked;
				strInputfieldName = 'strRadiogroup' + nIndex;
				strValue = document.getElementById(strInputfieldName).value;
								
				if ((bChecked) && (strValue == ''))
				{
					Error = strMyError + '. (Radiobutton)';
				}
				
				if (strValue != '')
				{
					nCountEntries++;
				}
			}

			if (nCountEntries<2)
			{
				Error = strMyError + '. (Radiobutton)';
			}
			break;
		case '7':
			var nMax = nCheckboxGroups;
			var nCountEntries = 0;
			
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strBox 		= 'nCheckboxGroup' + nIndex;
				strField 	= 'strCheckboxGroup' + nIndex;
				
				bChecked 	= document.getElementById(strBox).checked;
				strValue 	= document.getElementById(strField).value;
				
				if ((bChecked) && (strValue == ''))
				{
					Error = strMyError + '. (Checkbox)';
				}
				
				if (strValue != '')
				{
					nCountEntries++;
				}
			}
			
			if (nCountEntries<2)
			{
				Error = strMyError + '. (Checkbox)';
			}
			break;
		case '8':
			var arrCombo = document.getElementsByName('nCombobox');
			var nMax = nComboboxGroups;
			var nCountEntries = 0;
			
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				bChecked 			= arrCombo[nIndex].checked;
				strInputfieldName 	= 'strCombogroup' + nIndex;
				strValue 			= document.getElementById(strInputfieldName).value;
				
				if ((bChecked) && (strValue == ''))
				{
					Error = strMyError + '. (Combobox)';
				}
				
				if (strValue != '')
				{
					nCountEntries++;
				}
			}
			
			if (nCountEntries<2)
			{
				Error = strMyError + '. (Combobox)';
			}
			break;
	}	
		
	if (strName == "")
	{
		Error = strMyError + '. (Fieldname)';
	}

	if (Error=="")
	{
		return true;
	}
	else
	{
		alert(Error);
		return false;
	}
}

function change_field_type()
{
	var strChoice = document.getElementById('nType').value;
	
	switch(strChoice)
	{
		case '1':
			document.getElementById('settings_text').style.display='block';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;
		case '2':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_checkbox').style.display='block';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;		
		case '3':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='block';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;		
		case '4':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='block';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;		
		case '5':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_largetext').style.display='block';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';	
			document.getElementById('settings_combobox').style.display='none';	
			document.getElementById('settings_file').style.display='none';	
			break;		
		case '6':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='block';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;
		case '7':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='block';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='none';
			break;
		case '8':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='block';
			document.getElementById('settings_file').style.display='none';
			break;
		case '9':
			document.getElementById('settings_text').style.display='none';
			document.getElementById('settings_largetext').style.display='none';
			document.getElementById('settings_number').style.display='none';
			document.getElementById('settings_date').style.display='none';
			document.getElementById('settings_checkbox').style.display='none';
			document.getElementById('settings_radiobutton').style.display='none';
			document.getElementById('settings_checkboxgroup').style.display='none';
			document.getElementById('settings_combobox').style.display='none';
			document.getElementById('settings_file').style.display='block';
			break;
	}
}

function checkInput(aTextField)
{
	if ((aTextField.value.length==0) || (aTextField.value==null) || (aTextField.value==''))
	{
		aTextField.style.background = '#dddddd';
	}
}