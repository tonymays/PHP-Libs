<?php

class CSV
{
	public function __construct () {}
	public function __destruct  () {}

    public function encode ( &$array, $delimiter = ',', $enclosure = '' )
    {
        $result = '';
        foreach ( $array as $key => $value )
        	$result .= $enclosure . implode ( $enclosure . $delimiter . $enclosure, $value ) . $enclosure . "\n";
        return $result;
    }

    public function decode ( $string, $delimiter = ',', $enclosure = '"', $escape = "\\" )
    {
    	return str_getcsv ( $string, $delimiter, $enclosure, $escape );
    }
}