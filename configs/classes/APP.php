<?php
/**
 * APP.php
 * Archivo de clase APP
 *
 * @filesource
 */

class APP
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
