<?php
include_once ("../pages/version.inc.php");

$strMessage = <<<END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=$DEFAULT_CHARSET">
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
<body bgcolor="#ffffff">
	<br>
	<br>
	<div align="center">
		<strong>$REMINDER_MAIL_HEADER1</strong><br/>
		$REMINDER_MAIL_HEADER2
		<br/>
		<a href="$CUTEFLOW_SERVER">$ALL_OPEN_WORKFLOWS</a>
		<br/>
		<br/>
		<table width="620" style="border: 1px solid #c8c8c8; background: #efefef;" cellspacing="0" cellpadding="3">
			<tr>
				<th align="left">$CIRCULATION_MNGT_NAME</th>
				<th align="left">$CIRCULATION_MNGT_SENDING_DATE</th>
				<th align="left">$CIRCORDER_SENDER</th>
				<th align="left">$CIRCULATION_MNGT_WORK_IN_PROCESS</th>
				<th></th>
			</tr>
END;
		foreach($circulations_aggregated as $single_circulation) {
			$strMessage .= '<tr>';
				$strMessage .= '<td>'.$single_circulation['circulation_name'].'</td>';
				$strMessage .= '<td>'.$single_circulation['circulation_start'].'</td>';
				$strMessage .= '<td>'.$single_circulation['circulation_sender'].'</td>';
				$strMessage .= '<td>'.$single_circulation['circulation_process_time'].'</td>';
				
				$strParams					= 'cfid='.$circulation['nID'].'&language='.$_REQUEST['language'];
				$strEncyrptedParams			= $objURL->encryptURL($strParams);
				$url	= $CUTEFLOW_SERVER.'/pages/editworkflow_standalone.php?key='.$strEncyrptedParams;

				$strMessage .= '<td><a href="'.$url.'">'.$STAT_HEADER_WORKFLOW.'</a></td>';			
			$strMessage .= '</tr>';
		}
$strMessage .=<<<END
		</table>
	</div>
</body>
</html>
END;
?>