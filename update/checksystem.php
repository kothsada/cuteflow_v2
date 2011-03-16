<?php
	if($_REQUEST['language'] == '')
	{
		$_REQUEST['language'] = 'en';
	}
		
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../install/new_ver.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
   	<link rel="stylesheet" href="../install/inst_format.css" type="text/css">
</head>
<body>
<center>
<div class="border_content">

	<div class="top">
		<div class="top_left">
			Cuteflow Update
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
				<span class="small">updating cuteflow...<br>
				</span>
			</td>
		</tr>
		</table>
		
		
		
	</div>
	
	<div class="content_border">
		<?php
		echo "<div class=\"content\" id=\"a\">";
		
		//checking file access
		echo "<div class=\"check\">";
		echo "$INSTALL_ACCESS 'config'...";
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
		echo "$INSTALL_ACCESS 'attachments'...";
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
		echo "$INSTALL_ACCESS 'upload'...";
		
		if (is_writeable('../upload'))
		{
			echo "<span class=\"check_ok\">OK</span></div>";
			$bNoAccessConfig = false;
		}
		else
		{
			echo "<span class=\"check_error\">error: $INSTALL_ENSURE_ACCESS 'upload' (chmod 777)</span></div>";
			$bNoAccessConfig = true;
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
		
		if ($bNoAccessAttachments||$bNoAccessConfig)
		{
			echo "</div>";
			
			echo "</div>";
	
			echo "<div class=\"bottom_left\">";
				echo "<form method=\"post\" action=\"update.php\" name=\"left\"><div class=\"bottom_left\">";
					echo "<input type=\"submit\" value=\"$INSTALL_BUTT_BAC\" class=\"button_prev\"><input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>";
					echo "<input type=\"hidden\" name=\"strIn_CFVersion\" value=\"".$_REQUEST["strIn_CFVersion"]."\">";
				echo "</div></form>";
			echo "</div>";
			
			echo "<div class=\"bottom_right\">";
			echo "</div>";
		}
		else
		{
			?>
			<form method=post action=update_v2111_to_v2112.php>
				<br>
				<span class=underline>
					Updating Cuteflow v2.11.2 to v2.12.0
				</span>
				<br><br>	
		
				<?php echo str_replace('2.0.x', '2.11.x', $UPDATE_INFO) ?>
				<br><br><br>
					
			</div>
			</div>
			<div class=bottom_left>
			</div>
				<div class=bottom_right>
					<input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
					<input type=submit value=update class=button_next>
				</div>
			</form>
			
			<?php
		}
		?>
		
	</div>
</center>

</body>
</html>