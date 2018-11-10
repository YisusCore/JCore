<?php
/**
 * _validacion.php
 * 
 * El archivo `_validacion` permite ...
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
 * @link		https://jcore.jys.pe/functions/_validacion
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

//--------------------------------------------------------------------
// Funciones Generales
//--------------------------------------------------------------------
if ( ! function_exists('is_empty'))
{
	/**
	 * is_empty()
	 * Validar si $valor está vacío
	 *
	 * Si es ARRAY entonces valida que tenga algún elemento
	 * Si es BOOL entonces retorna FALSO ya que es un valor así sea FALSO
	 * 
	 * @param array|bool|string|null $v
	 * @return bool
	 */
	function is_empty($v):bool
	{
		$type = gettype($v);
		
		if ($type === 'NULL')
		{
			return TRUE;
		}
		elseif ($type === 'string')
		{
			if ($v === '0')
			{
				return FALSE;
			}
			
			return empty($v);
		}
		elseif ($type === 'array')
		{
			return count($v) === 0;
		}
		
		return FALSE;
	}
}

if ( ! function_exists('def_empty'))
{
	/**
	 * def_empty()
	 * Obtener un valor por defecto en caso se detecte que el primer valor se encuentra vacío
	 *
	 * @param mixed
	 * @param mixed
	 * @return mixed
	 */
	function def_empty($v, $def = NULL)
	{
		if ( ! is_empty($v))
		{
			return $v;
		}
		
		return $def;
	}
}

if ( ! function_exists('non_empty'))
{
	/**
	 * non_empty()
	 * Ejecutar una función si detecta que el valor no está vacío
	 *
	 * @param mixed
	 * @param callable
	 * @return mixed
	 */
	function non_empty($v, callable $callback)
	{
		if ( ! is_empty($v))
		{
			return $callback($v);
		}
		
		return $v;
	}
}


//---------------------------------------------
// Funciones PregMatch
//---------------------------------------------
if ( ! function_exists('match'))
{
	/**
	 * match()
	 * Ejecuta de manera ordenada la función preg_match
	 *
	 * @param string Valor a validar el REGEXP
	 * @param string El REGEXP
	 * @param string El delimitador de la función
	 * @param string Las banderas de la función
	 * @return int
	 */
	function match ($v, $regex, $delimiter = '/', $flags = '')
	{
		$delimiter = (string)$delimiter;
		
		try
		{
			$v = (string)$v;
		}
		catch (Exception $e)
		{
			return FALSE;
		}
		
		return preg_match($delimiter . $regex . $delimiter . $flags, $v);
	}
}

if ( ! function_exists('is_version'))
{
	/**
	 * is_version()
	 * Validar que el valor tenga el formato de una versión
	 *
	 * @param mixed $V
	 * @param bool $force_point si true entonces indica que $v no solo sea un numero sino que al menos tenga un punto
	 * @return mixed
	 */
	function is_version ($v, $force_point = TRUE)
	{
		static $_regex = '^([0-9]+)(\.[0-9]+){0,}$';
		static $_regex2= '^([0-9]+)(\.[0-9]+){1,}$';
		
		return $force_point ? match($v, $_regex2) : match($v, $_regex);
	}
}

if ( ! function_exists('has_letter'))
{
	/**
	 * has_letter()
	 * Valida que el valor tenga al menos una letra
	 *
	 * @param mixed
	 * @return mixed
	 */
	function has_letter ($v)
	{
		static $_regex = '[a-zA-Z]';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('only_letters'))
{
	/**
	 * only_letters()
	 * Valida que el valor sea solo letras
	 *
	 * @param mixed
	 * @return mixed
	 */
	function only_letters ($v)
	{
		static $_regex = '^[a-zA-Z]+$';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('has_number'))
{
	/**
	 * has_number()
	 * Valida que el valor tenga al menos un número
	 *
	 * @param mixed
	 * @return mixed
	 */
	function has_number ($v)
	{
		static $_regex = '[0-9]';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('only_numbers'))
{
	/**
	 * only_numbers()
	 * Valida que el valor sea solo números
	 *
	 * @param mixed
	 * @return mixed
	 */
	function only_numbers ($v)
	{
		static $_regex = '^[0-9]+$';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('is_zero'))
{
	/**
	 * is_zero()
	 * Valida que el valor sea ZERO (0)
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_zero ($v)
	{
		$v = (double)$v;
		return $v == 0;
	}
}

if ( ! function_exists('has_space'))
{
	/**
	 * has_space()
	 * Valida que el valor tenga al menos un espacio
	 *
	 * @param mixed
	 * @return mixed
	 */
	function has_space ($v)
	{
		static $_regex = '[ ]';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('has_point'))
{
	/**
	 * has_point()
	 * Valida que el valor tenga al menos un punto
	 *
	 * @param mixed
	 * @return mixed
	 */
	function has_point ($v)
	{
		static $_regex = '[\.]';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('only_letters_spaces'))
{
	/**
	 * only_letters_spaces()
	 * Valida que el valor sea solo letras y/o espacios
	 *
	 * @param mixed
	 * @return mixed
	 */
	function only_letters_spaces ($v)
	{
		static $_regex = '^[a-zA-Z ]+$';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('only_numbers_points'))
{
	/**
	 * only_numbers_points()
	 * Valida que el valor sea solo números y puntos
	 *
	 * @param mixed
	 * @return mixed
	 */
	function only_numbers_points ($v)
	{
		static $_regex = '^[0-9\.]+$';
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('is_ascii'))
{
	/**
	 * is_ascii()
	 * Valida si el valor es código ASCII
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_ascii($v)
	{
		static $_regex = '[^\x00-\x7F]';
		return ! match($v, $_regex, '/', 'S');
	}
}



if ( ! function_exists('is_date'))
{
	/**
	 * is_date()
	 * Valida si el valor es fecha
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_date($v)
	{
		static $_regex = '^\d{4}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$';
		
		if (is_empty($v) OR $v === '0000-00-00')
		{
			return FALSE;
		}
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('is_time'))
{
	/**
	 * is_time()
	 * Valida si el valor es hora
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_time($v)
	{
		static $_regex = '^([01][0-9]|2[0123])[:]([0-5][0-9])([:]([0-5][0-9])){0,1}$';
		
		if (is_empty($v) OR $v === '00:00:00' OR $v === '00:00')
		{
			return FALSE;
		}
		
		return match($v, $_regex);
	}
}

if ( ! function_exists('is_datetime'))
{
	/**
	 * is_datetime()
	 * Valida si el valor es fecha y hora
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_datetime($v)
	{
		static $_regex = '^\d{4}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01]) '.
						 '([01][0-9]|2[0123])[:]([0-5][0-9])([:]([0-5][0-9])){0,1}$';
		
		if (is_empty($v) OR $v === '0000-00-00 00:00:00' OR $v === '0000-00-00 00:00')
		{
			return FALSE;
		}
		
		return match($v, $_regex);
	}
}


//---------------------------------------------
// Contadores
//---------------------------------------------
if ( ! function_exists('words_len'))
{
	/**
	 * words_len()
	 * Valida si el valor es fecha
	 *
	 * @param mixed
	 * @return mixed
	 */
	function words_len($v)
	{
		$v = strip_tags($v);
		return str_word_count($v, 0);
	}
}



//---------------------------------------------
// Min & Max
//---------------------------------------------
if ( ! function_exists('min_len'))
{
	/**
	 * min_len()
	 * Valida que el valor tenga un largo mínimo
	 *
	 * @param mixed
	 * @param int
	 * @return mixed
	 */
	function min_len($v, $len)
	{
		$len = (int)$len;
		return mb_strlen($v) >= $len;
	}
}

if ( ! function_exists('max_len'))
{
	/**
	 * max_len()
	 * Valida que el valor tenga un largo máximo
	 *
	 * @param mixed
	 * @param int
	 * @return mixed
	 */
	function max_len($v, $len)
	{
		$len = (int)$len;
		return mb_strlen($v) <= $len;
	}
}

if ( ! function_exists('range_len'))
{
	/**
	 * range_len()
	 * Valida que el valor tenga un largo entre un mínimo y un máximo
	 *
	 * @param mixed
	 * @param int
	 * @param int
	 * @return mixed
	 */
	function range_len($v, $len_min, $len_max)
	{
		$len_min = (int)$len_min;
		$len_max = (int)$len_max;
		
		return min_len($v, $len_min) and max_len($len_max);
	}
}

if ( ! function_exists('min_words'))
{
	/**
	 * min_words()
	 * Valida que el valor tenga un mínimo de palabras
	 *
	 * @param mixed
	 * @param int
	 * @return mixed
	 */
	function min_words($v, $len)
	{
		$len = (int)$len;
		return words_len($v) >= $len;
	}
}

if ( ! function_exists('max_words'))
{
	/**
	 * max_words()
	 * Valida que el valor tenga un máximo de palabras
	 *
	 * @param mixed
	 * @param int
	 * @return mixed
	 */
	function max_words($v, $len)
	{
		$len = (int)$len;
		return words_len($v) <= $len;
	}
}

if ( ! function_exists('range_words'))
{
	/**
	 * range_words()
	 * Valida que el valor tenga un rango de palabras
	 *
	 * @param mixed
	 * @param int
	 * @return mixed
	 */
	function range_words($v, $len_min, $len_max)
	{
		$len_min = (int)$len_min;
		$len_max = (int)$len_max;
		
		return min_words($v, $len_min) and max_words($len_max);
	}
}



//---------------------------------------------
// Otros
//---------------------------------------------
if ( ! function_exists('is_mail'))
{
	/**
	 * is_mail()
	 * Valida que el valor sea un correo válido
	 *
	 * @param mixed
	 * @return mixed
	 */
	function is_mail($v)
	{
		$v = trim($v);
		if (is_empty($v))
		{
			return FALSE;
		}
		
		$def_valid = (bool)filter_var($v, FILTER_VALIDATE_EMAIL);
		if ( ! $def_valid)
		{
			return FALSE;
		}
		//A little bit more validations
		$at_pos = strrpos($v, '@');
		$dot_pos = strrpos($v, '.');
		if ($at_pos < 1 || $dot_pos < $at_pos + 2 || $dot_pos + 2 >= mb_strlen($v))
		{
			// No hay un arroba o este esta al comenzar
			// El punto esta antes del arroba
			// El arroba y el punto estan juntos
			// El punto solo tiene un caracter despues
			return FALSE;
		}

		$name = mb_substr($v, 0, $at_pos);
		// Really Valid Chars
		// @link	https://www.jochentopf.com/email/chars.html
		// OK: 		a-zA-Z0-9+-._
		// MAYBE: 	&'*/=?^{}~
		if ( ! preg_match('/^[a-zA-Z0-9\+\-\.\_\&\'\*\/\=\?\^\{\}\~]*$/', $name))
		{
			return FALSE;
		}

		$domain = mb_substr($v, $at_pos + 1);
		static $temporary_mail_domains = [];
		if(count($temporary_mail_domains) === 0)
		{
			try
			{
				if ( ! file_exists(__DIR__ . '/disposables.json'))
				{
					trigger_error('Disposables Domains Files Not Exists', E_USER_WARNING);
					return TRUE;
				}

				$temporary_mail_domains = json_decode(file_get_contents(__DIR__ . '/disposables.json'), true);
			}
			catch(Exception $e)
			{}
		}

		if (in_array($domain, $temporary_mail_domains))
		{
			//Is a Temporary Mail
			return FALSE;
		}

		return TRUE;
	}
}

if ( ! function_exists('is_ip'))
{
	/**
	 * is_ip()
	 * Valida que el valor sea una IP válida
	 *
	 * @param mixed
	 * @param string $which (ipv4, ipv6, FILTER_FLAG_IPV4, FILTER_FLAG_IPV6)
	 * @return bool
	 */
	function is_ip($ip, $which = NULL)
	{
		switch (mb_strtolower((string)$which))
		{
			case 'ipv4':case 'FILTER_FLAG_IPV4':
				$which = FILTER_FLAG_IPV4;
				break;
			case 'ipv6':case 'FILTER_FLAG_IPV6':
				$which = FILTER_FLAG_IPV6;
				break;
		}

		return filter_var($ip, FILTER_VALIDATE_IP, $which);
	}
}

