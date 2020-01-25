<?php
class Database {
	
	private $username;
	private $password;
	private $host;
	private $db;
	private $type;
	
	private $connection;
	
	public function __construct( $username, $password, $host, $db, $type ) {
		$this->username = $username;
		$this->password = $password;
		$this->host = $host;
		$this->db = $db;
		$this->type = $type;
	}
	
	public function query( $sql ) {
		if ( !isset( $this->connection ) ) {
			$this->connect();
		}
		
		switch($this->type) {
			case 'mssql' :
				$result = $this->_mssqlQuery( $sql );
				break;
		}		
           
		return $result;
	}
	
	private function connect() {
		switch($this->type) {
			case "mssql" :
				$this->connection = mssql_connect( $this->host, $this->username, $this->password );
				if ( !$this->connection ) throw new Exception_E('Could not initialise Database connection', 500 );
				$dbselected = mssql_select_db( $this->db, $this->connection );
				if ( !$dbselected ) throw new Exception_E('Could not select configured database', 500 );
				break;
		}
	}
	
	private function _mssqlQuery( $sql ) {
		$query_result  = mssql_query( $sql, $this->connection );
				
		if ( !$query_result ) 
			throw new Exception('Invalid query specified', 500);
                
		// fetch the results as an array
        $result = array();
        while ( $row = mssql_fetch_object( $query_result ) ) {
        	$result[] = $row;
        }
               
        // dispose of the query
        mssql_free_result($query_result);
        
        return $result;
	}
}