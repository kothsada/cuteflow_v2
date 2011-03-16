<?php
	/** Copyright (c) Timo Haberkern. All rights reserved.
	*
	* Redistribution and use in source and binary forms, with or without 
	* modification, are permitted provided that the following conditions are met:
	* 
	*  o Redistributions of source code must retain the above copyright notice, 
	*    this list of conditions and the following disclaimer. 
	*     
	*  o Redistributions in binary form must reproduce the above copyright notice, 
	*    this list of conditions and the following disclaimer in the documentation 
	*    and/or other materials provided with the distribution. 
	*     
	*  o Neither the name of Timo Haberkern nor the names of 
	*    its contributors may be used to endorse or promote products derived 
	*    from this software without specific prior written permission. 
	*     
	* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, 
	* THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
	* PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR 
	* CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, 
	* EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, 
	* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; 
	* OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, 
	* WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR 
	* OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, 
	* EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
	*/
	
    require_once '../config/config.inc.php';
    $_REQUEST['language'] = $DEFAULT_LANGUAGE;
    
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';
	require_once 'send_circulation.php';

	$objCirculation = new CCirculation();
	
	
	$nMinMinutes = $SUBSTITUTE_PERSON_MINUTES;
	// get the max time period
	$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'DAYS' and bDeleted = 0 LIMIT 1;";
	$result 	= mysql_query($strQuery, $nConnection);
	$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
	
	if ($arrResult['nMinSubstituteTimeValue'] != '')
	{	// found an entry
		$nMaxDays 		= $arrResult['nMinSubstituteTimeValue'];
		$nMaxHours		= $nMaxDays * 24;
		$nMinMinutes	= $nMaxHours * 60;
		
		$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'HOURS' AND nSubstituteTimeValue < $nMaxHours and bDeleted = 0 LIMIT 1;";
		$result 	= mysql_query($strQuery, $nConnection);
		$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
		
		if ($arrResult['nMinSubstituteTimeValue'] != '')
		{	// found an entry
			$nMaxHours 		= $arrResult['nMinSubstituteTimeValue'];
			$nMinMinutes	= $nMaxHours * 60;
			
			$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'MINUTES' AND nSubstituteTimeValue < $nMinMinutes and bDeleted = 0 LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($arrResult['nMinSubstituteTimeValue'] != '')
			{	// found an entry
				$nMinMinutes	= $arrResult['nMinSubstituteTimeValue'];
			}
		}
		else
		{
			$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'MINUTES' and bDeleted = 0 LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($arrResult['nMinSubstituteTimeValue'] != '')
			{	// found an entry
				$nMinMinutes	= $arrResult['nMinSubstituteTimeValue'];
			}
		}
	}
	else
	{
		$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'HOURS' and bDeleted = 0 LIMIT 1;";
		$result 	= mysql_query($strQuery, $nConnection);
		$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
		
		if ($arrResult['nMinSubstituteTimeValue'] != '')
		{	// found an entry
			$nMaxHours 		= $arrResult['nMinSubstituteTimeValue'];
			$nMinMinutes	= $nMaxHours * 60;
			
			$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'MINUTES' AND nSubstituteTimeValue < $nMinMinutes and bDeleted = 0 LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($arrResult['nMinSubstituteTimeValue'] != '')
			{	// found an entry
				$nMinMinutes	= $arrResult['nMinSubstituteTimeValue'];
			}
		}
		else
		{
			$strQuery 	= "SELECT MIN(nSubstituteTimeValue) as nMinSubstituteTimeValue FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 AND strSubstituteTimeUnit = 'MINUTES' and bDeleted = 0 LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			if ($arrResult['nMinSubstituteTimeValue'] != '')
			{	// found an entry
				$nMinMinutes	= $arrResult['nMinSubstituteTimeValue'];
			}
		}
	}

	// build the timestamp
	$TSsendSP = mktime(date("H"),date("i")-$nMinMinutes,date("s"),date("m"), date("d"), date("Y"));

	// get all Users
	$strQuery = "SELECT * FROM cf_user WHERE bUseGeneralSubstituteConfig = 0 and bDeleted = 0";
	$nResult = mysql_query($strQuery, $nConnection);
	if ($nResult)
	{
		if (mysql_num_rows($nResult) > 0)
		{
			while ($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrUsers[$arrRow['nID']] = $arrRow;    							
			}
		}
	}
	
	// get the waiting circulations
	$strQuery = "SELECT * FROM cf_circulationprocess WHERE nDecissionState = 0 AND dateInProcessSince < '$TSsendSP'";
	$nResult = mysql_query($strQuery, $nConnection);
	if ($nResult)
	{
		if (mysql_num_rows($nResult) > 0)
		{
			while ($arrRow = mysql_fetch_array($nResult, MYSQL_ASSOC))
			{
				$arrOpenCirculations[] = $arrRow;    							
			}
		}
	}
	
	$nMax = sizeof($arrOpenCirculations);
	for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
	{
		$arrCirculationProcess 	= $arrOpenCirculations[$nIndex];
		$nCirculationProcessId 	= $arrCirculationProcess['nID'];
		$nCirculationFormId 	= $arrCirculationProcess['nCirculationFormId'];
		$nSlotId			 	= $arrCirculationProcess['nSlotId'];
		$nUserId 				= $arrCirculationProcess['nUserId'];
		$dateInProcessSince		= $arrCirculationProcess['dateInProcessSince'];
		$nIsSubtituteOf	 		= $arrCirculationProcess['nIsSubstitiuteOf'];
		$nCirculationHistoryId 	= $arrCirculationProcess['nCirculationHistoryId'];
		
		if ($nIsSubtituteOf == 0)
		{	// the current user is no substitute
			// get his substitutes
			$arrSubstitutes = $objCirculation->getSubstitutes($nUserId);
			if (sizeof($arrSubstitutes) > 0)
			{	// the user has one or more substitutes
				// set the default substitute time
				$tsCurrentSendSP = mktime(date("H"),date("i") - $SUBSTITUTE_PERSON_MINUTES,date("s"),date("m"), date("d"), date("Y"));
				
				// check the user's individual subtitute time
				if ($arrUsers[$nUserId]['nID'] != '')
				{
					$arrUser 	= $arrUsers[$nUserId];
					$SP_UNIT 	= $arrUser['strSubstituteTimeUnit'];
					$SP_VALUE	= $arrUser['nSubstituteTimeValue'];
					switch($SP_UNIT)
					{
						case 'DAYS':
							$SP_MINUTES = $SP_VALUE * 24 * 60;
							break;
						case 'HOURS':
							$SP_MINUTES = $SP_VALUE * 60;
							break;
						case 'MINUTES':
							$SP_MINUTES = $SP_VALUE;
							break;
					}
					
					$tsCurrentSendSP = mktime(date("H"),date("i") - $SP_MINUTES,date("s"),date("m"), date("d"), date("Y"));
				}
				
				if ($dateInProcessSince < $tsCurrentSendSP)
				{
					$nSubstituteId = $arrSubstitutes[0]['substitute_id'];
					
					if ($nSubstituteId == -3)
					{	// in this case the substitute is the sender of the circulation - let's find his user_id
						$arrSenderDetails = $objCirculation->getSenderDetails($nCirculationFormId);
						$nSubstituteId = $arrSenderDetails['nID'];
					}
					
					//--- change decission state
					$strQuery = "UPDATE cf_circulationprocess SET nDecissionState = '8', dateDecission = '$TStoday' WHERE nID = '$nCirculationProcessId'";
					mysql_query($strQuery, $nConnection);
							
					//--- send substitute mail
					sendToUser($nSubstituteId, $nCirculationFormId, $nSlotId, $nCirculationProcessId, $nCirculationHistoryId);
				}
			}
		}
		else
		{	// user is a substitute
			// let's see who this substitute belongs to
			// it's NOT saved in "nIsSubstituteOf" -.-

			$strQuery 	= "SELECT MAX(dateInProcessSince) as nMaxDateInProcessSince FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormId' AND nIsSubstitiuteOf = '0' AND dateInProcessSince < '$TSsendSP' LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			$strQuery 	= "SELECT nUserId FROM cf_circulationprocess WHERE nCirculationFormId = '$nCirculationFormId' AND dateInProcessSince = '".$arrResult['nMaxDateInProcessSince']."' LIMIT 1;";
			$result 	= mysql_query($strQuery, $nConnection);
			$arrResult 	= mysql_fetch_array($result, MYSQL_ASSOC);
			
			$nSubsUserId = $arrResult['nUserId'];
			
			$arrSubstitutes = $objCirculation->getSubstitutes($nSubsUserId);
			
			if (sizeof($arrSubstitutes) > 0)
			{	// the user has one or more substitutes
				$nMax2 = sizeof($arrSubstitutes);
				for ($nIndex2 = 0; $nIndex2 < $nMax2; $nIndex2++)
				{
					$nSubstituteId = $arrSubstitutes[$nIndex2]['substitute_id'];
					
					if ($nSubstituteId == -3)
					{	// in this case the substitute is the sender of the circulation - let's find his user_id
						$arrSenderDetails = $objCirculation->getSenderDetails($nCirculationFormId);
						$nSubstituteId = $arrSenderDetails['nID'];
					}
					
					if ($nSubstituteId == $nUserId)
					{	// found the current user - let's send the circulation to the next substitute in list (if exists)
						$nSubstituteId = $arrSubstitutes[($nIndex2+1)]['substitute_id'];
						
						if ($nSubstituteId == -3)
						{	// in this case the substitute is the sender of the circulation - let's find his user_id
							$arrSenderDetails = $objCirculation->getSenderDetails($nCirculationFormId);
							$nSubstituteId = $arrSenderDetails['nID'];
						}
						
						if ($nSubstituteId != '')
						{
							// set the default substitute time
							$tsCurrentSendSP = mktime(date("H"),date("i") - $SUBSTITUTE_PERSON_MINUTES,date("s"),date("m"), date("d"), date("Y"));
							
							// check the user's individual subtitute time
							if ($arrUsers[$nUserId]['nID'] != '')
							{
								$arrUser 	= $arrUsers[$nUserId];
								$SP_UNIT 	= $arrUser['strSubstituteTimeUnit'];
								$SP_VALUE	= $arrUser['nSubstituteTimeValue'];
								switch($SP_UNIT)
								{
									case 'DAYS':
										$SP_MINUTES = $SP_VALUE * 24 * 60;
										break;
									case 'HOURS':
										$SP_MINUTES = $SP_VALUE * 60;
										break;
									case 'MINUTES':
										$SP_MINUTES = $SP_VALUE;
										break;
								}
								
								$tsCurrentSendSP = mktime(date("H"),date("i") - $SP_MINUTES,date("s"),date("m"), date("d"), date("Y"));
							}
							
							if ($dateInProcessSince < $tsCurrentSendSP)
							{
								//--- change decission state
								$strQuery = "UPDATE cf_circulationprocess SET nDecissionState = '8', dateDecission = '$TStoday' WHERE nID = '$nCirculationProcessId'";
								mysql_query($strQuery, $nConnection);
										
								//--- send substitute mail
								sendToUser($nSubstituteId, $nCirculationFormId, $nSlotId, $nCirculationProcessId, $nCirculationHistoryId);
							}
						}
						$nIndex2 = $nMax2;
					}
				}
			}
		}
	}
	
?>