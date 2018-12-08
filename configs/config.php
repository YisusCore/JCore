<?php
/**
 * config.php
 * 
 * Archivo de Configuración
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


/**
 * charset
 * Charset por Defecto
 *
 * @global
 */
$config['charset'] = 'UTF-8';

/**
 * timezone
 * TimeZone por Defecto
 *
 * @global
 */
$config['timezone'] = 'America/Lima';

/**
 * lang
 * Lenguaje por Defecto
 * WARNING: Si es NULO se detectará el lenguaje del usuario
 *
 * @global
 */
$config['lang'] = NULL;

/**
 * subclass_prefix
 * Prefijo para Extensión de Clases
 *
 * @global
 */
$config['subclass_prefix'] = 'MY_';

/**
 * log
 * Datos de Registros de Logs 
 * (solo en caso de que no se haya guardado antes con el filtro save_logs o en la base datos)
 *
 * BBDD:				Si es TRUE, tomará la conección de la base datos actual y si es array creará una nueva conección
 *
 * PATH:				Directorio donde se almacenarán los archivos 
 * FILE_EXT:			Extensión del archivo a crear
 * FILE_PERMISSIONS:	Permisos del archivo a crear
 * FORMAT_LINE:			Función que retornará la linea con los datos del LOG que se almacenará en el archivo
 *						Los parametros que se enviarán a la función son: $message, $severity, $code, $filepath, $line, $trace, $meta
 *
 * @global
 */
$config['log'] = [];

$config['log']['bbdd'] = TRUE;

$config['log']['path'] = APPPATH . DS . 'logs';

$config['log']['file_ext'] = 'txt';

$config['log']['file_permissions'] = 0644;

$config['log']['format_line'] = function($message, $severity, $code, $filepath, $line, $trace, $meta)
{
	return 	csvstr([
		date2('Y-m-d H:i:s', $meta['time']),
		$severity,
		$code,
		$message,
		$filepath,
		$line,
		json_encode($trace),
		json_encode($meta)
	], ';');
};

/**
 * db - bd
 * Datos de la primera conección de Base Datos
 *
 * HOST:	Host del servidor mysql
 * USER:	Usuario para conectar en el servidor
 * PASW:	Clave de la conección. (Si es NULO entonces el usuario no requiere de clave)
 * NAME:	Nombre de la base datos autorizado
 * PREF:	Prefijo que se utilizará para la creación de tablas por defecto
 *
 * @global
 */
$config['db'] = [];
$config['bd'] =& $config['db'];

//$config['db']['host'] = 'localhost';

//$config['db']['user'] = 'root';

//$config['db']['pasw'] = 'mysql';

//$config['db']['name'] = 'intranet';

//$config['db']['pref'] = 'jc_';

/**
 * functions_files
 * Listado de archivos de funciones
 *
 * @global
 */
$config['functions_files'] = [];

/**
 * autoload_paths
 * Listado de directorios donde buscar las clases no encontradas
 *
 * @global
 */
$config['autoload_paths'] = [];

/**
 * www
 * WWW por Defecto
 *
 * Si el valor es NULO entonces no redireccionará en caso de no corresponder el WWW
 * El valor debe ser boleano y si no corresponde con url('www') redireccionará al que corresponda
 *
 * @global
 */
$config['www'] = NULL;

/**
 * https
 * HTTPS por Defecto
 *
 * Si el valor es NULO entonces no redireccionará en caso de no corresponder el HTTPS
 * El valor debe ser boleano y si no corresponde con url('https') redireccionará al que corresponda
 *
 * @global
 */
$config['https'] = NULL;

/**
 * allowed_http_methods
 * Métodos autorizados para llamar los REQUESTs
 *
 * @global
 */
$config['allowed_http_methods'] = ['GET', 'POST'];

/**
 * default_method
 * El método por defecto para las uris que no se han indicado un parametro de método
 *
 * @global
 */
$config['default_method'] = 'index';

/**
 * home_display
 * El display por defecto para cuando el URI se encuentre vacío
 *
 * @global
 */
$config['home_display'] = 'inicio';

/**
 * error404_display
 * El display por defecto para cuando no se encuentre un display correcto
 *
 * @global
 */
$config['error404_display'] = 'error404';

/**
 * images_zones
 * Datos de las aplicaciones que permiten el procesamiento de la carga de imagenes.
 *
 * Se requiere los siguientes datos por cada zona
 * URI:		Dirección URL absoluta para cargar las imágenes
 *			No debe empezar por https://
 *
 *			Eg:
 *				i.localhost.com
 *				localhost.com/assets/img
 *				www.localhost.com/assets/img
 * ABSPATH:	Directorio base absoluto donde se encontrarán las imagenes
 *          Permite validar el archivo existente de manera local
 *
 * PATH:	Directorio dentro del ABSPATH que contiene las imagenes
 *          
 * SLUG:	Slug que ejecutará el procesador de imagenes
 *			Eg:
 *				img
 *				imagen
 *				i
 *
 * @global
 */
$config['images_zones'] = [];

$_subpath = url('subpath');

$config['images_zones'][] = [
	'uri'     => str_replace('www.', '', url('host')),
	'abspath' => ABSPATH,
	'path'    => '/assets/img',
	'slug'    => 'img'
];

if ( ! empty($_subpath))
{
	$config['images_zones'][] = [
		'uri'     => (string)url('host-abs') . $_subpath,
		'abspath' => ABSPATH,
		'path'    => '/assets/img',
		'slug'    => 'img'
	];
}
else
{
	$config['images_zones'][] = [
		'uri'     => (string)url('host-abs'),
		'abspath' => ABSPATH,
		'path'    => '/assets/img',
		'slug'    => 'img'
	];
}

/**
 * files
 * Datos de la configuración para la carga de archivos en modo local (servidor local)
 *
 * PATH:	Directorio absoluto que contiene los archivos
 * URI:		Dirección URL absoluta para cargar los archivos
 *			Eg:
 *				//f.localhost.com
 *				//localhost.com/assets/files
 *				https://localhost.com/_
 *				https://www.localhost.com/uploads
 *
 * @global
 */
$config['files'] = [];

$config['files']['abspath'] = ABSPATH;

$config['files']['uri'] = (string)url('host-abs');

$config['files']['upload'] = '/_';


/**
 * compress_html
 *
 * si el Response Output en HTML será comprimid
 */
$config['compress_html'] = false;
