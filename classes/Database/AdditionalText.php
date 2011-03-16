<?php

class Database_AdditionalText
{
	public $id;
	public $title;
	public $content;
	public $is_default;
	
	/**
	 * default constructor
	 * 
	 * @param Integer $id
	 * @param String $title
	 * @param String $content
	 * @param Integer $is_default
	 */
	public function __construct($id = false, $title = false, $content = false, $is_default = false)
	{
		$this->id 			= $id;
		$this->title 		= $title;
		$this->content 		= $content;
		$this->is_default 	= $is_default;
	}
	
	/**
	 * returns true / false if a database entry of the given id exists
	 * also saves the database entry to the object
	 *
	 * @param Integer $id the user id
	 * @return Integer true / false if a database entry of the given id exists
	 */
	public function getById($id = false)
	{	
		if ($id)
		{
			$strQuery 	= "SELECT * FROM cf_additional_text WHERE id = '$id'";
			$result 	= @mysql_query($strQuery);
			
			if($result)
			{
				$arrResult	= @mysql_fetch_array($result, MYSQL_ASSOC);
			}
			
			if($arrResult)
			{	// found database entry - let's save the details
				
				$this->id 			= $arrResult['id'];
				$this->title 		= $arrResult['title'];
				$this->content 		= $arrResult['content'];
				$this->is_default	= $arrResult['is_default'];
				
				return true;
			}
		}
		// Error - no database entries found
		return false;
	}
	
	/**
	 * Returns an array of the entries
	 * 
	 * @param String $companyName
	 * 
	 * @return Array $companys
	 */
	public function getByParams($title = false)
	{
		// build the query
		$query = "SELECT * FROM cf_additional_text WHERE 1=1";
		
		if ($title) $query .= " AND title LIKE '$title%'";
		
		$query .= " ORDER BY title ASC";
		$result = @mysql_query($query);
		
		if($result) 
		{
			$index = 0;
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				$rows[$index] = $row;
				$index++;						
			}
			if($rows && (sizeof($rows) < 60))
			{	// found database entry
				return $rows;
			}
		}
		
		return false;
	}
	
	/**
	 * returns the default value
	 *
	 * @return String $additionalTextDefaultValue
	 */
	public function getDefaultValue()
	{	
		$strQuery 	= "SELECT content FROM cf_additional_text WHERE is_default = '1'";
		$result 	= @mysql_query($strQuery);
		
		if($result)
		{
			$arrResult	= @mysql_fetch_array($result, MYSQL_ASSOC);
		}
		
		if($arrResult)
		{	// found database entry - let's save the details
			return $arrResult['content'];
		}
		// Error - no database entries found
		return false;
	}
	
	/**
	 * sets the current additional text as default one
	 * 
	 * @param Integer $id
	 */
	public function setDefault($id = false)
	{
		if ($id)
		{
			$query 	= "UPDATE cf_additional_text SET is_default = 0";
			$result = @mysql_query($query);
			
			if ($result)
			{
				$query 	= "UPDATE cf_additional_text SET is_default = 1 WHERE id = $id";
				$result = @mysql_query($query);
				if ($result)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * save the object to database
	 * creates a new entry if no id exists otherwise updates the existing entry
	 *
	 * @return true / false depending on the process sucess
	 */
	public function save()
	{
		if(!$this->id)
		{
			$strQuery 	= "INSERT INTO cf_additional_text VALUES (	'',
																	'$this->title',
																	'$this->content',
																	'$this->is_default')";			
			$result 	= @mysql_query($strQuery);
			
			if ($result)
			{
				$strQuery	= "SELECT MAX(id) FROM cf_additional_text";
				$result2 	= @mysql_query($strQuery);
			}
			
			if ($result2)
			{
				$arrRow 	= @mysql_fetch_row($result2);
				$this->id 	= $arrRow[0];
				
				return true;
			}
		}
		else
		{
			$strQuery 	= "UPDATE cf_additional_text SET 	title 		= '$this->title',
															content 	= '$this->content',
															is_default	= '$this->is_default'
															WHERE id 	= '$this->id'";
			$result 	= @mysql_query($strQuery);
			
			if ($result)
			{
				return true;
			}
		}
		// Error writing to database
		return false;
	}	
	
	/**
	 * delete the object from database depending on the current id
	 *  
	 */
	public function delete()
	{
		if($this->id) 
		{// User object has id
			$strQuery	= "DELETE FROM cf_additional_text WHERE id = '$this->id' LIMIT 1;";
			$result 	= @mysql_query($strQuery);
			
			if ($result)
			{
				return true;
			}
		}
		// Error deleting from database
		return false;
	}
}

?>