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
	
	//--- first save the last step
	//--- open database
	$templateid = $_REQUEST["templateid"];
	
   	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
   	if ($nConnection)
   	{
   		//--- get maximum count of users
   		if (mysql_select_db($DATABASE_DB, $nConnection))
   		{
			if (!isset($_REQUEST["reload"]))
			{
				if ($_REQUEST["templateid"] != -1)
				{
					$strQuery = "UPDATE cf_formtemplate SET strName='".$_REQUEST["strName"]."' WHERE nID=".$_REQUEST["templateid"];
					mysql_query($strQuery, $nConnection);
				}
				else
				{
					$strQuery = "INSERT INTO cf_formtemplate VALUES(null, '".$_REQUEST["strName"]."', 0)";
					mysql_query($strQuery, $nConnection);
					
					//--- get the new template id
					$strQuery = "SELECT MAX(nID) FROM cf_formtemplate";
					$nResult = mysql_query($strQuery, $nConnection);
						
					if ($nResult)
		    		{
		    			if (mysql_num_rows($nResult) > 0)
	    				{
	    					$arrRow = mysql_fetch_array($nResult);
							$templateid = $arrRow[0];
						}
					}
				}
			}
			
			//--- get the needed Data for step 2
			$strQuery = "SELECT * FROM cf_formslot WHERE nTemplateid = ".$_REQUEST["templateid"]." ORDER BY nSlotNumber ASC";
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
		}
	}
	
	//--- than show the mask for step 2		
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script language="JavaScript">
	<!--
		function deleteSlot(nSlotId)
		{
			Check = confirm("<?php echo $TEMPLATE_EDIT2_ASKDELETE;?>");
			if(Check == true) 
			{
				location.href="deleteslot.php?slotid="+nSlotId+"&templateid=<?php echo $templateid;?>&language=<?php echo $_REQUEST["language"]?>&start=<?php echo $_REQUEST["start"]?>";
			}
		}
		
		function back()
		{
			location.href="edittemplate_step1.php?templateid=<?php echo $templateid;?>&language=<?php echo $_REQUEST["language"]?>&start=<?php echo $_REQUEST["start"]?>";
		}
	//-->
	</script>
	
	<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript" SRC="tooltip.js"></SCRIPT>
	<script type="text/javascript">
	   	maketip('delete','<?php echo escapeSingle($TEMPLATE_EDIT2_TIP_DELETE);?>');
	   	maketip('detail','<?php echo escapeSingle($TEMPLATE_EDIT2_TIP_DETAIL);?>');
		maketip('up','<?php echo escapeSingle($TEMPLATE_EDIT2_TIP_UP);?>');
		maketip('down','<?php echo escapeSingle($TEMPLATE_EDIT2_TIP_DOWN);?>');
		
		function validate(objForm)
		{
			//--- does nothing at the moment
			return true;
		}
	</script>
</head>
<body><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_TEMPLATE;?>
</span><br><br>
	
	<form action="edittemplate_step3.php" id="EditTemplate" name="EditTemplate" onsubmit="return validate(this);">
		<table width="620" cellspacing="0" cellpadding="3">
    		<tr>
    			<td align="left">
    				<a href="editslot.php?language=<?php echo $_REQUEST["language"];?>&templateid=<?php echo $templateid;?>&slotid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><img src="../images/addtemplate.png" border="0"></a>
    				[ <a href="editslot.php?language=<?php echo $_REQUEST["language"];?>&templateid=<?php echo $templateid;?>&slotid=-1&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>"><?php echo $TEMPLATE_EDIT2_NEWSLOT;?></a> ]
    			</td>
    		</tr>
			<tr>
				<td>
					<br><?php echo $TEMPLATE_EDIT2_HEADER;?> <?php if ($_REQUEST["strName"]) echo '"'.$_REQUEST["strName"].'"' ?>
				</td>
			</tr>
    	</table>
		
		<table width="620" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3">
			<tr>
				<td class="table_header" width="10">#</td>
                <td align="left" class="table_header"><a style="color:White" href="" onclick="this.blur(); return sortTable('tblBdy',  1, true);"><?php echo $TEMPLATE_EDIT2_HEADER_NAME;?></a></td>
                <td class="table_header" align="right"><?php echo $TABLE_OPTIONS;?></td>
            </tr>

			<?php 
				$nRunningNumber = 1;
				
				if ( isset($arrSlots) )
				{
					foreach ($arrSlots as $arrCurSlot)
					{
						$style = "background-color: #efefef;";
						if ($nRunningNumber%2 == 1)
						{
							$style = "background-color: #fff;";
						}
						
						echo "<tr valign=\"top\" style=\"$style\">";
	                	
						echo "<td nowrap>".$nRunningNumber."</td>";
						
						echo "<td>".$arrCurSlot["strName"]."</td>";
											
						echo "<td width=\"100px\" align=\"right\">";
						echo "<a href=\"javascript:deleteSlot($arrCurSlot[0])\" alt=\"L�schen\" onMouseOver=\"tip('delete')\" onMouseOut=\"untip()\"><img src=\"../images/edit_remove.gif\" border=\"0\"height=\"16\" width=\"16\" style=\"margin-right: 4px;\"></a>";
            			echo "<a href=\"editslot.php?slotid=$arrCurSlot[0]&templateid=".$templateid."&language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\" onMouseOver=\"tip('detail')\" onMouseOut=\"untip()\" alt=\"Anzeigen\"><img src=\"../images/act_view.gif\" border=\"0\"height=\"16\" width=\"16\"></a>";
            			echo "<a href=\"slotup.php?slotid=$arrCurSlot[0]&templateid=".$templateid."&language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\" onMouseOver=\"tip('up')\" onMouseOut=\"untip()\"><img src=\"../images/up.gif\" border=\"0\" height=\"16\" width=\"16\"></a>";
						echo "<a href=\"slotdown.php?slotid=$arrCurSlot[0]&templateid=".$templateid."&language=".$_REQUEST["language"]."&sortby=".$_REQUEST["sortby"]."&start=".$_REQUEST["start"]."\" onMouseOver=\"tip('down')\" onMouseOut=\"untip()\"><img src=\"../images/down.gif\" border=\"0\" height=\"16\" width=\"16\"></a>";
						echo "</td></tr>";
						
						$nRunningNumber++;
					}
				}
			?>
		</table>
		
		<table cellspacing="0" cellpadding="3" align="left" width="620">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_BACK;?>" onclick="back();">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $BTN_NEXT;?>" class="Button">
			</td>
		</tr>
		</table>
		
	<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
	<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sort" name="sort">
	<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">
	<input type="hidden" value="<?php echo $templateid;?>" id="templateid" name="templateid">
	<input type="hidden" value="<?php echo $_REQUEST["strName"] ?>" id="strName" name="strName">
    </form>

</body>
</html>