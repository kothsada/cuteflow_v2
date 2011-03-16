<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php 
	include_once ("../../language_files/language.inc.php");

	//--- parameterlist without show
	while(list($key, $value) = each($_REQUEST))
	{
		if ($key != "show")
		{
				$strURL = $strURL."&$key=".urlencode($value);
		}	
	}	
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="../format.css" type="text/css">
	<script language="JavaScript1.2">
	<!--
		function printMain()
		{
			parent.frames["Main"].focus();
			parent.frames["Main"].print();
		}
		
		function closeWindow()
		{
			parent.close();
		}
		
	//-->
	</script>
</head>
<body style="background-color:#D6D6D6;">
	<table>
		<tr>
			<td><a href="javascript:printMain();"><img src="../../images/printer2.png" height="29" width="29" alt="" border="0"></a></td>
			<td width="20px">&nbsp;</td>
			<td><a href="javascript:closeWindow();"><img src="../../images/close.png" height="29" width="29" alt="" border="0"></a></td>
			<td width="20px">&nbsp;</td>
		</tr>
	</table>
	 
</body>
</html>
