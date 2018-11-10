<?php
/**
 * init.php
 * 
 * Archivo que permite inicializar el sistema
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
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

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
defined('HOMEPATH') or define('HOMEPATH', __DIR__);

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
		if (version_compare(PHP_VERSION, '5.3', '>='))
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
		}
		else
		{
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
		}
	break;

	case 'desarrollo':
	default:
		ini_set('display_errors', 1);
		error_reporting(-1);
	break;
}

/**
 * VALIDACIÓN PHP VERSION, APACHE MODULS, PHP EXTENSIONS
 *
 * Si TRUE, entonces JCore validará la versión de PHP mínima
 * así como los módulos del apache y las extensiones de PHP
 * requeridas para que funcione correctamente el SISTEMA
 *
 * @global
 */
isset($server_validation) or $server_validation = FALSE;

/**
 * DIRECTORIO NÚCLEO JCORE
 *
 * La variable contiene la ruta a la carpeta del núcleo JCore.
 * WARNING: No debe finalizar en DS (Directory Separator)
 *
 * @internal
 */
isset($JCore_path) or $JCore_path = ABSPATH . DS . 'JCore' . DS . 'v3';

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
isset($APP_path) or $APP_path = ABSPATH . DS . 'APP';

/**
 * DIRECTORIOS BASES
 *
 * Array de los directorios base que buscará las estructuras de archivos
 *
 * @internal
 */
isset($BASES_path) or $BASES_path = [];

/** Agregando la carpeta APP en la lista de los directorios Base */
$BASES_path[] =& $APP_path;

/** Agregando la carpeta JCore en la lista de los directorios Base */
$BASES_path[] =& $JCore_path;

/** Corrigiendo directorio base cuando se ejecuta como comando */
defined('STDIN')and chdir(dirname(__FILE__));

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

/**
 * INICIANDO EL NÚCLEO
 *
 * El núcleo inicializa todas las funciones básicas y todas las configuraciones mínimas.
 * 
 * @see JCore.php
 */

if( $file = $JCore_path . DS . 'JCore.php' and ! file_exists($file))
{
	die('<br /><b>Fatal Error:</b> Archivo `'. basename($file, '.php') .'` not exists');
}

require_once $file;

/**
 * INICIANDO EL APP
 * El app permite cambiar configuraciones o agregar cambios antes de  procesar el REQUEST para emitir un RESPONSE.
 * 
 * @see APP.php
 */

if( $file = $APP_path . DS . 'APP.php' and file_exists($file))
{
	require_once $file;
}

/**
 * APP\run()
 *
 * Función que procesa el request y emite un response
 */
APP()->run();