<?php
class Registry {
	protected function __construct() {
	}
	
	public static function instance() {
		if (null == self::$instance) {
			self::$instance = new Registry();
		}
		
		return self::$instance;
	}
	
	
	public function set($key, $value) {
		$this->value_hash[$key] = $value;
	}
	
	public function get($key) {
		if (array_key_exists($key, $this->value_hash)) {
			return $this->value_hash[$key];
		}
		
		return null;
	}
	
	private $value_hash = array();
	private static $instance = null;
}
?>