<?php
	/** Copyright (c) 2003, 2004 EMEDIA OFFICE GmbH. All rights reserved.
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
	*  o Neither the name of EMEDIA OFFICE GmbH nor the names of 
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
	<script src="jsval.js" type="text/javascript" language="JavaScript"></script>	
</head>
<?php
	//--- load data from database
	//--- open database
   	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
   	
   	if ($nConnection)
   	{
   		//--- get maximum count of users
   		if (mysql_select_db($DATABASE_DB, $nConnection))
   		{
			$query = "SELECT * FROM cf_circulationform WHERE nID=".$_REQUEST["circid"];
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
	        
	        $query = "SELECT * FROM cf_mailinglist WHERE nID=".$arrCirculationData["nMailingListId"];
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
	        
	        $query = "SELECT MAX(nRevisionNumber) FROM cf_circulationhistory WHERE nCirculationFormId=".$_REQUEST["circid"];
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
	        
	        $query = "SELECT * FROM cf_circulationhistory WHERE nRevisionNumber=".$nMaxRevision." AND nCirculationFormId=".$_REQUEST["circid"];
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
?>
<body>
	<br/>
	<br/>
	<div align="center">
		<form ENCTYPE="multipart/form-data" METHOD="POST" action="restart_circulation_write.php" id="EditCirculation" name="EditCirculation">
    		<table class="note">
    			<tr>
    				<td colspan="2" bgcolor="Red" align="left" style="font-weight:bold;color:White;">
						<?php echo $CIRCULATION_RESTART_FORM_HEADER;?>
					</td>
    			</tr>
                <tr valign="top">
    				<td class="mandatory"><?php echo $CIRCULATION_EDIT_NAME;?></td>
    				<td><input id="strName" readonly Name="strName" type="text" class="FormInput" style="background-color:#F7F7F7;width:150px;" value="<?php echo $arrCirculationData["strName"];?>"></td>
    			</tr>
             	<tr>
					<td colspan="2" height="10px"></td>
				</tr>
				<tr valign="top">
    				<td class="mandatory"><?php echo $CIRCULATION_EDIT_MAILINGLIST;?></td>
    				<td>
						<div id="MailingListName" style="background-color:#F7F7F7; border:1px solid #B8B8B8;width:200px;"><?php echo $strMailingListName;?></div>
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
    				<td>
						<textarea cols="50" rows="5" name="strAdditionalText" class="FormInput"><?php echo $arrHistoryData["strAdditionalText"];?></textarea>
					</td>
    			</tr>
				<tr>
					<td>&nbsp;</td>
    				<td>
						<input type="checkbox" name="SuccessMail" id="SuccessMail" checked><?php echo $CIRCULATION_EDIT_SUCCESS_MAIL;?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
    				<td>
						<input type="checkbox" name="SuccessArchive" id="SuccessArchive" checked><?php echo $CIRCULATION_EDIT_SUCCESS_ARCHIVE;?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="checkbox" name="SuccessDelete" id="SuccessDelete"><?php echo $CIRCULATION_EDIT_SUCCESS_DELETE ?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
    				<td>
						<input type="checkbox" name="EditValues" id="EditValues" value="1"><?php echo $EDIT_CIRCULATION_VALUES_OPTION;?>
					</td>
				</tr>
    			<tr>
    				<td colspan="2" style="border-top: 1px solid #B8B8B8;padding: 6px 0px 4px 0px;" align="right">
						<input type="button" class="Button" value="<?php echo $BTN_CANCEL;?>" onclick="history.back()">&nbsp;&nbsp;<input type="submit" value="<?php echo $BTN_OK;?>" class="Button">
					</td>
    			</tr>
    		</table>
			<input type="hidden" value="<?php echo $arrCirculationData["nMailingListId"];?>" id="listid" name="listid">
			<input type="hidden" value="<?php echo $_REQUEST["language"];?>" id="language" name="language">
			<input type="hidden" value="<?php echo $_REQUEST["sort"];?>" id="sortby" name="sortby">
			<input type="hidden" value="<?php echo $_REQUEST["start"];?>" id="start" name="start">
			<input type="hidden" value="<?php echo $_REQUEST["circid"]?>" name="circid">
		</form>
	</div>
</body>
</html>