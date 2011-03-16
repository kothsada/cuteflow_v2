<?php
class Field {
	
	/**
	 * 
	 * @return Field
	 */
	public static function create() {
		return new Field();
	}
	
	public function find($id) {
		$query	= "SELECT * FROM cf_inputfield WHERE nID = {$id} LIMIT 1;";
		$result = mysql_query($query);
		$result	= @mysql_fetch_array($result, MYSQL_ASSOC);
		
		return $result;
	}
	
	public function findAll() {
		$query 	= "SELECT * FROM cf_inputfield ORDER BY strName;";
		$result 	= @mysql_query($query);
		
		if ($result) {
			while (	$arrRow = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$arrRows[] = $arrRow;
			}
		}
		return $arrRows;
	}
}