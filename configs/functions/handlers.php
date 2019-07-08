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

		if ($_error)
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

