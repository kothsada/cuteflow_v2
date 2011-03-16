<?php
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
    include_once ("../lib/datetime.inc.php");
    include_once ("../lib/viewutils.inc.php");
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			
			//-----------------------------------------------
			//--- get the single circulation form
			//-----------------------------------------------
			$query = "select * from cf_circulationform WHERE nID=".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationForm = mysql_fetch_array($nResult);				
				}
			}
			
			//-----------------------------------------------
			//--- get the single circulation history
			//-----------------------------------------------
			$query = "select * from cf_circulationhistory WHERE nID=".$_REQUEST["chid"];
			$nResult = mysql_query($query, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationHistory = mysql_fetch_array($nResult);
				}
			}
			
			//-----------------------------------------------
    		//--- get all users
         	//-----------------------------------------------
         	$arrUsers = array();
    		$strQuery = "SELECT * FROM cf_user  WHERE bDeleted <> 1";
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					$arrUsers[$arrRow["nID"]] = $arrRow;
    				}
    			}
    		}
			
			//-----------------------------------------------
			//--- get the template id
			//-----------------------------------------------
			$strQuery = "SELECT * FROM cf_mailinglist WHERE nID=".$_REQUEST["listid"];
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrRow = mysql_fetch_array($nResult);
					$templateid = $arrRow["nTemplateId"];
				}
			}
			
			//-----------------------------------------------
			//--- get the form slots
            //-----------------------------------------------	            
            $arrSlots = array();
            $strQuery = "SELECT * FROM cf_formslot WHERE nTemplateID=".$templateid." ORDER BY nSlotNumber ASC";
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					$arrSlots[$arrRow["nID"]] = $arrRow;
    				}
    			}
    		}
			
			//-----------------------------------------------
            //--- get the field values
            //-----------------------------------------------	            
            $arrValues = array();
            $strQuery = "SELECT * FROM cf_fieldvalue WHERE nFormId=".$_REQUEST["cfid"]." AND nCirculationHistoryId=".$_REQUEST["chid"];
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					$arrValues[$arrRow["nInputFieldId"]."_".$arrRow["nSlotId"]."_".$arrRow["nFormId"]] = $arrRow;
    				}
    			}
    		}
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="../pages/format.css" type="text/css">
	<script language="javascript">
		var strAllIDs = '';
		
		function addID(strNewID)
		{
			strAllIDs = strAllIDs + strNewID + 'xxxx';
		}
		
		function validate_editfield()
		{	
			arrAllIDs = strAllIDs.split('xxxx');
			
			nMax = arrAllIDs.length;
			
			var Error 		= '';
			var valText		= /[^A-z0-9\.\-\_\/\s\ä\ö\ü\ß\,\;\:\(\)\?\!]/i;
			
			
			for(nIndex = 0; nIndex < nMax; nIndex++)
			{
				arrCurID = arrAllIDs[nIndex];				
				arrCurIDDetails	= arrCurID.split('zz');
				
				strCurID	= arrCurIDDetails[0];
				strCurType	= arrCurIDDetails[1];
								
				switch (strCurType)
				{
					case '1':						
						strCurID = strCurID + '_1';						
						var strValue 		= document.getElementById(strCurID).value;
						var bValstrValue	= valText.test(strValue);
												
						if (bValstrValue == true)
						{
							Error = "Der eingegebene Wert im Textfeld enthält ungültige Zeichen.";
						}	
						if (strValue == '')
						{
							Error = "Bitte geben Sie einen Wert vom Typ \"Text\" ein.";
						}
						break;
					case '3_0':
						strCurID = strCurID + '_3_0';
						var strStandardValue = document.getElementById(strCurID).value;
			
						if (document.EditField.IN_regex[0].checked == true)
						{
							var valNumber = /[^0-9\-\,\.]/i;
						}
											
						var bValstrStandardValue = valNumber.test(strStandardValue);
						
						if (bValstrStandardValue == true)
						{
							Error = "Ein Feld vom Typ \"Zahl\" enthält ungültige Zeichen.";
						}
						
						if (strStandardValue == '')
						{
							Error = "Bitte geben Sie einen Wert vom Typ \"Zahl\" ein.";
						}
						break;						
					case '3_1':
						strCurID = strCurID + '_3_1';
						var strStandardValue = document.getElementById(strCurID).value;
						var valNumber = /[^0-9\-\,\.]/i;			
						if (strStandardValue < 0)
						{
							Error = "Bitte geben Sie eine positive Zahl ein.";
						}
						var bValstrStandardValue = valNumber.test(strStandardValue);
						
						if (bValstrStandardValue == true)
						{
							Error = "Ein Feld vom Typ \"Zahl\" enthält ungültige Zeichen.";
						}
						
						if (strStandardValue == '')
						{
							Error = "Bitte geben Sie einen Wert vom Typ \"Zahl\" ein.";
						}
						break;
					case '3_2':
						strCurID = strCurID + '_3_2';
						var strStandardValue = document.getElementById(strCurID).value;
						var valNumber = /[^0-9\-\,\.]/i;
						
						if (strStandardValue > 0)
						{
							Error = "Bitte geben Sie eine negative Zahl ein.";
						}
						var bValstrStandardValue = valNumber.test(strStandardValue);
						
						if (bValstrStandardValue == true)
						{
							Error = "Ein Feld vom Typ \"Zahl\" enthält ungültige Zeichen.";
						}
						
						if (strStandardValue == '')
						{
							Error = "Bitte geben Sie einen Wert vom Typ \"Zahl\" ein.";
						}
						break;						
					case '4_1':
						strCurID = strCurID + '_4_1';
						var strStandardValue = document.getElementById(strCurID).value;
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
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							if (arrDate[1]>12)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							var bValstrDateFormat = valDate.test(strStandardValue);
			
							if (bValstrDateFormat == false)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
						}						
						else
						{
							Error = "Bitte geben Sie ein Datum ein.";
						}
						break;
					case '4_2':
						strCurID = strCurID + '_4_2';
						var strStandardValue = document.getElementById(strCurID).value;
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
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							if (arrDate[0]>12)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							var bValstrDateFormat = valDate.test(strStandardValue);
			
							if (bValstrDateFormat == false)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
						}						
						else
						{
							Error = "Bitte geben Sie ein Datum ein.";
						}
						break;
					case '4_3':
						strCurID = strCurID + '_4_3';
						var strStandardValue = document.getElementById(strCurID).value;
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
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							if (arrDate[1]>12)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
							
							var bValstrDateFormat = valDate.test(strStandardValue);
			
							if (bValstrDateFormat == false)
							{
								Error = "Das eingegebene Datum wurde im falschen Format eingegeben.";
							}
						}
						else
						{
							Error = "Bitte geben Sie ein Datum ein.";
						}
						break;
					case '5':
						strCurID = strCurID + '_5';
						var strValue 		= document.getElementById(strCurID).value;
						var bValstrValue	= valText.test(strValue);
						
						if (bValstrValue == true)
						{
							Error = "Der eingegebene Wert im Textfeld enthält ungültige Zeichen.";
						}	
						if (strValue == '')
						{
							Error = "Bitte geben Sie einen Wert vom Typ \"Text\" ein.";
						}
						break;
				}
			}
			
			if (Error == '')
			{
				return true;
			}
			else
			{
				alert(Error);
				return false;	
			}
		}
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
</head>
<body style="margin-top:0px">
	
	<form enctype="multipart/form-data" method="post" action="restart_circulation_values_write.php" id="MailContentForm" name="MailContentForm" target="_self" onSubmit="return validate_editfield();">
				<center><table border="0" width="700">
				<tr><td><br><br>
					<?php
						if (sizeof($arrSlots) != 0)
						{
					?>
								<table border="0" width="100%" cellpadding="0" cellspacing="0" class="BorderSilver" style="background-color:White;">
								    <tr>
								        <td colspan="2">
								            <table bgcolor="Silver" width="100%">
								                <tr>
								                    <td width="20px"><img src="../images/values.png" height="16" width="16"></td>
								                    <td style="font-weight:bold;"><?php echo htmlentities($EDIT_CIRCULATION_EDIT_VALUES_HEAD);?></td>
								                </tr>
								            </table>
								        </td>
								    </tr>
									<?php
										$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
								   	    if ($nConnection)
									    {
										    if (mysql_select_db($DATABASE_DB, $nConnection))
								   			{
												foreach ($arrSlots as $arrSlot)
												{
													?>
													    <tr>        
													        <td style="border-top: 1px solid Silver;">
													            <table>
																<?php
																	$strQuery = "SELECT * FROM cf_inputfield INNER JOIN cf_slottofield ON cf_inputfield.nID = cf_slottofield.nFieldId WHERE cf_slottofield.nSlotId = ".$arrSlot["nID"]."  ORDER BY cf_slottofield.nPosition ASC";
																	$nResult = mysql_query($strQuery, $nConnection);
								                   					if ($nResult)
												                  	{
								            			       			if (mysql_num_rows($nResult) > 0)
								                   						{
																			$nRunningCounter = 1;
											    		                  	while (	$arrRow = mysql_fetch_array($nResult))
								            			       				{
																				echo "<td class=\"mandatory\" width=\"200px\" valign=\"top\">".htmlentities($arrRow["strName"]).":</td>";
																				echo "<td width=\"250px\" valign=\"top\">";
																				
																				$keyId = $arrRow["nFieldId"]."_".$arrSlot["nID"]."_".$_REQUEST["cfid"];
																				if ($arrRow["nType"] == 1)
																				{
																					echo "<input style=\"width:220px;\" class=\"FormInput\" type=\"text\" name=\"".$keyId.'_1'."\" id=\"".$keyId.'_1'."\" value=\"".$arrRow['3']."\">";
																					?>
																					<script language="javascript">
																					addID('<?php echo $keyId."zz1"; ?>');
																					</script>
																					<?php																					
																				}
																				else if ($arrRow["nType"] == 2)
																				{
																					echo "<input type=\"checkbox\" name=\"".$keyId.'_2'."\" value=\"on\">";
																				}
																				else if ($arrRow["nType"] == 3)
																				{																					
																					$arrMyValue = split('xx',$arrRow['3']);
																					$strMyValue = $arrMyValue['2'];
																					echo "<input class=\"FormInput\" type=\"text\" name=\"".$keyId.'_3_'.$arrMyValue['1']."\" id=\"".$keyId.'_3_'.$arrMyValue['1']."\" value=\"".$strMyValue."\"><br>(";
																																										
																					switch ($arrMyValue['1'])
																					{
																						case '0':
																							echo "$FIELD_NUMTYPE_NOREGEX)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz3_0"; ?>');
																							</script>
																							<?php
																							break;
																						case '1':
																							echo "$FIELD_NUMTYPE_POSITIVE)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz3_1"; ?>');
																							</script>
																							<?php
																							break;
																						case '2':
																							echo "$FIELD_NUMTYPE_NEGATIVE)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz3_2"; ?>');
																							</script>
																							<?php
																							break;
																					}
																				}
																				else if ($arrRow["nType"] == 4)
																				{
																					$arrMyValue = split('xx',$arrRow['3']);
																					$strMyValue = $arrMyValue['2'];
																					echo "<input class=\"FormInput\" type=\"text\" name=\"".$keyId.'_4_'.$arrMyValue['1']."\" id=\"".$keyId.'_4_'.$arrMyValue['1']."\" value=\"".$strMyValue."\"><br>(";
																					
																					switch ($arrMyValue['1'])
																					{
																						case '1':
																							echo "dd-mm-yyyy)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz4_1"; ?>');
																							</script>
																							<?php
																							break;
																						case '2':
																							echo "mm-dd-yyyy)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz4_2"; ?>');
																							</script>
																							<?php
																							break;
																						case '3':
																							echo "yyyy-mm-dd)";
																							?>
																							<script language="javascript">
																							addID('<?php echo $keyId."zz4_3"; ?>');
																							</script>
																							<?php
																							break;
																					}
																				}
																				else if ($arrRow["nType"] == 5)
																				{
																					?>
																					<script language="javascript">
																					addID('<?php echo $keyId."zz5"; ?>');
																					</script>
																					<textarea Name="<?php echo $keyId.'_5'; ?>" id="<?php echo $keyId.'_5'; ?>" class="FormInput" style="width:250px; height: 100px;"><?php echo $arrRow['3'];?></textarea>
																					<?php
																				}
																				else if ($arrRow["nType"] == 6)
																				{
																					$arrRBGroup = '';
																					$arrGroup = '';
																					
																					$arrSplit = split('---',$arrRow['3']);
																																										
																					$arrRBGroup[]	= $arrSplit['2'];
																					$arrGroup[]		= $arrSplit['3'];
																					$arrRBGroup[]	= $arrSplit['4'];
																					$arrGroup[]		= $arrSplit['5'];
																					$arrRBGroup[]	= $arrSplit['6'];
																					$arrGroup[]		= $arrSplit['7'];
																					$arrRBGroup[]	= $arrSplit['8'];
																					$arrGroup[]		= $arrSplit['9'];
																					$arrRBGroup[]	= $arrSplit['10'];
																					$arrGroup[]		= $arrSplit['11'];
																					$arrRBGroup[]	= $arrSplit['12'];
																					$arrGroup[]		= $arrSplit['13'];
																																																																								
																					for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
																					{
																						$CurStrRBGroup 	= $arrRBGroup[$nMyIndex];	// content of corresponding Radiobutton
																						$CurRBGroup 	= $arrGroup[$nMyIndex];		// state of Radiobutton either '0' or '1'
																						?>																							
																						<input type="radio" name="<?php echo $keyId; ?>_nRadiogroup_<?php echo $nRunningCounter; ?>" id="<?php echo $keyId; ?>_nRadiogroup_<?php echo $nRunningCounter; ?>" value="<?php echo $nMyIndex; ?>" <?php if ($CurRBGroup) { echo "checked"; } ?>>
																						<?php
																						echo $CurStrRBGroup."<br>";
																						?>
																						<input type="hidden" name="RBName_<?php echo $keyId; ?>_nRadiogroup_<?php echo $nRunningCounter; ?>_<?php echo $nMyIndex; ?>" value="<?php echo $CurStrRBGroup; ?>">
																						<?php
																					}																					
																				}
																				else if ($arrRow["nType"] == 7)
																				{
																					$arrCBGroup	= '';
																					$arrGroup	= '';
																					
																					$arrSplit = split('---',$arrRow['3']);																						
																					
																					$arrCBGroup[]	= $arrSplit['2'];
																					$arrGroup[]		= $arrSplit['3'];
																					$arrCBGroup[]	= $arrSplit['4'];
																					$arrGroup[]		= $arrSplit['5'];
																					$arrCBGroup[]	= $arrSplit['6'];
																					$arrGroup[]		= $arrSplit['7'];
																					$arrCBGroup[]	= $arrSplit['8'];
																					$arrGroup[]		= $arrSplit['9'];
																					$arrCBGroup[]	= $arrSplit['10'];
																					$arrGroup[]		= $arrSplit['11'];
																					$arrCBGroup[]	= $arrSplit['12'];
																					$arrGroup[]		= $arrSplit['13'];
																					
																					for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
																					{
																						$CurStrCBGroup 	= $arrCBGroup[$nMyIndex];	
																						$CurCBGroup 	= $arrGroup[$nMyIndex];
																						?>
																						<input type="checkbox" name="<?php echo $keyId; ?>_nCheckboxGroup_<?php echo $nRunningCounter; ?>_<?php echo $nMyIndex; ?>" id="<?php echo $keyId; ?>_nCheckboxGroup_<?php echo $nRunningCounter; ?>_<?php echo $nMyIndex; ?>" value="1" <?php if ($CurCBGroup) { echo "checked"; } ?>> 
																						<?php
																						echo $CurStrCBGroup."<br>";
																						?>
																						<input type="hidden" name="CBName_<?php echo $keyId; ?>_nCheckboxGroup_<?php echo $nRunningCounter; ?>_<?php echo $nMyIndex; ?>" value="<?php echo $CurStrCBGroup; ?>">
																						<?php
																					}
																				}
																				elseif($arrRow["nType"] == 8)
																				{
																					$arrComboGroup = '';
																					$arrGroup = '';
																					
																					if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
																					{
																						$arrSplit = split('---',$arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);
																					}
																					else
																					{
																						$arrSplit = split('---',$arrRow['strStandardValue']);
																					}						
																																		
																					$arrComboGroup[]	= $arrSplit['2'];
																					$arrGroup[]			= $arrSplit['3'];
																					$arrComboGroup[]	= $arrSplit['4'];
																					$arrGroup[]			= $arrSplit['5'];
																					$arrComboGroup[]	= $arrSplit['6'];
																					$arrGroup[]			= $arrSplit['7'];
																					$arrComboGroup[]	= $arrSplit['8'];
																					$arrGroup[]			= $arrSplit['9'];
																					$arrComboGroup[]	= $arrSplit['10'];
																					$arrGroup[]			= $arrSplit['11'];
																					$arrComboGroup[]	= $arrSplit['12'];
																					$arrGroup[]			= $arrSplit['13'];
																											
																					?>
																					<select name="<?php echo $keyId; ?>_nComboboxV_<?php echo $nRunningCounter; ?>" id="<?php echo $keyId; ?>_nComboboxV_<?php echo $nRunningCounter; ?>" size="1">
																					<?php
																					for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
																					{
																						$CurStrCBGroup 	= $arrComboGroup[$nMyIndex];	
																						$CurCBGroup 	= $arrGroup[$nMyIndex];
																						
																						if ($CurCBGroup)
																						{
																							?>
																							<option value="<?php echo $nMyIndex; ?>" selected><?php echo $CurStrCBGroup; ?></option>
																							<?php
																						}
																						else
																						{
																							?>
																							<option value="<?php echo $nMyIndex; ?>"><?php echo $CurStrCBGroup; ?></option>
																							<?php
																						}
																					}
																					echo '</select>';
																					for ($nMyIndex = 0; $nMyIndex < $arrSplit['1']; $nMyIndex++)
																					{
																						$CurStrCBGroup 	= $arrComboGroup[$nMyIndex];	
																						$CurCBGroup 	= $arrGroup[$nMyIndex];
																						?>
																						<input type="hidden" name="COMBOName_<?php echo $keyId; ?>_nCombobox_<?php echo $nRunningCounter; ?>_<?php echo $nMyIndex; ?>" value="<?php echo $CurStrCBGroup; ?>">
																						<?php																						
																					}
																				}
																				elseif($arrRow["nType"] == 9)
																				{						
																					?>
																					<input type="file" Name="<?php echo $keyId.'_9'; ?>">
																					<?php																						
																				}
																				echo "</td>";
																													
																				if ($nRunningCounter % 2 == 0)
																				{
																					echo "</tr>\n<tr><td height=\"10\"></td></tr><tr>\n";
																				}
																				else
																				{
																					echo "<td width=\"10px\">&nbsp;</td>";
																				}
																				
																				$nRunningCounter++;
																			}
																			echo "</tr>\n<tr><td height=\"10\"></td></tr><tr>\n";
																		}
																	}
																?>
																</table>
															</td>
														</tr>
													<?php
												}
											}
										}
										?>
								</table>
						<?PHP
							}
						?> 
						</td>
					</tr>
					<tr>
		   				<td colspan="2" align="right">
							<input type="submit" value="<?php echo $BTN_SAVE;?>" class="Button">								
						</td>
		   			</tr>
				</table>
	<input type="hidden" name="language" value="<?php echo $_REQUEST["language"];?>">
	<input type="hidden" name="nCirculationFormID" value="<?php echo $_REQUEST["cfid"];?>">
	<input type="hidden" name="nCirculationHistoryID" value="<?php echo $_REQUEST["chid"];?>">
	<input type="hidden" name="listid" value="<?php echo $_REQUEST["listid"];?>">
	<input type="hidden" name="cpid" value="<?php echo $_REQUEST["cpid"];?>">
</form>
</body>
</html>