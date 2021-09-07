<?php

class Dir
{
	public function __construct () {}
	public function __destruct  () {}

	public function create ( $directory ) {	return mkdir ( $directory ); }
	public function exists ( $directory ) { return is_dir ( $directory); }
}