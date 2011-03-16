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
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';
	
	$_REQUEST['listid']	= strip_tags($_REQUEST['nMailinglistID']);
	$listid				= $_REQUEST['listid'];
	
	$objCirculation = new CCirculation();
	
	$arrMailinglist 		= $objCirculation->getMailinglist($listid);	// corresponding mailinglist
	$_REQUEST['templateid']	= $arrMailinglist['nTemplateId'];
	
	
	// get all users
	$arrUsers = array();
	$strQuery = "SELECT * FROM cf_user WHERE bDeleted <> 1 ORDER BY strLastName ASC";
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
	
  	// get all slots for the given template
	$arrSlots = array();
	$strQuery = "SELECT * FROM cf_formslot WHERE nTemplateID=".$_REQUEST["templateid"]."  ORDER BY nSlotNumber ASC";
	$nResult = mysql_query($strQuery, $nConnection);
	if ($nResult)
	{
		if (mysql_num_rows($nResult) > 0)
		{
			while (	$arrRow = mysql_fetch_array($nResult))
			{
				$arrSlots[] = $arrRow;
			}
		}
	}
	
	if (-1 != $listid)
	{
		// get the mailing list
		$query = "select * from cf_mailinglist WHERE nID = '".$_REQUEST['listid']."'";
		$nResult = mysql_query($query, $nConnection);
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$arrMailingList = mysql_fetch_array($nResult);				
			}
		}		
	}
	
	header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");
	echo '<?xml version="1.0" encoding="'.$DEFAULT_CHARSET.'"?>';
?>

	<table width="100%" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3" id="step2_table" name="step2_table">
		<tr>
			<td class="table_header" colspan="3" style="border-bottom: 3px solid #ffa000;">
				<?php echo $INSTALL_STEP ?> 2/3: <?php echo $CIRCULATION_EDIT_MAILINGLIST_EDIT ?>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr>
			<td>
				<input type="radio" name="changeMailinglist" value="0" checked onClick="document.getElementById('layer').style.display = 'block';"><?php echo $EDITCIRC_DEF ?><br>
				<input type="radio" name="changeMailinglist" value="1" onClick="showMailinglist();"><?php echo $EDITCIRC_ADA ?>
			</td>
		</tr>
		<tr><td height="10"></td></tr>
		<tr><td align="center" valign="top">

		<table width="750" cellspacing="0" cellpadding="0" style="background: #fff; border: 1px solid silver;">
			<tr>
				<td align="left" valign="top" style="padding: 15px;">
					<table cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
						<tr>
							<td align="left">
								<?php echo $MAILLIST_EDIT_FORM_HEADER_STEP2 ?>:
							</td>
						</tr>
			    	</table>
				
					<table style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="2">
						<?php
						$nSlotNumber = 1;
						foreach ($arrSlots as $arrSlot)
						{
							?>
							<tr>
								<td style="background: gray; color: white;" colspan="2" height="25">
									<?php echo $MAILLIST_EDIT_FORM_SLOT.' '.$nSlotNumber.': '.$arrSlot['strName'] ?>
								</td>
							</tr>
							<tr>
								<td align="left valign="top" style="padding-bottom:5px;">							
									<table cellpadding="2" cellspacing="0" class="BorderSilver" style="background-color:white;" width="100%">
										<tr style="background-color: silver;">
											<td width="47"><?php echo $MAILINGLIST_EDIT_POS ?></td>
											<td><?php echo $MAILINGLIST_EDIT_NAME ?></td>
										</tr>
									</table>
										<?php
										//--- open database
										$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
										if ($nConnection)
										{
											//--- get maximum count of users
  												if (mysql_select_db($DATABASE_DB, $nConnection))
   											{
												$strQuery = "SELECT * FROM cf_slottouser WHERE nMailingListID = '".$_REQUEST['listid']."' AND nSlotId = '".$arrSlot['nID']."' ORDER BY nPosition ASC";
									    		$nResult = mysql_query($strQuery, $nConnection);
									    		if ($nResult)
									    		{
									    			?>
								    				<div style="height: 100px; width: 300px; overflow: auto;">
														<table cellpadding="2" cellspacing="0" class="BorderSilver" style="background-color:white;" width="100%">
															<tbody id="AttachedUsers_<?php echo $arrSlot['nID'];?>">
								    							<?php
												    			if (mysql_num_rows($nResult) > 0)
												    			{
												    				while (	$arrRow = mysql_fetch_array($nResult))
												    				{
																		$arrUser = $arrUsers[$arrRow['nUserId']];
																		?>
																		<tr>
																			<td width="20px" style="border-top: 1px solid silver;">
																				<?php echo $arrRow['nPosition'] ?>
																			</td>
																		
																			<?php
																			if ($arrRow['nUserId'] != -2)
																			{
																				?>
																				<td width="22px" style="border-top: 1px solid silver;"><img src="../images/singleuser.gif" height="19" width="16"></td>
																				<td style="border-top:1px solid silver;"><?php echo $arrUser['strUserId'] ?></td>
																				<?php
																				$s2uid = $arrSlot['nID'].'_'.$arrUser['nID'].'_'.$arrRow['nPosition'];
																			}
																			else
																			{
																				?>
																				<td width="22px" style="border-top: 1px solid silver;"><img src="../images/user_green.gif" height="19" width="16"></td>
																				<td style="border-top: 1px solid silver;"><?php echo $SELF_DELEGATE_USER ?></td>
																				<?php
																				$s2uid = $arrSlot["nID"]."_-2_".$arrRow["nPosition"];
																			}
																			?>
																		
																		
																			<td style="border-top:1px solid silver;" align="right">
																				<a href="javascript:up('<?php echo $arrSlot['nID'] ?>', '<?php echo $arrRow['nPosition'] ?>')"><img border="0" src="../images/up.gif" height="16" width="16"></a><a href="javascript:down('<?php echo $arrSlot['nID'] ?>', '<?php echo $arrRow['nPosition'] ?>')"><img border="0" src="../images/down.gif" height="16" width="16"></a><a href="javascript: remove(<?php echo $arrSlot['nID'] ?>, <?php echo $arrRow['nPosition'] ?>)"><img border="0" src="../images/edit_remove.gif" height="16" width="16"></a>
																				<input type="hidden" name="<?php echo $s2uid ?>_MAILLIST" id="<?php echo $s2uid ?>_MAILLIST" value="<?php echo $s2uid ?>">
																			</td>
																		</tr>
																		<?php
																	}
																}
																?>
															</tbody>
														</table>								
													</div>
												</td>
												<td valign="top" align="left" style="padding-top: 10px; padding-left: 10px; padding-right: 10px;">								
													<input type="button" class="Button" value="<?php echo $BTN_ADD;?>" onclick="addUsers('<?php echo $arrSlot['nID'] ?>')">
												</td>
												<?php
											}
										}
									}
									?>
							</tr>
							<?php
							$nSlotNumber++;
						}
						?>
					</table>
				</td>
				
				<td align="left" valign="top" style="padding: 15px;">
					<table cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
						<tr>
							<td align="left">
								<?php echo $MAILINGLIST_EDIT_AVAILABLE_USER ?>
							</td>
						</tr>
			    	</table>
				
					<table style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<table cellpadding="2" cellspacing="0" style="background-color: white;" width="300">
									<tr style="background-color: gray;">
										<td align="left" height="25" style="color: #fff;">
											<?php echo $CIRCULATION_MNGT_FILTER ?>
										</td>
										<td align="center">
											<input type="text" name="user_filter" id="user_filter" class="InputText" style="width: 200px; background: #efefef;">
										</td>
									</tr>
								</table>
								<div style="height: 500px; width: 300px; overflow: auto;" id="available_users">
									<table cellpadding="2" cellspacing="0" style="background-color:white;" width="100%">
										<tbody id="AvailableUsers">
										
											<?php $sid = -2 ?>
											
											<tr>
											<td width="16px" style="border-top:1px solid Silver;" valign="middle"><input type="checkbox" id="<?php echo $sid ?>" name="<?php echo $sid ?>" value="<?php echo $sid ?>"></td>
											<td width="20px" style="border-top:1px solid Silver;" valign="middle"><img src="../images/user_green.gif" height="19" width="16"></td>
											<td style="border-top:1px solid Silver;" valign="middle"><?php echo $SELF_DELEGATE_USER ?></td>
											</tr>
											
											<?php
											foreach ($arrUsers as $arrUser)
											{
												$sid = $arrUser['nID'];
												?>
												<tr>
												<td width="16px" style="border-top:1px solid Silver;" valign="middle"><input type="checkbox" id="<?php echo $sid ?>" name="<?php echo $sid ?>" value="<?php echo $sid ?>"></td>
												<td width="20px" style="border-top:1px solid Silver;" valign="middle"><img src="../images/singleuser.gif" height="19" width="16"></td>
												<td style="border-top:1px solid Silver;" valign="middle"><?php echo $arrUser['strUserId'] ?></td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>
								</div>
							</td>
						</tr>
					</table>
					
				</td>
			</tr>
		</table>
		
		</td></tr>

		<tr>
			<td colspan="2" style="border-top: 1px solid #ffa000;padding: 6px 0px 4px 0px;" align="right">
				<table width="100%">
					<tr>
						<td align="left" width="25%">
							<input type="button" class="button" value="<< <?php echo $BTN_BACK ?>" onClick="showStep1();">
						</td>
						<td align="right" width="25%">
							<input type="button" class="button" value="<?php echo $BTN_CANCEL ?>" onClick="location='showcirculation.php?language=<?php echo $_REQUEST['language'] ?>&archivemode=0&start=1&bFirstStart=true'">
						</td>
						<td align="left" width="25%">
							<input type="submit" name="step2" class="button" value="<?php echo $BTN_COMPLETE ?>">
						</td>
						<td align="right" width="25%">
							<input type="button" class="button" value="<?php echo $BTN_NEXT ?> >>" onClick="showStep3();">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
