<?php
class db
{
	
	var $ready=false;
	
	function db($hostname,$dbuser,$dbpassword,$dbname){
	
		$this->hostname=$hostname;
		$this->dbuser=$dbuser;
		$this->dbpassword=$dbpassword;
		
		$this->dbc=$this->lconnect();
		if (!$this->dbc){
			$this->ready=false;
			$this->db_error(sprintf("
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information in your <code>lumonata-config.php</code> file is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
	<li>Are you sure you have the correct username and password?</li>
	<li>Are you sure that you have typed the correct hostname?</li>
	<li>Are you sure that the database server is running?</li>
</ul>",$hostname));
			return false;
		}
		$this->ready=true;
		$this->lselect_db($dbname);
	}
	
	function db_error($message){
		echo lumonata_die($message);
	}
	//connecting to the database using define username and password
	function lconnect(){
		return $this->result = mysql_connect($this->hostname,$this->dbuser,$this->dbpassword,true);
	}
	
	//select the database
	function lselect_db($dbname){
		if (!mysql_select_db($dbname))
		{
			$this->ready = false;
			$this->db_error(sprintf(/*DB_SELECT_ERROR*/'
						<h1>Can&#8217;t select database</h1>
						<p>We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>%1$s</code> database.</p>
						<ul>
						<li>Are you sure it exists?</li>
						<li>Does the user <code>%2$s</code> have permission to use the <code>%1$s</code> database?</li>
						<li>On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?</li>
						</ul>
						<p>If you don\'t know how to setup a database you should <strong>contact your host</strong>.'/*/WP_I18N_DB_SELECT_DB*/, $db, DB_USER));
						
			return false;
		}
	}
	
	//query to selected database
	function do_query($query){
		if ( ! $this->ready )
			return false;
	
		if (defined('ERR_DEBUG') and ERR_DEBUG == true)
			$error="Query failed: $query<br><br>".mysql_error();
			
		$result = mysql_query($query) or die($error);

		
		return $result;
	}
	
	//get the array data from selected query
	function fetch_array($result){
		return mysql_fetch_array($result);
	}
	
	//get the pbject data from selected query
	function fetch_object($result){
		return mysql_fetch_object($result);
	}
	
	function insert($table, $data) {
		$data = $this->add_magic_quotes($data);
		$fields = array_keys($data);
		
		return $this->do_query("INSERT INTO $table (`" . implode('`,`',$fields) . "`) VALUES ('".implode("','",$data)."')");
	}
	function update($table, $data, $where){
		$data = $this->add_magic_quotes($data);
		$bits = $wheres = array();
		foreach ( array_keys($data) as $k )
			$bits[] = "`$k` = '$data[$k]'";

		if ( is_array( $where ) )
			foreach ( $where as $c => $v )
				$wheres[] = "$c = '" . $this->escape( $v ) . "'";
		else
			return false;
		
		return $this->do_query( "UPDATE $table SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) );
	}
	//get the number of recorded data from table	
	function num_rows($result){
		return mysql_num_rows($result);
	}
	
	function _weak_escape($string) {
		return addslashes($string);
	}

	function _real_escape($string) {
		if ( $this->result )
			return mysql_real_escape_string( $string, $this->result );
		else
			return addslashes( $string );
	}

	function _escape($data) {
		if ( is_array($data) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->_escape( $v );
				else
					$data[$k] = $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content for insertion into the database using addslashes(), for security
	 *
	 * @since 0.71
	 *
	 * @param string|array $data
	 * @return string query safe string
	 */
	function escape($data) {
		if ( is_array($data) ) {
			foreach ( (array) $data as $k => $v ) {
				if ( is_array($v) )
					$data[$k] = $this->escape( $v );
				else
					$data[$k] = $this->_weak_escape( $v );
			}
		} else {
			$data = $this->_weak_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security
	 *
	 * @since 2.3.0
	 *
	 * @param string $s
	 */
	function escape_by_ref(&$string) {
		$string = $this->_real_escape( $string );
		
	}	
	/*function escape_by_ref(&$s) {
		$s = $this->escape($s);
	}*/
	function prepare_query($args=NULL) {
		if ( NULL === $args )
			return;
		$args = func_get_args();
		$query = array_shift($args);
		$query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
		$query = str_replace('"%s"', '%s', $query); // doublequote unquoting
		$query = str_replace('%s', "'%s'", $query); // quote the strings
		array_walk($args, array(&$this, 'escape_by_ref'));
		
		return @vsprintf($query, $args);
	}
	
	function add_magic_quotes( $array ) {
		foreach ( $array as $k => $v ) {
			if ( is_array( $v ) ) {
				$array[$k] = $this->add_magic_quotes( $v );
			} else {
				$array[$k] = $this->escape( $v );
			}
		}
		return $array;
	}
	
}
$db=new db(HOSTNAME,DBUSER,DBPASSWORD,DBNAME);




?>