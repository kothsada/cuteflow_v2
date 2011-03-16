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
	
	require_once '../../config/config.inc.php';
	require_once '../../config/db_connect.inc.php';
	require_once '../../language_files/language.inc.php';
	require_once '../../lib/datetime.inc.php';
	require_once '../../lib/viewutils.inc.php';
	require_once '../../pages/CCirculation.inc.php';
	
	$language 		= $_REQUEST['language'];
	
	// get all users
	$arrUsers = array();
	$strQuery = "SELECT * FROM cf_user WHERE bDeleted = 0 ORDER BY strLastName ASC";
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
	
	// get the default mailinglist
	$query 	= "SELECT * FROM cf_mailinglist WHERE bIsDefault = 1";
	$result = mysql_query($query);
	if ($result)
	{
		$resultRow = mysql_fetch_array($result);
		if ($resultRow)
		{
			$curListId = $resultRow['nID'];
		}
	}
	
	// get the slotId - it has to be only one, otherwise this extension fails
	$query 	= "SELECT nSlotId FROM cf_slottouser WHERE nMailingListId = $curListId";
	$result = mysql_query($query);
	if ($result)
	{
		$resultRow = mysql_fetch_array($result);
		if ($resultRow)
		{
			$curSlotId = $resultRow['nSlotId'];
		}
	}
	
	$AdditionalText 	= new Database_AdditionalText();
	$additionalTexts	= $AdditionalText->getByParams();
	$additionalTextDefaultValue = $AdditionalText->getDefaultValue();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="../../pages/format.css" type="text/css">
	
	<script src="../../lib/prototype/prototype.js" type="text/javascript"></script>
	<script language="JavaScript">
	<!--
		document.onkeyup 	= filterUsers;
		var jsReceiverId 	= 0;
		var jsFocus			= 'none';
		
		function validateQC()
		{		
			var jsCirculationName	= document.getElementById('strCirculationName').value;
			
			if (jsCirculationName.length < 3)
			{
				alert('Bitte geben Sie einen Umlaufnamen ein.');
				return false;
			}
			
			if (jsReceiverId < 1)
			{
				alert('Bitte wählen Sie einen Empfänger.');
				return false;
			}
			return true;
		}
		
		function setReceiver(myReceiverId)
		{
			jsReceiverId 		= myReceiverId;
			jsSlotId			= '<?php echo $curSlotId ?>';
			mailinglistField 	= document.getElementById('MAILLIST');
			fieldValue			= jsSlotId + '_' + jsReceiverId + '_1';
			
			mailinglistField.name 	= fieldValue + '_MAILLIST';
			mailinglistField.value 	= fieldValue;
		}
		
		function filterUsers()
		{
			if (jsFocus != 'none')
			{
				strFilter = document.getElementById('user_filter').value;
				new Ajax.Request
				(
					'ck_ajax_getusers.php',
					{
						onSuccess : function(resp) 
						{
							document.getElementById('available_users').innerHTML = resp.responseText;
							jsReceiverId = 0;
						},
				 		onFailure : function(resp) 
				 		{
				   			alert("Oops, there's been an error.");
				 		},
				 		parameters : "strFilter=" + strFilter + "&language=<?php echo $_REQUEST['language'] ?>"
					}
				);
			}
		}
		
		function browsePlaceholders()
		{
			url = "../../pages/selectplaceholder_addtext.php?language=<?php echo $_REQUEST['language'] ?>";
			open(url,"BrowsePlaceholder","width=300,height=190,status=no,menubar=no,resizable=no,scrollbars=no");		
		}
		
		function insertPlaceholder(Value)
		{
			var strCurrentContent = document.getElementById('strAdditionalText').value;
			var strNewContent = strCurrentContent + Value;
			document.getElementById('strAdditionalText').value = strNewContent;
		}
		
		function setAdditionalText(jsAdditionalTextId)
		{
			if (jsAdditionalTextId > 0)
			{
				new Ajax.Request
				(
					'../../pages/ajax_getadditionaltext.php',
					{	
						onSuccess : function(resp) 
						{
							document.getElementById('strAdditionalText').value = resp.responseText;
						},
				 		onFailure : function(resp) 
				 		{
				   			alert("Oops, there's been an error.");
				 		},
				 		parameters : 'language=<?php echo $_REQUEST['language']; ?>&action=showValue&additionalTextId=' + jsAdditionalTextId
					}
				);
			}
			else
			{
				document.getElementById('strAdditionalText').value = '';
			}
		}
	</script>
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>	
</head>
<body>
	
	<br>
	<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_CIRCULATION ?>
	</span>
	<br><br>
	
	<form ENCTYPE="multipart/form-data" METHOD="POST" action="../../pages/editcirculation_write.php" id="EditCirculation" name="EditCirculation" onsubmit="return validateQC();">
	
		<table width="700" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
			<tr>
				<td class="table_header" colspan="2" style="border-bottom: 3px solid #ffa000;" colspan="2">
					<?php echo $CIRCULATION_EDIT_FORM_HEADER ?>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" width="50%">
					
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td align="left" valign="top" style="padding: 4px;">
								<b><?php echo $CIRCULATION_EDIT_NAME ?></b>
							</td>
							<td align="left" valign="top" style="padding: 4px;">
								<input id="strCirculationName" name="strCirculationName" type="text" class="FormInput" style="width:202px; padding: 1px;" value="entgangener Anruf">
							</td>
						</tr>
						<tr>
							<td align="left" valign="middle" style="padding: 4px;">
								<b><?php echo $CIRCULATION_EDIT_ADDITIONAL_TEXT ?></b>
							</td>
							<td align="left" valign="top" style="padding: 4px;">
								<div style="float: left;">
									<select class="FormInput">
										<option onClick="setAdditionalText(0);">(ohne Vorlage)</option>
										<?php
										$additionalTexts = $AdditionalText->getByParams();
										$max = sizeof($additionalTexts);
										for ($index = 0; $index < $max; $index++)
										{
											$additionalText = $additionalTexts[$index];
											
											$id 		= $additionalText['id'];
											$title 		= $additionalText['title'];
											$content	= $additionalText['content'];
											$is_default	= $additionalText['is_default'];
											?>
											<option onClick="setAdditionalText(<?php echo $id ?>);" <?php if ($is_default) echo 'selected' ?>><?php echo $title ?></option>
											<?php
										}
										?>
									</select>
								</div>
								<div>
									<img title="<?php echo escapeDouble($INSERT_PLACEHOLDER);?>" src="../../images/grid_insert_row_style_2_16.gif" style="margin-left: 4px; height: 16px; border: 1px solid #666; background: #eeeeee; cursor: pointer;" onClick="browsePlaceholders();">
								</div>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" colspan="2" style="padding: 4px;">
								<textarea cols="54" rows="7" name="strAdditionalText" id="strAdditionalText" class="FormInput" style="padding: 2px;"><?php echo $additionalTextDefaultValue?></textarea>
							</td>
						</tr>
					</table>
					
				</td>
				<td align="left" valign="top" width="50%">
					
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td align="center" valign="top" style="padding: 0px 4px 4px 4px;">
								
								<table cellpadding="0" cellspacing="0" width="300" style="margin-bottom: 7px;">
									<tr>
										<td align="left" height="25" colspan="1">
											<b>Empfänger wählen:</b>
										</td>
									</tr>
									<tr>
										<td align="left" height="25">
											Filter: <input type="text" name="user_filter" id="user_filter" class="InputText" style="width: 200px; margin-left: 15px;" onFocus="jsFocus = 'filter';" onBlur="jsFocus = 'none';">
										</td>
									</tr>
								</table>
								
								<div style="height: 350px; width: 300px; overflow: auto; border: 1px solid #aaa; background: #fff;" id="available_users">
									<table cellpadding="2" cellspacing="0" style="background-color: white;" width="100%">
										<tbody id="AvailableUsers">
											<?php
											foreach ($arrUsers as $arrUser)
											{
												$sid = $arrUser['nID'];
												?>
												<tr onMouseOver="this.style.background = '#ddd;'" onMouseOut="this.style.background = '#fff;'" onClick="document.getElementById('receiver_<?php echo $sid ?>').checked = 'true'; setReceiver(<?php echo $sid ?>);" style="cursor: pointer;">
													<td width="16px" style="border-top:1px solid Silver;" valign="middle">
														<input type="radio" name="receiver" id="receiver_<?php echo $sid ?>" value="<?php echo $sid ?>">
													</td>
													<td width="20px" style="border-top:1px solid Silver;" valign="middle">
														<img src="../../images/singleuser.gif" height="19" width="16">
													</td>
													<td style="border-top:1px solid Silver;" valign="middle">
														<?php echo $arrUser['strUserId'] ?>
													</td>
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
			<tr>
				<td style="border-top: 1px solid #ffa000; padding: 6px;" align="left">
					<input type="button" class="button" value="<?php echo $BTN_CANCEL ?>" onClick="location='../../pages/showcirculation.php?language=<?php echo $language ?>&archivemode=0&start=1&bFirstStart=true'">
				</td>
				<td style="border-top: 1px solid #ffa000; padding: 6px;" align="right">
					<input type="submit" class="button" value="<?php echo $BTN_COMPLETE ?>">
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="SuccessArchive" value="on">
		<input type="hidden" name="step2" value="ok">
		<input type="hidden" name="changeMailinglist" value="1">
		<input type="hidden" name="MAILLIST" id="MAILLIST" value="">
		<input type="hidden" name="listid" value="<?php echo $curListId ?>">
		<input type="hidden" name="language" value="<?php echo $language ?>">
	
	</form>

</body>
</html>