<?php
include_once ("../pages/version.inc.php");

$strMessage =<<<END
$REMINDER_MAIL_HEADER1\n
$REMINDER_MAIL_HEADER2\n
\n
$ALL_OPEN_WORKFLOWS: $CUTEFLOW_SERVER\n\n
END;

foreach($circulations_aggregated as $single_circulation) {

	$strMessage .= $CIRCULATION_MNGT_NAME.':'.$single_circulation['circulation_name'].', '.$CIRCULATION_MNGT_SENDING_DATE.': '.$single_circulation['circulation_start'].
				', '. $CIRCORDER_SENDER.': '.$single_circulation['circulation_sender'].', '.
				$CIRCULATION_MNGT_WORK_IN_PROCESS.': '.$single_circulation['circulation_process_time'];
}

$strMessage .= "\n\n\npowered by\nCuteflow $CUTEFLOW_VERSION";
?>