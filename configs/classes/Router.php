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
 * @package		JCore\Router
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
 * Router
 * Clase Principal JCore
 */

class Router
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
	
	/**
	 * $http_methods
	 * Listado de Métodos de llamadas
	 *
	 * **Lista de Métodos**
	 * * **GET**: Sirve para leer un contenido
	 * * **POST**: Sirve para crear un contenido
	 * * **PUT**: Sirve para actualizar o reemplazar un contenido
	 * * **PATCH**: Sirve para actualizar o modificar un contenido
	 * * **DELETE**: Sirve para eliminar un contenido
	 *
	 * **Lista de Códigos de Respuestas**
	 * * **200**: OK, estándar para peticiones correctas
	 * * **201**: Created, se ha creado un nuevo contenido correctamente
	 * * **202**: Accepted, se ha aceptado el request y probablemente se este procesando una acción.
	 * * **204**: No Content, se ha procesado el request correctamente pero devuelve contenido vacío
	 * * **205**: Reset Content, se ha procesado el request correctamente pero devuelve contenido vacío
	 *							y ademas, el navegador tiene que inicializar la página desde la que se realizó la petición.
	 * * **206**: Parcial Content, se ha procesado el request correctamente pero devuelve contenido en partes
	 * * **400**: Bad Request, el request tiene errores
	 * * **401**: Unauthorized, la autenticación ha fallado
	 * * **403**: Forbidden, el request es correcto pero no tiene privilegios (indistinto a si esta o no autenticado)
	 * * **404**: Not Found, el request es correcto pero no se encuentra un procesador
	 * * **405**: Method Not Allowed, El request no debería ser llamado con el http_method enviado
	 * * **409**: Conflict, hay conflicto con el estado actual del contenido
	 * * **410**: Gone, igual que 404 pero mas rapido en los buscadores 
	 *				(el contenido no esta disponible y no lo estará de nuevo)
	 *
	 * @static
	 * @global
	 */
	public static $http_methods = [//~ ENTIRE COLLECTION        ~ SPECIFIC ITEM
		'GET',   // Read           		~ 200 (OK)                 ~ 200 (OK), 404 (Not Found)
		'POST',  // Create         		~ 201 (Created)            ~ 		 , 404 (Not Found), 409 (Conflict)
		'PUT',   // Update/Replace 		~ 405 (Method Not Allowed) ~ 200 (OK), 204 (No Content), 404 (Not Found)
		'PATCH', // Update/Modify  		~ 405 (Method Not Allowed) ~ 200 (OK), 204 (No Content), 404 (Not Found)
		'DELETE' // Delete         		~ 405 (Method Not Allowed) ~ 200 (OK), 404 (Not Found)
	];

	/**
	 * Variable para almacenar las reglas para reescribir la URI
	 * @protected
	 */
	protected $_uri_rewrite_rules = [];

	/**
	 * Variable para almacenar los processors para ejecutar en una URI
	 * @protected
	 */
	protected $_uri_processors = [];

	/**
	 * Variable para almacenar los displays para ejecutar en una URI
	 * @protected
	 */
	protected $_uri_displays = [];

	/**
	 * Variable para almacenar el método HTTP usado
	 * @protected
	 */
	protected $http_verb;

	/**
	 * Variable para almacenar el URI a procesar
	 * @protected
	 */
	protected $uri_real;

	/**
	 * Variable para almacenar el URI procesado
	 * @protected
	 */
	protected $uri;

	/**
	 * Variable para almacenar la versión de la URI procesada
	 * @protected
	 */
	protected $uri_version;

	/**
	 * Variable para almacenar los números en la URI procesada
	 * @protected
	 */
	protected $uri_ids;

	/**
	 * Variable para almacenar los procesadores de la URI procesada
	 * @protected
	 */
	protected $uri_processors;

	/**
	 * Variable para almacenar los displays de la URI procesada
	 * @protected
	 */
	protected $uri_display;

	/**
	 * Variable para almacenar la URI partida
	 * @protected
	 */
	protected $uri_parsed;

	/**
	 * Variable para almacenar lel portal
	 * @protected
	 */
	protected $portal;

	//=====================================================
	// Constructor de la Clase
	//=====================================================
	protected function __construct()
	{
		## Obteniendo las reglas para reescribir la URI
		$this->_uri_rewrite_rules =& uri_rewrite_rules();

		## Obteniendo los processors para ejecutar en una URI
		$this->_uri_processors =& uri_processors();

		## Obteniendo los displays para ejecutar en una URI
		$this->_uri_displays =& uri_displays();

		## El método como es llamado el REQUEST
		$this->http_verb = url('request_method');

		## El URI ha procesar
		$this->uri = NULL;

		is_array($this->_uri_rewrite_rules) or $this->_uri_rewrite_rules = [];
		isset($this->_uri_rewrite_rules[$this->http_verb]) or $this->_uri_rewrite_rules[$this->http_verb] = [];
		isset($this->_uri_rewrite_rules['ALL']) or $this->_uri_rewrite_rules['ALL'] = [];
		
		/**
		 * Instancia de APP
		 */
		$this->APP = APP();
		
		/**
		 * Obteniendo la clase RESPONSE
		 */
		$this->Response = RSP();
	}

	public function init()
	{
		/**
		 * Variable para saber si la clase ya ha sido inicializada
		 */
		static $_inited = FALSE;
		
		if ($_inited)
		{
			return $this;
		}
		
		$_inited = TRUE;

		/**
		 * Recorrer funciones preparatorias
		 */
		foreach(['__init__', '__prepare__', 'authentication', '__loaded__'] as $kwrd)
		{
			foreach(['', $this->http_verb, '_' . $this->http_verb] as $hvrb)
			{
				if (isset($this->_uri_processors[$kwrd . $hvrb]))
				{
					exec_callable($this->_uri_processors[$kwrd . $hvrb]);
					unset($this->_uri_processors[$kwrd . $hvrb]);
				}
			}
		}
		
		$this->process_uri();
		
		return $this;
	}

	private $_was_processed = false;
	
	public function http_verb()
	{
		$this->_was_processed or $this->process_uri();
		return $this->http_verb;
	}

	public function uri()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri;
	}

	public function uri_version()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri_version;
	}

	public function uri_ids()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri_ids;
	}

	public function uri_processors()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri_processors;
	}

	public function uri_display()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri_display;
	}

	public function uri_parsed()
	{
		$this->_was_processed or $this->process_uri();
		return $this->uri_parsed;
	}

	public function portal()
	{
		$this->_was_processed or $this->process_uri();
		return $this->portal;
	}

	public function setUri($uri)
	{
		$this->_was_processed or $this->process_uri();
		$this->uri = $uri;
		return $this;
	}

	public function changeUri($uri)
	{
		$this->_was_processed or $this->process_uri();
		$this->uri_real = $uri;
		$this->process_uri();

		return $this;
	}

	public function process_uri($uri = NULL)
	{
		$this->_was_processed = true;
		
		if ( ! is_null($uri))
		{
			return (new self())
				-> setUri($uri)
				-> process_uri();
		}

		isset($this->uri_real) and $this->uri = $this->uri_real;
		is_null($this->uri) and $this->uri = url('path');

		$this->uri = '/' . trim($this->uri, '/');
		$this->uri_real = $this->uri;

		$this->uri_version = $this->obtain_version($this->uri);
		$this->uri         = $this->rewrite_uri($this->uri);

		$this->uri_ids        = $this->obtain_ids($this->uri);
		$this->uri_processors = $this->obtain_processors($this->uri);
		$this->uri_display   = $this->obtain_display($this->uri);

		$this->uri_parsed = explode('/', $this->uri);
		while(isset($this->uri_parsed[0]) and empty($this->uri_parsed[0]))
		{
			unset($this->uri_parsed[0]);
		}
		$this->uri_parsed = array_values($this->uri_parsed);

		$this->portal   = ucfirst(( ! isset($this->uri_parsed[0]) or is_empty($this->uri_parsed[0])) ? config('home_display') : $this->uri_parsed[0]);

		return $this;
	}

	public function obtain_version(&$uri)
	{
		$version = 'Last';
		$split = explode('/', $uri);
		
		isset($split[0]) and empty($split[0]) and array_shift($split);
		
		if (isset($split[0]) and is_version($split[0]))
		{
			$version = array_shift($split);
			
			array_unshift($split, '');
			$uri = implode('/', $split);
		}
		
		return $version;
	}

	public function add_rewrite_rule($match, $newUri, $method = NULL, $before = TRUE)
	{
		if ( ! is_null($method))
		{
			isset($this->_uri_rewrite_rules[$method]) or $this->_uri_rewrite_rules[$method] = [];
			
			$rules =& $this->_uri_rewrite_rules[$method];
		}
		else
		{
			$rules =& $this->_uri_rewrite_rules;
		}

		if (isset($rules[$match]))
		{
			unset($rules[$match]);
		}
		
		if ($before)
		{
			$rules = array_merge($rules, [$match => $newUri]);
		}
		else
		{
			$rules = array_merge([$match => $newUri], $rules);
		}
		return $this;
	}

	public function add_processor($match, $method, ...$callbacks)
	{
		$processors =& $this->_uri_processors;

		isset($processors[$match]) or $processors[$match] = [];
		isset($processors[$match][$method]) or $processors[$match][$method] = [];

		foreach($callbacks as $callback)
		{
			$processors[$match][$method][] = $callback;
		}
		return $this;
	}

	public function add_processor_before($match, $method, ...$callbacks)
	{
		$processors =& $this->_uri_processors;

		isset($processors[$match]) or $processors[$match] = [];
		isset($processors[$match][$method]) or $processors[$match][$method] = [];

		foreach($callbacks as $callback)
		{
			array_unshift($processors[$match][$method], $callback);
		}
		return $this;
	}

	public function add_display($match, $display, $method = 'ALL')
	{
		$displays =& $this->_uri_displays;

		isset($displays[$match]) or $displays[$match] = [];
		
		$displays[$match][$method] = $display;
		return $this;
	}

	public function rewrite_uri($uri)
	{
		$http_verb = $this->http_verb;
		$rules = $this->_uri_rewrite_rules;

		if ( ! is_array($rules))
		{
			trigger_error('Reglas de reescritura de URI no es array', E_USER_WARNING);
			return $uri;
		}

		isset($rules[$http_verb]) or $rules[$http_verb] = [];
		isset($rules['ALL']) or $rules['ALL'] = [];

		$rules_http_verb = $rules[$http_verb];
		$rules_ALL = $rules['ALL'];
		
		unset($rules[$http_verb], $rules['ALL']);
		foreach(self::$http_methods as $method)
		{
			if (isset($rules[$method]))
			{
				unset($rules[$method]);
			}
		}

		foreach([$rules_http_verb, $rules, $rules_ALL] as $_rules)
		foreach($_rules as $match => $newUri)
		{
			$match = str_replace([':any', ':num', ':id'], ['[^/]+', '[0-9]+', '[0-9]+'], $match);

			if ( ! preg_match('#^'.$match.'#', $uri, $matches))
			{
				continue;
			}

			if ( is_array($newUri))
			{
				if ( isset($newUri[$http_verb]))
				{
					$newUri = $newUri[$http_verb];
				}
				else
				{
					continue;
				}
			}
			
			if ( is_callable($newUri))
			{
				array_shift($matches);
				$matches[] = $this;
				$uri = call_user_func_array($newUri, $matches);
			}
			elseif ($newUri = (string)$newUri and 
					strpos($newUri, '$') !== FALSE and strpos($match, '(') !== FALSE)
			{
				$uri = preg_replace('#^'.$match.'#', $newUri, $uri);
			}
			else
			{
				$uri = (string)$newUri;
			}
			
			break;
		}

		$uri = filter_apply('rewrite_uri', $uri);
		
		return $uri;
	}

	public function obtain_ids($uri)
	{
		$return = [];

		$parse = explode('/', $uri);
		while(isset($parse[0]) and empty($parse[0]))
		{
			array_shift($parse); ## espacio en blanco
		}

		foreach($parse as $_part)
		{
			if (is_numeric($_part))
			{
				$return[] = $_part;
			}
		}

		return $return;
	}

	public function obtain_processors($uri)
	{
		$return = [];
		$http_verb = $this->http_verb;
		$processors = $this->_uri_processors;

		if ( ! is_array($processors))
		{
			trigger_error('Reglas de reescritura de URI no es array', E_USER_WARNING);
			return $return;
		}

		foreach($processors as $match => $_processors)
		{
			$match = str_replace([':any', ':num', ':id'], ['[^/]+', '[0-9]+', '[0-9]+'], $match);

			if ( ! preg_match('#^'.$match.'#', $uri, $matches))
			{
				continue;
			}

			is_array($_processors) or $_processors = [$_processors];

			$proc_http_verb = [];
			$proc_ALL = [];
			
			isset($_processors[$http_verb]) and $proc_http_verb = $_processors[$http_verb];
			isset($_processors['ALL']) and $proc_ALL = $_processors['ALL'];
			
			unset($_processors[$http_verb], $_processors['ALL']);
			foreach(self::$http_methods as $method)
			{
				if (isset($_processors[$method]))
				{
					unset($_processors[$method]);
				}
			}
			
			$return = array_merge($proc_http_verb, $_processors, $proc_ALL);
			break;
		}

		if (count($return) === 0)
		{
			## Intentar buscar la clase del uri
			$parse = explode('/', $uri);

			while(isset($parse[0]) and empty($parse[0]))
			{
				array_shift($parse); ## espacio en blanco
			}

			$parse_temp = [];
			foreach($parse as $_part)
			{
				if ( ! is_numeric($_part))
				{
					$parse_temp[] = $_part;
				}
			}
			$parse = $parse_temp;
			
			isset($parse[0]) or $parse[0] = config('home_display');
			$parse[] = config('default_method');
			$parse[] = '__DEFAULT CLASS PROCESSOR__';
			$return[] = $parse;
		}

		return $return;
	}

	public function obtain_display($uri)
	{
		$return = NULL;
		$http_verb = $this->http_verb;
		$displays = $this->_uri_displays;

		if ( ! is_array($displays))
		{
			trigger_error('Reglas de reescritura de URI no es array', E_USER_WARNING);
			return $return;
		}

		foreach($displays as $match => $_display)
		{
			$match = str_replace([':any', ':num', ':id'], ['[^/]+', '[0-9]+', '[0-9]+'], $match);

			if ( ! preg_match('#^'.$match.'#', $uri, $matches))
			{
				continue;
			}

			is_array($_display) or $_display = ['ALL' => $_display];

			$disp_http_verb = NULL;
			$disp_opt = NULL;
			$disp_ALL = NULL;
			
			isset($_display[$http_verb]) and $disp_http_verb = $_display[$http_verb];
			isset($_display['ALL']) and $disp_ALL = $_display['ALL'];
			
			unset($_display[$http_verb], $_display['ALL']);
			foreach(self::$http_methods as $method)
			{
				if (isset($_display[$method]))
				{
					unset($_display[$method]);
				}
			}
			
			if (count($_display) > 0)
			{
				$disp_opt = $disp_opt;
				count($disp_opt) === 1 and is_array($disp_opt[0]) and $disp_opt = $disp_opt[0];
			}
			
			if ( ! is_null($disp_http_verb))
			{
				$return = $disp_http_verb;
			}
			elseif ( ! is_null($disp_opt))
			{
				$return = $disp_opt;
			}
			elseif ( ! is_null($disp_ALL))
			{
				$return = $disp_ALL;
			}

			break;
		}

		if (is_null($return))
		{
			## Intentar buscar la clase del uri
			$parse = explode('/', $uri);

			while(isset($parse[0]) and empty($parse[0]))
			{
				array_shift($parse); ## espacio en blanco
			}

			$parse_temp = [];
			foreach($parse as $_part)
			{
				if ( ! is_numeric($_part))
				{
					$parse_temp[] = $_part;
				}
			}
			$parse = $parse_temp;

			isset($parse[0]) or $parse[0] = config('home_display');
			$parse[] = config('default_method');
			$parse[] = '__DEFAULT CLASS PROCESSOR__';
			$return = $parse;
		}

		return $return;
	}

	/**
	 * Retorna el nombre y la versión de la clase
	 * @return string
	 */
	public function __toString()
	{
		return get_class() . ' v' . self::version . ' by JYS Perú';
	}
	
	/**
	 * Permite retornar datos de desarrollador
	 * @return Array
	 */
	public function __debugInfo()
	{
		return [
			'_' => $this->__toString(),
			'http_verb'      => $this->http_verb,
			'uri_real'       => $this->uri_real,
			'uri'            => $this->uri,
			'uri_version'    => $this->uri_version,
			'uri_ids'        => $this->uri_ids,
			'uri_processors' => $this->uri_processors,
			'uri_display'    => $this->uri_display,
			'uri_parsed'     => $this->uri_parsed,
			'portal'         => $this->portal,
		];
	}
	
}