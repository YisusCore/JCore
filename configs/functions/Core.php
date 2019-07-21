<?php
/**
 * Core.php
 * Archivo de funciones principales
 *
 * @filesource
 */

/**
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or 
	$BASES_path = [];

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
			$_files = array_reverse((array)load_file (DS . 'configs' . DS . 'config.php', FALSE, TRUE));
			foreach($_files as $_file)
			{
				require_once $_file;
			}
			
			$_files = array_reverse((array)load_file (DS . 'configs' . DS. ENVIRONMENT . DS . 'config.php', FALSE, TRUE));
			foreach($_files as $_file)
			{
				require_once $_file;
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
		
		isset($config[$get]) or 
		$config[$get] = NULL;
		
		return $config[$get];
	}
}
