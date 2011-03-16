<?php
	session_start();
	
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/ldap_common.php';
	
	//Get LDAP Values 
	$LDAP = getLDAP('../config/');
	
	$nColsRunningNumber = 0;
	$nAllColsRunningNumber = 0;
	$nColsMax = sizeof($ARR_COL_SPLIT);
	for ($nColsIndex = 0; $nColsIndex < $nColsMax; $nColsIndex++)
	{
		$strCurCol 	= $ARR_COL_SPLIT[$nColsIndex];
		$nColsIndex = $nColsIndex + 1;
		$bActive 	= $ARR_COL_SPLIT[$nColsIndex];
		
		switch($strCurCol)
		{
			case 'NAME':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']		= 'COL_CIRCULATION_NAME';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_NAME;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_NAME';
				}
				break;
			case 'STATION':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_STATION';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_STATION;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_STATION';
				}
				break;
			case 'DAYS':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_PROCESS_DAYS';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_DAYS;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_PROCESS_DAYS';
				}
				break;
			case 'START':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_PROCESS_START';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_START;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_PROCESS_START';
				}
				break;
			case 'SENDER':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_SENDER';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $CIRCORDER_SENDER;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_SENDER';
				}
				break;
			case 'MAILLIST':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_MAILLIST';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_MAILLIST;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_MAILLIST';
				}
				break;
			case 'TEMPLATE':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_TEMPLATE';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_TEMPLATE;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_TEMPLATE';
				}
				break;
			case 'WHOLETIME':
				$arrCONF_AllCols[$nAllColsRunningNumber]['strTitle']	= 'COL_CIRCULATION_WHOLETIME';
				$arrCONF_AllCols[$nAllColsRunningNumber]['strScreenTitle']	= $SHOW_CIRCULATION_WHOLETIME;
				if ($bActive)
				{
					$arrCirculation_Cols[] 		= 'COL_CIRCULATION_WHOLETIME';
				}
				break;
		}
		if ($bActive)
		{				
			$nColsRunningNumber++;
		}
		$arrCONF_AllCols[$nAllColsRunningNumber]['bActive']	= $bActive;
		$nAllColsRunningNumber++;
	}
	$AdditionalText 	= new Database_AdditionalText();
	$additionalTexts	= $AdditionalText->getByParams();
	
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
    	
	$fields = array(); 
    if ($nConnection) {
    	//--- get maximum count of users
    	if (mysql_select_db($DATABASE_DB, $nConnection)) {
    		//--- read the values of the user
			$strQuery = "SELECT * FROM cf_inputfield";
			$nResult = mysql_query($strQuery, $nConnection);
        
        	if ($nResult) {
        		if (mysql_num_rows($nResult) > 0) {
        			while (	$arrRow = mysql_fetch_array($nResult)) {
        				$fields[] = $arrRow;
        			}
        		}
        	}
    	}
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
   	<link rel="stylesheet" href="format.css" type="text/css">
   	<script src="../lib/prototype.js" type="text/javascript"></script>
  	<script src="../src/scriptaculous.js" type="text/javascript"></script>
  	<script src="../src/unittest.js" type="text/javascript"></script>
   	<script language="javascript">
	function checkValue()
	{
		var tempIndex, choiceVal, tempIndex2, choiceVal2
		tempIndex = document.config_form.strIN_Email_Value.selectedIndex
		choiceVal = document.config_form.strIN_Email_Value.options[tempIndex].text
		tempIndex2 = document.config_form.strIN_Email_Format.selectedIndex
		choiceVal2 = document.config_form.strIN_Email_Format[tempIndex2].text
		
		if ((tempIndex==2)&&(tempIndex2==0))
		{
			if ((choiceVal=="IFrame")&&(choiceVal2=="Text"))
			{
				window.alert("'IFrames' only works with HTML!");
				document.config_form.strIN_Email_Value[2].selected = false;
				document.config_form.strIN_Email_Value[0].selected = true;
			}
		}		
	}

	function mailChanged() {
		var mail_send_type = document.getElementById('mail_send_type');

		document.getElementById('smtp_options').style.display = 'none';
		document.getElementById('mta_options').style.display = 'none';

		if (mail_send_type.value == "SMTP") {
			document.getElementById('smtp_options').style.display = 'block';
		}
		else if (mail_send_type.value == "MTA") {
			document.getElementById('mta_options').style.display = 'block';	
		}
	}
	
	var arrSettings = new Array('database', 'server', 'gui', 'auth', 'workflow');
		
	function showSettings(strSettings)
	{
		nMax = arrSettings.length;
		for (nIndex = 0; nIndex < nMax; nIndex++)
		{
			strCurSettings = arrSettings[nIndex];
			strDiv = strCurSettings + '_table';
			
			if (strCurSettings == strSettings)
			{
				document.getElementById(strDiv).style.display = 'block';
				document.getElementById(strCurSettings).style.background = '#8e8f90';
				document.getElementById(strCurSettings).style.cursor = 'auto';
			}
			else
			{
				document.getElementById(strDiv).style.display = 'none';
				document.getElementById(strCurSettings).style.background = '#bbb';
				document.getElementById(strCurSettings).style.cursor = 'pointer';
			}
		}
	}
	
	function changeStyle(strTd, strAction)
	{
		strDiv = strTd + '_table';
		objDiv = document.getElementById(strDiv).style.display;
		
		if (objDiv == 'none')
		{
			objTd = document.getElementById(strTd);
			switch(strAction)
			{
				case 'over':
					objTd.style.background = '#ffc056';
					objTd.style.cursor = 'pointer';
					break;
				case 'out':
					objTd.style.background = '#bbb';
					break;
			}
		}
	}

	
	function editAdditionalText(jsAdditionalTextId, jsAction)
	{
		window.open('additionaltext.edit.php?language=<?php echo $_REQUEST['language'] ?>&action=' + jsAction + '&additionalTextId=' + jsAdditionalTextId,
					'BrowseAdditionalText',
					'width=650, height=230, resizable=yes, scrollbars=yes, status=yes, toolbar=no, menubar=no, location=no'
					);
	}
	
	function deleteAdditionalText(jsAdditionalTextId)
	{
		new Ajax.Request
		(
			'ajax_getadditionaltext.php',
			{
				onSuccess : function(resp) 
				{
					var response = resp.responseText;
					document.getElementById('divListAdditionalText').innerHTML = response;
					document.getElementById('divListAdditionalText').style.display = 'block';
				},
		 		onFailure : function(resp) 
		 		{
		   			alert("Oops, there's been an error.");
		 		},
		 		parameters : 'language=<?php echo $_REQUEST['language']; ?>&action=delete&additionalTextId=' + jsAdditionalTextId
			}
		);
	}
	
	function setDefaultAdditionalText(jsAdditionalTextId)
	{
		new Ajax.Request
		(
			'ajax_getadditionaltext.php',
			{
				onSuccess : function(resp) 
				{
					var response = resp.responseText;
					document.getElementById('divListAdditionalText').innerHTML = response;
					document.getElementById('divListAdditionalText').style.display = 'block';
				},
		 		onFailure : function(resp) 
		 		{
		   			alert("Oops, there's been an error.");
		 		},
		 		parameters : 'language=<?php echo $_REQUEST['language']; ?>&action=setDefault&additionalTextId=' + jsAdditionalTextId
			}
		);
	}
	
	function reloadTimeout()
	{
		setTimeout('reloadAdditionalText()', 500);
	}
	
	function reloadAdditionalText()
	{
		new Ajax.Request
		(
			'ajax_getadditionaltext.php',
			{
				onSuccess : function(resp) 
				{
					var response = resp.responseText;
					document.getElementById('divListAdditionalText').innerHTML = response;
					document.getElementById('divListAdditionalText').style.display = 'block';
				},
		 		onFailure : function(resp) 
		 		{
		   			alert("Oops, there's been an error.");
		 		},
		 		parameters : 'language=<?php echo $_REQUEST['language']; ?>'
			}
		);
	}
	</script>
	
	<style type="text/css">
	    ul {
	      margin: 0;
	      margin-top: 8px;
	      padding: 0;
	      list-style-type: none;
	      width: 250px;
	    }
	    li {
	      margin: 0;
	      margin-bottom: 4px;
	      padding: 1px;
	      border: 1px solid #888;
	      cursor: move;
	    }
	</style>
</head>
<body>

	<br/>
	<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_CONFIG;?>
	</span><br/><br/>
	
	<?php
		// table styles
		$style = "background-color: #fff; text-align: left;";
		$style2 = "background-color: #EFEFEF;  text-align: left;";	
		$style3 = "background-color: #8e8f90; color: #fff; font-size: 12px; font-weight: bold; font-family: arial;";
		
		// encrypting link:
		$strParams				= 'language='.$_REQUEST['language'];
		$strEncyrptedParams		= $objURL->encryptURL($strParams);
		$strEncryptedLinkURL	= 'editconfig_write.php?key='.$strEncyrptedParams;
	?>
	<form action="<?php echo $strEncryptedLinkURL ?>" method="post" name="config_form">
	
	
<?php // ############################### TABLE FOR TABBING :: START ############################### ?>
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" valign="top" onClick="showSettings('database');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #c8c8c8; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('database', 'over');" onMouseOut="changeStyle('database', 'out');" id="database">
				<?php echo $CONFIG_HEADCATEGORY_DATABASE ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('server');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #c8c8c8; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('server', 'over');" onMouseOut="changeStyle('server', 'out');" id="server">
				<?php echo $CONFIG_HEADCATEGORY_SERVER ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('gui');" style="padding: 2px 6px 2px 6px; background: #8e8f90; border: 1px solid #c8c8c8; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('gui', 'over');" onMouseOut="changeStyle('gui', 'out');" id="gui">
				<?php echo $CONFIG_HEADCATEGORY_GUI ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('auth');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #c8c8c8; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('auth', 'over');" onMouseOut="changeStyle('auth', 'out');" id="auth">
				<?php echo $CONFIG_HEADCATEGORY_AUTHENTICATION ?>
			</td>
			<td align="left" valign="top" onClick="showSettings('workflow');" style="padding: 2px 6px 2px 6px; background: #bbb; border: 1px solid #c8c8c8; border-left: 0px; border-bottom: 0px; font-weight: bold; color: #fff;" onMouseOver="changeStyle('workflow', 'over');" onMouseOut="changeStyle('workflow', 'out');" id="workflow">
				<?php echo $CONFIG_HEADCATEGORY_WORKFLOW ?>
			</td>
		</tr>
	</table>
<?php // ############################### TABLE FOR TABBING :: END ############################### ?>
	

<?php // ############################### DATABASE SETTINGS :: START ############################### ?>
	<div id="database_table" style="display: none;">
		<table style="border: 1px solid #c8c8c8; width: 80%;" cellspacing="0" cellpadding="3">
	
			<tr style="<?php echo $style3 ?>">
				<td align="left" width="30%"><?php echo $CONFIG_HEADLINE_CATEGORY ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_CURSETTINGS ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_DEFSETTINGS ?></td>
			</tr>	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap width="300"><?php echo $CONFIG_DATABASE_HOST ?></td>
				<td width="300"><?php echo $DATABASE_HOST ?></td>
				<td nowrap>-</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_DATABASE_DATABASE ?></td>
				<td><?php echo $DATABASE_DB ?></td>
				<td>-</td>
			</tr>
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_DATABASE_PWD ?></td>
				<td>********</td>
				<td>-</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_DATABASE_USERID ?></td>
				<td><?php echo $DATABASE_UID ?></td>
				<td>-</td>
			</tr>
		</table>
	</div>
<?php // ############################### DATABASE SETTINGS :: END ############################### ?>


<?php // ############################### SERVER SETTINGS :: START ############################### ?>
	
	<div id="server_table" style="display: none;">
		<table style="border: 1px solid #c8c8c8; width: 80%;" cellspacing="0" cellpadding="3">
			<tr style="<?php echo $style3 ?>">
				<td align="left" valign="middle" width="30%"><?php echo $CONFIG_HEADLINE_CATEGORY ?></td>
				<td align="left" valign="middle"><?php echo $CONFIG_HEADLINE_CURSETTINGS ?></td>
				<td align="left" valign="middle"><?php echo $CONFIG_HEADLINE_DEFSETTINGS ?></td>
			</tr>
			<tr style="<?php echo $style ?>">
				<td nowrap valign="middle"><?php echo $CONFIG_SERVER_CFSERVER ?></td>
				<td><input name="strIN_CF_Server" type="text" class="InputText" style="width:200px;" value="<?php echo $CUTEFLOW_SERVER ?>"></td>
				<td nowrap valign="middle">-</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap valign="middle"><?php echo $CONFIG_SERVER_SYSREPLYADDR ?></td>
				<td><input name="strIN_SysReplyAddr" type="text" class="InputText" style="width:200px;" value="<?php echo $SYSTEM_REPLY_ADDRESS ?>"></td>
				<td>CuteFlow_System-no_reply_allowed</td>
			</tr>
			
			
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CFG_MAIL_SEND_TYPE; ?></td>
				<td>
					<select name="mail_send_type" id="mail_send_type" onchange="mailChanged()" class="InputText" style="width: 210px;">
						<option value="SMTP" <?php if ($MAIL_SEND_TYPE == 'SMTP') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_SMTP ?></option>
						<option value="PHP" <?php if ($MAIL_SEND_TYPE == 'PHP') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_PHP ?></option>
						<option value="MTA" <?php if ($MAIL_SEND_TYPE == 'MTA') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_MTA ?></option>
					</select>
					
					<br/>
					<table id="smtp_options" style="display: <?php echo $MAIL_SEND_TYPE=='SMTP' ? 'block' : 'none'?>">
						<tr id="smtp_server" valign="top" style="<?php echo $style2 ?>">
							<td nowrap><?php echo $CONFIG_SERVER_SMTPSEVER ?></td>
							<td><input name="strIN_SMTP_Server" type="text" class="InputText" style="width:200px;" value="<?php echo $SMTP_SERVER ?>"></td>
						</tr>
						<tr id="smtp_port" valign="top" style="<?php echo $style ?>">
							<td nowrap><?php echo $CONFIG_SERVER_SMTPPORT ?></td>
							<td><input name="strIN_SMTP_port" type="text" class="InputText" style="width:200px;" value="<?php echo $SMTP_PORT ?>"></td>
						</tr>
						<tr id="smtp_user" valign="top" style="<?php echo $style2 ?>">
							<td nowrap><?php echo $CONFIG_SERVER_SMTPUSERID ?></td>
							<td><input name="strIN_SMTP_userid" type="text" class="InputText" style="width:200px;" value="<?php echo $SMTP_USERID ?>"></td>
						</tr>
						<tr id="smtp_pwd" valign="top" style="<?php echo $style ?>">
							<td nowrap><?php echo $CONFIG_SERVER_SMTPPWD ?></td>
							<td><input name="strIN_SMTP_pwd" type="password" class="InputText" style="width:200px;" value="<?php echo $SMTP_PWD ?>"></td>
						</tr>
						<tr id="smtp_auth" valign="top" style="<?php echo $style2 ?>">
							<td nowrap><?php echo $CONFIG_SERVER_USEAUTH ?></td>
							<td nowrap valign="top"><input type="checkbox" id="SMTP_use_auth" name="bRB_SMTP_use_auth" value="y" <?php if ($SMTP_USE_AUTH == 'y') echo 'checked'; ?>></td>
						</tr>
						<tr id="smtp_encrypt" valign="top" style="<?php echo $style ?>">
							<td nowrap><?php echo $MAIL_SEND_TYPE_SMTP_ENCRYPTION ?></td>
							<td nowrap valign="top">
								<select name="SMTP_encryption" id="SMTP_encryption" class="InputText" style="width: 130px;">
									<option value="NONE" <?php if ($SMPT_ENCRYPTION == 'NONE') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_SMTP_ENCRYPTION_NONE ?></option>
									<option value="SSL" <?php if ($SMPT_ENCRYPTION == 'SSL') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_SMTP_ENCRYPTION_SSL ?></option>
									<option value="TLS" <?php if ($SMPT_ENCRYPTION == 'TLS') echo 'selected' ?>><?php echo $MAIL_SEND_TYPE_SMTP_ENCRYPTION_TLS ?></option>
								</select>
							</td>
						</tr>
					</table>
					<table id="mta_options"  style="display: <?php echo $MAIL_SEND_TYPE=='MTA' ? 'block' : 'none'?>">
						<tr id="mta_path" valign="top" style="<?php echo $style2 ?>">
							<td nowrap><?php echo $MAIL_SEND_TYPE_MTA_PATH ?></td>
							<td><input name="mta_path" type="text" class="InputText" style="width:200px;" value="<?php echo $MTA_PATH ?>"></td>
						</tr>	
					</table>
				</td>
				<td align="left"><?php echo $MAIL_SEND_TYPE_SMTP ?></td>
			</tr>
		</table>
	</div>
<?php // ############################### SERVER SETTINGS :: END ############################### ?>


<?php // ############################### GUI SETTINGS :: START ############################### ?>
	<div id="gui_table">
		<table style="border: 1px solid #c8c8c8; width: 80%;" cellspacing="0" cellpadding="3">
			<tr style="<?php echo $style3 ?>">
				<td align="left" width="30%"><?php echo $CONFIG_HEADLINE_CATEGORY ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_CURSETTINGS ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_DEFSETTINGS ?></td>	
			</tr>
		
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_GUI_LANG ?></td>
				<td>
					<?php
					function getTranslationSettings($strTranslation)
					{
						global $CUTEFLOW_SERVER;
						
						$strTranslationFilePath = '../language_files/'.$strTranslation;
						$FP = @fopen($strTranslationFilePath, 'r');
						
						if ($FP)
						{
							while (!feof($FP)) //read all columns in current language file
							{
								$strLine = trim(fgets($FP, 4096));
								
								if (substr($strLine,0,5) == "_jotl")
								{
									$nPos = strpos($strLine, "=");
								    
								    $strStart = substr($strLine, 0, $nPos);
								    $strValue = trim(substr($strLine, $nPos+1));
									
									$nPos_LastDot = (strrpos($strStart,'.')+1);
									
									$strCategory = substr($strStart,$nPos_LastDot);
									
									switch ($strCategory)
									{
										case 'encoding':
											$arrTranslationSettings["encoding"] = $strValue;
											break;
										case 'langname':
											$arrTranslationSettings["langname"] = $strValue;
											break;
										case 'langshrt':
											$arrTranslationSettings["langshrt"] = $strValue;
											break;
										case 'dateform':
											$arrTranslationSettings["dateformat"] = $strValue;
											break;
										case 'timeform':
											$arrTranslationSettings["timeformat"] = $strValue;
											break;	
									}
								}						
							}
							fclose($FP);
						}
						
						return $arrTranslationSettings;
					}
					
					$strCurDirectory 	= $arrDirectories[$nIndex];		
					$strBasename 		= basename($PHP_SELF);
					
					$verz		= opendir ('../language_files');
					$nFIndex 	= 0;
					$arrFiles 	= '';
					while ($file = readdir($verz))
					{
					    if ( ($file != '.') && ($file != '..') && (substr($file, 0, 4) == 'gui_') )
					    {
				        	$arrCurDir[$nFIndex] = $file;
				        	$nFIndex++;
					 	}
					}
					closedir($verz);
					?>				
					<select name="strIN_DefLang" size="1" class="FormInput" style="width:100px;">
					<?php
	
					$nLNGMax = sizeof($arrCurDir);
					
					for ($nLNGIndex = 0; $nLNGIndex < $nLNGMax; $nLNGIndex++)
					{										
						$arrCurTrans = getTranslationSettings($arrCurDir[$nLNGIndex]);
						
						if ($arrCurTrans['langshrt'] != '')
						{
							?>
							<option value="<?php echo $arrCurTrans['langshrt']; ?>" <?php if ($DEFAULT_LANGUAGE == $arrCurTrans['langshrt']) echo 'selected' ?>><?php echo $arrCurTrans['langname']; ?></option>
							<?php
						}
					}
					?>
					</select>
				</td>
				<td><?php echo $arrAvailable_Languages[1] ?></td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_ROWS_PER_PAGE ?></td>
				<td>
					<select onChange="changeRows()" style="border: 1px solid #999; width: 60px; padding: 1px; font-family: arial; font-size: 12px;" name="IN_strShowRows" id="IN_strShowRows">
						<option value="10" <?php if($SHOWROWS == 10){ echo 'selected'; } ?>>10</option>
						<option value="20" <?php if($SHOWROWS == 20){ echo 'selected'; } ?>>20</option>
						<option value="50" <?php if($SHOWROWS == 50){ echo 'selected'; } ?>>50</option>
						<option value="100" <?php if($SHOWROWS == 100){ echo 'selected'; } ?>>100</option>
					</select>
				</td>
				<td>50</td>
			</tr>
			
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $AUTO_RELOAD ?> (sec)</td>
				<td><input name="strIN_AutoReload" type="text" class="InputText" style="width:100px;" value="<?php echo $AUTO_RELOAD_SEC ?>"></td>
				<td>-</td>
			</tr>
			
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_SERVER_MAILADDTEXT ?></td>
				<td align="left" valign="top" style="text-align: left;">
					<table cellpadding="0" cellspacing="0" style="margin-bottom: 4px;">
						<tr>
							<td width="20">
								<img src="../images/addtemplate.png" onClick="editAdditionalText(0, 'add');" style="cursor: pointer;">
							</td>
							<td>
								[ <a href="Javascript: editAdditionalText(0, 'add');"><?php echo $ADD_ADDITIONAL_TEXT ?></a> ]
							</td>
						</tr>
					</table>
					<div id="divListAdditionalText" class="InputText" style="margin-bottom: 4px; width: 350px; height: 100px; background: #fff; overflow: auto;">
						<table cellspacing="0" cellpadding="0" width="100%">
							<tr>
								<td align="left" valign="top" width="16" style="border-bottom: 1px solid #ccc;">
									#
								</td>
								<td align="left" valign="top" style="padding-right: 5px; border-bottom: 1px solid #ccc;">
									<?php echo $CIRCORDER_NAME ?>
								</td>
								<td align="center" valign="top" style="padding-right: 5px; border-bottom: 1px solid #ccc;" width="40">
									<?php echo $DEFAULT ?>
								</td>
								<td align="left" valign="top" width="60" style="border-bottom: 1px solid #ccc;">
									<?php echo $CIRCDETAIL_COMMANDS ?>
								</td>
							</tr>
							<tr><td height="2"></td></tr>
							<?php
							
							$max = ($additionalTexts) ? sizeof($additionalTexts) : 0;
							for ($index = 0; $index < $max; $index++)
							{
								$additionalText = $additionalTexts[$index];
								
								$id 		= $additionalText['id'];
								$title 		= $additionalText['title'];
								$content	= $additionalText['content'];
								$is_default	= $additionalText['is_default'];
								?>
								<tr>
									<td align="left" valign="top" style="padding-right: 5px;">
										<?php echo ($index+1) ?>
									</td>
									<td align="left" valign="top" style="padding-right: 5px;">
										<a href="Javascript: editAdditionalText(<?php echo $id ?>, 'show');"><?php echo $title ?></a>
									</td>
									<td align="center" valign="top" style="padding-right: 5px;">
										<?php if ($is_default) echo '<img src="../images/state_ok.png">' ?>
									</td>
									<td align="center" valign="top" style="padding-right: 5px;">
										<img src="../images/edit.png" style="cursor: pointer;" title="edit" onClick="editAdditionalText(<?php echo $id ?>, 'edit');">
										<img src="../images/edit_remove.gif" style="cursor: pointer;" title="delete" onClick="deleteAdditionalText(<?php echo $id ?>);">
										<img src="../images/tag_red.gif" style="cursor: pointer;" title="make default" onClick="setDefaultAdditionalText(<?php echo $id ?>);">
									</td>
								</tr>
								<?php
							}
							?>
						</table>
					</div>
				</td>
				<td>-</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_GUI_SORT ?></td>
				<td>
					<select name="strIN_Def_Sort" size="1" class="FormInput" style="width:100px;">
					<?php
					for ($nIndex=0; $nIndex < count($arrCONF_AllCols); $nIndex++)
					{
						?>
						<option <?php if($arrCONF_AllCols[$nIndex]['strTitle'] == $DEFAULT_SORT_COL) echo 'selected'; ?> value="<?php echo $arrCONF_AllCols[$nIndex]['strTitle'] ?>"><?php echo $arrCONF_AllCols[$nIndex]['strScreenTitle'] ?></option>
						<?php
					}
					?>
					</select>
					<select name="strIN_SortDirection" size="1" class="FormInput" style="width:100px;">
							<option value="ASC" <?php if($SORTDIRECTION == 'ASC') { echo 'selected'; } ?>><?php echo $CONFIG_SORTDIRECTION_ASC; ?></option>
							<option value="DESC" <?php if($SORTDIRECTION == 'DESC') { echo 'selected'; } ?>><?php echo $CONFIG_SORTDIRECTION_DESC; ?></option>
					</select>
				</td>
				<td><?php echo $CONFIG_DEFSORTCOL_NAME ?></td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_GUI_DETAILSWIN ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="OpenInSeperateWin" name="bOpenInSeperateWin" value="true" <?php if ($OPEN_DETAILS_IN_SEPERATE_WINDOW == 'true') echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_YES ?></td>
			</tr>

			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_GUI_REGEX ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="AutoRegWordStart" name="bAutoRegWordStart" value="true" <?php if ($FILTER_AUTO_REGEX_WORDSTART == 'true') echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_YES ?></td>
			</tr>
			
			<tr valign="top" style="<?php echo $style2 ?>">
				<td><?php echo $CONFIG_GUI_CIRCULATIONORDER ?></td>
				<td>
					<ul id="list">
						<?php
						$nIndex = 0;
						foreach ($arrCONF_AllCols as $arrCirculationCol)
						{
							$strCirculationCol = $arrCirculationCol['strTitle'];
							$strChecked = '';
							if ($arrCirculationCol['bActive'])
							{
								$strChecked = ' checked';
							}
							
							switch ($strCirculationCol)
							{
								case 'COL_CIRCULATION_FIELD1':
									$strCurCheckbox		= 'CB_FIELD1';
									?>
									<li id="<?php echo 'COL_CIRCULATION_FIELD1'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $FIELD.':';?>
											<select id="field1-id" name="field1-id">
												<?php foreach ($fields as $field):?>
													<option value="<?php echo $field['nID'];?>" <?php echo $arrCirculationCol['strScreenTitle'] == $field['nID'] ? 'selected="selected"' : '';?>><?php echo $field['strName']?></option>
												<?php endforeach; ?>			
											</select>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
									
								case 'COL_CIRCULATION_FIELD2':
									$strCurCheckbox		= 'CB_FIELD2';
									?>
									<li id="<?php echo 'COL_CIRCULATION_FIELD2'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $FIELD.':'; ?>
											<select id="field2-id" name="field2-id">
												<?php foreach ($fields as $field):?>
													<option value="<?php echo $field['nID'];?>"><?php echo $field['strName']?></option>
												<?php endforeach; ?>			
											</select>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
									
								case 'COL_CIRCULATION_NAME':
									$strCurCheckbox		= 'CB_NAME';
									?>
									<li id="<?php echo 'COL_CIRCULATION_NAME'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $CIRCORDER_NAME; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_STATION':
									$strCurCheckbox		= 'CB_STATION';
									?>
									<li id="<?php echo 'COL_CIRCULATION_STATION'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $CIRCORDER_STATION; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_PROCESS_DAYS':
									$strCurCheckbox		= 'CB_DAYS';
									?>
									<li id="<?php echo 'COL_CIRCULATION_PROCESS_DAYS'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $CIRCORDER_DAYS; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_PROCESS_START':
									$strCurCheckbox		= 'CB_START';
									?>
									<li id="<?php echo 'COL_CIRCULATION_PROCESS_START'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $CIRCORDER_START; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_SENDER':
									$strCurCheckbox		= 'CB_SENDER';
									?>
									<li id="<?php echo 'COL_CIRCULATION_SENDER'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $CIRCORDER_SENDER; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_MAILLIST':
									$strCurCheckbox		= 'CB_MAILLIST';
									?>
									<li id="<?php echo 'COL_CIRCULATION_MAILLIST'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $SHOW_CIRCULATION_MAILLIST; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_TEMPLATE':
									$strCurCheckbox		= 'CB_TEMPLATE';
									?>
									<li id="<?php echo 'COL_CIRCULATION_TEMPLATE'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $SHOW_CIRCULATION_TEMPLATE; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
								case 'COL_CIRCULATION_WHOLETIME':
									$strCurCheckbox		= 'CB_WHOLETIME';
									?>
									<li id="<?php echo 'COL_CIRCULATION_WHOLETIME'; ?>">
										<table cellspacing="0" cellpadding="0">
										<tr><td>
											<input type="checkbox" name="<?php echo $strCurCheckbox; ?>" value="1"<?php echo $strChecked; ?>>
										</td><td>
											<?php echo $SHOW_CIRCULATION_WHOLETIME; ?>
										</td></tr>
										</table>
									</li>						
									<?php
									break;
							}
							$nIndex++;
						}
						?>
					</ul>
		
					<input type="hidden" id="guicircorder" name="guicircorder" value="">
					<script type="text/javascript">
						Sortable.create( 'list', 
						{
							onUpdate:function()
							{
								$('guicircorder').value = Sortable.serialize('list');
								new Effect.Highlight('list',{});
							}
						})
					
					</script>
				</td>
				<td><?php echo $CIRCORDER_NAME ?> - <?php echo $CIRCORDER_STATION ?> - <?php echo $CIRCORDER_DAYS ?> - <?php echo $CIRCORDER_START ?> - <?php echo $CIRCORDER_SENDER ?></td>
			</tr>
		
			<tr><td nowrap><?php echo $CONFIG_HEADCATEGORY_DELAY ?></td><tr>
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_DELAY_NORMAL ?></td>
				<td><input name="nIN_Delay_norm" type="text" class="InputText" style="width:30px;" value="<?php echo $DELAY_NORMAL ?>"></td>
				<td>7</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_DELAY_INTERM ?></td>
				<td><input name="nIN_Delay_interm" type="text" class="InputText" style="width:30px;" value="<?php echo $DELAY_INDERMIDIATE ?>"></td>
				<td>10</td>
			</tr>
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_DELAY_LATE ?></td>
				<td><input name="nIN_Delay_late" type="text" class="InputText" style="width:30px;" value="<?php echo $DELAY_LATE ?>"></td>
				<td>12</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_DATE_FORMAT ?></td>
				<td><input name="IN_Date_Format" type="text" class="InputText" style="width:100px;" value="<?php echo $DATE_FORMAT ?>"></td>
				<td>m-d-Y (<?php echo $CONFIG_DATE_FORMAT_HINT;?>)</td>
			</tr>
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_USERDETAILS_USERDEFINED ?> 1:</td>
				<td><input name="IN_userdefined1_title" type="text" class="InputText" style="width: 260px;" value="<?php echo $USERDEFINED_TITLE1 ?>"></td>
				<td>user-defined1</td>
			</tr>
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_USERDETAILS_USERDEFINED ?> 2:</td>
				<td><input name="IN_userdefined2_title" type="text" class="InputText" style="width: 260px;" value="<?php echo $USERDEFINED_TITLE2 ?>"></td>
				<td>user-defined2</td>
			</tr>
		</table>
	</div>
<?php // ############################### GUI :: END ############################### ?>

<?php // ############################### WORKFLOW SETTINGS :: START ############################### ?>
	<div id="workflow_table" style="display: none;">
		<table style="border: 1px solid #c8c8c8; width: 80%;" cellspacing="0" cellpadding="3">
			<tr style="<?php echo $style3 ?>">
				<td align="left" width="30%"><?php echo $CONFIG_HEADLINE_CATEGORY ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_CURSETTINGS ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_DEFSETTINGS ?></td>	
			</tr>
			
			<tr valign="top" style="<?php echo $style ?>">
				<td width="280" valign="middle"><?php echo $CONFIG_SERVER_SUBSDAYS ?></td>
				<td>					
					<input name="strIN_Subtitute_Person_Value" type="text" class="InputText" style="width:50px;" value="<?php echo $SUBTITUTE_PERSON_VALUE ?>">
					
					<select name="strIN_Subtitute_Person_Unit" size="1" class="FormInput" style="width:100px;">			
						<option <?php if ($SUBTITUTE_PERSON_UNIT == 'DAYS') echo 'selected'; ?> value="DAYS"><?php echo $CONFIG_DAYS ?></option>
						<option <?php if ($SUBTITUTE_PERSON_UNIT == 'HOURS') echo 'selected'; ?> value="HOURS"><?php echo $CONFIG_HOURS ?></option>
						<option <?php if ($SUBTITUTE_PERSON_UNIT == 'MINUTES') echo 'selected'; ?> value="MINUTES"><?php echo $CONFIG_MINUTES ?></option>
					</select>
				</td>
				<td>4</td>
			</tr>
			
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_GUI_POSINMAIL ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="ShowPosInMail" name="bShowPosInMail" value="true" <?php if ($SHOW_POSITION_IN_MAIL == 'true') echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_YES ?></td>
			</tr>
				
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_UNENCRYPTED_REQUESTS ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="IN_UnencryptedRequest" name="IN_UnencryptedRequest" value="1" <?php if ($ALLOW_UNENCRYPTED_REQUEST) echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_NO ?></td>
			</tr>
		
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CFG_EMAIL_FORMAT ?></td>		
				<td>
					<select name="strIN_Email_Format" size="1" class="FormInput" style="width:100px;"  onChange=checkValue()>				
						<option <?php if ($EMAIL_FORMAT == 'PLAIN') echo 'selected'; ?> value="TEXT"><?php echo $EMAIL_FORMAT_TEXT ?></option>
						<option <?php if ($EMAIL_FORMAT == 'HTML') echo 'selected'; ?> value="HTML">HTML</option>
					</select>				
					<select name="strIN_Email_Value" size="1" class="FormInput" style="width:100px;" onChange=checkValue()>
						<option <?php if ($EMAIL_VALUES == 'NONE') echo 'selected'; ?> value="NONE"><?php echo $EMAIL_VALUES_NONE ?></option>
						<option <?php if ($EMAIL_VALUES == 'VALUES') echo 'selected'; ?> value="VALUES"><?php echo $EMAIL_VALUES_VALUES ?></option>
						<option <?php if ($EMAIL_VALUES == 'IFRAME') echo 'selected'; ?> value="IFRAME">IFrame</option>
					</select>
				</td>
				<td>HTML - IFrame</td>
			</tr>
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CFG_SEND_WORKFLOW_MAIL ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="IN_SendWorkflowMail" name="IN_SendWorkflowMail" value="1" <?php if ($SEND_WORKFLOW_MAIL) echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_YES ?></td>
			</tr>			
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CFG_SEND_REMINDER_MAIL ?></td>		
				<td nowrap valign="top"><input type="checkbox" id="IN_SendReminderMail" name="IN_SendReminderMail" value="1" <?php if ($SEND_REMINDER_MAIL) echo 'checked'; ?>></td>
				<td><?php echo $CONFIG_NO ?></td>
			</tr>
			<tr>
				<td nowrap><?php echo $CFG_SLOT_VISIBILITY; ?></td>
				<td>
					<select name="slot_visibility" class="InputText" style="width: 270px;">
						<option value="SINGLE" <?php if ($SLOT_VISIBILITY == 'SINGLE') echo 'selected' ?>><?php echo $SLOT_VISIBILITY_SINGLE ?></option>
						<option value="ALL" <?php if ($SLOT_VISIBILITY == 'ALL') echo 'selected' ?>><?php echo $SLOT_VISIBILITY_ALL ?></option>
						<option value="TOP" <?php if ($SLOT_VISIBILITY == 'TOP') echo 'selected' ?>><?php echo $SLOT_VISIBILITY_ALL_TOP ?></option>
					</select>
				</td>
				<td align="left"><?php echo $SLOT_VISIBILITY_ALL_TOP ?></td>
			</tr>
			
		</table>
	</div>
<?php // ############################### WORKFLOW :: END ############################### ?>

	

<!-- /* CHRISTOPHER A. USEY START LDAP MODIFICATION */  -->
<?php // ############################### LDAP SETTINGS :: START ############################### ?>
	<div id="auth_table" style="display: none;">
		<table style="border: 1px solid #c8c8c8; width: 80%;" cellspacing="0" cellpadding="3">
			<tr style="<?php echo $style3 ?>">
				<td align="left" width="30%"><?php echo $CONFIG_HEADLINE_CATEGORY ?></td>
				<td align="left" width="30%"><?php echo $CONFIG_HEADLINE_CURSETTINGS ?></td>
				<td align="left"><?php echo $CONFIG_HEADLINE_DEFSETTINGS ?></td>
			</tr>
		
			<tr>
				<td nowrap><?php echo $CONFIG_LDAP_AUTHENTICATION_METHOD ?></td>
				<td>
					<select name="auth_method" class="InputText" style="width: 210px;">
						<option value="DB" <?php if ($LDAP['auth_method'] == 'DB') echo 'selected' ?>><?php echo $CONFIG_HEADCATEGORY_DATABASE ?></option>
						<option value="LDAP" <?php if ($LDAP['auth_method'] == 'LDAP') echo 'selected' ?>><?php echo $CONFIG_LDAP_AUTH_LDAP ?></option>
						<option value="HYBRID" <?php if ($LDAP['auth_method'] == 'HYBRID') echo 'selected' ?>><?php echo $CONFIG_LDAP_AUTH_HYBRID ?></option>
						<option value="AD" <?php if ($LDAP['auth_method'] == 'AD') echo 'selected' ?>><?php echo $CONFIG_LDAP_AUTH_ACTIVE_DIRECTORY ?></option>
						<option value="HYBRID_AD" <?php if ($LDAP['auth_method'] == 'HYBRID_AD') echo 'selected' ?>><?php echo $CONFIG_LDAP_AUTH_HYBRID_AD ?></option>
					</select>
				</td>
				<td align="left"><?php echo $CONFIG_HEADCATEGORY_DATABASE ?></td>
			</tr>
			
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_HOST ?></td>
				<td><input name="ldap_host" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_host] ?>"></td>
				<td>-</td>
			</tr>
			
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_DOMAIN ?></td>
				<td><input name="ldap_domain" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_domain] ?>"></td>
				<td>-</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_BIND_UN_CONTEXT ?></td>
				<td><input name="ldap_binddn" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_binddn] ?>"></td>
				<td>-</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_PASSWORD ?></td>
				<td><input name="ldap_bindpwd" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_bindpwd] ?>"></td>
				<td>-</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_ROOT_CONTEXT ?></td>
				<td><input name="ldap_rootdn" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_rootdn] ?>"></td>
				<td>-</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_USER_SEARCH_ATTRIBUTE ?></td>
				<td><input name="ldap_searchattr" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_searchattr] ?>"></td>
				<td align="left">samaccountname</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_FIRST_NAME_ATTRIBUTE ?></td>
				<td><input name="ldap_fname" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_fname] ?>"></td>
				<td align="left">givenname</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_LAST_NAME_ATTRIBUTE ?></td>
				<td><input name="ldap_lname" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_lname] ?>"></td>
				<td align="left">sn</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_USERNAME_ATTRIBUTE ?></td>
				<td><input name="ldap_uname" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_uname] ?>"></td>
				<td align="left">samaccountname</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_EMAIL_ATTRIBUTE ?></td>
				<td><input name="ldap_email_add" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_email] ?>"></td>
				<td align="left">mail</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_OFFICE ?></td>
				<td><input name="ldap_office" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_office] ?>"></td>
				<td align="left">physicaldeliveryofficename</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style ?>">
				<td nowrap><?php echo $CONFIG_LDAP_PHONE_ATTRIBUTE ?></td>
				<td><input name="ldap_phone" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_phone] ?>"></td>
				<td align="left">telephonenumber</td>
			</tr>
	
			<tr valign="top" style="<?php echo $style2 ?>">
				<td nowrap><?php echo $CONFIG_LDAP_CONTEXT_ATTRIBUTE ?></td>
				<td><input name="ldap_context" type="text" class="InputText" style="width:200px;" value="<?php echo $LDAP[ldap_context] ?>"></td>
				<td align="left">dn</td>
			</tr>
			
			<tr>
				<td nowrap>
					<?php echo $CONFIG_LDAP_NEW_USER_DEFAULT_LEVEL ?>
				</td>
				<td>
					<select name="default_level" class="InputText" style="width: 210px;">
						<option value='2' <?php if ($LDAP['default_level'] == 2) echo 'selected' ?>><?php echo $USER_ACCESSLEVEL_ADMIN ?></option>
						<option value='8' <?php if ($LDAP['default_level'] == 8) echo 'selected' ?>><?php echo $USER_ACCESSLEVEL_SENDER ?></option>
						<option value='4' <?php if ($LDAP['default_level'] == 4) echo 'selected' ?>><?php echo $USER_ACCESSLEVEL_READONLY ?></option>
						<option value='1' <?php if ($LDAP['default_level'] == 1) echo 'selected' ?>><?php echo $USER_ACCESSLEVEL_RECEIVER ?></option>
					</select>
				</td>
				<td align="left"><?php echo $USER_ACCESSLEVEL_READONLY ?></td>
			</tr>
			
		</table>
	</div>
<?php // ############################### LDAP SETTINGS :: END ############################### ?>
	
	<table width="80%">
		<tr>
			<td align="left">
				<input type="button" class="Button" value="<?php echo $BTN_RESET ?>" onclick="document.config_form.reset();">
			</td>
			<td align="right">
				<input type="submit" class="Button" value="<?php echo $CONFIG_SAVE ?>">
			</td>
		</tr>
	</table>
	
	</form>
	<script type="text/javascript">
    	new Ajax.Request
		(
			"cronjob_check_substitute.php",
			{
				onSuccess : function(resp) 
				{
					
				},
		 		onFailure : function(resp) 
		 		{
		   			
		 		}
			}
		);
	</script>
</body>
</html>