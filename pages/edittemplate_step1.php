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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script language="JavaScript">
	<!--
		function setProps()
		{
			var objForm = document.forms["EditTemplate"];
			
			objForm.strName.required = 1;
			objForm.strName.err = "<?php echo $TEMPLATE_NEW_ERROR_NAME;?>";
		}
		
		function validate(objForm)
		{
			bResult = jsVal(objForm);
			return bResult	;
		}
	-->
	</script>
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>	
</head>
<?php
	//--- load data from database
	$strTemplateName = '';
		
	if ($templateid != -1)
	{
		//--- open database
    	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
    	
    	if ($nConnection)
    	{
    		//--- get maximum count of users
    		if (mysql_select_db($DATABASE_DB, $nConnection))
    		{
    			//--- read the values of the user
				$strQuery = "SELECT * FROM cf_formtemplate WHERE nID = ".$_REQUEST["templateid"];
				$nResult = mysql_query($strQuery, $nConnection);
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
        				$arrRow = mysql_fetch_array($nResult);
        				$strTemplateName = $arrRow["strName"];
					}
				}
			}
		}
	}
?>
<body onload="setProps()"><br>
<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
	<?php echo $MENU_TEMPLATE;?>
</span><br><br>

	<form action="edittemplate_step2.php" id="EditTemplate" name="EditTemplate" onsubmit="return validate(this);">
		<table width="620" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3">
			<tr>
				<td class="table_header" colspan="2">
					<?php echo $TEMPLATE_EDIT1_HEADER ?> <?php if ($strTemplateName) echo '"'.$strTemplateName.'"' ?>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
            <tr>
				<td width="200"><?php echo $TEMPLATE_EDIT1_NAME;?></td>
				<td><input id="strName" Name="strName" type="text" class="InputText" style="width:150px;" value="<?php echo $strTemplateName ?>"></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
		<table cellspacing="0" cellpadding="3" align="left" width="620">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_CANCEL;?>" onclick="history.back()">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $BTN_NEXT;?>" class="Button">
			</td>
		</tr>
		</table>
		
	<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
	<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sort" name="sort">
	<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">
	<input type="hidden" value="<?php echo $_REQUEST["templateid"];?>" id="templateid" name="templateid">
	</form>

</body>
</html>