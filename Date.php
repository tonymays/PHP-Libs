<?php

class Date
{
	public function __construct            () {}
	public function __destruct             () {}
	public function current_time           ()        { return strtotime ( 'now' ); }
    public function formatted_current_time ()        { return date      ( 'Y-m-d h:i:s', strtotime ('now' ) ); }
	public function formatted_current_date ()        { return date      ( 'Y-m-d', strtotime ('now' ) ); }
    public function get_epoch              ( $date ) { return strtotime ( $date ); }
	public function is_valid_date          ( $date ) { return (bool) strtotime ( $date ); }

}