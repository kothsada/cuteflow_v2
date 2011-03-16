<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../pages/version.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
   	
   	<script language="JavaScript1.2">
	function checkValue()
	{
		var choiceVal, choiceVal2;
		choiceVal = document.install_1.bRBInstall_TestData.checked;
		choiceVal2 = document.install_1.strIN_TestData_Email.value;
		if ((choiceVal==true)&&(choiceVal2==""))
		{
			window.alert("Please enter a valid EMail adress for your testdata!");
			return false;
		}			
	}
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
					<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION ?></strong>
				</div>
			</div>
				
			<div class="step">
				
				<table width="100%" height="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<span class="small"><?php echo $INSTALL_STEP ?> 4/5<br>
						<?php echo $INSTALL_STEP1 ?> >> <?php echo $INSTALL_STEP2 ?> >> <?php echo $INSTALL_STEP3 ?> >> <span class="mandatory"><?php echo $INSTALL_STEP4 ?></span>
						</span>
					</td>
				</tr>
				</table>
			</div>
			
			<div class="content_border">
				<span class="underline"><?php echo "$INSTALL_HEAD4"; ?></span>
				<div class="content">
		
		<form action="writetestdata.php" method="post" name="install_1" onSubmit="return checkValue()">
		
		<?php
		$style = "background-color: #F0F0F0;";
		
		echo "<br><table width=\"500\" align=\"center\" bgcolor=\"white\" style=\"border: 1px solid grey;\" cellpadding=\"5\">";
		echo "<tr><td colspan =\"2\" class=\"table_header\">$INSTALL_TD_HEAD</td><tr>";
				
			echo "</tr>";
			echo "<tr valign=\"top\" style=\"$style2\">";
				echo "<td nowrap>$INSTALL_TD_INSTTD</td>";		
				if ($_REQUEST["bRBInstall_TestData"])
				{
					echo "<td nowrap valign=\"top\"><input type=\"checkbox\" name=\"bRBInstall_TestData\" value=\"true\" checked>";
					echo "<div class=\"small\">$INSTALL_TD_INSTTD_INFO</div>";
					echo "</td>";
				}
				else
				{
					echo "<td nowrap valign=\"top\"><input type=\"checkbox\" name=\"bRBInstall_TestData\" value=\"true\">";
					echo "<div class=\"small\">$INSTALL_TD_INSTTD_INFO</div>";
					echo "</td>";
				}
			
			echo "</tr>";
			echo "<tr valign=\"top\" style=\"$style\">";
				echo "<td nowrap>$INSTALL_TD_MAIL</td>";
				echo "<td><input name=\"strIN_TestData_Email\" type=\"text\" class=\"FormInput\" style=\"width:200px;\" value=\"".$_REQUEST["strIN_TestData_Email"]."\">";
				echo "<div class=\"small\">$INSTALL_TD_MAIL_INFO</div>";
				echo "</td>";
				
			echo "</tr>";
		echo "</table>";
		?>
		
			</div>
			</div>	
			
			<div class="bottom_right">
				<input type="submit" value="<?php echo $INSTALL_BUTT_CON ?>" class="button_next"><?php echo "<input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>"; ?>
			</div></form>
			
			<form action="writeserversettings.php" method="post">
			<div class="bottom_left">
				<?php
				echo "<input type=\"hidden\" name=\"strIN_CF_Server\" value=\"".$_REQUEST["strIN_CF_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_Server\" value=\"".$_REQUEST["strIN_SMTP_Server"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_port\" value=\"".$_REQUEST["strIN_SMTP_port"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_userid\" value=\"".$_REQUEST["strIN_SMTP_userid"]."\">";
				echo "<input type=\"hidden\" name=\"strIN_SMTP_pwd\" value=\"".$_REQUEST["strIN_SMTP_pwd"]."\">";
				echo "<input type=\"hidden\" name=\"bRB_SMTP_use_auth\" value=\"".$_REQUEST["bRB_SMTP_use_auth"]."\">";
				?>
				<input type="submit" value="<?php echo "$INSTALL_BUTT_BAC"; ?>" class="button_prev"><?php echo "<input type=\"hidden\" name=\"language\" value=$_REQUEST[language]>"; ?>
			</div></form>	
		
		</div>
	</center>

</body>
</html>