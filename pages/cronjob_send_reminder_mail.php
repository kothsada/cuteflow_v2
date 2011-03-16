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
    $_REQUEST['language'] = $DEFAULT_LANGUAGE;
    
    require_once '../lib/swift/swift_required.php';
    
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	
	require_once 'CCirculation.inc.php';
	
	$objCirculation = new CCirculation();
	
	// get all active users
	$query = "SELECT * FROM cf_user WHERE bDeleted=0";
	$user_result 	= mysql_query($query, $nConnection);
	
	while (	$user = mysql_fetch_array($user_result)) {
		// send reminder email to user
		// first: getting all open circulations of this user
		$circulations_aggregated = array();

		$circulations = $objCirculation->getCirculationOverview(1, 
									'COL_CIRCULATION_PROCESS_DAYS', 
									'DESC',
									false,
									100,
									true,
									'',
									false,
									false,
									$user['nID']
									);

		foreach ($circulations as $circulation) {
			$arrDecissionState 	= $objCirculation->getDecissionState($circulation['nID']);
			$strStartDate		= $objCirculation->getStartDate($circulation['nID']);
			$strSender			= $objCirculation->getSender($circulation['nID']);

			$single_circulations_aggregated['circulation_name'] = $circulation['strName'];
			$single_circulations_aggregated['circulation_start'] = $strStartDate;
			$single_circulations_aggregated['circulation_sender'] = $strSender;
			$single_circulations_aggregated['circulation_name'] = $circulation['strName'];
			$single_circulations_aggregated['circulation_process_time'] = $arrDecissionState["nDaysInProgress"];
			$single_circulations_aggregated['circulation_url'] = '';
			
			$circulations_aggregated[] = $single_circulations_aggregated;
		}
		
		if (count($circulations_aggregated) > 0) {
			// second: create email message body
			$useGeneralEmailConfig	= $user['bUseGeneralEmailConfig'];
	    		
	    	if (!$useGeneralEmailConfig) {
	    		$emailFormat	= $user['strEmail_Format'];
    		}
    		else {
	    		$emailFormat	= $EMAIL_FORMAT;
    		}
		    		   	
    		require '../mail/mail_'.$emailFormat.'_reminder.inc.php';
			
			// third and last: sending the mail
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
			$mail_message->setSubject($REMINDER_MAIL_SUBJECT);
			
			$mail_message->setTo(array($user["strEMail"]));
			$result = $mailer->send($mail_message);
			
		}
	}
	