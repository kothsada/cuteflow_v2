<?php
$arrPlaceholders[] = '{%DATE_SENDING%}';
$arrPlaceholders[] = '{%TIME%}';
$arrPlaceholders[] = '{%CIRCULATION_TITLE%}';
$arrPlaceholders[] = '{%CIRCULATION_ID%}';

function replaceMyPlaceholder($strPlaceholder)
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
		case 'CIRCULATION_ID':
			return $nCirculationFormID;
			break;
	}
}
?>
