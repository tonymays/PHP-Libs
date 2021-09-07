<?php

class Curl
{
	public function __construct () {}
	public function __destruct  () {}

	public function curl_enabled ()
	{
		if
		(
			!function_exists ('curl_init'  ) &&
			!function_exists ('curl_setopt') &&
			!function_exists ('curl_exec'  ) &&
			!function_exists ('curl_close' )
		) 	return false;
		return true;
	}

	public function execute ( $url, $options = [], $return_curl_info = false )
	{
		if ( ! $this->curl_enabled ( ) ) return [ 'ERROR: Request Failed - cURL not enabled' ];
		if ( empty ( $options ) )        return [ 'ERROR: cURL cannot be empty' ];

		$handle = curl_init ( $url );
		curl_setopt_array   ( $handle, $options );
		$result = curl_exec ( $handle );

		if ( $result === false )
		{
			trigger_error ( 'cURL Error: ' . curl_error ( $handle ) );
			$result = [ 'ERROR: Request Failed - See php log for specific request errors' ];
		}
		else
			if ( $return_curl_info ) $result = curl_getinfo ( $handle, CURLINFO_CONTENT_TYPE );

		curl_close ( $handle );
		return $result;
	}
}