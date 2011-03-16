<?php
	$file = "config/db_config.inc.php";
	
	//checking if cuteflow is installed
	if (!file_exists($file))
	{
		header ("Location:install/install_cuteflow.php");
	}
	
	require_once 'config/config.inc.php';
	require_once 'pages/version.inc.php';
	
	function lang_getfrombrowser ($allowed_languages, $default_language, $lang_variable = null, $strict_mode = true)
	{
		// $_SERVER['HTTP_ACCEPT_LANGUAGE'] verwenden, wenn keine Sprachvariable mitgegeben wurde
		if ($lang_variable === null) {
			$lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		if (empty($lang_variable)) 
		{
			return $default_language;
		}

		$accepted_languages = preg_split('/,\s*/', $lang_variable);

		$current_lang = $default_language;
		$current_q = 0;

		foreach ($accepted_languages as $accepted_language) 
		{
			$res = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)'.
								'(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $accepted_language, $matches);


			if (!$res)
			{
				continue;
			}
       
			$lang_code = explode ('-', $matches[1]);

			if (isset($matches[2])) 
			{
				$lang_quality = (float)$matches[2];
			} 
			else 
			{
				$lang_quality = 1.0;
			}

			while (count ($lang_code)) 
			{
				if (in_array (strtolower (join ('-', $lang_code)), $allowed_languages)) 
				{
					
					if ($lang_quality > $current_q) 
					{
						$current_lang = strtolower (join ('-', $lang_code));
						$current_q = $lang_quality;
						break;
					}
				}

				if ($strict_mode) 
				{
					break;
				}
				array_pop ($lang_code);
			}
		}

		return $current_lang;
	}
	
	$allowed_langs = array ('br','cz', 'th', 'de', 'en', 'es', 'fr', 'it', 'nl', 'pl', 'pt', 'tr');


	
	if(isset($_COOKIE['strMyLanguage'])) 
	{// cookie is set
        if ($_REQUEST['language'] == '')
		{// no language is selected
        	$_REQUEST['language'] = $_COOKIE['strMyLanguage'];
		}
		else
		{// language is selected
			if ($_REQUEST['language'] != $_COOKIE['strMyLanguage'])
			{// set new cookie - because a new language is selected
				$var = time() + (60*60*24*7*4);
				setcookie('strMyLanguage', $_REQUEST['language'], $var);
			}
		}
    } 
    else 
    {// cookie is not set
        if ($_REQUEST['language'] == '')
		{// no language is selected
        	$lang = lang_getfrombrowser ($allowed_langs, 'en', null, false);
        	$var = time() + (60*60*24*7*4);
			setcookie('strMyLanguage', $_REQUEST['language'], $var);
			$_REQUEST['language'] = $lang;
		}
		else
		{// language is selected
			if ($_REQUEST['language'] != $_COOKIE['strMyLanguage'])
			{// set new cookie - because a new language is selected
				$var = time() + (60*60*24*7*4);
				setcookie('strMyLanguage', $_REQUEST['language'], $var);
			}
		}
    }
	
	include ("language_files/language.inc.php");
	
	echo header('Cache-Control: no-cache');	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title>CuteFlow</title>
	<link rel="stylesheet" href="pages/format.css" type="text/css">
	<script src="pages/jsval.js" type="text/javascript" language="JavaScript"></script>
	<script src="lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script language="JavaScript1.2">
	<!--				
		function changeLanguage (select) 
		{
		  	var wert 		= select.options[select.options.selectedIndex].value;
		  	
		  	inpdata	= 'language=' + wert;
			encodeblowfish();
			encoded = outdata;
		  	
		  	
		  	location.href 	= 'index.php?key=' + encoded;
		}
	
		function setProps()
		{
			var objForm = document.forms["Login"];
			
			objForm.Password.required = 1;
			objForm.Password.err = "<?php echo $LOGIN_ERROR_PASSWORD;?>";
			
			objForm.UserId.required = 1;
			objForm.UserId.err = "<?php echo $LOGIN_ERROR_USERID;?>";
		}
	//-->
	</script>
</head>

<body onLoad="setProps()" style="padding: 0px; margin: 0px;">
	<div style="background: url(images/headerbar.png); height: 60px; color: #fff; font-family: arial; font-size: 24px; text-align: center; margin-bottom: 60px;">
		<span style="position:relative; top:10px; font-size: 24px; color: #fff; font-family: Verdana; font-weight: bold;">
		<?php echo $TITLE_1." - ".$TITLE_2;?>
		</span>
	</div> 
		
	<div align="center">
		<br>
		<br>
		<br>
		<table cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<img style="position:relative;top:-15px;left:35px" src="images/login.png" height="48" width="48" alt="login">
				</td>
				<td>
					<form id="Login" action="login.php" method="post" onsubmit="return jsVal(this);">
						<table width="300" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
							<tr>
								<td colspan="2" class="table_header" align="left" style="border-bottom: 3px solid #ffa000;">
								<strong style="padding-left:35px"><?php echo $LOGIN_LOGIN;?></strong>
								</td>
							</tr>
							<tr>
								<td colspan="2" height="15px"></td>
							</tr>
							<tr>
								<td align="left"><?php echo $LOGIN_USERID;?>:</td>
								<td align="left"><input type="text" name="UserId" class="FormInput"></td>
							</tr>
							<tr>
								<td align="left"><?php echo $LOGIN_PWD;?>:</td>
								<td align="left"><input type="password" name="Password" class="FormInput"></td>
							</tr>
							<tr>
								<td align="left"><?php echo $LOGIN_LANGUAGE;?>:</td>
								<td align="left">
									<?php										
										function getTranslationSettings($strTranslation)
										{
											global $CUTEFLOW_SERVER;
											
											$strTranslationFilePath = "language_files/".$strTranslation;
											
											$FP = fopen ($strTranslationFilePath,"r");
											
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
											
											return $arrTranslationSettings;
										}
										
										$strCurDirectory = $arrDirectories[$nIndex];
		
										$verz		= opendir ("language_files");
										$nFIndex 	= 0;
										$arrFiles 	= '';
										while ($file=readdir($verz))
										{
										    if ( ($file != '.') && ($file != '..') && (substr($file, 0, 4) == 'gui_') )
										    {
										        $arrCurDir[$nFIndex] = $file;
										        
										        $nFIndex++;
										    }
										}
										closedir($verz);
									?>
									
									<select class="FormInput" onchange="changeLanguage(this)" id="language" name="language">
										<?php
										$nLNGMax = sizeof($arrCurDir);
										for ($nLNGIndex = 0; $nLNGIndex < $nLNGMax; $nLNGIndex++)
										{										
											$arrCurTrans = getTranslationSettings($arrCurDir[$nLNGIndex]);
											if ($arrCurTrans['langshrt'] != '')
											{
												?>
												<option value="<?php echo $arrCurTrans['langshrt']; ?>" <?php if ($_REQUEST["language"]== $arrCurTrans['langshrt']) echo "selected";?>><?php echo $arrCurTrans['langname']; ?></option>
												<?php
											}
										}
										?>
									</select>
																		
									
								</td>
							</tr>
							<tr>
								<td colspan="2" height="15px"></td>
							</tr>
							
						</table>
						
						<table cellspacing="0" cellpadding="3" align="center" width="300">
						<tr>
							<td align="left">
								&nbsp;
							</td>
							<td align="right">
								<input type="submit" value="<?php echo $BTN_LOGIN;?>" class="Button">
							</td>
						</tr>
						</table>
					</form>
				</td>
				<td width="48px">&nbsp;</td>
			</tr>
		</table>
		<br>
		<br>
		<strong style="font-size:8pt;font-weight:normal">powered by</strong><br>
		<a href="http://cuteflow.org" target="_blank"><img src="images/cuteflow_logo_small.png" border="0" /></a><br>
		<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION ?></strong><br> 
		
	</div>
</body>
</html>
