<?php

	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
    require_once '../lib/datetime.inc.php';
    require_once '../lib/viewutils.inc.php';

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
	
    //--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			//-----------------------------------------------
			//--- get the user information from 
			//--- cf_circulationprocess
			//-----------------------------------------------
			$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationProcess = mysql_fetch_array($nResult);				
				}
			}
			
            //-----------------------------------------------
			//--- get the single circulation form
			//-----------------------------------------------
			$query = "select * from cf_circulationform WHERE nID=".$arrCirculationProcess["nCirculationFormId"];
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
    		//--- get all users
            //-----------------------------------------------
            $arrUsers = array();
    		$strQuery = "SELECT * FROM cf_user WHERE bDeleted <> 1";
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
    		
    		if ($_REQUEST["nRevisionId"] == "")
    		{
    			//-----------------------------------------------
				//--- get history (all revisions)
				//-----------------------------------------------
				$arrHistoryData = array();
				$nMaxRevisionId = 0;
				$strQuery = "SELECT MAX(nID) FROM cf_circulationhistory WHERE nCirculationFormId=".$arrCirculationProcess["nCirculationFormId"];
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
	    			{
	    				$arrRow = mysql_fetch_array($nResult);
	    				$_REQUEST["nRevisionId"] = $arrRow[0];
	    			}
	    		}
    		}
    		
    		//-----------------------------------------------
            //--- get the form process detail
            //-----------------------------------------------	            
            $arrProcessInformation = array();
			$arrProcessInformationSubstitute = array();
			
            $strQuery = "SELECT * FROM cf_circulationprocess WHERE nCirculationFormId=".$arrCirculationProcess["nCirculationFormId"]." AND nCirculationHistoryId=".$_REQUEST["nRevisionId"]." ORDER BY dateInProcessSince";
            //$strQuery = "SELECT * FROM cf_circulationprocess WHERE nCirculationFormId=".$_REQUEST["circid"]." AND nCirculationHistoryId=".$_REQUEST["nRevisionId"]." ORDER BY dateInProcessSince";
    		$nResult = mysql_query($strQuery, $nConnection);
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
		global $CIRCDETAIL_DAYS, $CIRCDETAIL_STATE_DENIED, $SELF_DELEGATE_USER;
		global $arrCirculationForm;
		
		if ($bIsSubstitute == false)
		{
			if ($nUserId != -2)
			{
				echo "<td width=\"20px\" align=\"left\" valign=\"top\"><img src=\"../images/singleuser.gif\" height=\"19\" width=\"16\"></td>\n";
	       		if ($arrCirculationForm["bAnonymize"] == 1) {
	       			echo "<td width=\"140px\" align=\"left\" valign=\"top\">anon</td>\n";	
	       		}
	       		else {
					echo "<td width=\"140px\" align=\"left\" valign=\"top\">".$arrUsers[$nUserId]["strUserId"]."</td>\n";
	       		}
			}
			else
			{
				echo "<td width=\"20px\" align=\"left\" valign=\"top\"><img src=\"../images/user_green.gif\" height=\"19\" width=\"16\"></td>\n";
	       		echo "<td width=\"140px\" align=\"left\" valign=\"top\">$SELF_DELEGATE_USER</td>\n";
			}
		}
		else
		{
			echo "<td width=\"20px\" align=\"right\"><img src=\"../images/right.png\" height=\"16\" width=\"16\"></td>\n";
			if ($arrCirculationForm["bAnonymize"] == 1) {
       			echo "<td width=\"140px\" align=\"left\" valign=\"top\"><img src=\"../images/singleuser2.gif\" height=\"19\" width=\"16\" align=\"absmiddle\">&nbsp;&nbsp;anon</td>\n";
       		}
       		else {
				echo "<td width=\"140px\" align=\"left\" valign=\"top\"><img src=\"../images/singleuser2.gif\" height=\"19\" width=\"16\" align=\"absmiddle\">&nbsp;&nbsp;".$arrUsers[$arrRow["nUserId"]]["strUserId"]."</td>\n";
       		}

		}
	
		//--- The process state
		if (!$arrRow)
		{
			echo "<td width=\"16px\">&nbsp;</td>\n";
		}
		else
		{
			switch ($arrRow["nDecissionState"])
			{
				case 0: $strImage = "state_wait.gif";
						break;
				case 1: $strImage = "state_ok.png";
						break;
				case 2: $strImage = "state_stop.png";
						break;
				case 4: $strImage = "state_skip.png";
						break;
				case 8: $strImage = "state_skip.png";
						break;
				case 16: $strImage = "stop.gif";
						break;
						
			}
			echo "<td width=\"16px\" align=\"right\">";
			echo "<img src=\"../images/$strImage\" height=\"16\" width=\"16\">";
			echo "</td>\n";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
   	<link rel="stylesheet" href="../pages/format.css" type="text/css">
</head>
<body style="margin: 8px 0px 0px 0px;"><center>
	<table border="0" width="95%" cellpadding="0" cellspacing="0" class="BorderSilver">
    <tr>
        <td colspan="2">
            <table bgcolor="Silver" width="100%">
                <tr>
                    <td width="20px"><img src="../images/history.gif" height="16" width="16"></td>
                    <td style="font-weight:bold;"><?php echo $CIRCDETAIL_HISTORY;?></td>
                </tr>
            </table>
        </td>
    </tr>	
	<tr>
		<td colspan="2">
			<table width="100%" style="background-color: white;">
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
			    				
			    				$nResult = mysql_query($strQuery, $nConnection);
			                    if ($nResult)
				   		        {
			                    	if (mysql_num_rows($nResult) > 0)
			                        {
										?>
											<tr>
										    	<td colspan="8" style="border-bottom: 1px solid Silver; padding-top:8px;"><strong><?php echo $MAILLIST_EDIT_FORM_SLOT.": ".$arrSlot[1];?></strong></td>
											</tr>
						                <?php
			                           	while (	$arrRow = mysql_fetch_array($nResult))
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
											
											$nCurPiId = $arrCurPi['nID'];
											$arrSubstitute = $arrProcessInformationSubstitute[$nCurPiId];
											
											?>
											<tr style="<?php if ($bLastUser && !$arrSubstitute) echo 'background-color: #FFE88E;'; ?> height:22px;">
											<?php
											
											printUser($arrCurPi, false, $arrRow['nUserId'], $bLastUser);
											
											
											
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
														?>
														</tr>
														<tr style="<?php if ($bLastUser && $arrSubstitute && ($arrCurCP['nDecissionState'] == 0)) echo 'background-color: #FFE88E;' ?> height:22px;">
														<?php
														printUser($arrCurCP, true, $nSubstituteId, $bLastUser);
													}
													else
													{
														$print_next = 0;
													}
													if ($arrCurCP['nID'] == $nCurPiId) $print_next = 1;
												}
											}

											?>
											</tr>
											<?php
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
</body>
</html>
