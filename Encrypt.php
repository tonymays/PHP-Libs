<?php

class Encrypt
{

    // ------------------------------------------  CLASS PROPERTIES  --------------------------------------------------

    protected $encrypt_method;	// property that holds the encryption method used
    protected $secret_key;		// property that holds the password used
    protected $secret_iv;		// property that holds the non-null initialization vector
    protected $key;  			// property that holds a hash of the password used
    protected $iv;   			// property that holds the a hash of the initialization vector


	public function __construct ()
	{
		$this->encrypt_method = 'AES-256-CBC';
		$this->secret_key     = 'bostonredsoxworldchampions2013';
		$this->secret_iv      = '3102snoipmahcdlrowxosdernotsob';
		$this->key  		  = '';
		$this->iv			  = '';
	}

	public function __destruct ()
	{
		$this->encrypt_method = null;
		$this->secret_key     = null;
		$this->secret_iv      = null;
		$this->key  		  = null;
		$this->iv			  = null;
	}


	// ------------------------------------------  ENCRYPTION PROCESSES  ----------------------------------------------

    public function encrypt ( $string )
    {
        if ( trim ( $string ) === '' ) return false;

        $this->key = hash   ( 'sha256',       $this->secret_key );
        $this->iv  = substr ( hash ('sha256', $this->secret_iv ), 0, 16 );

        return base64_encode
        (
            openssl_encrypt
            (
                $this->scramble ( $string ),
                $this->encrypt_method,
                $this->key,
                0,
                $this->iv
            )
        );
    }


    public function decrypt ( $string )
    {
        if ( trim ( $string ) === '' ) return false;

        $this->key = hash   ( 'sha256',       $this->secret_key );
        $this->iv  = substr ( hash ('sha256', $this->secret_iv ), 0, 16 );

        return $this->unscramble
        (
            openssl_decrypt
            (
                base64_decode ( $string ),
                $this->encrypt_method,
                $this->key,
                0,
                $this->iv
            )
        );
    }


    // ------------------------------------------  STRING SCRAMBLING PROCESSES  ---------------------------------------

    public function scramble($string)
    {
        if ( trim ( $string ) === '' ) return false;

        $result = '';
        $chars = str_split     ( $string );
        $chars = array_reverse ( $chars  );
        foreach ( $chars as $key => $value )
        	$result .= '&' . pow( strlen ( ord ( $value ) ) . ord ( $value ), 2 );
        return $result;
    }


    public function unscramble ( $string )
    {
        if ( trim ( $string ) === '' ) return false;

        $result = '';
        $chars  = explode( '&', $string );
        unset ( $chars [ 0 ] );
        $chars  = array_reverse ( $chars );
        foreach ( $chars as $key => $value ) $result .= chr ( substr ( sqrt ( $value ), 1, strlen ( sqrt ( $value ) ) - 1 ) );
        return $result;
    }
}