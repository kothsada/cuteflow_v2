<?php
	include	('../language_files/language.inc.php');
	header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");
	echo '<?xml version="1.0" encoding="'.$DEFAULT_CHARSET.'"?>';
	
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');
	
	$nCurFilterID	= $_REQUEST['nFilterID'];
	
	$objCirculation = new CCirculation();
	
	$arrFilter		= $objCirculation->getFilter($nCurFilterID);

	$objCirculation = new CCirculation();
	
	$arrAllUsers 		= $objCirculation->getAllUsers();
	$arrAllMailingLists	= $objCirculation->getAllMailingLists();
	$arrAllInputFields	= $objCirculation->getMyInputFields();
	$arrAllTemplates	= $objCirculation->getAllTemplates();
		
?>
	<table cellspacing="2" cellpadding="2" width="100%">
		<tr height="26">
			<td width="200" valign="top">
				<?php echo $CIRCULATION_MNGT_NAME; ?>
			</td>
			<td valign="top" colspan="2">
				<table width="100%" cellspacing="0" cellpadding="0">
				<tr><td align="left">
					<input type="text" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Name" id="FILTER_Name" value="<?php echo $arrFilter["strName"];?>">
				</td><td align="right">
					<div id="select_filter" style="width: 155px; height: 21px; background: url(../images/combo_dummy.jpg); text-align: left;" onClick="show_filters();">
    					<table cellpadding="0" cellspacing="0" width="155">
    					<tr><td style="padding: 2px; padding-left: 5px;">
    						<?php echo $FILTER_CHOOSE_FILTER; ?>
    					</td>
    					</tr>
    					</table>
   					</div>
				</td></tr>
				</table>
			</td>
		</tr>
		<tr height="26">
			<td valign="top">
				<?php echo $CIRCULATION_MNGT_CURRENT_SLOT; ?>
			</td>
			<td valign="top">
				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Station" id="FILTER_Station">
					<option value="0"><?php echo $FILTER_STATION; ?></option>
					<?php
						$nMax = sizeof($arrAllUsers);
						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
						{
							$arrUser 		= $arrAllUsers[$nIndex];
							$nUserID 		= $arrUser['nID'];
							$strUsername 	= $arrUser['strUserId'];
							
							if ($arrFilter['nStationID'] ==  $nUserID)
							{
								echo '<option value="'.$nUserID.'" selected>'.$strUsername.'</option>';
							}
							else
							{
								echo '<option value="'.$nUserID.'">'.$strUsername.'</option>';
							}
						}                        							
					?>
				</select>
			</td>
		</tr>
		<tr height="26">
   			<td valign="top">
   				<?php echo $CIRCDETAIL_SENDER; ?>
   			</td>
   			<td valign="top">
   				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Sender" id="FILTER_Sender">
   					<option value="0"><?php echo $FILTER_SENDER; ?></option>
   					<?php
   						$nMax = sizeof($arrAllUsers);
   						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
   						{
   							$arrUser 		= $arrAllUsers[$nIndex];
   							
   							if ($arrUser["nAccessLevel"] > 0)
   							{
    							$nUserID 		= $arrUser['nID'];
    							$strUsername 	= $arrUser['strUserId'];
    							
    							if ($arrFilter["nSenderID"] == $nUserID)
    							{
    								echo '<option value="'.$nUserID.'" selected>'.$strUsername.'</option>';
    							}
    							else
    							{
    								echo '<option value="'.$nUserID.'">'.$strUsername.'</option>';
    							}
   							}
   						}                        							
   					?>
   				</select>
   			</td>
   		</tr>
		<tr height="26">
			<td valign="top">
				<?php echo $CIRCULATION_MNGT_WORK_IN_PROCESS; ?>
			</td>
			<td valign="top">
				<?php echo $FILTER_FROM; ?> <input type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_DaysInProgress_Start" id="FILTER_DaysInProgress_Start" value="<?php echo $arrFilter['nDaysInProgress_Start'];?>">
				<?php echo $FILTER_TO; ?> <input type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px; margin-left: 33px;" name="FILTER_DaysInProgress_End" id="FILTER_DaysInProgress_End" value="<?php echo $arrFilter['nDaysInProgress_End'];?>">
			</td>
		</tr>
		<tr height="26">
			<td valign="top">
				<?php echo $CIRCULATION_MNGT_SENDING_DATE; ?>
			</td>
			<td valign="top">
				<table cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle">
						<?php echo $FILTER_FROM; ?> <input readonly type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Date_Start" id="FILTER_Date_Start" value="<?php echo $arrFilter['strSendDate_Start'];?>">
					</td>
					<td valign="top">
						<a href="#"><img style="margin: 2px 20px 0px 0px;" src="../images/calendar.gif" title="<?php echo escapeDouble($SELECT_DATE);?>" border="0" id="FILTER_Date_Start_Button"></a>
					</td>
					<td valign="middle">
						<?php echo $FILTER_TO; ?> <input readonly type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Date_End" id="FILTER_Date_End" value="<?php echo $arrFilter['strSendDate_End'];?>">
					</td>
					<td valign="top">
						<a href="#"><img style="margin: 2px 7px 0px 0px;" src="../images/calendar.gif" title="<?php echo escapeDouble($SELECT_DATE);?>" border="0" id="FILTER_Date_End_Button"></a>
					</td>
				</tr>
				</table>
				
				 <script type="text/javascript">
				 Calendar.setup(
				    {
				      inputField  : "FILTER_Date_Start",         // ID of the input field
				      ifFormat    : "%d.%m.%Y",    // the date format
				      button      : "FILTER_Date_Start_Button"       // ID of the button
				    }
				  );
				  Calendar.setup(
				    {
				      inputField  : "FILTER_Date_End",         // ID of the input field
				      ifFormat    : "%d.%m.%Y",    // the date format
				      button      : "FILTER_Date_End_Button"       // ID of the button
				    }
				  );
				  </script>
			</td>
		</tr>
		<tr height="26">
			<td valign="top">
				<?php echo $MENU_MAILINGLIST; ?>
			</td>
			<td valign="top">
				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Mailinglist" id="FILTER_Mailinglist">
					<option value="0"><?php echo $FILTER_MAILINGLIST; ?></option>
					<?php
						$nMax = sizeof($arrAllMailingLists);
						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
						{
							$arrRow 	= $arrAllMailingLists[$nIndex];
							$nID 		= $arrRow['nID'];
							$strTitle 	= $arrRow['strName'];
							
							if ($arrFilter['nMailinglistID'] ==  $nID)
							{
								echo '<option value="'.$nID.'" selected>'.$strTitle.'</option>';
							}
							else
							{
								echo '<option value="'.$nID.'">'.$strTitle.'</option>';
							}
						}                        							
					?>
				</select>
			</td>
		</tr>
		<tr height="26">
			<td valign="top">
				<?php echo $SHOW_CIRCULATION_TEMPLATE; ?>
			</td>
			<td valign="top">
				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Template" id="FILTER_Template">
					<option value="0"><?php echo $FILTER_TEMPLATE; ?></option>
					<?php
						$nMax = sizeof($arrAllTemplates);
						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
						{
							$arrRow 	= $arrAllTemplates[$nIndex];
							$nID 		= $arrRow['nID'];
							$strTitle 	= $arrRow['strName'];
							
							if ($arrFilter['nTemplateID'] == $nID)
							{
								echo '<option value="'.$nID.'" selected>'.$strTitle.'</option>';
							}
							else
							{
								echo '<option value="'.$nID.'">'.$strTitle.'</option>';
							}
						}                        							
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">
				<?php echo $FILTER_FREE; ?>
			</td>
			<td valign="top" align="left">
				<div id="custom">
				
				<script language="javascript">
					function startExtendedFilter(nMyID)
					{
						var strCurID = 'FILTERCustom_Field--' + nMyID;
						
						var nCurInputFieldID = document.getElementById(strCurID).value;
						                    						
						var strMyParameters = "nCurInputFieldID=" + nCurInputFieldID + "&nCurRunningID=" + nMyID;
						
						new Ajax.Request
						(
							"showcirculation_extendedfilter.php",
							{
								onSuccess : function(resp) 
								{
									if (resp.responseText != '')
									{
										var strCurDiv = 'extendedDIV--' +nMyID 
										document.getElementById(strCurDiv).innerHTML = resp.responseText;
									}
									else
									{
										
									}
								},
						 		onFailure : function(resp) 
						 		{
						   			alert("Oops, there's been an error.");
						 		},
						 		parameters : strMyParameters
							}
						);
					}
				</script>
				
				<?php
				
					$strCustomFilter = $arrFilter['strCustomFilter'];
					
					$arrSplit = split('----', $strCustomFilter);
					
					$nAmountOfCustomFilters = sizeof($arrSplit) - 1;
					
					$nMax = $nAmountOfCustomFilters;
					for($nIndex = 0; $nIndex < $nMax; $nIndex++)
					{
						$strCurFilter = $arrSplit[$nIndex];
						
						$arrCurSplit = split('__', $strCurFilter);
						
						$arrFILTERCustom[$nIndex]['nInputFieldID']	= $arrCurSplit[0];
						$arrFILTERCustom[$nIndex]['nOperatorID'] 	= $arrCurSplit[1];
						$arrFILTERCustom[$nIndex]['strValue'] 		= $arrCurSplit[2];
					}
					
					if ($arrFILTERCustom[0]['strValue'] == '')
					{
						?>
						
						<table cellspacing="0" cellpadding="0" id="FILTERCustom_TableID--0">
	    				<tr>
	    					<td valign="middle" align="left">
	            				<div style="float:left;">
		            				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTERCustom_Field--0" id="FILTERCustom_Field--0" onChange="startExtendedFilter(0)">
		            					<option value="0"><?php echo $FILTER_FIELD; ?></option>
		            					<?php
		            						$nMax = sizeof($arrAllInputFields);
		            						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
		            						{
		            							$arrRow 	= $arrAllInputFields[$nIndex];
		            							$nID 		= $arrRow['nID'];
		            							$strTitle 	= $arrRow['strName'];
		            							
		            							echo '<option value="'.$nID.'">'.$strTitle.'</option>';
		            						}                        							
		            					?>
		            				</select>
		            				<select style="width: 90px; font-family: arial; font-size: 12px;" name="FILTERCustom_Operator--0" id="FILTERCustom_Operator--0">
		            					<option value="0"><?php echo $FILTER_OPERATOR; ?></option>
		            					<option value="1">=</option>
		            					<option value="2">&lt;</option>
		            					<option value="3">&gt;</option>
		            					<option value="4">&lt;=</option>
		            					<option value="5">&gt;=</option>
		            					<option value="6">~ (like)</option>
		            				</select>
	            				</div>
	            				
	    						<div id="extendedDIV--0" style="float:left; margin: 0px 4px 0px 2px;">
	    							<input type="text" name="FILTERCustom_Value--0" id="FILTERCustom_Value--0" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
	    						</div>
	    					</td>
	    					<td valign="middle" align="left">
	    						<a href="#"><img src="../images/edit_add.gif" border="0" onClick="addCustom();"></a>
	    					</td>
	    				</tr>
	    				</table>
						
						<?php
						$nIndexValues = 0;
					}
					else
					{
						$nMax = sizeof($arrFILTERCustom);
						for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
						{
							$arrCurFILTERCustom = $arrFILTERCustom[$nIndex];
							
							$nInputFieldID	= $arrCurFILTERCustom['nInputFieldID'];
							$nOperatorID	= $arrCurFILTERCustom['nOperatorID'];
							$strValue		= $arrCurFILTERCustom['strValue'];
							
							$nFieldType		= $objCirculation->getFieldType($nInputFieldID);
							?>	                        						
							
							<table cellspacing="0" cellpadding="0" id="FILTERCustom_TableID--<?php echo $nIndex; ?>">
	        				<tr>
	        					<td valign="middle" align="left" width="420">
	                				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTERCustom_Field--<?php echo $nIndex; ?>" id="FILTERCustom_Field--<?php echo $nIndex; ?>">
	                					<option value="0"><?php echo $FILTER_FIELD; ?></option>
	                					<?php
	                						$nMyMax = sizeof($arrAllInputFields);
	                						for($nMyIndex = 0; $nMyIndex < $nMyMax; $nMyIndex++)
	                						{
	                							$arrRow 	= $arrAllInputFields[$nMyIndex];
	                							$nID 		= $arrRow['nID'];
	                							$strTitle 	= $arrRow['strName'];
	                							
	                							if($nInputFieldID == $nID)
	                							{
	                								echo '<option value="'.$nID.'" selected>'.$strTitle.'</option>';
	                							}
	                							else
	                							{
	                								echo '<option value="'.$nID.'">'.$strTitle.'</option>';
	                							}
	                						}                        							
	                					?>
	                				</select>
	                				<select style="width: 90px; font-family: arial; font-size: 12px;" name="FILTERCustom_Operator--<?php echo $nIndex; ?>" id="FILTERCustom_Operator--<?php echo $nIndex; ?>">
	                					<option value="0"><?php echo $FILTER_OPERATOR; ?></option>
	                					<option value="1" <?php if($nOperatorID == 1) { echo 'selected'; } ?>>=</option>
	                					<option value="2" <?php if($nOperatorID == 2) { echo 'selected'; } ?>>&lt;</option>
	                					<option value="3" <?php if($nOperatorID == 3) { echo 'selected'; } ?>>&gt;</option>
	                				</select>
	        						<?php
	        						if (($nFieldType == '6') || ($nFieldType == '8'))
									{
										$strQuery 	= "SELECT * FROM cf_inputfield WHERE nID = '$nInputFieldID'";
										$nResult 	= @mysql_query($strQuery);
										
										$arrCurInputField = mysql_fetch_array($nResult, MYSQL_ASSOC);
										$strCurStdValue = $arrCurInputField['strStandardValue'];
										
										switch ($nFieldType)
										{
											case '6':				
												$arrSplit = split('---',$strCurStdValue);
												
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
												
												?>				
													<select name="FILTERCustom_Value--<?php echo $nIndex; ?>" id="FILTERCustom_Value--<?php echo $nIndex; ?>">				
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
												
												?>				
													<select name="FILTERCustom_Value--<?php echo $nIndex; ?>" id="FILTERCustom_Value--<?php echo $nIndex; ?>">				
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
									elseif ($nFieldType == '2')
									{
										?>
										<input type="checkbox" name="FILTERCustom_Value--<?php echo $nIndex; ?>" id="FILTERCustom_Value--<?php echo $nIndex; ?>" style="padding-left: 20px; padding-right: 20px;" <?php if($strValue == 'on') { echo 'checked'; } ?>>
										<?php
									}
									else
									{
										?>
										<input type="text" name="FILTERCustom_Value--<?php echo $nIndex; ?>" id="FILTERCustom_Value--<?php echo $nIndex; ?>" value="<?php echo $strValue; ?>" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
										<?php
									}
									?>
	        						
	        						
	        						
	        					</td>
	        					<td valign="middle" align="left">
	        						<a href="#"><img src="../images/edit_add.gif" border="0" onClick="addCustom();"></a>
	        						<?php if ($nIndex > 0) { ?>
	        						<a href="#"><img src="../images/edit_remove.gif" border="0" onClick="removeCustom('<?php echo $nIndex; ?>');"></a>
	        						<?php } ?>
	        					</td>
	        				</tr>
	        				</table>
							<?php
						}
					}
				?>
				<script language="javascript">
					//setCurID(<?php echo ($nIndexValues+1); ?>);
				</script>
				
				</div>
			</td>
		</tr>
		<tr><td height="5" colspan="5" style="border-bottom: 1px solid #666;"></td></tr>
		<tr>
			<td colspan="2">
				<input type="button" class="Button" value="<?php echo $FILTER_SAVE_FILTER; ?>" onClick="addFilter();">
			</td>
		</tr>
	</table>
	