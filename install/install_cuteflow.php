<?php
	if ($_REQUEST['language'] == '')
	{
		$_REQUEST['language'] = 'en';	
	}
	require_once '../language_files/language.inc.php';
	require_once 'new_ver.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
   	<script src="pages/jsval.js" type="text/javascript" language="JavaScript"></script>
	<script language="JavaScript1.2">
	<!--
		function changeLanguage (select) 
		{
		  	var wert = select.options[select.options.selectedIndex].value;
		  	location.href = 'install_cuteflow.php?language=' + wert;
		}
	//-->
	</script>
</head>
<body>

	<center>
		<div class="border_content">
		
			<div class="top">
				<div class="top_left">
					<?php echo $INSTALL_HEAD ?>
				</div>
							
				<div class="top_right">
					<a href="http://cuteflow.org" target="_blank"><img src="../images/cuteflow_logo_small.png" border="0" /></a><br>
					<strong style="font-size:8pt;font-weight:normal">Version <?php echo $nNewVersion ?></strong>
				</div>
			</div>
				
			<div class="step">
				<form method="post" action="checksystem.php">
					<table width="100%" height="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								<span class="small"><?php echo $INSTALL_STEP ?> 1/5<br>
								<span class="mandatory"><?php echo $INSTALL_STEP1 ?></span>
								</span>
							</td>
						
							<td align="right"><?php echo $LOGIN_LANGUAGE ?>:
						
								<?php										
								function getTranslationSettings($strTranslation)
								{
									global $CUTEFLOW_SERVER;
									
									$strTranslationFilePath = '../language_files/'.$strTranslation;
									
									
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
				
								$strBasename 	= basename($PHP_SELF);
								
								$verz		= opendir ('../language_files');
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
							
							<select onchange="changeLanguage(this)" id="language" name="language">
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
					</table>
					
				</div>
				
				<div class="content_border">
					<span class="underline"><?php echo $INSTALL_HEAD1 ?></span>
					<div class="content" id="a">	
						<?php echo $INSTALL_START1 ?>
						<br><br>
					</div>
				</div>
				
				<div class="bottom_left">
					
				</div>
				<div class="bottom_right">
					<input type="submit" value="<?php echo $INSTALL_BUTT_INS ?>" class="button_next">
				</div>
			</form>
		</div>
	</center>

</body>
</html>