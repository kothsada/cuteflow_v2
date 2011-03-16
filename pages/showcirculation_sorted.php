<?php 	

/**
* Function converts an Javascript escaped string back into a string with specified charset (default is UTF-8).
*
* @param string $source escaped with Javascript's escape() function
* @param string $iconv_to destination character set will be used as second paramether in the iconv function. Default is UTF-8.
* @return string
*/
function unescape($source, $iconv_to = 'UTF-8')
{
	$decodedStr = '';
	$pos = 0;
	$len = strlen ($source);
	while ($pos < $len) 
	{
		$charAt = substr ($source, $pos, 1);
		if ($charAt == '%') 
		{
			$pos++;
			$charAt = substr ($source, $pos, 1);
			if ($charAt == 'u') 
			{
				// we got a unicode character
				$pos++;
				$unicodeHexVal = substr ($source, $pos, 4);
				$unicode = hexdec ($unicodeHexVal);
				$decodedStr .= code2utf($unicode);
				$pos += 4;
			}
			else 
			{
				// we have an escaped ascii character
				$hexVal = substr ($source, $pos, 2);
				$decodedStr .= chr (hexdec ($hexVal));
				$pos += 2;
			}
		}
		else 
		{
			$decodedStr .= $charAt;
			$pos++;
		}
	}
	if ($iconv_to != "UTF-8") 
	{
		$decodedStr = iconv("UTF-8", $iconv_to, $decodedStr);
	}
	return $decodedStr;
}

include	('../language_files/language.inc.php');
	header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");  
	
	echo '<?xml version="1.0" encoding="'.$DEFAULT_CHARSET.'"?>';
	
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once	('CCirculation.inc.php');
		
	$language		= $_REQUEST['language'];	
	$archivemode	= $_REQUEST['archivemode'];
	$sortDirection	= $_REQUEST['sortDirection'];
	$sortby			= $_REQUEST['sortby'];
	$start			= $_REQUEST['start'];
	$nShowRows		= $_REQUEST['nShowRows'];
		
	$_SESSION['SESSION_CUTEFLOW_ACCESSLEVEL'] = $_REQUEST['nAccessLevel'];
	
    $objCirculation = new CCirculation();
	
	if ($_REQUEST['bFilterOn'])
	{	// extended filter has been activated
		$_REQUEST['FILTER_Name'] = unescape($_REQUEST['FILTER_Name'], $DEFAULT_CHARSET);
		
		$nIndexValues 		= 0;
		while(list($key, $value) = each($_REQUEST))
		{
			$arrCurKey = split('_', $key);
						
			if ($arrCurKey[0] == 'FILTERCustom')
			{
				$arrPart2 = split('--', $arrCurKey[1]);
				
				$strType 			= $arrPart2[0];
				$nFILTERCustomID 	= $arrPart2[1];
				
				switch($strType)
				{
					case 'Field':
						$arrFILTERCustom[$nFILTERCustomID]['nInputFieldID'] = $value;
						break;
					case 'Operator':
						if ($value == 1) $strOperator = '=';
						if ($value == 2) $strOperator = '<';
						if ($value == 3) $strOperator = '>';
						if ($value == 4) $strOperator = '<=';
						if ($value == 5) $strOperator = '>=';
						if ($value == 6) $strOperator = 'LIKE';
						
						$arrFILTERCustom[$nFILTERCustomID]['strOperator'] = $strOperator;
						break;
					case 'Value':
						$arrFILTERCustom[$nFILTERCustomID]['strValue'] = unescape($value, $DEFAULT_CHARSET);
						break;
				}														
				$nIndexValues++;
			}		
		}
		
		$FILTER_CUSTOM = false;
		if ((sizeof($arrFILTERCustom) > 0) && ($arrFILTERCustom[0]['strValue'] != ''))
		{
			$FILTER_CUSTOM = $arrFILTERCustom;
		}
		
		$arrCirculationOverview = $objCirculation->getCirculationOverview($start, $sortby, $sortDirection, $archivemode, $nShowRows, 1, $_REQUEST['FILTER_Name'], $_REQUEST['FILTER_Sender'], $_REQUEST['FILTER_Mailinglist'], $_REQUEST['FILTER_Station'], $_REQUEST['FILTER_DaysInProgress_Start'], $_REQUEST['FILTER_DaysInProgress_End'], $_REQUEST['FILTER_Date_Start'], $_REQUEST['FILTER_Date_End'], $_REQUEST['FILTER_Template'], $FILTER_CUSTOM);
		
		if (($_REQUEST['FILTER_Name'] != '') || ($_REQUEST['FILTER_Sender'] != '') || ($_REQUEST['FILTER_Mailinglist'] != '') || ($_REQUEST['FILTER_Station'] != '') || ($_REQUEST['FILTER_DaysInProgress_Start'] != '') || ($_REQUEST['FILTER_DaysInProgress_End'] != '') || ($_REQUEST['FILTER_Date_Start'] != '') || ($_REQUEST['FILTER_Date_End'] != '') || ($_REQUEST['FILTER_Template'] != '0') || ($FILTER_CUSTOM != false))
		{
			// TODO
			if (($FILTER_CUSTOM != false) && (sizeof($FILTER_CUSTOM) > 0))
			{
				$arrResults = array();
				$nMyCounter = 0;
				$nMax = sizeof($FILTER_CUSTOM);
				for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
				{
					$arrFilter = $FILTER_CUSTOM[$nIndex];
				
					$nInputfieldId 	= $arrFilter['nInputFieldID'];
					$strOperator 	= $arrFilter['strOperator'];
					$strValue		= $arrFilter['strValue'];
					
					if ($strValue != '')
					{
						$arrResults = array();
						$nMyCounter = 0;
						$nMaxOverview = sizeof($arrCirculationOverview);
						
						for($nIndex2 = 0; $nIndex2 < $nMaxOverview; $nIndex2++)
						{
							$arrCurCircOverview = $arrCirculationOverview[$nIndex2];
							
						    $nCirculationFormID = $arrCurCircOverview['nID'];
						    $nSenderID			= $arrCurCircOverview['nSenderId'];
						    $strTitle			= $arrCurCircOverview['strName'];
						    $nMailingListID		= $arrCurCircOverview['nMailingListId'];
							
						    $nCirculationHistoryID = $objCirculation->getMaxCirculationHistoryID($nCirculationFormID);
							$strMyFieldValue = $objCirculation->getMyFieldValue($nCirculationFormID, $nCirculationHistoryID, $nInputfieldId);
							
							//echo 'strMyFieldValue: '.$strMyFieldValue.'<br>';
							//echo 'Operator: '.$strOperator.'<br>';
							//echo 'strValue: '.$strValue.'<br>';
							//echo 'nInputfieldId: '.$nInputfieldId.'<br><br>';
							switch($strOperator)
							{
								case '=':
									if ($strMyFieldValue == $strValue)
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
								case '<':
									if ($strMyFieldValue < $strValue)
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
								case '>':
									if ($strMyFieldValue > $strValue)
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
								case '<=': 
									if ($strMyFieldValue <= $strValue)
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
								case '>=': 
									if ($strMyFieldValue >= $strValue)
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
								case 'LIKE':
									if (preg_match("/$strValue/i", $strMyFieldValue))
									{
										$arrResults[$nMyCounter] = $arrCurCircOverview;
										$nMyCounter++;
									}
									break;
							}
						}
					}
				}
				$arrCirculationOverview = $arrResults;
			}
			
			$nAmountOfFilteredCirculations = sizeof($arrCirculationOverview);
		}
		
		if($nShowRows < sizeof($arrCirculationOverview))
		{
			$nStart = $start-1;
			$nMax	= $nShowRows + $nStart;					
			$nTempIndex = 0;
			
			for ($nIndex = $nStart; $nIndex < $nMax; $nIndex++)
			{
				if ($arrCirculationOverview[$nIndex] != '')
				{
					$arrTemp[$nTempIndex] = $arrCirculationOverview[$nIndex];
					$nTempIndex++;
				}
			}
			$arrCirculationOverview = $arrTemp;
		}			
		
		if ($nShowRows < sizeof($arrCirculationOverview))
		{
			$arrResults = Array();
			for ($nIndex = 0; $nIndex < $nShowRows; $nIndex++)
			{
				$arrResults[$nIndex] = $arrCirculationOverview[$nIndex];
			}
			$arrCirculationOverview = $arrResults;
		}
	}
	else
	{
		$arrCirculationOverview = $objCirculation->getCirculationOverview($start, $sortby, $sortDirection, $archivemode, $nShowRows, 0);
	}

	$arrAllUsers 		= $objCirculation->getAllUsers();
	$arrAllMailingLists	= $objCirculation->getAllMailingLists();
	$arrAllInputFields	= $objCirculation->getMyInputFields();
    
    function getSortDirection($strColumn)
    {
    	global $sortby, $sortDirection;
    	
    	if ($strColumn == $sortby)
    	{
    		if ($sortDirection == 'ASC')
    		{
    			return 'DESC';
    		}
    		else 
    		{
    			return 'ASC';
    		}
    	}
    	else 
    	{
    		return 'ASC';
    	}
    }
    
    function getColHighlight($nRow, $strSortBy, $strCol)
    {
    	if ($strCol == $strSortBy)
    	{
    		if (($nRow % 2) == 0)
    		{
    			return ' class="highlight_dark" ';	
    		}
    		else 
    		{
    			return ' class="highlight_bright" ';	
    		}
    	}
    	return '';
    }
	
	function getDelayColor($nDays)
    {
		global $DELAY_NORMAL, $DELAY_INDERMIDIATE;

        if ($nDays <= $DELAY_NORMAL)
        {
            return "#019A10";
        }
        else if ($nDays <= $DELAY_INDERMIDIATE)
        {
            return "#FF6C00";
        }
        else
        {
            return "#F70415";
        }
    }
?>
	
	<table width="90%" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="2">
	<tr>
		<td class="table_header" width="20">#</td>
	
		<?php
		foreach ($arrCirculation_Cols as $strCurRow)
		{			
			switch($strCurRow)
			{
				case 'COL_CIRCULATION_NAME': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_NAME')."', 'COL_CIRCULATION_NAME', '1');\"\">";
					echo $CIRCULATION_MNGT_NAME; 
					break;	
				case 'COL_CIRCULATION_STATION': 
					if ($archivemode == 0)
					{
						echo "<td class=\"table_header\" align=\"left\" nowrap>";
						echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_STATION')."', 'COL_CIRCULATION_STATION', '1');\"\">";
						echo $CIRCULATION_MNGT_CURRENT_SLOT;
					} 
					break;
				case 'COL_CIRCULATION_PROCESS_DAYS': 
					if ($archivemode == 0)
					{
						echo "<td class=\"table_header\" align=\"left\" nowrap>";
						echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_PROCESS_DAYS')."', 'COL_CIRCULATION_PROCESS_DAYS', '1');\"\">";
						echo $CIRCULATION_MNGT_WORK_IN_PROCESS;
					}  
					break;
				case 'COL_CIRCULATION_PROCESS_START': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_PROCESS_START')."', 'COL_CIRCULATION_PROCESS_START', '1');\"\">";
					echo $CIRCULATION_MNGT_SENDING_DATE; 
					break;
				case 'COL_CIRCULATION_SENDER': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_SENDER')."', 'COL_CIRCULATION_SENDER', '1');\"\">";
					echo $CIRCDETAIL_SENDER; 
					break;
				case 'COL_CIRCULATION_MAILLIST': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_MAILLIST')."', 'COL_CIRCULATION_MAILLIST', '1');\"\">";
					echo $SHOW_CIRCULATION_MAILLIST; 
					break;
				case 'COL_CIRCULATION_TEMPLATE': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_TEMPLATE')."', 'COL_CIRCULATION_TEMPLATE', '1');\"\">";
					echo $SHOW_CIRCULATION_TEMPLATE; 
					break;
				case 'COL_CIRCULATION_WHOLETIME': 
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a href=\"#\" style=\"color: #fff;\" onClick=\"sortResult('".getSortDirection('COL_CIRCULATION_WHOLETIME')."', 'COL_CIRCULATION_WHOLETIME', '1');\"\">";
					echo $SHOW_CIRCULATION_WHOLETIME; 
					break;
				case 'COL_CIRCULATION_FIELD1':
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a style=\"color: #fff;\">";
					$field_id = 1;					
					foreach ($arrCONF_AllCols as $column_description) {
						if ($column_description['strTitle'] == 'COL_CIRCULATION_FIELD1') {
							$field_id = $column_description['strScreenTitle'];
						}
					}
					$field = Field::create()->find($field_id);
					echo $field['strName']; 
					break;
				case 'COL_CIRCULATION_FIELD2':
					echo "<td class=\"table_header\" align=\"left\" nowrap>";
					echo "<a style=\"color: #fff;\">";
					$field_id = 1;					
					foreach ($arrCONF_AllCols as $column_description) {
						if ($column_description['strTitle'] == 'COL_CIRCULATION_FIELD2') {
							$field_id = $column_description['strScreenTitle'];
						}
					}
					$field = Field::create()->find($field_id);
					echo $field['strName']; 
					break;
			}
			echo '</a></td>';
		}
		
    	if ($archivemode != 1)
		{
			?>
        	<td class="table_header">
        		<?php echo $CIRCULATION_VIEW_PROCESS; ?>
        	</td>
        	<?php
        } 
        ?>
        <td class="table_header" width="80" align="center"><?php echo $TABLE_OPTIONS;?></td>
    </tr>
	<tbody id="tblBdy">
	
	<?php	
		$nAmount = sizeof($arrCirculationOverview)-1;
		$nShownIndex = $start;
				
		for($nIndex = 0; $nIndex <= $nAmount; $nIndex++)
		{ 
			$arrRow = $arrCirculationOverview[$nIndex];
			
			$nCirculationFormID = $arrRow['nID'];
			$nSenderID 			= $arrRow['nSenderId'];
			$strTitle	 		= $arrRow['strName'];
			$nMailingListId		= $arrRow['nMailingListId'];
			if ($arrRow['strCurStation'] != '')
			{
				$strCurStation	= $arrRow['strCurStation'];
			}
			
			$nCirculationHistoryID = $objCirculation->getMaxCirculationHistoryID($nCirculationFormID);
			
			$arrDecissionState 	= $objCirculation->getDecissionState($nCirculationFormID);
			$strStartDate		= $objCirculation->getStartDate($nCirculationFormID);
			$strSender			= $objCirculation->getSender($nCirculationFormID);
			$arrMaillist		= $objCirculation->getMailinglist($nMailingListId);
			$strMaillist		= $arrMaillist['strName'];
			$list_permission 	= $arrMaillist['permissions'];
			$arrTemplate		= $objCirculation->getTemplate($arrMaillist['nTemplateId']);
			$strTemplate		= $arrTemplate['strName'];
			$strWholeTime		= $objCirculation->getWholeTime($nCirculationFormID);
			
			$bStopped = false;
			$class = 'rowEven';
			if ($nIndex%2 == 0)
			{
				$class = 'rowUneven';
			}
                
			echo "\n<tr class=\"$class\" valign=\"top\">\n";
			echo "<td>$nShownIndex</td>";
			
			foreach ($arrCirculation_Cols as $strCurRow)
			{			
				switch($strCurRow)
				{
					case 'COL_CIRCULATION_NAME':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_NAME')." align=\"left\">";
						echo $arrRow['strName'];
						break;	
					case 'COL_CIRCULATION_STATION':
						if ($archivemode == 0)
						{
							echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_STATION')." align=\"left\">";
							switch ($arrDecissionState['nDecissionState'])
							{
								case 0: echo $arrDecissionState['strCurStation']; break;
								case 1: echo "<img src=\"../images/circ_done.gif\">&nbsp;<em>$CIRCULATION_MNGT_CIRC_DONE</em>"; break;
								case 2: $bStopped = true; echo "<img src=\"../images/circ_stop.gif\">&nbsp;<em>$CIRCULATION_MNGT_CIRC_BREAK</em>"; break;
								case 4: echo "<img src=\"../images/circ_done.gif\">&nbsp;<em>$CIRCULATION_MNGT_CIRC_DONE </em>"; break; //new
								case 8: echo $arrDecissionState['strCurStation']; break;
								case 16: $bStopped = true; echo "<img src=\"../images/circ_stop.gif\">&nbsp;<em>$CIRCULATION_MNGT_CIRC_STOP</em>"; break;
							}
						}
						break;
					case 'COL_CIRCULATION_PROCESS_DAYS': 
						if ($archivemode == 0)
						{
							echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_PROCESS_DAYS')." align=\"left\">";
							echo "<span style=\"color:".getDelayColor($arrDecissionState["nDaysInProgress"]).";\">".$arrDecissionState["nDaysInProgress"]."</span>";
						}						
						break;
					case 'COL_CIRCULATION_PROCESS_START':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_PROCESS_START')." align=\"left\">";
						echo $strStartDate;
						break;
					case 'COL_CIRCULATION_SENDER':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_SENDER')." align=\"left\">";
						echo $strSender;
						break;
					case 'COL_CIRCULATION_MAILLIST':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_MAILLIST')." align=\"left\">";
						echo $strMaillist;
						break;
					case 'COL_CIRCULATION_TEMPLATE':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_TEMPLATE')." align=\"left\">";
						echo $strTemplate;
						break;
					case 'COL_CIRCULATION_WHOLETIME':
						echo "<td nowrap ".getColHighlight($nIndex, $sortby, 'COL_CIRCULATION_WHOLETIME')." align=\"left\">";
						echo $strWholeTime;
						break;
					case 'COL_CIRCULATION_FIELD1':
						echo "<td nowrap align=\"left\">";
						$field_id = 1;					
						foreach ($arrCONF_AllCols as $column_description) {
							if ($column_description['strTitle'] == 'COL_CIRCULATION_FIELD1') {
								$field_id = $column_description['strScreenTitle'];
							}
						}
						echo $objCirculation->getMyFieldValue($nCirculationFormID, $nCirculationHistoryID, $field_id);
						break;
					case 'COL_CIRCULATION_FIELD2':
						echo "<td nowrap align=\"left\">";
						echo $strField2Value;
						break;
				}
				echo "</td>";
			}
			
			if ($archivemode != 1)
			{
				if (($arrDecissionState['nDecissionState'] != 1) || ($arrDecissionState['nDecissionState'] != 4) )
				{
					$arrProgress = $objCirculation->getWidth($nCirculationFormID, $nMailingListId);
					
					$width = $arrProgress['width'];
					$mycolor = $arrProgress['color'];
					
					if ($width < 0)
					{
						echo "	<td> - </td>";
					}
					else
					{	
						?>
						<td valign="middle">
		    				<div id="counter" class="counter" style="width: <?php echo $width ?>%; <?php if ($width > 0) echo 'background-color: '.$mycolor ?>;"><?php echo $width ?>%
							</div>
						</td>
						<?php
					}
				}
				else
				{
					echo "	<td> - </td>";
				}
			}
			
			// at least the options column
			echo "<td align=\"center\" nowrap>";			
			
			if (!$_REQUEST['bOwnCirculations'])
			{	// show the general options
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					echo "<a href=\"javascript:deleteCirculation($nCirculationFormID, $start)\" onMouseOver=\"tip('delete')\" onMouseOut=\"untip()\"><img src=\"../images/edit_remove.gif\" border=\"0\"height=\"16\" width=\"16\" style=\"margin-right: 4px;\"></a>";
				}
				
				if ($OPEN_DETAILS_IN_SEPERATE_WINDOW == true)
				{
					echo "<a $strTarget href=\"#\" onMouseOver=\"tip('detail')\" onMouseOut=\"untip()\" onClick=\"showCirculationDetails(".$nCirculationFormID.", 1); return false\"><img src=\"../images/act_view.gif\" border=\"0\"height=\"16\" width=\"16\"></a> ";
				}
				else
				{
					echo "<a $strTarget href=\"#\" onMouseOver=\"tip('detail')\" onMouseOut=\"untip()\" onClick=\"showCirculationDetails(".$nCirculationFormID.", 0); return false\"><img src=\"../images/act_view.gif\" border=\"0\"height=\"16\" width=\"16\"></a> ";
				}
				
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					$archivebit = 0;
					$tip = 'unarchive';
					$img = '../images/export_wiz.gif';
					if ($archivemode == 0)
					{
						$archivebit = 1;
						$tip = 'archive';
						$img = '../images/import_wiz.gif';
					}
					?>
					<a href="javascript:archiveCirculation(<?php echo $nCirculationFormID ?>, <?php echo $archivebit ?>, <?php echo $start ?>)" onMouseOver="tip('<?php echo $tip ?>')" onMouseOut="untip()"><img src="<?php echo $img ?>" border="0"height="16" width="16"></a>
					<?php
				}
				
				if ($archivemode == 0)
				{
					$SetOne = 0;
					if ( ($arrDecissionState['nDecissionState'] != 1) && 
						 ($arrDecissionState['nDecissionState'] != 2) &&
						 ($arrDecissionState['nDecissionState'] != 4) &&
						 ($arrDecissionState['nDecissionState'] != 16) &&
						 (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8)))
					{
						?>
						<a href="javascript:stopCirculation(<?php echo $nCirculationFormID ?>, <?php echo $start ?>)" onMouseOver="tip('stop')" onMouseOut="untip()"><img src="../images/stop.gif" border="0"height="16" width="16"></a>
						<?php
						$SetOne = 1;
					}
					
					if ( ($bStopped == true) && (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8)))
					{
						echo "<a href=\"editcirculation.php?circid=$nCirculationFormID&language=$language&bRestart=1\" onMouseOver=\"tip('restart')\" onMouseOut=\"untip()\" ><img src=\"../images/restart.gif\" border=\"0\"height=\"16\" width=\"16\"></a> ";
						$SetOne = 1;
					}
					if (!$SetOne)
					{
						?>
						<img src="../images/inv.gif" height="1" width="17">
						<?php
					}
				}
			}
			else
			{	// show the options for "Own Circulations" only
				if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
				{
					?>
					<a href="javascript: deleteCirculation(<?php echo $nCirculationFormID ?>, <?php echo $start ?>)" onMouseOver="tip('delete')" onMouseOut="untip()"><img src="../images/edit_remove.gif" border="0" height="16" width="16" style="margin-right: 4px;"></a>
					<?php
				}
				?>
				<a href="#" onMouseOver="tip('detail')" onMouseOut="untip()" onClick="showCirculationDetails(<?php echo $nCirculationFormID ?>, <?php if ($OPEN_DETAILS_IN_SEPERATE_WINDOW == true) { echo 1; } else { echo 0; } ?>); return false"><img src="../images/act_view.gif" border="0"height="16" width="16" style="margin-right: 4px;"></a>
				<a href="javascript: editCirculation(<?php echo $nCirculationFormID ?>)" onMouseOver="tip('edit')" onMouseOut="untip()"><img src="../images/edit.png" border="0"height="16" width="16" style="margin-right: 4px;"></a>
				<?php
			}
			echo "</td></tr>";
			$nShownIndex++;
		}
		echo '</tbody>';
		echo '</table>';
	?>
	
	
    <table width="90%">
		<tr>
			<td align="left">
				<?php 
				if (!$_REQUEST['bFilterOn'])
				{
					$nAmountOfCirculations = sizeof($arrCirculationOverview);
				}
				else
				{
					$nAmountOfCirculations = $nAmountOfFilteredCirculations;
				} 
				
				if ($nAmountOfCirculations == 0) {
					$From = 0;
				}
				else {
					$From = ( ($start-1) == 0) ? 1 : $start;
				}
				
                $strRange = str_replace("_%From", $From, $CIRCULATION_MNGT_SHOWRANGE);
				$strRange = str_replace("_%To", $start + ($nIndex-1), $strRange);
				$strRange = str_replace("_%Off", $nAmountOfCirculations, $strRange);
				
				echo $strRange;
				?>
				<br>		
			</td>
			<td align="right">
				<?php
				$nNumberofRows = $nShowRows;
				$end = $nShownIndex-1;
				
				if ($start > $nNumberofRows)
				{
					?>
						<a onClick="sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', '<?php echo ($start-$nNumberofRows);?>');" href="#"><?php echo $BTN_BACK;?></a>
					<?php
				}
				
				if (($end) < $nAmountOfCirculations)
				{
					?>
						<a onClick="sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', '<?php echo ($start+$nNumberofRows);?>');" href="#"><?php echo $BTN_NEXT;?></a>
					<?php
				}
				?>
			</td>
		</tr>
	</table>