<?php
/**
 * Response.php
 * Archivo de clase Response
 *
 * @filesource
 */

class Response
{
	//-------------------------------------------
	// Statics
	//-------------------------------------------
	static function &instance()
	{
		static $instance;

		isset($instance) or $instance = new self();

		return $instance;
	}

}
