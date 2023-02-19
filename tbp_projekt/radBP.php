<?php
	function otvoriBP(){
		$GLOBALS['host'] = 'host=localhost';
		$GLOBALS['port'] = 'port=5432';
		$GLOBALS['dbname'] = 'dbname=postgres';
		$GLOBALS['user'] = 'user=postgres';
		$GLOBALS['password'] = 'password=password';
		
		//$dbconn = pg_connect("host=localhost port=5432");
		
		$db = pg_connect($host, $port, $dbname, $user, $password);
		if(!$db){
		
			echo "Error: ".pg_last_error;
			exit();
			
		}else {
			echo "veza postoji";
			return $db;
		
		}
		
		pg_set_client_encoding($db, UTF8); // UNICODE

	}
	
	function izvrsiBP($sql) {
		$rs = pg_query(pg_connect("dbname=postgres"), $sql);
		if(! $rs) {
			echo "Error: ".pg_last_error;
			exit();
		}
		return $rs; 
	}	
	
	function zatvoriBP(){
		global $dbc;
		mysql_close($dbc);
	}

?>	