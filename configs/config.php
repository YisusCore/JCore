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