<?php
	include_once ("../config/config.inc.php");	
	include_once ("../language_files/language.inc.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<?php 
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=".$DEFAULT_CHARSET."\" />";
	?>
	
	<title></title>
	<link rel="stylesheet" href="../pages/format.css" type="text/css">
</head>
<?php

	if (!$ALLOW_UNENCRYPTED_REQUEST)
	{
		// clear $_REQUEST to ensure that only the encryptet "key" is used
		foreach ($_GET as $key => $value)
		{
			if($key != 'key')
			{
				$_REQUEST[$key]		= '';
			}
		}
	}
	
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection)
	{
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			if ($_REQUEST['cpid'] != '')
			{
				//-----------------------------------------------
				//--- get the user information from 
				//--- cf_circulationprocess
				//-----------------------------------------------
				$strQuery = "SELECT * FROM cf_circulationprocess WHERE nID=".$_REQUEST["cpid"];
				$nResult = mysql_query($strQuery, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrCirculationProcess = mysql_fetch_array($nResult);				
					}
				}
				//-----------------------------------------------
				//--- get the single circulation form
				//-----------------------------------------------
				$query = "select * from cf_circulationform WHERE nID=".$arrCirculationProcess["nCirculationFormId"];
				$nResult = mysql_query($query, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrCirculationForm = mysql_fetch_array($nResult);				
					}
				}
			}
			elseif ($_REQUEST['cfid'] != '')
			{
				//-----------------------------------------------
				//--- get the single circulation form
				//-----------------------------------------------
				$query = "select * from cf_circulationform WHERE nID=".$_REQUEST['cfid'];
				$nResult = mysql_query($query, $nConnection);
				if ($nResult)
				{
					if (mysql_num_rows($nResult) > 0)
					{
						$arrCirculationForm = mysql_fetch_array($nResult);				
					}
				}
				
				$strQuery = "SELECT nID FROM `cf_circulationprocess` WHERE nCirculationFormId = '".$_REQUEST['cfid']."' AND ( nDecissionState = '0' OR nDecissionState = '2' OR nDecissionState = '16')";
				$nResult = mysql_query($strQuery, $nConnection);
	    		if ($nResult)
	    		{
	    			if (mysql_num_rows($nResult) > 0)
	    			{
	    				$arrLastRow = array();
	    				
	    				while ($arrRow = mysql_fetch_array($nResult))
	    				{
	    					$arrLastRow = $arrRow;
	    				}
						$Circulation_cpid = $arrLastRow[0];
						$_REQUEST['cpid'] = $Circulation_cpid;
					}
				}
			}
		}
	}

	if (!$arrCirculationForm)
	{
		echo "<table class=\"note\" style=\"background-color:white;\"> ";		
		echo "<tr>";		
		echo "<td><img src=\"../images/alert_warning.gif\" border=\"0\"></td>";
		echo "<td style\"font-weight: bold;\">$MAIL_CONTENT_CIRCULATION_CLOSED</td>";
		echo "</tr>";		
		echo "</table>";
	}
	else 
	{
		$strParams = 'cpid='.$_REQUEST['cpid'].'&language='.$_REQUEST['language'];
		if ($_REQUEST['cfid'] != '')
		{
			$strParams = 'cpid='.$_REQUEST['cpid'].'&language='.$_REQUEST['language'].'&bOwnCirculationView=1';
		}
		
		$strEncyrptedParams	= $objURL->encryptURL($strParams);
		
		if (($SHOW_POSITION_IN_MAIL == true) && ($_REQUEST['cfid'] == ''))
		{
			?>
			<frameset cols="180,*" frameborder="1" framespacing="0" border="1">
				<frame name="FRAME_POSITION" src="mail_content_position.php?key=<?php echo $strEncyrptedParams ?>" marginwidth="0" marginheight="0" scrolling="auto" frameborder="1">
			    <frame name="FRAME_VALUES" src="mail_content_values.php?key=<?php echo $strEncyrptedParams ?>" frameborder="0" scrolling="Auto" marginwidth="0" marginheight="0">
			</frameset>
			<?php
		}
		else
		{
			?>	
			<frameset cols="*" frameborder="0" framespacing="0" border="0">
				<frame name="FRAME_VALUES" src="mail_content_values.php?key=<?php echo $strEncyrptedParams ?>" frameborder="0" scrolling="Auto" marginwidth="0" marginheight="0">
			</frameset>
			<?php 
		}
	}
?>
</html>