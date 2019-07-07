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

