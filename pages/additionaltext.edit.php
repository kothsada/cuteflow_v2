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
	require_once '../config/db_connect.inc.php';
	require_once '../language_files/language.inc.php';
	require_once 'placeholder_tags_addtext.php';
	
	$AdditionalText = new Database_AdditionalText();
	
	if ($_REQUEST['write'])
	{
		if ($_REQUEST['additionalTextId']) $AdditionalText->getById($_REQUEST['additionalTextId']);
		
		$AdditionalText->title		= $_REQUEST['title'];
		$AdditionalText->content	= $_REQUEST['content'];
		$AdditionalText->save();
		
		if ($_REQUEST['isDefault']) $AdditionalText->setDefault($AdditionalText->id);
	}
	else
	{
		$AdditionalText->getById($_REQUEST['additionalTextId']);
		$id 		= $AdditionalText->id;
		$title		= $AdditionalText->title;
		$content	= $AdditionalText->content;
		$is_default	= $AdditionalText->is_default;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title><?php echo $CIRCULATION_EDIT_ADDITIONAL_TEXT ?></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script language="JavaScript">
	<!--
	var jsWrite = '<?php if ($_REQUEST['write']) echo 'true' ?>';
	
	function insertPlaceholder()
	{
		jsPlaceholder	= document.getElementById('placeholder').value;
		currentContent 	= document.getElementById('content').value;
		newContent 		= currentContent + jsPlaceholder;
		
		document.getElementById('content').value = newContent;
	}
	
	function validate()
	{
		jsTitle 	= document.getElementById('title').value;
		jsContent 	= document.getElementById('content').value;
		
		if ((jsTitle.length > 2) && (jsContent.length > 2)) return true;
		return false;
	}
	//-->
	</script>
</head>
<body topmargin="0" leftmargin="0" style="margin: 0px; padding: 5px;">

	<form method="post" action="<?php echo $PHPSELF ?>" onSubmit="return validate();">
		<table style="background: #efefef; border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<td colspan="2" class="table_header" style="border-bottom: 3px solid #ffa000;">
					<?php
					switch ($_REQUEST['action'])
					{
						case 'edit':
							echo $EDIT_ADDITIONAL_TEXT;
							break;
						case 'add':
							echo $ADD_ADDITIONAL_TEXT;
							break;
						default:
							echo $CIRCULATION_EDIT_ADDITIONAL_TEXT;
							break;
					}
					?>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top">
					
					<table cellspacing="0" cellpadding="0" width="630">
						<tr>
							<td width="90" align="left" valign="top" width="90" style="font-weight: bold; padding: 1px; padding-right: 5px;">
								<?php echo $CIRCORDER_NAME ?>:
							</td>
							<td width="250" align="left" valign="top" style="padding: 1px; padding-right: 5px;">
								<input type="text" class="InputText" name="title" id="title" value="<?php echo $title ?>">
							</td>
							<td width="10">&nbsp;</td>
							<td align="left" valign="bottom">
								<?php echo $PLACEHOLDERS ?>:
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="font-weight: bold; padding: 1px; padding-right: 5px;">
								<?php echo $ADDITIONAL_TEXT_CONTENT ?>:
							</td>
							<td align="left" valign="top" style="font-weight: bold; padding: 1px; padding-right: 5px;" width="250">
								<textarea class="InputText" name="content" id="content" cols="40" rows="5"><?php echo $content ?></textarea>
							</td>
							<td align="left" valign="top" style="padding: 2px; padding-right: 5px;">
								<img src="../images/grid_insert_row_style_2_16.gif" style="border: 1px solid #999; cursor: pointer;" onClick="insertPlaceholder();" title="use placeholder">
							</td>
							<td align="left" valign="top" style="padding: 1px;">
								<select id="placeholder" class="FormInput" size="6" style="width: 100%;">
			    					<?php
			    						
			    						if (sizeof($arrPlaceholdersAddText) > 0)
			    						{
			    							$nMax = sizeof($arrPlaceholdersAddText);
			    							for($nIndex = 0; $nIndex < $nMax; $nIndex++)
			    							{    							
			    								echo "<option value=\"".$arrPlaceholdersAddText[$nIndex]."\">".$arrPlaceholdersAddText[$nIndex]."</option>";
			    							}
			    						}
			    						else
			    						{
			    							echo "<option value=\"0\">No Placeholder available</option>";
			    						}   					
			                           
			    					?>
								</select>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" style="font-weight: bold; padding: 1px; padding-right: 5px;">
								<?php echo $DEFAULT ?>:
							</td>
							<td align="left" valign="top" style="font-weight: bold; padding: 1px; padding-right: 5px;">
								<input type="checkbox" name="isDefault" id="isDefault" value="1" <?php if ($is_default) echo 'checked' ?>>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<table cellspacing="0" cellpadding="0" align="center" width="620" style="margin-top: 5px;">
			<tr>
				<td align="left">
					<input type="button" value="<?php echo $BTN_CANCEL;?>" class="Button" onClick="window.close()">
				</td>
				<td align="right">
					<input type="submit" value="<?php echo $BTN_OK;?>" class="Button">
				</td>
			</tr>
		</table>
		
		<input type="hidden" name="additionalTextId" value="<?php echo $id ?>">
		<input type="hidden" name="write" value="1">
	</form>
	
	<script language="JavaScript">
	<!--
	if (jsWrite == 'true')
	{
		opener.reloadTimeout();
		window.close();
	}
	//-->
	</script>
</body>
</html>
