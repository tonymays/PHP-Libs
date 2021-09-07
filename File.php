<?php

class File
{
	// ------------------------------------------  CLASS PROPERTIES ---------------------------------------------------
	protected $file_modes;
	protected $Curl;
    protected $Command;

	public function __construct ()
	{
		$this->Curl 		= new Curl ();
		$this->Command      = new Command();
		$this->file_modes 	= [ 'r', 'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+' ];
	}
	public function __destruct  () { $this->file_modes = null; }

	// ------------------------------------------  FILE CREATION PROCESSES  -------------------------------------------

	public function create ( $file_name )
    {
        if ( file_exists ( $file_name ) )  return false;
        $handle = fopen ( $file_name, 'w+' );
        return $this->close ( $handle );
    }

    public function delete ( $file_name ) { if ( !file_exists ( $file_name ) ) return false; return unlink ( $file_name ); }


	// ------------------------------------------  FILE CONNECTION PROCESSES  -----------------------------------------

	public function open ( $file_name, $mode = 'w+' )
	{
    	if ( !in_array    ( $mode, $this->file_modes ) ) return false;  // mode must exist
        if ( !file_exists ( $file_name ) ) $this->create ( $file_name ); // create file if it does not exist
		return fopen      ( $file_name, $mode );
	}


	public function close ( $handle ) { if ( is_resource ( $handle ) ) return fclose ( $handle ); return true; }


	// ------------------------------------------  FILE OPERATION PROCESSES  ------------------------------------------
	public function get_mime_type ( $url )
	{
		$options =
			[
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_RETURNTRANSFER => true,
			];

		return $this->Curl->execute ( $url, $options, true );
	}


	public function get_remote_file_size ( $url )
	{
		$handle = curl_init( $url );

		curl_setopt( $handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt( $handle, CURLOPT_HEADER, TRUE);
		curl_setopt( $handle, CURLOPT_NOBODY, TRUE);

		curl_exec  ( $handle );
		$size   = curl_getinfo ( $handle, CURLINFO_CONTENT_LENGTH_DOWNLOAD );
		curl_close ( $handle );
		return $size;
	}

    public function upload ( $url, $file )
	{

	}

	public function download ( $url, $destination )
	{
	    exec ( "curl -s -k -o " . $destination . " " . $url );
	}

	public function read ( $handle, $chunk_size = 4096 )
	{
		if ( !is_resource ( $handle ) ) return true;
		$result = '';
		while ( !feof ( $handle ) )	$result .= fgets ( $handle, $chunk_size );
		return trim ( $result );
	}

	public function write ( $handle, $contents ) { if ( is_resource ( $handle ) ) return fwrite ( $handle, $contents ); return false; }

	public function truncate ( $handle )
	{
			if ( !is_resource ( $handle ) ) return false;
			ftruncate         ( $handle, 0 );
			return rewind     ( $handle );
	}


    public function write_contents ( $file_name, $contents, $append = true )
    {
        $mode   = ( $append )   ? 'a+' : 'r+';
        $handle = $this->open  ( $file_name, $mode );
        $result = $this->write ( $handle, $contents );
        $this->close           ( $handle );
        return                 ( $result > 0 );
    }



    // ------------------------------------------  FILE LOCKING PROCESSES  --------------------------------------------

	public function lock ( $handle, $lock_type = LOCK_EX )
	{
		if ( !is_resource ( $handle ) ) 							return false;
		if ( ! ( $lock_type == LOCK_SH || $lock_type == LOCK_EX ) ) return false;
		return flock ( $handle, $lock_type );
	}


	public function unlock ( $handle )
	{
		if ( !is_resource ( $handle ) ) return true;
		fflush       ( $handle );
		return flock ( $handle, LOCK_UN );
	}
}