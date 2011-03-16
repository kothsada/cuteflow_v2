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
	require_once 'send_circulation.php';
?>
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
<?php
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			//-----------------------------------------------
			//--- get current user data
			//-----------------------------------------------
			$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrProcessInfo = mysql_fetch_array($nResult);
				}
			}
			
			$strQuery = "SELECT * FROM cf_mailinglist INNER JOIN cf_circulationform ON cf_mailinglist.nID = cf_circulationform.nMailingListId WHERE cf_circulationform.nID=".$arrProcessInfo["nCirculationFormId"];
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrRow = mysql_fetch_array($nResult);
					
					$nListId = $arrRow[0];
				}
			}

			//-----------------------------------------------
			//--- remove user entry in process information
			//--- cause send_mail() adds a new one after
			//--- sending the mail 
			//-----------------------------------------------
			$strQuery = "DELETE FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
			mysql_query($strQuery, $nConnection);
			
			
			//-----------------------------------------------
			//--- send mail to next user in mailing list
			//-----------------------------------------------
			sendToUser($arrProcessInfo["nUserId"], $arrProcessInfo["nCirculationFormId"], $arrProcessInfo["nSlotId"], $arrProcessInfo["nIsSubstitiuteOf"], $arrProcessInfo["nCirculationHistoryId"]);
		}
	}
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<script language="JavaScript">
	<!--
		function siteLoaded()
		{
			var strParams	= "circid=<?php echo $_REQUEST["circid"];?>&language=<?php echo $_REQUEST["language"];?>&sort=<?php echo $_REQUEST["sort"];?>&start=<?php echo $_REQUEST["start"];?>";
			inpdata	= strParams;
			encodeblowfish();
			location.href = "circulation_detail.php?key=" + outdata;
		}
	//-->
	</script>
</head>
<body onLoad="siteLoaded()">
</body>
</html>