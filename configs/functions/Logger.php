<?php
/**
 * Logger.php
 *
 * @filesource
 */

if ( ! function_exists('print_array'))
{
	/**
	 * print_array()
	 * Muestra los contenidos enviados en el parametro para mostrarlos en HTML
	 *
	 * @use display_errors
	 * @use is_localhost
	 * @use logger
	 * @use protect_server_dirs
	 *
	 * @param	...array
	 * @return	void
	 */
	function print_array(...$array)
	{
		if (function_exists('display_errors') and function_exists('is_localhost') and function_exists('logger') and 
			! display_errors() and ! is_localhost())
		{
			logger('Está mostrando información de Desarrollador con la opción `display_errors` desactivada', FALSE);
		}

		$r = '';

		$trace = debug_backtrace(false);
		if (isset($trace[0]) && isset($trace[0]['function']) && $trace[0]['function'] === 'print_array')
		{
			array_shift($trace);
		}

		$file_line = '';
		if (isset($trace[0]))
		{
			$file_line = $trace[0]['file'] . ' #' . $trace[0]['line'];
			
			function_exists('protect_server_dirs') and
			$file_line = protect_server_dirs($file_line);
			
			$file_line = '<small style="color: #ccc;display: block;margin: 0;">' . $file_line . '</small><br>';
		}

		if (count($array) === 0)
		{
			$r.= '<small style="color: #888">[SIN PARAMETROS]</small>';
		}
		else
		foreach ($array as $ind => $_arr)
		{
			if (is_null($_arr))
			{
				$_arr = '<small style="color: #888">[NULO]</small>';
			}
			elseif (is_string($_arr) and empty($_arr))
			{
				$_arr = '<small style="color: #888">[VACÍO]</small>';
			}
			elseif (is_bool($_arr))
			{
				$_arr = '<small style="color: #888">['.($_arr?'TRUE':'FALSE').']</small>';
			}
			elseif (is_array($_arr) and function_exists('array_html'))
			{
				$_arr = array_html($_arr);
			}
			else
			{
				$_arr = htmlentities(print_r($_arr, true));
			}
			
			$r.= ($ind > 0 ? '<hr style="border: none;border-top: dashed #ebebeb '.($ind % 2 === 0 ? '1' : '').'.5px;margin: 12px 0;">' : '') . $_arr;
		}

		echo '<pre style="display: block;text-align: left;color: #444;background: white;position: relative;z-index: 99999999999;margin: 5px 5px 15px;padding: 0px 10px 10px;border: solid 1px #ebebeb;box-shadow: 4px 4px 4px rgba(235, 235, 235, .5);">' . $file_line . $r . '</pre>' . PHP_EOL;
	}
}

if ( ! function_exists('die_array'))
{
	/**
	 * die_array()
	 * Muestra los contenidos enviados en el parametro para mostrarlos en HTML y finaliza los segmentos
	 *
	 * @use print_array
	 *
	 * @param	...array
	 * @return	void
	 */
	function die_array(...$array)
	{
		call_user_func_array('print_array', $array);
		die();
	}
}

if ( ! function_exists('logger'))
{
	/**
	 * logger()
	 * Función que guarda los logs
	 *
	 * @param BasicException|Exception|TypeError|Error|string 	$message	El mensaje reportado
	 * @param int|null 		$code		(Optional) El código del error
	 * @param string|null	$severity	(Optional) La severidad del error
	 * @param array|null 	$meta		(Optional) Los metas del error
	 * @param string|null 	$filepath	(Optional) El archivo donde se produjo el error
	 * @param int|null 		$line		(Optional) La linea del archivo donde se produjo el error
	 * @param array|null 	$trace		(Optional) La ruta que tomó la ejecución hasta llegar al error
	 * @return void
	 */
	function logger ($message, $code = NULL, $severity = NULL, $meta = NULL, $filepath = NULL, $line = NULL, $trace = NULL, $show = TRUE)
	{
		static $_count = 0;
		$_count ++;
		
		$_count > 10 and
		exit('<br /><b>Fatal Error:</b> Se han producido demasiados errores de manera continua.');
		
		/**
		 * Listado de Levels de Errores
		 * @static
		 * @global
		 */
		static $error_levels = 
		[
			E_ERROR			    =>	'Error',				
			E_WARNING		    =>	'Warning',				
			E_PARSE			    =>	'Parsing Error',		
			E_NOTICE		    =>	'Notice',				

			E_CORE_ERROR		=>	'Core Error',		
			E_CORE_WARNING		=>	'Core Warning',		

			E_COMPILE_ERROR		=>	'Compile Error',	
			E_COMPILE_WARNING	=>	'Compile Warning',	

			E_USER_ERROR		=>	'User Error',		
			E_USER_DEPRECATED	=>	'User Deprecated',	
			E_USER_WARNING		=>	'User Warning',		
			E_USER_NOTICE		=>	'User Notice',		

			E_STRICT		    =>	'Runtime Notice'		
		];
		
		// Reordenamiento de parametros enviados
		is_bool ($code)     and $show = $code and $code = NULL;
		is_array($severity) and is_null($meta) and $meta = $severity and $severity = NULL;
		is_null ($code)     and $code = 0;
		is_null ($meta)     and $meta = [];
		is_array($meta)     or  $meta = (array)$meta;
		
		// Datos de FechaHora
		$meta['timestamp'] = [
			'time' => time(),
			'microtime' => microtime(),
			'microtimeF' => microtime(true),
			'datetime' => date('Y-m-d H:i:s'),
			'fecha' => date('Y-m-d'),
			'hora' => date('H:i:s'),
		];
		
		function_exists('date2') and
		$meta['timestamp']['fechaLL'] = date2('LL');
		
		defined('APP_loaded') and
		$meta['APP_loaded'] = APP_loaded;
		
		defined('RQS_loaded') and
		$meta['RQS_loaded'] = RQS_loaded;
		
		defined('RSP_loaded') and
		$meta['RSP_loaded'] = RSP_loaded;
		
		if (defined('BMK_loaded'))
		{
			$meta['BMK_loaded'] = BMK_loaded;
			$meta['BMK_totaltime'] = BenchMark::instance() -> between('total_execution_time_start');
		}
		
		if (defined('OPB_loaded'))
		{
			$meta['OPB_loaded'] = OPB_loaded;
			$meta['OPB_content'] = OutputBuffering::instance() -> stop() -> getContents();
		}
		
		$SER = [];
		foreach($_SERVER as $x => $y)
		{
			if (preg_match('/^((GATEWAY|HTTP|QUERY|REMOTE|REQUEST|SCRIPT|CONTENT)\_|REDIRECT_URL|REDIRECT_STATUS|PHP_SELF|SERVER\_(ADDR|NAME|PORT|PROTOCOL))/i', $x))
			{
				$SER[$x] = $y;
			}
		}
		
		$meta['server'] = $SER;
		
		defined('disp') and
		$meta['disp'] =  disp;
		
		isset($_SESSION['stat']) and
		$meta['stat'] = $_SESSION['stat'];

		// URL info
		try
		{
			$meta['url'] = url('array');
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		
		// IP info
		try
		{
			$meta['ip_address'] = ip_address('array');
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		
		// REQUEST info
		try
		{
			$meta['request'] = request('array');
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}

		// Reinformación de la data
		if ($message instanceof BasicException)
		{
			$exception = $message;
			
			$meta = array_merge($exception->getMeta(), $meta);
			is_null($severity) and $severity = 'BasicException';
			
			$meta['class'] = get_class($exception);
		}
		elseif ($message instanceof Exception)
		{
			$exception = $message;
			
			is_null($severity) and $severity = 'Exception';
			
			$meta['class'] = get_class($exception);
		}
		elseif ($message instanceof TypeError)
		{
			$exception = $message;
			
			is_null($severity) and $severity = 'Error';
			
			$meta['class'] = get_class($exception);
		}
		elseif ($message instanceof Error)
		{
			$exception = $message;
			
			is_null($severity) and $severity = 'Error';
			
			$meta['class'] = get_class($exception);
		}
		
		if (isset($exception))
		{
			$message  = $exception->getMessage();
			
			is_null($filepath) and $filepath = $exception->getFile();
			is_null($line)     and $line     = $exception->getLine();
			is_null($trace)    and $trace    = $exception->getTrace();
			
			$code == 0         and $code     = $exception->getCode();
		}

		is_null($severity) and $severity = E_USER_NOTICE;
		
		$severity = isset($error_levels[$severity]) ? $error_levels[$severity] : $severity;
		
		is_null($message) and $message = '[NULL]';
		
		if ($message === 'Only variable references should be returned by reference' and $code === E_NOTICE)
		{
			// Mensaje muy fastidioso
			return;
		}
		
		// Detectar la ruta del error
		if (is_null($trace))
		{
			$trace = debug_backtrace(false);
			
			if ($trace[0]['function'] === __FUNCTION__)
			{
				array_shift($trace);
			}
			
			if (in_array($trace[0]['function'], ['_exception_handler', '_error_handler']))
			{
				array_shift($trace);
			}
		}
		
		if (isset($trace[0]))
		{
			is_null($filepath) and $filepath = $trace[0]['file'];
			is_null($line) and $line = $trace[0]['line'];

			isset($trace[0]['class']) and ! isset($meta['class']) and $meta['class'] = $trace[0]['class'];
			isset($trace[0]['function']) and ! isset($meta['function']) and $meta['function'] = $trace[0]['function'];
		}
		
		// Prevenir que no se producza multiple error mientras se ejecuta el logger
		static $_isLoggerSv = FALSE;
		
//		$_isLoggerSv and
//		die_array('Se produjo un error mientras se ejecuta el loger', $message, $code, $severity, $meta, $filepath, $line, $trace, $show);
		
//		$_isLoggerSv = TRUE;
		
		
		
		
		die_array($message, $code, $severity, $meta, $filepath, $line, $trace, $show);
	}
}