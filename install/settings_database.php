<?php
	require_once '../language_files/language.inc.php';
	require_once 'new_ver.inc.php';
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
						<span class="small"><?php echo $INSTALL_STEP ?> 3/5<br>
						<?php echo $INSTALL_STEP1 ?> >> <?php echo $INSTALL_STEP2 ?> >> <span class="mandatory"><?php echo $INSTALL_STEP3 ?></span>
						</span>
					</td>
				</tr>
				</table>
			</div>
			
			<div class="content_border">
				<span class="underline"><?php echo $INSTALL_HEAD3_1 ?></span>
				<div class="content">
		
					<form action="writedatabase.php" method="post" name="install_1">
					
						<?php
						$style = "background-color: #F0F0F0;";
						?>
						
						<br><table width="500" align="center" bgcolor="white" style="border: 1px solid grey;" cellpadding="5">
						<tr><td colspan ="2" class="table_header"><?php echo $CONFIG_HEADCATEGORY_DATABASE ?></td><tr>
						
						<tr valign="top" style="<?php echo $style ?>" align="left">
							<td nowrap><?php echo $INSTALL_DB_TYPE?></td>
							<td align="left"><select name="strIN_DBType" size="1">
								<option selected>MySQL</option></select>
							<div class="small"><?php echo $INSTALL_DB_TYPE_INFO?></div>
							</td>
						</tr>
						<tr valign="top" style="<?php echo $style ?>" align="left">
							<td nowrap width="200"><?php echo $CONFIG_DATABASE_HOST?></td>
							<td><input name="strIN_Host" type="text" class="FormInput" style="width:150px;" value="<?php echo $_REQUEST["strIN_Host"] ?>">
							<div class="small"><?php echo $CONFIG_DATABASE_HOST_INFO ?></div>
							</td>
						</tr>
						<tr valign="top" style="<?php echo $style ?>" align="left">
							<td nowrap><?php echo $CONFIG_DATABASE_DATABASE ?></td>
							<td><input name="strIN_DB" type="text" class="FormInput" style="width:150px;" value="<?php echo $_REQUEST["strIN_DB"] ?>">
							<div class="small"><?php echo $CONFIG_DATABASE_DATABASE_INFO ?></div>
							</td>
						</tr>
						<tr valign="top" style="<?php echo $style ?>" align="left">
							<td nowrap><?php echo $CONFIG_DATABASE_USERID ?></td>
							<td><input name="strIN_UserID" type="text" class="FormInput" style="width:150px;" value="<?php echo $_REQUEST["strIN_UserID"] ?>">
							<div class="small"><?php echo $CONFIG_DATABASE_USERID_INFO ?></div>
							</td>
						</tr>
						<tr valign="top" style="<?php echo $style ?>" align="left">
							<td nowrap><?php echo $CONFIG_DATABASE_PWD ?></td>
							<td><input name="strIN_Pwd" type="password" class="FormInput" style="width:150px;" value="<?php echo $_REQUEST["strIN_Pwd"] ?>">
							<div class="small"><?php echo $CONFIG_DATABASE_PWD_INFO ?></div>
							</td>
						</tr>
						</table>
		
		
			</div>
			</div>
			
			<div class="bottom_right">
				<input type="submit" value="<?php echo $INSTALL_BUTT_CON ?>" class="button_next"><input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
			</div></form>
			
			<form action="checksystem.php" method="post">
			<div class="bottom_left">
				<input type="submit" value="<?php echo $INSTALL_BUTT_BAC ?>" class="button_prev"><input type="hidden" name="language" value="<?php echo $_REQUEST['language'] ?>">
			</div></form>	
		
		</div>
	</center>

</body>
</html>