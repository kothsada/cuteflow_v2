<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
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
	include_once ("../config/config.inc.php");
	
	//--- open database
   	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
   	
   	if ($nConnection)
   	{
   		//--- get maximum count of users
   		if (mysql_select_db($DATABASE_DB, $nConnection))
   		{
			//-----------------------------------------------
    		//--- get all users
            //-----------------------------------------------
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
			
			//-----------------------------------------------
    		//--- get all slots for the given template
            //-----------------------------------------------
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
				//-----------------------------------------------
				//--- get the mailing list
				//-----------------------------------------------
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
		}
	}
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>" />
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script src="../lib/prototype/prototype.js" type="text/javascript"></script>
	<script src="../src/scriptaculous.js" type="text/javascript"></script>
	<script language="JavaScript">
	<!--
		function up(SlotId, nPosition)
		{
			if (nPosition > 1)
			{
				swapPosition (SlotId, nPosition, nPosition-1);
			}
		}
		
		function down(SlotId, nPosition)
		{
			strDestinationId = "AttachedUsers_"+SlotId;
			objDestinationTable = document.getElementById(strDestinationId);		
			nLastPos = getLastPosition(objDestinationTable);
			
			if (nPosition < nLastPos)
			{
				swapPosition (SlotId, nPosition+1, nPosition);
			}
		}
		
		//--- swapping nPos2 in front of nPos1
		function swapPosition (SlotId, nPos1, nPos2)
		{
			strDestinationId = "AttachedUsers_"+SlotId;
			objTable = document.getElementById(strDestinationId);
					
			//--- copy the pos2 in front of pos1
			objRow1 = findRow(objTable, nPos1);
			objRow2 = findRow(objTable, nPos2);
			
			if (objRow1)
			{
				objRow1.swapNode(objRow2);
			
				changePosition(objRow1, nPos2, SlotId);
				changePosition(objRow2, nPos1, SlotId);
			}
		}
		
		function changePosition(objRow, nPosNumber, SlotId)
		{
			nPosTd = getPosOfType(objRow.childNodes, "TD", 1);
			nHrefTd = getPosOfType(objRow.childNodes, "TD", 4);
			
			objRow.childNodes[nPosTd].innerHTML = nPosNumber;

			//--- change "up"-href
			nHref1Pos = getPosOfType(objRow.childNodes[nHrefTd].childNodes, "A", 1);
			objHref1 = objRow.childNodes[nHrefTd].childNodes[nHref1Pos];
			strUrl = "javascript:up("+SlotId+","+nPosNumber+")";
			objHref1.setAttribute("href", strUrl);
			
			//--- change "down"-href
			nHref2Pos = getPosOfType(objRow.childNodes[nHrefTd].childNodes, "A", 2);
			objHref2 = objRow.childNodes[nHrefTd].childNodes[nHref2Pos];
			strUrl = "javascript:down("+SlotId+","+nPosNumber+")";
			objHref2.setAttribute("href", strUrl);
			
			//--- change "remove"-href
			nHref3Pos = getPosOfType(objRow.childNodes[nHrefTd].childNodes, "A", 3);
			objHref3 = objRow.childNodes[nHrefTd].childNodes[nHref3Pos];
			strUrl = "javascript:remove("+SlotId+","+nPosNumber+")";
			objHref3.setAttribute("href", strUrl);
			
			//--- change the hidden input field
			nInputPos = getPosOfType(objRow.childNodes[nHrefTd].childNodes, "INPUT", 1);
			objInput = objRow.childNodes[nHrefTd].childNodes[nInputPos];
			
			strCurValue = objInput.getAttribute("value");
			
			Ids = strCurValue.split("_");
			strNewId = SlotId+"_"+Ids[1]+"_"+nPosNumber;
			objInput.setAttribute("id", strNewId);
			objInput.setAttribute("name", strNewId);
			objInput.setAttribute("value", strNewId);					
		}
		
		function remove(SlotId, nPosition)
		{
			strDestinationId = "AttachedUsers_"+SlotId;
			objTable = document.getElementById(strDestinationId);
			
			//--- remove row
			objRowDelete = findRow(objTable, nPosition);
			
			objTable.removeChild(objRowDelete);
			
			//--- renumber all following rows
			nLastPos = getLastPosition(objTable);
			for (nCurPos = nPosition+1; nCurPos <= nLastPos; nCurPos++)
			{
				objCurRow = findRow(objTable, nCurPos);
				changePosition(objCurRow, nCurPos-1, SlotId);											
			}
		}
	
		function addUsers(SlotId)
		{
			strDestinationId = 'AttachedUsers_'+SlotId;
			strSourceId = 'AvailableUsers';
			
			objSourceTable = document.getElementById(strSourceId);
			objDestinationTable = document.getElementById(strDestinationId);
			
			//--- get last position in destination table
			nLastPos = getLastPosition(objDestinationTable);
			for (i=0; i <objSourceTable.childNodes.length; i++)
			{
				nCheckboxPos = getPosOfType(objSourceTable.childNodes[i].childNodes, "TD", 1);
				if (-1 != nCheckboxPos)
				{
					if (objSourceTable.childNodes[i].childNodes[nCheckboxPos])
					{
						if (objSourceTable.childNodes[i].childNodes[nCheckboxPos].firstChild.checked)
						{
							nID = objSourceTable.childNodes[i].childNodes[nCheckboxPos].firstChild.getAttribute("id");
							nNamePos = getLastOfType(objSourceTable.childNodes[i].childNodes, "TD");
							strUserName = objSourceTable.childNodes[i].childNodes[nNamePos].innerHTML;
							
							//--- add element to table (as last item)
							nLastPos++;
							new_row=document.createElement("TR");
								first_cell=document.createElement("TD");
									first_cell.setAttribute("style", "border-top:1px solid Silver;");
									first_cell.setAttribute("width", "20px");
									first_cell.appendChild(document.createTextNode(nLastPos));
								new_row.appendChild(first_cell);
								
								second_cell=document.createElement("TD");
									if (nID == -2)
									{
										second_cell.appendChild(createImage("../images/user_green.gif", 16, 19));
									}
									else
									{
										second_cell.appendChild(createImage("../images/singleuser.gif", 16, 19));
									}
									second_cell.setAttribute("style", "border-top:1px solid Silver;");
									second_cell.setAttribute("width", "22px");
								new_row.appendChild(second_cell);
								
								third_cell=document.createElement("TD");
									third_cell.appendChild(document.createTextNode(strUserName));							
									third_cell.setAttribute("style", "border-top:1px solid Silver;");
								new_row.appendChild(third_cell);
								
								forth_cell=document.createElement("TD");
									forth_cell.setAttribute("style", "border-top:1px solid Silver; padding-right: px;");
									forth_cell.setAttribute("align", "right");
									forth_cell.setAttribute("width", "80px");								
									
									strUrl = "javascript:up("+SlotId+","+nLastPos+")";
									href = createHref(strUrl, "", "");
									href.appendChild(createImage("../images/up.gif", 16, 16));
									forth_cell.appendChild(href);
									
									strUrl = "javascript:down("+SlotId+","+nLastPos+")";
									href = createHref(strUrl, "", "");
									href.appendChild(createImage("../images/down.gif", 16, 16));
									forth_cell.appendChild(href);
									
									strUrl = "javascript:remove("+SlotId+","+nLastPos+")";
									href = createHref(strUrl, "", "");
									href.appendChild(createImage("../images/edit_remove.gif", 16, 16));
									forth_cell.appendChild(href);
									
									strNewId = SlotId+"_"+nID+"_"+nLastPos;
									hidden_field = document.createElement("INPUT");
										hidden_field.setAttribute("type", "hidden");
										hidden_field.setAttribute("id", strNewId);
										hidden_field.setAttribute("name", strNewId);
										hidden_field.setAttribute("value", strNewId);
									forth_cell.appendChild(hidden_field);
								new_row.appendChild(forth_cell);
								
							objDestinationTable.appendChild(new_row);
							
							//new: deselects the checkbox after adding it to the field
							objSourceTable.childNodes[i].childNodes[nCheckboxPos].firstChild.checked = false;
						}
					}
				}
			}				
		}
		
		function createHref(strUrl, strTarget, strAlt)
		{
			href = document.createElement("A");
			href.setAttribute("href", strUrl);
			href.setAttribute("target", strTarget);
			href.setAttribute("alt", strAlt);
			
			return href;
		}
		
		function createImage(strPath, nWidth, nHeight)
		{
			img = document.createElement("IMG");
			img.setAttribute("src", strPath);
			img.setAttribute("height", nHeight);
			img.setAttribute("width", nWidth);		
			img.setAttribute("border", 0);
				
			return img;
		}
		
		function getLastPosition(objTable)
		{	
			nLastPos = 0;		
			nTrPos = getLastOfType(objTable.childNodes, "TR");
			
			if (-1 != nTrPos)
			{
				nTdPos = getPosOfType(objTable.childNodes[nTrPos].childNodes, "TD", 1); 
				
				if (-1 != nTdPos)
				{
					nLastPos = parseInt(objTable.childNodes[nTrPos].childNodes[nTdPos].innerHTML);
				}
			}
			
			return nLastPos;
		}
		
		function findRow (objTable, nPosition)
		{
			for (x = 0; x < objTable.childNodes.length; x++)
			{
				if (objTable.childNodes[x].nodeName == "TR")
				{
					nPos = getPosOfType(objTable.childNodes[x].childNodes, "TD", 1);
					objTd = objTable.childNodes[x].childNodes[nPos];
					
					nCurPosition = Math.abs(objTd.innerHTML);
					
					if (nCurPosition == nPosition)
					{
						return objTable.childNodes[x];
					}
				}
			}
		}
		
		function getPosOfType(objCollection, strTag, Pos)
		{
			nTempPos = 0;
			for (iPos = 0; iPos < objCollection.length; iPos++)
			{
				if (objCollection[iPos].nodeName == strTag)
				{
					nTempPos++;
					
					if (nTempPos == Pos)
					{		
						return iPos;
					}
				}		
			}
			
			return -1;
		}
		
		function getLastOfType(objCollection, strTag)
		{
			for (ilPos = objCollection.length-1; ilPos >= 0; ilPos--)
			{
				if (objCollection[ilPos].nodeName == strTag)
				{
					return ilPos;
				}
			}
			
			return -1;
		}
		
		document.onkeyup = filterUsers;
		
		function filterUsers()
		{
			strFilter = document.getElementById('user_filter').value;
			
			new Ajax.Request
			(
				"ajax_getusers.php",
				{
					onSuccess : function(resp) 
					{
						document.getElementById('available_users').innerHTML = resp.responseText;
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "strFilter=" + strFilter + "&language=<?php echo $_REQUEST['language'] ?>"
				}
			);
		}

		if(!document.all){
			Node.prototype.swapNode = function (node) 
			{
	  			var nextSibling = this.nextSibling;
	  		  	var parentNode = this.parentNode;
				parentNode.replaceChild(this, node);
				parentNode.insertBefore(node, nextSibling);  
				return this;
			}
		}
	</script>
</head>
<body>
	
	<br>
	<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_MAILINGLIST ?>
	</span><br><br>

	<form action="editmailinglist_write.php" id="EditMailingList" name="EditMailingList">
		
		<table width="750" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" valign="top">
					<table cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
						<tr>
							<td align="left">
								<?php echo $MAILLIST_EDIT_FORM_HEADER_STEP2 ?> <?php if ($arrMailingList['strName']) echo '"'.$arrMailingList['strName'].'"' ?>
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
								<td align="left" valign="top" style="padding-bottom:5px;">
									<table cellpadding="2" cellspacing="0" class="BorderSilver" style="background-color:white;" width="100%">
										<tr style="background-color: silver;">
											<td width="47"><?php echo $MAILINGLIST_EDIT_POS ?></td>
											<td><?php echo $MAILINGLIST_EDIT_NAME ?></td>
										</tr>
									</table>
									<?php
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
																		<input type="hidden" name="<?php echo $s2uid ?>" id="<?php echo $s2uid ?>" value="<?php echo $s2uid ?>">
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
									?>
							</tr>
							<?php
							$nSlotNumber++;
						}
						?>
					</table>
				</td>
				
				<td align="left" valign="top">
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
												<td style="border-top:1px solid Silver;" valign="middle"><?php echo $arrUser['strUserId'] ?> (<?php echo $arrUser['strFirstName'].' ',$arrUser['strLastName'];?> )</td>
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
		
		<table cellspacing="0" cellpadding="3" align="left" width="750" style="border-top: 1px solid silver; margin-top: 10px;">
			<tr>
				<td align="left">
					<input type="button" class="Button" value="<?php echo $BTN_BACK ?>" onclick="history.back()">
				</td>
				<td align="right">
					<input type="submit" value="<?php echo $BTN_OK ?>" class="Button">
				</td>
			</tr>
		</table>
		<br><br>
		
		<input type="hidden" value="<?php echo $_REQUEST['listid'] ?>" id="listid" name="listid">
		<input type="hidden" value="<?php echo $_REQUEST['language'] ?>" id="language" name="language">
		<input type="hidden" value="<?php echo $_REQUEST['sort'] ?>" id="sort" name="sort">
		<input type="hidden" value="<?php echo $_REQUEST['start'] ?>" id="start" name="start">
		<input type="hidden" value="<?php echo $_REQUEST['templateid'] ?>" id="templateid" name="templateid">
		<input type="hidden" value="<?php echo $_REQUEST['strName'] ?>" id="strName" name="strName">
		<input type="hidden" value="<?php echo $_REQUEST['allowed_sender']?>" id="allowed_sender" name="allowed_sender" />
		
		<?php 
			$permissions = '';
			foreach ($_REQUEST['permission'] as $key=>$subarray) {
				foreach ($subarray as $subkey=>$value) {
					echo '<input type="hidden" name="permission['.$key.']['.$subkey.']" value="1" />';
				}
			}
		?>
		
	</form>

</body>
</html>