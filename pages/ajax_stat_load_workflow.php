<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';	

	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection) {
		$query = "SELECT AVG (dateDecission - dateInProcessSince) a, l.strName, f.nMailingListId
					FROM `cf_circulationprocess` p, cf_circulationform f, cf_mailinglist l 
					WHERE p.dateDecission != 0 AND p.nCirculationFormId=f.nID AND f.nMailingListId=l.nID AND
							dateDecission != -1 AND
							dateInProcessSince > ".$_REQUEST['from']." AND
							dateInProcessSince < ".$_REQUEST['to']. "
							GROUP BY l.strName
							ORDER BY a DESC";
		$result = mysql_query($query, $nConnection);

		if ($result) {
			$mailing_lists = array();
			
			while ($row = mysql_fetch_array($result)) {
				$mailing_lists[] = $row;
			}
		}
	}
?>

<?php foreach ($mailing_lists as $list): ?>
<tr id="row-wf-<?php echo $list['nMailingListId']; ?>" class="row<?php echo $index++ % 2 == 0 ? "Odd" : "Even" ?>Workflow">
	<td height="20px">
		<img src="../images/inv.gif" width="20px" height="5px" /><a href="javascript: loadSlotDetails('row-wf-<?php echo $list['nMailingListId']; ?>')"><img src="../images/bullet_toggle_plus.png" align="absmiddle"/><?php echo $SHOW_CIRCULATION_MAILLIST;?>: <?php echo $list['strName']; ?></a>
	</td>
	<td align="right"><?php echo number_format($list['a'] / (60 * 60 * 24), 2)." ".$DAYS;?></td>
</tr>
<?php endforeach; ?>
