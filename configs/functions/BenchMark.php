<?php
/**
 * BenchMark.php
 * Funciones BranchTimer
 *
 * @filesource
 */

if ( ! function_exists('mark'))
{
	/**
	 * mark()
	 * Función que utiliza la clase BenchMark
	 *
	 * @return void
	 */
	function mark($key)
	{
		BenchMark::instance()
		-> mark($key)
		;
	}
}