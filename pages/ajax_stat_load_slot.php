<?php
	require_once '../config/config.inc.php';
	require_once '../language_files/language.inc.php';
	require_once '../config/db_connect.inc.php';
	require_once 'CCirculation.inc.php';	

	$parts = explode('-', $_REQUEST['id']);
	
	$nConnection = mysql_connect($DATABASE_HOST, $DATABASE_UID, $DATABASE_PWD);
	if ($nConnection) {
		$query = "SELECT nID FROM cf_circulationform WHERE nMailingListId=".$parts[2];
		
		$result = mysql_query($query, $nConnection);
		
		if ($result) {
			$mailing_lists = array();
			
			while ($row = mysql_fetch_array($result)) {
				$forms[] = $row;
			}
		}

		$slotValues = array();
		
		foreach ($forms as $form) {
			
			$query = "SELECT AVG(dateDecission - dateInProcessSince) a, p.nCirculationFormId, s.strName, p.nSlotId 
						FROM cf_formslot s, `cf_circulationprocess` p 
						WHERE p.nCirculationFormId=".$form['nID']." AND 
								s.nID=p.nSlotId AND 
								dateDecission != 0 AND
								dateDecission != -1 AND
								dateInProcessSince > ".$_REQUEST['from']." AND
								dateInProcessSince < ".$_REQUEST['to']. "
						GROUP BY p.nSlotId
						ORDER BY a DESC";
			
			$result = mysql_query($query, $nConnection);
			
			if ($result) {
				while ($row = mysql_fetch_array($result)) {
					
					if (array_key_exists($row['nSlotId'], $slotValues)) {
						$slotValues[$row['nSlotId']] = ($row['a'] + $slotValues[$row['nSlotId']]) / 2;	
					}
					else {
						$slotValues[$row['nSlotId']] = $row['a'];
					}
					
					$slotNames[$row['nSlotId']] = $row['strName'];
				}
			}
		}
	}
?>

<?php foreach ($slotValues as $key=>$average): ?>
<tr class="row<?php echo $index++ % 2 == 0 ? "Odd" : "Even" ?>">
	<td height="20px">
		<img src="../images/inv.gif" width="40px" height="5px" /><?php echo $MAILLIST_EDIT_FORM_SLOT;?>: <?php echo $slotNames[$key]; ?>
	</td>
	<td align="right"><?php echo number_format($average / (60 * 60 * 24), 2)." ".$DAYS;?></td>
</tr>
<?php endforeach; ?>

