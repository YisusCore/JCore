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
 * @link		https://jcore.jys.pe/
 * @version		1.0.1
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

defined('EXECTIMESTART') or define('EXECTIMESTART', microtime(TRUE));

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
defined('HOMEPATH') or exit('<br /><b>Fatal Error:</b> La variable HOMEPATH no definida.');

/**
 * SUBDIRECTORIO DEL SITIO
 *
 * Subdirectorio donde se encuentra alojado el archivo init.php {@see init.php}
 *
 * Si variable SUBPATH se encuentra vacío entonces la aplicación se 
 * encuentra alojada en la misma carpeta del sitio.
 *
 * WARNING: No debe finalizar pero si empezar con DS (Directory Separator)
 *
 * @global
 */
defined('SUBPATH') or define('SUBPATH', DS);

/**
 * DIRECTORIO ABSOLUTO DEL SITIO
 *
 * Carpeta donde se encuentra alojado el init.php {@see init.php}
 *
 * @global
 */
defined('ABSPATH') or define('ABSPATH', realpath(HOMEPATH . SUBPATH));

/**
 * DIRECTORIO NÚCLEO JCORE
 *
 * La variable contiene la ruta a la carpeta del núcleo JCore.
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @internal
 */
define('ROOTPATH', __DIR__);

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
defined('APPPATH') or define('APPPATH',  ABSPATH);

/**
 * ENVIRONMENT - AMBIENTE DE DESARROLLO
 *
 * Permite manejar distintas configuraciones dependientemente de 
 * la etapa o fase en la que se encuentre la aplicación (proyecto)
 *
 * **Posibles valores:**
 * *	desarrollo
 * *	pruebas
 * *	produccion
 *
 * @global
 */
defined('ENVIRONMENT') or define('ENVIRONMENT', 'desarrollo');

/**
 * ERROR REPORTING
 *
 * Dependientemente del ambiente de desarrollo, el sistema mostrará
 * diferentes levels de errores.
 *
 * @internal
 */
switch (ENVIRONMENT)
{
	case 'pruebas':
	case 'produccion':
		ini_set('display_errors', 0);
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
	break;

	case 'desarrollo':
	default:
		ini_set('display_errors', 1);
		error_reporting(E_ALL & ~E_NOTICE);
//		error_reporting(-1);
	break;
}

/**
 * APP_NAMESPACE
 *
 * 
 */
defined('APP_NAMESPACE') or define('APP_NAMESPACE', 'JCore App')

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
isset($BASES_path) or $BASES_path = [];

in_array(APPPATH, $BASES_path) or $BASES_path[] = APPPATH;
in_array(ROOTPATH, $BASES_path) or $BASES_path[] = ROOTPATH;

/** Verificando las carpetas base */
foreach($BASES_path as &$path)
{
	$_path = $path;
	
	if (($_temp = realpath($path)) !== FALSE)
	{
		$path = $_temp;
	}
	else
	{
		$path = strtr(
			rtrim($path, '/\\'),
			'/\\',
			DS.DS
		);
	}
	
	if ( ! is_dir($path) || ! file_exists($path))
	{
		header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
		echo 'El directorio `' . $_path . '` no es correcto o no existe.';
		exit(3); // EXIT_CONFIG
	}
	
	unset($path, $_path);
}

/** Corrigiendo directorio base cuando se ejecuta como comando */
defined('STDIN') and chdir(APPPATH);

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
 * El archivo @basic.php contiene todas las funciones básicas a utilizar en el sistema
 *
 * @internal
 */
require_once ROOTPATH . DS . 'configs' . DS . 'functions' . DS . '@basic.php'; ## funciones básicas

foreach($BASES_path as $basedir)
{
	if (isset($server_validation) and $server_validation)
	{
		/**
		 * Creando Directorios Base
		 * Valida que los diretorios principales hayan sido creados o los crea
		 *
		 * @internal
		 */
		
		foreach(['displays', 'processors', 'objects', 'templates', 'configs'] as $dir)
		{
			mkdir2(DS . $dir, $basedir);
		}
		
		foreach(['functions', 'classes', 'libs', 'translate'] as $dir)
		{
			mkdir2(DS . $dir, $basedir . DS . 'configs');
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
		$file = $basedir . DS . 'configs' . DS . 'functions' . DS . $file_name . '.php';

		if ( ! file_exists($file))
		{
			isset($server_validation) and $server_validation and file_put_contents($file, '<?php' .PHP_EOL);
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
 * LEYENDO LOS HOOKS (Acciones programadas)
 * Lee todas las acciones programadas
 *
 * @internal
 */
foreach($BASES_path as $basedir)
{
	if ($file = $basedir. DS. 'configs'. DS. 'hooks.php' and file_exists($file))
	{
		require_once $file;
	}
}

/**
 * Marcando el punto de proceso `functions_loaded`
 * @see mark()
 *
 * @internal
 */
mark('functions_loaded');

/**
 * Prioridad de WWW y HTTPS
 */
redirect_default_www ();

redirect_default_protocol ();

/**
 * Inicializar el APP
 * Permite inicializar la clase APP y todo las configuraciones
 *
 * @internal
 */
APP() -> init();

/**
 * Inicializar Router
 */
RTR()->init();

/**
 * Imagen Service Slug
 */
check_image_slug ();

/**
 * INICIANDO EL APP
 * El app permite cambiar configuraciones o agregar cambios antes de  procesar el REQUEST para emitir un RESPONSE.
 * 
 * @see APP.php
 */
if ( $file = APPPATH . DS . 'APP.php' and file_exists($file))
{
	require_once $file;
}

/**
 * APP\run()
 *
 * Función que procesa el request y emite un response
 */
APP()->run();