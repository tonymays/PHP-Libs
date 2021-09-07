<?php

class DB
{
	const DB_CONF   = BASE_DIR . '/config/db.conf';

	// ---------------------------------------  CLASS CONSTANTS  -------------------------------------------------------
	const SQL_UNKNOWN = 'UNKNOWN';
	const SQL_QUERY   = 'QUERY';
	const SQL_DML     = 'DML';
	const SQL_DDL     = 'DDL';

	// ---------------------------------------  CLASS PROPERTIES  ------------------------------------------------------
	protected $IniFile;
	protected $Array;
	protected $String;
	protected $Logger;
	protected $db_config;
	protected $query_commands;
	protected $dml_commands;
	protected $ddl_commands;

	public function __construct ()
	{
		$this->IniFile        = new IniFile ();
		$this->Array          = new Arr     ();
		$this->String         = new Str     ();
		$this->Log            = new Logger  ();
		$this->query_commands = [ 'SELECT' ];
		$this->dml_commands   = [ 'INSERT', 'UPDATE', 'DELETE' ];
		$this->ddl_commands   = [ 'ALTER', 'CREATE', 'DROP' ];
	}

	public function __destruct ()
	{
		$this->IniFile        = null;
		$this->Array          = null;
		$this->String         = null;
		$this->Log            = null;
		$this->db_config      = null;
		$this->query_commands = null;
		$this->dml_commands   = null;
		$this->ddl_commands   = null;
	}

	// ---------------------------------------  DATABASE CONNECTIONS  --------------------------------------------------

	public function open ( $named_connection )
	{
		// get database connection parameters based upon requested connection name
		$params = $this->Array->make_single_dimensional ( $this->IniFile->get ( self::DB_CONF, $named_connection, false ) );
		$handle = null;
		$dns    = $params [ 'type' ] . ':dbname=' . $params [ 'dbname' ] . ';host=' . $params [ 'host' ];
		try
		{
			$handle = new PDO ( $dns, $params [ 'user'], $params [ 'password' ],  [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
			return $handle;
		}
		catch ( PDOException $e ) { $this->Log->error ( $e->getMessage() ); return null; }
	}

	public function param_open ( $type, $host, $db_name, $user_name, $password, $port = 5432 )
	{
		$handle = null;
		$dns    = $type . ':dbname=' . $db_name . ';host=' . $host . ';port=' . $port;
		try
		{
			$handle = new PDO ( $dns, $user_name, $password,  [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
			return $handle;
		}
		catch ( PDOException $e ) { $this->Log->error ( $e->getMessage() ); return null; }
	}

	public function close ( $handle ) { $handle = null; }


	// --------------------------------------  PROCESS QUERIES  --------------------------------------------------------

	public function prepare ( $handle, $sql, $driver_options = [] )
	{
		if ( $handle === null ) return false;

		try   { return $handle->prepare ( $sql, $driver_options ); }
		catch ( PDOException $e ) { $this->Log->error ( $e->getMessage () ); return null; }
	}


	public function execute ( $statement_object, $input_parameters = [] )
	{
		if ( $statement_object === null ) return false;

		try   { $statement_object->execute ( $input_parameters ); return true; }
		catch ( PDOException $e ) { $this->Log->error ( $e->getMessage () ); return false; }
	}


	public function query ( $handle, $sql )
	{
		if ( $handle === null ) return false;

		$command_type = $this->get_command_type ( $sql );
		if ( $command_type === self::SQL_UNKNOWN || $command_type === self::SQL_DDL ) return false;

		try
		{
			// execute the query
			$handle->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$set = $handle->query ( $sql );

			// return a result set if the command type is a select statement
			if ( $command_type === self::SQL_QUERY )
				$result = $set->fetchAll ( PDO::FETCH_ASSOC );

			// return the last inserted id if the sql command is an insert statement
			else if ( strtoupper ( $this->String->get_first_word ($sql) ) === 'INSERT' )
				$result = $this->get_last_insert_id ( $handle );

			// otherwise set the result to true if no error to this point
			else $result = true;

			$set->closeCursor (); 	// close the cursor
			return $result;			// return the appropriate result set
		}
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}


	// use this function with prepare and execute type queries
	public function fetch_all ( $statement_object )
	{
		if ( $statement_object === null ) return false;

		try
		{
			$result = $statement_object->fetchAll ( PDO::FETCH_ASSOC );
			$statement_object->closeCursor ();
			return $result;
		}
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return []; }
	}


	// --------------------------------------  TRANSACTION PROCESSING  -------------------------------------------------
	public function start_transaction  ( $handle )
	{
		if ( $handle === null ) return false;

		try	  { return $handle->beginTransaction (); }
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}

    public function in_transaction ( $handle )
	{
		if ($handle === null) return false;

		try   { return $handle->inTransaction(); }
		catch (PDOException $e) { $this->Log->error($e->getMessage()); return false; }
	}

    public function commit ( $handle )
	{
		if ( !$this->in_transaction ( $handle ) ) return true;

		try   { return $handle->commit (); }
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}

    public function rollback ( $handle )
	{
		if ( !$this->in_transaction ( $handle ) ) return true;

		try   { return $handle->rollback (); }
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}

	public function get_last_insert_id ( $handle )
	{
		if ( $handle === null ) return false;

		try   { return $handle->lastInsertID (); }
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}


	// --------------------------------------  UTILITY FUNCTIONS  ------------------------------------------------------
	private function get_command_type ( $sql )
	{
		if ( trim ( $sql ) === '' ) return self::SQL_UNKNOWN;

		$command = strtoupper ( $this->String->get_first_word ( $sql ) );

		if ( in_array ( $command, $this->ddl_commands   ) ) return self::SQL_DDL;
		if ( in_array ( $command, $this->dml_commands   ) ) return self::SQL_DML;
		if ( in_array ( $command, $this->query_commands ) ) return self::SQL_QUERY;

		return self::SQL_UNKNOWN;
	}

	public function quote ( $handle, $string )
	{
		if ( $handle === null ) return false;

		try   { return $handle->quote ( $string ); }
		catch ( PDOException $e ) {	$this->Log->error ( $e->getMessage () ); return false; }
	}
}