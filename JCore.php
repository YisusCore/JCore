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

