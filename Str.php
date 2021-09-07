<?php

class Str
{
	public function __construct () {}
	public function __destruct  () {}

	// --------------------------------------  STRING SLICING FUNCTIONS ------------------------------------------------

	public function starts_with ( $haystack, $needle ) { return ( substr($haystack, 0, strlen ( $needle ) ) === $needle ); }
	public function ends_with   ( $haystack, $needle ) { return ( substr($haystack, -( strlen ( $needle ) ) )    === $needle );  }

	public function slice_before ( $haystack, $needle, $include_needle = false )
	{
		$result = strstr ( $haystack, $needle, true );
		if      ( $result === false ) return $haystack;
		else if ( $include_needle )   $result .= $needle;  // keep needle if requested
		return $result;
	}

	public function slice_after ( $haystack, $needle, $include_needle = false )
	{
		$result = strstr ( $haystack, $needle, false );
		if 	( $result === false ) return $haystack;
		else if ( !$include_needle  ) $result = str_replace($needle, '', $result);  // remove the needle if requested
		return $result;
	}

	public function slice_between ( $haystack, $before_needle, $after_needle )
	{
		$before_pos = strpos ( $haystack, $before_needle );
		$after_pos  = strpos ( $haystack, $after_needle, $before_pos + 1 );
		if ( $before_pos === false || $after_pos === false ) return $haystack;
		return substr ( $haystack, $before_pos + strlen ( $before_needle ), $after_pos - $before_pos - strlen ( $before_needle ) );
	}


	
	// --------------------------------------  STRING UTILITY FUNCTIONS ------------------------------------------------

	public function contains           ( $haystack, $needle, $offset = 0 ) { return ( strpos ( $haystack, $needle, $offset ) !== false); }
	public function count              ( $haystack, $needle, $offset = 0 ) { return substr_count ( $haystack, $needle, $offset ); }
	public function decode_html_chars  ( $string )                         { return html_entity_decode  ( $string ); }
	public function decode_url_chars   ( $string )                         { return urldecode ( $string ); }
	public function get_words          ( $string )                         { return explode ( ' ', $string ); }
	public function get_word_count     ( $string )                         { return count ( $this->get_words ( $string ) ); }
	public function get_first_word     ( $string )                         { $result = $this->get_words ( $string ); return $result [ 0 ]; }
	public function get_first_int      ( $string )	                       { return strcspn ( $string , '0123456789' ); }
	public function is_first_char_int  ( $string )                         { return ( strcspn ( $string , '0123456789' )  == 0 ); }

	public function is_first_char_char ( $string )
	{
		return ( strcspn ( strtolower( $string ) , 'abcdefghijklmnopqrstuvwxyz' )  == 0 );
	}

	public function contains_char      ( $string )
	{
		return ( strlen ( $string ) != strcspn ( strtolower( $string ), 'abcdefghijklmnopqrstuvwxyz' ) );
	}

	public function pad ( $string, $pad_length, $pad_string, $pad_direction = STR_PAD_LEFT )
	{
		return str_pad ( $string, $pad_length, $pad_string, $pad_direction );
	}

	public function highlight_diff ( $old, $new )
	{
		$from_start	= strspn ( $old ^ $new, "\0" );
		$from_end 	= strspn ( strrev ( $old ) ^ strrev ( $new ), "\0");
		$old_end 	= strlen ( $old ) - $from_end;
		$new_end 	= strlen ( $new ) - $from_end;
		$start 		= substr ( $new, 0, $from_start );
		$end 		= substr ( $new, $new_end );
		$new_diff 	= substr ( $new, $from_start, $new_end - $from_start );
		$old_diff 	= substr ( $old, $from_start, $old_end - $from_start );
		$new 		= "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
		$old 		= "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";

		return [ 'old'=>$old, 'new'=>$new ];
	}
}