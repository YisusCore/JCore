<?php
/**
 * JCore.php
 * 
 * El núcleo inicializa todas las funciones básicas y todas las configuraciones mínimas.
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
 * @package		JCore
 * @author		YisusCore
 * @link		https://jcore.jys.pe/jcore
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
 * DIRECTORIO DEL SITIO
 *
 * Directorio Raiz de donde es leído el app
 *
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @global
 */
defined('HOMEPATH') or define('HOMEPATH', ABSPATH);

/**
 * DIRECTORIO NÚCLEO JCORE
 *
 * La variable contiene la ruta a la carpeta del núcleo JCore.
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @internal
 */
( ! isset($JCore_path) or empty($JCore_path)) and $JCore_path = __DIR__;
define('ROOTPATH', $JCore_path);

/**
 * DIRECTORIO PROCESOS DE APLICACIÓN
 *
 * La variable contiene la ruta a la carpeta que contiene las 
 * funciones {@link https://jcore.jys.pe/functions}, 
 * configuraciones {@link https://jcore.jys.pe/configs}, 
 * objetos {@link https://jcore.jys.pe/objects}, 
 * procesadores {@link https://jcore.jys.pe/processers} y 
 * pantallas {@link https://jcore.jys.pe/displays} de la aplicación.
 *
 * *DIRECTORIOS EN LA CARPETA*
 * * functions, almacena todas los archivos de las funciones
 * * class, almacena todos los archivos de las clases
 * * libs, almacena todos los archivos de las librerías
 * * config, almacena todos los archivos que afectan a la configuración
 * * processors, almacena todos los archivos procesadores
 * * displays, almacena todos los archivos encargados de manipular el contenido del RESPONSE
 * * templates, almacena partes html/php de vistas repetibles
 *
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @internal
 */
( ! isset($APP_path) or empty($APP_path)) and $APP_path = ABSPATH;
define('APPPATH',  $APP_path);

/**
 * VARIABLE JCore
 *
 * Variable global que permite almacenar valores y datos de manera global 
 * sin necesidad de almacenarlo en una sesión u otra variable posiblemente 
 * no existente
 *
 * @global
 */
$JCore = [];
$JC =& $JCore;

/**
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or $BASES_path = [APPPATH, ROOTPATH];

/**
 * VALIDACIÓN PHP VERSION, APACHE MODULS, PHP EXTENSIONS
 *
 * Se valida la versión de PHP mínima así como los módulos del apache y las 
 * extensiones de PHP requeridas para que funcione correctamente el SISTEMA
 */
if ($server_validation)
{
	$modules    = function_exists('apache_get_modules')    ? apache_get_modules()    : [];
	$extensions = function_exists('get_loaded_extensions') ? get_loaded_extensions() : ['mbstring', 'iconv'];

	if ( ! version_compare(PHP_VERSION, '5.6', '>='))
	{
		die('<br /><b>PHP Versión: ' . phpversion() . '</b> Se requiere al menos Versión 5.6');
	}
	
	foreach (['mod_cache', 'mod_deflate', 'mod_expires', 'mod_filter', 'mod_headers', 'mod_mime', 'mod_rewrite'] as $module)
	{
		if ( ! in_array($module, $modules))
		{
			die('<br /><b>Fatal Error:</b> Módulo de APACHE requerido `' . $module . '`');
		}
	}
	unset($module);

	foreach (['zip', 'json', 'session', 'curl', 'fileinfo', 'gd', 'mysqli', 'hash', 'mbstring', 'iconv'] as $extension)
	{
		if ( ! in_array($extension, $extensions))
		{
			die('<br /><b>Fatal Error:</b> Extensión de PHP requerido `' . $extension . '`');
		}
	}
	unset($extension);
	
	unset($modules, $extensions);
}

/**
 * session_start
 *
 * Iniciando la sesión
 */
session_start();

/**
 * ob_start
 *
 * Iniciando el leído del buffering
 */
ob_start();

/**
 * Cargando Archivo de Funciones básicas
 * El archivo _basic.php contiene todas las funciones básicas a utilizar en el sistema
 *
 * @internal
 */
require_once ROOTPATH . DS . 'the.functns' . DS . '_basic.php'; ## funciones básicas

foreach($BASES_path as $basedir)
{
	if ($server_validation)
	{
		/**
		 * Creando Directorios Base
		 * Valida que los diretorios principales hayan sido creados o los crea
		 *
		 * @internal
		 */
		foreach([
			'displays', 'processors', 'objects', 'templates', 
			'the.configs', 'the.functns', 'the.classes', 'the.libs', 'translate'
		] as $dir)
		{
			mkdir2(DS . $dir, $basedir);
		}
	}

	foreach ([
		// Funciones Generales
		'_variables',	## Funciones de conjuntos de Variables
		'_mimes',		## Funciones y clase manipuladora de los mimes
		'_validacion',	## Funciones de validación
		'_security',	## Funciones de Seguridad

		// Función Principal
		'core',			## Funciones principales del núcleo

		// Funciones Manipuladoras
		'mngr.bbdd',	## Funciones manipuladores de las BBDDs
		'mngr.vrbls',	## Funciones manipuladores de (Array, Date, Strings, Numerics)
		'mngr.files',	## Funciones manipuladores de (Directory, Download, File)
		'mngr.html',	## Funciones manipuladores de (Html)
		'mngr.url',		## Funciones manipuladores de (URL)
	] as $file_name)
	{
		$file = $basedir . DS . 'the.functns' . DS . $file_name . '.php';

		if ( ! file_exists($file))
		{
			$server_validation and file_put_contents($file, '<?php' .PHP_EOL);
			continue;
		}

		require_once $file;
	}
}

/**
 * DEFINIENDO EL HANDLER _autoload
 * @see _autoload()
 *
 * @internal
 */
spl_autoload_register('_autoload');

/**
 * DEFINIENDO EL HANDLER _error_handler
 * @see _error_handler()
 *
 * @internal
 */
set_error_handler('_error_handler');

/**
 * DEFINIENDO EL HANDLER _exception_handler
 * @see _exception_handler()
 *
 * @internal
 */
set_exception_handler('_exception_handler');

/**
 * DEFINIENDO EL HANDLER _shutdown_handler
 * @see _shutdown_handler()
 *
 * @internal
 */
register_shutdown_function('_shutdown_handler');

/**
 * LEYENDO ARCHIVOS DE FUNCIONES (Extras)
 * Recorre todas las funciones indicadas en la configuración
 *
 * @internal
 */
foreach((array)config('functions_files') as $file)
{
	if (file_exists($file))
	{
		require_once $file;
	}
}

/**
 * LEYENDO LOS HOOKS (Acciones programadas)
 * Lee todas las acciones programadas
 *
 * @internal
 */
foreach($BASES_path as $basedir)
{
	if ($file = $basedir. DS. 'the.configs'. DS. 'hooks.php' AND file_exists($file))
	{
		require_once $file;
	}
}

/**
 * EJECUTANDO ACCIÓN PROGRAMADA `functions_loaded`
 * @see action_apply()
 *
 * @internal
 */
action_apply('functions_loaded');


/**
 * Inicializar el APP
 * Permite inicializar la clase APP y todo las configuraciones
 *
 * @internal
 */
APP()->init();

/**
 * Marcando el punto de proceso `total_execution_time_start`
 * @see mark()
 *
 * @internal
 */
mark('total_execution_time_start');

/**
 * EJECUTANDO ACCIÓN PROGRAMADA `core_start`
 * @see action_apply()
 *
 * @internal
 */
action_apply('core_start');

/**
 * Marcando el punto de proceso `core_start`
 * @see mark()
 *
 * @internal
 */
mark('core_start');

## Validar si el UTF-8 està habilitado
if (defined('PREG_BAD_UTF8_ERROR') && APP()->charset === 'UTF-8')
{
	/**
	 * UTF8_ENABLED
	 *
	 * Variable que permite conocer si la codificación UTF8 está habilitado
	 *
	 * @global
	 */
	define('UTF8_ENABLED', TRUE);
}
else
{
	define('UTF8_ENABLED', FALSE);
}

/*
 * ------------------------------------------------------
 * Prioridad de WWW y HTTPS
 * ------------------------------------------------------
 */
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

$HTTPS =& url('https');
$HTTPS_def =& config('https');

if ( ! is_null($HTTPS_def) and $HTTPS !== $HTTPS_def)
{
	$scheme =& url('scheme');
	
	$scheme = $HTTPS_def ? 'https' : 'http';
	redirect(build_url(url('array')));
}

## Conectar la base de datos
$db =& config('db');

if ( ! is_empty($db))
{
	isset($db['host']) or $db['host'] = 'localhost';
	isset($db['user']) or $db['user'] = 'root';
	isset($db['pasw']) or $db['pasw'] = NULL;

	cbd($db['host'], $db['user'], $db['pasw'], $db['name']);
}

RTR()->init();

action_apply('jcore_loaded');

/**
 * Imagen Service Slug
 */
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
