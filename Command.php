<?php

class Command
{

	// ------------------------------------------  CLASS PROPERTIES ---------------------------------------------------
	protected $command;	// command being prepared and executed
	protected $output;	// the output from an executed command
	protected $result;	// the execution result of a prepared command


	public function __construct () { $this->command = null; $this->output  = []; $this->result  = null;	}
	public function __destruct  () { $this->command = null; $this->output  = []; $this->result  = null; }

	// ------------------------------------------  SHELL COMMAND PROCESSES  -------------------------------------------

	public function prepare ( $command, &$params = [] )
	{
		if ( trim ( $command ) === '' ) return false;

		$param_count = count ( $params );
		if ( ! empty ( $params ) )
			if ( substr_count ( $command, '?' ) != $param_count ) return false;

		$this->$command = $command;  // needed in case there are no params in the command
		if ( $param_count > 0 )
		{
			foreach ( $params as $key => $value ) $params [ $key ] = str_replace ( [ '"' ], '', $value );
			$this->command = vsprintf ( str_replace ('?', '%s', $command ), $params );
		}
		return true;
	}


	public function execute ()
	{
		if ( $this->command === null ) return false;

		$this->output = [];
		$this->result = null;
		return exec ( $this->command, $this->output, $this->result );
	}


	public function prepare_and_execute ( $command, $params = [] )
	{
		if ( trim ( $command )  === '' ) return false;

		$this->prepare ( $command, $params );
		$this->execute ();
		return $this->output;
	}



	// ------------------------------------------  CLASS GETTERS  -----------------------------------------------------

	public function get_command () { return $this->command; }
	public function get_output  () { return $this->output;  }
	public function get_result  () { return $this->result;  }
}