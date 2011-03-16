<?php
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
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
	require_once 'CCirculation.inc.php';
	
	$objCirculation = new CCirculation();
	
	$nShowRows = 50;
	
	if ($_REQUEST['sortby'] == '')
	{
		$_REQUEST['sortby'] = 'strLastName';
	}
	if ($_REQUEST['sortdir'] == '')
	{
		$_REQUEST['sortdir'] = 'ASC';
	}
	
	function getSortDirection($strColumn)
    {
    	global $_REQUEST;
    	
    	if ($strColumn == $_REQUEST['sortby'])
    	{
    		if ($_REQUEST['sortdir'] == 'ASC')
    		{
    			return 'DESC';
    		}
    		else 
    		{
    			return 'ASC';
    		}
    	}
    	else 
    	{
    		return 'ASC';
    	}
    }
    
	function getColHighlight($nRow, $strCol)
    {
    	global $_REQUEST;
    	
    	if ($strCol == $_REQUEST['sortby'])
    	{
    		if (($nRow % 2) == 0)
    		{
    			return ' style="background-color: #F4E8C2;" ';	
    		}
    		else 
    		{
    			return ' style="background-color: #FFF7DE;" ';	
    		}
    	}
    }
?>

<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<title></title>	
	<link rel="stylesheet" href="format.css" type="text/css">
	<script src="../lib/prototype/prototype.js" type="text/javascript"></script>
	<script LANGUAGE="JavaScript" TYPE="text/javascript" SRC="tooltip.js"></script>	
	<script language="JavaScript">
	<!--
		function deleteUser(nUserId)
		{
			Check = confirm("<?php echo $USER_MNGT_ASKDELETE;?>\n\n" + "<?php echo $USER_MNGT_ASKDELETE_PART2;?>\n" + "<?php echo $USER_MNGT_ASKDELETE_PART3;?>");
			if(Check == true) 
			{
				location.href="deleteuser.php?userid="+nUserId+"&language=<?php echo $_REQUEST["language"]?>&sortby=<?php echo $_REQUEST["sortby"]?>&start=<?php echo $_REQUEST["start"]?>";
			}
		}
	//-->
	</script>
</head>
<?php
	include_once ("../lib/datetime.inc.php");
    include_once ("../lib/viewutils.inc.php");
	
	//--- open database
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	
	if ($nConnection)
	{
		//--- get maximum count of users
		if (mysql_select_db($DATABASE_DB, $nConnection))
		{
			$query = "select COUNT(*) from cf_user WHERE bDeleted = 0";
			$nResult = mysql_query($query, $nConnection);

			if ($nResult)
			{
				if (mysql_num_rows($nResult) > 0)
				{
					while (	$arrRow = mysql_fetch_array($nResult))
					{	
						$nUserCount = $arrRow[0];
					}				
				}
			}
		}
		
		if ($nUserCount > $_REQUEST['start'] + $nShowRows)
		{
			$end = $_REQUEST['start'] + ($nShowRows - 1);
		}
		else
		{
			if (($_REQUEST['start'] + $nShowRows) > $nUserCount)
			{
				$end = $nUserCount;
			}
			else
			{
				$end = $_REQUEST['start'] + $nShowRows;
			}
		}
		
		//--- get all users
		$arrUsers = array();
		
		$start = $_REQUEST['start']-1;
		
		if ($_REQUEST['sortby'] == 'tsLastAction')
		{
			if ($sortdir == 'DESC') 
			{
				$sortdir = 'ASC';
			}
			else
			{
				$sortdir = 'DESC';
			}
		}
		else
		{
			$sortdir = $_REQUEST['sortdir'];
		}
		
		$strQuery = "SELECT * FROM cf_user WHERE bDeleted = '0' ORDER BY ".$_REQUEST['sortby']." $sortdir LIMIT $start, $nShowRows";
		$nResult = mysql_query($strQuery, $nConnection);
		
		if ($nResult)
		{
			if (mysql_num_rows($nResult) > 0)
			{
				$nRunningNumber = $_REQUEST['start'];
				while (	$arrRow = mysql_fetch_array($nResult))
				{
					$arrUsers[$arrRow["nID"]] = $arrRow;
				}
			}
		}	
	}

?>
<body>
	<br>
	<span style="font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_USERMNG;?>
	</span>
	<br><br>

	<table width="90%" cellspacing="0" cellpadding="3">
		<tr>
			<td align="left" width="14px">
				<a href="edituser.php?language=<?php echo $_REQUEST['language'];?>&userid=-1&sortby=<?php echo $_REQUEST['sortby']?>&start=<?php echo $_REQUEST['start']?>"><img src="../images/adduser.gif" border="0"></a>
			</td>
			<td align="left">
				[ <a href="edituser.php?language=<?php echo $_REQUEST['language'];?>&userid=-1&sortby=<?php echo $_REQUEST['sortby']?>&start=<?php echo $_REQUEST['start']?>"><?php echo $USER_MNGT_ADDUSER;?></a> ]
			</td>
		</tr>
	</table>
	<br>

	<table width="90%" style="border: 1px solid #c8c8c8;" cellspacing="0" cellpadding="2">
		<tr>
			<td class="table_header" width="20">#</td>
			<td class="table_header"><a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=1&sortby=strLastName&sortdir=<?php echo getSortDirection('strLastName'); ?>" style="color: #fff;"><?php echo $USER_MNGT_LASTNAME;?></a></td>
			<td class="table_header"><a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=1&sortby=strFirstName&sortdir=<?php echo getSortDirection('strFirstName'); ?>" style="color: #fff;"><?php echo $USER_MNGT_FIRSTNAME;?></a></td>
			<td class="table_header"><a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=1&sortby=strUserId&sortdir=<?php echo getSortDirection('strUserId'); ?>" style="color: #fff;"><?php echo $LOGIN_USERID ?></a></td>
			<td class="table_header"><a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=1&sortby=strEMail&sortdir=<?php echo getSortDirection('strEMail'); ?>" style="color: #fff;"><?php echo $USER_MNGT_EMAIL ?></a></td>
			<td class="table_header" align="center"><a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=1&sortby=tsLastAction&sortdir=<?php echo getSortDirection('tsLastAction'); ?>" style="color: #fff;"><?php echo $USER_ONLINE_STATE ?></a></td>
			<td class="table_header" align="center"><?php echo $USER_MNGT_ADMINACCESS;?></td>
			<td class="table_header"><?php echo $USER_MNGT_SUBSTITUDE;?></td>
			<td class="table_header" align="center" width="70" nowrap><?php echo $TABLE_OPTIONS;?></td>
		</tr>
		<?php
			//--- output the user inbetween the range (start to end)
			foreach ($arrUsers as $arrRow)
			{	
				$style = "background-color: #efefef;";
				if ($nRunningNumber%2 == 1)
				{
					$style = "background-color: #fff;";
				}
				
				$strImage = '../images/inactive.gif';
				if (($arrRow["nAccessLevel"] & 2) == 2)
				{
					$strImage = '../images/active.gif';
				}
				
				if ($arrRow["nSubstitudeId"] == -2)
				{
					$arrSubstitutes = $objCirculation->getSubstitutes($arrRow['nID']);
					if ($arrSubstitutes[0]['substitute_id'] == -3)
					{
						$strSubstitute = $SELF_DELEGATE_USER;
					}
					else
					{
						$strSubstitute 	= $objCirculation->getUsername($arrSubstitutes[0]['substitute_id']);
					}
				}
				elseif ($arrRow["nSubstitudeId"] != 0)
				{
					$strSubstitute 	= $objCirculation->getUsername($arrRow['nSubstitudeId']);
				}
				else
				{
					$strSubstitute = '-';
				}
				
				$strOnlineState = '';
				if (($arrRow['tsLastAction'] + $USER_TIMEOUT) > time())
				{
					$strOnlineState = '<img src="../images/flag_green.gif">';
				}
				?>
				<tr valign="top" style="<?php echo $style ?>">
					<td nowrap><?php echo $nRunningNumber ?></td>
					<td nowrap <?php echo getColHighlight($nRunningNumber, 'strLastName') ?>><?php echo $arrRow['strLastName'] ?></td>
					<td nowrap <?php echo getColHighlight($nRunningNumber, 'strFirstName') ?>><?php echo $arrRow['strFirstName'] ?></td>
					<td nowrap <?php echo getColHighlight($nRunningNumber, 'strUserId') ?>><?php echo $arrRow['strUserId'] ?></td>
					<td nowrap <?php echo getColHighlight($nRunningNumber, 'strEMail') ?>><?php echo $arrRow['strEMail'] ?></td>
					<td nowrap align="center" <?php echo getColHighlight($nRunningNumber, 'tsLastAction') ?>><?php echo $strOnlineState ?></td>
					<td align="center"><img src="<?php echo $strImage ?>" height="16" width="16"></td>
					<td nowrap><?php echo $strSubstitute ?></td>
					<td align="center">
						<a href="javascript:deleteUser(<?php echo $arrRow[0] ?>)" onMouseOver="tip('delete')" onMouseOut="untip()"><img src="../images/edit_remove.gif" border="0" height="16" width="16" style="margin-right: 4px;"></a>
						<a href="edituser.php?userid=<?php echo $arrRow[0] ?>&language=<?php echo $_REQUEST['language'] ?>&sortby=<?php echo $_REQUEST['sortby'] ?>&sortdir=<?php echo $_REQUEST['sortdir'] ?>&start=<?php echo $_REQUEST['start'] ?>" onMouseOver="tip('detail')" onMouseOut="untip()" alt="Editieren"><img src="../images/edit.png" border="0" height="16" width="16"></a>
					</td>
				</tr>
				
				<?php
				$nRunningNumber++;
			}
		?>
	</table>
		<table width="90%">
			<tr>
				<td>
					<?php
					
					$nStart = $_REQUEST['start'];
					$nEnd 	= $end;
					$nMax 	= $nUserCount;
					
					$strRange = str_replace("_%From", $nStart, $USER_MNGT_SHOWRANGE);
					$strRange = str_replace("_%To", $nEnd, $strRange);
					$strRange = str_replace("_%Off", $nMax, $strRange);
					
					echo $strRange;
					?>		
				</td>
				<td align="right">
					<?php
						if ($_REQUEST["start"] > $nShowRows)
						{
							?>
								<a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=<?php echo $_REQUEST['start'] - $nShowRows;?>&sortby=<?php echo $_REQUEST['sortby'] ?>&sortdir=<?php echo $_REQUEST['sortdir'] ?>"><?php echo $BTN_BACK;?></a>
							<?php
						}
						
						if ($end < $nUserCount)
						{
							?>
								<a href="showuser.php?language=<?php echo $_REQUEST['language'] ?>&start=<?php echo $_REQUEST['start'] + $nShowRows;?>&sortby=<?php echo $_REQUEST['sortby'] ?>&sortdir=<?php echo $_REQUEST['sortdir'] ?>"><?php echo $BTN_NEXT;?></a>
							<?php
						}
					?>
				</td>
			</tr>
		</table><br>

	<script type="text/javascript">
    	maketip('delete','<?php echo escapeSingle($USER_TIP_DELETE);?>');
    	maketip('detail','<?php echo escapeSingle($USER_TIP_DETAIL);?>');
	</script>
</body>
</html>
<?php
	//--- close database	
	mysql_close($nConnection);
?>