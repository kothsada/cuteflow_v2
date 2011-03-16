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
    
	if (!$ALLOW_UNENCRYPTED_REQUEST)
	{
		// clear $_REQUEST to ensure that only the encryptet "key" is used
		foreach ($_GET as $key => $value)
		{
			if($key != 'key')
			{
				$_REQUEST[$key]		= '';
			}
		}
	}
    
    $objMyCirculation = new CCirculation();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
   	<link rel="stylesheet" href="format.css" type="text/css">
	<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="tooltip.js"></SCRIPT>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script type="text/javascript">
    	maketip('skip_station','<?php echo escapeSingle($CIRCDETAIL_TIP_SKIP);?>');
    	maketip('retry_station','<?php echo escapeSingle($CIRCDETAIL_TIP_RETRY);?>');
    	maketip('change_station','<?php echo escapeSingle($CIRCDETAIL_TIP_CHANGE_STATION);?>');
    	maketip('change_substitute','<?php echo escapeSingle($CIRCDETAIL_TIP_CHANGE_SUBSTITUTE);?>');
    	
    	var nCURCirculationFormID;
    	var strCURLanguage;
    	var nCURCirculationProcessID;
    	var nCURMailinglistID;
    	
    	function Go(nId) 
		{
			document.forms["RevisionForm"].submit();
		}
		
		var WindowObjectReference;
		
		function BrowseUserlist( nCirculationFormID, strLanguage, nCirculationProcessID, nMailinglistID)
		{
			nCURCirculationFormID		= nCirculationFormID;
	    	strCURLanguage				= strLanguage;
	    	nCURCirculationProcessID	= nCirculationProcessID;
	    	nCURMailinglistID			= nMailinglistID;
			
			var strParams	= 'nCirculationFormID=' + nCirculationFormID + '&strLanguage=' + strLanguage + '&nCirculationProcessID=' + nCirculationProcessID + '&nMailinglistID=' + nMailinglistID;
			inpdata	= strParams;
			encodeblowfish();
			
			WindowObjectReference = window.open(
				"selectskipuser.php?key=" + outdata,
				'BrowseMailinglist',
				'width=310,height=250,resizable=no,scrollbars=no,status=1'
				);
		}
		
		function BrowseUserlist_Subs( nCirculationFormID, strLanguage, nCirculationProcessID, nMailinglistID)
		{
			nCURCirculationFormID		= nCirculationFormID;
	    	strCURLanguage				= strLanguage;
	    	nCURCirculationProcessID	= nCirculationProcessID;
	    	nCURMailinglistID			= nMailinglistID;
			
			var strParams	= 'nCirculationFormID=' + nCirculationFormID + '&strLanguage=' + strLanguage + '&nCirculationProcessID=' + nCirculationProcessID + '&nMailinglistID=' + nMailinglistID;
			inpdata	= strParams;
			encodeblowfish();
			
			WindowObjectReference = window.open(
				'selectskipuser_subs.php?key=' + outdata,
				'BrowseMailinglist',
				'width=310,height=250,resizable=no,scrollbars=no,status=1'
				);		
		}
		
		function changeCurrentStation_Subs(nUserID)
		{
			strParams = '?nUserID=' + nUserID + '&nCURCirculationFormID=' + nCURCirculationFormID + '&nCURCirculationProcessID=' + nCURCirculationProcessID + '&nCURMailinglistID=' + nCURMailinglistID + '&language=<?php echo $_REQUEST['language']; ?>';
			location='circulation_detail_changestation_subs.php' + strParams;
		}
		
		function changeCurrentStation(nMyIndex, nSlotID, nUserID, nPosition)
		{
			strParams = '?nSlotID=' + nSlotID + '&nMyIndex=' + nMyIndex + '&nUserID=' + nUserID + '&nPosition=' + nPosition + '&nCURCirculationFormID=' + nCURCirculationFormID + '&nCURCirculationProcessID=' + nCURCirculationProcessID + '&nCURMailinglistID=' + nCURMailinglistID + '&language=<?php echo $_REQUEST['language']; ?>';
			location='circulation_detail_changestation.php' + strParams;
		}
	</script>
</head>
<?php
    //--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
            //--- get the single circulation form
			$query = "select * from cf_circulationform WHERE nID=".$_REQUEST["circid"];
			$nResult = mysql_query($query, $nConnection);

			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationForm = mysql_fetch_array($nResult);
				}
			}
			$nSenderUserId = $arrCirculationForm['nSenderId'];
			//-----------------------------------------------
			//--- get history (all revisions)
			//-----------------------------------------------
			$arrHistoryData = array();
			$nMaxRevisionId = 0;
			$strQuery = "SELECT * FROM cf_circulationhistory WHERE nCirculationFormId=".$_REQUEST["circid"]." ORDER BY nRevisionNumber DESC";
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					if ($nMaxRevisionId == 0)
    					{
    						$nMaxRevisionId = $arrRow["nID"];	
    					}
    					$arrHistoryData[$arrRow["nID"]] = $arrRow;
    				}
    			}
    		}
    		
    		if ($_REQUEST['nRevisionId'] == '')
    		{
    			$_REQUEST['nRevisionId'] = $nMaxRevisionId;
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
			//--- get the mailing list
			//-----------------------------------------------
			$query = "select * from cf_mailinglist WHERE nID=".$arrCirculationForm["nMailingListId"];
			$nResult = mysql_query($query, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrMailingList = mysql_fetch_array($nResult);
				}
			}
			$nMailingListID = $arrMailingList['nID'];
			
            //-----------------------------------------------
            //--- get the template
            //-----------------------------------------------	            
            $strQuery = "SELECT * FROM cf_formtemplate WHERE nID=".$arrMailingList["nTemplateId"];
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				$arrTemplate = mysql_fetch_array($nResult);
   					$strTemplateName = $arrTemplate["strName"];
    			}
    		}
            
            //-----------------------------------------------
            //--- get the form slots
            //-----------------------------------------------	            
            $arrSlots = array();
            $strQuery = "SELECT * FROM cf_formslot WHERE nTemplateID=".$arrMailingList["nTemplateId"]."  ORDER BY nSlotNumber ASC";
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
			
			//-----------------------------------------------
            //--- get the field values
            //-----------------------------------------------	
			            
            $arrValues = array();
            $strQuery = "SELECT * FROM cf_fieldvalue WHERE nFormId=".$_REQUEST["circid"]." AND nCirculationHistoryId=".$_REQUEST["nRevisionId"];
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					$arrValues[$arrRow["nInputFieldId"]."_".$arrRow["nSlotId"]] = $arrRow;
    				}
    			}
    		}
			
			//-----------------------------------------------
            //--- get the form process detail
            //-----------------------------------------------	            
            $arrProcessInformation = array();
			$arrProcessInformationSubstitute = array();
			
            $strQuery = "SELECT * FROM cf_circulationprocess WHERE nCirculationFormId=".$_REQUEST["circid"]." AND nCirculationHistoryId=".$_REQUEST["nRevisionId"]." ORDER BY dateInProcessSince";
    		$nResult = mysql_query($strQuery, $nConnection) or die ($strQuery."<br>".mysql_error());
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
    				$nPosInSlot = -1;
    				$nLastSlotId = -1;
    				while (	$arrRow = mysql_fetch_array($nResult))
    				{
    					if ($arrRow["nIsSubstitiuteOf"] != 0)
						{
							if ($arrRow["nSlotId"] != $nLastSlotId)
	    					{
	    						$nLastSlotId = $arrRow["nSlotId"];	
	    						$nPosInSlot = -1;
	    					}
	    					//$nPosInSlot++;
							$arrProcessInformationSubstitute[$arrRow["nIsSubstitiuteOf"]] = $arrRow;
						}
						else
						{
    						if ($arrRow["nSlotId"] != $nLastSlotId)
	    					{
	    						$nLastSlotId = $arrRow["nSlotId"];	
	    						$nPosInSlot = -1;
	    					}
	    					$nPosInSlot++;
    						$arrProcessInformation[$arrRow["nUserId"]."_".$arrRow["nSlotId"]."_".$nPosInSlot] = $arrRow;
    					}
    				}    				
    			}
    		}
		}
    }

	function printUser($arrRow, $bIsSubstitute, $nUserId, $bLastUser)
	{
		global $arrUsers, $_REQUEST;
		global $CIRCDETAIL_RECEIVE, $CIRCDETAIL_STATE_WAITING, $CIRCDETAIL_STATE_OK, $CIRCDETAIL_STATE_STOP;
		global $CIRCDETAIL_STATE_SKIPPED, $CIRCDETAIL_STATE_SUBSTITUTE, $CIRCDETAIL_PROCESS_DURATION;
		global $CIRCDETAIL_DAYS, $CIRCDETAIL_STATE_DENIED, $nMailingListID, $SELF_DELEGATE_USER;
		
		echo "<tr style=\"height:22px;\">\n";
		
		if ($bIsSubstitute == false)
		{
        	if ($nUserId != -2)
        	{
				echo "<td width=\"20px\"><img src=\"../images/singleuser.gif\" height=\"16\" width=\"16\"></td>\n";
	       		echo "<td width=\"140px\">".$arrUsers[$nUserId]["strUserId"]."</td>\n";
        	}
        	else
        	{
        		echo "<td width=\"20px\"><img src=\"../images/user_green.gif\" height=\"16\" width=\"16\"></td>\n";
	       		echo "<td width=\"140px\">".$SELF_DELEGATE_USER."</td>\n";
        	}
		}
		else
		{
			?>
			<td width="20" align="right"><img src="../images/right.png" height="16" width="16"></td>
       		<td width="140"><img src="../images/singleuser2.gif" height="16" width="16" align="absmiddle" style="margin-right: 6px;"><?php echo $arrUsers[$arrRow['nUserId']]['strUserId'] ?></td>
       		<?php
		}
	
		//--- The receiving date
		$dateReceive = convertDateFromDB($arrRow["dateInProcessSince"]);
		if (0 == $arrRow["dateInProcessSince"])
		{
			$dateReceive = "-";
			echo "<td width=\"150px\">&nbsp;</td>\n";
		}
		else
		{
			echo "<td width=\"150px\" nowrap>".$dateReceive."</td>\n";
		}

		//--- The process state
		if (!$arrRow)
		{
			echo "<td width=\"16px\">&nbsp;</td>\n";
	        echo "<td width=\"110px\">&nbsp;</td>\n";
		}
		else
		{
			switch ($arrRow["nDecissionState"])
			{
				case 0: $strImage = "state_wait.gif";
						$strText = $CIRCDETAIL_STATE_WAITING;
						break;
				case 1: $strImage = "state_ok.png";
						$strText = $CIRCDETAIL_STATE_OK;
						break;
				case 2: $strImage = "state_stop.png";
						$strText = "<strong style=\"color:Red;\">$CIRCDETAIL_STATE_DENIED</strong>";
						break;
				case 4: $strImage = "state_skip.png";
						$strText = $CIRCDETAIL_STATE_SKIPPED;
						break;
				case 8: $strImage = "state_skip.png";
						$strText = $CIRCDETAIL_STATE_SUBSTITUTE;													
						break;
				case 16: $strImage = "stop.gif";
						$strText = "<strong style=\"color:Red;\">$CIRCDETAIL_STATE_STOP</strong>";
						break;
						
			}
			echo "<td width=\"16px\">";
			echo "<img src=\"../images/$strImage\" height=\"16\" width=\"16\">";
			echo "</td>\n";
	       	echo "<td width=\"200px\" nowrap>$strText</td>\n";
		}
		
		//--- the working duration
		if ($dateReceive != "-")
		{
			if ($arrRow["nDecissionState"] == 0)
			{
				$diff = abs(time() - $arrRow["dateInProcessSince"]);
				$nDays = floor($diff / (60 * 60 * 24) );
			}
			else
			{
				if ($arrRow["nDecissionState"] != 16)
				{					
					$dateDecission = $arrRow["dateDecission"];
					$diff = abs($dateDecission - $arrRow["dateInProcessSince"]);
					$nDays = floor($diff / (60 * 60 * 24) );
				}
				else
				{
					$nDays = "-";
				}
			}
			
            echo "<td nowrap><strong style=\"color:".getDelayColor($nDays).";\">$nDays</strong> $CIRCDETAIL_DAYS</td>\n";
		}
		else
		{
            echo "<td>&nbsp;</td>\n";
		}
		
		//--- the actions
		global $objURL;
		echo "<td nowrap>";
		if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
		{
			if ($dateReceive != "-")
			{
				$nState = $arrRow["nDecissionState"];
				
				if ( ($nState == 0) || ($nState == 2) )
				{
					$strParams				= 'circid='.$_REQUEST['circid'].'&language='.$_REQUEST['language'].'&cpid='.$arrRow['nID'].'&start='.$_REQUEST['start'].'&sortby='.$_REQUEST['sortby'].'&archivemode='.$_REQUEST['archivemode'];
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					?>					
					<a onMouseOver="tip('retry_station')" onMouseOut="untip()" href="retryuser.php?key=<?php echo $strEncyrptedParams ?>">
					<img src="../images/retry.png" border="0" height="16" width="16" style="margin-right: 4px;">
					</a>
					
					<a onMouseOver="tip('skip_station')" onMouseOut="untip()" href="skipuser.php?key=<?php echo $strEncyrptedParams ?>">
					<img src="../images/stepover_co.png" border="0" height="16" width="16">
					</a>
					
					<?php if ($bIsSubstitute == false): ?>					
						<a onMouseOver="tip('change_substitute')" onMouseOut="untip()" href="javascript:BrowseUserlist_Subs('<?php echo $_REQUEST['circid'] ?>', '<?php echo $_REQUEST['language'] ?>', '<?php echo $arrRow['nID'] ?>', '<?php echo $nMailingListID ?>')">
						<img src="../images/cs_subs.jpg" border="0" height="16" width="16" style="margin-left: 4px;">
						</a>
					<?php endif; ?>
					
					<a onMouseOver="tip('change_station')" onMouseOut="untip()" href="javascript:BrowseUserlist('<?php echo $_REQUEST['circid'] ?>', '<?php echo $_REQUEST['language'] ?>', '<?php echo $arrRow['nID'] ?>', '<?php echo $nMailingListID ?>')">
					<img src="../images/cs.jpg" border="0" height="16" width="16" style="margin-left: 4px;">
					</a>
					<?php
				}
				/*else if (($bLastUser == true) && ($bIsSubstitute == false) && ($nState != 16) && ($nState != 8))
				{
					$strParams				= 'circid='.$_REQUEST['circid'].'&language='.$_REQUEST['language'].'&cpid='.$arrRow['nID'].'&start='.$_REQUEST['start'].'&sortby='.$_REQUEST['sortby'].'&archivemode='.$_REQUEST['archivemode'];
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					?>
					<a onMouseOver="tip('retry_station')" onMouseOut="untip()" href="retryuser.php?key=<?php echo $strEncyrptedParams ?>">
					<img src="../images/retry.png" border="0" height="16" width="16">
					</a>
					<?php
				}*/
			}
		}
		echo "&nbsp;</td>";
        echo "</tr>\n";
	}

?>
<body bgcolor="White">
<center><br>
<?php
	//var strParams	= 'nCirculationFormID=' + nCirculationFormID + '&strLanguage=' + strLanguage + '&nCirculationProcessID=' + nCirculationProcessID + '&nMailinglistID=' + nMailinglistID;
	$strParams				= 'language='.$_REQUEST['language'].'&circid='.$arrCirculationForm['nID'];
	$strEncyrptedParams		= $objURL->encryptURL($strParams);
	$strEncryptedLinkURL	= 'circulation_detail.php?key='.$strEncyrptedParams;
?>
<form method="POST" id="RevisionForm" action="<?php echo $strEncryptedLinkURL ?>">
	<table border="0" width="95%" cellpadding="0" cellspacing="0" class="BorderSilver">
	    <tr>
	        <td colspan="3">
	            <table bgcolor="Silver" width="100%">
	                <tr>
	                    <td width="20px" align="left"><img src="../images/circulate.png" height="16" width="16"></td>
	                    <td style="font-weight:bold;" align="left"><?php echo $arrCirculationForm["strName"];?></td>
	                </tr>
	            </table>
	        </td>
	    </tr>
	    <tr style="height:22px;">
	        <td width="20px" align="left"><img src="../images/template_type.gif" height="16" width="16"></td>
	        <td width="150px" align="left"><?php echo $CIRCDETAIL_TEMPLATE_TYPE;?></td>
	        <td align="left"><?php echo $strTemplateName;?></td>
	    </tr>
	    <tr style="height:22px;">
	        <td width="20px" align="left"><img src="../images/singleuser2.gif" height="16" width="16"></td>
	        <td width="150px" align="left"><?php echo $CIRCDETAIL_SENDER;?></td>
	        <td align="left">
	        <?php
	            echo $arrUsers[$arrCirculationForm["nSenderId"]]["strLastName"].", ".$arrUsers[$arrCirculationForm["nSenderId"]]["strFirstName"]." (".$arrUsers[$arrCirculationForm["nSenderId"]]["strUserId"].")";
	        ?>
	        </td>
	    </tr>
	    <?php 
	    if ($view != "print")
		{
	    ?>
	    <tr style="height:22px;">
	        <td width="20px" align="left"><img src="../images/calendar.gif" height="16" width="16" ></td>
	        <td width="150px" align="left"><?php echo $CIRCDETAIL_SENDREV;?></td>
	        <td align="left">
	        	<select name="nRevisionId" id="nRevisionId" class="FormInput" onChange="Go(this.form.nRevisionId.options[this.form.nRevisionId.options.selectedIndex].value)">
				<?php 
	        		foreach ($arrHistoryData as $arrCurHistory)
	        		{
						$check = "";
						if($_REQUEST["nRevisionId"] == $arrCurHistory["nID"])
							$check = "selected";
						
						echo "<option value=\"".$arrCurHistory["nID"]."\" ".$check.">#".$arrCurHistory["nRevisionNumber"]." - ".convertDateFromDB($arrCurHistory["dateSending"])."</option>";
					}
				?>
				</select>
				
	        </td>
	    </tr>
	    <?php
		}
	    ?>
		 <tr style="height:22px; padding-top: 4px;">
		 	<td align="left" style="padding-top: 4px;" width="20px" valign="top"><img src="../images/description.gif" height="16" width="16" ></td>
	        <td align="left" style="padding-top: 4px;" width="150px" valign="top"><?php echo $CIRCDETAIL_DESCRIPTION;?></td>
	        <td align="left" style="padding-top: 4px;" valign="top"><?php echo str_replace("\n", "<br>", $arrHistoryData[$_REQUEST["nRevisionId"]]["strAdditionalText"]);?></td>
		 </tr>
	</table>
</form><br>

<?php
if ($view != 'print')
{
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
            //-----------------------------------------------
    		//--- get all attachments
            //-----------------------------------------------
            $strQuery = "SELECT * FROM cf_attachment WHERE  nCirculationHistoryId=".$arrHistoryData[$_REQUEST["nRevisionId"]]["nID"];
    		$nResult = mysql_query($strQuery, $nConnection);
    		if ($nResult)
    		{
    			if (mysql_num_rows($nResult) > 0)
    			{
					?>
					<table border="0" width="95%" cellpadding="0" cellspacing="0" class="BorderSilver">
					    <tr>
					        <td colspan="5" align="left">
					            <table bgcolor="Silver" width="100%">
					                <tr>
					                    <td width="20px" align="left"><img src="../images/attach.png" height="16" width="16"></td>
					                    <td style="font-weight:bold;" align="left"><?php echo $CIRCDETAIL_ATTACHMENT;?></td>
					                </tr>
					            </table>
					        </td>
					    </tr>
					    <?php					    
		                    $nRunningNumber = 1;
		                    echo "<tr>\n";
							while (	$arrRow = mysql_fetch_array($nResult))
		    				{
		                        echo "<td align=\"left\">\n";
								echo "<table>\n<tr>\n";
		    					echo "<td align=\"left\" style=\"height:22px;\" width=\"20px\"><img src=\"../images/document.png\" height=\"16\" width=\"16\"></td>\n";
		                        echo "<td align=\"left\" style=\"height:22px;\"><a target=\"_blank\" href=\"".$arrRow["strPath"]."\">".getFileNameFromPath($arrRow["strPath"])."</td>\n";
		                    	echo "</tr>\n</table\n";
								echo "</td>\n";
								
		                        if ($nRunningNumber % 2 == 0)
		                        {
		                            echo "</tr>\n<tr>";
		                        }
		                        else
		                        {
		                            echo "<td style=\"height:22px;\" width=\"10px\">&nbsp;</td>\n";
		                        }
		                        
		                        $nRunningNumber++;
		        			}
							echo "</tr>\n";
					    ?>
					</table><br>
					<?php
    			}
    		}
		}
	}
?>
<table border="0" width="95%" cellpadding="0" cellspacing="0" class="BorderSilver">
    <tr>
        <td colspan="2" align="left">
            <table bgcolor="Silver" width="100%">
                <tr>
                    <td width="20px" align="left"><img src="../images/history.gif" height="16" width="16"></td>
                    <td style="font-weight:bold;" align="left"><?php echo $CIRCDETAIL_HISTORY;?></td>
                </tr>
            </table>
        </td>
    </tr>
	
	<tr>
		<td colspan="2" align="left">
			<table width="100%">
				<tr style="background-color:#EEEEEE;">
					<td>&nbsp;</td>
					<td align="left"><?php echo $CIRCDETAIL_STATION;?></td>
					<td align="left"><?php echo $CIRCDETAIL_RECEIVE;?></td>
					<td colspan="2" align="left"><?php echo $CIRCDETAIL_STATE;?></td>
					<td align="left"><?php echo $CIRCDETAIL_PROCESS_DURATION;?></td>
					<td align="left"><?php echo $CIRCDETAIL_COMMANDS;?></td>
				</tr>
	<?php
        $nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
        if ($nConnection)
	    {
		    if (mysql_select_db($DATABASE_DB, $nConnection))
    		{
    			$nPICount = 0;
    			for ($nIndex = 0; $nIndex < sizeof($arrSlots); $nIndex++)
    			{
    				$nPosInSlot = 0;
    				$arrSlot = $arrSlots[$nIndex];

    				$strQuery = "SELECT * FROM cf_slottouser WHERE nMailingListId=".$arrCirculationForm["nMailingListId"]." AND nSlotId=".$arrSlot["nID"]." ORDER BY nPosition ASC";
                    $nResult = mysql_query($strQuery, $nConnection) or die ($strQuery."<br>".mysql_error());
                    if ($nResult)
	   		        {
                    	if (mysql_num_rows($nResult) > 0)
                        {
							?>
								<tr>
							    	<td colspan="8" style="border-bottom: 1px solid Silver; padding-top:8px;" align="left"><strong><?php echo $MAILLIST_EDIT_FORM_SLOT.": ".$arrSlot[1];?></strong></td>
								</tr>
			                <?php
                           	while ($arrRow = mysql_fetch_array($nResult))
                        	{
                        		if ($arrRow['nUserId'] != -2)
                        		{
                        			$arrCurPi = $arrProcessInformation[$arrRow['nUserId'].'_'.$arrSlot['nID'].'_'.$nPosInSlot];
                        		}
                        		else
                        		{
                        			$arrCurPi = $arrProcessInformation[$nSenderUserId.'_'.$arrSlot['nID'].'_'.$nPosInSlot];
                        			if (sizeof($arrCurPi) < 1)
                        			{
                        				$arrCurPi = $arrProcessInformation['-2'.'_'.$arrSlot['nID'].'_'.$nPosInSlot];
                        			}
                        		}
								
								$nPICount++;
								$bLastUser = ($nPICount == sizeof($arrProcessInformation)) ? true : false;
																
								printUser($arrCurPi, false, $arrRow["nUserId"], $bLastUser);
								$printed_users = $printed_users+1;
								$nCurPiId 		= $arrCurPi["nID"];
								$arrSubstitute 	= $arrProcessInformationSubstitute[$nCurPiId];
								
								if ($arrSubstitute)
								{
									$nUserId 				= $arrRow['nUserId'];
									$nCirculationFormId 	= $arrSubstitute['nCirculationFormId'];
									$nCirculationHistoryId 	= $arrSubstitute['nCirculationHistoryId'];
									//?
									$nSubstituteId 			= $arrSubstitute['nUserId'];
									//$arrSubstitutes = $objMyCirculation->getSubstitutes($nUserId);
									
									
									$strQuery 	= "SELECT * FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormId' AND nCirculationHistoryId = '$nCirculationHistoryId' ORDER BY dateInProcessSince ASC";
									$result		= mysql_query($strQuery, $nConnection);
									$arrCPResult = NULL;
									while ($arrRow2 = mysql_fetch_array($result))
                        			{
                        				$arrCPResult[] = $arrRow2;
                        			}
									
									$print_next = 0;
									$nMax2 = sizeof($arrCPResult);
									for ($nIndex2 = 0; $nIndex2 < $nMax2; $nIndex2++)
									{
										$arrCurCP = $arrCPResult[$nIndex2];
										if ($print_next && ($arrCurCP['nIsSubstitiuteOf'] != 0))
										{
											printUser($arrCurCP, true, $nSubstituteId, $bLastUser);
										}
										else
										{
											$print_next = 0;
										}
										if ($arrCurCP['nID'] == $nCurPiId) $print_next = 1;
									}
								}	
								$nPosInSlot++;
                         	}
    		           }
    		       }
                }
            }
        }
    ?>
			</table>		
		</td>
	</tr>
</table>
<br>
<?php
}	//--- end if ($view != "print")
?>
<table border="0" width="95%" cellpadding="0" cellspacing="0" class="BorderSilver">
    <tr>
        <td colspan="2" align="left">
            <table bgcolor="Silver" border="0" width="100%">
                <tr>
                    <td align="left" width="20px"><img src="../images/values.png" height="16" width="16"></td>
                    <td align="left" style="font-weight:bold;"><?php echo $CIRCDETAIL_VALUES;?></td>
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
					        <td style="border-top: 1px solid Silver;" align="left">
					            <table width="100%">
								<tr><td style="font-weight: bold;background: #666666; color: #fff; padding:1px; width: 50px;" colspan="16"><?php echo $arrSlot['strName']; ?></td></tr>
								<tr>
								<?php
									$strQuery = "SELECT * FROM cf_inputfield INNER JOIN cf_slottofield ON cf_inputfield.nID = cf_slottofield.nFieldId WHERE cf_slottofield.nSlotId = ".$arrSlot["nID"]."  ORDER BY cf_slottofield.nPosition ASC";
									$nResult = mysql_query($strQuery, $nConnection) or die ($strQuery."<br>".mysql_error());
                   					if ($nResult)
				                  	{
            			       			if (mysql_num_rows($nResult) > 0)
                   						{
											$nRunningCounter = 1;
			    		                  	while (	$arrRow = mysql_fetch_array($nResult))
            			       				{
												echo "<td class=\"mandatory\" width=\"20%\" valign=\"top\">".$arrRow["strName"].":</td>";
												echo "<td width=\"300px\" valign=\"top\">";
												if ($arrRow["nType"] == 1)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$arrValue = split('rrrrr',$arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);
														
														$output = replaceLinks($arrValue[0]); 
														if ($arrRow['strBgColor'] != "") {
															$output = '<span style="background-color: #'.$arrRow['strBgColor'].'">'.$output.'<span>';
														}																
														echo $output; 
													}
													else
													{
														$arrValue = split('rrrrr',$arrRow['strStandardValue']);
														
														$output = replaceLinks($arrValue[0]); 
														if ($arrRow['strBgColor'] != "") {
															$output = '<span style="background-color: #'.$arrRow['strBgColor'].'">'.$output.'<span>';
														}																
														echo $output;
													}
												}
												else if ($arrRow["nType"] == 2)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"] != "on")
													{
														$state = "inactive";
													}
													else
													{
														$state = "active";
													}
													
													echo "<img src=\"../images/$state.gif\" height=\"16\" width=\"16\">";
												}
												else if ($arrRow["nType"] == 3)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$arrValue = split('xx',$arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);								
														$nNumGroup 	= $arrValue[1];														
														$arrValue1 = split('rrrrr',$arrValue[2]);														
														$strMyValue	= $arrValue1[0];
													}
													else
													{
														$arrValue = split('xx',$arrRow['strStandardValue']);								
														$nNumGroup 	= $arrValue[1];														
														$arrValue1 = split('rrrrr',$arrValue[2]);														
														$strMyValue	= $arrValue1[0];
													}
													$output = replaceLinks($strMyValue); 
													if ($arrRow['strBgColor'] != "") {
														$output = '<span style="background-color: #'.$arrRow['strBgColor'].'">'.$output.'<span>';
													}																
													echo $output;
												}
												else if ($arrRow["nType"] == 4)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$arrValue = split('xx',$arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);
														$nDateGroup 	= $arrValue[1];
														$arrValue2 = split('rrrrr',$arrValue[2]);
														$strMyValue 	= $arrValue2[0];
													}
													else
													{
														$arrValue 		= split('xx',$arrRow['strStandardValue']);
														$nDateGroup 	= $arrValue[1];
														$arrValue2 		= split('rrrrr',$arrValue[2]);
														$strMyValue 	= $arrValue2[0];
													}
													$output = replaceLinks($strMyValue); 
													if ($arrRow['strBgColor'] != "") {
														$output = '<span style="background-color: #'.$arrRow['strBgColor'].'">'.$output.'<span>';
													}																
													echo $output;
												}
												else if ($arrRow["nType"] == 5)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														echo replaceLinks($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);
													}
													else
													{
														echo replaceLinks($arrRow['strStandardValue']);
													}
												}
												else if ($arrRow["nType"] == 6)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
														$arrMySplit = split('---', $strValue);
														
														if ($arrMySplit[1] > 1)
														{	// edited field values
															
															$strValue = '';
															$nMax = (sizeof($arrMySplit));
															for ($nIndex = 3; $nIndex < $nMax; $nIndex = $nIndex + 2)
															{
																$strValue .= $arrMySplit[$nIndex].'---';
															}
															$keyId = rand(1, 150);
														}
														else
														{	// we have to use the standard value
															$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
															$keyId = rand(1, 150);
														}
													}
													else
													{
														$strValue = $arrRow['strStandardValue'];
													}
													
													$nInputfieldID 	= $arrRow["nFieldId"];
													$bIsEnabled 	= 0;
													
													$strEcho = $objMyCirculation->getRadioGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter);
													
													echo $strEcho;
												}
												else if ($arrRow["nType"] == 7)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
													$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
														$arrMySplit = split('---', $strValue);
														
														if ($arrMySplit[1] > 1)
														{	// edited field values
															
															$strValue = '';
															$nMax = (sizeof($arrMySplit));
															for ($nIndex = 3; $nIndex < $nMax; $nIndex = $nIndex + 2)
															{
																$strValue .= $arrMySplit[$nIndex].'---';
															}
															$keyId = rand(1, 150);
														}
														else
														{	// we have to use the standard value
															$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
															$keyId = rand(1, 150);
														}
													}
													else
													{
														$strValue = $arrRow['strStandardValue'];
													}
													
													$nInputfieldID 	= $arrRow["nFieldId"];
													$bIsEnabled 	= 0;
													
													
													$strEcho = $objMyCirculation->getCheckboxGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter);
													
													echo $strEcho;										
												}
												elseif($arrRow["nType"] == 8)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
														$arrMySplit = split('---', $strValue);
														
														if ($arrMySplit[1] > 1)
														{	// edited field values
															
															$strValue = '';
															$nMax = (sizeof($arrMySplit));
															for ($nIndex = 3; $nIndex < $nMax; $nIndex = $nIndex + 2)
															{
																$strValue .= $arrMySplit[$nIndex].'---';
															}
															$keyId = rand(1, 150);
														}
														else
														{	// we have to use the standard value
															$strValue = $arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"];
															$keyId = rand(1, 150);
														}
													}
													else
													{
														$strValue = $arrRow['strStandardValue'];
													}
													
													$nInputfieldID 	= $arrRow["nFieldId"];
													$bIsEnabled 	= 0;
													
													
													$strEcho = $objMyCirculation->getComboBoxGroup($nInputfieldID, $strValue, $bIsEnabled, $keyId, $nRunningCounter);
													
													echo $strEcho;
												}
												elseif($arrRow["nType"] == 9)
												{
													if ($arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]!='')
													{
														$arrSplit = split('---',$arrValues[$arrRow["nFieldId"]."_".$arrSlot["nID"]]["strFieldValue"]);
													}
													else
													{
														$arrSplit = split('---',$arrRow['strStandardValue']);
													}
													
													$nNumberOfUploads 	= $arrSplit[1];
													$strDirectory		= $arrSplit[2].'_'.$nNumberOfUploads;
													
													$arrValue22 = split('rrrrr',$arrSplit[3]);
													
													$strFilename		= $arrValue22[0];
													
													$strUploadPath 		= $CUTEFLOW_SERVER.'/upload/';
													$strLink			= $strUploadPath.$strDirectory.'/'.$strFilename;
													
													echo "<a href=\"$strLink\" target=\"_blank\">$strFilename</a>";
												}
												
												echo "</td>";
																					
												if ($nRunningCounter % 2 == 0)
												{
													echo "</tr>\n<tr>\n";
												}
												else
												{
													echo "<td width=\"10px\">&nbsp;</td>";
												}
												
												$nRunningCounter++;
											}
											echo "<td></td>";
										}
									}
								?>
									</tr>
								</table>
							</td>
						</tr>
					<?php
				}
			}
		}
		
		
		function replaceLinks($value) {
			$linktext = preg_replace('/(([a-zA-Z]+:\/\/)([a-zA-Z0-9?&%.;:\/=+_-]*))/i', "<a href=\"$1\" target=\"_blank\">$1</a>", $value);
            return $linktext;
		}
		?>
</table>
<br>
<br>
<br>
</center>
</body>
</html>
