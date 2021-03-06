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

//	use BasicException;
	
	try
	{
		$Upload = new Upload ($_FILES['archivo']);
	}
	catch (BasicException $e)
	{
		RSP()
			-> error($e->getMessage())
			-> exit()
		;
	}
	
	RSP()
		-> success('Archivo cargado correctamente')
		-> addJson((array)$Upload)
		-> exit()
	;
};
