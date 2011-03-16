<?php
    include	('../language_files/language.inc.php');
	include	('../config/config.inc.php');
	include	('../config/db_connect.inc.php');
	include_once ('CCirculation.inc.php');
	
	$strName 				= $_REQUEST['FILTER_Name'];
	$nStationID				= $_REQUEST['FILTER_Station'];
	$nDaysInProgress_Start	= $_REQUEST['FILTER_DaysInProgress_Start'];
	$nDaysInProgress_End	= $_REQUEST['FILTER_DaysInProgress_End'];
	$strDate_Start			= $_REQUEST['FILTER_Date_Start'];
	$strDate_End			= $_REQUEST['FILTER_Date_End'];
	$nMailinglistID			= $_REQUEST['FILTER_Mailinglist'];
	$nTemplateID			= $_REQUEST['FILTER_Template'];
	$nSenderID				= $_REQUEST['FILTER_Sender'];
		
	$nUserID	= $_REQUEST['FILTER_nUserID'];
	$strLabel	= $_REQUEST['FILTER_strLabel'];
	
	$strCustom = '';
	
	$nIndexValues 		= 0;
	while(list($key, $value) = each($_REQUEST))
	{
		$arrShow = $arrShow.'Key: '.$key.' Value: '.$value.'<br>';
		
		if ($value != '')
		{
			$arrCurKey = split('_', $key);
						
			if ($arrCurKey[0] == 'FILTERCustom')
			{
				$arrPart2 = split('--', $arrCurKey[1]);
				
				$strType 			= $arrPart2[0];
				$nFILTERCustomID 	= $arrPart2[1];
				
				switch($strType)
				{
					case 'Field':
						$arrFILTERCustom[$nFILTERCustomID]['nInputFieldID'] = $value;
						break;
					case 'Operator':
						$arrFILTERCustom[$nFILTERCustomID]['nOperatorID'] = $value;
						break;
					case 'Value':
						$arrFILTERCustom[$nFILTERCustomID]['strValue'] = $value;
						break;
				}														
				$nIndexValues++;
			}
		}
	}
	
	if (sizeof($arrFILTERCustom) > 0)
	{			
		foreach($arrFILTERCustom as $arrCurFILTERCustom)
		{
			$nInputFieldID	= $arrCurFILTERCustom['nInputFieldID'];
			$nOperatorID	= $arrCurFILTERCustom['nOperatorID'];
			$strValue		= $arrCurFILTERCustom['strValue'];
			
			if ($strValue != '')
			{
				$strCustom = $strCustom.$nInputFieldID.'__'.$nOperatorID.'__'.$strValue.'----';
			}
		}
	}
	
	$strQuery 	= "INSERT INTO cf_filter values (null, '$nUserID', '$strLabel', '$strName', '$nStationID',
												'$nDaysInProgress_Start', '$nDaysInProgress_End', '$strDate_Start',
												'$strDate_End', '$nMailinglistID', '$nTemplateID', '$strCustom', '$nSenderID')";
	//echo $strQuery;
	$nResult 	= @mysql_query($strQuery);	
?>