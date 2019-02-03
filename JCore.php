<?php
/**
 * JCore.php
 * 
 * El núcleo inicializa todas las funciones básicas y todas las configuraciones mínimas.
 *
 * @link		https://jcore.jys.pe/files/JCore.php
 * @version		1.0.0
 * @filesource
 */

/**
 * Excute Time Start
 *
 * Indicate the exactly time what was loaded this file
 * Used for BranchTimer
 */
$_ETS = microtime(TRUE);

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
 * Directorio Raiz de donde es accedido al sitio
 *
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @global
 */
defined('HOMEPATH') or exit('<br /><b>Fatal Error:</b> La variable HOMEPATH no está definida.');

/**
 * SUBDIRECTORIO DEL SITIO
 *
 * Subdirectorio donde se encuentra alojado los recursos del sitio 
 * <i><small>(Recomendado cuando se aloja multiples sitios o plataformas en un mismo hosting)</small></i>
 *
 * WARNING: No debe finalizar pero si empezar con DS (Directory Separator)
 *
 * @global
 */
defined('SUBPATH') or define('SUBPATH', DS);

/**
 * DIRECTORIO ABSOLUTO DEL SITIO
 *
 * Equivalente a <i>HOMEPATH</i>&nbsp;<b>.</b>&nbsp;<i>SUBPATH</i>
 *
 * @global
 */
defined('ABSPATH') or define('ABSPATH', realpath(HOMEPATH . SUBPATH));

/**
 * DIRECTORIO NÚCLEO JCORE
 *
 * Directorio de JCore PHP
 *
 * @internal
 */
define('ROOTPATH', __DIR__);

/**
 * DIRECTORIO PROCESOS DE APLICACIÓN
 *
 * Ruta a la carpeta que contiene los archivos para el APP
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
defined('ENVIRONMENT') or define('ENVIRONMENT', 'pruebas');

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
	break;
}

/**
 * APPNMSP
 *
 * Un identificador sencillo de la aplicación que utiliza el núcleo JCore
 */
defined('APPNMSP') or define('APPNMSP', 'Another JCore App');

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
 * VARIABLE $config
 *
 * @global
 */
isset($config) or $config = [];

/**
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or $BASES_path = [];
$BASES_path = (array)$BASES_path;

in_array(APPPATH, $BASES_path) or array_unshift($BASES_path, APPPATH);
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

wfile('core', 'functions');

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
wfile('hook', 'configs');

/**
 * Marcando el punto de proceso `functions_loaded`
 * @see mark()
 *
 * @internal
 */
mark('APP_prepared');

/**
 * Inicializar el APP
 * Permite inicializar la clase APP y todo las configuraciones
 *
 * @internal
 */
APP();

/**
 * INICIANDO EL APP
 * El app permite cambiar configuraciones o agregar cambios antes de  procesar el REQUEST para emitir un RESPONSE.
 * 
 * @see APP.php
 */
wfile('APP', '.', true);

/**
 * APP\run()
 *
 * Función que procesa el request y emite un response
 */
RQS()->valid();
RQS()->process();

RSP()->response();