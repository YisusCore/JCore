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
	 * @param $file string Archivo a leer
	 * @param $first_founded_only boolean (Optional) Si se requiere solo obtener el primer archivo encontrado
	 * @param $return_list boolean (Optional) Si se desea obtener el listado pero no requerir los archivos
	 * @param $function string (include|require) Que función ejecutar para requerir el archivo
	 * @param $once boolean (Optional) Si se desea que la función a ejecuta sea _once
	 * @param $scandir_sorting_order boolean (Optional) Si se desea que la función a ejecuta sea _once
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
					$lista[] = $_temp;
				}
				
				continue;
			}
			
			$_temp = $_temp_path . DS . $file;
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
	}
}



