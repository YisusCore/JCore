<?php
/**
 * ClassesCallers.php
 *
 * @filesource
 */

if ( ! function_exists('APP'))
{
	/**
	 * APP()
	 * Función que retorna la instancia de la clase APP
	 *
	 * @return App
	 */
	function APP ()
	{
		return APP::instance();
	}
}

if ( ! function_exists('RQS'))
{
	/**
	 * RQS()
	 * Función que retorna la instancia de la clase Request
	 *
	 * @return Request
	 */
	function RQS ()
	{
		return Request::instance();
	}
}

if ( ! function_exists('RSP'))
{
	/**
	 * RSP()
	 * Función que retorna la instancia de la clase Response
	 *
	 * @return Response
	 */
	function RSP ()
	{
		return Response::instance();
	}
}
