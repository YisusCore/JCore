<?php
/**
 * Request.php
 * Archivo de clase Request
 *
 * @filesource
 */

class Request
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
