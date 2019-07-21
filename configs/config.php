<?php
/**
 * config.php
 * Archivo de las configuraciones básicas
 *
 * @filesource
 */

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
$config['lang'] = (function(){
	$_langs = isset ($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'EN';
	
	$_langs = explode(',', $_langs, 2);
	
	return mb_strtoupper(mb_substr($_langs[0], 0, 2));
})();

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