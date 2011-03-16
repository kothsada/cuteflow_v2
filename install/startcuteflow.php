<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../pages/version.inc.php';
	
	$objURL->checkBoxes('../lib/RPL/Encryption/boxes.js', $URL_ENCODING_TS);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title></title>
   	<link rel="stylesheet" href="inst_format.css" type="text/css">
   	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
</head>
<body>
<center>
<div class="border_content">

	<div class="top">
		<div class="top_left">
			<?php echo "$INSTALL_HEAD"; ?>
		</div>
					
		<div class="top_right">
			<a href="http://cuteflow.org" target="_blank"><img src="../images/cuteflow_logo_small.png" border="0" /></a><br>
			<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION;?></strong>
		</div>
	</div>
		
	<div class="step">
		<table width="100%" height="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td>
				<span class="small">step 5/5<br>
				<?php echo "$INSTALL_STEP1"; ?> >> <?php echo "$INSTALL_STEP2"; ?> >> <?php echo "$INSTALL_STEP3"; ?> >> <?php echo "$INSTALL_STEP4"; ?> >> <span class="mandatory"><?php echo "$INSTALL_STEP5"; ?></span>
				</span>
			</td>
		</tr>
		</table>
	</div>
	
	<div class="content_border">
		<span class="underline"><?php echo $INSTALL_HEAD5 ?></span>
		<div class="content">
		
			<?php echo $INSTALL_END1; ?>
			<br/><br />
			<?php echo $INSTALL_START_ADMIN; ?>
		</div>
	</div>	
	
	<div class="bottom_left">
		
	</div><form method="post" action="../index.php"><div class="bottom_right">
	<div class="bottom_right">
		<input type="submit" value="start cuteflow" class="button_next">
	</div>
	</form>

</div>
</center>
</body>
</html>