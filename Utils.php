<?php

class Utils
{
	protected $String;
	protected $File;

	public function __construct ()
	{
		$this->String = new Str  ();
		$this->File   = new File ();
	}

	public function __destruct  ()
	{
		$this->String = null;
		$this->File   = null;
	}

	public function stack_trace () { return debug_backtrace(); }

	public function is_valid_ip_address ( $ip_address )
	{
		return $this->filter_var ( $ip_address, FILTER_VALIDATE_IP  );
	}

	public function is_valid_timestamp ( $timestamp )
	{
		return ( strtotime ( date ('m-d-Y H:i:s', $timestamp ) ) === (int) $timestamp );
	}

	public function is_valid_integer ( $integer )
	{
		return $this->filter_var($integer,FILTER_VALIDATE_INT);
	}

	public function filter_var ( $value, $validate_as )
	{
		return ( filter_var ( $value, $validate_as)  !== false );
	}

    public function is_valid_mac_address ( $mac_address )
	{
    	return $this->filter_var ( $mac_address, FILTER_VALIDATE_MAC );
    }

    public function is_alpha ( $value )
	{
		// accept spaces as valid even though they would fail a ctype_alpha test
		$value = str_replace ( ' ', '', trim ( $value ) );
		return ctype_alpha ( $value );
	}

	public function is_alphanumeric ( $value )
	{
		// accept spaces as valid even though they would fail a ctype_alpha test
		$value = str_replace ( ' ', '', trim ( $value ) );
		return ctype_alnum ( $value );
	}

    public function is_valid_email_address ( $email_address, $strip_after_dot = false )
    {
        if ( $strip_after_dot ) $email_address = $this->String->slice_before($email_address, '.');
        return $this->filter_var ( $email_address, FILTER_VALIDATE_EMAIL );
    }

    public function is_valid_state ( $state )
	{
		$states = [ 'AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA', 'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY',
                    'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS', 'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND',
                    'OH', 'OK', 'OR', 'PA', 'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY', 'AS',
                    'DC', 'FM', 'GU', 'MH', 'MP', 'PW', 'PR', 'VI'
		          ];
		return in_array ( strtoupper ( $state ), $states );
	}

	public function is_valid_phone ( $phone )
	{
		// phone number must be 10 numbers after stripping out hyphens and parens
		return  ( strlen ( str_replace ([ '-', ')', '('], '', $phone ) ) == 10 && ctype_digit ( $phone ) );
	}

	public function is_valid_zip_code ( $zip_code )
	{
		// zip codes must be 5 numbers after stripping out hyphens
		// TODO: make Canadian zip codes accessible
		return  ( strlen ( str_replace ('-', '', $zip_code ) ) == 5 && ctype_digit ( $zip_code ) );
	}

	public function is_valid_decimal_places ( $number, $limit )
	{
		$length = strlen($number);
		$pos    = strpos($number, ".");
		if ($pos !== false) if ( ( ( $length - $pos ) - 1 ) > $limit ) return false;
		return true;
	}

	public function is_pdf_file ( $url )
	{
		return ( $this->File->get_mime_type ( $url ) == 'application/pdf' );
	}
}