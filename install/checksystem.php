<?php
	require_once '../language_files/language.inc.php';
	require_once 'new_ver.inc.php';
	
	function ver_cmp($arg1, $arg2 = null, $arg3 = null) {
	  static $phpversion = null;
	  if ($phpversion===null) $phpversion = phpversion();
	
	  switch (func_num_args()) {
	  case 1: return version_compare($phpversion, $arg1);
	  case 2:
	    if (preg_match('/^[lg][te]|[<>]=?|[!=]?=|eq|ne|<>$/i', $arg1))
	      return version_compare($phpversion, $arg2, $arg1);
	    elseif (preg_match('/^[lg][te]|[<>]=?|[!=]?=|eq|ne|<>$/i', $arg2))
	      return version_compare($phpversion, $arg1, $arg2);
	    return version_compare($arg1, $arg2);
	  default:
	    $ver1 = $arg1;
	    if (preg_match('/^[lg][te]|[<>]=?|[!=]?=|eq|ne|<>$/i', $arg2))
	      return version_compare($arg1, $arg3, $arg2);
	    return version_compare($arg1, $arg2, $arg3);
	  }
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
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
				
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<span class="small"><?php echo $INSTALL_STEP ?> 2/5<br>
						<?php echo $INSTALL_STEP1 ?> >> <span class="mandatory"><?php echo $INSTALL_STEP2 ?></span>
						</span>
					</td>
				</tr>
				</table>
				
				
				
			</div>
			
			<div class="content_border">
				<span class="underline"><?php echo $INSTALL_HEAD2 ?></span>
				<div class="content">
		
			<?php
			//check PHPVersion
			echo "<br><div class=\"check\">";
			echo $INSTALL_CHECKING." PHP version...";
			$minPHPVersion = "5.0.0";
			$PHPVersion = phpversion();
				
			if ($PHPVersion == $minPHPVersion)
			{
				echo "<span class=\"check_ok\">OK (PHP $PHPVersion)</span></div>";
			}
			else
			{				
				if ( ver_cmp ( ">=", $minPHPVersion ) ) 
				{
					echo "<span class=\"check_ok\">OK (PHP $PHPVersion)</span></div>";
					$bLowPHPVer = false;
				} 
				else 
				{
					echo "<span class=\"check_error\">error: PHP $minPHPVersion is required.</span></div>";
					$bLowPHPVer = true;
				}
			}
				
			//checking file access
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." 'config'...";
			//chmod("../config/dummy.txt", 755);
			if (is_writeable('../config'))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bNoAccessConfig = false;
			}
			else
			{
				echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'config' (chmod 777)</span></div>";
				$bNoAccessConfig = true;
			}
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." 'attachments'...";
			if (is_writeable('../attachments'))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bNoAccessAttachments = false;
			}
			else
			{
				echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'attachments' (chmod 777)</span></div>";
				$bNoAccessAttachments = true;
			}
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." 'upload'...";
			
			if (is_writeable('../upload'))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bNoAccessUpload = false;
			}
			else
			{
				echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'upload' (chmod 777)</span></div>";
				$bNoAccessUpload = true;
			}
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." 'boxes.js'...";
			if (is_writeable('../lib/RPL/Encryption'))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bNoAccessAttachments = false;
			}
			else
			{
				echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'lib/RPL/Encryption' (chmod 777)</span></div>";
				$bNoAccessAttachments = true;
			}
			
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." Function: '<a href=\"http://de2.php.net/manual/en/ref.iconv.php\" target=\"_blank\">iconv()</a>'...";
			if (function_exists("iconv"))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
				$bNoAccessAttachments = false;
			}
			else
			{
				echo "<span class=\"check_error\">error: Please install the required extensions for '<a href=\"http://de2.php.net/manual/en/ref.iconv.php\" target=\"_blank\">iconv()</a>'</span></div>";
				$bNoAccessAttachments = true;
			}
			
			// check extensions
			echo "<div class=\"check\">";
			echo $INSTALL_CHECKING." Extension: 'php_ldap'...";
			if (function_exists("ldap_connect"))
			{
				echo "<span class=\"check_ok\">OK</span></div>";
			}
			else
			{
				echo "<span class=\"check_error\">warning: If you want to use LDAP Authentification you have to install the required extension: 'php_ldap extension'</span></div>";
			}
			
			//Checking MySQL
			?>
			<div class="check">
			<?php echo $INSTALL_CHECKING ?> MySQL...
			
			<?php
			if (!function_exists('mysql_connect'))
			{
				$bNoMySQLExt = true;
				?>
				<span class="check_error">error: <?php echo $INSTALL_NO_MYSQL ?></span></div>
				<?php
			}
			else
			{
				$bNoMySQLExt = false;
				?>
				<span class="check_ok">OK</span></div>
				<?php
			}
		
			if($bLowPHPVer || $bNoAccessConfig || $bNoAccessAttachments || $bNoMySQLExt || $bNoAccessUpload)
			{
				?>
				<br><div class="check"><b><?php echo $INSTALL_TRY_AGAIN ?></b></div>
				</div></div>
				
				<form method="post" action="install_cuteflow.php" name="left"><div class="bottom_left">
					<input type="submit" value="<?php echo $INSTALL_BUTT_BAC ?>" class="button_prev"><input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
				</div></form>
				
				<div class="bottom_right">
					
				</div></form>
				<?php
			}
			else
			{
				?>
				<br><div class="check"><b><?php echo $INSTALL_SUCCESS_SYS ?></b></div>
				</div></div>
					
				<form method="post" action="install_cuteflow.php" name="left"><div class="bottom_left">
					<input type="submit" value="<?php echo $INSTALL_BUTT_BAC ?>" class="button_prev"><input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
				</div></form>
				
				<form method="post" action="settings_database.php" name="right"><div class="bottom_right">
					<input type="submit" value="<?php echo $INSTALL_BUTT_CON ?>" class="button_next"><input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
				</div></form>
				<?php
			}
			?>
		</div>
	</center>
	
</body>
</html>
