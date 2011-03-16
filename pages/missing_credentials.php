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
require_once '../language_files/language.inc.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>
	<link rel="stylesheet" href="format.css" type="text/css">
</head>
<body style="padding: 0px; margin: 0px;">
	<div align="center">
		<br>
		<br>
		<br>
		<table style="background-color:#FFEBC1;border:2px solid #FDBF3B;">
			<tr>
				<td style="font-weight: bold; padding: 10px;"><?php echo $MISSING_CREDENTIALS;?></td>
			</tr>
		</table>
		<br>
		<br>
		<strong style="font-size:8pt;font-weight:normal">powered by</strong><br>
		<a href="http://cuteflow.org" target="_blank"><img src="../images/cuteflow_logo_small.png" border="0" /></a><br>
		<strong style="font-size:8pt;font-weight:normal">Version <?php echo $CUTEFLOW_VERSION ?></strong><br> 
	</div>
</body>
</html>