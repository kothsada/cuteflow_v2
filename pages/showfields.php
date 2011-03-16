<?php
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

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
    require_once '../lib/datetime.inc.php';
    require_once '../lib/viewutils.inc.php';
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<link rel="stylesheet" href="format.css" type="text/css">
	
	<script language="JavaScript">
	<!--
		function deleteField(nFieldId)
		{
			Check = confirm("<?php echo $FIELD_MNGT_ASKDELETE;?>");
			if(Check == true) 
			{
				location.href="deletefield.php?fieldid="+nFieldId+"&language=<?php echo $_REQUEST["language"]?>&start=<?php echo $_REQUEST["start"]?>";
			}
		}
	//-->
	</script>
    <script type="text/javascript">//<![CDATA[
    //-----------------------------------------------------------------------------
    // sortTable(id, col, rev)
    //
    //  id  - ID of the TABLE, TBODY, THEAD or TFOOT element to be sorted.
    //  col - Index of the column to sort, 0 = first column, 1 = second column,
    //        etc.
    //  rev - If true, the column is sorted in reverse (descending) order
    //        initially.
    //
    // Note: the team name column (index 1) is used as a secondary sort column and
    // always sorted in ascending order.
    //-----------------------------------------------------------------------------
    
    function sortTable(id, col, rev) {
    
      // Get the table or table section to sort.
      var tblEl = document.getElementById(id);
    
      // The first time this function is called for a given table, set up an
      // array of reverse sort flags.
      if (tblEl.reverseSort == null) {
        tblEl.reverseSort = new Array();
        // Also, assume the team name column is initially sorted.
        tblEl.lastColumn = 1;
      }
    
      // If this column has not been sorted before, set the initial sort direction.
      if (tblEl.reverseSort[col] == null)
        tblEl.reverseSort[col] = rev;
    
      // If this column was the last one sorted, reverse its sort direction.
      if (col == tblEl.lastColumn)
        tblEl.reverseSort[col] = !tblEl.reverseSort[col];
    
      // Remember this column as the last one sorted.
      tblEl.lastColumn = col;
    
      // Set the table display style to "none" - necessary for Netscape 6 
      // browsers.
      var oldDsply = tblEl.style.display;
      tblEl.style.display = "none";
    
      // Sort the rows based on the content of the specified column using a
      // selection sort.
    
      var tmpEl;
      var i, j;
      var minVal, minIdx;
      var testVal;
      var cmp;
    
      for (i = 0; i < tblEl.rows.length - 1; i++) {
    
        // Assume the current row has the minimum value.
        minIdx = i;
        minVal = getTextValue(tblEl.rows[i].cells[col]);
    
        // Search the rows that follow the current one for a smaller value.
        for (j = i + 1; j < tblEl.rows.length; j++) {
          testVal = getTextValue(tblEl.rows[j].cells[col]);
          cmp = compareValues(minVal, testVal);
          // Negate the comparison result if the reverse sort flag is set.
          if (tblEl.reverseSort[col])
            cmp = -cmp;
          // Sort by the second column (team name) if those values are equal.
          if (cmp == 0 && col != 1)
            cmp = compareValues(getTextValue(tblEl.rows[minIdx].cells[1]),
                                getTextValue(tblEl.rows[j].cells[1]));
          // If this row has a smaller value than the current minimum, remember its
          // position and update the current minimum value.
          if (cmp > 0) {
            minIdx = j;
            minVal = testVal;
          }
        }
    
        // By now, we have the row with the smallest value. Remove it from the
        // table and insert it before the current row.
        if (minIdx > i) {
          tmpEl = tblEl.removeChild(tblEl.rows[minIdx]);
          tblEl.insertBefore(tmpEl, tblEl.rows[i]);
        }
      }
    
      // Make it look pretty.
      makePretty(tblEl, col);
    
      // Set team rankings.
    //  setRanks(tblEl, col, rev);
    
      // Restore the table's display style.
      tblEl.style.display = oldDsply;
    
      return false;
    }
    
    //-----------------------------------------------------------------------------
    // Functions to get and compare values during a sort.
    //-----------------------------------------------------------------------------
    
    // This code is necessary for browsers that don't reflect the DOM constants
    // (like IE).
    if (document.ELEMENT_NODE == null) {
      document.ELEMENT_NODE = 1;
      document.TEXT_NODE = 3;
    }
    
    function getTextValue(el) {
    
      var i;
      var s;
    
      // Find and concatenate the values of all text nodes contained within the
      // element.
      s = "";
      for (i = 0; i < el.childNodes.length; i++)
        if (el.childNodes[i].nodeType == document.TEXT_NODE)
          s += el.childNodes[i].nodeValue;
        else if (el.childNodes[i].nodeType == document.ELEMENT_NODE &&
                 el.childNodes[i].tagName == "BR")
          s += " ";
        else
          // Use recursion to get text within sub-elements.
          s += getTextValue(el.childNodes[i]);
    
      return normalizeString(s);
    }
    
    function compareValues(v1, v2) {
    
      var f1, f2;
    
      // If the values are numeric, convert them to floats.
    
      f1 = parseFloat(v1);
      f2 = parseFloat(v2);
      if (!isNaN(f1) && !isNaN(f2)) {
        v1 = f1;
        v2 = f2;
      }
    
      // Compare the two values.
      if (v1 == v2)
        return 0;
      if (v1 > v2)
        return 1
      return -1;
    }
    
    // Regular expressions for normalizing white space.
    var whtSpEnds = new RegExp("^\\s*|\\s*$", "g");
    var whtSpMult = new RegExp("\\s\\s+", "g");
    
    function normalizeString(s) {
    
      s = s.replace(whtSpMult, " ");  // Collapse any multiple whites space.
      s = s.replace(whtSpEnds, "");   // Remove leading or trailing white space.
    
      return s;
    }
    
    //-----------------------------------------------------------------------------
    // Functions to update the table appearance after a sort.
    //-----------------------------------------------------------------------------
    
    // Style class names.
    var rowClsNm = "bright";
    var colClsNm = "sortedColumn";
    
    // Regular expressions for setting class names.
    var rowTest = new RegExp(rowClsNm, "gi");
    var colTest = new RegExp(colClsNm, "gi");
    
    function makePretty(tblEl, col) {
    
      var i, j;
      var rowEl, cellEl;
    
      // Set style classes on each row to alternate their appearance.
      for (i = 0; i < tblEl.rows.length; i++) {
       rowEl = tblEl.rows[i];
       rowEl.className = rowEl.className.replace(rowTest, "");
        if (i % 2 != 0)
          rowEl.className += " " + rowClsNm;
        rowEl.className = normalizeString(rowEl.className);
        // Set style classes on each column (other than the name column) to
        // highlight the one that was sorted.
        for (j = 0; j < tblEl.rows[i].cells.length; j++) {
          cellEl = rowEl.cells[j];
          cellEl.className = cellEl.className.replace(colTest, "");
          if (j == col)
            cellEl.className += " " + colClsNm;
          cellEl.className = normalizeString(cellEl.className);
        }
      }
    }
//]]>
</script>

<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="tooltip.js"></SCRIPT>
<script type="text/javascript">
   	maketip('delete','<?php echo escapeSingle($FIELD_TIP_DELETE);?>');
   	maketip('detail','<?php echo escapeSingle($FIELD_TIP_DETAILS);?>');
</script>
</head>
<?php
    //--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		//--- get maximum count of users
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			$query = "select COUNT(*) from cf_inputfield";
			$nResult = mysql_query($query, $nConnection);

			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{	
						$nCirculationCount = $arrRow[0];
					}				
				}
			}
		}
		
		if ($nCirculationCount >= $_REQUEST["start"] + 50)
		{
			$end = $_REQUEST["start"] + 49;
		}
		else
		{
			$end = $nCirculationCount;
		}
        
        $arrLists = array();
				
		//--- output the user inbetween the range (start to end)
		$strQuery = "SELECT * FROM cf_inputfield ORDER BY strName ASC LIMIT ".($_REQUEST["start"]-1).", 50";
		
		$nResult = mysql_query($strQuery, $nConnection);
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$nRunningNumber = $start;
				while (	$arrRow = mysql_fetch_array($nResult))
				{
					$arrLists[$arrRow["nID"]] = $arrRow;
				}
			}
		}            	
    }  
?>
<body><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_FIELDS;?>
</span><br><br>

	<table width="80%" cellspacing="0" cellpadding="3">
		<tr>
			<td align="left" width="14px">
				<a href="editfield.php?language=<?php echo $_REQUEST["language"];?>&fieldid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><img src="../images/addfield.gif" border="0"></a>
			</td>
			<td align="left">
				[ <a href="editfield.php?language=<?php echo $_REQUEST["language"];?>&fieldid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><?php echo $FIELD_MNGT_ADDFIELD;?></a> ]
			</td>
		</tr>
	</table><br>

	<table width="80%" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3">
        <thead>
			<tr>
				<td class="table_header" width="20">#</td>
                <td align="left" class="table_header"><a style="color:White" href="" onclick="this.blur(); return sortTable('tblBdy',  1, true);"><?php echo $FIELD_TBL_HDR_NAME;?></a></td>
				<td align="left" class="table_header"><a style="color:White" href="" onclick="this.blur(); return sortTable('tblBdy',  2, true);"><?php echo $FIELD_TBL_HDR_TYPE;?></a></td>
				<td align="left" class="table_header"><a style="color:White" href="" onclick="this.blur(); return sortTable('tblBdy',  3, true);"><?php echo $FIELD_TBL_HDR_STDVALUE;?></a></td>
				<td align="left" class="table_header"><?php echo $FIELD_TBL_HDR_READONLY;?></td>
                <td align="center" class="table_header"><?php echo $TABLE_OPTIONS;?></td>
            </tr>
        </thead>
        <tbody id="tblBdy">
        <?php
            //--- output the circulations inbetween the range (start to end)
			$nRunningNumber = $_REQUEST['start'];
            
            foreach ($arrLists as $arrRow)
    		{
            	$style = "background-color: #efefef;";
				if ($nRunningNumber%2 == 1)
				{
					$style = "background-color: #fff;";
				}
                            
            	echo "<tr valign=\"top\" style=\"$style\">";
            	echo "<td nowrap>".$nRunningNumber."</td>";
            				
            	echo "<td align=\"left\" nowrap>".$arrRow["strName"]."</td>";
				
				//--- The type of the field
				echo "<td align=\"left\">";					
				switch ($arrRow["nType"])
				{
					case 1: echo $FIELD_TYPE_TEXT; break;
					case 2: echo $FIELD_TYPE_BOOLEAN; break;
					case 3: echo $FIELD_TYPE_DOUBLE; break;
					case 4: echo $FIELD_TYPE_DATE; break;
					case 5: echo $FIELD_TYPE_LARGETEXT; break;
					case 6: echo $FIELD_TYPE_RADIOGROUP; break;
					case 7: echo $FIELD_TYPE_CHECKBOXGROUP; break;
					case 8: echo $FIELD_TYPE_COMBOBOX; break;
					case 9: echo $FIELD_TYPE_FILE; break;
				}					
				echo "</td>";
				
				echo "<td align=\"left\">";
				
				$strValue = $arrRow['strStandardValue'];
				switch($arrRow['nType'])
				{
				
					case '1':
						$arrValue = split('rrrrr',$strValue);
							
						$strFieldValue 	= $arrValue[0];
						$REG_Text		= $arrValue[1];
						break;	
					case '2':
						$strFieldValue = '-';
						break;	
					case '3':
						$arrValue = split('xx',$strValue);
						
						$arrValue1 = split('rrrrr',$arrValue[2]);
							
						$strFieldValue 	= $arrValue1[0];
						$REG_Number		= $arrValue1[1];
						break;	
					case '4':
						$arrValue = split('xx',$strValue);
						$strFieldValue = $arrValue[2];
						
						
						$arrValue1 = split('rrrrr',$arrValue[2]);
							
						$strFieldValue 	= $arrValue1[0];
						$REG_Date		= $arrValue1[1];
						break;	
					case '5':
						$strFieldValue = substr($strValue,0,45);
						$strFieldValue = str_replace("\\\"", "\"", $strFieldValue); 
						$strFieldValue = str_replace("\\'", "'", $strFieldValue);
						$strFieldValue = $strFieldValue.'...';
						break;
					case '6':
						$arrSplit = split('---',$strValue);
								
						$nSplitRunningNumber = 0;
						$nMax = sizeof($arrSplit);
						for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
						{									
							$bChecked 	= $arrSplit[$nIndex+1];
							$strValue	= $arrSplit[$nIndex];
							
							if ($bChecked)
							{
								$strFieldValue = $strValue;
								$nSplitRunningNumber = $nMax;
							}
							$nSplitRunningNumber++;
						}
						break;
					case '7':
						$strFieldValue = '-';
						break;
					case '8':
						$arrSplit = split('---',$strValue);
								
						$nSplitRunningNumber = 0;
						$nMax = sizeof($arrSplit);
						for ($nIndex = 2; $nIndex < $nMax; $nIndex += 2)
						{									
							$bChecked 	= $arrSplit[$nIndex+1];
							$strValue	= $arrSplit[$nIndex];
							
							if ($bChecked)
							{
								$strFieldValue = $strValue;
								$nSplitRunningNumber = $nMax;
							}
							$nSplitRunningNumber++;
						}
						break;
					case '9':
						$strFieldValue = '-';
						break;
				}
				
				echo $strFieldValue;
									
				echo "</td>";
				
				echo "<td align=\"left\">";
				if ($arrRow["bReadOnly"] == 0)
				{
					echo "<img src=\"../images/inactive.gif\" height=\"16\" width=\"16\">";		
				}
				else
				{
					echo "<img src=\"../images/active.gif\" height=\"16\" width=\"16\">";
				}
				echo "</td>";
				                                
                echo "<td align=\"center\">";							
            	echo "<a href=\"javascript:deleteField($arrRow[0])\" alt=\"L�schen\" onMouseOver=\"tip('delete')\" onMouseOut=\"untip()\"><img src=\"../images/edit_remove.gif\" border=\"0\"height=\"16\" width=\"16\" style=\"margin-right: 4px;\"></a>";
            	echo "<a href=\"editfield.php?fieldid=$arrRow[0]&language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\" onMouseOver=\"tip('detail')\" onMouseOut=\"untip()\" alt=\"Anzeigen\"><img src=\"../images/edit.png\" border=\"0\"height=\"16\" width=\"16\"></a>";
            	echo "</td></tr>";
            											
            	$nRunningNumber++;
            }
        ?>
        </tbody>
    </table>
    <table width="80%">
		<tr>
			<td>
				<?php 
                    if ($_REQUEST["start"] > $end)
                    {
                        $_REQUEST["start"] = $end;
                    }
                    
					$FIELD_MNGT_SHOWRANGE = str_replace("_%From", $_REQUEST["start"], $FIELD_MNGT_SHOWRANGE);
					$FIELD_MNGT_SHOWRANGE = str_replace("_%To", $end, $FIELD_MNGT_SHOWRANGE);
                    $FIELD_MNGT_SHOWRANGE = str_replace("_%Off", $nCirculationCount, $FIELD_MNGT_SHOWRANGE);
					echo $FIELD_MNGT_SHOWRANGE;?><br>		
			</td>
			<td align="right">
				<?php
					if ($_REQUEST["start"] > 50)
					{
						?>
							<a href="showfields.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"]-50;?>&sortby=<?php echo $_REQUEST["sortby"];?>"><img src="../images/prev.png" height="10" width="10" alt="prev" border="0"> <?php echo $BTN_BACK;?></a>
						<?php
					}
					
					if ($end < $nCirculationCount)
					{
						?>
							<a href="showfields.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"]+50;?>&sortby=<?php echo $_REQUEST["sortby"];?>"><?php echo $BTN_NEXT;?> <img src="../images/next.png" height="10" width="10" alt="next"  border="0"></a>
						<?php
					}
					
				?>
			</td>
		</tr>
	</table>
	
</body>
</html>
