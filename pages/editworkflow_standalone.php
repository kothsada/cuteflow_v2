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
require_once '../lib/viewutils.inc.php';

if (!$ALLOW_UNENCRYPTED_REQUEST) {
	// clear $_REQUEST to ensure that only the encryptet "key" is used
	foreach ($_GET as $key => $value) {
		if($key != 'key') {
			$_REQUEST[$key]		= '';
		}
	}
}

$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
if ($nConnection)
{
	if (mysql_select_db($DATABASE_DB, $nConnection))
	{
		if ($_REQUEST['cpid'] != '')
		{
			//-----------------------------------------------
			//--- get the user information from 
			//--- cf_circulationprocess
			//-----------------------------------------------
			$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
			$nResult = mysql_query($strQuery, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationProcess = mysql_fetch_array($nResult);				
				}
			}
			//-----------------------------------------------
			//--- get the single circulation form
			//-----------------------------------------------
			$query = "select * from cf_circulationform WHERE nID=".$arrCirculationProcess["nCirculationFormId"];
			$nResult = mysql_query($query, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationForm = mysql_fetch_array($nResult);				
				}
			}
		}
		elseif ($_REQUEST['cfid'] != '')
		{
			//-----------------------------------------------
			//--- get the single circulation form
			//-----------------------------------------------
			$query = "select * from cf_circulationform WHERE nID=".$_REQUEST['cfid'];
			$nResult = mysql_query($query, $nConnection);
			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					$arrCirculationForm = mysql_fetch_array($nResult);				
				}
			}
			
			$strQuery = "SELECT nID FROM `cf_circulationprocess` WHERE nCirculationFormId = '".$_REQUEST['cfid']."' AND ( nDecissionState = '0' OR nDecissionState = '2' OR nDecissionState = '16')";
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
					$_REQUEST['cpid'] = $Circulation_cpid;
				}
			}
		}
		
		$query = "select * from cf_circulationhistory WHERE nCirculationFormId=".$arrCirculationForm['nID'];
		$nResult = mysql_query($query, $nConnection);
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$arrCirculationHistory = mysql_fetch_array($nResult);				
			}
		}
		
		//-----------------------------------------------
    	//--- get all users
        //-----------------------------------------------
        $arrUsers = array();
    	$strQuery = "SELECT * FROM cf_user  WHERE bDeleted <> 1";
    	$nResult = mysql_query($strQuery, $nConnection);
    	if ($nResult)
    	{
    		if (mysql_num_rows($nResult) > 0)
    		{
    			while (	$arrRow = mysql_fetch_array($nResult))
    			{
    				$arrUsers[$arrRow["nID"]] = $arrRow;
    			}
    		}
    	}
		
		//-----------------------------------------------
		//--- get the mailing list
		//-----------------------------------------------
		$query = "select * from cf_mailinglist WHERE nID=".$arrCirculationForm["nMailingListId"];
		$nResult = mysql_query($query, $nConnection);
		if ($nResult) {
			if (mysql_num_rows($nResult) > 0) {
				$arrMailingList = mysql_fetch_array($nResult);
			}
		}
		$nMailingListID = $arrMailingList['nID'];
			
        //-----------------------------------------------
        //--- get the template
        //-----------------------------------------------	            
        $strQuery = "SELECT * FROM cf_formtemplate WHERE nID=".$arrMailingList["nTemplateId"];
    	$nResult = mysql_query($strQuery, $nConnection);
    	if ($nResult)
    	{
    		if (mysql_num_rows($nResult) > 0)
    		{
    			$arrTemplate = mysql_fetch_array($nResult);
   				$strTemplateName = $arrTemplate["strName"];
    		}
    	}
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="format.css" type="text/css">
	
	<link rel="stylesheet" href="../lib/extjs/css/ext-all.css" type="text/css">
	<script src="../lib/extjs/ext-base.js" type="text/javascript"></script>
	<script src="../lib/extjs/ext-all.js" type="text/javascript"></script>
	<script src="../lib/extjs/miframe.js" type="text/javascript"></script>
	
	<style type="text/css">
        html, body {
            font: normal 12px verdana;
            margin: 0;
            padding: 0;
            border: 0 none;
        }
    </style>
    
    <script type="text/javascript">
       
        Ext.onReady(function() {
        	var viewport = new Ext.Viewport({
                layout:'border',
                margins: '5 5 5 5',
                items: [{
                    region:'north',
                    xtype: 'panel',
                    title: '<?php echo $arrCirculationForm['strName'];?>',
                    split:true,
                    contentEl: 'header',
                    height: 'auto',
                    minHeight: 100,
                    layout:'fit',
                    margins: '5 5 5 5'
                    
               }, {
                    region:'center',
                    margins: '0 5 5 5',
                    xtype: 'iframepanel',
                    layout: 'fit', 
                    defaultSrc: '../mail/mail_content.php?key=<?php echo $_REQUEST['key'];?>'
               }]
        	});
        });
   </script>
</head>
<body>
	<div align="center" id="header">
		<table width="100%" cellspacing="0" cellpadding="3" >
			<tr>
				<td align="left" colspan="2" style="padding-lefrt: 5px">
					<table border="0" width="100%" cellpadding="0" cellspacing="0">
					    <tr style="height:22px;">
					        <td width="20px" align="left"><img src="../images/template_type.gif" height="16" width="16"></td>
					        <td width="150px" align="left"><?php echo $CIRCDETAIL_TEMPLATE_TYPE;?></td>
					        <td align="left"><?php echo $strTemplateName;?></td>
					    </tr>
					    <tr style="height:22px;">
					        <td width="20px" align="left"><img src="../images/singleuser2.gif" height="16" width="16"></td>
					        <td width="150px" align="left"><?php echo $CIRCDETAIL_SENDER;?></td>
					        <td align="left">
					        <?php
					            echo $arrUsers[$arrCirculationForm["nSenderId"]]["strLastName"].", ".$arrUsers[$arrCirculationForm["nSenderId"]]["strFirstName"]." (".$arrUsers[$arrCirculationForm["nSenderId"]]["strUserId"].")";
					        ?>
					        </td>
					    </tr>
					    <tr style="height:22px; padding-top: 4px;">
						 	<td align="left" style="padding-top: 4px;" width="20px" valign="top"><img src="../images/description.gif" height="16" width="16" ></td>
					        <td align="left" style="padding-top: 4px;" width="150px" valign="top"><?php echo $CIRCDETAIL_DESCRIPTION;?></td>
					        <td align="left" style="padding-top: 4px;" valign="top"><?php echo str_replace("\n", "<br>", $arrCirculationHistory["strAdditionalText"]);?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>	
				<td>
					<?php
					$strQuery = "SELECT * FROM cf_attachment WHERE  nCirculationHistoryId=".$arrCirculationHistory["nID"];
		    		$nResult = mysql_query($strQuery, $nConnection);
		    		if ($nResult)
		    		{
		    			if (mysql_num_rows($nResult) > 0)
		    			{
							?>
							<table border="0" width="100%" cellpadding="0" cellspacing="0" class="BorderSilver">
							    <tr>
							        <td colspan="5" align="left">
							            <table bgcolor="Silver" width="100%">
							                <tr>
							                    <td width="20px" align="left"><img src="../images/attach.png" height="16" width="16"></td>
							                    <td style="font-weight:bold;" align="left"><?php echo $CIRCDETAIL_ATTACHMENT;?></td>
							                </tr>
							            </table>
							        </td>
							    </tr>
							    <?php					    
				                    $nRunningNumber = 1;
				                    echo "<tr>\n";
									while (	$arrRow = mysql_fetch_array($nResult))
				    				{
				                        echo "<td align=\"left\">\n";
										echo "<table>\n<tr>\n";
				    					echo "<td align=\"left\" style=\"height:22px;\" width=\"20px\"><img src=\"../images/document.png\" height=\"16\" width=\"16\"></td>\n";
				                        echo "<td align=\"left\" style=\"height:22px;\"><a target=\"_blank\" href=\"".$arrRow["strPath"]."\">".getFileNameFromPath($arrRow["strPath"])."</td>\n";
				                    	echo "</tr>\n</table\n";
										echo "</td>\n";
										
				                        if ($nRunningNumber % 2 == 0)
				                        {
				                            echo "</tr>\n<tr>";
				                        }
				                        else
				                        {
				                            echo "<td style=\"height:22px;\" width=\"10px\">&nbsp;</td>\n";
				                        }
				                        
				                        $nRunningNumber++;
				        			}
									echo "</tr>\n";
							    ?>
							</table><br>
							<?php
		    			}
		    		}	
		    		?>
				</td>
			</tr>			
		</table>
	</div>
</body>
</html>