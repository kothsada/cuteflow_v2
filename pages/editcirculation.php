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
	session_start();
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../lib/datetime.inc.php';
	require_once '../lib/viewutils.inc.php';
	require_once 'CCirculation.inc.php';
	
	$sortDirection	= $_REQUEST['sortDirection'];
	$sortby			= $_REQUEST['sortby'];
	
	$language = $_REQUEST['language'];
	$pathToRoot = "../";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	
	<script src="../lib/prototype/prototype.js" type="text/javascript"></script>
	<script language="JavaScript">
		function getPosition(element)
		{
			var elem 	= element;
			var tagname = '';
			var x		= 0;
			var y		= 0;
			
			while ( (typeof(elem) == "object") && (typeof(elem.tagName) != "undefined") )
			{		    	
		    	y 		+= elem.offsetTop;
		    	x 		+= elem.offsetLeft;
		    	tagname = elem.tagName.toUpperCase();
				
				
				if (tagname == "BODY")
				{
					elem = 0;
				}
				
				if (typeof(elem) == "object")
				{
					if (typeof(elem.offsetParent) == "object")
					{
			        	elem = elem.offsetParent;
			        }
				}
			}

			position	= new Object();
			position.x	= x;
			position.y	= y;
			return position;
		}
								
		function showStep1()
		{
			document.getElementById('step1').style.display = 'block';
			document.getElementById('step2').style.display = 'none';
			document.getElementById('step3').style.display = 'none';
			
			
			if (document.getElementById('layer').style.display == 'block')
			{
				document.getElementById('layer').style.display = 'none';
				
				document.getElementById('bShowStep2_layer').value = 'true';
			}
			else
			{
				document.getElementById('bShowStep2_layer').value = 'false';
			}
		}
		
		function showStep2()
		{
			var nMailinglistID 		= document.getElementById('listid').value;			
			var strCirculationName	= document.getElementById('strCirculationName').value;
			
			if (document.getElementById('step3').style.display == 'block')
			{
				if (document.getElementById('layer3').style.display == 'block')
				{
					document.getElementById('layer3').style.display = 'none';
					
					document.getElementById('bShowStep3_layer').value = 'true';
				}
				else
				{
					document.getElementById('bShowStep3_layer').value = 'false';
				}
			}
			
			if (nMailinglistID != '')
			{
				if (strCirculationName != '')
				{
					if (document.getElementById('bShowStep2').value == 'true')
					{
						document.getElementById('loading').style.display = 'block';
						new Ajax.Request
						(
							"ajax_getmailinglist.php",
							{
								onSuccess : function(resp) 
								{
									var result = resp.responseText;
									
									document.getElementById('step1').style.display = 'none';
									document.getElementById('step2').style.display = 'block';
									document.getElementById('step3').style.display = 'none';
									
									document.getElementById('step2').innerHTML 	= result;
									document.getElementById('loading').style.display = 'none';
									
									
									var MyPos 	= getPosition(step2);
									var MyX		= MyPos.x;
									var MyY		= (MyPos.y) + 85;
									
									
									var dimensions 	= Element.getDimensions(step2);
								  	var curWidth 	= (dimensions.width);
								  	var curHeight 	= (dimensions.height) - 125;
									
									
									$(layer).style.height 	= curHeight + 'px';
									$(layer).style.width 	= curWidth + 'px';
									$(layer).style.left 	= MyX + 'px';
									$(layer).style.top 		= MyY + 'px';
									
									$(layer).style.display 	= 'block';
									
									document.getElementById('bShowStep2').value = 'false';
								},
						 		onFailure : function(resp) 
						 		{
						   			alert("Oops, there's been an error.");
						 		},
						 		parameters : "nMailinglistID=" + nMailinglistID + "&language=<?php echo $language; ?>"
							}
						);
					}
					else
					{
						document.getElementById('step1').style.display = 'none';
						document.getElementById('step2').style.display = 'block';
						document.getElementById('step3').style.display = 'none';
						
						if (document.getElementById('bShowStep2_layer').value == 'true')
						{
							document.getElementById('layer').style.display = 'block';
						}
					}
				}
				else
				{
					alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_NAME);?>');
				}
			}
			else
			{
				alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_MAILINGLIST);?>');
			}
		}

		function showStep3()
		{			
			var nMailinglistID 		= document.getElementById('listid').value;			
			var strCirculationName	= document.getElementById('strCirculationName').value;
			
			if (nMailinglistID != '')
			{
				if (strCirculationName != '')
				{
					if (document.getElementById('bShowStep3').value == 'true')
					{
						document.getElementById('loading').style.display = 'block';
						new Ajax.Request
						(
							"ajax_getvalues.php",
							{
								onSuccess : function(resp) 
								{
									var result = resp.responseText;
									
									document.getElementById('step1').style.display = 'none';
									document.getElementById('step2').style.display = 'none';
									document.getElementById('step3').style.display = 'block';
									document.getElementById('step3').innerHTML 	= result;
									document.getElementById('loading').style.display = 'none';
									
									
									var MyPos 	= getPosition(step3);
									var MyX		= MyPos.x;
									var MyY		= (MyPos.y) + 85;
									
									
									var dimensions 	= Element.getDimensions(step3);
								  	var curWidth 	= (dimensions.width);
								  	var curHeight 	= (dimensions.height) - 125;
									
									
									$(layer3).style.height 	= curHeight + 'px';
									$(layer3).style.width 	= curWidth + 'px';
									$(layer3).style.left 	= MyX + 'px';
									$(layer3).style.top 	= MyY + 'px';
									
									$(layer).style.display 		= 'none';
									$(layer3).style.display 	= 'block';
									document.getElementById('bShowStep3').value = 'false';

								},
						 		onFailure : function(resp) 
						 		{
						   			alert("Oops, there's been an error.");
						 		},
						 		parameters : "nMailinglistID=" + nMailinglistID + "&language=<?php echo $language; ?>"
							}
						);

					}
					else
					{
						document.getElementById('step1').style.display = 'none';
						document.getElementById('step2').style.display = 'none';
						document.getElementById('step3').style.display = 'block';
						
						if (document.getElementById('layer').style.display == 'block')
						{
							document.getElementById('layer').style.display = 'none';
							
							document.getElementById('bShowStep2_layer').value = 'true';
						}
						else
						{
							document.getElementById('bShowStep2_layer').value = 'false';
						}
						
						if (document.getElementById('bShowStep3_layer').value == 'true')
						{
							document.getElementById('layer3').style.display = 'block';
						}
					}
				}
				else
				{
					alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_NAME);?>');
				}
			}
			else
			{
				alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_MAILINGLIST);?>');
			}
		}
		
	// javascript for step 1
		function setMailingList(nId) {
			document.EditCirculation["listid"].value = nId;
			
			document.getElementById('bShowStep2').value = 'true';
			document.getElementById('bShowStep3').value = 'true';
		}
		
		function setProps()
		{
			var objForm = document.forms["EditCirculation"];
			
			objForm.strCirculationName.required = 1;
			objForm.strCirculationName.err = "<?php echo $CIRCULATION_NEW_ERROR_NAME;?>";
		}
		
		function setPropsRestart(nId, strName)
		{
			var objForm = document.forms["EditCirculation"];
			
			objForm.strCirculationName.required = 1;
			objForm.strCirculationName.err = "<?php echo $CIRCULATION_NEW_ERROR_NAME;?>";
			
			document.EditCirculation["listid"].value = nId;
			
			objMailingList = document.getElementById("MailingListName");
			objMailingList.innerHTML = strName;
			
			document.getElementById('bShowStep2').value = 'true';
			document.getElementById('bShowStep3').value = 'true';
		}
		
		function validate(objForm)
		{
			var nMailinglistID 		= document.getElementById('listid').value;			
			var strCirculationName	= document.getElementById('strCirculationName').value;
			var Error = '';
			
			if (nMailinglistID != '')
			{
				if (strCirculationName != '')
				{
					return true;
				}
				else
				{
					alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_NAME);?>');
					return false;
				}
			}
			else
			{
				alert('<?php echo str_replace("'", "\'", $CIRCULATION_NEW_ERROR_MAILINGLIST);?>');
				return false;
			}
		}
	
		//--- javascript for step 2 (the mailinglist) 
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
			objInput.setAttribute("id", strNewId+"_MAILLIST");
			objInput.setAttribute("name", strNewId+"_MAILLIST");
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
										hidden_field.setAttribute("id", strNewId + "_MAILLIST");
										hidden_field.setAttribute("name", strNewId + "_MAILLIST");
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
			if (document.getElementById('step2').style.display == 'block')
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
		}

		if(!document.all){
			Node.prototype.swapNode = function (node) 
			{
	  			var nextSibling = this.nextSibling;
	  		  	var parentNode = this.parentNode;
				node.parentNode.replaceChild(this, node);
				parentNode.insertBefore(node, nextSibling);  
	
				return this;
			}
		}
		
		function browsePlaceholders()
		{
			url = "selectplaceholder_addtext.php?language=<?php echo $_REQUEST['language'] ?>";
			open(url,"BrowsePlaceholder","width=300,height=190,status=no,menubar=no,resizable=no,scrollbars=no");		
		}
		
		function insertPlaceholder(Value)
		{
			var strCurrentContent = document.getElementById('strAdditionalText').value;
			var strNewContent = strCurrentContent + Value;
			document.getElementById('strAdditionalText').value = strNewContent;
			
		}
		
		function showMailinglist()
		{
			document.getElementById('layer').style.display = 'none';
		}
		
		function showValues()
		{
			document.getElementById('layer3').style.display = 'none';
		}
		
		function setAdditionalText(jsAdditionalTextId)
		{
			if (jsAdditionalTextId > 0)
			{
				new Ajax.Request
				(
					'ajax_getadditionaltext.php',
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
<?php
	$curListId = "";
	$curListName = "&nbsp;";
	if($_REQUEST['bRestart'])
	{
		$nCirculationFormID = $_REQUEST['circid'];
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
   	
	   	if ($nConnection)
	   	{
	   		//--- get maximum count of users
	   		if (mysql_select_db($DATABASE_DB, $nConnection))
	   		{
				$query = "SELECT * FROM cf_circulationform WHERE nID = '$nCirculationFormID'";
				$nResult = mysql_query($query, $nConnection);
	
		        if ($nResult)
		        {
		            if (mysql_num_rows($nResult) > 0)
		            {
		                $arrRow = mysql_fetch_array($nResult);
		                
		                if ($arrRow)
		                { 
		                	$arrCirculationData = $arrRow;
		                }
		            }
		        }
		        $nMailingListID = $arrCirculationData["nMailingListId"];
		        		        
		        $query = "SELECT * FROM cf_mailinglist WHERE nID = '$nMailingListID'";
				$nResult = mysql_query($query, $nConnection);
	
		        if ($nResult)
		        {
		            if (mysql_num_rows($nResult) > 0)
		            {
		                $arrRow = mysql_fetch_array($nResult);
		                
		                if ($arrRow)
		                { 
		                	$strMailingListName = $arrRow["strName"];
		                }
		            }
		        }
		        
		        $query = "SELECT MAX(nRevisionNumber) FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID'";
		        $nResult = mysql_query($query, $nConnection);
	
		        if ($nResult)
		        {
		            if (mysql_num_rows($nResult) > 0)
		            {
		                $arrRow = mysql_fetch_array($nResult);
		                
		                if ($arrRow)
		                { 
		                	$nMaxRevision = $arrRow[0];
		                }
		            }
		        }
		        
		        $query = "SELECT * FROM cf_circulationhistory WHERE nRevisionNumber=".$nMaxRevision." AND nCirculationFormId = '$nCirculationFormID'";
		        $nResult = mysql_query($query, $nConnection);
	
		        if ($nResult)
		        {
		            if (mysql_num_rows($nResult) > 0)
		            {
		                $arrRow = mysql_fetch_array($nResult);
		                
		                if ($arrRow)
		                { 
		                	$arrHistoryData = $arrRow;
		                }
		            }
		        }
			}
		}
		echo "<body onload=\"setPropsRestart('".$nMailingListID."', '".$strMailingListName."')\">";
	}
	else
	{
		echo '<body onload="setProps()">';
		
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
   	
	   	if ($nConnection)
	   	{
	   		//--- get maximum count of users
	   		if (mysql_select_db($DATABASE_DB, $nConnection))
	   		{
	   			$query = "SELECT * FROM cf_mailinglist WHERE bIsDefault = 1 and bDeleted = 0";
				$nResult = mysql_query($query, $nConnection);
				
				if ($nResult)
		        {
		            if (mysql_num_rows($nResult) > 0)
		            {
		                $arrRow = mysql_fetch_array($nResult);
		                
		                if ($arrRow)
		                { 
		                	$curListId = $arrRow['nID'];
		                	$curListName = $arrRow['strName'];
		                }
		            }
					else
					{
						$curListName = "";
					}
				}
	   		}
	   	}
	}
	
	$AdditionalText 	= new Database_AdditionalText();
	$additionalTexts	= $AdditionalText->getByParams();
	$additionalTextDefaultValue = $AdditionalText->getDefaultValue();
	
	$user_id = $_SESSION['SESSION_CUTEFLOW_USERID'];
?>
<body onload="setProps()"><br>
<table cellspacing="0" cellpadding="0" width="700">
<tr>
	<td align="left" style="padding-right: 20px;">
	<span style="float: left;font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php
			echo $MENU_CIRCULATION;
		?>
	</span>
	<div style="display: none; margin-top: 5px;" id="loading">
		<table cellspacing="0" cellpadding="0">
		<tr>
			<td align="left" valign="middle">
				<img src="../images/loading_moz.gif" hspace="3">
			</td>
			<td align="left" valign="top">
				<?php echo $LOADING_DATA;?>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
</table>
<br>
	<?php		
		// - - - - - - - - - - - START STEP 1 - - - - - - - - - - - 
	?>
	<form ENCTYPE="multipart/form-data" METHOD="POST" action="editcirculation_write.php" id="EditCirculation" name="EditCirculation" onsubmit="return validate(this);">
	
	<div id="step1" style="display: block;">
		<table width="90%" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
			<tr>
				<td class="table_header" colspan="2" style="border-bottom: 3px solid #ffa000;">
					<?php echo $INSTALL_STEP.' 1/3: '.$CIRCULATION_EDIT_FORM_HEADER;?>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
            <tr valign="top">
				<td class="mandatory" width="170"><?php echo $CIRCULATION_EDIT_NAME;?></td>
				<td><input id="strCirculationName" Name="strCirculationName" type="text" class="FormInput" style="width:202px;" <?php if($_REQUEST['bRestart']) { echo 'value="'.$arrCirculationData['strName'].'" readonly'; } ?>></td>
			</tr>
         	<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr valign="top">
				<td class="mandatory"><?php echo $CIRCULATION_EDIT_MAILINGLIST;?></td>
				<td>
					<select style="width: 200px" onchange="setMailingList(this.value)">
						<option><?php echo $FILTER_MAILINGLIST;?></option>
						<option disabled="disabled">---</option>
						<?php 
						$strQuery = "SELECT * FROM cf_mailinglist INNER JOIN cf_allowed_sender ON cf_allowed_sender.mailinglist_id=cf_mailinglist.nID WHERE cf_mailinglist.bIsEdited <> '1' AND cf_mailinglist.bDeleted=0 AND cf_allowed_sender.user_id = {$user_id} ORDER BY strName ASC";
                    	$nResult = mysql_query($strQuery, $nConnection);
                            
                        if ($nResult) {
                            if (mysql_num_rows($nResult) > 0) {
                            	while (	$arrRow = mysql_fetch_array($nResult)) {
                         			echo "<option value=\"".$arrRow["nID"].'"';
                         			
                         			if ($curListId == $arrRow['nID']) {
                         				echo ' selected="selected" ';
                         			}
                         			
                         			echo ">".$arrRow["strName"]."</option>";
                            	}		
                            }
                        }
                        ?>
					</select>
		
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr valign="top">
				<td class="mandatory"><?php echo $CIRCULATION_EDIT_ATTACHMENTS;?></td>
				<td>
					<INPUT class="FormInput" NAME="attachment1" TYPE="file" size="40" maxlength="255"><br>
					<INPUT class="FormInput" NAME="attachment2" TYPE="file" size="40" maxlength="255"><br>
					<INPUT class="FormInput" NAME="attachment3" TYPE="file" size="40" maxlength="255"><br>
					<INPUT class="FormInput" NAME="attachment4" TYPE="file" size="40" maxlength="255"><br>
				</td>
			</tr>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr valign="top">
				<td class="mandatory"><?php echo $CIRCULATION_EDIT_ADDITIONAL_TEXT;?></td>
				<td valign="top" align="left">
					<select class="FormInput">
						<option onClick="setAdditionalText(0);"><?php echo $FILTER_TEMPLATE ?></option>
						<?php
						$additionalTexts = $AdditionalText->getByParams();
						
						if (false !== $additionalTexts) {
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
						}
						?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<td></td>
				<td valign="top" align="left">
					<textarea cols="50" rows="5" name="strAdditionalText" id="strAdditionalText" class="FormInput"><?php echo $additionalTextDefaultValue?></textarea><img title="<?php echo escapeDouble($INSERT_PLACEHOLDER);?>" src="../images/grid_insert_row_style_2_16.gif" style="margin-left: 4px; height: 16px; border: 1px solid #666; background: #eeeeee; cursor: pointer;" onClick="browsePlaceholders();">
				</td>
			</tr>
			<?php if($_REQUEST['bRestart']): ?>
			<tr valign="top">
				<td class="mandatory" width="170"><?php echo $CIRCULATION_RESTART_SELECT_START ?>:</td>
				<td>
					<?php
					$objCirculation 	= new CCirculation();
					$arrMailinglist 	= $objCirculation->getMailinglist($nMailingListID);
					$nFormTemplateID 	= $arrMailinglist['nTemplateId'];
					$arrUsers			= $objCirculation->getUsers();
					$arrSlots			= $objCirculation->getFormslots($nFormTemplateID);
					
					$nIndex2 = 0;
					
					// search the latest station
					$query 		= "SELECT MAX(nRevisionNumber) as nMaxRevisionNumber FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID' LIMIT 1;";
					$result		= mysql_query($query, $nConnection);
      				$arrResult	= mysql_fetch_array($result, MYSQL_ASSOC);
					$nMaxRevisionNumber = $arrResult['nMaxRevisionNumber'];
					
					$query 		= "SELECT nID FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID' AND nRevisionNumber = '$nMaxRevisionNumber' LIMIT 1;";
      				$result		= mysql_query($query, $nConnection);
      				$arrResult	= mysql_fetch_array($result, MYSQL_ASSOC);
					$nCircHistoryId = $arrResult['nID'];
      					
					$query 		= "SELECT * FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormID' AND nCirculationHistoryId = '$nCircHistoryId' AND (nDecissionState = '16' OR nDecissionState = '2') LIMIT 1;";
      				$result		= mysql_query($query, $nConnection);
    				$arrCircProcess	= mysql_fetch_array($result, MYSQL_ASSOC);
			
    				$query 		= "SELECT nPosition FROM cf_slottouser WHERE nMailingListId = '$nMailingListID' AND nSlotId = '".$arrCircProcess['nSlotId']."' AND nUserId = '".$arrCircProcess['nUserId']."' ORDER BY nPosition ASC";
    				$result		= mysql_query($query, $nConnection);
    				$arrResult	= mysql_fetch_array($result, MYSQL_ASSOC);
    				$nPosition	= $arrResult['nPosition'];
    					
    				$strFirstKey = '99---'.$arrCircProcess['nSlotId'].'---'.$arrCircProcess['nUserId'].'---'.$nPosition;
					?>
					<select id="MailingList" name="MailingList" class="FormInput" style="width: 250px;">
       					<option value="<?php echo $strFirstKey ?>" selected>(<?php echo $CIRCULATION_RESTART_LAST_STATION ?>)</option>
						<?php
       					
       					
       					$nMax = sizeof($arrSlots);
       					for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
       					{
       						$arrSlot = $arrSlots[$nIndex];
       						$nSlotID = $arrSlot['nID'];
       						$strSlot = $arrSlot['strName'];
       						
       						$strQuery 	= "SELECT * FROM cf_slottouser WHERE nMailingListId = '$nMailingListID' AND nSlotId = '$nSlotID' ORDER BY nPosition ASC";
				    		$nResult 	= mysql_query($strQuery, $nConnection);
				    		?>
				    		<option value="0">- - - <?php echo $strSlot ?> - - -</option>
				    		<?php
       						if ($nResult)
				    		{
				    			if (mysql_num_rows($nResult) > 0)
				    			{
				    				while (	$arrRow = mysql_fetch_array($nResult))
				    				{
										$arrUser = $arrUsers[$nUserID];
										
										$nUserID 	= $arrRow['nUserId'];
										if ($nUserID != -2)
										{
											$strUser	= $objCirculation->getUsername($nUserID);
										}
										else
										{
											$strUser	= $SELF_DELEGATE_USER;
										}
										
										$nPosition 	= $arrRow['nPosition'];
										
										$nCurKey = $nIndex2.'---'.$nSlotID.'---'.$nUserID.'---'.$nPosition;
										?>
										<option value="<?php echo $nCurKey ?>"><?php echo ($nIndex2+1).') '.$strUser ?></option>
										<?php
										$nIndex2++;
				    				}
				    			}
				    		}
       					}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" style="font-weight: bold;">
					<?php echo $OTHER_OPTIONS ?>:
				</td>
				<td>
					<input type="checkbox" name="bUseLatestValues" id="bUseLatestValues"><?php echo $CIRCULATION_RESTART_USE_LATEST_VALUES ?>
				</td>
			</tr>	
			<?php endif ?>		
			<tr>
				<td align="left" valign="top" style="font-weight: bold;">
					<?php if(!$_REQUEST['bRestart']) echo $OTHER_OPTIONS.':' ?>
				</td>
				<td>
					<input type="checkbox" name="SuccessMail" id="SuccessMail" checked><?php echo $CIRCULATION_EDIT_NOTIFY;?>
					<div style="padding-left: 20px">
						<input type="checkbox" name="NotificationType[]" value="1" checked /><?php echo $CIRCULATION_EDIT_NOTIFY_TYPE_END;?><br/>
						<input type="checkbox" name="NotificationType[]" value="8" /><?php echo $CIRCULATION_EDIT_NOTIFY_TYPE_SLOTEND;?><br/>
					</div>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="SuccessArchive" id="SuccessArchive" checked onClick="document.getElementById('SuccessDelete').checked = false;"><?php echo $CIRCULATION_EDIT_SUCCESS_ARCHIVE ?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="SuccessDelete" id="SuccessDelete" onClick="document.getElementById('SuccessArchive').checked = false;"><?php echo $CIRCULATION_EDIT_SUCCESS_DELETE ?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" name="Anonymize" id="Anonymize" value="1"><?php echo $CIRCULATION_EDIT_ANONYMIZE ?>
				</td>
			</tr>
			
			<?php
			// read the hook CF_ENDACTION
			$circulation 	= new CCirculation();
			$endActions		= $circulation->getExtensionsByHookId('CF_ENDACTION');
			
			if ($endActions)
			{
				foreach ($endActions as $endAction)
				{
					$params = $circulation->getEndActionParams($endAction);
					?>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input type="checkbox" name="<?php echo $params['checkboxName'] ?>" id="<?php echo $params['checkboxName'] ?>"><?php echo $params['checkboxTitle'] ?>
						</td>
					</tr>
					<?php
				}
			}
			?>
			<tr>
				<td colspan="2" height="10px"></td>
			</tr>
			<tr>
				<td colspan="4" style="border-top: 1px solid #ffa000; padding: 6px 0px 4px 0px;" align="right">
					<table width="100%">
						<tr>
							<td align="left" width="25%">
								&nbsp;
							</td>
							<td align="right" width="25%">
								<input type="button" class="button" value="<?php echo $BTN_CANCEL ?>" onClick="location='showcirculation.php?language=<?php echo $language; ?>&sortDirection=<?php echo $sortDirection; ?>&sortby=<?php echo $sortby; ?>&archivemode=0&start=1&bFirstStart=true'">
							</td>
							<td align="left" width="25%">
								<input type="submit" name="step1" class="button" value="<?php echo $BTN_COMPLETE ?>">
							</td>
							<td align="right" width="25%">
								<?php if(!$_REQUEST['bRestart']): ?>
								<input type="button" class="button" value="<?php echo $BTN_NEXT ?> >>" onClick="showStep2();">
								<?php endif ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" value="<?php echo $curListId;?>" id="listid" name="listid">	
	</div>
	
	<?php
		// - - - - - - - - - - - START STEP 2 - - - - - - - - - - - 
	?>
	<div id="step2" style="display: none; width: 90%;" align="left">
	</div>
	
	
	<?php
		// - - - - - - - - - - - START STEP 3 - - - - - - - - - - - 
	?>
	<div id="step3" style="display: none; width: 90%;" align="left">
	</div>
	
	<div id="layer" style="display: none; background: url(../images/layer.gif); position: absolute; width: 100px; height: 100px; top: 100px; left: 100px;">
	</div>
	<div id="layer3" style="display: none; background: url(../images/layer.gif); position: absolute; width: 100px; height: 100px; top: 100px; left: 100px;">
	</div>
	
	<input type="hidden" name="language" value="<?php echo $language; ?>">
	<input type="hidden" name="bShowStep2" id="bShowStep2" value="true">
	<input type="hidden" name="bShowStep3" id="bShowStep3" value="true">
	
	<?php
		if($_REQUEST['bRestart'])
		{
			?>
			<input type="hidden" name="cfid" id="cfid" value="<?php echo $_REQUEST['circid']; ?>">
			<input type="hidden" name="bRestart" id="bRestart" value="1">
			<?php
		}
	?>
	
	<input type="hidden" name="bShowStep2_layer" id="bShowStep2_layer" value="true">
	<input type="hidden" name="bShowStep3_layer" id="bShowStep3_layer" value="true">
	
	</form>
</body>
</html>