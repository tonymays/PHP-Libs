<?php

class XML
{
	protected $JSON;  // json class

	public function __construct () { $this->JSON = new JSON (); }
	public function __destruct  () { $this->JSON = null; }

	public function to_array ( $xml_document )
	{
		$array = $this->JSON->decode ( $this->JSON->encode ( ( array ) simplexml_load_string ( $xml_document ) ), true );
		if ( isset ( $array [ 0 ] ) && $array [ 0 ] == "\n") unset ( $array [ 0 ] );  // unset empty array lines
		return array_values ( $array );
	}
}