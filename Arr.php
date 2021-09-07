<?php

class Arr
{
	public function __construct () {}
	public function __destruct  () {}

	// --------------------------------------  ARRAY SEARCH FUNCTIONS  -------------------------------------------------
	public function contains ( $haystack, $needle )
	{
		if ( !is_array ( $haystack ) ) $haystack = $this->make_array ( $haystack );
		$haystack = array_flip ( $haystack );
		return array_key_exists ( $needle, $haystack );
	}


	public function filter ( &$array, $column_name, $column_value )
	{
		$result = [];
		foreach ( $array as $key => $value ) if ( $value[ $column_name ] == $column_value )	$result [] = $array [ $key ];
		return $result;
	}

	public function find ( &$array, $column_name, $column_value )
	{
		$result = -1;
		foreach ( $array as $key => $value ) if ( $value [ $column_name ] == $column_value ) { $result = $key; break; }
		return $result;
	}


	// --------------------------------------  ARRAY COMBINE FUNCTIONS  ------------------------------------------------
	public function join ( &$array1, &$array2, $join_column )
	{
		$result = $array1;
		foreach ( $array1 as $key => $value )
		{
			$fkey = $this->find ( $array2, $join_column, $value [ $join_column ] );
			if ( $fkey != -1 ) $result [ $key ] = array_merge ( $result [ $key ], $array2 [ $fkey ] );
		}
		return $result;
	}

	// --------------------------------------  ARRAY UTILITY FUNCTIONS  ------------------------------------------------


	public function trim                    ( $array ) { $result = []; foreach ( $array as $key => $value ) $result[] = trim ( $value ); return $result; }
	public function remove_empty_elements   ( $array ) { $result = []; foreach ( $array as $key => $value ) if ( trim ( $value ) !== '') $result [] = $value; return $result; }
	public function make_array              ( $mixed ) { return [ $mixed ]; }
	public function make_multi_dimensional  ( $array ) { return  count ( $array ) !== count ( $array, COUNT_RECURSIVE ) ? $array : $this->make_array ( $array ); }
	public function make_single_dimensional ( $array )
	{
		$result = [];
		foreach ( $array as $key => $value )
			foreach ( $value as $inner_key => $inner_value )
				$result [ $inner_key ] = $inner_value;
		return $result;
	}

	public function delete ( $array, $start, $end = null )
	{
		// return empty array on bad param set
		if (
			!is_array ( $array )                          							// $array must be an array
			|| !( $start !== null && is_int ( $start ) && $start >= 0 )				// $start cannot be null, a non int or less than 0
			|| !( $end === null || ( is_int ( $end ) && $end > $start ) )			// $end can be null but when not null must be an int and greater than $start
		) return [];

		$result = $array;                            							// work on a copy

		if ( $end === null ) unset ( $result [ $start ] );						// unset one element if $end is null
		else for ( $i = $start; $i <= $end; $i++ ) unset ( $result [ $i ] );    // unset the range specified
		return array_values ( $result );                    					// cleanup the keys
	}

	public function array_diff ( $array1, $array2 )
	{
		$result = [];

		foreach( $array1 as $key => $value )
		{
			// it's an array and both have the key
			if ( is_array ( $value ) && isset ( $array2 [ $key ] ) )
			{
				$new_diff = $this->array_diff ( $value, $array2 [ $key ] );
				if ( ! empty ( $new_diff ) )
					$result [ $key ] = $new_diff;
			}
			// the value is a string and it's not in array B
			else if ( is_string ( $value ) && ! in_array ( $value, $array2 ) )
				$result [ $key ] = $value;
			// the key is not numeric and is missing from array B
			else if ( ! is_numeric ( $key ) && ! array_key_exists ( $key, $array2 ) )
				$result [ $key ] = $value;
		}

		return $result;
	}

	public function array_keys ($array)
	{
		$result = [];
		foreach ( $array as $key => $value )
		{
			if ( is_array ( $value ) )
			{
				$result [] = $key;
				$new_array = $this->array_keys( $value );
				foreach ( $new_array as $inner_key => $inner_value )
					$result [] = $inner_value;
			}
			else
				$result [] = $key;
		}
		return $result;
	}

	public function remove_numeric_values ( $array )
	{
		$result = $array;
		foreach ( $array as $keys => $value )
		{
			if ( is_numeric ( $value ) )
				unset ( $result [ $keys ] );
		}
		return array_values ( $result );
	}
}