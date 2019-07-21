<?php
/**
 * APP.php
 * Archivo de clase APP
 *
 * @filesource
 */

class APP extends JArray
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

	
	/**
     * __invoke ()
     */
    public function __invoke()
    {
		return APP::instance();
    }
}
