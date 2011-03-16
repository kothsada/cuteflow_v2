<?php
$arrPlaceholdersAddText[] = '{%DATE_SENDING%}';
$arrPlaceholdersAddText[] = '{%TIME%}';
$arrPlaceholdersAddText[] = '{%CIRCULATION_TITLE%}';
$arrPlaceholdersAddText[] = '{%SENDER_USERNAME%}';
$arrPlaceholdersAddText[] = '{%SENDER_FULLNAME%}';

function getPlaceholderContent($strPlaceholder)
{
	global $nCirculationFormID, $strCirculationName;
	
	switch ($strPlaceholder)
	{
		case 'DATE_SENDING':
			return date('Y-m-d', time());
			break;
		case 'TIME':
			return date('H:i', time());
			break;
		case 'CIRCULATION_TITLE':
			return $strCirculationName;
			break;
		case 'SENDER_USERNAME':
			return $_SESSION['SESSION_CUTEFLOW_USERNAME'];
			break;
		case 'SENDER_FULLNAME':
			$userId = $_SESSION['SESSION_CUTEFLOW_USERID'];
			
			$query 	= "SELECT strLastName, strFirstName FROM cf_user WHERE nID = '$userId' LIMIT 1;";
			$result = mysql_query($query);
			
			if ($result)
			{
				$row = mysql_fetch_array($result, MYSQL_ASSOC);
				
				return $row['strLastName'].', '.$row['strFirstName'];
			}
			
			break;
	}
}
?>
