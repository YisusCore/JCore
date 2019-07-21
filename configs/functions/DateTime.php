<?php
/**
 * DateTime.php
 * Archivo de funciones manipuladores de fecha, hora y zona horaria
 *
 * @filesource
 */

if ( ! function_exists('getUTC'))
{
	/**
	 * getUTC()
	 * Obtiene el UTC del timezone actual
	 *
	 * @return string
	 */
	function getUTC()
	{
		$dtz = new DateTimeZone(date_default_timezone_get());
		$dt  = new DateTime('now', $dtz);
		$offset = $dtz->getOffset($dt);

		return sprintf( "%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs( $offset / 3600 ), abs( $offset % 3600 ) );
	}
}