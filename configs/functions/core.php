<?php
/**
 * core.php
 * 
 * Funciones principales del núcleo
 *
 * Copyright (c) 2018 - 2023, JYS Perú
 *
 * Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software 
 * y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, 
 * incluidos, entre otros, los derechos de uso, copia, modificación y fusión. , publicar, distribuir, 
 * sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les 
 * proporciona el Software que lo hagan, sujeto a las siguientes condiciones:
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 *
 * EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS,
 * ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.
 * EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, 
 * DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, 
 * DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.
 *
 * @package		JCore\Functions
 * @author		YisusCore
 * @link		https://jcore.jys.pe/functions/core
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

defined('ABSPATH') or exit('Acceso directo al archivo no autorizado');

/**
 * DIRECTORY_SEPARATOR
 *
 * Separador de Directorios para el sistema operativo de ejecución
 *
 * @global
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

isset($BASES_path) or $BASES_path = [];

//=========================================================
// Hooks
//=========================================================

/**
 * $JC_filters
 * Variable que almacena todas las funciones aplicables para los filtros
 * @internal
 */
$JC_filters = [];

/**
 * $JC_filters_defs
 * Variable que almacena todas las funciones aplicables para los filtros 
 * por defecto cuando no se hayan asignado alguno
 * @internal
 */
$JC_filters_defs = [];

/**
 * $JC_actions
 * Variable que almacena todas las funciones aplicables para los actions
 * @internal
 */
$JC_actions = [];

/**
 * $JC_actions_defs
 * Variable que almacena todas las funciones aplicables para los actions
 * por defecto cuando no se hayan asignado alguno
 * @internal
 */
$JC_actions_defs = [];

if ( ! function_exists('filter_add'))
{
	/**
	 * filter_add()
	 * Agrega funciones programadas para filtrar variables
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (Orden) a ejecutar la función cuando es llamado el Hook
	 * @return bool
	 */
	function filter_add ($key, $function, $priority = 50)
	{
		global $JC_filters;
		
		$lista =& $JC_filters;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('non_filtered'))
{
	/**
	 * non_filtered()
	 * Agrega funciones programadas para filtrar variables
 	 * por defecto cuando no se hayan asignado alguno
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (Orden) a ejecutar la función cuando es llamado el Hook
	 * @return bool
	 */
	function non_filtered ($key, $function, $priority = 50)
	{
		global $JC_filters_defs;
		
		$lista =& $JC_filters_defs;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('filter_apply'))
{
	/**
	 * filter_apply()
	 * Ejecuta funciones para validar o cambiar una variable
	 *
	 * @since 0.2 Se ha agregado las funciones por defecto cuando
	 * @since 0.1
	 *
	 * @param	string	$key	Hook
	 * @param	mixed	&...$params	Parametros a enviar en las funciones del Hook (Referenced)
	 * @return	mixed	$params[0] || NULL
	 */
	function filter_apply ($key, &...$params)
	{
		global $JC_filters;
		$lista =& $JC_filters;
		
		if (empty($key))
		{
			throw new Exception ('Hook es requerido');
		}
		
		count($params) === 0 and $params[0] = NULL;
		
		if ( ! isset($lista[$key]) OR count($lista[$key]) === 0)
		{
			global $JC_filters_defs;

			$lista_defs =& $JC_filters_defs;

			if ( ! isset($lista_defs[$key]) OR count($lista_defs[$key]) === 0)
			{
				return $params[0];
			}

			$functions = $lista_defs[$key];
		}
		else
		{
			$functions = $lista[$key];
		}
		
		krsort($functions);
		
		$params_0 = $params[0]; ## Valor a retornar
		foreach($functions as $priority => $funcs){
			foreach($funcs as $func){
				$return = call_user_func_array($func, $params);
				
				if ( ! is_null($return) and $params_0 === $params[0])
				{
					## El parametro 0 no ha cambiado por referencia 
					## y en cambio la función ha retornado un valor no NULO 
					## por lo tanto le asigna el valor retornado
					$params[0] = $return;
				}
				
				$params_0 = $params[0]; ## Valor a retornar
			}
		}
		
		return $params_0;
	}
}

if ( ! function_exists('action_add'))
{
	/**
	 * action_add()
	 * Agrega funciones programadas
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (orden) a ejecutar la función
	 * @return bool
	 */
	function action_add ($key, $function, $priority = 50)
	{
		global $JC_actions;
		
		$lista =& $JC_actions;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('non_actioned'))
{
	/**
	 * non_actioned()
	 * Agrega funciones programadas
 	 * por defecto cuando no se hayan asignado alguno
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (orden) a ejecutar la función
	 * @return bool
	 */
	function non_actioned ($key, $function, $priority = 50)
	{
		global $JC_actions_defs;
		
		$lista =& $JC_actions_defs;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('action_apply'))
{
	/**
	 * action_apply()
	 * Ejecuta las funciones programadas
	 *
	 * @since 0.2.2 Se ha agregado las funciones por defecto cuando
	 * @since 0.2.1 Se ha cambiado el $RESULT por defecto de FALSE a NULL
	 * @since 0.1
	 *
	 * @param string	$key	Hook
	 * @param	mixed	&...$params	Parametros a enviar en las funciones del Hook (Referenced)
	 * @return bool
	 */
	function action_apply ($key, ...$params)
	{
		global $JC_actions;
		$lista =& $JC_actions;
		
		empty($key) and user_error('Hook es requerido');
		
		$RESULT = NULL;
		
		if ( ! isset($lista[$key]) OR count($lista[$key]) === 0)
		{
			global $JC_actions_defs;

			$lista_defs =& $JC_actions_defs;

			if ( ! isset($lista_defs[$key]) OR count($lista_defs[$key]) === 0)
			{
				return $RESULT;
			}

			$functions = $lista_defs[$key];
		}
		else
		{
			$functions = $lista[$key];
		}
		
		krsort($functions);
		
		foreach($functions as $priority => $funcs){
			foreach($funcs as $func){
				$RESULT = call_user_func_array($func, $params);
			}
		}
		
		return $RESULT;
	}
}


//=========================================================
// Manipuladores
//=========================================================

if ( ! function_exists('redirect'))
{
	/**
	 * redirect()
	 * Redirecciona a una URL
	 *
	 * @param	string	$url	El link a redireccionar
	 * @return	void
	 */
	function redirect($url, $query = NULL)
	{
		error_reporting(0);
		
		$url = parse_url($url);
		
		isset($url['scheme'])   or $url['scheme'] = url('scheme');
		if ( ! isset($url['host']))
		{
			$url['host'] = url('host');
			$url['path'] = url('uri_subpath') . '/' . ltrim($url['path'], '/');
		}
		
		if ( ! is_null($query))
		{
			isset($url['query'])    or $url['query']  = [];
			is_array($url['query']) or $url['query']  = parse_str($url['query']);
			
			$url['query'] = array_merge($url['query'], $query);
		}

		$url = build_url($url);

		while (ob_get_level() > 0)
		{
			ob_end_clean();
		}

		header('Location: ' . $url) OR die('<script>location.replace("' . $url . '");</script>');
		die();
	}
}

if ( ! function_exists('set_status_header'))
{
	/**
	 * set_status_header()
	 * Establece la cabecera del status HTTP
	 *
	 * @param	int		$code	El codigo
	 * @param	string	$text	El texto del estado
	 * @return	void
	 */
	function set_status_header($code = 200, $text = '')
	{
		if (is_cli())
		{
			return;
		}
		
		is_int($code) OR $code = (int) $code;

		if (empty($text))
		{
			$def_codes_text = [
				100	=> 'Continue',
				101	=> 'Switching Protocols',

				200	=> 'OK',
				201	=> 'Created',
				202	=> 'Accepted',
				203	=> 'Non-Authoritative Information',
				204	=> 'No Content',
				205	=> 'Reset Content',
				206	=> 'Partial Content',

				300	=> 'Multiple Choices',
				301	=> 'Moved Permanently',
				302	=> 'Found',
				303	=> 'See Other',
				304	=> 'Not Modified',
				305	=> 'Use Proxy',
				307	=> 'Temporary Redirect',

				400	=> 'Bad Request',
				401	=> 'Unauthorized',
				402	=> 'Payment Required',
				403	=> 'Forbidden',
				404	=> 'Not Found',
				405	=> 'Method Not Allowed',
				406	=> 'Not Acceptable',
				407	=> 'Proxy Authentication Required',
				408	=> 'Request Timeout',
				409	=> 'Conflict',
				410	=> 'Gone',
				411	=> 'Length Required',
				412	=> 'Precondition Failed',
				413	=> 'Request Entity Too Large',
				414	=> 'Request-URI Too Long',
				415	=> 'Unsupported Media Type',
				416	=> 'Requested Range Not Satisfiable',
				417	=> 'Expectation Failed',
				422	=> 'Unprocessable Entity',
				426	=> 'Upgrade Required',
				428	=> 'Precondition Required',
				429	=> 'Too Many Requests',
				431	=> 'Request Header Fields Too Large',

				500	=> 'Internal Server Error',
				501	=> 'Not Implemented',
				502	=> 'Bad Gateway',
				503	=> 'Service Unavailable',
				504	=> 'Gateway Timeout',
				505	=> 'HTTP Version Not Supported',
				511	=> 'Network Authentication Required',
			];

			if (isset($def_codes_text[$code]))
			{
				$text = $def_codes_text[$code];
			}
			else
			{
				$text = 'Non Status Text';
			}
		}

		if (strpos(PHP_SAPI, 'cgi') === 0)
		{
			header('Status: '.$code.' '.$text, TRUE);
			return TRUE;
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && 
							in_array($_SERVER['SERVER_PROTOCOL'], ['HTTP/1.0', 'HTTP/1.1', 'HTTP/2'], TRUE))
			? $_SERVER['SERVER_PROTOCOL'] 
			: 'HTTP/1.1';
		header($server_protocol.' '.$code.' '.$text, TRUE, $code);
		
		return TRUE;
	}
}

if ( ! function_exists('http_code'))
{
	/**
	 * http_code()
	 * Establece la cabecera del status HTTP
	 *
	 * @use	set_status_header()
	 * @param	int		$code	El codigo
	 * @param	string	$text	El texto del estado
	 * @return	void
	 */
	function http_code($code = 200, $text = '')
	{
		return set_status_header($code, $text);
	}
}

if ( ! function_exists('getUTC'))
{
	/**
	 * getUTC()
	 * Obtiene el UTC del timezone actual
	 *
	 * @return string
	 */
	function getUTC()
	{
		$dtz = new DateTimeZone(date_default_timezone_get());
		$dt  = new DateTime('now', $dtz);
		$offset = $dtz->getOffset($dt);

		return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
	}
}

//=========================================================
// JCore
//=========================================================

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
		
		static $saving = FALSE;

		$config = config('log');
		
		is_bool($code) and $show = $code and $code = NULL;
		
		(is_array($severity) and is_null($meta)) and $meta = $severity and $severity = NULL;
		
		is_null($code) and $code = 0;
		
		is_null($meta) and $meta = [];
		is_array($meta) or $meta = (array)$meta;
		
		$meta['time'] = time();
		$meta['datetime'] = function_exists('date2') ? date2('LL') : date('Y-m-d H:i:s');
		$meta['microtime'] = microtime();
		$meta['microtime_float'] = microtime(true);
		
		try
		{
			$APP = APP();
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['APP_loadable'] = isset($APP);
		}
		
		try
		{
			$RSP = RSP();
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['RSP_loadable'] = isset($RSP);
		}
				
		try
		{
			$RTR = RTR();
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['RTR_loadable'] = isset($RTR);
		}
		
		try
		{
			$OPB = OPB();
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['OPB_loadable'] = isset($OPB);
		}
		
		isset($OPB) and
		$meta['buffer'] = $OPB -> stop() -> getContents();

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
		
		// Detectar la ruta del error
		if (is_null($trace))
		{
			$trace = debug_backtrace(false);
			
			if ($trace[0]['function'] === __FUNCTION__ and isset($trace[0]['class']) and $trace[0]['class'] === __CLASS__)
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
		
		$SER = [];
		foreach($_SERVER as $x => $y)
		{
			if (preg_match('/^((GATEWAY|HTTP|QUERY|REMOTE|REQUEST|SCRIPT|CONTENT)\_|REDIRECT_URL|REDIRECT_STATUS|PHP_SELF|SERVER\_(ADDR|NAME|PORT|PROTOCOL))/i', $x))
			{
				$SER[$x] = $y;
			}
		}
		
		$meta['server'] = $SER;
		$meta['disp'] = defined('disp') ? disp : NULL;
		$meta['stat'] = isset($_SESSION['stat']) ? $_SESSION['stat'] : NULL;
		
		try
		{
			$url = url('array');
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['URL_loadable'] = isset($url);
		}
		
		isset($url) and
		$meta['url'] = $url;
		
		try
		{
			$ip_address = ip_address('array');
		}
		catch (\BasicException $e){}
		catch (\Exception $e){}
		catch (\TypeError $e){}
		catch (\Error $e){}
		finally
		{
			$meta['IPADRESS_loadable'] = isset($url);
		}
		
		isset($ip_address) and
		$meta['ip_address'] = $ip_address;
		
		$saving and $error_while_saving = TRUE; // Se produjo un error mientras
		
		if ( ! isset($error_while_saving))
		{
			
		$saving = TRUE;
		$saved = FALSE;
		
		$saved = filter_apply('save_logs', $saved, $message, $severity, $code, $filepath, $line, $trace, $meta);
		
		// Guardar Log en Archivo
		// almacena los logs en un archivo, solo agrega lineas
		// PRIORIDAD II
		if ( ! $saved)
		{
			mkdir2($config['path']);

			$log_file = $config['path'] . DS . 'log-' . date('Y-m') . '.' . $config['file_ext'];
			$msg_file = '';

			if ( ! file_exists($log_file))
			{
				$newfile = TRUE;

				if ($config['file_ext'] === 'php')
				{
					$msg_file .= "<?php exit('No direct script access allowed'); ?>\n\n";
				}
			}

			$msg_file .= $config['format_line']($message, $severity, $code, $filepath, $line, $trace, $meta);

			$result = file_put_contents($log_file, $msg_file, FILE_APPEND | LOCK_EX);

			if (isset($newfile) && $newfile === TRUE)
			{
				chmod($log_file, $config['file_permissions']);
			}
			
			$saved = is_int($result);
		}
		
		}
		## Mostrar Log en Web
		if ((display_errors() and $show) or isset($error_while_saving))
		{
			if ( ! is_cli())
			{
				$message = protect_server_dirs($message);
				$filepath = protect_server_dirs($filepath);
				foreach ($trace as &$error)
				{
					isset($error['file']) or $error['file'] = '[NO FILE]';
					isset($error['line']) or $error['line'] = '[NO LINE]';
					
					$error['file'] = protect_server_dirs($error['file']);
				}
				unset($error);
			}

			if ($severity === 'Exception')
			{
				include @template('errors' . DS . 'exception.php', FALSE);
			}
			else
			{
				include @template('errors' . DS . 'php-error.php', FALSE);
			}
		}
	}
}

if ( ! function_exists('APP'))
{
	/**
	 * APP()
	 * Retorna la instancia del APP
	 * @return	APP
	 */
	function APP()
	{
		return APP::instance();
	}
}

if ( ! function_exists('RTR'))
{
	/**
	 * RTR()
	 * Retorna la instancia del Reponse
	 * @return	RTR
	 */
	function RTR()
	{
		return Router::instance();
	}
}

if ( ! function_exists('RSP'))
{
	/**
	 * RSP()
	 * Retorna la instancia del Reponse
	 * @return	RSP
	 */
	function RSP()
	{
		return Response::instance();
	}
}

if ( ! function_exists('OPB'))
{
	/**
	 * OPB()
	 * Retorna la instancia del OPB
	 * @return	OutputBuffering
	 */
	function OPB()
	{
		return OutputBuffering::instance();
	}
}

if ( ! function_exists('config'))
{
	/**
	 * config()
	 *
	 * Obtiene y retorna la configuración.
	 *
	 * La función lee los archivos de configuración generales tales como los de JCore 
	 * y los que se encuentran en la carpeta 'config' de APPPATH (directorio de la aplicación)
	 *
	 * @param	string 	$get		permite obtener una configuración específica, 
	 * 								si es NULL entonces devolverá toda la configuración.
	 * @param	array 	$replace	reemplaza algunas opciones de la variable $config leida
	 * @param	boolean	$force		si es FALSE, entonces validará que el valor a "reemplazar"
	 *								no exista previamente (solo inserta no reemplaza)
	 * @return	mixed
	 */
	function &config($get = NULL, Array $replace = [], bool $force = FALSE)
	{
		static $config = [];
		
		if (count($config) === 0)
		{
			global $BASES_path;
			
			$BASES_path_ = array_reverse($BASES_path);

			foreach($BASES_path_ as $path)
			{
				if ($file = $path. DS. 'configs'. DS. 'config.php' and file_exists($file))
				{
					require_once $file;
				}
				
				if ($file = $path. DS. 'configs'. DS. ENVIRONMENT. DS. 'config.php' and file_exists($file))
				{
					require_once $file;
				}
			}
		}
		
		foreach ($replace as $key => $val)
		{
			if ( ! $force and isset($config[$key]))
			{
				continue;
			}

			$config[$key] = $val;
		}
		
		if ($get === 'array' or is_null($get))
		{
			return $config;
		}
		
		isset($config[$get]) or $config[$get] = NULL;
		return $config[$get];
	}
}

if ( ! function_exists('_t'))
{
	/**
	 * _t()
	 * Permite la traducción de una frase
	 *
	 * @param	string	Frase a traducir
	 * @param	int		Indica la pluralidad de la frase
	 * @param	string	Lenguaje a retornar; Si NULL entonces toma el lenguaje por defecto
	 * @param	array
	 * @return	string
	 */
	function _t($frase, $n = NULL, $lang = NULL, ...$sprintf)
	{
		static $langs = [];
		
		if (count($langs) === 0)
		{
			global $BASES_path;
			
			$BASES_path_ = array_reverse($BASES_path);

			foreach($BASES_path_ as $path)
			{
				if ($file = $path. DS. 'configs' . DS . 'translate'. DS. 'langs.php' and file_exists($file))
				{
					require_once $file;
				}
				
				if ($file = $path. DS. 'configs' . DS . 'translate'. DS. ENVIRONMENT. DS. 'langs.php' and file_exists($file))
				{
					require_once $file;
				}
			}
		}
		
		if ($frase === 'array')
		{
			return $langs;
		}

		$_sprintf = function($frase, ...$params)
		{
			array_unshift($params, $frase);
			return call_user_func_array('sprintf', $params);
		};
		
		if (is_null($lang))
		{
			if ( ! is_null($n) and ! is_numeric($n) and strlen($n) == 2)
			{
				$lang = $n;
				$n = NULL;
			}
			else
			{
				$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'ES';
			}
		}
		
		$lang = strtoupper($lang);

		if (is_null($n))
		{
			$n = 1;
		}
		
		is_int($n) OR $n = (int)$n;
		array_unshift($sprintf, $n);
		
		if ( ! isset($langs[$lang]) or ! isset($langs[$lang][$frase]))
		{
			if ($lang <> 'ES')
			{
				$bcktrc = debug_backtrace(false);
				$bcktrc_0 = array_shift($bcktrc);
				extract($bcktrc_0);
				
				isset($file) or $file = __FILE__;
				isset($line) or $line = __LINE__;
				
				logger('Frase sin traducción - '.$lang.' ('.$frase.')', E_USER_WARNING, 'Translate', [], $file, $line, $bcktrc, false);
			}
			
			return $_sprintf($frase, $sprintf);
		}
		
		$traduccion = (array)$langs[$lang][$frase];
		
		switch(count($traduccion))
		{
			case 2:
					if($n==1) $frase_traducida = $_sprintf($traduccion[0], $sprintf);
				else          $frase_traducida = $_sprintf($traduccion[1], $sprintf);
				break;
			case 3:
					if($n==1) $frase_traducida = $_sprintf($traduccion[0], $sprintf);
				elseif($n==0) $frase_traducida = $_sprintf($traduccion[2], $sprintf);
				else          $frase_traducida = $_sprintf($traduccion[1], $sprintf);
				break;
			case 4:
					if($n==1) $frase_traducida = $_sprintf($traduccion[0], $sprintf);
				elseif($n==0) $frase_traducida = $_sprintf($traduccion[2], $sprintf);
				elseif($n <0) $frase_traducida = $_sprintf($traduccion[3], $sprintf);
				else          $frase_traducida = $_sprintf($traduccion[1], $sprintf);
				break;
			default:
				$frase_traducida = $_sprintf($traduccion[0], $sprintf);
				break;
		}
		
		return $frase_traducida;
	}
}

if ( ! function_exists('request'))
{
	/**
	 * request()
	 * Obtiene los request ($_GET $_POST)
	 *
	 * @param	string	$get
	 * @return	mixed
	 */
	function &request($get = 'array', $default = NULL, $put_default_if_empty = TRUE)
	{
		static $datos = [];
		
		if (count($datos) === 0)
		{
			$datos = array_merge(
				$_REQUEST,
				$_POST,
				$_GET
			);
			
			$path = explode('/', url('path'));
			foreach($path as $_p)
			{
				if (preg_match('/(.+)(:|=)(.*)/i', $_p, $matches))
				{
					$datos[$matches[1]] = $matches[3];
				}
			}
		}
		
		if ($get === 'array')
		{
			return $datos;
		}
		
		$get = (array)$get;
		
		$return = $datos;
		foreach($get as $_get)
		{
			if ( ! isset($return[$_get]))
			{
				$return = $default;
				break;
			}
			
			if (is_empty($return[$_get]) and $put_default_if_empty)
			{
				$return = $default;
				break;
			}
			
			$return = $return[$_get];
		}
		
		return $return;
	}
}

if ( ! function_exists('url'))
{
	/**
	 * url()
	 * Obtiene la estructura y datos importantes de la URL
	 *
	 * @param	string	$get
	 * @return	mixed
	 */
	function &url($get = 'base')
	{
		static $datos = [];
		
		if (count($datos) === 0)
		{
			$file = __FILE__;
			
			//Archivo index que se ha leído originalmente
			$script_name = $_SERVER['SCRIPT_NAME'];
			
			//Variable indica si el index.php controlador esta dentro de una subcarpeta de donde se va a leer
			defined('SUBPATH') or define('SUBPATH', DS);
			
			//Si el archivo index está dentro de una carpeta desde la raiz (/)
			//No reemplaza la variable SUBPATH
			$uri_subpath = rtrim(str_replace('\\', '/', str_replace(basename($script_name), '', $script_name)), '/');
			$datos['uri_subpath'] = $uri_subpath;

			//Devuelve si usa https (boolean)
			$datos['https'] = FALSE;
			if (
				( ! empty($_SERVER['HTTPS']) && mb_strtolower($_SERVER['HTTPS']) !== 'off') ||
				(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && mb_strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
				( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && mb_strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') ||
				(isset($_SERVER['REQUEST_SCHEME']) and $_SERVER['REQUEST_SCHEME'] === 'https')
			)
			{
				$datos['https'] = TRUE;
			}

			isset($_SERVER['REQUEST_SCHEME']) or $_SERVER['REQUEST_SCHEME'] = 'http' . ($datos['https'] ? 's' : '');

			$_parsed = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			$_parsed = parse_url($_parsed);
			
			//Devuelve 'http' o 'https' (string)
			$datos['scheme'] = $_parsed['scheme'];
			
			//Devuelve el host (string)
			$datos['host'] = $_parsed['host'];
			
			//Devuelve el port (int)
			$datos['port'] = $_parsed['port'];
			
			isset($_parsed['user']) and $datos['user'] = $_parsed['user'];
			isset($_parsed['pass']) and $datos['pass'] = $_parsed['pass'];
			
			$datos['path'] = isset($_parsed['path']) ? $_parsed['path'] : '/';
			empty($uri_subpath) or $datos['path'] = str_replace($uri_subpath, '', $datos['path']);
			
			$datos['query'] = isset($_parsed['query']) ? $_parsed['query'] : '';
			$datos['fragment'] = isset($_parsed['fragment']) ? $_parsed['fragment'] : '';
			
			//Devuelve el port en formato enlace (string)		:8082	para el caso del port 80 o 443 retorna vacío
			$datos['port-link'] = (new class($datos) implements JsonSerializable {
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$port_link = '';
					if ($this->datos['port'] <> 80 and $this->datos['port'] <> 443)
					{
						$port_link = ':' . $this->datos['port'];
					}
					return $port_link;
				}
				
				public function __debugInfo()
				{
					return [
						'port' => $this->datos['port'],
						'port-link' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve si usa WWW (boolean)
			$datos['www'] = (bool)preg_match('/^www\./', $datos['host']);
			
			//Devuelve el base host (string)
			$datos['host-base'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$host_base = explode('.', $this->datos['host']);
					
					while (count($host_base) > 2)
					{
						array_shift($host_base);
					}
					
					$host_base = implode('.', $host_base);
					
					return $host_base;
				}
				
				public function __debugInfo()
				{
					return [
						'host' => $this->datos['host'],
						'host-base' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve el host mas el port (string)			intranet.net:8082
			$datos['host-link'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$host_link = $this->datos['host'] . $this->datos['port-link'];
					return $host_link;
				}
				
				public function __debugInfo()
				{
					return [
						'host' => $this->datos['host'],
						'port-link' => (string)$this->datos['port-link'],
						'host-link' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve el host sin puntos o guiones	(string)	intranetnet
			$datos['host-clean'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$host_clean = preg_replace('/[^a-z0-9]/i', '', $this->datos['host']);
					return $host_clean;
				}
				
				public function __debugInfo()
				{
					return [
						'host' => $this->datos['host'],
						'host-clean' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve el scheme mas el host-link (string)	https://intranet.net:8082
			$datos['host-uri'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$host_uri = $this->datos['scheme'] . '://' . $this->datos['host-link'];
					return $host_uri;
				}
				
				public function __debugInfo()
				{
					return [
						'scheme' => $this->datos['scheme'],
						'host-link' => (string)$this->datos['host-link'],
						'host-uri' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la URL base hasta la aplicación
			$datos['base'] = (new class($datos, $uri_subpath) implements JsonSerializable{
				private $datos;
				private $uri_subpath;
				
				public function __construct(&$datos, $uri_subpath)
				{
					$this->datos =& $datos;
					$this->uri_subpath = $uri_subpath;
				}
				
				public function __toString()
				{
					$base = $this->datos['host-uri'] . $this->uri_subpath;
					return $base;
				}
				
				public function __debugInfo()
				{
					return [
						'host-uri' => (string)$this->datos['host-uri'],
						'uri_subpath' => $this->uri_subpath,
						'base' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la URL base hasta el alojamiento real de la aplicación
			$datos['subpath'] = rtrim(str_replace('\\', '/', SUBPATH), '/');
			
			//Devuelve la URL base hasta el alojamiento real de la aplicación
			$datos['abs'] = (new class($datos, $uri_subpath) implements JsonSerializable{
				private $datos;
				private $uri_subpath;
				private $subpath;
				
				public function __construct(&$datos, $uri_subpath)
				{
					$this->datos =& $datos;
					$this->uri_subpath = $uri_subpath;
				}
				
				public function __toString()
				{
					$abs = $this->datos['host-uri'] . $this->uri_subpath . $this->datos['subpath'];
					return $abs;
				}
				
				public function __debugInfo()
				{
					return [
						'host-uri' => (string)$this->datos['host-uri'],
						'uri_subpath' => $this->uri_subpath,
						'subpath' => $this->datos['subpath'],
						'abs' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la URL base hasta el alojamiento real de la aplicación
			$datos['host-abs'] = (new class($datos, $uri_subpath) implements JsonSerializable{
				private $datos;
				private $uri_subpath;
				private $subpath;
				
				public function __construct(&$datos, $uri_subpath)
				{
					$this->datos =& $datos;
					$this->uri_subpath = $uri_subpath;
				}
				
				public function __toString()
				{
					$abs = str_replace('www.', '', $this->datos['host']) . $this->uri_subpath;
					return $abs;
				}
				
				public function __debugInfo()
				{
					return [
						'host' => (string)$this->datos['host'],
						'uri_subpath' => $this->uri_subpath,
						'host-abs' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la URL completa incluido el PATH obtenido
			$datos['full'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$full = $this->datos['base'] . $this->datos['path'];
					
					return $full;
				}
				
				public function __debugInfo()
				{
					return [
						'base' => (string)$this->datos['base'],
						'path' => $this->datos['path'],
						'full' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la URL completa incluyendo los parametros QUERY si es que hay
			$datos['full-wq'] = (new class($datos) implements JsonSerializable{
				private $datos;
				
				public function __construct(&$datos)
				{
					$this->datos =& $datos;
				}
				
				public function __toString()
				{
					$full_wq = $this->datos['full'] . ( ! empty($this->datos['query']) ? '?' : '' ) . $this->datos['query'];
					
					return $full_wq;
				}
				
				public function __debugInfo()
				{
					return [
						'full' => (string)$this->datos['full'],
						'query' => $this->datos['query'],
						'full-wq' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Devuelve la ruta de la aplicación como directorio del cookie
			$datos['cookie-base'] = $uri_subpath . '/';
			
			//Devuelve la ruta de la aplicación como directorio del cookie hasta la carpeta de la ruta actual
			$datos['cookie-full'] = (new class($datos, $uri_subpath) implements JsonSerializable{
				private $datos;
				private $uri_subpath;
				
				public function __construct(&$datos, $uri_subpath)
				{
					$this->datos =& $datos;
					$this->uri_subpath = $uri_subpath;
				}
				
				public function __toString()
				{
					$cookie_full = $this->uri_subpath . rtrim($this->datos['path'], '/') . '/';
					return $cookie_full;
				}
				
				public function __debugInfo()
				{
					return [
						'uri_subpath' => $this->uri_subpath,
						'path' => $this->datos['path'],
						'cookie-full' => $this->__toString()
					];
				}

				public function jsonSerialize() {
					return $this->__toString();
				}
			});
			
			//Obtiene todos los datos enviados
			$datos['request'] =& request('array');
			
			//Request Method
			$datos['request_method'] = mb_strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'cli');
			
//			$datos['array'] =& $datos;
		}
		
		if ($get === 'array')
		{
			return $datos;
		}
		
		isset($datos[$get]) or $datos[$get] = NULL;
		return $datos[$get];
	}
}

if ( ! function_exists('class2'))
{
	/**
	 * class2()
	 * Buscar una clase correcta
	 *
	 * @param string $class Clase a buscar
	 * @param string $dir Directorio a buscar
	 * @param string $version Versión de la clase a priorizar, si NULO entonces buscará el mayor.
	 * @param mixed $param Parametros a enviar al constructor de la clase
	 * @return string
	 */
	function class2($class, $dir = NULL, $ver = NULL, $param = NULL)
	{
		static $_founds = [];
		
		$class_how_was_called = $class;
		$dir_how_was_called = $dir;
		
// 		if ($dir === 'displays') define('debug', true);
// 		defined('debug') and debug and die_array($class);
//		if ( ! in_array($class, ['BenchMark', 'Response', 'OutputBuffering', 'Router', 'usuario', 'sucursal', 'cbo']))die_array($class);

		if (is_empty($class))
		{
			throw new Exception('Clase no puede estár vacío');
		}
		
		$class = explode('\\', $class);
		$class = array_map('ucfirst', $class);
		$class_dir = DS . implode(DS, $class);
		$class = implode('\\', $class);

		isset($_founds[$class]) or $_founds[$class] = [];

		if (is_null($dir))
		{
			$dir = 'processors';
			isset($_founds[$class][$dir]) and $dir = 'displays';
		}

		switch(mb_strtolower($dir))
		{
			case 'p':case 'pr':case 'pro':case 'proc':case 'process':case 'processor':case 'processors':case 'controller':case 'controllers':case 1:
				$dir = 'processors';
				$class_base = 'Processor';
				break;
			case 'o':case 'ob':case 'obj':case 'object':case 'objects':case 'model':case 'models':case 2:
				$dir = 'objects';
				$class_base = 'Object';
				break;
			case 'd':case 'di':case 'dis':case 'display':case 'displays':case 'view':case 'views':case 3:
				$dir = 'displays';
				$class_base = 'Display';
				break;
			case 'c':case 'cl':case 'cls':case 'clas':case 'class':case 'classes':case 4:
				$dir = 'configs' . DS . 'classes';
				$class_base = '';
				break;
			default:
				$class_base = '';
				break;
		}

		isset($_founds[$class][$dir]) or $_founds[$class][$dir] = [];

		global $BASES_path;
		
		$max_lvl = 0;
		$max_lvl_= 0;
		$max_indx= 0;
		
		foreach($BASES_path as $indx => $basedir)
		{
			$indx_ = count($BASES_path) - $indx + 1;
			
			$basedir .= DS . $dir;
			if ( ! file_exists($basedir))
			{
				continue;
			}
			
			$_class_dir = $class_dir;
			$_class_dir_= explode(DS, $_class_dir);

			if ($dir !== 'objects')
			{
				while ( ! is_dir($basedir . $_class_dir) and  ! is_dir($basedir . strtr($_class_dir, '-', '_')) and count($_class_dir_) >= 3)
				{
					$class_name = strtr(array_pop($_class_dir_), '-_', '__');
					$_class_dir = implode(DS, $_class_dir_);
				}
			}
			
			$lvl = count($_class_dir_);
			
			if ($max_lvl_ === $lvl)
			{
				$indx_ < $max_indx and $lvl++;
			}
			
			$max_lvl_ = $max_lvl;
			$max_indx = $indx_;
			
			$lvl > $max_lvl and $max_lvl = $lvl;

			if ( ! is_empty($_class_dir) and is_dir($basedir . $_class_dir))
			{
				## Versiones
				$_files = directory_map($basedir . $_class_dir);
				
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);
					$version = trim($version, '.');
					
					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;
					
					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = $_class_dir . preg_replace('/\.php$/', '', $file);
				}
			}
			
			if ( ! is_empty($_class_dir) and is_dir($basedir . strtr($_class_dir, '-', '_')))
			{
				## Versiones
				$_files = directory_map($basedir . strtr($_class_dir, '-', '_'));
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);

					$version = trim($version, '.');
					
					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;
					
					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = strtr($_class_dir, '-', '_') . preg_replace('/\.php$/', '', $file);
				}
			}
			
			if (isset($class_name))
			{
				## Versiones
				$_files = directory_map($basedir . $_class_dir);
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace($class_name, '', $version);
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);
					$version = trim($version, '.');

					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;

					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = $_class_dir . preg_replace('/\.php$/', '', $file);
				}
				
				## Versiones
				$_files = directory_map($basedir . strtr($_class_dir, '-', '_'));
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace($class_name, '', $version);
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);
					$version = trim($version, '.');

					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;

					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = strtr($_class_dir, '-', '_') . preg_replace('/\.php$/', '', $file);
				}
			}
			
			$_class_dir = explode(DS, $_class_dir);
			$class_name = strtr(array_pop($_class_dir), '-_', '__');
			$_class_dir = implode(DS, $_class_dir);
			
			if (is_dir($basedir . $_class_dir))
			{
				## Versiones
				$_files = directory_map($basedir . $_class_dir);
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace($class_name, '', $version);
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);
					$version = trim($version, '.');

					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;

					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = $_class_dir . preg_replace('/\.php$/', '', $file);
				}
			}
			
			if (is_dir($basedir . strtr($_class_dir, '-', '_')))
			{
				## Versiones
				$_files = directory_map($basedir . strtr($_class_dir, '-', '_'));
				foreach(array_values($_files) as $file)
				{
					if (is_array($file))
					{
						continue;
					}

					$version = basename($file, '.php');
					$version = str_replace($class_name, '', $version);
					$version = str_replace('v', '', $version);
					$version = str_replace('V', '', $version);
					$version = str_replace('_', '.', $version);
					$version = trim($version, '.');

					(ucfirst($version) === 'Last' or empty($version)) and $version = '999999.999999.9999' . $lvl;

					if ( ! is_version($version))
					{
						continue;
					}
					
					isset($_founds[$class][$dir][$version]) or $_founds[$class][$dir][$version] = strtr($_class_dir, '-', '_') . preg_replace('/\.php$/', '', $file);
				}
			}
		}
		
		uksort($_founds[$class][$dir], function($a, $b){
			return version_compare($a, $b, '<');
		});
		
		(is_null($ver) or empty($ver) or ucfirst($ver)==='Last') and $ver = '999999.999999.9999' . $max_lvl;
		
		$real_class = NULL;
		
		$_fcd = $_founds[$class][$dir];
		
		foreach($_fcd as $_ver => $file)
		{
			if (version_compare($_ver, $ver, '<'))
			{
				break;
			}
			
			$real_class = str_replace(DS, '\\', $file);
		}
		
		if (is_null($real_class) and $_fcd = array_values($_fcd) and count($_fcd) > 0)
		{
			$real_class = str_replace(DS, '\\', $_fcd[0]);
		}
		
		is_null($real_class) and $real_class = '\\' . ltrim($class, '\\');

		$_class = $real_class;
		if ( ! empty($class_base))
		{
			$_class = '\\' . $class_base . $real_class;
		}

		if ($function = [$_class, 'instance'] and is_callable($function))
		{
			return call_user_func($function, $param);
		}
		
		if ($function = [$_class, 'getInstance'] and is_callable($function))
		{
			return call_user_func($function, $param);
		}

		try
		{
			$obj = (new $_class($param));
			return $obj;
		}
		catch(\Error $e)
		{
			if ( ! preg_match('/Class \''.regex($_class).'\' not found/i', $e->getMessage()))
			{
				throw $e;
			}

			throw new \Error('CLASS2: Class \''.$_class.'\' ('.$class_how_was_called.' of '.$dir_how_was_called.') not found');
		}
	}
}

if ( ! function_exists('obj'))
{
	/**
	 * obj()
	 * Buscar una clase object
	 *
	 * @param string $class Clase a buscar
	 * @param string $version Versión de la clase a priorizar, si NULO entonces buscará el mayor.
	 * @param mixed $id Parametros a enviar al constructor de la clase
	 * @return string
	 */
	function &obj($class, $id = NULL, $ver = NULL)
	{
		static $_founds = [];

		if ($class === 'return')
		{
			return $_founds;
		}

		isset($_founds[$class]) or $_founds[$class] = [];

		if ($id === 'return')
		{
			return $_founds[$class];
		}

		if ( ! is_null($id) and isset($_founds[$class][$id]))
		{
			return $_founds[$class][$id];
		}

		$obj = class2($class, 'Object', $ver, $id);

		is_null($id) or $_founds[$class][$id] = $obj;

		return $obj;
	}
}

if ( ! function_exists('exec_callable'))
{
	/**
	 * exec_callable()
	 * Intenta ejecutar una función callable o un método de una clase por iniciar o ya iniciazo
	 *
	 * @todo	$class_pre	Si la función necesita instanciar una clase, el prefijo que se antepondrá a la clase
	 *
	 * @param	string		$function	Función callable o un método de una clase
	 * @param	array|null	$params		Parametros a enviar a la función
	 * @param	array|null	$class_prms	Parametros a enviar a la función
	 * @param	string|null	$class_dir	Si la función necesita instanciar una clase, el directorio del cual buscar el autoload
	 * @param	string|null	$class_ver	Si la función necesita instanciar una clase, la versión del cual buscar el autoload
	 * @return	mixed
	 *
	 * @throws Exception si la clase no existe o la función haya generado la excepción
	 */
	function exec_callable($function, $params = [], $class_prms = [], $class_dir = NULL, $class_ver = NULL)
	{
		$http_verb = APP()->Router->http_verb();
		$responseType = APP()->Response->responseType();
		
		$function_ = $function;
		
		/**
		 * Obteniendo el array dentro del primer parametro array
		 */
		is_array($function) and ! isset($function[1]) and $function = $function[0];

		/**
		 * Validando si la función es n sí un método de una clase
		 */
		if (is_array($function))
		{
			$class_method = array_pop($function);
			$class_method === '__DEFAULT CLASS PROCESSOR__' and $class_method = array_pop($function);

			if (isset($function[0]) and is_object($function[0]))
			{
				$class = $function[0];
				$class_name = get_class($class);
				
				$class_name = explode('\\', $class_name);
				array_shift($class_name); ## $class_dir
				$class_name = implode('\\', $class_name);
			}
			else
			{
				$class_name = implode('\\', $function);
				
				$class_prms = (array)$class_prms;
				array_unshift($class_prms, $class_ver);
				array_unshift($class_prms, $class_dir);
				array_unshift($class_prms, $class_name);

				try
				{
					$class = call_user_func_array('class2', $class_prms);
				}
				catch (\Error $e)
				{
					if ( ! preg_match('/CLASS2: Class \'(.*)\' \('.regex($class_name).' of '.regex($class_dir).'\) not found/i', $e->getMessage()))
					{
						throw $e;
					}

					throw new \BasicException('La función no se puede ejecutar', __LINE__, func_get_args());
				}
			}
			
			$function = NULL;
			
			$last_class_name = explode('\\', $class_name);
			
			$class_name = get_class($class);
			
			$new_class_name = explode('\\', $class_name);

			array_shift($new_class_name); ## $class_dir
			
			while(count($new_class_name) > 0 and count($last_class_name) > 0 and mb_strtolower(strtr($new_class_name[0], '-_', '__')) === mb_strtolower(strtr($last_class_name[0], '-_', '__')))
			{
				array_shift($new_class_name);
				array_shift($last_class_name);
			}
			
			$class_method2 = isset($last_class_name[0]) ? $last_class_name[0] : $class_method;
			
			$class_method = strtr($class_method, '-_', '__');
			$class_method2 = strtr($class_method2, '-_', '__');

			$class_method = $class_method2; ## Forzar generar el error 404
			
			$functions = []; ## posibilidades de funciones
			foreach(['', $http_verb] as $x)
			{
				foreach(['', $responseType] as $y)
				{
					$functions[] = $x . ($x === '' ? '' : '_') . $y . ($y === '' ? '' : '_') . $class_method;
					$functions[] = $x . ($x === '' ? '' : '_') . $y . ($y === '' ? '' : '_') . $class_method2;
					
					$functions[] = $y . ($y === '' ? '' : '_') . $x . ($x === '' ? '' : '_') . $class_method;
					$functions[] = $y . ($y === '' ? '' : '_') . $x . ($x === '' ? '' : '_') . $class_method2;
				}
			}
			
			$functions = array_reverse($functions);
			
			$functions = array_merge(array_map(function($func) use ($class){
				return [$class, $func];
			}, $functions), array_map(function($func) use ($class_name){
				return [$class_name, $func];
			}, $functions));

			foreach($functions as $function)
			{
				if ( ! is_callable($function))
				{
					$function = NULL;
				}
				else
				{
					break;
				}
			}
		}

		/**
		 * Ejecutando la función
		 */
		if (is_null($function) or ! is_callable($function))
		{
			throw new \BasicException('La función no se puede ejecutar', __LINE__, func_get_args());
		}
		
		if (is_empty($params))
		{
			return call_user_func($function);
		}
		
		return call_user_func_array($function, $params);
	}
}


//=========================================================
// Views
//=========================================================

if ( ! function_exists('_o'))
{
	/**
	 * _o()
	 * Obtiene el ob_content de una función
	 *
	 * @param callable
	 * @return string
	 */
	function _o (callable ...$callbacks)
	{
		ob_start();
		foreach($callbacks as $callback)
		{
			call_user_func($callback);
		}
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
}

if ( ! function_exists('template'))
{
	/**
	 * template()
	 * Obtiene el archivo de una vista
	 *
	 * @param string
	 * @return string
	 */
	function template ($file, $return_content = TRUE, $declared_variables = [])
	{
		static $dirs = [];
		
		if (count($dirs) === 0)
		{
			$_subdir = is_cli() ? 'cli' : 'html';
			
			global $BASES_path;
			
			foreach($BASES_path as $path)
			{
				if ($dir = $path . DS . 'templates' . DS . ENVIRONMENT . DS . $_subdir 
					and $dir = realpath($dir) 
					and $dir !== FALSE
					and file_exists($dir)
					and is_dir($dir))
				{
					$dirs[] = $dir;
				}
				
				if ($dir = $path . DS . 'templates' . DS . ENVIRONMENT 
					and $dir = realpath($dir) 
					and $dir !== FALSE
					and file_exists($dir)
					and is_dir($dir))
				{
					$dirs[] = $dir;
				}
				
				if ($dir = $path . DS . 'templates' . DS . $_subdir 
					and $dir = realpath($dir) 
					and $dir !== FALSE
					and file_exists($dir)
					and is_dir($dir))
				{
					$dirs[] = $dir;
				}
				
				if ($dir = $path . DS . 'templates'
					and $dir = realpath($dir) 
					and $dir !== FALSE
					and file_exists($dir)
					and is_dir($dir))
				{
					$dirs[] = $dir;
				}
			}
		}
		
		$directory = dirname($file);
		$file_name = basename($file, '.php') . '.php';
		
		if ($directory === '.')
		{
			$directory = DS;
		}
		else
		{
			$directory = strtr($directory, '/\\', DS.DS);
			$directory = DS . ltrim($directory, DS);
		}
		
		$file_view = NULL;
		foreach ($dirs as $dir)
		{
			if ( ! file_exists($dir))
			{
				continue;
			}

			$file_view = $dir . $directory . DS . $file_name;

			if ( ! file_exists($file_view))
			{
				$file_view = NULL;
				continue;
			}
			break;
		}
		
		if (is_null($file_view))
		{
			trigger_error('Vista `' . $file . '` no encontrado', E_USER_WARNING);
			return NULL;
		}

		if (is_array($return_content))
		{
			$declared_variables = (array)$declared_variables;
			$declared_variables = array_merge($return_content, $declared_variables);
			
			$return_content = TRUE;
		}
		
		if ($return_content)
		{
			ob_start();
			
			extract($declared_variables, EXTR_REFS);
			
			include $file_view;
			
			$content = ob_get_contents();
			ob_end_clean();
			
			return $content;
		}

		return $file_view;
	}
}

/**
 * $gi_realsize
 * Variable que almacena el verdadero tamaño de la imagen procesada
 * @internal
 */
$gi_realsize = [];

if ( ! function_exists('get_image'))
{
	/**
	 * get_image()
	 * Obtiene la ruta convertida de la imagen
	 *
	 * @todo si es externo, validar que en el $url['query'] no tenga parametros de 
	 *		'size' o 'crop' que puedan afectar al app
	 *
	 * @todo retornar el enlace utilizando URI de config y no el host
	 *       por el momento solo sirve para subdominios
	 *
	 * @todo si el archivo no tiene host y no existe en la carpeta de imagenes 
	 *       entonces validar si existe en el directorio local e intentar copiarlo 
	 *       en la carpeta de imagenes
	 *
	 * @since 2.0 Agregado la configuración en modo zonas
	 * @since 1.0
	 *
	 * @param string Ruta de la imagen
	 * @param array  Opciones de la imagen
	 * @param string Ruta de la imagen por defecto en caso de que no se encuentre la primera imagen
	 * @return string
	 */
	function get_image($src, $opt = [], $def = NULL)
	{
		static $zones = [];
		global $gi_realsize;
		
		/**
		 * OBTENER LAS ZONAS DE LAS IMÁGENES
		 */
		if (empty($zones))
		{
			$zones =& config('images_zones');
			
			/**
			 * AGREGANDO ZONAS POR DEFECTO
			 */
			array_unshift($zones, [
				'uri' => url('host-abs'),
				'abspath' => HOMEPATH,
				'path' => '/assets/img',
				'slug' => 'img'
			]);

			array_unshift($zones, [
				'uri' => url('host-abs'),
				'abspath' => ABSPATH,
				'path' => '/assets/img',
				'slug' => 'img'
			]);
		}
		
		
		/**
		 * CORRIGIENDO $src
		 */
		$zone = end($zones);
		
		$url = parse_url($src);

		isset($url['scheme']) or $url['scheme']  = url('scheme');
		isset($url['host'])   or $url['host']  = (string)$zone['uri'];// Por defecto es el host de la IMG
		isset($url['query'])  or $url['query'] = '';
		isset($url['path'])   or $url['path']  = '';

		$url['path'] = '/' . ltrim($url['path'], '/');

		$src = build_url($url);

		/**
		 * RECORRIENDO LAS ZONAS
		 * Aquel que matchee se almacena en $zone que por defecto es el primero que ha encontrado
		 */
		$externo = TRUE;

		foreach(array_reverse($zones) as $_zone)
		{
			if (preg_match('#'.regex($_zone['uri']).'#i', $src))
			{
				$zone = $_zone;
				$externo = FALSE;
				break;
			}
		}

		/**
		 * EXTRAYENDO LOS DATOS DE LA ZONA ENCONTRADA
		 *
		 * * **uri**, host y posiblemente subcarpetas
		 * * **abspath**, directorio absoluto hasta llegar a la carpeta del uri
		 * * **path**, directorio interno donde se encuentran los recursos
		 * * **slug**, procesador de las imagenes del uri
		 */
		extract($zone, EXTR_REFS);

		/**
		 * DESCARGAR LA IMAGEN SI ES EXTERNO AL SERVER
		 */
		if ($externo)
		{
			$url = parse_url($src);

			isset($url['query']) or $url['query'] = '';
			isset($url['fragment']) or $url['fragment'] = '';

			extract($url, EXTR_PREFIX_ALL, 'src');

			$directorio = explode('/', $src_path);
			count($directorio) and empty($directorio[0]) and array_shift($directorio);

			array_unshift($directorio, $src_host);
			array_unshift($directorio, 'externo');
			array_unshift($directorio, '');

			$file_name = array_pop($directorio);

			$file_name = explode('.', $file_name);
			$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
			$file_name = implode('.', $file_name);

			$directorio = implode(DS, $directorio);

			if ( ! empty($src_query) or ! empty($src_fragment))
			{
				$file_name = md5(json_encode([
					$file_name,
					$src_query,
					$src_fragment
				]));
			}

			$file_saved = $directorio . DS . $file_name . $file_ext;

			mkdir2(dirname($path . $file_saved), $abspath);

			if ( ! file_exists($abspath . $path . $file_saved))
			{
				try
				{
					$contents = file_get_contents($src);

					if (empty($contents))
					{
						throw new Exception('Contenido Vacío');
					}
				}
				catch(Exception $e)
				{
					if ( ! is_empty($def))
					{
						return get_image($def, $opt);
					}
					return $src;
				}

				file_put_contents($abspath . $path . $file_saved, $contents);
			}

			$url['host'] = $zone['uri'];
			$url['path'] = strtr($file_saved, '\\/', '//');

			$url['query'] = '';
			$url['fragment'] = '';

			$src = build_url($url);
		}
		unset($externo);

		/**
		 * PARSEANDO LA SRC
		 */
		$url = parse_url($src);

		isset($url['query']) or $url['query'] = '';
		isset($url['fragment']) or $url['fragment'] = '';

		/**
		 * EXTRAYENDO LOS DATOS DE LA URL
		 */
		extract($url, EXTR_PREFIX_ALL, 'src');

		// Obtener los parametros de la SRC
		parse_str($src_query, $src_params);

		$directorio = explode('/', $src_path);
		count($directorio) and empty($directorio[0]) and array_shift($directorio);

		$file_name = array_pop($directorio);

		$file_name = explode('.', $file_name);
		$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
		$file_name = implode('.', $file_name);

		if (empty($file_name))
		{
			// No hay ningun nombre de archivo en la $src
			return $src;
		}

		// Eliminar carpetas del uri
		$uri_array = explode('/', $uri);
		array_shift($uri_array);

		while (count($directorio) > 0 and count($uri_array) > 0 and $directorio[0] === $uri_array[0])
		{
			array_shift($directorio);
			array_shift($uri_array);
		}
		unset($uri_array);

		// Eliminar carpetas del slug
		if (count($directorio) > 0 and $directorio[0] === $slug)
		{
			array_shift($directorio);
		}

		// Eliminar carpetas del path
		$path_array = explode('/', $path);
		count($path_array) and empty($path_array[0]) and array_shift($path_array);

		while (count($directorio) > 0 and count($path_array) > 0 and $directorio[0] === $path_array[0])
		{
			array_shift($path_array);
			array_shift($directorio);
		}
		unset($path_array);

		isset($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial
		empty($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial

		$directorio = implode(DS, $directorio);

		/**
		 * OBTENCIÓN DE LAS OPCIONES
		 */
		
		if ( ! is_array($opt))
		{
			if (is_callable($opt))
			{
				$opt = $opt($src, $url, $src_params);
			}

			if (is_string($opt) or ! is_array($opt))
			{
				$opt = (string)$opt;
				$opt = ['size' => $opt];
			}
		}
	
		$opt = array_merge([
			'size'    => NULL,
			'crop'    => NULL,
			'offset'  => NULL,

			'quality' => NULL,
		], (array)$opt);

		extract($opt, EXTR_REFS);

		// Buscar parametro de Quality en el nombre
		$opts_in_name = explode('@', $file_name);
		$opts_in_name_base = array_shift($opts_in_name);//Elimino el primero porque corresponde al nombre

		if (count($opts_in_name) > 0)
		{
			foreach($opts_in_name as $ind => $par)
			{
				if ( ! preg_match('#^[0-4]X$#i', $par))
				{
					// Valores autorizados:
					// @0X  ~ verylow
					// @1X  ~ low
					// @2X  ~ normal
					// @3X  ~ hight
					// @4X  ~ veryhight
					continue;
				}
				
				if (is_null($opt['quality']))
				{
					$opt['quality'] = $par;
				}
				
				unset($opts_in_name[$ind]);
			}
			
			$file_name = $opts_in_name_base;
			if (count($opts_in_name) > 0)
			{
				$file_name .= '@' . implode('@', $opts_in_name);
			}
		}

		// Buscar parametro no Quality en el nombre
		$opts_in_name = explode('.', $file_name);
		$opts_in_name_base = array_shift($opts_in_name);//Elimino el primero porque corresponde al nombre
		
		if (count($opts_in_name) > 0)
		{
			foreach($opts_in_name as $ind => $par)
			{
				if ( ! preg_match('#^(is([0-9]+)X([0-9]+)|ic[0-1]|io([0-9]+)X([0-9]+))$#i', $par))
				{
					// Valores autorizados:
					// is1234X4321
					// ic1	ic0
					// io12X0
					continue;
				}
				
				switch(mb_substr($par, 0, 2))
				{
					case 'is':
						if (is_null($opt['size']))
						{
							$opt['size'] = mb_substr($par, 2);
						}
						break;
					case 'ic':
						if (is_null($opt['crop']))
						{
							$opt['crop'] = (bool)(int)mb_substr($par, 2, 1);
						}
						break;
					case 'io':
						if (is_null($opt['offset']))
						{
							$opt['offset'] = mb_substr($par, 2);
						}
						break;
				}
				
				unset($opts_in_name[$ind]);
			}
			
			$file_name = $opts_in_name_base;
			if (count($opts_in_name) > 0)
			{
				$file_name .= '.' . implode('.', $opts_in_name);
			}
		}

		foreach(array_keys($opt) as $opt_name)
		{
			if (isset($src_params[$opt_name]))
			{
				if (is_null($opt[$opt_name]))
				{
					$opt[$opt_name] = $src_params[$opt_name];
				}

				if ( ! $externo)
				{
					unset($src_params[$opt_name]);
				}
				// Si es externo puede que el campo sea necesario
			}
		}

		/**
		 * El archivo real
		 */
		$real_file = $abspath . $path . $directorio . DS .$file_name . $file_ext;
		$real_file = strtr($real_file, '/\\', DS.DS);

		$file_size = [1, 1]; ## 1x1 para que no produzca errores
		$gi_realsize =& $file_size;

		try
		{
			if ( ! file_exists($real_file))
			{
				throw new Exception('Archivo original no existe');
			}
			
			$mime = filemime($real_file);
			$extension = FT()->getExtensionByMime($mime);
			
			if ($extension === NULL)
			{
				throw new Exception('Extensión no encontrada');
			}
			
			if (is_null($file_ext))
			{
				$file_ext = $extension;
			}
			
			$tipo = $extension()['type'];
			
			if ($tipo !== 'IMAGEN')
			{
				throw new Exception('Archivo es ' . $tipo);
			}
			
			$file_size = getimagesize($real_file);
		}
		catch(Exception $e)
		{}

		/**
		 * Obtener datos de las opciones
		 */
		// Verificar las OPCIONES
		IF (is_null($size))
		{
			//Obteners el tamaño del archivo original
			$size = [$file_size[0], $file_size[1]];
		}
		
		IF (is_string($size))
		{
			//Obteners el tamaño del archivo original
			$size = preg_split('/x/i', $size, 2);
		}
		
		IF (is_numeric($size))
		{
			//Obteners el tamaño del archivo original
			$size = [$size, $size];
		}
		
		$size = (array)$size;
		
		if ( ! isset($size[1]))
		{
			$size[1] = $file_size[1];
		}
		
		if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[0], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[2];
			
			$width = $file_size[0];
			if ($matches[1] == '*')
			{
				if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[1], $matches_temp))
				{
					//obtener el porcentaje del width original
					$percent_temp = (double)$matches_temp[2];

					$height = $file_size[1];
					if ($matches_temp[1] == '*')
					{
						throw new Exception('No pueden haber dos * en los valores del Tamaño de imagen');
					}

					$size[1] = $height * $percent_temp / 100;
				}
				
				$size[1] = (int) $size[1];

				if ($size[1] == 0)
				{
					$size[1] = $file_size[1];
				}
				
				$width = $size[1] * $file_size[1] / $width;
			}
			
			$size[0] = $width * $percent / 100;
		}
		
		$size[0] = (int) $size[0];
		
		if ($size[0] == 0)
		{
			$size[0] = $file_size[0];
		}
		
		if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[1], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[2];
			
			$height = $file_size[1];
			if ($matches[1] == '*')
			{
				$height = $size[0] * $file_size[0] / $height;
			}
			
			$size[1] = $height * $percent / 100;
		}
		
		$size[1] = (int) $size[1];
		
		if ($size[1] == 0)
		{
			$size[1] = $file_size[1];
		}
		
		if (is_null($crop))
		{
			$crop = FALSE;
		}
		
		if (is_null($offset))
		{
			$offset = [0, 0];
		}
		
		IF (is_string($offset))
		{
			//Obteners el tamaño del archivo original
			$offset = preg_split('/x/i', $offset, 2);
		}
		
		IF (is_numeric($offset))
		{
			//Obteners el tamaño del archivo original
			$offset = [$offset, $offset];
		}
		
		$offset = (array)$offset;
		
		if ( ! isset($offset[1]))
		{
			$offset[1] = 0;
		}
		
		if (preg_match('#^([0-9\.]+)\%$#', $offset[0], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[1];
			
			$width = $size[0];
			
			$offset[0] = $width * $percent / 100;
		}
		
		$offset[0] = (int) $offset[0];
		
		if (preg_match('#^([0-9\.]+)\%$#', $offset[1], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[1];
			
			$height = $size[1];
			
			$offset[1] = $height * $percent / 100;
		}
		
		$offset[1] = (int) $offset[1];
		
		IF (is_null($quality))
		{
			$quality = '1X';
		}
		
		$quality = mb_strtoupper($quality);
		

		/**
		 * Formando la ruta del nuevo archivo
		 */
		$opt_uri = '';
		$opt_uri.= '.is' . implode('x', $size);
		
		if ($crop)
		{
			$opt_uri .= '.ic1';
		}
		
		if ($offset[0] > 0 and $offset[1] > 0)
		{
			$opt_uri .= '.io' . implode('x', $offset);
		}
		
		if ($quality <> '1X')
		{
			$opt_uri .= '@' . $quality .'X';
		}

		/**
		 * Corrigiendo URI (www) si es el mismo host
		 * Si el host a comparar no debe usar www si se va a comparar al host actual
		 */
		if (preg_match('#'.regex(str_replace('www.', '', $uri)).'#i', url('host-abs')))
		{
			$uri_www = preg_match('/^www\./i', $uri);
			$url_www = url('www');
			
			if ($url_www !== $uri_www)
			{
				$uri = $url_www ? ('www.'.$uri) : preg_replace('/^www\./i', '', $uri);
			}
		}

		/**
		 * El archivo final
		 */
		$the_file = (is_empty($slug) ? '' : ('/' . $slug)) . $path . $directorio . '/' . $file_name . $opt_uri . $file_ext;
		$the_file = strtr($the_file, '/\\', '//');

		$the_file_path = $abspath . str_replace('/', DS, $the_file);

		$time = file_exists($real_file) ? filemtime($real_file) : 404;
		if (file_exists($the_file_path))
		{
			$_time = filemtime($the_file_path);
			if ($time > $_time)
			{
				unlink($the_file_path);
			}
		}

		$the_file_uri = url('scheme') . '://' . $uri . $the_file . '?' . $time;

		return $the_file_uri;
	}
}

if ( ! function_exists('get_file'))
{
	/**
	 * get_file()
	 * Obtiene la ruta de un archivo
	 *
	 * @since 1.0
	 *
	 * @param string $src	Ruta del archivo
	 * @param int/null $cache_time	Si la ruta es externa, actualizará el archivo cada esta cantidad de segundos
	 * @return string
	 */
	function get_file($src, $cache_time = NULL)
	{
		static $zone = [];

		/**
		 * OBTENER LA CONFIGURACIÓN DE LOS FILES
		 */
		if (empty($zone))
		{
			$zone =& config('files');
		}

		/**
		 * CORRIGIENDO $src
		 */
		$url = parse_url($src);

		isset($url['scheme']) or $url['scheme']  = url('scheme');
		isset($url['host'])   or $url['host']  = (string)$zone['uri'];
		isset($url['query'])  or $url['query'] = '';
		isset($url['path'])   or $url['path']  = '';

		$url['path'] = '/' . ltrim($url['path'], '/');

		$src = build_url($url);

		/**
		 * RECORRIENDO LAS ZONAS
		 * Aquel que matchee se almacena en $zone que por defecto es el primero que ha encontrado
		 */
		$externo = TRUE;

		if (preg_match('#'.regex($zone['uri']).'#i', $src))
		{
			$externo = FALSE;
		}

		/**
		 * EXTRAYENDO LOS DATOS DE LA ZONA ENCONTRADA
		 *
		 * * **uri**, host y posiblemente subcarpetas
		 * * **abspath**, directorio absoluto hasta llegar a la carpeta del uri
		 * * **path**, directorio interno donde se encuentran los recursos
		 * * **slug**, procesador de las imagenes del uri
		 */
		extract($zone, EXTR_REFS);

		/**
		 * DESCARGAR EL ARCHIVO SI ES EXTERNO AL SERVER
		 */
		if ($externo)
		{
			is_null($cache_time) and $cache_time = 60 * 60 * 24 * 365;
			
			$url = parse_url($src);

			isset($url['query']) or $url['query'] = '';
			isset($url['fragment']) or $url['fragment'] = '';

			extract($url, EXTR_PREFIX_ALL, 'src');

			$directorio = explode('/', $src_path);
			count($directorio) and empty($directorio[0]) and array_shift($directorio);

			array_unshift($directorio, $src_host);
			array_unshift($directorio, 'externo');
			array_unshift($directorio, '');

			$file_name = array_pop($directorio);

			$file_name = explode('.', $file_name);
			$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
			$file_name = implode('.', $file_name);

			$directorio = implode(DS, $directorio);

			if ( ! empty($src_query) or ! empty($src_fragment))
			{
				$file_name = md5(json_encode([
					$file_name,
					$src_query,
					$src_fragment
				]));
			}

			$file_saved = $directorio . DS . $file_name . $file_ext;

			mkdir2(dirname($file_saved), $abspath);

			if (file_exists($file_saved) and time() - filemtime($file_saved) > $cache_time)
			{
				unlink($file_saved);
			}

			if ( ! file_exists($abspath . $file_saved))
			{
				try
				{
					$contents = file_get_contents($src);

					if (empty($contents))
					{
						throw new Exception('Contenido Vacío');
					}
				}
				catch(Exception $e)
				{
					return $src;
				}

				file_put_contents($abspath . $file_saved, $contents);
			}

			$url['host'] = $zone['uri'];
			$url['path'] = strtr($file_saved, '\\/', '//');

			$url['query'] = '';
			$url['fragment'] = '';

			$src = build_url($url);
		}
		unset($externo);

		/**
		 * PARSEANDO LA SRC
		 */
		$url = parse_url($src);

		isset($url['query']) or $url['query'] = '';
		isset($url['fragment']) or $url['fragment'] = '';

		/**
		 * EXTRAYENDO LOS DATOS DE LA URL
		 */
		extract($url, EXTR_PREFIX_ALL, 'src');

		// Obtener los parametros de la SRC
		parse_str($src_query, $src_params);

		$directorio = explode('/', $src_path);
		count($directorio) and empty($directorio[0]) and array_shift($directorio);

		$file_name = array_pop($directorio);

		$file_name = explode('.', $file_name);
		$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
		$file_name = implode('.', $file_name);

		if (empty($file_name))
		{
			// No hay ningun nombre de archivo en la $src
			return $src;
		}

		// Eliminar carpetas del uri
		$uri_array = explode('/', $uri);
		array_shift($uri_array);

		while (count($directorio) > 0 and count($uri_array) > 0 and $directorio[0] === $uri_array[0])
		{
			array_shift($directorio);
			array_shift($uri_array);
		}
		unset($uri_array);

		isset($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial
		empty($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial

		$directorio = implode(DS, $directorio);

		/**
		 * El archivo real
		 */
		$real_file = $abspath . $directorio . DS .$file_name . $file_ext;
		$real_file = strtr($real_file, '/\\', DS.DS);

		/**
		 * Corrigiendo URI (www) si es el mismo host
		 * Si el host a comparar no debe usar www si se va a comparar al host actual
		 */
		if (preg_match('#'.regex(str_replace('www.', '', $uri)).'#i', url('host-abs')))
		{
			$uri_www = preg_match('/^www\./i', $uri);
			$url_www = url('www');
			
			if ($url_www !== $uri_www)
			{
				$uri = $url_www ? ('www.'.$uri) : preg_replace('/^www\./i', '', $uri);
			}
		}

		/**
		 * El archivo final
		 */
		$the_file = $directorio . '/' . $file_name . $file_ext;
		$the_file = strtr($the_file, '/\\', '//');

		$the_file_path = $abspath . str_replace('/', DS, $the_file);

		$time = file_exists($real_file) ? filemtime($real_file) : $url['query'];//time();
		if (file_exists($the_file_path))
		{
			$_time = filemtime($the_file_path);
			if ($time > $_time)
			{
				unlink($the_file_path);
			}
		}

		$the_file_uri = url('scheme') . '://' . $uri . $the_file . '?' . $time;

		return (new class ($the_file_uri, $the_file_path) implements JsonSerializable {
			private $the_file_uri;
			private $the_file_path;
				
			public function __construct ($the_file_uri, $the_file_path)
			{
				$this->the_file_uri  = $the_file_uri;
				$this->the_file_path = $the_file_path;
			}
			
			public function getPath()
			{
				return $this->the_file_path;
			}
			
			public function __toString()
			{
				return $this->the_file_uri;
			}
			
			public function __debugInfo()
			{
				return [
					'uri' => $this->the_file_uri,
					'path' => $this->the_file_path
				];
			}

			public function jsonSerialize() {
				return $this->__toString();
			}
		});
	}
}


//=========================================================
// Handlers
//=========================================================

if ( ! function_exists('_autoload'))
{
	/**
	 * _autoload()
	 * Función a ejecutar para leer una clase que aún no ha sido declarada
	 * 
	 * @param string $class
	 * @return void
	 */
	function _autoload($class)
	{
		/**
		 * $_groups
		 * Listado de grupos de posible extensión
		 */
		static $_groups = [
			'Exception', 
			'Object', 
			'Output'
		];
		
		/**
		 * $_directorys
		 * Listado de directorios a leer
		 */
		static $_directorys = [
			'displays', 'objects', 'processors',
			'configs'.DS.'classes', 'configs'.DS.'libs'
		];

		/**
		 * $subclass_prefix
		 * Prefijo con el cual llaman a las subclases
		 */
		$subclass_prefix = config('subclass_prefix');

		/**
		 * $class_structure
		 * Convirtiendo la clase como array
		 */
		$class_structure = explode('\\', $class);
		
		/**
		 * $start_ws
		 * Identificar si han llamado a la clase como \ (backslash)
		 */
		$start_ws = FALSE;
		empty($class_structure[0]) and $start_ws = TRUE and array_shift($class_structure);

		/**
		 * $class_name
		 * Nombre de la clase
		 */
		$class_name = array_pop($class_structure);
		
		$class_name === 'ObjectTable'    and $class !== 'Object\ObjectTable' and $class_async = '\Object\ObjectTable';
		$class_name === 'BasicException' and $class !== 'BasicException'     and $class_async = '\BasicException'    ;
		$class_name === 'JArray'         and $class !== 'JArray'             and $class_async = '\JArray'            ;
		
		if (count($class_structure) > 0)
		{
			$_class_base = array_shift($class_structure);

			foreach($_directorys as $dir)
			{
				if (preg_match('/^' . regex($_class_base) . '/i', $dir))
				{
					/**
					 * $class_base
					 * Iddentificar la base de la clase
					 */
					$class_base = $dir;
					break;
				}
			}

			isset($class_base) or array_unshift($class_structure, $_class_base);
		}
		
		/**
		 * $directory
		 * Directorio a buscar
		 */
		$directory = implode(DS, $class_structure);
		empty($directory) or $directory = DS . $directory;

		if ( ! is_null($subclass_prefix))
		{
			$real_class_name = explode((string)$subclass_prefix, $class_name, 2);
			if (count($real_class_name) === 2)
			{
				$subclass_name = $class_name;
				$class_name = $real_class_name[1];

				/**
				 * $subclass
				 * Directorio a buscar
				 */
				$subclass = $class;
				$class = implode('\\', $class_structure) . $class_name;
			}
		}

		if (preg_match('/(.+)('.implode('|', $_groups).')/i', $class_name, $matched))
		{
			$group = ucfirst($matched[2]);
		}
		
		$class_ws = ($start_ws ? '' : '\\') . $class;

		$class_b = $class;
		$class_bws = $class_ws;

		if (isset($class_base))
		{
			$explode = explode('\\', $class);
			array_shift($explode);
			
			$class_b = implode('\\', $explode);
			$class_bws = '\\' . $class_b;
		}
		
		$class_file = $directory . DS . $class_name . '.php';
		isset($subclass_name) and $subclass_file = $directory . DS . $subclass_name . '.php';

		global $BASES_path;
		
		## Requiriendo los archivos de la CLASE principal
		foreach($_directorys  as $basedir)
		{
			if (isset($class_base) and $basedir !== $class_base)
			{
				continue;
			}

			foreach($BASES_path as $path)
			{
				if (isset($group) and $file = $path. DS. $basedir. DS. ENVIRONMENT. DS. $group. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}
				
				if (isset($group) and $file = $path. DS. $basedir. DS. ENVIRONMENT. DS. $group.'s'. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if ($file = $path. DS. $basedir. DS. ENVIRONMENT. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if (isset($group) and $file = $path. DS. $basedir. DS. $group. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if (isset($group) and $file = $path. DS. $basedir. DS. $group.'s'. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if ($file = $path. DS. $basedir. $class_file and file_exists($file))
				{
					if (class_exists($class, FALSE) === FALSE and 
						class_exists($class_ws, FALSE) === FALSE and 
						class_exists($class_b, FALSE) === FALSE and 
						class_exists($class_bws, FALSE) === FALSE)
					{
						require_once $file;
					}
				}
			}
		}

		// Otros directorios
		$paths = (array)config('autoload_paths');
		foreach($paths as $path)
		{
			if ($file = $path. $class_file and file_exists($file))
			{
				if (class_exists($class, FALSE) === FALSE and 
					class_exists($class_ws, FALSE) === FALSE and 
					class_exists($class_b, FALSE) === FALSE and 
					class_exists($class_bws, FALSE) === FALSE)
				{
					require_once $file;
				}
			}
		}
		
		class_exists($class, FALSE) === FALSE and class_exists($class_ws , FALSE) === TRUE and class_alias($class_ws , $class);
		class_exists($class, FALSE) === FALSE and class_exists($class_b  , FALSE) === TRUE and class_alias($class_b  , $class);
		class_exists($class, FALSE) === FALSE and class_exists($class_bws, FALSE) === TRUE and class_alias($class_bws, $class);
		
		class_exists($class, FALSE) === FALSE and isset($class_async) and class_exists($class_async) === TRUE and class_alias($class_async, $class);
		
		//@# si es subclass entonces buscará los archivos de la subclase
		if ( ! isset($subclass))
		{
			return;
		}
		
		## Requiriendo los archivos de la SUBCLASE
		foreach([
			'processors', 	## Contiene todos los procesadores de REQUESTs
			'displays', 	## Contiene todos las pantallas a mostrar de REQUESTs
			'templates', 	## Contiene partes de HTML que pueden ser leídos por las pantallas
			'objects', 		## Contiene todos los objetos utilizables peculiarmente enlaces con la BBDD
			'class', 		## Contiene clases utilizables
		]  as $basedir)
		{
			
			foreach($BASES_path_ as $path)
			{
				if (isset($group) and $file = $path. DS. $basedir. DS. ENVIRONMENT. DS. $group. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if (isset($group) and $file = $path. DS. $basedir. DS. ENVIRONMENT. DS. $group.'s'. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if ($file = $path. DS. $basedir. DS. ENVIRONMENT. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if (isset($group) and $file = $path. DS. $basedir. DS. $group. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if (isset($group) and $file = $path. DS. $basedir. DS. $group.'s'. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}

				if ($file = $path. DS. $basedir. $subclass_file and file_exists($file))
				{
					if (class_exists($subclass, FALSE) === FALSE)
					{
						require_once $file;
					}
				}
			}
		}

		// Otros directorios
		$paths = (array)config('autoload_paths');
		foreach($paths as $path)
		{
			if ($file = $path. $subclass_file and file_exists($file))
			{
				if (class_exists($subclass, FALSE) === FALSE)
				{
					require_once $file;
				}
			}
		}
	}
}

if ( ! function_exists('_error_handler'))
{
	/**
	 * _error_handler()
	 * Función a ejecutar al producirse un error durante la aplicación
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function _error_handler($severity, $message, $filepath, $line)
	{
		$is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

		if ($is_error)
		{
			set_status_header(500);
		}

		if (($severity & error_reporting()) !== $severity)
		{
			return;
		}

		logger($message, 
			   $severity, 
			   $severity, 
			   [], 
			   $filepath, 
			   $line);

		if ($is_error)
		{
			exit(1);
		}
	}
}

if ( ! function_exists('_exception_handler'))
{
	/**
	 * _exception_handler()
	 * Función a ejecutar cuando se produzca una exception
	 *
	 * @param	Exception	$exception
	 * @return	void
	 */
	function _exception_handler($exception)
	{
		logger($exception);
		
		is_cli() OR set_status_header(500);
		exit(1);
	}
}

if ( ! function_exists('_shutdown_handler'))
{
	/**
	 * _shutdown_handler()
	 * Función a ejecutar antes de finalizar el procesamiento de la aplicación
	 *
	 * @return void
	 */
	function _shutdown_handler()
	{
		$last_error = error_get_last();
		
		if ( isset($last_error) &&
			($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
		{
			_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
		
		action_apply('do_when_end');
		action_apply('shutdown');
		
		flush();
	}
}




if ( ! function_exists('uri_rewrite_rules'))
{
	/**
	 * uri_rewrite_rules()
	 * Obtiene las reglas de reescritura de los URIs
	 *
	 * @return	mixed
	 */
	function &uri_rewrite_rules()
	{
		static $rules = [];
		
		if (count($rules) === 0)
		{
			global $BASES_path;
			
			$BASES_path_ = array_reverse($BASES_path);

			foreach($BASES_path_ as $path)
			{
				if ($file = $path. DS. 'configs'. DS. 'uri_rewrite_rules.php' and file_exists($file))
				{
					require_once $file;
				}
				
				if ($file = $path. DS. 'configs'. DS. ENVIRONMENT. DS. 'uri_rewrite_rules.php' and file_exists($file))
				{
					require_once $file;
				}
			}

			isset($config)  and $rules = array_merge($rules, $config);	## Deprecated
			isset($rule)    and $rules = array_merge($rules, $rule);	## Deprecated
			isset($route)   and $rules = array_merge($rules, $route);	## Deprecated
			isset($routes)  and $rules = array_merge($rules, $routes);	## Deprecated
			isset($filter)  and $rules = array_merge($rules, $filter);	## Deprecated
			isset($filters) and $rules = array_merge($rules, $filters);	## Deprecated
		}
		
		return $rules;
	}
}

if ( ! function_exists('add_rewrite_rule'))
{
	/**
	 * add_rewrite_rule()
	 * Agrega una nueva regla de reescritura de url
	 *
	 * @param	string			$match
	 * @param	string|callable	$newUri
	 * @param	string|NULL		$method
	 * @param	bool			$before
	 * @return	mixed
	 */
	function add_rewrite_rule($match, $newUri, $method = NULL, $before = TRUE)
	{
		return APP()->Router->add_rewrite_rule($match, $newUri, $method, $before);
	}
}

if ( ! function_exists('add_processor'))
{
	/**
	 * add_processor()
	 * Agrega nuevos callbacks de procesamientos para las URIs
	 *
	 * @param	string			$match
	 * @param	string|NULL		$method
	 * @param	array|callable	...$callbacks
	 * @return	mixed
	 */
	function add_processor($match, $method, ...$callbacks)
	{
		array_unshift($callbacks, $method);
		array_unshift($callbacks, $match);
		
		return call_user_func_array([APP()->Router, 'add_processor'], $callbacks);
	}
}

if ( ! function_exists('add_display'))
{
	/**
	 * add_display()
	 * Agrega nuevo procesador display para las URIs
	 *
	 * @param	string			$match
	 * @param	array|callable	$method (Optional) Defecto: ALL
	 * @param	string|NULL		$method
	 * @return	mixed
	 */
	function add_display($match, $display, $method = 'ALL')
	{
		return APP()->Router->add_display($match, $display, $method);
	}
}

if ( ! function_exists('uri_processors'))
{
	/**
	 * uri_processors()
	 * Obtiene los callbacks routes
	 *
	 * @param	string	$return
	 * @return	mixed
	 */
	function &uri_processors()
	{
		static $processors = [];
		
		if (count($processors) === 0)
		{
			global $BASES_path;
			
			$BASES_path_ = array_reverse($BASES_path);

			foreach($BASES_path_ as $path)
			{
				if ($file = $path. DS. 'configs'. DS. 'uri_processors.php' and file_exists($file))
				{
					require_once $file;
				}
				
				if ($file = $path. DS. 'configs'. DS. ENVIRONMENT. DS. 'uri_processors.php' and file_exists($file))
				{
					require_once $file;
				}
			}

			isset($config)  and $processors = array_merge($processors, $config);	## Deprecated
			isset($processor) and $processors = array_merge($processors, $processor);	## Deprecated
			isset($process) and $processors = array_merge($processors, $process);	## Deprecated
			isset($route)   and $processors = array_merge($processors, $route);		## Deprecated
			isset($routes)  and $processors = array_merge($processors, $routes);	## Deprecated
			isset($filter)  and $processors = array_merge($processors, $filter);	## Deprecated
			isset($filters) and $processors = array_merge($processors, $filters);	## Deprecated
		}
		
		return $processors;
	}
}

if ( ! function_exists('uri_displays'))
{
	/**
	 * uri_displays()
	 * Obtiene los callbacks routes
	 *
	 * @param	string	$return
	 * @return	mixed
	 */
	function &uri_displays()
	{
		static $displays = [];
		
		if (count($displays) === 0)
		{
			global $BASES_path;
			
			$BASES_path_ = array_reverse($BASES_path);

			foreach($BASES_path_ as $path)
			{
				if ($file = $path. DS. 'configs'. DS. 'uri_displays.php' and file_exists($file))
				{
					require_once $file;
				}
				
				if ($file = $path. DS. 'configs'. DS. ENVIRONMENT. DS. 'uri_displays.php' and file_exists($file))
				{
					require_once $file;
				}
			}

			isset($config)  and $displays = array_merge($displays, $config);	## Deprecated
			isset($display) and $displays = array_merge($displays, $display);	## Deprecated
			isset($process) and $displays = array_merge($displays, $process);	## Deprecated
			isset($route)   and $displays = array_merge($displays, $route);		## Deprecated
			isset($routes)  and $displays = array_merge($displays, $routes);	## Deprecated
			isset($filter)  and $displays = array_merge($displays, $filter);	## Deprecated
			isset($filters) and $displays = array_merge($displays, $filters);	## Deprecated
		}
		
		return $displays;
	}
}


if ( ! function_exists('mark'))
{
	/**
	 * mark()
	 * Función que utiliza la clase BenchMark
	 *
	 * @return void
	 */
	function mark($key)
	{
		class2('BenchMark', 'class')-> mark($key);
	}
}