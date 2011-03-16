<?php
	session_start();
    
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';
	
	$nCurUserID = $_SESSION["SESSION_CUTEFLOW_USERID"];
	
	if ($_REQUEST['to'] == "") {
		$_REQUEST['to'] = time() + (60 * 60 * 24 * 30);	// 30 days ahead
	}
	else {
		$_REQUEST['to'] = strtotime($_REQUEST['to'] );
	}
	
	if ($_REQUEST['from'] == "") {
		$_REQUEST['from'] = time()- (60 * 60 * 24 * 30);	// 30 days back
	}
	else {
		$_REQUEST['from'] = strtotime($_REQUEST['from'] );
	}
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
		
		tr.rowOdd
		{
			background-color: #fff;
		}
		
		tr.rowEvenWorkflow
		{
			background-color: #F4E8C2;
		}
		
		tr.rowOddWorkflow
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
	
	<script src="../lib/RPL/Encryption/aamcrypt.js" type="text/javascript" language="JavaScript"></script>
	<script src="../lib/RPL/Encryption/boxes.js?<?php echo time();?>" type="text/javascript" language="JavaScript"></script>
	<script language="JavaScript">
	<!--
		var openedRows = new Array();
		
		function loadWorkflowDetails() {
			new Ajax.Request
			(
				"ajax_stat_load_workflow.php",
				{
					onSuccess : function(resp) 
					{
						var response = resp.responseText;
						if (openedRows['root'] != 1)  {
							openedRows['root'] = 1;
							Element.insert($('row-all'), {after: response});
						}
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "language=<?php echo $_REQUEST['language']; ?>&to=<?php echo $_REQUEST['to']; ?>&from=<?php echo $_REQUEST['from']; ?>"
				}
			);
		}
		
		function loadSlotDetails(id) {
			new Ajax.Request
			(
				"ajax_stat_load_slot.php",
				{
					onSuccess : function(resp) 
					{
						var response = resp.responseText;
						
						if (openedRows[id] != 1)  {
							openedRows[id] = 1;
							Element.insert($(id), {after: response});
						}
					},
			 		onFailure : function(resp) 
			 		{
			   			alert("Oops, there's been an error.");
			 		},
			 		parameters : "language=<?php echo $_REQUEST['language']; ?>&to=<?php echo $_REQUEST['to']; ?>&from=<?php echo $_REQUEST['from']; ?>&id="+id
				}
			);
		}
		
		function submit() {
			$('filter-form').submit();
		}
		
		function siteLoaded() {
			Calendar.setup(
			    {
			      inputField  : "from",         // ID of the input field
			      ifFormat    : "%d.%m.%Y",    // the date format
			      button      : "FILTER_Date_Start_Button"       // ID of the button
			    }
			  );
			  Calendar.setup(
			    {
			      inputField  : "to",         // ID of the input field
			      ifFormat    : "%d.%m.%Y",    // the date format
			      button      : "FILTER_Date_End_Button"       // ID of the button
			    }
			  );
		}
	//-->
	</script>
</head>
<body onload="siteLoaded()"><br>
<table cellspacing="0" cellpadding="0" width="700">
<tr>
	<td align="left" style="padding-right: 20px;">
	<span style="float: left;font-size: 14pt; color: #ffa000; font-family: Verdana; font-weight: bold;">
		<?php echo $MENU_STATISTIC; ?>
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
	
	$objCirculation = new CCirculation();
	$arrAllUsers 		= $objCirculation->getAllUsers();
	$arrAllMailingLists	= $objCirculation->getAllMailingLists();
	$arrAllTemplates	= $objCirculation->getAllTemplates();
?>
	<table width="90%" cellspacing="0" cellpadding="0">
        <tr>
            <td align="left">
                <form action="showstatistic.php" id="filter-form" method="post">
	               <table style="border: 1px solid #aaa; width: 400px;" cellspacing="0" cellpadding="0">
	                    <tr>	
	                    	<td style="padding: 1px; background: #ccc;" align="left" valign="middle">
	                        	<?php echo $CIRCULATION_MNGT_FILTER; ?>
	                    	</td>
	                    	<td style="padding: 1px; background: #fff; width: 16px;" align="center">
	                        	<a onClick="submit()" href="#"><img title="<?php echo escapeDouble($START_FILTER);?>" src="../images/filter.png" height="16" width="16" style="border: 2px solid #999;"></a>
	                    	</td>
	                    </tr>
    	            </table>
					<table style="border: 1px solid #aaa; width: 400px">
						<tr>
	                    	<td><?php echo $STAT_FILTER_TIME; ?>:</td>
	                    	<td><?php echo $FILTER_FROM; ?>: <input readonly value="<?php echo date("d.m.Y", $_REQUEST['from']);?>" type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="from" id="from">
		                    	<a href="#"><img style="margin: 2px 7px 0px 0px;" src="../images/calendar.gif" border="0" id="FILTER_Date_Start_Button"></a> 
		                    	<?php echo $FILTER_TO; ?>:<input value="<?php echo date("d.m.Y", $_REQUEST['to']);?>" readonly type="text" style="border: 1px solid #999; width: 80px; padding: 1px; font-family: arial; font-size: 12px;" name="to" id="to"><a href="#"><img style="margin: 2px 7px 0px 0px;" src="../images/calendar.gif" border="0" id="FILTER_Date_End_Button"></a></td>
						</tr>
					</table> 
					<input type="hidden" name="language" value="<?php echo $_REQUEST['language']; ?>" />   	           
    	        </form>
    	    </td>
    	</tr>
   	</table>
	
	<?php
		$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
		if ($nConnection) {
			$query = "SELECT AVG (dateDecission - dateInProcessSince) a 
						FROM `cf_circulationprocess` 
						WHERE dateDecission != 0 AND
								dateDecission != -1 AND
								dateInProcessSince > ".$_REQUEST['from']." AND
								dateInProcessSince < ".$_REQUEST['to'];
			$result = mysql_query($query, $nConnection);
			
			if ($result) {
				$all_workflows = mysql_fetch_array($result);
			}
		}
	?>

	<h3><?php echo $STAT_MEAN_PROCESSING_TIME;?></h3>
	<table style="border: 1px solid rgb(200, 200, 200);" id="average-processing-time">
		<tr>
			<th class="table_header" align="left"><?php echo $STAT_HEADER_WORKFLOW; ?></th>
			<th class="table_header" align="left"><?php echo $STAT_MEAN_PROCESSING_TIME;?></th>
		</tr>
		<tr id="row-all">
			<td>
				<a href="javascript: loadWorkflowDetails()"><img src="../images/bullet_toggle_plus.png" align="absmiddle"/><?php echo $NOTIFICATION_RECIEVER_ALL;?> </a>
			</td>
			<td align="right"><?php echo number_format($all_workflows['a'] / (60 * 60 * 24), 2); ?> <?php echo $CIRCORDER_DAYS;?></td>
		</tr>
	</table>
	
	<?php
	if ($nConnection) {
		//--- get maximum count of users
		if (mysql_select_db($DATABASE_DB, $nConnection)) {
			$query = "SELECT AVG (dateDecission - dateInProcessSince) a, nUserId 
						FROM `cf_circulationprocess` 
						WHERE dateDecission != 0 AND
								dateDecission != -1 AND
								dateInProcessSince > ".$_REQUEST['from']." AND
								dateInProcessSince < ".$_REQUEST['to']."
						GROUP BY nUserId ORDER BY a DESC";
			$result = mysql_query($query, $nConnection);

			$user_avg = array();
			while ($row = mysql_fetch_array($result)) {
				$user_avg[] = $row;
			}
		}
	}
	
	if (isset($user_avg) && (sizeof($user_avg) > 0)) {
		?>
		<h3><?php echo $STAT_MEAN_DURATION;?> <?php echo $STAT_HEADLINE_USER; ?></h3>
		<table style="border: 1px solid rgb(200, 200, 200);" >
			<tr>
				<th class="table_header" align="left"><?php $STAT_HEADER_USER;?></th>
				<th class="table_header" align="left"><?php echo $STAT_MEAN_DURATION;?></th>
			</tr>	
			<?php foreach ($user_avg as $info): ?>
				<?php if ( ($info['nUserId'] != -2 ) && (array_key_exists($info['nUserId'], $arrAllUsers)) ): ?>
					<tr class="<?php echo $index++ % 2 == 0 ? "rowEven" : "rowOdd"; ?>" >
						<td><?php echo $arrAllUsers[$info['nUserId']]['strLastName']. ', '. $arrAllUsers[$info['nUserId']]['strFirstName']; ?>
						<td align="right"><?php echo number_format($info['a'] / (60 * 60 * 24), 2)." ".$DAYS;?> </td>
					</tr>				
				<?php endif; ?>
			<?php endforeach;?>
		</table>
		<?php
	}
	?>
	</body>
</html>
