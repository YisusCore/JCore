<?php
/**
 * _basic.php
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
 * @package		JCore\Functions
 * @author		YisusCore
 * @link		https://jcore.jys.pe/functions/_basic
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

if ( ! function_exists('define2'))
{
	/**
	 * define2()
	 * Define la variable en caso de que aún no se haya definido la variable, 
	 * esto para que no se produzca error
	 *
	 * @param string $name Nombre de variable a definir
	 * @param mixed $value Valor de la variable
	 * @param bool $case_insensitive La variable tendrá el nombre insensible a mayúsculas y minúsculas
	 * @return void
	 */
	function define2(string $name, $value, bool $case_insensitive = false)
	{
		is_string($name) or $name = (string)$name;
		
		defined($name) or define($name, $value, $case_insensitive);
	}
}

if ( ! function_exists('is_php'))
{
	/**
	 * is_php()
	 * Determina si la versión de PHP es igual o mayor que el parametro
	 *
	 * @param string $version Versión a validar
	 * @return bool TRUE si la versión actual es $version o mayor
	 */
	function is_php(string $version)
	{
		static $_is_php = [];

		isset($_is_php[$version]) or $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');

		return $_is_php[$version];
	}
}

if ( ! function_exists('is_cli'))
{
	/**
	 * is_cli()
	 * Identifica si el REQUEST ha sido hecho desde comando de linea
	 *
	 * @return bool
	 */
	function is_cli()
	{
		return (PHP_SAPI === 'cli' OR defined('STDIN'));
	}
}

if ( ! function_exists('is_localhost'))
{
	/**
	 * is_localhost()
	 * Identificar si la aplicación está corriendo en modo local
	 *
	 * Se puede cambiar el valor durante la ejecución
	 *
	 * @since 1.1 Se habilitó el cambio del valor mediante ejecución enviando un parametro $set
	 * @since 1.0
	 *
	 * @param bool|NULL $set Si es Bool entonces asigna al valor mediante ejecución
	 * @return bool
	 */
	function &is_localhost(bool $set = NULL)
	{
		static $is_localhost = []; ## No puede ser referenciado si es BOOL
		
		if (count($is_localhost) === 0)
		{
			$is_localhost[0] = (bool)preg_match('/^(192\.168\.[0-9]{1,3}\.[0-9]{1,3}|127\.[0]{1,3}\.[0]{1,3}\.[0]{0,2}1)$/', $_SERVER['SERVER_ADDR']);
		}
		
		is_bool($set) and $is_localhost[0] = $set;
		
		return $is_localhost[0];
	}
}

if ( ! function_exists('regex'))
{
	/**
	 * regex()
	 * Permite convertir un string para ser analizado como REGEXP
	 *
	 * @param string $str String a convertir en REGEXable
	 * @return string
	 */
	function regex ($str)
	{
		/** Caractéres que son usables */
		static $chars = ['/','.','*','+','?','|','(',')','[',']','{','}','\\','$','^','-'];
		
		$_regex = '/(\\' . implode('|\\', $chars).')/';
		
		return preg_replace($_regex, "\\\\$1", $str);
	}
}

if ( ! function_exists('protect_server_dirs'))
{
	/**
	 * protect_server_dirs()
	 * Proteje los directorios base y los reemplaza por vacío o un parametro indicado
	 *
	 * @since 1.1 Se cambio la carga de directorios en la variable $_dirs a los de la variable $BASES_path
	 * @since 1.0
	 *
	 * @param string $str Contenido que probablemente contiene rutas a proteger
	 * @return string
	 */
	function protect_server_dirs(string $str)
	{
		static $_dirs = [];

		global $BASES_path;

		$add_basespath = count($_dirs) === 0;
		
		defined('ROOTPATH') and ! isset($_dirs[ROOTPATH]) and $_dirs[ROOTPATH] = DS . 'ROOTPATH';
		defined('APPPATH')  and ! isset($_dirs[APPPATH])  and $_dirs[APPPATH]  = DS . 'APPPATH';
		defined('ABSPATH')  and ! isset($_dirs[ABSPATH])  and $_dirs[ABSPATH]  = DS . 'ABSPATH';
		defined('HOMEPATH') and ! isset($_dirs[HOMEPATH]) and $_dirs[HOMEPATH] = DS . 'HOMEPATH';

		$add_basespath and $_dirs = array_merge($_dirs, array_combine($BASES_path, array_map(function($path){
			return DS . basename($path);
		}, $BASES_path)));

		return strtr($str, $_dirs);
	}
}

if ( ! function_exists('mkdir2'))
{
	/**
	 * mkdir2()
	 * Crea los directorios faltantes desde la carpeta $base
	 *
	 * @param	string 	$folder folder
	 * @param	string 	$base	base a considerar
	 * @return 	string 	ruta del folder creado
	 */
	function mkdir2($folder, $base = NULL)
	{
		static $_dirs = [];

		global $BASES_path;

		$add_basespath = count($_dirs) === 0;
		
		defined('ABSPATH')  and ! in_array(ABSPATH, $_dirs)  and $_dirs[] = ABSPATH;
		defined('HOMEPATH') and ! in_array(HOMEPATH, $_dirs) and $_dirs[] = HOMEPATH;
		defined('APPPATH')  and ! in_array(APPPATH, $_dirs)  and $_dirs[] = APPPATH;
		defined('ROOTPATH') and ! in_array(ROOTPATH, $_dirs) and $_dirs[] = ROOTPATH;

		$add_basespath and $_dirs = array_merge($_dirs, $BASES_path);

		if (is_null($base))
		{
			$base = DS;
			
			foreach($_dirs as $base_dir)
			{
				if ($temp = str_replace($base_dir, '', $folder) and $temp <> $folder)
				{
					$base = $base_dir;
					break;
				}
			}
		}
		
		$folder = preg_replace('/^' . regex($base) . '/i', '', $folder);
		$folder = strtr($folder, '/\\', DS.DS);
		$folder = trim($folder);
		$folder = trim($folder, DS);
		
		$return = realpath($base);
		
		if (empty($folder))
		{
			return $return;
		}
		
		$folder = explode(DS, $folder);
		
		foreach ($folder as $dir)
		{
			$return .= DS . $dir;
			
			if ( ! file_exists($return))
			{
				mkdir($return);
			}
			
			if ( ! file_exists($return . DS . 'index.htm'))
			{
				file_put_contents($return . DS . 'index.htm', '');
			}
		}
		
		return $return;
	}
}

if ( ! function_exists('display_errors'))
{
	/**
	 * display_errors()
	 * Identificar si la aplicación debe mostrar los errores o los logs
	 * 
	 * Se puede cambiar el valor durante la ejecución
	 *
	 * @since 1.1 Se habilitó el cambio del valor mediante ejecución enviando un parametro $set
	 * @since 1.0
	 *
	 * @param bool|NULL $set Si es Bool entonces asigna al valor mediante ejecución
	 * @return bool
	 */
	function &display_errors(bool $set = NULL)
	{
		static $display_errors = []; ## No puede ser referenciado si es BOOL
		
		if (count($display_errors) === 0)
		{
			$display_errors[0] = (bool)str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'));
		}
		
		is_bool($set) and $display_errors[0] = $set;
		
		return $display_errors[0];
	}
}

if ( ! function_exists('print_array'))
{
	/**
	 * print_array()
	 * Muestra los contenidos enviados en el parametro para mostrarlos en HTML
	 *
	 * @param	...array
	 * @return	void
	 */
	function print_array(...$array)
	{
		if ( ! display_errors() and ! is_localhost() and function_exists('APP'))
		{
			APP()->log('Está mostrando información de Desarrollador con la opción `display_errors` desactivada', FALSE);
		}
		
		$r = '';
		
		$trace = debug_backtrace(false);
		if (isset($trace[0]) && isset($trace[0]['function']) && $trace[0]['function'] === 'print_array')
		{
			array_shift($trace);
		}
		
		$file_line = isset($trace[0]) ? ('<small style="color: #ccc;display: block;margin: 0;">' . protect_server_dirs($trace[0]['file']) . ' #' . $trace[0]['line'] . '</small><br>') : '';
		
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
			
			$r.= ($ind > 0 ? '<hr style="border: none;border-top: dashed #ebebeb .5px;margin: 12px 0;">' : '') . $_arr;
		}
		
		echo '<pre style="display: block;text-align: left;color: #444;background: white;position: relative;z-index: 99999999999;margin: 5px 5px 15px;padding: 0px 10px 10px;border: solid 1px #ebebeb;box-shadow: 4px 4px 4px rgba(235, 235, 235, .5);">' . $file_line . $r . '</pre>' . PHP_EOL;
	}
}

if ( ! function_exists('print_r2'))
{
	/**
	 * print_r2()
	 * @see print_array
	 */
	function print_r2(...$array)
	{
		return call_user_func_array('print_array', $array);
	}
}

if ( ! function_exists('die_array'))
{
	/**
	 * die_array()
	 * Muestra los contenidos enviados en el parametro para mostrarlos en HTML y finaliza los segmentos
	 *
	 * @param	...array
	 * @return	void
	 */
	function die_array(...$array)
	{
		$die = true;
		
		if (count($array)>1)
		{
			$last = array_pop($array);
			if (is_bool($last))
			{
				$die = $last;
			}
			else
			{
				$array[] = $last;
			}
		}
		
		call_user_func_array('print_array', $array);
		
		if ($die)
		{
			die();
		}
	}
}

if ( ! function_exists('ip_address'))
{
	/**
	 * ip_address()
	 * Obtiene el IP del cliente
	 *
	 * @param string $get
	 * @return mixed
	 */
	function &ip_address ($get = 'ip_address')
	{
		static $datos = [];
		
		if (count($datos) === 0)
		{
			$datos = [
				'ip_address' => '',
				'separator' => '',
				'binary' => '',
			];
			
			extract($datos, EXTR_REFS);
			
			$ip_address = $_SERVER['REMOTE_ADDR'];

			$spoof = NULL;
			foreach(['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP'] as $ind)
			{
				if ( ! isset($_SERVER[$ind]) OR is_null($_SERVER[$ind]))
				{
					continue;
				}

				$spoof = $_SERVER[$ind];
				sscanf($spoof, '%[^,]', $spoof);

				if ( ! is_ip($spoof))
				{
					$spoof = NULL;
					continue;
				}

				break;
			}

			is_null($spoof) or $ip_address = $spoof;

			$separator = is_ip($ip_address, 'ipv6') ? ':' : '.';

			if ($separator === ':')
			{
				// Make sure we're have the "full" IPv6 format
				$binary = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($ip_address, ':')), $ip_address));

				for ($j = 0; $j < 8; $j++)
				{
					$binary[$j] = intval($binary[$j], 16);
				}
				$sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
			}
			else
			{
				$binary = explode('.', $ip_address);
				$sprintf = '%08b%08b%08b%08b';
			}

			$binary = vsprintf($sprintf, $binary);

			if ( ! is_ip($ip_address))
			{
				$ip_address = '0.0.0.0';
			}
			
			$datos['array'] =& $datos;
		}
		
		if ( ! isset($datos[$get]))
		{
			$get = 'ip_address';
		}
		
		return $datos[$get];
	}
}
