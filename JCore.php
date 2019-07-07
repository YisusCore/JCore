<?php
/**
 * JCore.php
 * El núcleo inicializa el aplicativo
 *
 * @filesource
 */

/**
 * VARIABLE JCore
 *
 * Variable global que permite almacenar valores y datos de manera global 
 * sin necesidad de almacenarlo en una sesión u otra variable posiblemente 
 * no existente
 *
 * @global
 */
isset($JC) or
	$JC = [];

/** 
 * Corrigiendo directorio base cuando se ejecuta como comando
 */
defined('STDIN') and 
	chdir(APPPATH);

/**
 * Definiendo Variables
 */
{

/**
 * Excute Time Start
 *
 * Indicate the exactly time what was loaded this file
 * Used for BranchTimer
 */
$JC['ETS'] = microtime(TRUE);

/**
 * Excute Memory Start
 *
 * Indicate the exactly memory what was loaded this file
  */
$JC['EMS'] = memory_get_usage(TRUE);

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
 * DIRECTORIO DEL SITIO
 *
 * Directorio Raiz de donde es accedido al sitio
 *
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @global
 */
defined('HOMEPATH') or 
	exit('<br /><b>Fatal Error:</b> La variable HOMEPATH no está definida.');

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
defined('SUBPATH') or 
	define('SUBPATH', DS);

/**
 * DIRECTORIO ABSOLUTO DEL SITIO
 *
 * Equivalente a <i>HOMEPATH</i>&nbsp;<b>.</b>&nbsp;<i>SUBPATH</i>
 *
 * @global
 */
defined('ABSPATH') or 
	define('ABSPATH', realpath(HOMEPATH . SUBPATH));

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
defined('APPPATH') or 
	define('APPPATH',  ABSPATH);

/**
 * APPNMSP
 *
 * Un identificador sencillo de la aplicación que utiliza el núcleo JCore
 */
defined('APPNMSP') or 
	define('APPNMSP', 'Another JCore App');

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
defined('ENVIRONMENT') or 
	define('ENVIRONMENT', 'pruebas');

}

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

