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
	private static $_initialized = false;
	static function &instance()
	{
		static $instance;

		isset($instance) or 
		$instance = new self();

		! $instance::$_initialized and 
		$instance->init() and
		$instance::$_initialized = TRUE;

		return $instance;
	}

	protected $_config;
	protected function init ()
	{
		$this->_config =& config('array');
		
		foreach (['charset', 'timezone', 'lang'] as $index)
		{
			$this->_callbacks['set_' . $index] = function ($newval) use ($index)
			{
				$this->_config[$index] = $newval;
			};

			$this->_callbacks['get_' . $index] = function (&$return) use ($index)
			{
				$return = $this->_config[$index];
			};
		}
		
	}
	
	/**
     * __invoke ()
     */
    public function __invoke()
    {
		return APP::instance();
    }
}
