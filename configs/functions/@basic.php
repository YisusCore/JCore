<?php
/**
 * @basic.php
 * Funciones básicas
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
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or 
	$BASES_path = [];

if ( ! function_exists('load_file'))
{
	/**
	 * load_file
	 *
	 * Permite leer y requerir los archivos encontrados de todos los directorios 
	 *
	 * @param string $file Archivo a leer
	 * @param boolean $first_founded_only (Optional) Si se requiere solo obtener el primer archivo encontrado
	 * @param boolean $return_list (Optional) Si se desea obtener el listado pero no requerir los archivos
	 * @param string $function (include|require) Que función ejecutar para requerir el archivo
	 * @param boolean $once (Optional) Si se desea que la función a ejecuta sea _once
	 * @param boolean $scandir_sorting_order (Optional) Si se desea que la función a ejecuta sea _once
	 *
	 * @return array|void
	 */
	function load_file ($file, $first_founded_only = FALSE, $return_list = FALSE, $function = 'include', $once = TRUE, $scandir_sorting_order = SCANDIR_SORT_NONE)
	{
		// Corrección de ruta
		$file = str_replace(['/', '\\', DS], DS, $file);
		
		// Separar Directorio de Archivo
		$file_dir = explode(DS, $file);
		
		// El último es el archivo a buscar
		$file = array_pop($file_dir);
		
		
		// Si el directorio buscado es uno que esta dentro del directorio configs pero de forma abreviado
		$dirs_in_config = ['functions', 'classes', 'libs', 'translate', 'install.bbdd'];
		if(isset($file_dir[0]) and in_array($file_dir[0], $dirs_in_config))
		{
			array_unshift($file_dir, 'configs');
		}
		
		// Unimos el directorio y corregimos el DS inicial (si es que hay)
		$file_dir = DS . ltrim(implode(DS, $file_dir), DS);
		
		$file_dir === DS and
		$file_dir = '';
		
		// Si se requiere obtener todo el listado archivos en el directorio buscado
		$all_files_of_dir = $file === '*';

		global $BASES_path;
		
		// Identificar si se requiere buscar un archivo específico de BASEPATH
		$BASE_path_matched = NULL;
		foreach($BASES_path as $_path)
		{
			if ($_temp = str_replace($_path, '', $file) and $_temp !== $file)
			{
				$BASE_path_matched = $_path;
				break;
			}
		}
		
		$lista = [];
		
		foreach($BASES_path as $_path)
		{
			if ($first_founded_only and count($lista) >= 1)
			{
				// Finalizamos el proceso ya que solo requiere el primer encontrado
				break;
			}
			
			if ( ! is_null($BASE_path_matched) and $_path !== $BASE_path_matched)
			{
				// Si no es el BASEPATH matchado, pasamos al siguiente
				continue;
			}
			
			$_temp_path = $_path . $file_dir;
			
			if ($all_files_of_dir)
			{
				// Escaneamos todo el directorio
				if (file_exists($_temp_path))
				{
					$_files = @scandir ($_temp_path, $scandir_sorting_order);
				}
				else
				{
					$_files = [];
				}
				
				$_files = (array)$_files;

				foreach($_files as $_file)
				{
					if (in_array($_file, ['.', '..', 'index.htm']))
					{
						// Los archivos ., .. e index.htm son excluidos
						continue;
					}
					
					if (is_dir($_file))
					{
						// Los directorios son excluidos
						continue;
					}
					
					if (empty($_file))
					{
						// Si es vacío continuar
						continue;
					}
					
					$_temp = $_temp_path . DS . $_file;
					
					file_exists($_temp) and
					$lista[] = $_temp;
				}
				
				continue;
			}
			
			$_temp = $_temp_path . DS . $file;
			
			file_exists($_temp) and
			$lista[] = $_temp;
		}
		
		if ($return_list)
		{
			return $lista;
		}
		
		foreach ($lista as $_temp)
		{
			switch($function)
			{
				case 'require':
					if($once)
					{
						require_once $_temp;
					}
					else
					{
						require_once $_temp;
					}
					break;
				case 'include':default:
					if($once)
					{
						include_once $_temp;
					}
					else
					{
						include_once $_temp;
					}
					break;
					
			}
		}
		
		return count($lista);
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

if ( ! function_exists('display_errors'))
{
	/**
	 * display_errors()
	 * Identificar si la aplicación debe mostrar los errores o los logs
	 * 
	 * Se puede cambiar el valor durante la ejecución
	 *
	 * @param bool|NULL $set Si es Bool entonces asigna al valor mediante ejecución
	 * @return bool
	 */
	function &display_errors(bool $set = NULL)
	{
		static $display_errors = []; ## No puede ser referenciado si es BOOL
		
		count($display_errors) === 0 and
			$display_errors[0] = (bool)str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', @ini_get('display_errors'));
		
		is_bool($set) and 
			$display_errors[0] = $set;
		
		return $display_errors[0];
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
	 * @param bool|NULL $set Si es Bool entonces asigna al valor mediante ejecución
	 * @return bool
	 */
	function &is_localhost(bool $set = NULL)
	{
		static $is_localhost = []; ## No puede ser referenciado si es BOOL

		count($is_localhost) === 0 and
		$is_localhost[0] = (bool)preg_match('/^(192\.168\.[0-9]{1,3}\.[0-9]{1,3}|127\.[0]{1,3}\.[0]{1,3}\.[0]{0,2}1)$/', $_SERVER['SERVER_ADDR']);

		is_bool($set) and 
		$is_localhost[0] = $set;

		return $is_localhost[0];
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

		$add_basespath and 
		$_dirs = array_merge(array_combine($BASES_path, array_map(function($path){
			return DS . basename($path);
		}, $BASES_path)), $_dirs);

		return strtr($str, $_dirs);
	}
}
