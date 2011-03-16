<?php

/**
 * URL encoding/ decoding Class
 *
 * @package    RPL
 * @subpackage Encryption
 * @author     Frieder Kesch <fkesch@projektphp.de>
 */
class RPL_Encryption_Url
{
	private $strBlowfishPassword;
	private $td;
	
	public function __construct()
	{
	}
	
	/**
	 * Sets $_GET based on the given encrypted URL Parameter String
	 * e.g.:
	 * given string: JUYO8YwVtKQAHbPGvXHm68xHGwVGHRE1W2CrB0bCipeEeJ8vkjVqwGERmLhbfdHOvOOJMmLch
	 * encrypted string: site=start&user=74&command=login&another_var=XyZZ
	 * this will set the following:
	 * $_GET['site'] = 'start';
	 * $_GET['site'] = '74';
	 * $_GET['site'] = 'login';
	 * $_GET['site'] = 'XyZZ';
	 *
	 * @param String $strEncryptedParams
	 * @return Array Contains the value to each key
	 */
	public function setParams($strEncryptedParams = false)
	{
		if ($strEncryptedParams)
		{
			$strParams = $this->decryptURL($strEncryptedParams);
			
			$arrSplit = split('&', $strParams);
			foreach ($arrSplit as $strParam)
			{
				$arrParamSplit = split('=', $strParam);
				
				// define the $_REQUEST Params
				$_REQUEST[$arrParamSplit[0]] = $arrParamSplit[1];
			}
		}
	}
	
	/**
	 * Encrypts a given String
	 *
	 * @param String $plaintext
	 * @return String The encrypted Version of the given String
	 */
	public function encryptURL($plaintext = false) 
	{
		if ($plaintext)
		{
			$objBlowfish 	= new RPL_Encryption_Blowfish($this->strBlowfishPassword);	
			$objBlowfish->setInputdata($plaintext);	
			$strEncoded 	= $objBlowfish->encodeBlowfish();
			
			return $strEncoded;
		}
	}
	
	/**
	 * Decrypts a given String
	 *
	 * @param String $crypttext
	 * @return String The decrypted Version of the given String
	 */
	private function decryptURL($crypttext = false) 
	{
		if ($crypttext)
		{
			//print_r($this);
			$objBlowfish 	= new RPL_Encryption_Blowfish($this->strBlowfishPassword);	
			$objBlowfish->setInputdata($crypttext);
			$strDecoded 	= $objBlowfish->decodeBlowfish();
			
			return $strDecoded;
		}
	}
	
	/**
	 * Set the Encryption Key
	 * 
	 * @param String $strPassword
	 */
	public function setPassword($strPassword)
	{
		$this->strBlowfishPassword = $strPassword;
	}
	
	/**
	 * checks if the boxes.js has to updated
	 *
	 * @param String $strFilename path to the boxes.js e.g. 'RPL/Encryption/boxes.js'
	 * @param Integer $URL_ENCODING_TS
	 */
	public function checkBoxes($strFilename, $URL_ENCODING_TS)
	{
		if (file_exists($strFilename))
		{			
			$tsLastUpdatePW = $URL_ENCODING_TS;
			$tsLastUpdateJS = filemtime($strFilename);
			
			if ($tsLastUpdatePW > $tsLastUpdateJS)
			{	// in this case the Password has changed --> the boxes have to be updated
				$this->writeBoxes($strFilename);
			}		   
		}
		else
		{
			$this->writeBoxes($strFilename);
		}
	}
	
	/**
	 * writes the current blowfish boxes to boxes.js
	 *
	 * @param String $strFilename path to the boxes.js e.g. 'RPL/Encryption/boxes.js'
	 */
	private function writeBoxes($strFilename)
	{
		if ( (!file_exists($strFilename)) OR (file_exists($strFilename) && is_writable($strFilename)) )
		{
			// get the Boxes
			//echo "writeBoxes=".$this->strBlowfishPassword."<br>";
			$objBlowfish = new RPL_Encryption_Blowfish($this->strBlowfishPassword);		
			$arrBctx 	= $objBlowfish->getBctx();			
			$arrP 		= $arrBctx['p'];
			$arrS0		= $arrBctx['sb'][0];
			$arrS1		= $arrBctx['sb'][1];
			$arrS2		= $arrBctx['sb'][2];
			$arrS3		= $arrBctx['sb'][3];
			
			for ($nIndex2 = 0; $nIndex2 < 5; $nIndex2 ++)
			{
				switch ($nIndex2)
				{
					case 0:
						$strBox = 'P';
						break;
					case 1:
						$strBox = 'S0';
						break;
					case 2:
						$strBox = 'S1';
						break;
					case 3:
						$strBox = 'S2';
						break;
					case 4:
						$strBox = 'S3';
						break;
				}
				
				$strCurArrTitle = 'arr'.$strBox;
				
				$strJSFile = $strJSFile."bf_".$strBox." 	= new Array(\n";
				$nMax = sizeof($$strCurArrTitle);
				for ($nIndex = 0; $nIndex < $nMax; $nIndex++)
				{
					$arrCurArray = $$strCurArrTitle;
					
					if(($nIndex % 30 == 0) && ($nIndex != 0))
					{
						$strJSFile = $strJSFile.$arrCurArray[$nIndex].",\n";
					}
					elseif($nIndex+1 != $nMax)
					{
						$strJSFile = $strJSFile.$arrCurArray[$nIndex].",";
					}
					else
					{
						$strJSFile = $strJSFile.$arrCurArray[$nIndex];
					}
				}
				$strJSFile = $strJSFile.");\n\n";
			}

			$handle = @fopen($strFilename, 'w+');
			if ($handle)
			{
				fwrite($handle, $strJSFile);
				fclose($handle);
			}			
		}		
	}
	
}
?>