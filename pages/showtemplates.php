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
	
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
    include_once ("../lib/datetime.inc.php");
    include_once ("../lib/viewutils.inc.php");
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<link rel="stylesheet" href="format.css" type="text/css">
	<script src="../lib/prototype/prototype.js" type="text/javascript"></script>
	<script language="JavaScript">
	<!--
		function deleteTemplate(nTemplateId)
		{
			Check = confirm("<?php echo $TEMPLATE_MNGT_ASKDELETE;?>");
			if(Check == true) 
			{
				location.href="deletetemplate.php?templateid="+nTemplateId+"&language=<?php echo $_REQUEST["language"]?>&start=<?php echo $_REQUEST["start"]?>";
			}
		}
	//-->
	</script>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="tooltip.js"></SCRIPT>
<script type="text/javascript">
   	maketip('delete','<?php echo escapeSingle($TEMPLATE_TIP_DELETE);?>');
   	maketip('detail','<?php echo escapeSingle($TEMPLATE_TIP_DETAILS);?>');
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
			$query = "select COUNT(*) from cf_formtemplate WHERE bDeleted=0";
			$nResult = mysql_query($query, $nConnection);

			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{	
						$nListCount = $arrRow[0];
					}				
				}
			}
		}
		
		if ($nListCount > $_REQUEST["start"] + 50)
		{
			$end = $_REQUEST["start"] + 49;
		}
		else
		{
			if ($_REQUEST["start"]+50 > $nListCount)
			{
				$end = $nListCount;
			}
			else
			{
				$end = $_REQUEST["start"] + 50;
			}
		}
        
        $arrLists = array();
				
		//--- output the user inbetween the range (start to end)
		$sortCol = "strName";
		
		if ($_REQUEST["sortas"] != "")
		{
			$sortAs = $_REQUEST["sortas"];
			$toggledSortAs = $_REQUEST["sortas"]=="ASC" ? "DESC" : "ASC";
		}
		else
		{
			$sortAs = "ASC";
			$toggledSortAs = "DESC";
		}
		
		$strQuery = "SELECT * FROM cf_formtemplate WHERE bDeleted=0 ORDER BY $sortCol $sortAs LIMIT ".($_REQUEST["start"]-1).", ".$end."";
		
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
	<?php echo $MENU_TEMPLATE;?>
</span><br><br>

	<table width="620" cellspacing="0" cellpadding="3">
		<tr>
			<td align="left" width="14px">
				<a href="edittemplate_step1.php?language=<?php echo $_REQUEST["language"];?>&templateid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><img src="../images/addtemplate.png" border="0"></a>
			</td>
			<td align="left">
				[ <a href="edittemplate_step1.php?language=<?php echo $_REQUEST["language"];?>&templateid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><?php echo $TEMPLATE_MNGT_ADDTEMPLATE;?></a> ]
			</td>
		</tr>
	</table><br>
	
	<table width="620" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3">
       	<tr>
			<td class="table_header" align="left" width="20">#</td>
            <td align="left" class="table_header"><a style="color:White" href="showtemplates.php?<?php echo "language=".$_REQUEST["language"]."&start=".$_REQUEST["start"]."&sortby=name&sortas=".$toggledSortAs;?>"><?php echo $FIELD_TBL_HDR_NAME;?></a></td>
	        <td class="table_header" align="right"><?php echo $TABLE_OPTIONS;?></td>
        </tr>

        <?php
            //--- output the circulations inbetween the range (start to end)
			$nRunningNumber = 1;
            
            foreach ($arrLists as $arrRow)
    		{
            	$style = "background-color: #FFFAFA;";
				if ($nRunningNumber%2 == 1)
				{
					$style = "background-color: #EFEFEF;";
				}
                            
            	echo "<tr valign=\"top\" style=\"$style\">";
            	echo "<td nowrap align=\"left\">".$nRunningNumber."</td>";
            				
            	echo "<td align=\"left\" nowrap>".$arrRow["strName"]."</td>";
				
                echo "<td align=\"right\">";							
            	echo "<a href=\"javascript:deleteTemplate($arrRow[0])\" alt=\"L�schen\" onMouseOver=\"tip('delete')\" onMouseOut=\"untip()\"><img src=\"../images/edit_remove.gif\" border=\"0\" height=\"16\" width=\"16\" style=\"margin-right: 4px;\"></a>";
            	echo "<a href=\"edittemplate_step1.php?templateid=$arrRow[0]&language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\" onMouseOver=\"tip('detail')\" onMouseOut=\"untip()\" alt=\"Anzeigen\"><img src=\"../images/edit.png\" border=\"0\"></a>";
            	echo "</td></tr>";
            											
            	$nRunningNumber++;
            }
        ?>
    </table>
    <table width="620">
		<tr>
			<td align="left">
				<?php 
                    if ($_REQUEST["start"] > $end)
                    {
                        $_REQUEST["start"] = $end;
                    }
                    
					$TEMPLATE_MNGT_SHOWRANGE = str_replace("_%From", $_REQUEST["start"], $TEMPLATE_MNGT_SHOWRANGE);
					$TEMPLATE_MNGT_SHOWRANGE = str_replace("_%To", $end, $TEMPLATE_MNGT_SHOWRANGE);
                    $TEMPLATE_MNGT_SHOWRANGE = str_replace("_%Off", $nListCount, $TEMPLATE_MNGT_SHOWRANGE);
					echo $TEMPLATE_MNGT_SHOWRANGE;?><br>
			</td>
			<td align="right">
				<?php
					if ($_REQUEST["start"] > 50)
					{
						?>
							<a href="showtemplates.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"]-50;?>&sortby=<?php echo $_REQUEST["sortby"];?>"><img src="../images/prev.png" height="10" width="10" alt="prev" border="0"> <?php echo $BTN_BACK;?></a>
						<?php
					}
					
					if ($end < $nListCount)
					{
						?>
							<a href="showtemplates.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"]+50;?>&sortby=<?php echo $_REQUEST["sortby"];?>"><?php echo $BTN_NEXT;?> <img src="../images/next.png" height="10" width="10" alt="next"  border="0"></a>
						<?php
					}
					
				?>
			</td>
		</tr>
	</table>
	
	<script type="text/javascript">
    	new Ajax.Request
		(
			"cronjob_check_substitute.php",
			{
				onSuccess : function(resp) 
				{
					
				},
		 		onFailure : function(resp) 
		 		{
		   			
		 		}
			}
		);
	</script>
</body>
</html>
