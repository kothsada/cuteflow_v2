<?php
include ('../classes/RssFeedItem.php');
include ('../classes/RssFeed.php');


	
include	('../config/config.inc.php');
include	('../config/db_connect.inc.php');
include_once	('CCirculation.inc.php');
include	('../language_files/language.inc.php');

$language		= $_REQUEST['language'];	
$archivemode	= $_REQUEST['archivemode'];
$sortDirection	= $_REQUEST['sortDirection'];
$sortby			= $_REQUEST['sortby'];
$start			= $_REQUEST['start'];
$nShowRows		= $_REQUEST['nShowRows'];

//http://cuteflowbranch/pages/todo_feed.php?language=de&archivemode=0&start=1&nShowRows=50&sortby=COL_CIRCULATION_PROCESS_DAYS&sortDirection=DESC&uid=1
$objRssFeed = new RssFeed();

$objCirculation = new CCirculation();
$arrCirculationOverview = $objCirculation->getCirculationOverview($start, 
													$sortby, 
													$sortDirection, 
													$archivemode, 
													50, 
													0,
													'',
													false,
													false,
													$_REQUEST['uid']);

foreach($arrCirculationOverview as $item) {
	$feed_item = new RssFeedItem();
	$feed_item->setTitle($item['strName']);	
	
	$nCirculationFormID = $item['nID'];
	$nSenderID 			= $item['nSenderId'];
	$strTitle	 		= $item['strName'];
	$nMailingListId		= $item['nMailingListId'];
	if ($item['strCurStation'] != '')
	{
		$strCurStation	= $item['strCurStation'];
	}
		
	$arrDecissionState 	= $objCirculation->getDecissionState($nCirculationFormID);
	$strStartDate		= $objCirculation->getStartDate($nCirculationFormID);
	$strSender			= $objCirculation->getSender($nCirculationFormID);
	$arrMaillist		= $objCirculation->getMailinglist($nMailingListId);
	$strMaillist		= $arrMaillist['strName'];
	 
	$strDesc  = '<dl>';
	$strDesc .= "<dt><strong>$CIRCULATION_MNGT_WORK_IN_PROCESS:</strong> ".$arrDecissionState["nDaysInProgress"]."</dt>";
	$strDesc .= "<dt><strong>$CIRCULATION_MNGT_SENDING_DATE:</strong> $strStartDate</dt>";
	$strDesc .= "<dt><strong>$CIRCDETAIL_SENDER:</strong> $strSender</dt>";
	$strDesc .= "<dt><strong>$SHOW_CIRCULATION_MAILLIST</strong> $strMaillist</dt>";
	$strDesc .= '</dl>';
	
	$feed_item->setDescription($strDesc);
	$feed_item->setLink("$CUTEFLOW_SERVER/pages/editcirculation.php?circid=$nCirculationFormID&language=$language&bRestart=1");
	$objRssFeed->addItem($feed_item);
}

$arrAllUsers = $objCirculation->getAllUsers();
$objRssFeed->setTitle($FEED_HEADLINE." ".$arrAllUsers[$_REQUEST['uid']]);
$objRssFeed->setLink($CUTEFLOW_SERVER);

	
echo $objRssFeed;

?>