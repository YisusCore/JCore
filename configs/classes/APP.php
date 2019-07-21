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
		
		## Callback para el charset
		$this->_callbacks['set_charset'] = function ($charset)
		{
			$this->_config['charset'] = $charset;
			
			## Convirtiendolo a mayúsculas
			$charset = mb_strtoupper($charset);

			## Estableciendo los charsets a todo lo que corresponde
			ini_set('default_charset', $charset);
			ini_set('php.internal_encoding', $charset);

			@ini_set('mbstring.internal_encoding', $charset);
			mb_substitute_character('none');

			@ini_set('iconv.internal_encoding', $charset);
		};

		$this->_callbacks['get_charset'] = function (&$return)
		{
			$return = $this->_config['charset'];
		};
		
		$this['charset'] = $this->_config['charset'];
		
		## Callback para el timezone
		$this->_callbacks['set_timezone'] = function ($timezone)
		{
			$this->_config['timezone'] = $timezone;
			
			## Estableciendo el timezone a todo lo que corresponde
			date_default_timezone_set($timezone);
		};

		$this->_callbacks['get_timezone'] = function (&$return)
		{
			$return = $this->_config['timezone'];
		};
		
		$this['timezone'] = $this->_config['timezone'];
		
		## Callback para el lang
		$this->_callbacks['set_lang'] = function ($lang)
		{
			$this->_config['lang'] = $lang;
		};

		$this->_callbacks['get_lang'] = function (&$return)
		{
			$return = $this->_config['lang'];
		};
		
		$this['lang'] = $this->_config['lang'];
		
		
	}
	
	/**
     * __invoke ()
     */
    public function __invoke()
    {
		return APP::instance();
    }
}
