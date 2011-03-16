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
	
	//--- write user to database
	include_once ("../config/config.inc.php");
	include_once ("../language_files/language.inc.php");

    function delete_file($file)
    {
        $delete = @unlink($file);
        clearstatcache();
        if (@file_exists($file)) 
        {
            $filesys = eregi_replace("/","\\",$file);
            $delete = @system("del $filesys");
            clearstatcache();
      
            if (@file_exists($file)) 
            {
                $delete = @chmod ($file, 0775);
                $delete = @unlink($file);
                $delete = @system("del $filesys");
            }
        }
        clearstatcache();
        if (@file_exists($file))
        {
            return false;
        }
        else
        {
            return true;
        }
    }  // end function
    
    function rmdirr($dirname)
	 {
	     // Sanity check
	     if (!file_exists($dirname)) {
	         return false;
	     }
	  
	     // Simple delete for a file
	     if (is_file($dirname) || is_link($dirname)) {
	         return unlink($dirname);
	     }
	  
	     // Loop through the folder
	     $dir = dir($dirname);
	     while (false !== $entry = $dir->read()) {
	         // Skip pointers
	         if ($entry == '.' || $entry == '..') {
	             continue;
	         }
	  
	         // Recurse
	         rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
	     }
	  
	     // Clean up
	     $dir->close();
	     return rmdir($dirname);
	 }
    $nCirculationFormID = $_REQUEST['cfid'];
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		//--- get maximum count of users
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			//deleting files of field type "file"
			
			$nCirculationFormID = $_REQUEST['cfid'];
			
			// --- get all Circulation History Information
				$query 		= "SELECT * FROM cf_circulationhistory WHERE nCirculationFormId = '$nCirculationFormID';";
				$result		= mysql_query($query) or die (mysql_error());
				
				$nIndex = 0;
				while ($arrRow = mysql_fetch_row($result))
				{
					$arrAllCirculationHistoy[$nIndex]['nID'] 				= $arrRow[0];
					$arrAllCirculationHistoy[$nIndex]['nRevisionNumber'] 	= $arrRow[1];
					$arrAllCirculationHistoy[$nIndex]['dateSending'] 		= $arrRow[2];
					$arrAllCirculationHistoy[$nIndex]['strAdditionalText'] 	= $arrRow[3];
					$arrAllCirculationHistoy[$nIndex]['nCirculationFormId']	= $arrRow[4];
					
					$nIndex++;
				}
			
			
			$nMax = sizeof($arrAllCirculationHistoy);
			for($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$arrCirculationHistory = $arrAllCirculationHistoy[$nIndex];
				
				// --- get all fieldvalues of current circulation history id
					$strQuery 	= "SELECT * FROM cf_fieldvalue WHERE nCirculationHistoryId = '".$arrCirculationHistory['nID']."';";
					$result		= mysql_query($strQuery) or die (mysql_error());
					
					$nIndexS = 0;
					while ($arrRow = mysql_fetch_row($result))
					{
						$arrAllFieldValues[$nIndexS]['nID'] 				= $arrRow[0];
						$arrAllFieldValues[$nIndexS]['nInputFieldId'] 		= $arrRow[1];
						$arrAllFieldValues[$nIndexS]['strFieldValue'] 		= $arrRow[2];
						$arrAllFieldValues[$nIndexS]['nSlotId'] 			= $arrRow[3];
						$arrAllFieldValues[$nIndexS]['nFormId']				= $arrRow[4];
						$arrAllFieldValues[$nIndexS]['nCirculationHistoryId'] = $arrRow[5];
						
						$nIndexS++;
					}
					
					$nMaxIn = sizeof($arrAllFieldValues);
					for($nIndexIn = 0; $nIndexIn < $nMaxIn; $nIndexIn++)
					{
						$arrCurFieldValue = $arrAllFieldValues[$nIndexIn];
						
						if ($arrCurFieldValue['strFieldValue']!='')
						{
							$arrSplitNo1 = split('---',$arrCurFieldValue['strFieldValue']);
							$arrSplit = split('_',$arrSplitNo1[2]);
							
							if(sizeof($arrSplit)==3)
							{
								$nNumberOfUploads 	= $arrSplitNo1[1];
								
								for ($nMyIndex = 0; $nMyIndex < $nNumberOfUploads; $nMyIndex++)
								{
									$nMyNumUploads = ($nMyIndex + 1);
									
									$strDirectory		= $arrSplitNo1[2].'_'.$nMyNumUploads;
									$strFilename		= $arrSplitNo1[3];
									//$strLink			= "../upload/".$strDirectory.'/'.$strFilename;
									$strLink			= "../upload/".$strDirectory;
									@rmdirr($strLink);
									//delete_file($strLink);
								}
							}
						}
					}
			}	
			
			$strQuery 	= "SELECT * FROM cf_circulationform WHERE nID = '$nCirculationFormID' LIMIT 1;";
			$nResult 	= @mysql_query($strQuery, $nConnection);
			$arrMyRow 	= mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			$nMyMailinglistID = $arrMyRow['nMailingListId'];
			
			$strQuery 	= "SELECT * FROM cf_mailinglist WHERE nID = '$nMyMailinglistID' LIMIT 1;";
			$nResult 	= @mysql_query($strQuery, $nConnection);
			$arrMyRow 	= mysql_fetch_array($nResult, MYSQL_ASSOC);
			
			if ($arrMyRow['bIsEdited'])
			{
				$query = "DELETE FROM cf_mailinglist WHERE nID = '$nMyMailinglistID' LIMIT 1;";
				$nResult = @mysql_query($query, $nConnection);
				
				$query = "DELETE FROM cf_slottouser WHERE nMailingListId = '$nMyMailinglistID'";
				$nResult = @mysql_query($query, $nConnection);
			}
			
			//--- delete the form
			$query = "DELETE FROM cf_circulationform WHERE nID=".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);
            
            //--- delete the Process history
            $query = "DELETE FROM cf_circulationprocess WHERE nCirculationFormId =".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);   
            
            //--- delete the values
            $query = "DELETE FROM cf_slottouser WHERE nCirculationId =".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);   
            
            $query = "SELECT nValueId FROM cf_slottovalue WHERE nFormId=".$_REQUEST["cfid"];
            $nResult = mysql_query($query, $nConnection);
            if ($nResult)
			{
                $arrValueIds = array();
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{	
					    $arrValueIds[] = $arrRow["nValueId"];	
					}				
				}
			}
            
			$query = "DELETE FROM cf_fieldvalue WHERE nFormId =".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);
			
            $query = "DELETE FROM cf_slottovalue WHERE nFormId =".$_REQUEST["cfid"];
			$nResult = mysql_query($query, $nConnection);   
            
            //--- delete the attachments
            $query = "SELECT cf_attachment.* FROM cf_attachment, cf_circulationhistory WHERE cf_attachment.nCirculationHistoryId=cf_circulationhistory.nID AND cf_circulationhistory.nCirculationFormId=".$_REQUEST["cfid"];
            $nResult = mysql_query($query, $nConnection);
            
            $arrAttachmentsToDelete = array();
            if ($nResult)
			{
                $arrValueIds = array();
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{	
						$arrAttachmentsToDelete[] = $arrRow["nID"];
                        delete_file($arrRow["strPath"]);					        
					}				
				}
            }
            
            foreach ($arrAttachmentsToDelete as $nAttachmentId)
            {
            	$query = "DELETE FROM cf_attachment WHERE nID =".$nAttachmentId;
				$nResult = mysql_query($query, $nConnection);   
            }
            
            //--- deleting the history
            $query = "DELETE FROM cf_circulationhistory WHERE nCirculationFormId=".$_REQUEST["cfid"];
            mysql_query($query, $nConnection);
		}
	}
?>
