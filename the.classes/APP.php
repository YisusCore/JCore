<?php
/**
 * JCore.php
 * 
 * El núcleo inicializa todas las funciones básicas y todas las configuraciones mínimas.
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
 * @package		JCore\APP
 * @author		YisusCore
 * @link		https://jcore.jys.pe/classes
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

/**
 * JCore
 * Clase Principal JCore
 */
class APP implements ArrayAccess
{
	/**
	 * Versión de la Clase
	 * @constant
	 * @global
	 */
	const version = '1.0';
	
	//===================================================================
	// Statics
	//===================================================================

	/**
	 * Instancia de Clase
	 * @static
	 * @global
	 */
	private static $instance;

	/**
	 * Función para llamar la instancia de la aplicación
	 * @static
	 * @return APP La instancia de la Aplicación
	 */
	public static function &instance()
	{
		isset(self::$instance) or self::$instance = new self();

		return self::$instance;
	}
	
	/**
	 * Listado de Levels de Errores
	 * @static
	 * @global
	 */
	protected static $error_levels = [
		E_ERROR			=>	'Error',				
		E_WARNING		=>	'Warning',				
		E_PARSE			=>	'Parsing Error',		
		E_NOTICE		=>	'Notice',				
		
		E_CORE_ERROR		=>	'Core Error',		
		E_CORE_WARNING		=>	'Core Warning',		

		E_COMPILE_ERROR		=>	'Compile Error',	
		E_COMPILE_WARNING	=>	'Compile Warning',	
		
		E_USER_ERROR		=>	'User Error',		
		E_USER_DEPRECATED	=>	'User Deprecated',	
		E_USER_WARNING		=>	'User Warning',		
		E_USER_NOTICE		=>	'User Notice',		
		
		E_STRICT		=>	'Runtime Notice'		
	];

	/**
	 * Listado de Clases a leer
	 * @static
	 * @global
	 */
	protected static $clases = [
		'BenchMark', // Encargado de los puntos de procesos
		'Response',  // Encargado de manejar la respuesta del navegador
		'Router'     // Encargado de analizar los processors y displays para el response
	];
	
	/**
	 * Listado de Funciones a leer
	 * @static
	 * @global
	 */
	protected static $funciones = [
		'mark', 
		'template', 
		'redirect', 
		'ip_address',
		'add_rewrite_rule',
		'add_processor',
		'add_display'
	];
	
	//===================================================================
	// Variables
	//===================================================================
	
	/**
	 * Variable para almacenar la configuración
	 * @global
	 */
	public $config = [];
	
	/**
	 * Variable para almacenar los datos de la URL
	 * @global
	 */
	public $url = [];
	
	/**
	 * Variable para almacenar la IP del usuario
	 * @global
	 */
	public $ip_address;
	
	/**
	 * Variable para almacenar la primera Conección a la Base Datos
	 * @global
	 */
	public $CON;

	/**
	 * Variable para almacenar todas las conecciones de la Base Datos
	 * @global
	 */
	public $CONs;

	/**
	 * Variable para almacenar todas las funciones realizables por la clase
	 * @protected
	 */
	protected $functions = [];

	/**
	 * Variable para almacenar todas las variables usables por la clase
	 * @protected
	 */
	protected $variables = [];

	//===================================================================
	// Constructor
	//===================================================================
	protected function __construct()
	{}

	/**
	 * Inicializador de la clase
	 * @global
	 */
	public function init()
	{
		/**
		 * Variable para saber si la clase ya ha sido inicializada
		 */
		static $_inited = FALSE;
		
		if ($_inited)
		{
			return;
		}
		
		$_inited = TRUE;

		/**
		 * LLamando a las variables Globales
		 */
		global $CON, $CONs;

		$this->CON =& $CON;
		$this->CONs =& $CONs;
		
		/**
		 * Obteniendo las configuraciones
		 */
		$this->config =& config('array');
		$config =& $this->config;

		/**
		 * Obteniendo la codificación de caracteres
		 */
		$this->variables['charset'] =& config('charset', ['charset' => 'UTF-8']);
		$this->_charset_updated();

		/**
		 * Obteniendo la zona horaria
		 */
		$this->variables['timezone'] =& config('timezone', ['timezone'=>'America/Lima']);
		$this->_timezone_updated();

		/**
		 * Obteniendo el IP del usuario
		 */
		$this->ip_address =& ip_address();

		/**
		 * Obteniendo los datos de la URL
		 */
		$this->url =& url('array');

		/**
		 * Leyendo las clases por defecto
		 */
		foreach(self::$clases as $class)
		{
			$this->variables[$class] = class2($class, 'class');
		}
		
		/**
		 * Leyendo las funciones por defecto
		 */
		foreach(self::$funciones as $function)
		{
			$this->functions[$function] = function () use ($function)
			{
				if (func_num_args() === 0)
				{
					return call_user_func($function);
				}

				return call_user_func_array($function, func_get_args());
			};
		}

		/**
		 * Identificando los Métodos de Request autorizados
		 */
		( ! isset($config['allowed_http_methods']) or is_empty($config['allowed_http_methods'])) and $config['allowed_http_methods'] = ['GET', 'POST'];
		is_array($config['allowed_http_methods']) or $config['allowed_http_methods'] = [$config['allowed_http_methods']];

		$config['allowed_http_methods'] = array_map('mb_strtoupper', $config['allowed_http_methods']); ## Convirtiendo todos a mayúsculas

		Router::$http_methods = array_merge(Router::$http_methods, $config['allowed_http_methods']); ## Agregando posibles faltantes
		Router::$http_methods = array_unique(Router::$http_methods); ## Eliminando duplicados

		/**
		 * Leyendo las funciones de REQUEST
		 */
		foreach(Router::$http_methods as $method)
		{
			$method_lower = mb_strtolower($method);

			$this->functions[$method_lower] = function($route, ...$callback) use ($method)
			{
				if ( ! in_array($method, $this->config['allowed_http_methods']))
				{
					user_error('HTTP Method `' . $method . '` not allowed');
					exit();
				}

				$_params = $callback;
				array_unshift($_params, $method);
				array_unshift($_params, $route);

				return call_user_func_array([$this->Router, 'add_processor'], $_params);
			};
		}
		
		if ( ! in_array($this->url['request_method'], $this->config['allowed_http_methods']))
		{
			throw new Exception('El método utilizado no está autorizado');
		}
	}
	
	//===================================================================
	// Funciones
	//===================================================================
	
	/**
	 * log()
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
	public function log($message, $code = NULL, $severity = NULL, $meta = NULL, $filepath = NULL, $line = NULL, $trace = NULL, $show = TRUE)
	{
		isset($this->config['log']) OR $this->config['log'] = [];
		
		$config =& $this->config['log'];
		
		is_bool($code) and $show = $code and $code = NULL;
		
		(is_array($severity) and is_null($meta)) and $meta = $severity and $severity = NULL;
		
		is_null($code) and $code = 0;
		
		is_null($meta) and $meta = [];
		is_array($meta) or $meta = (array)$meta;
		
		$meta['time'] = time();
		$meta['microtime'] = microtime();
		$meta['microtime_float'] = microtime(true);
		
		$meta['buffer'] = OutputBuffering::instance()->stop()->getContents();
		
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
		
		$severity = isset(self::$error_levels[$severity]) ? self::$error_levels[$severity] : $severity;
		
		is_null($message) and $message = '[NULL]';
		
		if ( ! is_cli() and ! is_null($filepath))
		{
			$filepath = str_replace('\\', '/', $filepath);
			if (FALSE !== strpos($filepath, '/'))
			{
				$x = explode('/', $filepath);
				$filepath = $x[count($x)-2].'/'.end($x);
			}
		}
		
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
		
		$meta['server'] = $_SERVER;
		$meta['disp'] = defined('disp') ? disp : NULL;
		$meta['stat'] = isset($_SESSION['stat']) ? $_SESSION['stat'] : NULL;
		$meta['url'] = $this->url;
		$meta['ip_address'] = $this->ip_address;
		
		$saved = FALSE;
		
		$saved = filter_apply('save_logs', $saved, $message, $severity, $code, $filepath, $line, $trace, $meta);
		
		// Guardar Log en BBDD
		// guarda los logs en la bbdd, consume menos recursos que almacenarlo en un archivo físico
		// PRIORIDAD I
		if ( ! $saved)
		{
			// Hay alguna conección a BBDD, entonces intenta guardar en la tabla JC_logs
		}
		
		// Guardar Log en Archivo
		// almacena los logs en un archivo, solo agrega lineas
		// PRIORIDAD II
		if ( ! $saved)
		{
			if ( ! isset($config['path']) or is_null($config['path']))
			{
				// Directorio donde se encontrará los archivos LOGs
				$config['path'] = APPPATH . DS . 'logs';
			}

			mkdir2($config['path']);

			if ( ! isset($config['file_ext']) or is_null($config['file_ext']))
			{
				//Extensión del archivo LOG
				$config['file_ext'] = 'log';
			}

			if ( ! isset($config['file_permissions']) or is_null($config['file_permissions']))
			{
				//Permisos del archivo LOG
				$config['file_permissions'] = 0644;
			}

			if ( ! isset($config['format_line']) or is_null($config['format_line']))
			{
				//Función a ejecutar para formatear la linea a guardar
				$config['format_line'] = function ($message, $severity, $code, $filepath, $line, $trace, $meta)
				{
					return $severity . ' - ' . $meta['microtime'] . ' --> ' . $message . "\n";
				};
			}

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
		
		## Mostrar Log en Web
		if (display_errors() and $show)
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
	
	public function run()
	{
		$Router = $this->Router;

		$http_verb 	= $Router->http_verb();
		$version 	= $Router->uri_version();
		$uri	    = $Router->uri();
		$ids 		= $Router->uri_ids();
		$uri_parsed = $Router->uri_parsed();

		$class_prms = $ids;
		$params = $uri_parsed;

		$processors = $Router->uri_processors();
		
		action_apply('dobefore_apprun_processor');
		
		foreach($processors as $processor)
		{
			if (is_array($processor) and count($processor) >= 3 and end($processor) === '__DEFAULT CLASS PROCESSOR__')
			{
				try
				{
					exec_callable($processor, $params, $class_prms, 'processors', $version);
				}
				catch (BasicException $e){

					## Como es por defecto, no debe generar error si no encuentra la clase por defecto
					if ( ! preg_match('/La función no se puede ejecutar/i', $e->getMessage()))
					{
						throw $e;
					}
				}

				continue;
			}

			exec_callable($processor, $params, $class_prms, 'processors', $version);
		}
		
		$display    = $Router->uri_display();
		action_apply('dobefore_apprun_display');
		
		if (is_array($display) and count($display) >= 3 and end($display) === '__DEFAULT CLASS PROCESSOR__')
		{
			try
			{
				exec_callable($display, $params, $class_prms, 'displays', $version);
				action_apply('doafter_apprun');
				return TRUE; ## Finalizado
			}
			catch (BasicException $e){
				## Como es por defecto, no debe generar error si no encuentra la clase por defecto
				if ( ! preg_match('/La función no se puede ejecutar/i', $e->getMessage()))
				{
					throw $e;
				}
			}

			## El display por defecto no ha podido procesarse así que toma el display del error 404
			$display = [config('error404_display'), config('default_method')];
		}
		
		exec_callable($display, $params, $class_prms, 'displays', $version);
		action_apply('doafter_apprun');
		return TRUE; ## Finalizado
	}
	
	//=====================================================
	// Funciones Protegidas
	//=====================================================
	
	/**
	 * Ejecutado cuando se actualiza la variable charset
	 * @return void
	 */
	protected function _charset_updated()
	{
		$charset =& $this->variables['charset'];
		
		## Convirtiendolo a mayúsculas
		$charset = mb_strtoupper($charset);
		
		## Estableciendo los charsets a todo lo que corresponde
		ini_set('default_charset', $charset);
		ini_set('php.internal_encoding', $charset);
		
		@ini_set('mbstring.internal_encoding', $charset);
		mb_substitute_character('none');
		
		@ini_set('iconv.internal_encoding', $charset);
	}
	
	/**
	 * Ejecutado cuando se actualiza la variable timezone
	 * @return void
	 */
	protected function _timezone_updated()
	{
		$timezone =& $this->variables['timezone'];
		
		## Estableciendo los charsets a todo lo que corresponde
		date_default_timezone_set($timezone);
		
		if (is_empty($this->CONs))
		{
			return;
		}
		
		foreach($this->CONs as $conection)
		{
			@mysqli_query($conection, 'SET time_zone = ' . qp_esc(getUTC()));
		}
	}
	
	//===================================================================
	// Magic Functions
	//===================================================================
	
	/**
	 * Retorna el nombre y la versión de la clase
	 * @return string
	 */
	public function __toString()
	{
		return get_class() . ' v' . self::version . ' by JYS Perú';
	}
	
	/**
	 * Permite retornar la data de mimes para su validación
	 * @return Array
	 */
	public function __debugInfo()
	{
		$class   = get_class();
		$vars    = array_keys(get_class_vars($class));
		$methods = array_values(get_class_methods($class));
		
		$vars = array_combine($vars, $vars);
		unset($vars['instance']);
		unset($vars['error_levels'], $vars['http_methods'], $vars['functions'], $vars['variables']);
		$vars = array_values($vars);
		
		$vars = array_merge($vars, array_keys($this->variables));
		
		$methods = array_combine($methods, $methods);
		unset($methods['instance']);
		unset($methods['__construct'], $methods['__toString'], $methods['__debugInfo'], $methods['__call']);
		unset($methods['offsetExists'], $methods['offsetGet'], $methods['offsetSet'], $methods['offsetUnset']);
		$methods = array_values($methods);
		
		$methods = array_merge($methods, array_keys($this->functions));
		
		return [
			'class' => $class,
			'variables' => $vars,
			'funciones' => $methods
		];
	}
	
	/**
	 * Permite validar si la data de mimes cuenta con la extensión requerida
	 * @param string
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}
	
	/**
	 * Elimina una variable
	 * @see $variables
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->variables[$name]);
	}
	
	/**
	 * Establece una variable
	 * @see $variables
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->variables[$name] = $value;
		
		if ($function = [$this, '_' . $name . '_updated']  and is_callable($function))
		{
			@call_user_func($function, __FUNCTION__);
		}
	}
	
	/**
	 * El usuario puede obtener la información de una extensión
	 * considerando a la extensión como una variable pública de la clase
	 * @param string
	 * @return Mixed
	 */
	public function &__get($name)
    {
		if ( ! isset($this->variables[$name]))
		{
			$this->variables[$name] = NULL;
		}
		
		return $this->variables[$name];
    }
	
	public function __call($name, $arguments)
    {
		if ( ! isset($this->functions[$name]))
		{
			trigger_error('Function ´' . $name . '´ not loaded', E_USER_WARNING);
		}
		
		return call_user_func_array($this->functions[$name], $arguments);
    }
	
	
	//===================================================================
	// Array Access
	//===================================================================
	
	/**
	 * Valida que la extensión exista en la data
	 *
	 * @param string
	 * @return bool
	 */
	public function offsetExists ($offset)
	{
		return isset($this->variables[$offset]);
	}
	
	/**
	 * Obtiene la información de la extensión
	 *
	 * @param string
	 * @return Mixed
	 */
	public function offsetGet ($offset)
	{
		return $this->variables[$offset];
	}
	
	/**
	 * Inserta o actualiza la data de un mime
	 *
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function offsetSet ($offset, $value)
	{
		$this->variables[$offset] = $value;
	}
	
	/**
	 * Elimina la información de la extensión
	 *
	 * @param string
	 * @return void
	 */
	public function offsetUnset ($offset)
	{
		unset ($this->variables[$offset]);
	}
}

