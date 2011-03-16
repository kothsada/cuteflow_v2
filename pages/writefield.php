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
	
	switch ($_REQUEST['nType'])
	{
		case '1':
			$strFieldValue = $_REQUEST['StdValue_Text'];
			if ($_REQUEST['REG_Text']!='')
			{
				$strFieldValue = $strFieldValue.'rrrrr'.$_REQUEST['REG_Text'];
			}			
			break;	
		case '2':
			$strFieldValue = $_REQUEST['StdValue_Checkbox'];
			break;	
		case '3':
			$nNumType = $_REQUEST['IN_regex'];
			
			if ($nNumType==3)
			{
				$strFieldValue = 'xx'.$nNumType.'xx'.$_REQUEST['StdValue_Number'].'rrrrr'.$_REQUEST['REG_Number'];
			}
			else
			{
				$strFieldValue = 'xx'.$nNumType.'xx'.$_REQUEST['StdValue_Number'];
			}
			break;	
		case '4':
			$nNumType = $_REQUEST['nDateFormat'];
			
			if ($_REQUEST['REG_Date']!='')
			{
				$strFieldValue = 'xx'.'0'.'xx'.$_REQUEST['StdValue_Date'].'rrrrr'.$_REQUEST['REG_Date'];
			}
			else
			{
				$strFieldValue = 'xx'.$nNumType.'xx'.$_REQUEST['StdValue_Date'];
			}
			break;	
		case '5':
			$strFieldValue = str_replace("\"", "\\\"", $_REQUEST['StdValue_LargeText']);
			$strFieldValue = str_replace("'", "\\'", $strFieldValue);
			break;	
		case '6':
			$selectedRadio	= $_REQUEST['nRadiogroup'];
			$strInputField 	= '/strRadiogroup/';
			$nIfLength		= strlen('strRadiogroup');
			$nAmountOfGroups = 0;
			
			foreach($_POST as $key => $value)
			{
				if (preg_match($strInputField, $key))
				{	// search the inputfield
					$nId = substr($key, $nIfLength, 2);
					
					if ($value != '')
					{
						$bSelected = 0;
						if ($selectedRadio == $nId)
						{
							$bSelected = 1;
						}
						$strFieldValue .= '---'.$value.'---'.$bSelected;
						
						$nAmountOfGroups++;
					}
				}
			}
			$strFieldValue = '---'.$nAmountOfGroups.$strFieldValue;
			break;	
		case '7':			
			$strInputField 	= '/strCheckboxGroup/';
			$nIfLength		= strlen('strCheckboxGroup');
			$nAmountOfGroups = 0;
			
			foreach($_POST as $key => $value)
			{
				if (preg_match($strInputField, $key))
				{	// search the inputfield
					$nId = substr($key, $nIfLength, 2);
					$strCheckbox = 'nCheckboxGroup'.$nId;
					
					echo 'test: '.$test.'<br>';
					echo 'key: '.$key.'<br>';
					echo 'value: '.$value.'<br><br>';
					
					if ($value != '')
					{
						$bSelected = 0;
						if ($_POST[$strCheckbox])
						{
							$bSelected = 1;
						}
						$strFieldValue .= '---'.$value.'---'.$bSelected;
						
						$nAmountOfGroups++;
					}
				}
			}
			$strFieldValue = '---'.$nAmountOfGroups.$strFieldValue;
			break;
		case '8':
			$selectedCombo	= $_REQUEST['nCombobox'];
			$strInputField 	= '/strCombogroup/';
			$nIfLength		= strlen('strCombogroup');
			$nAmountOfGroups = 0;
			
			foreach($_POST as $key => $value)
			{
				if (preg_match($strInputField, $key))
				{	// search the inputfield
					$nId = substr($key, $nIfLength, 2);
					
					if ($value != '')
					{
						$bSelected = 0;
						if ($selectedCombo == $nId)
						{
							$bSelected = 1;
						}
						$strFieldValue .= '---'.$value.'---'.$bSelected;
						
						$nAmountOfGroups++;
					}
				}
			}
			$strFieldValue = '---'.$nAmountOfGroups.$strFieldValue;
			break;
		case '9':
			$strFieldValue = '';
			if ($_REQUEST['REG_File']!='')
			{
				$strFieldValue = $strFieldValue.'rrrrr'.$_REQUEST['REG_File'];
			}
			break;	
	}
	
	$_REQUEST['StdValue'] = $strFieldValue;
		
	//--- write user to database
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");

	if ($_REQUEST["ReadOnly_".$_REQUEST['nType']] == "on")
	{
		$bReadOnly = 1;
	}
	else
	{
		$bReadOnly = 0;
	}
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			if ($_REQUEST["fieldid"] == -1)
			{
				//--- add new field
				$query = "INSERT INTO cf_inputfield values (null, \"".$_REQUEST["strName"]."\", ".$_REQUEST["nType"].", \"".$_REQUEST["StdValue"]."\", $bReadOnly, '".$_REQUEST["field_color"]."')";				
			}
			else
			{
				//--- update existing field
				$query = "UPDATE cf_inputfield SET strName=\"".$_REQUEST["strName"]."\", strStandardValue=\"".$_REQUEST["StdValue"]."\", nType=".$_REQUEST["nType"].", bReadOnly=$bReadOnly, strBgColor='".$_REQUEST["field_color"]."'";
				$query .= " WHERE nID=".$_REQUEST["fieldid"];
			}
			
	//		echo $query;
			
			$nResult = mysql_query($query, $nConnection);
		}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	
	<script language="JavaScript">
		function onLoad()
		{
			document.location.href="showfields.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"];?>&sortby=<?php echo $_REQUEST["sortby"];?>";
		}
	</script>
</head>
<body onLoad="onLoad()">

</body>
