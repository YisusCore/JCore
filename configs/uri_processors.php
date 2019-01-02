<?php
/**
 * uri_processors.php
 * 
 * Archivo que contiene todas los procesadores para los URIs
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
 * @package		JCore\APP
 * @author		YisusCore
 * @link		https://jcore.jys.pe/classes
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

//==========================================================================================
// Para agregar mas procesadores utilizar las variables
// $processors, $processor, $config
//==========================================================================================

$processor['/uploader.php'] = function(){
	defined('APPPATH') or exit('Error');

	if ( ! APP()->Logged)
	{
		RSP()
			-> error('Se requiere de un usuario logueado')
			-> exit()
		;
	}

	$F = $_FILES['archivo'];

	if ($F['error']>0){
		RSP()
			-> error('Error al cargar Archivo')
			-> exit()
		;
	}

	extract(config('files'));

	if(preg_match("/^image\/(.*)/", $F['type']))
	{
		$images_zones = config('images_zones');
		extract(end($images_zones));

		$isImagen = TRUE;
	}

	$dir = DS . $upload . DS . date('Y') . DS . date('m');
	mkdir2($dir, $abspath);

	extract($F);

	$name = mb_strtolower($name);
	$name = explode('.', $name);
	$ext = count($name) === 1 ? NULL : array_pop($name);
	$name = implode('.', $name);

	if (is_null($ext)){
		RSP()
			-> error('Archivo no tiene extensión')
			-> exit()
		;
	}

	$name = uniqid(strtoslug($name) . '_');
	if( preg_match('/^php/i', $ext))
	{
		$ext = 'html';
	}

	$path = $dir . DS . $name . '.' . $ext;

	if (file_exists($abspath . $path)){
		$name = na(5) . "_" . $name;
		$path = $dir . DS . $name . '.' . $ext;
	}

	if( ! move_uploaded_file($tmp_name, $abspath . $path)){
		RSP()
			-> error('Error al realizar update de file - Origen o Destino no leible.')
			->addJson('tmp_name', $tmp_name)
			->addJson('path', $path)
			-> exit()
		;
	}

	$href = url('array');
	$href['host'] = $uri;
	$href['path'] = str_replace(DS, '/', $path);
	$href = build_url($href);

	RSP()
		-> success('Archivo cargado correctamente')
		-> addJson('href', $href);

	isset($isImagen) and $isImagen and RSP()
		-> addJson('preview', get_image($href, ['size' => '300x300']))
		-> addJson('favicon', get_image($href, ['size' => '50x50']))
	;

	exit();
};
