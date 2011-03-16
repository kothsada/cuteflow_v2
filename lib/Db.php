<?php
// TODO: MySQLi Statment BLOB support
// TODO: Impliment Singlet fetching
// VERSION: 0.95b1

/*
   Class: Db
   A class that wraps MySQLi and manages Mysql database connections and queries
*/
class Db
{
    public $result;
    public $sql;
    public $params;
    protected $last_id;
    protected $count;
    protected $num_rows;
    protected $affected_rows;
    protected $debug;
    protected $link;
    protected $query;
    protected $stmt;
    protected $error;
    private $factory;
    private static $instance;
    
    /*  
        Group: Connection Functions
    */
    
    /*
        Constructor: Db
        Initializes the object.
        
        (start code)
        $db = new Db(); // No connection executed
        
        // Constructor Connection
        $db = new Db('localhost', 'master', 'pass', 'clients'); // Create connection at construction
        (end)
    */
    private function __construct($server=null, $user=null, $passwd=null, $db=null)
    {
	$server = ($server !== null) ? $server : DB_SERVER;
	$user = ($user !== null) ? $user : DB_USER;
	$passwd = ($passwd !== null) ? $passwd : DB_PASSWD;
	$db = ($db !== null) ? $db : DB_NAME;
        if(($server !== null) and ($user !== null) and ($passwd !== null) and ($db !== null)) {    
            $this->connect($server,$user,$passwd,$db);
        }
    }	

    function __destruct()
    {

    }
    /*
        Function: init

        Creates a connection or uses the exsiting connection.
        
        (start code)
        // Using Constants
        define('DB_SERVER', 'localhost');
        define('DB_USER', 'master');
        define('DB_PASSWD', 'pass');
        define('DB_NAME', 'clients');

        $db = Db::init();
        // Using parameter
        $db = Db::init('localhost', 'master', 'pass', 'clients');

        (end)

        Parameters:

          server - MySQL Server's location.
          user - The MySQL username.
          passwd - The MySQL password.
          db - The MySQL database name.

        Returns:

          Dd class

        See Also:

          <connect>
    */
    public static function init($server=null, $user=null, $passwd=null, $db=null)
    {
	$server = ($server !== null) ? $server : DB_SERVER;
	$user = ($user !== null) ? $user : DB_USER;
	$passwd = ($passwd !== null) ? $passwd : DB_PASSWD;
	$db = ($db !== null) ? $db : DB_NAME;
	if (!isset(self::$instance)) 
	{
	    $c = __CLASS__;
	    self::$instance = new $c($server,$user,$passwd,$db);
	}

	return self::$instance;
    }

    public function close() {
	$this->link->close();
    } 

    /*
        Function: connect

        Creates a connection. Just like <init> but always creates a new connection
        
        (start code)
        // Non-Constructor connection
        $db = new Db();
        $db->connect('localhost', 'master', 'pass', 'clients');
        
        // Constructor Connection
        $db = new Db('localhost', 'master', 'pass', 'clients');
        (end)

        Parameters:

          server - MySQL Server's location.
          user - The MySQL username.
          passwd - The MySQL password.
          db - The MySQL database name.

        See Also:

          <connect>
    */
    public function connect($server, $user=null, $passwd=null, $db=null) {
	$this->link = new mysqli($server, $user, $passwd, $db);

	if (mysqli_connect_errno()) {
	    echo '[MySQLi] Connection Error: '.mysqli_connect_errno();
	}
    }

    /*  
        Group: Fetch Functions
    */
    
    /*
        Function: fetchAll

        Returns all rows in a <query>.
        
        (start code)
        // Execute a query
        $db->query('SELECT * FROM blog_entries');
        $entries = $db->fetchAll();
        (end)
        
        Returns:

          array(of rows)
          
        See Also:

          <fetchOne>, <query>
    */
    public function fetchAll() {
	$results = array();
	for($i=0; $i < $this->count; $i++) {
	    $results[] = $this->fetchOne();
	}
	$this->result = $results;
	return $this->result;
    }

    /*
        Function: fetchOne

        Returns all rows in a <query>.
        
        (start code)
        // Execute a query
        $db->query('SELECT * FROM blog_entries');
        $entry = $db->fetchOne();
        
        // Fetch all rows with fetchOne
	$results = array();
	for($i=0; $i < $db->count(); $i++) {
	    $results[] = $db->fetchOne();
	}
        (end)

        Returns:

          array(associated)

        See Also:

          <fetchAll>, <fetchSinglet>, <query>
    */
    public function fetchOne() {
	if(is_object($this->stmt)) {
	    $params = array();
	    $args = array();
	    $data = $this->stmt->result_metadata();
	    while ($field = mysqli_fetch_field($data)) {
		${$field->name} = null;
		$params[] = $field->name;
		$args[] = &${$field->name};
	    }
	    call_user_func_array(array($this->stmt,'bind_result'), $args);
	    $this->stmt->fetch();

	    $result = array();
	    foreach($params as $param) {
		if($$param === null or $$param === 0) {continue;} 
		$result[$param] = $$param;
	    }
	    if(empty($result)) { $result = false; }
	    $this->result = $result;
	} else {
	    $this->result = mysqli_fetch_assoc($this->query);
	    if($this->link->error) { 
		$this->error = $this->link->error;
	    }
	}
	return $this->result;
    }


    /*
        Function: fetchSinglet

        Returns first select variable in a <query>.
        
        (start code)
        // Execute a query
        $db->query('SELECT id FROM blog_entries');
        $entryid = $db->fetchSinglet();
        // returns 1
        
        (end)

        Returns:

          mixed

        See Also:

          <fetchAll>, <fetchOne>, <query>
    */
    public function fetchSinglet() {
	$singlet = $this->fetchOne();
        $keys = array_keys($singlet);
        return $singlet[$key[0]];
    }

    protected function decache() {
	if(is_object($this->stmt)) {
	    $this->stmt->close();
	}
	$this->stmt = null;
	$this->count = null;
	$this->params = array();
	$this->last_id = null;
	$this->num_rows = null;
	$this->affected_rows = null;
	$this->error = false;
    }

    /*  
        Group: Logging Functions
    */
    
    /*
        Function: debug

        The last MySQL query ran.
        
        (start code)
        // Execute a query via Prepared statement
        $entry_id    = 23;
        $entry_title = 'Testy test';
        $db->query('SELECT * FROM blog_entries WHERE id=? AND title=?', array($entry_id, $entry_title));
        echo $db->debug();
        // returns: SELECT * FROM blog_entries WHERE id=23 AND title="Testy test"
        (end)

        See Also:

          <error>
    */
    public function debug() {
	return $this->debug;
    }

    /*
        Function: error

        The error of the last query ran.
        
        (start code)
        // Execute a query
        $db->query('SELECT * FROMblog_entries');
        $entry = $db->fetchOne();
        
        if(!$entry and $db->error()) die($db->error());
        // returns "You have an error in your SQL syntax; check the manual that 
        corresponds to your MySQL server version for the right syntax to use 
        near 'FROMblog_entries' at line 1"
        (end)
          
        Returns:

          string
          
        See Also:

          <debug>
    */
    public function error() {
	return $this->error;
    }


    protected function sqlFilter(&$item, $key) {
	$item = $this->link->real_escape_string($item);
	if(!is_numeric($item)) {
	    $item = '"' . $item . '"';
	}
    }

    /*  
        Group: Helper Functions
    */
    
    /*
        Function: filter

        Filter an array of variables for mysql injections, adds quotes if a string
        
        (start code)
        $vars = $db->filter(array('cat'=>'i haz cheezburger'));
        $db->query('INSERT INTO blog_entries (title) VALUES(' . $vars['cat'] . ')');
        echo $db->debug;
        // returns: INSERT INTO blog_entries (title) VALUES("i haz cheezburger")
        
        (end)
        
        Returns:

          array, string, interger, decimal
    */
    public function filter($args) {
	array_walk_recursive($args, array($this, 'sqlFilter'));
	return $args;
    }
    
    
    /*  
        Group: Sql Functions
    */

    /*
        Function: count

        The count of rows in a query modified or gathered.
        
        (start code)
        // Execute a query via Prepared statement
        $entry_id    = 23;
        $entry_title = 'Testy test';
        $db->query('SELECT * FROM blog_entries WHERE id=? AND title=?', array($entry_id, $entry_title));
        echo $db->error();
        // returns: SELECT * FROM blog_entries WHERE id=23 AND title="Testy test"
        (end)
        
        Returns: 
            integer
        See Also:
          <lastid>
    */
    public function count() {
	return $this->count;
    }
    
    /*
        Function: lastid

        The last inserted id.
        
        (start code)
        // Execute a query via Prepared statement
        $entry_id    = 23;
        $entry_title = 'Testy test';
        $db->query('INSERT INTO blog_entries (id, title) VALUES(?,?)', array($entry_id, $entry_title));
        echo $db->lastid();
        // returns: 23
        (end)
        
        Returns: 
            integer
        See Also:
          <count>
    */
    public function lastid() {
	return $this->insert_id;
    }
    
    /*
        Function: prepare

        Sets up a prepared statement
        
        (start code)
        // Execute a query
        $db->prepare('SELECT * FROM blog_entries WHERE id=?')->bind(23)->query();
        // or 
        $db->prepare(SELECT * FROM blog_entries WHERE id=?);
        $db->bind(23);
        $db->query();
        $entry = $db->fetchOne();
        (end)

        See Also:

          <bind>
    */
    public function prepare($stmt) {
	$this->sql = $stmt;
	return $this;
    }

    /*
        Function: bind

        Sets up variables to use alongisde a prepared statement
        
        (start code)
        // Execute a query
        $db->prepare('SELECT * FROM blog_entries WHERE id=?')->bind(23)->query();
        // or 
        $db->prepare(SELECT * FROM blog_entries WHERE id=?);
        $db->bind(23);
        $db->query();
        // you can also stack binds
        $db->prepare('SELECT * FROM blog_entries WHERE id=? and title=?')
            ->bind(23)->bind('Hello World')->query();
        $db->prepare('SELECT * FROM blog_entries WHERE id=? and title=?')
            ->bind(array(23,'Hello World'))->query();
        $entry = $db->fetchOne();
        (end)

        See Also:

          <prepare>
    */
    public function bind($params) {
	if(is_array($params)) {
	    $this->params = array_merge($params, $this->params);
	} else {
	    $this->params[] = $params;
	}
	return $this;
    }
    
    /*
        Function: query

        Execute a MySQL command
        
        (start code)
        // Execute a query
        $db->query('SELECT * FROM blog_entries');
        $entry = $db->fetchOne();
        
        // Execute a query via Prepared statement
        $db->query('SELECT * FROM blog_entries WHERE id = ?', array($entry_id));
        $entry = $db->fetchOne();
        
        (end)
        
        Returns:

          integer (last inserted id, row count, rows modified)

        See Also:

          <fetchAll>, <fetchOne>
    */
    public function query($stmt=null, $params=null) {
	$stmt = (empty($stmt)) ? $this->sql : $stmt;
	$params = (!empty($this->params)) ? $this->params : $params;
	$this->sql = $stmt;
	$this->debug = $stmt;

	//-- Clear last query results;
	$this->decache();

	if(!empty($params)) {
	    //-- Prepared Statment
	    $this->stmt = mysqli_stmt_init($this->link);
	    $this->stmt->prepare($stmt);
	    /* i - int, d - float, s - str, b - blob */
	    $binds = '';
	    foreach($params as $i=>$param) {
		if(is_float($param)) {
		    $param_type = 'd';
		    $param = mysqli_real_escape_string($this->link, $param);
		} else if (is_int($param)) {
		    $param_type = 'i';
		    $param = mysqli_real_escape_string($this->link, $param);
		} else {
		    $param_type = 's';
		    $param = "'" . mysqli_real_escape_string($this->link, $param) . "'";
		}
		$this->debug = preg_replace('/\?/', $param, $this->debug, 1);
		$binds .= $param_type;
	    }
	    $args = array($binds);
	    $args = array_merge($args, $params);

	    call_user_func_array(array($this->stmt,'bind_param'), $args) or
		die(mysqli_error($this->link).": \nSQL: ".$stmt."\n");
	    $this->stmt->execute();
	    $this->stmt->store_result();
	} else {
	    //-- Non-Prepared Statement
	    $this->query = mysqli_query($this->link, $stmt) or die(mysqli_error($this->link));  



	}
	preg_match("/insert/i", $stmt, $insert_matches);
	preg_match("/select/i", $stmt, $select_matches);

	if(count($insert_matches) > 0) {
	    $this->insert_id = (!is_object($this->stmt)) ? mysqli_insert_id($this->link) : $this->stmt->insert_id();
	    return $this->insert_id;
	} elseif(count($select_matches) > 0) {
	    $this->num_rows = (!is_object($this->stmt)) ? mysqli_num_rows($this->query) : $this->stmt->num_rows();
	    $this->count = $this->num_rows;
	    return $this->num_rows;
	} else {
	    $this->affected_rows = (!is_object($this->stmt)) ? mysqli_affected_rows($this->link) : $this->stmt->affected_rows();
	    $this->count = $this->affected_rows;
	    return $this->affected_rows;
	}
    }
}

?>