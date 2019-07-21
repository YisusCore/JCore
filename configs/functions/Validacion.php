<?php
/**
 * Validacion.php
 * Archivo de funciones para validación de información
 *
 * @filesource
 */

if ( ! function_exists('is_empty'))
{
	/**
	 * is_empty()
	 * Validar si $valor está vacío
	 *
	 * Si es ARRAY entonces valida que tenga algún elemento
	 * Si es BOOL entonces retorna FALSO ya que es un valor así sea FALSO
	 * 
	 * @param array|bool|string|null $v
	 * @return bool
	 */
	function is_empty($v):bool
	{
		$type = gettype($v);
		
		if ($type === 'NULL')
		{
			return TRUE;
		}
		elseif ($type === 'string')
		{
			if ($v === '0')
			{
				return FALSE;
			}
			
			return empty($v);
		}
		elseif ($type === 'array')
		{
			return count($v) === 0;
		}
		
		return FALSE;
	}
}

if ( ! function_exists('def_empty'))
{
	/**
	 * def_empty()
	 * Obtener un valor por defecto en caso se detecte que el primer valor se encuentra vacío
	 *
	 * @param mixed
	 * @param mixed
	 * @return mixed
	 */
	function def_empty($v, $def = NULL)
	{
		if ( ! is_empty($v))
		{
			return $v;
		}
		
		return $def;
	}
}

if ( ! function_exists('non_empty'))
{
	/**
	 * non_empty()
	 * Ejecutar una función si detecta que el valor no está vacío
	 *
	 * @param mixed
	 * @param callable
	 * @return mixed
	 */
	function non_empty($v, callable $callback)
	{
		if ( ! is_empty($v))
		{
			return $callback($v);
		}
		
		return $v;
	}
}