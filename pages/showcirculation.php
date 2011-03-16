<?php
	session_start();
    
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';
	
	$nCurUserID = $_SESSION["SESSION_CUTEFLOW_USERID"];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=<?php echo $DEFAULT_CHARSET ?>">
	<link rel="stylesheet" href="format.css" type="text/css">
	<style>		
		tr.rowEven
		{
			background-color: #efefef;
		}
		
		tr.rowUneven
		{
			background-color: #fff;
		}
		
		td.highlight_bright
		{
			background-color: #F4E8C2;
		}
		
		td.highlight_dark
		{
			background-color: #FFF7DE;
		}		
	</style>
	<script src="../lib/prototype/prototype.js" type="text/javascript"></script>
	
	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="../lib/calendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
	<!-- main calendar program -->
	<script type="text/javascript" src="../lib/calendar/calendar.js"></script>
	<!-- language for the calendar -->
	<script type="text/javascript" src="../lib/calendar/lang/calendar-en.js"></script>
	<!-- the following script defines the Calendar.setup helper function, which makes
	       adding a calendar a matter of 1 or 2 lines of code. -->
	<script type="text/javascript" src="../lib/calendar/calendar-setup.js"></script>
	<script type="text/javascript" src="tooltip.js"></script>	
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script type="text/javascript">
	  	maketip('delete','<?php echo escapeSingle($CIRCULATION_TIP_DELETE);?>');
	  	maketip('edit','<?php echo escapeSingle($CIRCULATION_TIP_EDIT);?>');
	  	maketip('detail','<?php echo escapeSingle($CIRCULATION_TIP_DETAIL);?>');
		maketip('archive','<?php echo escapeSingle($CIRCULATION_TIP_ARCHIVE);?>');
		maketip('unarchive','<?php echo escapeSingle($CIRCULATION_TIP_UNARCHIVE);?>');
		maketip('stop','<?php echo escapeSingle($CIRCULATION_TIP_STOP);?>');
		maketip('restart','<?php echo escapeSingle($CIRCULATION_TIP_RESTART);?>');
	</script>
	<script language="JavaScript">
	<!--
		function showCirculationDetails(nCircId, bSeperateWindow)
		{
			var strParams	= "language=<?php echo $_REQUEST['language']; ?>&circid=" + nCircId;
			inpdata	= strParams;
			encodeblowfish();
			
			if (bSeperateWindow)
			{
				destination = "circulation_detail.php?key=" + outdata;
				newWindow = window.open(destination, "Cuteflow", "width=900,height=600,left=100,top=200,status=yes,scrollbars=yes,resizable=yes,menubar=yes,toolbar=yes,location=yes");
				newWindow.focus();
			}
			else
			{
				location.href = "circulation_detail.php?key=" + outdata;
			}
		}
		
		var nCurID;
		
		function setCurID(myID)
		{
			nCurID = myID;
		}
		
		function editCirculation(nCirculationId)
		{	
			var strParams	= 'cfid=' + nCirculationId + '&language=<?php echo $_REQUEST['language']?>';
			inpdata	= strParams;
			encodeblowfish();
			
			location = 'editworkflow_standalone.php?key=' + outdata;
		}
		
		function deleteCirculation(nCirculationId, nStart)
		{
			Check = confirm("<?php echo $CIRCULATION_MNGT_ASKDELETE;?>");
			if(Check == true) 
			{
				var strParams	= "cfid="+nCirculationId+"&language=<?php echo $_REQUEST['language']?>&archivemode=<?php echo $_REQUEST['archivemode'];?>&sortDirection=<?php echo $_REQUEST['sortDirection'];?>&sortby=<?php echo $_REQUEST['sortby']?>&start=<?php echo $_REQUEST['start']?>";
				inpdata	= strParams;
				encodeblowfish();
				strParams = "key=" + outdata;
				
				new Ajax.Request
				(
					"ajax_deletecirculation.php",
					{
						onSuccess : function(resp) 
						{
							sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', nStart);
						},
				 		onFailure : function(resp) 
				 		{
				   			alert("Oops, there's been an error.");
				 		},
				 		parameters : strParams
					}
				);
			}
		}
		
		function archiveCirculation(nCirculationId, nArchiveBit, nStart)
		{
			var strParams	= 'circid=' + nCirculationId + '&archivebit=' + nArchiveBit + '&language=<?php echo $_REQUEST['language'] ?>';
			
			new Ajax.Request
			(
				"ajax_archive_circulation.php",
				{
					onSuccess : function(resp) 
					{
						sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', nStart);
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : strParams
				}
			);
		}
		
		function stopCirculation(nCirculationId, nStart)
		{
			var strParams	= 'circid=' + nCirculationId + '&language=<?php echo $_REQUEST['language'] ?>';
			
			new Ajax.Request
			(
				"ajax_stop_circulation.php",
				{
					onSuccess : function(resp) 
					{
						sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', nStart);
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : strParams
				}
			);
		}
		
		function checkFilterView()
		{
			if (document.getElementById('filter').style.display == 'none')
			{
				document.getElementById('filter').style.display = 'block';
			}
			else
			{
				document.getElementById('filter').style.display = 'none';
				document.getElementById('choose_filter').style.display = 'none';
			}
		}
		
		function addCustom()
		{
			var addMe = document.getElementById('custom_add').innerHTML;
			
			var addTest = replaceIt(addMe, 'REPLACE', nCurID);
			
			new Insertion.Bottom('custom', addTest);
			
			nCurID++;
		}
		
		function removeCustom(nID)
		{
			var TableID = 'FILTERCustom_TableID--' + nID;
			objRemove = document.getElementById(TableID);
			
			if (objRemove)
			{
				objRemove.parentNode.removeChild(objRemove);
			}
		}
		
		 function replaceIt(string,suchen,ersetzen) 
		 {
			ausgabe = "" + string;
			while (ausgabe.indexOf(suchen)>-1) {
			pos= ausgabe.indexOf(suchen);
			ausgabe = "" + (ausgabe.substring(0, pos) + ersetzen +
			ausgabe.substring((pos + suchen.length), ausgabe.length));
			}
			return ausgabe;
		 }
		 
		 function addFilter()
		 {
		 	WindowObjectReference = window.open(
				'showcirculation_filter_add.php?language=<?php echo $_REQUEST['language']; ?>&nCurCuserID=<?php echo $_SESSION["SESSION_CUTEFLOW_USERID"]; ?>',
				'add_filter',
				'width=310,height=200,resizable,scrollbars=no,status=1'
				);	
		 }
		 
		 function selectDeleteFilter()
		 {
		 	WindowObjectReference = window.open(
				'showcirculation_filter_delete.php?language=<?php echo $_REQUEST['language']; ?>&nCurCuserID=<?php echo $_SESSION["SESSION_CUTEFLOW_USERID"]; ?>',
				'delete_filter',
				'width=310,height=250,resizable=no,scrollbars=no,status=1'
				);
		 }
		 
		 function deleteFilter(nFilterID)
		 {		 	
		 	new Ajax.Request
			(
				"showcirculation_filter_delete_write.php",
				{
					onSuccess : function(resp) 
					{
						document.getElementById('choose_filter').style.display = 'none';
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "nFilterID=" + nFilterID
				}
			);
		 }
		 
		 function resetFilter()
		 {
		 	document.getElementById('FILTER_Name').value = "";
			document.getElementById('FILTER_Station').value = 0;
			document.getElementById('FILTER_Sender').value = 0;
			document.getElementById('FILTER_DaysInProgress_Start').value = "";
			document.getElementById('FILTER_DaysInProgress_End').value = "";
			document.getElementById('FILTER_Date_Start').value = "";
			document.getElementById('FILTER_Date_End').value = "";
			document.getElementById('FILTER_Mailinglist').value = 0;
			document.getElementById('FILTER_Template').value = 0;
			
			for (i = 20; i >= 0; i--)
			{
				removeCustom(i);
			}
			setCurID(0);
			
			addCustom();
		 }
	//-->
	</script>
</head>
<body><br>
<table cellspacing="0" cellpadding="0" width="700">
<tr>
	<td align="left" style="padding-right: 20px;">
	<span style="float: left;font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php 
		if ($_REQUEST['archivemode'] == 0)
		{
			if ($_REQUEST['bOwnCirculations'])
			{
				echo $MENU_OWN_CIRCULATIONS;
			}
			else
			{
				echo $MENU_CIRCULATION;
			}
		}
		else
		{
			echo $MENU_ARCHIVE;
		}
		?>
	</span>
	<div style="display: none; margin-top: 5px; marggin-left: 25px;" id="loading">
		<table cellspacing="0" cellpadding="0" style="background-color: #ffffff; margin-left: 15px;">
		<tr>
			<td align="left" valign="middle">
				<img src="../images/loading_moz.gif" hspace="3">
			</td>
			<td align="left" valign="top">
				<?php echo $LOADING_DATA;?>
			</td>
		</tr>
		</table>
	</div>
	</td>
</tr>
</table>
<br>
<?php

	$language		= $_REQUEST['language'];
	$archivemode	= $_REQUEST['archivemode'];
	$sortDirection	= $_REQUEST['sortDirection'];
	$sortby			= $_REQUEST['sortby'];
	$start			= $_REQUEST['start'];
	$nShowRows		= $_REQUEST['nShowRows'];
	
	if ($nShowRows == "")
	{
		$nShowRows	= $SHOWROWS;
	}
	
	if ($sortby == "")
	{
		$sortby = $DEFAULT_SORT_COL;	
	}
	
	if ($sortDirection == "")
	{
		$sortDirection = $SORTDIRECTION;	
	}
?>
	<script language="javascript">
	<!--
		var bFirstStart = <?php echo $_REQUEST['bFirstStart']; ?>
				
		if (bFirstStart == true)
		{
			<?php
			if ($_REQUEST['bOwnCirculations'])
			{
				?>
				window.setTimeout("sortResult('DESC','COL_CIRCULATION_PROCESS_DAYS','1')", 1000);
				<?php
			}
			else
			{
				?>
				window.setTimeout("sortResult('ASC','<?php echo $DEFAULT_SORT_COL; ?>','1')", 1000);
				<?php
			}
			?>
		}
		
		
		function saveFilter(Value, nCurUserID)
		 {
		 	var strCurFilterLabel 	= Value;
		 	var nCurUserID 			= nCurUserID;
		 	
		 	var FILTER_Name 				= document.getElementById('FILTER_Name').value;
			var FILTER_Station 				= document.getElementById('FILTER_Station').value;
			var FILTER_Sender				= document.getElementById('FILTER_Sender').value;
			var FILTER_DaysInProgress_Start = document.getElementById('FILTER_DaysInProgress_Start').value;
			var FILTER_DaysInProgress_End 	= document.getElementById('FILTER_DaysInProgress_End').value;
			var FILTER_Date_Start 			= document.getElementById('FILTER_Date_Start').value;
			var FILTER_Date_End 			= document.getElementById('FILTER_Date_End').value;
			var FILTER_Mailinglist 			= document.getElementById('FILTER_Mailinglist').value;
			var FILTER_Template 			= document.getElementById('FILTER_Template').value;
			
			var strCustom_No1 = '&FILTER_nUserID=' + nCurUserID + '&FILTER_strLabel=' + strCurFilterLabel + '&FILTER_Name=' + FILTER_Name + '&FILTER_Sender=' + FILTER_Sender + '&FILTER_Station=' + FILTER_Station + '&FILTER_DaysInProgress_Start=' + FILTER_DaysInProgress_Start + '&FILTER_DaysInProgress_End=' + FILTER_DaysInProgress_End + '&FILTER_Date_Start=' + FILTER_Date_Start + '&FILTER_Date_End=' + FILTER_Date_End + '&FILTER_Mailinglist=' + FILTER_Mailinglist + '&FILTER_Template=' + FILTER_Template;
			
			var strCustom = '';
			
			var nMax = 1;
			for (nIndex = 0; nIndex < nMax; nIndex++)
			{
				strFIELD_CurField 		= 'FILTERCustom_Field--' + nIndex;
				strFIELD_CurOperator 	= 'FILTERCustom_Operator--' + nIndex;
				strFIELD_CurValue 		= 'FILTERCustom_Value--' + nIndex;
										
				if (document.getElementById(strFIELD_CurValue))
				{
					strCurField 	= document.getElementById(strFIELD_CurField).value;
					strCurOperator 	= document.getElementById(strFIELD_CurOperator).value;
					strCurValue 	= document.getElementById(strFIELD_CurValue).value;
					
					strCustom = strCustom + '&' + strFIELD_CurField + '=' + strCurField;
					strCustom = strCustom + '&' + strFIELD_CurOperator + '=' + strCurOperator;
					strCustom = strCustom + '&' + strFIELD_CurValue + '=' + strCurValue;
					
					nMax++;
				}
			}
			
			//alert (strCustom_No1);
			
			new Ajax.Request
			(
				"showcirculation_filter_write.php",
				{
					onSuccess : function(resp) 
					{
						//--- nothing to do
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "language=<?php echo $_REQUEST['language']; ?>" + strCustom_No1 + strCustom
				}
			);
		 }
		 
		 
		
		function sortResult(sortDirection, strSortBy, nStart)
		{
			doCronjob();
			
			document.getElementById('loading').style.display = 'block';
			var strLanguage 	= document.getElementById('language').value;
			var nArchivemode 	= document.getElementById('archivemode').value;
			var nAccessLevel	= document.getElementById('nAccessLevel').value;
			
			if (sortDirection == '')
			{
				sortDirection 	= document.getElementById('sortDirection').value;
				strSortBy 		= document.getElementById('sortby').value;
			}
			else
			{
				document.getElementById('sortDirection').value = sortDirection;
				document.getElementById('sortby').value = strSortBy;
			}
			
			if (nStart == '')
			{
				nStart 			= document.getElementById('start').value;
			}
			else
			{
				document.getElementById('start').value = nStart;
			}
			
			var NumRows = document.getElementById('IN_nShowRows').value;
						
			if (NumRows != '')
			{
				if ((document.getElementById('filter').style.display != 'block') && ('<?php echo $_REQUEST['bOwnCirculations'] ?>' != 1))
				{
					new Ajax.Request
					(
						"showcirculation_sorted.php",
						{
							onSuccess : function(resp) 
							{
								document.getElementById('div_content').innerHTML = resp.responseText;
								document.getElementById('loading').style.display = 'none';
							},
					 		onFailure : function(resp) 
					 		{
					   			alert("Oops, there's been an error.");
					 		},
					 		parameters : "language=" + strLanguage + "&archivemode=" + nArchivemode + "&start=" + nStart + "&nShowRows=" + NumRows + "&sortDirection=" + sortDirection + "&sortby=" + strSortBy + "&nAccessLevel=" + nAccessLevel + "&bFilterOn=0"
						}
					);
				}
				else
				{
					var bFilterOn = 1;
					var FILTER_Name 				= (document.getElementById('FILTER_Name').value);
					var FILTER_Station 				= document.getElementById('FILTER_Station').value;
					var FILTER_Sender				= document.getElementById('FILTER_Sender').value;
					var FILTER_DaysInProgress_Start = document.getElementById('FILTER_DaysInProgress_Start').value;
					var FILTER_DaysInProgress_End 	= document.getElementById('FILTER_DaysInProgress_End').value;
					var FILTER_Date_Start 			= document.getElementById('FILTER_Date_Start').value;
					var FILTER_Date_End 			= document.getElementById('FILTER_Date_End').value;
					var FILTER_Mailinglist 			= document.getElementById('FILTER_Mailinglist').value;
					var FILTER_Template 			= document.getElementById('FILTER_Template').value;
					var bOwnCirculations			= 0;

					if ('<?php echo $_REQUEST['bOwnCirculations'] ?>' == 1)
					{
						FILTER_Station = <?php echo $_SESSION["SESSION_CUTEFLOW_USERID"] ?>;
						bOwnCirculations = 1;
					}
					
					var strCustom = '&bFilterOn=1&FILTER_Name=' + FILTER_Name + '&FILTER_Station=' + FILTER_Station + '&FILTER_Sender='+ FILTER_Sender+'&FILTER_DaysInProgress_Start=' + FILTER_DaysInProgress_Start + '&FILTER_DaysInProgress_End=' + FILTER_DaysInProgress_End + '&FILTER_Date_Start=' + FILTER_Date_Start + '&FILTER_Date_End=' + FILTER_Date_End + '&FILTER_Mailinglist=' + FILTER_Mailinglist + '&FILTER_Template=' + FILTER_Template + '&bOwnCirculations=' + bOwnCirculations;
					
					var nMax = 1;
					for (nIndex = 0; nIndex < nMax; nIndex++)
					{
						strFIELD_CurField 		= 'FILTERCustom_Field--' + nIndex;
						strFIELD_CurOperator 	= 'FILTERCustom_Operator--' + nIndex;
						strFIELD_CurValue 		= 'FILTERCustom_Value--' + nIndex;
												
						if (document.getElementById(strFIELD_CurValue))
						{
							strCurField 	= document.getElementById(strFIELD_CurField).value;
							strCurOperator 	= document.getElementById(strFIELD_CurOperator).value;
							strCurValue 	= document.getElementById(strFIELD_CurValue).value;
							
							strCustom = strCustom + '&' + strFIELD_CurField + '=' + strCurField;
							strCustom = strCustom + '&' + strFIELD_CurOperator + '=' + strCurOperator;
							strCustom = strCustom + '&' + strFIELD_CurValue + '=' + strCurValue;
							
							nMax++;
						}
					}
					
					new Ajax.Request
					(
						"showcirculation_sorted.php",
						{
							onSuccess : function(resp) 
							{
								document.getElementById('div_content').innerHTML = resp.responseText;
								document.getElementById('loading').style.display = 'none';
							},
					 		onFailure : function(resp) 
					 		{
					   			alert("Oops, there's been an error.");
					 		},
					 		parameters : "language=" + strLanguage + "&archivemode=" + nArchivemode + "&start=" + nStart + "&nShowRows=" + NumRows + "&sortDirection=" + sortDirection + "&sortby=" + strSortBy + "&nAccessLevel=" + nAccessLevel + strCustom
						}
					);
					
				}
			}
			else
			{
				document.getElementById('loading').style.display = 'none';
			}
		}		
		
		var objTimer = null;
		function changeRows()
		{			
	        if (objTimer) {
				window.clearTimeout(objTimer);
			}
			objTimer = window.setTimeout("doChangeRows()", 500);
		}
		
		function doChangeRows()
		{
			var sortDirection 	= document.getElementById('sortDirection').value;
			var strSortBy 		= document.getElementById('sortby').value;
			sortResult(sortDirection, strSortBy, '1');
		}
		
		var bReloadMe = 'false';
		var sMyTimeout = <?php echo $AUTO_RELOAD_SEC; ?> * 1000;
		
		function reload_loadpage()
		{
			sortDirection 	= document.getElementById('sortDirection').value;
			strSortBy 		= document.getElementById('sortby').value;
			nStart 			= document.getElementById('start').value;
			
			if (bReloadMe == 'true')
			{
				sortResult(sortDirection, strSortBy, nStart);
			}
			reload_timeout(bReloadMe);
		}
		
		function reload_loadnopage()
		{			
			reload_timeout(bReloadMe);
		}
		
		function reload_timeout(bRL)
		{			
			if(bRL == 'true')
			{
				window.setTimeout("reload_loadpage()", sMyTimeout);
			}
			else
			{
				window.setTimeout("reload_loadnopage()", sMyTimeout);
			}
		}
		
		function reload_activate()
		{			
			if(bReloadMe == 'false')
			{
				new Ajax.Request
				(
					"showcirculation_setcookie.php",
					{
						onSuccess : function(resp) 
						{
							bReloadMe = 'true';
							reload_timeout(bReloadMe);
						},
				 		onFailure : function(resp) 
				 		{
				   			alert("Oops, there's been an error.");
				 		},
				 		parameters : "nReloadTimeout=true"
					}
				);
			}
			else
			{
				new Ajax.Request
				(
					"showcirculation_setcookie.php",
					{
						onSuccess : function(resp) 
						{
							bReloadMe = 'false';
							reload_timeout(bReloadMe);
						},
				 		onFailure : function(resp) 
				 		{
				   			alert("Oops, there's been an error.");
				 		},
				 		parameters : "nReloadTimeout=false"
					}
				);
			}
		}	
		
		
		function changeFilter(nFilterID)
		 {
		 	new Ajax.Request
			(
				"showcirculation_filter_load.php",
				{
					onSuccess : function(resp) 
					{
						var response = resp.responseText;
						document.getElementById('choose_filter').style.display = 'none';
						document.getElementById('filter').innerHTML = response;
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "nFilterID=" + nFilterID + "&language=<?php echo $_REQUEST['language']; ?>"
				}
			);
		 }	
		
		
		function editCurCirculation(strAction)
		{
			if (document.getElementById('bFilterOn').checked)
			{
				alert('ok');
				
			}
		}
	//-->
	</script>
<?php	
	
	$objCirculation = new CCirculation();
	
	$arrAllUsers 		= $objCirculation->getAllUsers();
	$arrActiveUsers		= $objCirculation->getAllUsers(false);
	$arrAllMailingLists	= $objCirculation->getAllMailingLists();
	$arrAllInputFields	= $objCirculation->getMyInputFields();
	$arrAllTemplates	= $objCirculation->getAllTemplates();
	
	if (($_REQUEST["archivemode"] == 0) && !$_REQUEST['bOwnCirculations'])
	{
		?>
		<table width="90%" cellspacing="0" cellpadding="0">
		<tr>
			<?php 
			if (($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 2)||($_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"] == 8))
			{
				// check if an extension exists
				$extensions 	= $objCirculation->getExtensionsByHookId('CF_ADD_CIRCULATION');
				
				if ($extensions)
				{
					$extension 	= $extensions[0]; // only one extension is possible here
					$path 		= $extension['path'];
					$Extension	= $extension['Extension'];
					$hooks		= $Extension->hook;
					$hook		= $hooks[0];
					
					$destination			= $path.$hook->destination;
					$destination			.= $objCirculation->getExtensionParams($hook);
					$strEncryptedLinkURL	= $destination;
				}
				else
				{
					$strParams				= 'language='.$_REQUEST["language"].'&circid=-1';
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedLinkURL	= 'editcirculation.php?key='.$strEncyrptedParams;
				}
				?>
				<td align="left" width="14px">
    				<a href="<?php echo $strEncryptedLinkURL ?>"><img src="../images/addcirculation.png" border="0"></a>
    			</td>
    			<td align="left">
    				[ <a href="<?php echo $strEncryptedLinkURL ?>"><?php echo $CIRCULATION_MNGT_ADDCIRCULATION;?></a> ]
    			</td>
				<?php
			}
			else
			{
				?>
				<td align="left" width="14px">
    				<img src="../images/addcirculation.png" border="0">
    			</td>
    			<td align="left" style="color: #ddd;">
    				[ <?php echo $CIRCULATION_MNGT_ADDCIRCULATION;?> ]
    			</td>
				<?php
			}
			?>
			
			<td style="border: 1px solid #aaa; background: #ccc; border-right: 0px; padding-left: 3px; padding-right: 3px;" align="center" width="120">
				<?php echo $CONFIG_ROWS_PER_PAGE; ?>:
			</td>
			<td align="center" style="border: 1px solid #aaa; border-left: 0px; background: #ccc; width: 60px;">
				<select onChange="changeRows()" style="border: 1px solid #999; width: 60px; padding: 1px; font-family: arial; font-size: 12px;" name="IN_nShowRows" id="IN_nShowRows">
					<option value="10" <?php if($nShowRows == 10){ echo 'selected'; } ?>>10</option>
					<option value="20" <?php if($nShowRows == 20){ echo 'selected'; } ?>>20</option>
					<option value="50" <?php if($nShowRows == 50){ echo 'selected'; } ?>>50</option>
					<option value="100" <?php if($nShowRows == 100){ echo 'selected'; } ?>>100</option>
				</select>
			</td>
		</tr>
		</table>
		<?php
	}
	else
	{
		?>
		<table width="90%" cellspacing="0" cellpadding="0">
		<tr>
			
			<td align="left" width="80%">
				<?php
					$strParams = 'language='.$_REQUEST["language"];
					$strParams .= '&archivemode=0';
					$strParams .= '&start=1';
					$strParams .= '&nShowRows=100';
					$strParams .= '&sortby=COL_CIRCULATION_PROCESS_DAYS';
					$strParams .= '&sortDirection=DESC';
					$strParams .= '&uid='.$nCurUserID;
					
					$strEncyrptedParams		= $objURL->encryptURL($strParams);
					$strEncryptedFeedURL	= 'todo_feed.php?key='.$strEncyrptedParams;
				?>
				<img src="../images/feed.png" border="0"/> [ <a href="<?php echo $strEncryptedFeedURL; ?>"> RSS </a> ]
			</td>
    			
			<td style="border: 1px solid #aaa; background: #ccc; border-right: 0px; padding-left: 3px; padding-right: 3px;" align="center" width="120">
				<?php echo $CONFIG_ROWS_PER_PAGE; ?>:
			</td>
			<td align="center" style="border: 1px solid #aaa; border-left: 0px; background: #ccc; width: 60px;">
				<select onChange="changeRows()" style="border: 1px solid #999; width: 60px; padding: 1px; font-family: arial; font-size: 12px;" name="IN_nShowRows" id="IN_nShowRows">
					<option value="10" <?php if($nShowRows == 10){ echo 'selected'; } ?>>10</option>
					<option value="20" <?php if($nShowRows == 20){ echo 'selected'; } ?>>20</option>
					<option value="50" <?php if($nShowRows == 50){ echo 'selected'; } ?>>50</option>
					<option value="100" <?php if($nShowRows == 100){ echo 'selected'; } ?>>100</option>
				</select>
			</td>
		</tr>
		</table>
		<?php
	}
	
	if(isset($_COOKIE['nReloadTimeout'])) 
	{// cookie is set
		if ($_COOKIE['nReloadTimeout'] == 'true')
		{
			$bshowReload = true;
			?>
			<script language="javascript">
				bReloadMe = 'true';
				reload_timeout('true');
			</script>
			<?php
		}
	}
	
	
	?>
	
	<script language="javascript">
	function getPosition(element)
	{
		var elem 	= element;
		var tagname = '';
		var x		= 0;
		var y		= 0;
		
		while ( (typeof(elem) == "object") && (typeof(elem.tagName) != "undefined") )
		{		    	
	    	y 		+= elem.offsetTop;
	    	x 		+= elem.offsetLeft;
	    	tagname = elem.tagName.toUpperCase();
			
			
			if (tagname == "BODY")
			{
				elem = 0;
			}
			
			if (typeof(elem) == "object")
			{
				if (typeof(elem.offsetParent) == "object")
				{
		        	elem = elem.offsetParent;
		        }
			}
		}

		position	= new Object();
		position.x	= x;
		position.y	= y;
		return position;
	}
	
	function show_filters()
	{
		if (document.getElementById('choose_filter').style.display == 'block')
		{
			document.getElementById('choose_filter').style.display = 'none';
		}
		else
		{
			var testttt = document.getElementById('select_filter');
			
			var MyPos 	= getPosition(testttt);
			var MyX		= (MyPos.x) - 4;
			var MyY		= (MyPos.y) + 15;
			
			document.getElementById('choose_filter').style.left 	= MyX + 'px';
			document.getElementById('choose_filter').style.top 		= MyY + 'px';

			new Ajax.Request
			(
				"showcirculation_filter_select.php",
				{
					onSuccess : function(resp) 
					{
						var response = resp.responseText;
						document.getElementById('choose_filter').innerHTML = response;
						document.getElementById('choose_filter').style.display = 'block';
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "language=<?php echo $_REQUEST['language']; ?>&nCurCuserID=<?php echo $_SESSION["SESSION_CUTEFLOW_USERID"]; ?>"
				}
			);
		}
	}
	</script>
	
	<div id="choose_filter" style="display: none; position: absolute;">
	
	</div>
	
	
    <br><table width="90%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left">
                <form action="showcirculation.php" method="post">
                    <table style="border: 1px solid #aaa; width: 100%;" cellspacing="0" cellpadding="0">
                    <tr>	
                    	<td style="padding: 1px; background: #ccc; " align="center" valign="top" width="15" valign="middle">
                        	<input type="checkbox" name="bFilterOn" id="bFilterOn" value="1" onClick="checkFilterView();" <?php if ($_REQUEST['bFilterOn']) { echo 'checked'; } ?>>
                        </td>
                        <td style="padding: 1px; background: #ccc;" align="left" valign="middle">
                        	<?php echo $FILTER_FILTEROPTIONS; ?>
                    	</td>
                    	<td style="padding: 1px; background: #ccc; " align="center" valign="top" width="15" valign="middle">
                        	<input type="checkbox" name="bAutoReloadOn" id="bAutoReloadOn" onClick="reload_activate();" value="1" <?php if ($bshowReload  == 'true') { echo 'checked'; } ?>>
                        </td>
                    	<td valign="middle" width="180" align="left" style="padding: 1px; background: #ccc; ">
                    		<?php echo $AUTO_RELOAD; ?>
                    	</td>
                    	<td style="padding: 1px; background: #fff; width: 16px;" align="center">
                        	<a onClick="sortResult('<?php echo $sortDirection;?>', '<?php echo $sortby;?>', '1');" href="#"><img title="<?php echo escapeDouble($START_FILTER);?>" src="../images/filter.png" height="16" width="16" style="border: 2px solid #999;"></a>
                    	</td>
                    </tr>
                    </table>
                    <div id="filter" style="display: none; padding-top: 8px; border: 1px solid #999; border-top: 0px; padding-bottom: 5px; background: #eee;">
                    	<table cellspacing="2" border="0" cellpadding="2" width="100%">
                    		<tr height="26">
                    			<td width="200" valign="top">
                    				<?php echo $CIRCULATION_MNGT_NAME; ?>
                    			</td>
                    			<td valign="top" colspan="2">
                    				<table width="100%" cellspacing="0" cellpadding="0">
                    				<tr><td align="left">
                    					<input type="text" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Name" id="FILTER_Name" value="">
                    				</td><td align="right">
                    					<div id="select_filter" style="width: 155px; height: 21px; background: url(../images/combo_dummy.jpg); text-align: left;" onClick="show_filters();">
	                    					<table cellpadding="0" cellspacing="0" width="155">
	                    					<tr><td style="padding: 2px; padding-left: 5px;">
	                    						<?php echo $FILTER_CHOOSE_FILTER; ?>
	                    					</td>
	                    					</tr>
	                    					</table>
                    					</div>
                    				</td></tr>
                    				</table>
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $CIRCULATION_MNGT_CURRENT_SLOT; ?>
                    			</td>
                    			<td valign="top">
                    				<?php
                    				if ($_REQUEST['bOwnCirculations'])
                    				{
                    					?>
                    					<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Station" id="FILTER_Station" disabled>
	                    					<option value="0"><?php echo $FILTER_STATION; ?></option>
	                    					<?php
	                    						$nMax = sizeof($arrActiveUsers);
	                    						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
	                    						{
	                    							$arrUser 		= $arrAllUsers[$nIndex];
	                    							$nUserID 		= $arrUser['nID'];
	                    							$strUsername 	= $arrUser['strUserId'];
	                    							?>
	                    							<option value="<?php echo $nUserID ?>" <?php if ($nUserID == $_SESSION['SESSION_CUTEFLOW_USERID']) echo 'selected' ?>><?php echo $strUsername ?></option>
	                    							<?php
	                    						}                        							
	                    					?>
                    					</select>
                    					<?php
                    				}
                    				else
                    				{
                    					?>
                    					<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Station" id="FILTER_Station">
	                    					<option value="0"><?php echo $FILTER_STATION; ?></option>
	                    					<?php
	                    						foreach ($arrAllUsers as $arrUser)
	                    						{
	                    							$nUserID 		= $arrUser['nID'];
	                    							$strUsername 	= $arrUser['strUserId'];
	                    							?>
	                    							<option value="<?php echo $nUserID ?>"><?php echo $strUsername ?></option>
	                    							<?php
	                    						}                        							
	                    					?>
                    					</select>
                    					<?php
                    				}
                    				?>
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $CIRCDETAIL_SENDER; ?>
                    			</td>
                    			<td valign="top">
                    				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Sender" id="FILTER_Sender">
                    					<option value="0"><?php echo $FILTER_SENDER; ?></option>
                    					<?php
                    						$nMax = sizeof($arrAllUsers);
                    						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
                    						{
                    							$arrUser 		= $arrActiveUsers[$nIndex];
                    							
                    							if ($arrUser["nAccessLevel"] > 0)
                    							{
	                    							$nUserID 		= $arrUser['nID'];
	                    							$strUsername 	= $arrUser['strUserId'];
	                    							
	                    							echo '<option value="'.$nUserID.'">'.$strUsername.'</option>';
                    							}
                    						}                        							
                    					?>
                    				</select>
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $CIRCULATION_MNGT_WORK_IN_PROCESS; ?>
                    			</td>
                    			<td valign="top">
                    				<?php echo $FILTER_FROM; ?> <input type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_DaysInProgress_Start" id="FILTER_DaysInProgress_Start" value="">
                    				<?php echo $FILTER_TO; ?> <input type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px; margin-left: 33px;" name="FILTER_DaysInProgress_End" id="FILTER_DaysInProgress_End" value="">
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $CIRCULATION_MNGT_SENDING_DATE; ?>
                    			</td>
                    			<td valign="top">
                    				<table cellspacing="0" cellpadding="0" border="0">
                    				<tr>
                    					<td valign="middle">
                    						<?php echo $FILTER_FROM; ?> <input readonly type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Date_Start" id="FILTER_Date_Start" value="">
                    					</td>
                    					<td valign="top">
                    						<a href="#"><img style="margin: 2px 20px 0px 0px;" src="../images/calendar.gif" title="<?php echo escapeDouble($SELECT_DATE);?>" border="0" id="FILTER_Date_Start_Button"></a>
                    					</td>
                    					<td valign="middle">
                    						<?php echo $FILTER_TO; ?> <input readonly type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="FILTER_Date_End" id="FILTER_Date_End" value="">
                    					</td>
                    					<td valign="top">
                    						<a href="#"><img style="margin: 2px 7px 0px 0px;" src="../images/calendar.gif" title="<?php echo escapeDouble($SELECT_DATE);?>" border="0" id="FILTER_Date_End_Button"></a>
                    					</td>
                    				</tr>
                    				</table>
                    				
                    				 <script type="text/javascript">
                    				 Calendar.setup(
									    {
									      inputField  : "FILTER_Date_Start",         // ID of the input field
									      ifFormat    : "%d.%m.%Y",    // the date format
									      button      : "FILTER_Date_Start_Button"       // ID of the button
									    }
									  );
									  Calendar.setup(
									    {
									      inputField  : "FILTER_Date_End",         // ID of the input field
									      ifFormat    : "%d.%m.%Y",    // the date format
									      button      : "FILTER_Date_End_Button"       // ID of the button
									    }
									  );
									  </script>
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $MENU_MAILINGLIST; ?>
                    			</td>
                    			<td valign="top">
                    				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Mailinglist" id="FILTER_Mailinglist">
                    					<option value="0"><?php echo $FILTER_MAILINGLIST; ?></option>
                    					<?php
                    						$nMax = sizeof($arrAllMailingLists);
                    						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
                    						{
                    							$arrRow 	= $arrAllMailingLists[$nIndex];
                    							$nID 		= $arrRow['nID'];
                    							$strTitle 	= $arrRow['strName'];

                    							echo '<option value="'.$strTitle.'">'.$strTitle.'</option>';
                    						}                        							
                    					?>
                    				</select>
                    			</td>
                    		</tr>
                    		<tr height="26">
                    			<td valign="top">
                    				<?php echo $SHOW_CIRCULATION_TEMPLATE; ?>
                    			</td>
                    			<td valign="top">
                    				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTER_Template" id="FILTER_Template">
                    					<option value="0"><?php echo $FILTER_TEMPLATE; ?></option>
                    					<?php
                    						$nMax = sizeof($arrAllTemplates);
                    						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
                    						{
                    							$arrRow 	= $arrAllTemplates[$nIndex];
                    							$nID 		= $arrRow['nID'];
                    							$strTitle 	= $arrRow['strName'];

                    							echo '<option value="'.$nID.'">'.$strTitle.'</option>';
                    						}                        							
                    					?>
                    				</select>
                    			</td>
                    		</tr>
                    		<tr>
                    			<td valign="top" align="left">
                    				<?php echo $FILTER_FREE; ?>
                    			</td>
                    			<td valign="top" align="left">
                    				<div id="custom">
                    				
                    				<script language="javascript">
                    					function startExtendedFilter(nMyID)
                    					{
                    						var strCurID = 'FILTERCustom_Field--' + nMyID;
                    						
                    						var nCurInputFieldID = document.getElementById(strCurID).value;
                    						                    						
                    						var strMyParameters = "nCurInputFieldID=" + nCurInputFieldID + "&nCurRunningID=" + nMyID + '&language=<?php echo $_REQUEST['language'] ?>';
                    						
                    						new Ajax.Request
											(
												"showcirculation_extendedfilter.php",
												{
													onSuccess : function(resp) 
													{
														if (resp.responseText != '')
														{
															var strCurDiv = 'extendedDIV--' +nMyID 
															document.getElementById(strCurDiv).innerHTML = resp.responseText;
														}
														else
														{
															
														}
													},
											 		onFailure : function(resp) 
											 		{
											   			alert("Oops, there's been an error.");
											 		},
											 		parameters : strMyParameters
												}
											);
                    					}
                    				</script>
                    				
                    				<?php
                    					if (!$_REQUEST['bSearch'])
                    					{
                    						?>
                    						
                    						<table cellspacing="0" cellpadding="0" id="FILTERCustom_TableID--0">
	                        				<tr>
	                        					<td valign="middle" align="left" nowrap="nowrap" width="420">
			                        				<div style="float:left;">
			                        				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTERCustom_Field--0" id="FILTERCustom_Field--0" onChange="startExtendedFilter(0)">
			                        					<option value="0"><?php echo $FILTER_FIELD; ?></option>
			                        					<?php
			                        						$nMax = sizeof($arrAllInputFields);
			                        						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
			                        						{
			                        							$arrRow 	= $arrAllInputFields[$nIndex];
			                        							$nID 		= $arrRow['nID'];
			                        							$strTitle 	= $arrRow['strName'];
			                        							
			                        							echo '<option value="'.$nID.'">'.$strTitle.'</option>';
			                        						}                        							
			                        					?>
			                        				</select>
			                        				<select style="width: 90px; font-family: arial; font-size: 12px;" name="FILTERCustom_Operator--0" id="FILTERCustom_Operator--0">
			                        					<option value="0"><?php echo $FILTER_OPERATOR; ?></option>
			                        					<option value="1">=</option>
			                        					<option value="2">&lt;</option>
			                        					<option value="4">&lt;=</option>
			                        					<option value="3">&gt;</option>
			                        					<option value="5">&gt;=</option>	
			                        					<option value="6">~ (like)</option>		                        					
			                        				</select>
			                        				</div>
			                        				
	                        						<div id="extendedDIV--0" style="float:left; margin: 0px 4px 0px 2px;">
	                        						<input type="text" name="FILTERCustom_Value--0" id="FILTERCustom_Value--0" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
	                        						</div>
	                        					</td>
	                        					<td valign="middle" align="left">
	                        						<a href="#"><img src="../images/edit_add.gif" border="0" onClick="addCustom();"></a>
	                        					</td>
	                        				</tr>
	                        				</table>
                    						
                    						<?php
                    						$nIndexValues = 0;
                    					}
                    					else
                    					{                        					
											$nIndexValues 		= 0;
											while(list($key, $value) = each($_REQUEST))
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
											
											$nTableID = 0;
                        					foreach($arrFILTERCustom as $arrCurFILTERCustom)
                        					{
                        						$nInputFieldID	= $arrCurFILTERCustom['nInputFieldID'];
                        						$nOperatorID	= $arrCurFILTERCustom['nOperatorID'];
                        						$strValue		= $arrCurFILTERCustom['strValue'];
                        						?>	                        						
                        						
                        						<table cellspacing="0" cellpadding="0" id="FILTERCustom_TableID--<?php echo $nTableID; ?>">
		                        				<tr>
		                        					<td valign="middle" align="left" width="420">
		                        						<div style="float:left;">
					                        				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTERCustom_Field--<?php echo $nTableID; ?>" id="FILTERCustom_Field--<?php echo $nTableID; ?>">
					                        					<option value="0"><?php echo $FILTER_FIELD; ?></option>
					                        					<?php
					                        						$nMax = sizeof($arrAllInputFields);
					                        						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
					                        						{
					                        							$arrRow 	= $arrAllInputFields[$nIndex];
					                        							$nID 		= $arrRow['nID'];
					                        							$strTitle 	= $arrRow['strName'];
					                        							
					                        							if($nInputFieldID == $nID)
					                        							{
					                        								echo '<option value="'.$nID.'" selected>'.$strTitle.'</option>';
					                        							}
					                        							else
					                        							{
					                        								echo '<option value="'.$nID.'">'.$strTitle.'</option>';
					                        							}
					                        						}                        							
					                        					?>
					                        				</select>
					                        				<select style="width: 90px; font-family: arial; font-size: 12px;" name="FILTERCustom_Operator--<?php echo $nTableID; ?>" id="FILTERCustom_Operator--<?php echo $nTableID; ?>">
					                        					<option value="0"><?php echo $FILTER_OPERATOR; ?></option>
					                        					<option value="1" <?php if($nOperatorID == 1) { echo 'selected'; } ?>>=</option>
					                        					<option value="2" <?php if($nOperatorID == 2) { echo 'selected'; } ?>>&lt;</option>
					                        					<option value="4" <?php if($nOperatorID == 4) { echo 'selected'; } ?>>&lt;=</option>
					                        					<option value="3" <?php if($nOperatorID == 3) { echo 'selected'; } ?>>&gt;</option>
					                        					<option value="5" <?php if($nOperatorID == 5) { echo 'selected'; } ?>>&gt;=</option>
					                        					<option value="6" <?php if($nOperatorID == 6) { echo 'selected'; } ?>>~ (like)</option>
					                        				</select>
				                        				</div>
				                        				<div id="extendedDIV--REPLACE" style="float:left; margin: 0px 4px 0px 2px;"">
		                        							<input type="text" name="FILTERCustom_Value--<?php echo $nTableID; ?>" id="FILTERCustom_Value--<?php echo $nTableID; ?>" value="<?php echo $strValue; ?>" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
		                        						</div>
		                        					</td>
		                        					<td valign="middle" align="left">
		                        						<a href="#"><img src="../images/edit_add.gif" border="0" onClick="addCustom();"></a>
		                        						<?php if ($nTableID > 0) { ?>
		                        						<a href="#"><img src="../images/edit_remove.gif" border="0" onClick="removeCustom('<?php echo $nTableID; ?>');"></a>
		                        						<?php } ?>
		                        					</td>
		                        				</tr>
		                        				</table>
                        						
                        						<?php
                        						$nTableID++;
                        					}
                    					}
                    				?>
                    				<script language="javascript">
                    					setCurID(<?php echo ($nIndexValues+1); ?>);
                    				</script>
                    				
                    				</div>
                    			</td>
                    		</tr>
                    		<tr><td height="5" colspan="5" style="border-bottom: 1px solid #666;"></td></tr>
                    		<tr>
                    			<td colspan="2">
                    				<input type="button" class="Button" value="<?php echo $FILTER_SAVE_FILTER; ?>" onClick="addFilter();">&nbsp;&nbsp;
                    				<input type="button" class="Button" value="<?php echo $FILTER_RESET;?>" onClick="resetFilter()">
                    			</td>
                    		</tr>
                    	</table>
                    </div>
                    <input type="hidden" name="language" id="language" value="<?php echo $language;?>">
                    <input type="hidden" name="archivemode" id="archivemode" value="<?php echo $archivemode;?>">
                    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby;?>">
                    <input type="hidden" name="sortDirection" id="sortDirection" value="<?php echo $sortDirection;?>">
                    <input type="hidden" name="bSearch" value="1">
                    <input type="hidden" name="nShowRows" id="nShowRows" value="<?php echo $nShowRows; ?>">
                    <input type="hidden" name="start" id="start" value="1">
                    <input type="hidden" name="nAccessLevel" id="nAccessLevel" value="<?php echo $_SESSION["SESSION_CUTEFLOW_ACCESSLEVEL"]; ?>">
                </form>
                
                <div id="custom_add" style="display: none;">
					
					<table cellspacing="0" cellpadding="0" id="FILTERCustom_TableID--REPLACE">
        				<tr>
        					<td valign="middle" align="left" nowrap="nowrap" width="420">
                				<div style="float:left;">
	                				<select style="width: 155px; font-family: arial; font-size: 12px;" name="FILTERCustom_Field--REPLACE" id="FILTERCustom_Field--REPLACE" onChange="startExtendedFilter(REPLACE)">
	                					<option value="0"><?php echo $FILTER_FIELD; ?></option>
	                					<?php
	                						$nMax = sizeof($arrAllInputFields);
	                						for($nIndex = 0; $nIndex < $nMax; $nIndex++)
	                						{
	                							$arrRow 	= $arrAllInputFields[$nIndex];
	                							$nID 		= $arrRow['nID'];
	                							$strTitle 	= $arrRow['strName'];
	                							
	                							echo '<option value="'.$nID.'">'.$strTitle.'</option>';
	                						}                        							
	                					?>
	                				</select>
	                				<select style="width: 90px; font-family: arial; font-size: 12px;" name="FILTERCustom_Operator--REPLACE" id="FILTERCustom_Operator--REPLACE">
	                					<option value="0">Operator</option>
	                					<option value="1">=</option>
	                					<option value="2">&lt;</option>
	                					<option value="4">&lt;=</option>
	                					<option value="3">&gt;</option>
	                					<option value="5">&gt;=</option>
	                					<option value="6">~ (like)</option>
	                				</select>
                				</div>
                				
        						<div id="extendedDIV--REPLACE" style="float:left; margin: 0px 4px 0px 2px;">
        						<input type="text" name="FILTERCustom_Value--REPLACE" id="FILTERCustom_Value--REPLACE" style="border: 1px solid #999; width: 150px; padding: 1px; font-family: arial; font-size: 12px;">
        						</div>
        					</td>
        					<td valign="middle" align="left">
        						<a href="#"><img src="../images/edit_add.gif" border="0" onClick="addCustom();"></a>
        						<a href="#"><img src="../images/edit_remove.gif" border="0" onClick="removeCustom('REPLACE');"></a>
        					</td>
        				</tr>
        			</table>
        			
				</div>
				
            </td>
        </tr>
    </table>
	<br>

    <div id="div_content">
    </div>
    <br><br>
    <div id="testNewDiv">
    </div>
	<br><br>
</div>
	<script type="text/javascript">
		
		function doCronjob()
		{
			new Ajax.Request
			(
				"cronjob_check_substitute.php",
				{
					onSuccess : function(resp) 
					{
						
					},
			 		onFailure : function(resp) 
			 		{
			   			
			 		}
				}
			);
		}
	</script>
</body>
</html>
