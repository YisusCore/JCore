<?php
/**
 * @handlers.php
 * Funciones para eventos de codigo
 *
 * @filesource
 */

if ( ! function_exists('_error_handler'))
{
	/**
	 * _error_handler()
	 * Función a ejecutar al producirse un error durante la aplicación
	 *
	 * @use logger
	 * @use is_cli
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param int
	 *
	 * @return	void
	 */
	function _error_handler($severity, $message, $filepath, $line)
	{
		// Se valida si es error o solo una alerta
		$_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

		if ($_error and ! is_cli())
		{
			// Ya que es un error, se retorna un status 500 Internal Server Error
			http_response_code(500);
		}

		if (($severity & error_reporting()) !== $severity)
		{
			// No se desea reportar
			return;
		}

		// Se envía los datos a una función especial llamada logger definida por el usuario
		function_exists('logger') and
		logger($message, 
			   $severity, 
			   $severity, 
			   [], 
			   $filepath, 
			   $line);

		if ($_error)
		{
			// Ya que es un error, finaliza el proceso
			exit(1);
		}
	}
}

if ( ! function_exists('_exception_handler'))
{
	/**
	 * _exception_handler()
	 * Función a ejecutar cuando se produzca una exception
	 *
	 * @use logger
	 * @use is_cli
	 *
	 * @param	Exception	$exception
	 *
	 * @return	void
	 */
	function _exception_handler($exception)
	{
		// Ya que es una exception, se retorna un status 500 Internal Server Error
		if ( ! is_cli())
		{
			http_response_code(500);
		}
		
		// Se envía los datos a una función especial llamada logger definida por el usuario
		function_exists('logger') and
		logger($exception);
		
		// Ya que es una exception, finaliza el proceso
		exit(1);
	}
}

if ( ! function_exists('_shutdown_handler'))
{
	/**
	 * _shutdown_handler()
	 * Función a ejecutar antes de finalizar el procesamiento de la aplicación
	 *
	 * @use _error_handler
	 * @use action_apply
	 *
	 * @return void
	 */
	function _shutdown_handler()
	{
		// Validar si se produjo la finalización por un error
		$last_error = error_get_last();
		
		if ( isset($last_error) &&
			($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING)))
		{
			_error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
		}
		
		if (function_exists('action_apply'))
		{
			// Ejecutando funciones programadas
			action_apply ('do_when_end');
			action_apply ('shutdown');
		}
		
		flush();
	}
}

