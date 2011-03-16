<?php
class MailingList {
	/**
	 * 
	 * @return MailingList
	 */
	public static function create() {
		return new MailingList();
	}
	
	public function getAllowedSender($mailing_list_id) {
		$all_user = User::create()->findAll();
		
		$allowed_user = array();
		
		$query 	= "SELECT * FROM cf_allowed_sender WHERE mailinglist_id={$mailing_list_id};";

		$result 	= @mysql_query($query);
		
		if ($result) {
			while (	$row = mysql_fetch_array($result)) {
				$allowed_user[$row['user_id']] = $all_user[$row['user_id']];
			}
		}
		
		return $allowed_user;
	}
}