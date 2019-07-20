<?php
/**
 * @handlers.php
 * Funciones para eventos de codigo
 *
 * @filesource
 */

/**
 * DIRECTORY_SEPARATOR
 *
 * Separador de Directorios para el sistema operativo de ejecución
 *
 * @global
 */
defined('DS') or 
	define('DS', DIRECTORY_SEPARATOR);

/**
 * ENVIRONMENT - AMBIENTE DE DESARROLLO
 *
 * Permite manejar distintas configuraciones dependientemente de 
 * la etapa o fase en la que se encuentre la aplicación (proyecto)
 *
 * **Posibles valores:**
 * *	desarrollo
 * *	pruebas
 * *	produccion
 *
 * @global
 */
defined('ENVIRONMENT') or 
	define('ENVIRONMENT', 'pruebas');

/**
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or 
	$BASES_path = [];

if ( ! function_exists('_error_handler'))
{
	/**
	 * _error_handler()
	 * Función a ejecutar al producirse un error durante la aplicación
	 *
	 * @use logger
	 * @use is_cli
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param int
	 *
	 * @return	void
	 */
	function _error_handler($severity, $message, $filepath, $line)
	{
		// Se valida si es error o solo una alerta
		$_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

		if ($_error and ! is_cli())
		{
			// Ya que es un error, se retorna un status 500 Internal Server Error
			http_response_code(500);
		}

		if (($severity & error_reporting()) !== $severity)
		{
			// No se desea reportar
			return;
		}

		// Se envía los datos a una función especial llamada logger definida por el usuario
		function_exists('logger') and
		logger($message, 
			   $severity, 
			   $severity, 
			   [], 
			   $filepath, 
			   $line);

		if ($_error)
		{
			// Ya que es un error, finaliza el proceso
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
	 * @use logger
	 * @use is_cli
	 *
	 * @param	Exception	$exception
	 *
	 * @return	void
	 */
	function _exception_handler($exception)
	{
		// Ya que es una exception, se retorna un status 500 Internal Server Error
		if ( ! is_cli())
		{
			http_response_code(500);
		}
		
		// Se envía los datos a una función especial llamada logger definida por el usuario
		function_exists('logger') and
		logger($exception);

		// Ya que es una exception, finaliza el proceso
		exit(1);
	}
}

if ( ! function_exists('_shutdown_handler'))
{
	/**
	 * _shutdown_handler()
	 * Función a ejecutar antes de finalizar el procesamiento de la aplicación
	 *
	 * @use _error_handler
	 * @use action_apply
	 *
	 * @return void
	 */
	function _shutdown_handler()
	{
		// Validar si se produjo la finalización por un error
		$last_error = error_get_last();
		
		if ( isset($last_error) &&
			($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
		{
			_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
		
		if (function_exists('action_apply'))
		{
			// Ejecutando funciones programadas
			action_apply ('do_when_end');
			action_apply ('shutdown');
		}
		
		flush();
	}
}

if ( ! function_exists('_autoload'))
{
	/**
	 * _autoload()
	 * Función a ejecutar para leer una clase que aún no ha sido declarada
	 * 
	 * Las clases con namespace 	"Request" 		son buscados dentro de la carpeta 		"/request"
	 * Las clases con namespace 	"Response" 		son buscados dentro de la carpeta 		"/response"
	 * Las clases con namespace 	"Object" 		son buscados dentro de la carpeta 		"/objects"
	 *
	 * Las clases con namespace "Response" y sufijo "Structure" son buscados dentro de la carpeta 		"/response/structure"
	 *  	\Response\BasicStructure
	 *  	\Response\Structure\Basic
	 *
	 * Se busca en las carpetas configs/classes.
	 *
	 * Las clases con sufijo 		"Exception" 	también son buscados dentro de la carpeta 		"/configs/classes/exceptions"
	 * Las clases con sufijo 		"Object" 		también son buscados dentro de la carpeta 		"/objects"
	 *
	 * Las clase "Object" también es buscado dentro de la carpeta 		"/objects"
	 *
	 * @param string $main_class
	 * @return void
	 */
	function _autoload($main_class)
	{
		static $bcs = '\\';
		/**
		 * $class_structure
		 * Convirtiendo la clase como array
		 */
		$class_structure = explode($bcs, $main_class);
		
		/**
		 * $start_ws
		 * Identificar si han llamado a la clase como \ (backslash)
		 */
		$start_ws = FALSE;
		
		empty($class_structure[0]) and 
		$start_ws = TRUE and 
		array_shift($class_structure);
		
		/**
		 * $main_dir
		 * 
		 */
		$main_dir = '';
		
		/**
		 * $alter_dir
		 * 
		 */
		$alter_dir = '';
		
		/**
		 * $alter_class
		 * 
		 */
		$alter_class = '';
		
		if (count($class_structure) > 1)
		{
			$_namespace = array_shift($class_structure);
			
			$_class = array_shift($class_structure);
			
			switch($_namespace)
			{
				case 'Request':case 'Response':
					$main_dir = DS . mb_strtolower($_namespace);
					break;
				case 'Object':
					$main_dir = DS . mb_strtolower($_namespace) . 's';
					break;
			}
			
			if ($_namespace === 'Response' and preg_match('/(.+)Structure$/', $_class))
			{
				$main_dir.= DS . 'structure';
				$alter_class = $bcs . 'Response' . $bcs . 'Structure' . $bcs . preg_replace('/Structure$/', '', $_class);
				
				count($class_structure) > 0 and
				$alter_class .= $bcs . implode($bcs, $class_structure);
			}
			
			array_unshift($class_structure, $_class);
			array_unshift($class_structure, $_namespace);
		}
		
		empty($main_dir) and
		$main_dir = DS . 'configs' . DS . 'classes';
		
		$_class = array_shift($class_structure);
		
		if (preg_match('/(.+)Exception/', $_class))
		{
			$alter_class = DS . 'configs' . DS . 'classes' . DS . 'exceptions';
		}
		
		if (preg_match('/(.+)Object/', $_class) or $_class === 'Object')
		{
			$alter_class = DS . 'objects';
		}
		
		array_unshift($class_structure, $_class);
		
		global $BASES_path;
		
		$main_class_file = strtr($main_class, $bcs, DS) . '.php';
		
		$alter_class_file = '';
		
		! empty($alter_class) and
		$alter_class_file = strtr($alter_class, $bcs, DS) . '.php' and
		$alter_class = $main_class;
		
		foreach($BASES_path as $_path)
		{
			if ($_file = $_path . $main_dir . DS . ENVIRONMENT . DS . $main_class_file and file_exists($_file))
			{
				if (class_exists($main_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_dir) and $_file = $_path . $alter_dir . DS . ENVIRONMENT . DS . $main_class_file and file_exists($_file))
			{
				if (class_exists($main_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_class_file) and $_file = $_path . $main_dir . DS . ENVIRONMENT . DS . $alter_class_file and file_exists($_file))
			{
				if (class_exists($alter_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_class_file) and  ! empty($alter_dir) and $_file = $_path . $alter_dir . DS . ENVIRONMENT . DS . $alter_class_file and file_exists($_file))
			{
				if (class_exists($alter_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ($_file = $_path . $main_dir . DS . $main_class_file and file_exists($_file))
			{
				if (class_exists($main_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_dir) and $_file = $_path . $alter_dir . DS . $main_class_file and file_exists($_file))
			{
				if (class_exists($main_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_class_file) and $_file = $_path . $main_dir . DS . $alter_class_file and file_exists($_file))
			{
				if (class_exists($alter_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
			
			if ( ! empty($alter_class_file) and  ! empty($alter_dir) and $_file = $_path . $alter_dir . DS . $alter_class_file and file_exists($_file))
			{
				if (class_exists($alter_class, FALSE) === FALSE and class_exists($alter_class, FALSE) === FALSE)
				{
					require_once $_file;
				}
			}
		}
		
		class_exists($main_class, FALSE) === FALSE and class_exists($alter_class , FALSE) === TRUE and class_alias($alter_class , $main_class);
	}
}
