<?php
class User {
	/**
	 * 
	 * @return User
	 */
	public static function create() {
		return new User();
	}
	
	public function find($id) {
		$query	= "SELECT * FROM cf_user WHERE nID = {$id} LIMIT 1;";
		$result = mysql_query($query);
		$result	= @mysql_fetch_array($result, MYSQL_ASSOC);
		
		return $result;
	}
	
	public function findAll() {
		$all_user = array();
		
		$query 	= "SELECT * FROM cf_user ORDER BY strLastName;";
		$result 	= @mysql_query($query);
		
		if ($result) {
			while (	$row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$all_user[$row['nID']] = $row;
			}
		}
		return $all_user;
	}
}