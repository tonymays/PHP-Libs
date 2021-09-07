<?php

class Logger
{
	protected $Date;
	protected $File;

	public function __construct () { $this->Date = new Date (); $this->File = new File (); }
	public function __destruct  () { $this->Date = null; $this->File = null; }
	public function error       ( $msg, $error_type = E_USER_ERROR      ) { return trigger_error ( $msg, $error_type ); }
	public function file        ( $filename, $msg, $format_time = false )
	{
		$time = $format_time ? $this->Date->formatted_current_time () : $this->Date->current_time ();
		return $this->File->write_contents ( $filename, $time . "-> " . $msg . "\n" );
	}
}