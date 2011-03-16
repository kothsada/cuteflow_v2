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
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../lib/swift/swift_required.php';
	
	$strSubject = $_POST['IN_strSubject'];
	$strContent = $_POST['IN_strContent'];
	$nReciever 	= $_POST['IN_nRecievers'];
	
	$strSender = $_REQUEST['strSenderEmail'];
	
	// get the recievers
	$strQuery = "SELECT strEMail FROM cf_user";
	
	switch($nReciever)
	{
		case 2:
			$strQuery .= " WHERE nAccessLevel = '8' OR nAccessLevel = '2' and bDeleted = 0";
			break;
		case 3:
			$strQuery .= " WHERE tsLastAction > '".(time() - $USER_TIMEOUT)."' and bDeleted = 0";
			break;
	}
	
	$nResult = mysql_query($strQuery, $nConnection) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $strQuery . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	
	if ($nResult)
	{
		while ($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
		{
			$arrRecievers[] = $arrRow['strEMail'];
		}
	}	
	
	// send the EMail	
	
	// Create the Transport
	if ($MAIL_SEND_TYPE == 'SMTP') {
		$transport = Swift_SmtpTransport::newInstance($SMTP_SERVER, $SMTP_PORT)
  					->setUsername($SMTP_USERID)
  					->setPassword($SMTP_PWD);
  					
		if ($SMPT_ENCRYPTION != 'NONE') {
  			$transport = $transport->setEncryption(strtolower($SMPT_ENCRYPTION));
  		}
	}
	else if ($MAIL_SEND_TYPE == 'PHP') {
		$transport = Swift_MailTransport::newInstance();
	}
	else if ($MAIL_SEND_TYPE == 'MTA') {
		$transport = Swift_SendmailTransport::newInstance($MTA_PATH);
	}
  					
  	// Create the Mailer using the created Transport
	$mailer = Swift_Mailer::newInstance($transport);
	$message = Swift_Message::newInstance()
						->setSubject($strSubject)
						->setFrom(array($strSender => 'CuteFlow'))
						->setBody($strContent)
						->setTo($arrRecievers);
	
	// Send the message
	$result = $mailer->batchSend($message, $failures);
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
	<?php 
	if ($result)
	{
		echo $NOTIFICATION_SUCCESS;
	}
	else
	{
		echo $NOTIFICATION_ERROR;
	}
	?>


</body>
</html>