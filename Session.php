<?php

class Session
{
	public function __construct () {}
	public function __destruct  () {}

	// ------------------------------------------  SESSION HANDLING ---------------------------------------------------
	public function start          () { if ( !isset  ( $_SESSION ) ) return session_start (); return false; }
	public function end            () { if ( isset   ( $_SESSION ) ) session_unset(); }
    public function session_exists () { return isset ( $_SESSION ); }
    public function exists         ( $haystack, $key )
	{
		if ( !isset  ( $_SESSION [ $haystack ] ) ) return false;
		return array_key_exists ( $key, $_SESSION [ $haystack ] );
	}

	// ------------------------------------------  SESSION VARIABLES --------------------------------------------------
	public function cookies 	()  { return isset ( $_COOKIE )  ? $_COOKIE  : []; }
	public function environment ()  { return isset ( $_ENV )     ? $_ENV     : []; }
	public function files       ()  { return isset ( $_FILES )   ? $_FILES   : []; }
	public function gets        ()  { return isset ( $_GET )     ? $_GET     : []; }
	public function posts       ()  { return isset ( $_POST )    ? $_POST    : []; }
	public function requests    ()  { return isset ( $_REQUEST ) ? $_REQUEST : []; }
	public function server      ()  { return isset ( $_SERVER )  ? $_SERVER  : []; }
	public function sessions    ()  { return isset ( $_SESSION ) ? $_SESSION : []; }


	// ------------------------------------------  REQUEST VARIABLES --------------------------------------------------
	public function host_server   ()                     { return $_SERVER [ 'HTTP_HOST'      ]; }
	public function remote_server ()                     { return $_SERVER [ 'REMOTE_ADDR'    ]; }
	public function request_uri   ()                     { return $_SERVER [ 'REQUEST_URI'    ]; }
	public function content_type  ()                     { return $_SERVER [ 'CONTENT_TYPE'   ]; }
	public function content_len   ()                     { return $_SERVER [ 'CONTENT_LENGTH' ]; }
	public function script_name   ( $base_name = false ) { return $base_name ? basename ($_SERVER [ 'SCRIPT_NAME' ] ) : $_SERVER [ 'SCRIPT_NAME' ]; }
}