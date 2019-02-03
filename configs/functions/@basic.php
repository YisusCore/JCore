<?php
/**
 * @basic.php
 * 
 * Funciones básicas para la aplicación
 *
 * @package		JCore\Functions
 * @link		https://jcore.jys.pe/files/configs/functions/@basic.php
 * @version		1.0.0
 * @filesource
 */

/**
 * Variables Globales
 */
defined('ROOTPATH') or define('ROOTPATH', __DIR__);

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('HOMEPATH') or define('HOMEPATH', __DIR__);
defined('ABSPATH')  or define('ABSPATH',  __DIR__);
defined('APPPATH')  or define('APPPATH',  __DIR__);

isset($BASES_path) or $BASES_path = [__DIR__];

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
		
		if (count($_dirs) === 0)
		{
			global $BASES_path;
			
			$_dirs[] = ABSPATH;
			$_dirs[] = HOMEPATH;
			$_dirs[] = APPPATH;
			$_dirs[] = ROOTPATH;
			
			$_dirs = array_merge($_dirs, $BASES_path);
		}

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
	function is_localhost(bool $set = NULL)
	{
		static $is_localhost; ## No puede ser referenciado si es BOOL
		
		if ( ! isset($is_localhost))
		{
			$is_localhost = (bool)preg_match('/^(192\.168\.[0-9]{1,3}\.[0-9]{1,3}|127\.[0]{1,3}\.[0]{1,3}\.[0]{0,2}1)$/', $_SERVER['SERVER_ADDR']);
		}
		
		is_bool($set) and $is_localhost = $set;
		
		return $is_localhost;
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
	function display_errors(bool $set = NULL)
	{
		static $display_errors; ## No puede ser referenciado si es BOOL
		
		if ( ! isset($display_errors))
		{
			$display_errors = (bool)str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'));
		}
		
		is_bool($set) and $display_errors = $set;
		
		return $display_errors;
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
		if ( ! display_errors() and ! is_localhost() and function_exists('logger'))
		{
			logger('Está mostrando información de Desarrollador con la opción `display_errors` desactivada', FALSE);
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

if ( ! function_exists('wfile'))
{
	/**
	 * wfile()
	 *
	 * Hace un require_once a los archivos
	 */
	function wfile ($file, $dir = NULL, $reverse = FALSE)
	{
		in_array($dir, ['functions', 'classes', 'libs', 'translate', 'install.bbdd']) and $dir = 'configs/' . $dir;
		
		$dir === 'configs/functions' and ROOTPATH === __DIR__ and $dir = '.';
		empty($dir) and $dir = '.';
		
		$file = basename($file, '.php') . '.php';
		
		global $BASES_path;
		
		$requireds = 0;
		
		$ARRAY = $reverse ? array_reverse($BASES_path) : $BASES_path;
		foreach($ARRAY as $base_dir)
		{
			if ($xFile = $base_dir . DS . $dir . DS . $file and file_exists($xFile))
			{
				require_once $xFile;
				$requireds++;
			}
		}
		
		return $requireds;
	}
}