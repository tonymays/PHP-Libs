<?php

class JSON
{
	public function __construct () {}
	public function __destruct  () {}

	public function encode ( $value, $options = 0, $depth = 512 )
    {
        /*  JSON_ENCODE OPTIONS
            JSON_HEX_TAG                    (integer) All < and > are converted to \u003C and \u003E. Available since PHP 5.3.0.
            JSON_HEX_AMP                    (integer) All &s are converted to \u0026. Available since PHP 5.3.0.
            JSON_HEX_APOS                   (integer) All ' are converted to \u0027. Available since PHP 5.3.0.
            JSON_HEX_QUOT                   (integer) All " are converted to \u0022. Available since PHP 5.3.0.
            JSON_FORCE_OBJECT               (integer) Outputs an object rather than an array when a non-associative array is used. Especially useful when the recipient of the output is expecting an object and the array is empty. Available since PHP 5.3.0.
            JSON_NUMERIC_CHECK              (integer) Encodes numeric strings as numbers. Available since PHP 5.3.3.
            JSON_PRETTY_PRINT               (integer) Use whitespace in returned data to format it. Available since PHP 5.4.0.
            JSON_UNESCAPED_SLASHES          (integer) Don't escape /. Available since PHP 5.4.0.
            JSON_UNESCAPED_UNICODE          (integer) Encode multibyte Unicode characters literally (default is to escape as \uXXXX). Available since PHP 5.4.0.
            JSON_PARTIAL_OUTPUT_ON_ERROR    (integer) Substitute some unencodable values instead of failing. Available since PHP 5.5.0.
            JSON_PRESERVE_ZERO_FRACTION     (integer) Ensures that float values are always encoded as a float value. Available since PHP 5.6.6.
            JSON_UNESCAPED_LINE_TERMINATORS (integer) The line terminators are kept unescaped when JSON_UNESCAPED_UNICODE is supplied. It uses the same behaviour as it was before PHP 7.1 without this constant. Available since PHP 7.1.0.
            JSON_THROW_ON_ERROR             (integer) Throws JsonException if an error occurs instead of setting the global error state that is retrieved with json_last_error(). JSON_PARTIAL_OUTPUT_ON_ERROR takes precedence over JSON_THROW_ON_ERROR. Available since PHP 7.3.0.
        */

        return json_encode ( $value, $options, $depth);
    }

    public function decode ( $json_string, $assoc = true, $depth = 512, $options = 0 )
    {
        /*  JSON_DECODE OPTIONS
            JSON_BIGINT_AS_STRING   (integer) Decodes large integers as their original string value. Available since PHP 5.4.0.
            JSON_OBJECT_AS_ARRAY    (integer) Decodes JSON objects as PHP array. This option can be added automatically by calling json_decode() with the second parameter equal to TRUE. Available since PHP 5.4.0.
            JSON_THROW_ON_ERROR     (integer) Throws JsonException if an error occurs instead of setting the global error state that is retrieved with json_last_error(). JSON_PARTIAL_OUTPUT_ON_ERROR takes precedence over JSON_THROW_ON_ERROR. Available since PHP 7.3.0.
         */

        return json_decode ( $json_string, $assoc, $depth, $options );
    }


    public function is_json ( $string )
    {
        $this->decode ( $string );
        return ( $this->last_error ( false ) == JSON_ERROR_NONE );
    }


    public function last_error ( $msg_only = true)
    {
        /*  JSON ERROR CODE IF MSG_ONLY PARAMETER IS FALSE
            JSON_ERROR_NONE	                    No error has occurred
            JSON_ERROR_DEPTH	                The maximum stack depth has been exceeded
            JSON_ERROR_STATE_MISMATCH	        Invalid or malformed JSON
            JSON_ERROR_CTRL_CHAR	            Control character error, possibly incorrectly encoded
            JSON_ERROR_SYNTAX	                Syntax error
            JSON_ERROR_UTF8	                    Malformed UTF-8 characters, possibly incorrectly encoded	PHP 5.3.3
            JSON_ERROR_RECURSION	            One or more recursive references in the value to be encoded	PHP 5.5.0
            JSON_ERROR_INF_OR_NAN	            One or more NAN or INF values in the value to be encoded	PHP 5.5.0
            JSON_ERROR_UNSUPPORTED_TYPE	        A value of a type that cannot be encoded was given	PHP 5.5.0
            JSON_ERROR_INVALID_PROPERTY_NAME	A property name that cannot be encoded was given	PHP 7.0.0
            JSON_ERROR_UTF16
        */

        return ($msg_only) ? json_last_error_msg() : json_last_error();
    }
}