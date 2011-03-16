<?php
	session_start();
	session_unset();   //--- Unset session variables.
	session_destroy(); //--- End Session we created earlier.
	
	include ("../config/config.inc.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
	<head>
		<title></title>
		<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
		<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
		<script language="JavaScript">
		<!--
			
			inpdata	= 'language=<?php echo $_REQUEST["language"];?>';
			encodeblowfish();
			encoded = outdata;
		  	
			parent.location.href = '../index.php?key=' + encoded;;
		//-->
		</script>
	</head>
	<body>
	</body>
</html>