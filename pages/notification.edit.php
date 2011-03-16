<?php
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
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
	
	$strParams				= 'language='.$_REQUEST["language"];
	$strEncyrptedParams		= $objURL->encryptURL($strParams);
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">	
</head>
<body>
	
	<br>
	<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_NOTIFICATION;?>
	</span>
	<br><br>
	
	<form action="notification.send.php?key=<?php echo $strEncyrptedParams ?>" name="notification" method="post">

		<table style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3" width="585">
			<tr>
				<td class="table_header" colspan="2">
					<?php echo $NOTIFICATION_HEADER ?>:
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td valign="top" align="left" width="110">
					<?php echo $NOTIFICATION_SUBJECT ?>:
				</td>
				<td valign="top" align="left">
					<input id="IN_strSubject" name="IN_strSubject" type="text" class="InputText" style="width:450px;" value="<?php echo $TITLE_1.': '.$NOTIFICATION_DEFAULT_SUBJECT;?>">
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td valign="top" align="left">
					<?php echo $NOTIFICATION_CONTENT ?>:
				</td>
				<td valign="top" align="left">
					<textarea rows="10" cols="80" class="InputText" style="margin-right: 10px;" name="IN_strContent"></textarea>
				</td>
			</tr>
			<tr><td height="3"></td></tr>
			<tr>
				<td valign="top" align="left">
					<?php echo $NOTIFICATION_RECIEVER ?>:
				</td>
				<td valign="top" align="left">
					<table cellspacing="0" cellpadding="0">
						<tr>
							<td valign="top" align="left">
								<input type="radio" name="IN_nRecievers" value="1" checked>
							</td>
							<td valign="top" align="left">
								<?php echo $NOTIFICATION_RECIEVER_ALL ?>
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">
								<input type="radio" name="IN_nRecievers" value="2">
							</td>
							<td valign="top" align="left">
								<?php echo $NOTIFICATION_RECIEVER_SENDER ?>
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">
								<input type="radio" name="IN_nRecievers" value="3">
							</td>
							<td valign="top" align="left">
								<?php echo $NOTIFICATION_RECIEVER_ONLINE ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
		<table cellspacing="0" cellpadding="3" align="left" width="585">
		<tr>
			<td align="left">
				<input type="reset" class="Button" value="<?php echo $BTN_RESET;?>">
			</td>
			<td align="right">
				<input type="submit" value="<?php echo $BTN_SEND;?>" class="Button">
			</td>
		</tr>
		</table>
		<?php
			$strQuery 		= "SELECT strEMail from cf_user WHERE nID = '".$_REQUEST['userid']."' LIMIT 1;";
			$nResult 		= @mysql_query($strQuery);
			$arrResult 		= mysql_fetch_array($nResult, MYSQL_ASSOC);
			$strSenderEmail = $arrResult['strEMail'];
		?>
		<input type="hidden" name="strSenderEmail" value="<?php echo $strSenderEmail ?>">
	</form>

</body>
</html>