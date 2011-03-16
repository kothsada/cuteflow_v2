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
	
	/**
	* Function converts an Javascript escaped string back into a string with specified charset (default is UTF-8).
	*
	* @param string $source escaped with Javascript's escape() function
	* @param string $iconv_to destination character set will be used as second paramether in the iconv function. Default is UTF-8.
	* @return string
	*/
	function unescape($source, $iconv_to = 'UTF-8')
	{
		$decodedStr = '';
		$pos = 0;
		$len = strlen ($source);
		while ($pos < $len) 
		{
			$charAt = substr ($source, $pos, 1);
			if ($charAt == '%') 
			{
				$pos++;
				$charAt = substr ($source, $pos, 1);
				if ($charAt == 'u') 
				{
					// we got a unicode character
					$pos++;
					$unicodeHexVal = substr ($source, $pos, 4);
					$unicode = hexdec ($unicodeHexVal);
					$decodedStr .= code2utf($unicode);
					$pos += 4;
				}
				else 
				{
					// we have an escaped ascii character
					$hexVal = substr ($source, $pos, 2);
					$decodedStr .= chr (hexdec ($hexVal));
					$pos += 2;
				}
			}
			else 
			{
				$decodedStr .= $charAt;
				$pos++;
			}
		}
		if ($iconv_to != "UTF-8") 
		{
			$decodedStr = iconv("UTF-8", $iconv_to, $decodedStr);
		}
		return $decodedStr;
	}
	
	require_once '../../config/config.inc.php';
	require_once '../../config/db_connect.inc.php';
	require_once '../../language_files/language.inc.php';
	require_once '../../pages/CCirculation.inc.php';
	
	header("Content-Type: text/xml; charset=$DEFAULT_CHARSET");
	
	$strFiter = strip_tags($_REQUEST['strFilter']);
	
	$strFiter = ltrim(unescape($strFiter, $DEFAULT_CHARSET));
	
	$objCirculation = new CCirculation();
	
	$arrIndex = $objCirculation->filterUsers($strFiter);
?>
	<table cellpadding="2" cellspacing="0" style="background-color:white;" width="100%">
		<tbody id="AvailableUsers">
			<?php
			$nMax = sizeof($arrIndex);
			for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
			{
				$arrCurIndex 	= $arrIndex[$nIndex];
				$nUserId 		= $arrCurIndex['user_id'];
				$arrUser		= $objCirculation->getUserById($nUserId);
				
				$sid			= $nUserId;
				?>
				<tr onMouseOver="this.style.background = '#ddd;'" onMouseOut="this.style.background = '#fff;'" onClick="document.getElementById('receiver_<?php echo $sid ?>').checked = 'true'; setReceiver(<?php echo $sid ?>);" style="cursor: pointer;">
					<td width="16px" style="border-top:1px solid Silver;" valign="middle">
						<input type="radio" name="receiver" id="receiver_<?php echo $sid ?>" value="<?php echo $sid ?>">
					</td>
					<td width="20px" style="border-top:1px solid Silver;" valign="middle">
						<img src="../../images/singleuser.gif" height="19" width="16">
					</td>
					<td style="border-top:1px solid Silver;" valign="middle">
						<?php echo htmlentities($arrUser['strUserId']) ?>
					</td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>