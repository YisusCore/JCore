<?php
/**
 * mngr.url.php
 * 
 * El archivo `mngr.url` permite ...
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
 * @link		https://jcore.jys.pe/functions/mngr.url
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


if ( ! function_exists('redirect_default_www'))
{
	function redirect_default_www ()
	{
		$WWW =& url('www');
		$WWW_def =& config('www', ['www' => NULL]);

		if ( ! is_null($WWW_def) and $WWW !== $WWW_def)
		{
			$host =& url('host');

			if ($WWW_def)
			{
				$host = 'www.' . $host;
			}
			else
			{
				$host = preg_replace('/^www\./i', '', $host);
			}

			redirect(build_url(url('array')));
		}
	};
}

if ( ! function_exists('redirect_default_protocol'))
{
	function redirect_default_protocol ()
	{
		$HTTPS =& url('https');
		$HTTPS_def =& config('https');

		if ( ! is_null($HTTPS_def) and $HTTPS !== $HTTPS_def)
		{
			$scheme =& url('scheme');

			$scheme = $HTTPS_def ? 'https' : 'http';
			redirect(build_url(url('array')));
		}
	};
}

if ( ! function_exists('check_image_slug'))
{
	function check_image_slug ()
	{
		$images_zones = (array)config('images_zones');
		foreach (array_reverse($images_zones) as $zone)
		{
			if (preg_match('#'.regex($zone['uri']).'#i', url()) && RTR()->portal() === ucfirst($zone['slug']))
			{
				$mng = new ImageMng($zone);
				$mng-> processUrl();
				break;
			}
		}
	};
}


if ( ! function_exists('subdomain'))
{
	function subdomain ($subdomain, $from = 'base')
	{
		$datos = url('array');
		$host = $datos['scheme'] . '://' . $subdomain . '.' . ($from === 'base' ? $datos['host-base'] : $datos['host']) . $datos['port-link'];
		return $host;
	};
}

if ( ! function_exists('url_post'))
{
	function url_post ($url, $data = [])
	{
		$data = http_build_query($data);
		$opts = [
			'http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => $data,
			],
		];
		
		$context = stream_context_create($opts);
		return file_get_contents($url, FALSE, $context);
	}
}

if ( ! function_exists('url_get'))
{
	function url_get ($url, $data = [])
	{
		$data = http_build_query($data);
		$opts = [
			'http' => [
				'method' => 'GET',
				'content' => $data,
			],
		];
		
		$context = stream_context_create($opts);
		return file_get_contents($url, FALSE, $context);
	}
}


if ( ! function_exists('build_url'))
{
	/**
	 * build_url()
	 * Construye una URL
	 *
	 * @param	array	$parsed_url	Partes de la URL a construir {@see http://www.php.net/manual/en/function.parse-url.php}
	 * @return	string
	 */
	function build_url($parsed_url)
	{
		isset($parsed_url['query']) and is_array($parsed_url['query']) and 
			$parsed_url['query'] = http_build_query($parsed_url['query']);

		$scheme   = isset($parsed_url['scheme'])  ? $parsed_url['scheme']  : '';
		$host     = isset($parsed_url['host'])    ? $parsed_url['host']    : '';
		$port     = isset($parsed_url['port'])    ? $parsed_url['port']    : '';
		$user     = isset($parsed_url['user'])    ? $parsed_url['user']    : '';
		$pass     = isset($parsed_url['pass'])    ? $parsed_url['pass']    : '';
		$path     = isset($parsed_url['path'])    ? $parsed_url['path']    : '';
		$query    = isset($parsed_url['query'])   ? $parsed_url['query']   : '';
		$fragment = isset($parsed_url['fragment'])? $parsed_url['fragment']: '';

		if (in_array($port, [80, 443]))
		{
			## Son puertos webs que dependen del scheme
			empty($scheme) and $scheme = $port === 80 ? 'http' : 'https';
			$port = '';
		}
		
		empty($scheme)   or $scheme .= '://';
		empty($port)     or $port    = ':' . $port;
		empty($pass)     or $pass    = ':' . $pass;
		empty($query)    or $query   = '?' . $query;
		empty($fragment) or $fragment= '#' . $fragment;

		$pass     = ($user || $pass) ? "$pass@" : '';

		return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
	}
}
