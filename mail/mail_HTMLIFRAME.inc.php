<?php
include_once ("../pages/version.inc.php");
$CurLang = $_REQUEST["language"];

$strParams					= 'cpid='.$Circulation_cpid.'&language='.$CurLang;
$strEncyrptedParams			= $objURL->encryptURL($strParams);
$strEncryptedBrowserview	= $CUTEFLOW_SERVER.'/pages/editworkflow_standalone.php?key='.$strEncyrptedParams;


$strMessage = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
	<title></title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$DEFAULT_CHARSET\">
	<style>
		body, table, td, tr
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 9pt;
		}
		a
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 9pt;
			text-decoration: none;
		}
		a:hover
		{ text-decoration: underline; }
		.BorderRed
		{ border: 1px solid Red; }
		.BorderGray
		{ border: 1px solid Gray; }
		.FormInput
		{
			font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
			font-size: 8pt;
			border: 1px solid #B8B8B8;
		}
		.Button
		{
			font-size: 8pt;
			border: 1px solid #C10000;
			color: Black;
			padding: 2px 2px 2px 2px;
		}
		.note
		{
			padding-left : 2px;
			padding-top  : 2px;
			border-width : 1px;
			border-color : #B0B0AF;
			border-style : solid;
			background-color: #FCFBE9;
			font-size: 8pt;
		}
		.table_header
		{
			background-color: #8e8f90; 
			color: #fff; 
			font-size: 12px; 
			font-weight: bold;
		}
		.mandatory
		{ font-weight: bold; }
	</style>
</head>
<body bgcolor=\"#ffffff\">

	<div align=\"center\">
		
		<table width=\"100%\" style=\"border: 1px solid #c8c8c8; background: #efefef;\" cellspacing=\"0\" cellpadding=\"3\">
			<tr>
				<td colspan=\"2\" align=\"left\" class=\"table_header\" style=\"border-bottom: 3px solid #ffa000;\">
					$MAIL_HEADER_PRE $Circulation_Name
				</td>
			</tr>
			<tr>
				<td align=\"left\" colspan=\"2\" style=\"border-bottom: 1px solid gray;\">
					$Circulation_AdditionalText
				</td>
			</tr>
			<tr><td height=\"5\"></td></tr>
			<tr>
				<td class=\"note\" style=\"background-color:white;\" colspan=\"2\">$MAIL_LINK_DESCRIPTION
				<a href=\"$strEncryptedBrowserview\">$EMAIL_BROWSERVIEW</a>
				</td>
			</tr>
			<tr><td height=\"5\"></td></tr>
			<tr>
				<td align=\"left\" colspan=\"2\">
					<iframe id=\"mail_content\" name=\"mail_content\" src=\"$strEncryptedBrowserview\" width=\"100%\" frameborder=\"0\" height=\"500px\">
					</iframe>
				</td>
			</tr>
		</table>
		<br>
		<strong style=\"font-size:8pt;font-weight:normal\">powered by</strong><br>
		<a href=\"http://cuteflow.org\" target=\"_blank\"><img src=\"$CUTEFLOW_SERVER/images/cuteflow_logo_small.png\" border=\"0\" /></a><br>
		<strong style=\"font-size:8pt;font-weight:normal\">Version $CUTEFLOW_VERSION</strong><br>
	</div>

</body>
</html>";
?>