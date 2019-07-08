<?php
/**
 * Logger.php
 *
 * @filesource
 */

if ( ! function_exists('print_array'))
{
	/**
	 * print_array()
	 * Muestra los contenidos enviados en el parametro para mostrarlos en HTML
	 *
	 * @use display_errors
	 * @use is_localhost
	 * @use logger
	 * @use protect_server_dirs
	 *
	 * @param	...array
	 * @return	void
	 */
	function print_array(...$array)
	{
		if (function_exists('display_errors') and function_exists('is_localhost') and function_exists('logger') and 
			! display_errors() and ! is_localhost())
		{
			logger('Está mostrando información de Desarrollador con la opción `display_errors` desactivada', FALSE);
		}

		$r = '';

		$trace = debug_backtrace(false);
		if (isset($trace[0]) && isset($trace[0]['function']) && $trace[0]['function'] === 'print_array')
		{
			array_shift($trace);
		}

		$file_line = '';
		if (isset($trace[0]))
		{
			$file_line = $trace[0]['file'] . ' #' . $trace[0]['line'];
			
			function_exists('protect_server_dirs') and
			$file_line = protect_server_dirs($file_line);
			
			$file_line = '<small style="color: #ccc;display: block;margin: 0;">' . $file_line . '</small><br>';
		}

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

