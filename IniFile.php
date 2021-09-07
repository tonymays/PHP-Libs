<?php

class IniFile
{
	protected $Array;
	protected $String;

	public function __construct () { $this->Array = new Arr (); $this->String = new Str (); }
	public function __destruct  () { $this->Array = null; $this->String = null; }


	// ----------------------------------  SEARCH FUNCTIONS  ----------------------------------------------------------

	public function header_exists ( $file, $header )
	{
		$contents = file ( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$headers  = $this->get_header_keys ( $contents );
		return $this->Array->contains ( $headers, $header );
	}


	public function get ( $file, $header = '', $stringify = false)
	{
		if ( $header === '' && $stringify ) return file_get_contents ( $file );
		if ( !$this->String->starts_with ( $header, '[' ) ) $header = '[' . $header;
		if ( !$this->String->ends_with   ( $header, ']' ) ) $header = $header . ']';

		$sections    = $this->decode      ( $file, false  );
		$section_key = $this->Array->find ( $sections, 0, $header );

		return $stringify ? $this->stringify ( $sections [ $section_key ] )
			: $this->print_pretty ( $this->Array->make_multi_dimensional ( $sections [ $section_key ] ) );
	}



	// ----------------------------------  MANIPULATION FUNCTIONS  ----------------------------------------------------

	// TODO: BUILD MANIPULATION FUNCTIONS
	public function add    ( $file, $new_section ) {}
	public function update ( $file, $existing_section, $new_section ) {}
	public function delete ( $file, $section_header ) {}



	// ----------------------------------  PRIVATE UTILITY FUNCTIONS  -------------------------------------------------

	private function get_header_keys ( $array ) { return preg_grep ( "/^\[/", $array ); }

	private function decode ( $file, $print_pretty = false )
	{
		if (!file_exists ( $file ) ) return ['File does not exists'];

		$contents    = file ( $file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		$header_keys = $this->get_header_keys ( $contents );
		$key_range   = $this->get_key_range   ( $header_keys, count ( $contents ) - 1 );
		$result      = [];

		foreach ( $key_range as $keys => $value )
		{
			$row = explode ( ',', $value );
			$result [] = array_slice ( $contents, $row[ 0 ], $row [ 1 ] - $row[ 0 ] + 1 );
		}

		if ( $print_pretty ) $result = $this->print_pretty ( $result );

		return $result;
	}


	private function get_key_range ( $header_keys, $count )
	{
		$key_range   = [];
		$start       = 0;
		foreach ( $header_keys as $keys => $key )
		{
			if ( $keys === $start ) continue;
			$key_range [] = $start . ',' . ( $keys - 1 );
			$start 		  = $keys;
		}
		$key_range [] = $start . ',' . $count;
		return $key_range;
	}


	private function print_pretty ( $array )
	{
		if ( empty ( $array ) ) return [];

		$result  = [];
		$new_key = '';
		foreach ( $array as $key => $value )
			foreach ( $value as $inner_key => $inner_value )
			{
				if ( $inner_key == 0 )
				{
					$new_key = str_replace ( [ '[', ']' ], '', $inner_value );
					$result [ $new_key ] = [ 'header' => '[' . $new_key . ']' ];
				}
				else
				{
					$row = explode ( '=', $inner_value );

					if ( array_key_exists ( $row [ 0 ], $result [ $new_key ] ) )
					{
						if ( !is_array ( $result[ $new_key ][ $row[ 0 ] ] ) )
						{
							$result[ $new_key ][ $row[ 0 ] ] = $this->Array->make_array ( $result [ $new_key ] [ $row [ 0 ] ] );
						}
						$result [ $new_key ] [ $row [ 0 ] ] [] = $row [ 1 ];
					}
					else $result [ $new_key ] [ $row [ 0 ] ] = $row [ 1 ];
				}
			}
		return $result;
	}

	private function stringify ( $array )
	{
		if ( empty ( $array ) ) return '';
		$result = '';
		foreach ( $array as $key => $value ) $result .= $value . "\n";
		return $result;
	}
}