<?php
/**
 * mngr.files.php
 * 
 * El archivo `mngr.files` permite ...
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
 * @link		https://jcore.jys.pe/functions/mngr.files
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

function csvstr(...$params) : string
{
    $f = fopen('php://memory', 'r+');
	array_unshift($params, $f);

    if (call_user_func_array('fputcsv', $params) === false) {
        return false;
    }

    rewind($f);
    $csv_line = stream_get_contents($f);
    return ltrim($csv_line);
}


if ( ! function_exists('download'))
{
	/**
	 * Force Download
	 *
	 * Generates headers that force a download to happen
	 *
	 * @param	string	filename
	 * @param	mixed	the data to be downloaded
	 * @param	bool	whether to try and send the actual file MIME type
	 * @return	void
	 */
	function download($filename = '', $data = '', $set_mime = FALSE)
	{
		if ($filename === '' OR $data === '')
		{
			return;
		}
		elseif ($data === NULL)
		{
			if ( ! @is_file($filename) OR ($filesize = @filesize($filename)) === FALSE)
			{
				return;
			}

			$filepath = $filename;
			$filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
			$filename = end($filename);
		}
		else
		{
			$filesize = strlen($data);
		}

		// Set the default MIME type to send
		$mime = 'application/octet-stream';

		$x = explode('.', $filename);
		$extension = end($x);

		if ($set_mime === TRUE)
		{
			if (count($x) === 1 OR $extension === '')
			{
				/* If we're going to detect the MIME type,
				 * we'll need a file extension.
				 */
				return;
			}

			// Only change the default MIME if we can find one
			$mime = get_mime($extension);
		}

		/* It was reported that browsers on Android 2.1 (and possibly older as well)
		 * need to have the filename extension upper-cased in order to be able to
		 * download it.
		 *
		 * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
		 */
		if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT']))
		{
			$x[count($x) - 1] = strtoupper($extension);
			$filename = implode('.', $x);
		}

		if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE)
		{
			return;
		}

		// Clean output buffer
		if (ob_get_level() !== 0 && @ob_end_clean() === FALSE)
		{
			@ob_clean();
		}

		// Generate the server headers
		header('Content-Type: '.$mime);
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.$filesize);
		header('Cache-Control: private, no-transform, no-store, must-revalidate');

		// If we have raw data - just dump it
		if ($data !== NULL)
		{
			exit($data);
		}

		// Flush 1MB chunks of data
		while ( ! feof($fp) && ($data = fread($fp, 1048576)) !== FALSE)
		{
			echo $data;
		}

		fclose($fp);
		exit;
	}
}


if ( ! function_exists('directory_map'))
{
	/**
	 * Create a Directory Map
	 *
	 * Reads the specified directory and builds an array
	 * representation of it. Sub-folders contained with the
	 * directory will be mapped as well.
	 *
	 * @param	string	$source_dir		Path to source
	 * @param	int	$directory_depth	Depth of directories to traverse
	 *						(0 = fully recursive, 1 = current dir, etc)
	 * @param	bool	$hidden			Whether to show hidden files
	 * @return	array
	 */
	function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
	{
		if ($fp = @opendir($source_dir))
		{
			$filedata	= array();
			$new_depth	= $directory_depth - 1;

			while (FALSE !== ($file = readdir($fp)))
			{
				// Remove '.', '..', and hidden files [optional]
				if ($file === 'index.htm' OR $file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.'))
				{
					continue;
				}

				if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir . DS . $file))
				{
					$filedata[DS . $file] = directory_map($source_dir . DS . $file, $new_depth, $hidden);
				}
				else
				{
					$filedata[] = DS . $file;
				}
			}

			closedir($fp);
			return $filedata;
		}

		return [];
	}
}

if ( ! function_exists('unlink_directory'))
{
	function unlink_directory($source_dir, $directory_depth = 0)
	{
		if ( ! file_exists($source_dir) or ! is_dir($source_dir))
		{
			return TRUE;
		}
		
		$source_dir = realpath($source_dir);
		
		if ($fp = @opendir($source_dir))
		{
			$new_depth	= $directory_depth - 1;

			while (FALSE !== ($file = readdir($fp)))
			{
				// Remove '.', '..', and hidden files [optional]
				if ($file === '.' OR $file === '..')
				{
					continue;
				}

				if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir . DS . $file))
				{
					unlink_directory($source_dir . DS . $file, $new_depth);
				}
				else
				{
					@unlink($source_dir . DS . $file);
				}
			}

			closedir($fp);
			@rmdir($source_dir);
			
			return TRUE;
		}
	}
}













