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
 * @version		1.0.2
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
 * APP
 * Clase Principal JCore
 */
class APP implements ArrayAccess
{
	/**
	 * Versión de la Clase
	 * @constant
	 * @global
	 */
	const version = '1.0.2';
	
	//===================================================================
	// Statics
	//===================================================================

	/**
	 * Función para llamar la instancia de la aplicación
	 * @static
	 * @return APP La instancia de la Aplicación
	 */
	public static function &instance()
	{
		static $instance;
		
		isset($instance) or $instance = new self();

		return $instance;
	}
	
	//===================================================================
	// Variables
	//===================================================================
	
	/**
	 * Variable para almacenar todas las variables usables por la clase
	 * @protected
	 */
	protected $variables = [];

	//=====================================================
	// Constructor de la Clase
	//=====================================================
	public function init()
	{
		/**
		 * Obteniendo la codificación de caracteres
		 */
		$this->variables['charset'] =& config('charset');
		$this->_charset_updated();

		/**
		 * UTF8_ENABLED
		 *
		 * Variable que permite conocer si la codificación UTF8 está habilitado
		 *
		 * @global
		 */
		define('UTF8_ENABLED', defined('PREG_BAD_UTF8_ERROR') && APP()->charset === 'UTF-8');
		
		/**
		 * Obteniendo la zona horaria
		 */
		$this->variables['timezone'] =& config('timezone');
		$this->_timezone_updated();

		/**
		 * Conectar Primera Base Datos
		 */
		sql_start();

		/**
		 * Obteniendo la clase ROUTER
		 */
		$this->Router = RTR();
		$this->Router->APP = $this;

		/**
		 * Obteniendo la clase RESPONSE
		 */
		$this->Response = RSP();
		$this->Response->APP = $this;
		
		$this->Router->Response = $this->Response;
		$this->Response->Router = $this->Router;

		/**
		 * Identificando los Métodos de Request autorizados
		 */
		$allowed_http_methods = (array)config('allowed_http_methods');
		
		$allowed_http_methods = array_map('mb_strtoupper', $allowed_http_methods); ## Convirtiendo todos a mayúsculas

		Router::$http_methods = array_merge(Router::$http_methods, $allowed_http_methods); ## Agregando posibles faltantes
		Router::$http_methods = array_unique(Router::$http_methods); ## Eliminando duplicados

		/**
		 * Validando que se haya llamado con un REQUEST_METHOD autorizado
		 */
		in_array(url('request_method'), $allowed_http_methods) or
		RSP()
			-> error('HTTP Method `' . $method . '` not allowed')
			-> http_code(405, 'HTTP Method `' . $method . '` not allowed')
			-> exit()
		;
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
		
		global $CONs;
		
		if (is_empty($CONs))
		{
			return;
		}
		
		foreach($CONs as $conection)
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
		unset($vars['error_levels'], $vars['variables']);
		$vars = array_values($vars);
		
		$vars = array_merge($vars, array_keys($this->variables));
		
		$methods = array_combine($methods, $methods);
		unset($methods['instance'], $methods['init'], $methods['run']);
		unset($methods['__construct'], $methods['__toString'], $methods['__debugInfo'], $methods['__call']);
		unset($methods['__isset'], $methods['__unset'], $methods['__set'], $methods['__get']);
		unset($methods['_charset_updated'], $methods['_timezone_updated']);
		unset($methods['offsetExists'], $methods['offsetGet'], $methods['offsetSet'], $methods['offsetUnset']);
		$methods = array_values($methods);
		
		return [
			'_' => $this->__toString(),
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

