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
	
	require_once '../lib/swift/swift_required.php';
	
	include_once ("../config/config.inc.php");
	include ("../pages/version.inc.php");
	include_once ("../language_files/language.inc.php");
	
	
	function getNextUserInList($nCurUserId, $nMailingListId, $nSlotId)
	{
		global $DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD, $DATABASE_DB;
		
		$arrUserInfo = array();
		
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		$nConnection2 = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		
		if ( ($nConnection) && ($nConnection2) ) 
		{
			if (mysql_select_db($DATABASE_DB, $nConnection))
			{
				mysql_select_db($DATABASE_DB, $nConnection2);
				
				$query = "SELECT * FROM cf_formslot WHERE nID={$nSlotId}";
				$result = mysql_query($query, $nConnection);
				if ($result) {
					$arrSlotInfo = mysql_fetch_array($result);
				}
				
				$strQuery = "SELECT * FROM cf_slottouser INNER JOIN cf_formslot ON cf_slottouser.nSlotId  = cf_formslot.nID WHERE cf_slottouser.nMailingListId=$nMailingListId ORDER BY cf_formslot.nSlotNumber ASC, cf_slottouser.nPosition ASC";
				$nResult = mysql_query($strQuery, $nConnection);
				
        		if ($nResult)
        		{
        			if (mysql_num_rows($nResult) > 0)
        			{
						$bFoundOne == false;
        				while (	$arrRow = mysql_fetch_array($nResult))
        				{
        					if ($nCurUserId == -1)
							{
								//--- lets take the first user
								$arrUserInfo[0] = $arrRow["nUserId"];
								$arrUserInfo[1] = $arrRow["nSlotId"];
								
								return $arrUserInfo;
							}
							else if ($bFoundOne == true)
							{
								$arrUserInfo[0] = $arrRow["nUserId"];
								$arrUserInfo[1] = $arrRow["nSlotId"];
								
								// Slot has changed
								$arrUserInfo[2] = $nSlotId != $arrRow['nSlotId'] ? $arrRow['nSlotId'] : false; 
								
								return $arrUserInfo;
							}
							else
							{
								if ( ($arrRow["nUserId"] == $nCurUserId) && 
										($arrRow["nSlotId"] == $nSlotId))
								{
									$bFoundOne = true; //--- next loop returns user	
								}
							}
						}
					}
				}
			}
		}
		
		return $arrUserInfo;
	}

	function sendToUser($nUserId, $nCirculationId, $nSlotId, $nCirculationProcessId, $nCirculationHistoryId, $tsDateInProcessSince = '')
	{
		global $DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD, $DATABASE_DB, $MAIL_HEADER_PRE, $CUTEFLOW_SERVER;
		global $SMTP_SERVER, $SMTP_PORT, $SMTP_USERID, $SMTP_PWD, $SMTP_USE_AUTH;
		global $SYSTEM_REPLY_ADDRESS, $CUTEFLOW_VERSION, $TStoday, $objURL, $EMAIL_FORMAT, $EMAIL_VALUES, $MAIL_SEND_TYPE, $MTA_PATH, $SMPT_ENCRYPTION;
		
		global $CUTEFLOW_SERVER, $CUTEFLOW_VERSION, $EMAIL_BROWSERVIEW, $MAIL_LINK_DESCRIPTION, $MAIL_HEADER_PRE;
		global $CIRCULATION_DONE_MESSSAGE_REJECT, $CIRCULATION_DONE_MESSSAGE_SUCCESS, $CIRCDETAIL_SENDER, $CIRCDETAIL_SENDDATE, $MAIL_ADDITION_INFORMATIONS;
		
		global $DEFAULT_CHARSET, $SEND_WORKFLOW_MAIL;
		
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		if ($nConnection)
		{
			if (mysql_select_db($DATABASE_DB, $nConnection))
			{	
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
									->setCharset($DEFAULT_CHARSET);
	
				//------------------------------------------------------
				//--- get the needed informations
				//------------------------------------------------------
				
				//--- circulation form
				$arrForm = array();
				$strQuery = "SELECT * FROM cf_circulationform WHERE nID=$nCirculationId";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
	    			{
	    				$arrForm = mysql_fetch_array($nResult);
					}
				}
				
				//--- circulation history
				$arrHistory = array();
				$strQuery = "SELECT * FROM cf_circulationhistory WHERE nID=$nCirculationHistoryId";
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
	    			{
	    				$arrHistory = mysql_fetch_array($nResult);
					}
				}
				
				//--- the attachments
				$strQuery = "SELECT * FROM cf_attachment WHERE nCirculationHistoryId=$nCirculationHistoryId";
				$nResult = mysql_query($strQuery, $nConnection);
	    		if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
	    			{
	    				while (	$arrRow = mysql_fetch_array($nResult))
	    				{
							$strFileName = basename($arrRow['strPath']);
							$mimetype = new mimetype();
					      	$filemime = $mimetype->getType($strFileName);
							$message->attach(Swift_Attachment::fromPath(
													$arrRow["strPath"],
													$filemime)->setFilename($strFileName));
						}
					}
				}
				
				//------------------------------------------------------
				//--- update status in circulationprocess table
				//------------------------------------------------------				
				if ($tsDateInProcessSince == '')
				{
					$strQuery = "INSERT INTO cf_circulationprocess values (null, $nCirculationId, $nSlotId, $nUserId, $TStoday, 0, 0, $nCirculationProcessId, $nCirculationHistoryId, 0)";
					mysql_query($strQuery, $nConnection) or die ($strQuery.mysql_error());
				}
				else
				{
									//( `nID` , `nCirculationFormId` , `nSlotId`, `nUserId` , `dateInProcessSince` , `nDecissionState`, `dateDecission` , `nIsSubstitiuteOf` , `nCirculationHistoryId`)
					$strQuery = "INSERT INTO cf_circulationprocess values (null, $nCirculationId, $nSlotId, $nUserId, $tsDateInProcessSince, 0, 0, 0, $nCirculationHistoryId, 0)";
					mysql_query($strQuery, $nConnection) or die ($strQuery.mysql_error());
				}
				
				//------------------------------------------------------
				//--- generate email message
				//------------------------------------------------------	
				if ($SEND_WORKFLOW_MAIL == true) 
				{		
					$strQuery = "SELECT nID FROM cf_circulationprocess WHERE nSlotId=$nSlotId AND nUserId=$nUserId AND nCirculationFormId=$nCirculationId AND nCirculationHistoryId=$nCirculationHistoryId";
					$nResult = mysql_query($strQuery, $nConnection);
		    		if ($nResult)
		    		{
		    			if (mysql_num_rows($nResult) > 0)
		    			{
		    				$arrLastRow = array();
		    				
		    				while ($arrRow = mysql_fetch_array($nResult))
		    				{
		    					$arrLastRow = $arrRow;
		    				}
							$Circulation_cpid = $arrLastRow[0];
						}
					}				
					
					//switching Email Format
					if ($nUserId != -2)
					{	
						$strQuery = "SELECT * FROM `cf_user` WHERE nID = $nUserId;";
					}
					else
					{	// in this case the next user is the sender of this circulation
						$strQuery = "SELECT * FROM `cf_user` WHERE nID = ".$arrForm['nSenderId'].";";
					}
					$nResult = mysql_query($strQuery, $nConnection);
					if ($nResult)
		    		{
			    		$user						= mysql_fetch_array($nResult, MYSQL_ASSOC);
			    		
			    		$useGeneralEmailConfig		= $user['bUseGeneralEmailConfig'];
			    		
			    		if (!$useGeneralEmailConfig)
			    		{
				    		$emailFormat	= $user['strEmail_Format'];
				    		$emailValues	= $user['strEmail_Values'];
			    		}
			    		else
			    		{
				    		$emailFormat	= $EMAIL_FORMAT;
				    		$emailValues	= $EMAIL_VALUES;
			    		}
			    		
			    		$Circulation_Name			= $arrForm['strName'];
						$Circulation_AdditionalText = str_replace("\n", "<br>", $arrHistory['strAdditionalText']);
	    				
	    				//--- create mail
						require_once '../mail/mail_'.$emailFormat.$emailValues.'.inc.php';
	
						switch ($emailFormat)
						{
							case PLAIN:
								$message->setBody($strMessage, 'text/plain');
								break;
							case HTML:
								$message->setBody($strMessage, 'text/html');
								break;
		    			}		    		
		    		}				
					
					//------------------------------------------------------
					//--- send email to user
					//------------------------------------------------------
					if ($nUserId != -2)
					{
						$strQuery = "SELECT * FROM cf_user WHERE nID = $nUserId";
					}
					else
					{	// in this case the next user is the sender of this circulation
						$strQuery = "SELECT * FROM cf_user WHERE nID = ".$arrForm['nSenderId']."";
					}
					$nResult = mysql_query($strQuery, $nConnection);
	        		if ($nResult)
	        		{
	        			if (mysql_num_rows($nResult) > 0)
	        			{
							$arrRow = mysql_fetch_array($nResult);
							$SYSTEM_REPLY_ADDRESS = str_replace (' ', '_', $SYSTEM_REPLY_ADDRESS);
							
							$message->setFrom(array($SYSTEM_REPLY_ADDRESS=>'CuteFlow'));
							$message->setSubject($MAIL_HEADER_PRE.$arrForm["strName"]);
							
							$message->setTo(array($arrRow["strEMail"]));
							
							$result = $mailer->send($message);
							
							if (!$result)
							{
								$fp = @fopen ("mailerror.log", "a");
								if ($fp)
								{
									@fputs ($fp, date("d.m.Y", time())." - sendToUser\n");
									fclose($fp);
								}
							}
							else
							{
								return true;
							}
						}
					}
				}
			}
		}
		
		return false;
	}
	
	function sendMessageToSender($nSenderId, $nLastStationId, $strMessageFile, $strCirculationName, $strEndState, $Circulation_cpid, $slotname="")
	{
		global $DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD, $DATABASE_DB, $MAIL_HEADER_PRE, $CUTEFLOW_SERVER;
		global $SMTP_SERVER, $SMTP_PORT, $SMTP_USERID, $SMTP_PWD, $SMTP_USE_AUTH, $MAIL_ENDACTION_DONE_REJECT, $MAIL_ENDACTION_DONE_SUCCESS;
		global $SYSTEM_REPLY_ADDRESS, $CIRCULATION_DONE_MESSSAGE_REJECT, $CIRCULATION_DONE_MESSSAGE_SUCCESS, $CUTEFLOW_VERSION, $EMAIL_FORMAT;
		global $MAIL_SEND_TYPE, $MTA_PATH, $SMPT_ENCRYPTION, $CIRCULATION_SLOTEND_MESSSAGE_SUCCESS, $MAIL_ENDACTION_DONE_ENDSLOT;
		
		global $CUTEFLOW_SERVER, $CUTEFLOW_VERSION, $EMAIL_BROWSERVIEW, $MAIL_LINK_DESCRIPTION, $MAIL_HEADER_PRE;
		global $CIRCULATION_DONE_MESSSAGE_REJECT, $CIRCULATION_DONE_MESSSAGE_SUCCESS, $CIRCDETAIL_SENDER, $CIRCDETAIL_SENDDATE, $MAIL_ADDITION_INFORMATIONS, $objURL;
		
		global $DEFAULT_CHARSET;
		
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		if ($nConnection)
		{
			if (mysql_select_db($DATABASE_DB, $nConnection))
			{
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
				
				$mail_message = Swift_Message::newInstance()
									->setCharset($DEFAULT_CHARSET);
				
				//switching Email Format
				$strQuery	= "SELECT * FROM `cf_user` WHERE nID = $nSenderId;";
				$nResult	= mysql_query($strQuery, $nConnection) or die ("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $strQuery . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());

	    		$user					= mysql_fetch_array($nResult, MYSQL_ASSOC);
	    		
	    		$useGeneralEmailConfig	= $user['bUseGeneralEmailConfig'];
	    		
	    		if (!$useGeneralEmailConfig)
	    		{
		    		$emailFormat	= $user['strEmail_Format'];
	    		}
	    		else
	    		{
		    		$emailFormat	= $EMAIL_FORMAT;
	    		}
		    		   	
    			require_once '../mail/mail_'.$emailFormat.'_done.inc.php';					
	    					
				switch ($emailFormat)
				{
					case PLAIN:
						$mail_message->setBody($strMessage, 'text/plain');
						break;
					case HTML:
						$mail_message->setBody($strMessage, 'text/html');
						break;
    			}	
	    		
				$mail_message->setFrom(array($SYSTEM_REPLY_ADDRESS=>'CuteFlow'));
				eval ("\$strEndSubject = \"\$MAIL_ENDACTION_DONE_$strEndState\";");
				
				$mail_message->setSubject($MAIL_HEADER_PRE.$strCirculationName.
											$strEndSubject);
				
				$mail_message->setTo(array($user["strEMail"]));
				$result = $mailer->send($mail_message);
				if (!$result)
				{
					$fp = @fopen ("mailerror.log", "a");
					if ($fp)
					{
						@fputs ($fp, date("d.m.Y", time())." - sendToUser\n");
						fclose($fp);
					}
				}
				else
				{
					return true;
				}
			}
		}
	}
?>