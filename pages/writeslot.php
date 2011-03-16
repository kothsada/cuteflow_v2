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
	
	//--- write user to database
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			$nSendType = $_REQUEST['nSendType'] == "on" ? 1 : 0;
				
			if ($_REQUEST["slotid"] == -1)
			{
				//--- add new slot
				$strQuery = "SELECT MAX(nSlotNumber) FROM cf_formslot where nTemplateId=".$_REQUEST["templateid"];
				$nResult = mysql_query($strQuery, $nConnection);
					
				if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
    				{
    					$arrRow = mysql_fetch_array($nResult);
						$nMaxSlotNumber = $arrRow[0];
					}
				}
				
				$query = "INSERT INTO cf_formslot values (null, \"".$_REQUEST["strName"]."\", ".$_REQUEST["templateid"].", ".($nMaxSlotNumber+1).", ".$nSendType.")";
			}
			else
			{
				//--- update existing slot
				$query = "UPDATE cf_formslot SET strName=\"".$_REQUEST["strName"]."\", nSendType=".$nSendType." WHERE nID=".$_REQUEST["slotid"];
			}
			
			$nResult = mysql_query($query, $nConnection);
		}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	
	<script language="JavaScript">
		function onLoad()
		{
			document.location.href="edittemplate_step2.php?language=<?php echo $_REQUEST["language"];?>&start=<?php echo $_REQUEST["start"];?>&sortby=<?php echo $_REQUEST["sortby"];?>&templateid=<?php echo $_REQUEST["templateid"];?>&reload=1";
		}
	</script>
</head>
<body onLoad="onLoad()">

</body>
