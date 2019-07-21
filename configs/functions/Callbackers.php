<?php
/**
 * Callbackers.php
 * Archivo de funciones callbacks
 *
 * @filesource
 */

/**
 * $JC_filters
 * Variable que almacena todas las funciones aplicables para los filtros
 * @internal
 */
$JC_filters = [];

/**
 * $JC_filters_defs
 * Variable que almacena todas las funciones aplicables para los filtros 
 * por defecto cuando no se hayan asignado alguno
 * @internal
 */
$JC_filters_defs = [];

/**
 * $JC_actions
 * Variable que almacena todas las funciones aplicables para los actions
 * @internal
 */
$JC_actions = [];

/**
 * $JC_actions_defs
 * Variable que almacena todas las funciones aplicables para los actions
 * por defecto cuando no se hayan asignado alguno
 * @internal
 */
$JC_actions_defs = [];

if ( ! function_exists('filter_add'))
{
	/**
	 * filter_add()
	 * Agrega funciones programadas para filtrar variables
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (Orden) a ejecutar la función cuando es llamado el Hook
	 * @return bool
	 */
	function filter_add ($key, $function, $priority = 50)
	{
		global $JC_filters;
		
		$lista =& $JC_filters;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('non_filtered'))
{
	/**
	 * non_filtered()
	 * Agrega funciones programadas para filtrar variables
 	 * por defecto cuando no se hayan asignado alguno
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (Orden) a ejecutar la función cuando es llamado el Hook
	 * @return bool
	 */
	function non_filtered ($key, $function, $priority = 50)
	{
		global $JC_filters_defs;
		
		$lista =& $JC_filters_defs;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('filter_apply'))
{
	/**
	 * filter_apply()
	 * Ejecuta funciones para validar o cambiar una variable
	 *
	 * @since 0.2 Se ha agregado las funciones por defecto cuando
	 * @since 0.1
	 *
	 * @param	string	$key	Hook
	 * @param	mixed	&...$params	Parametros a enviar en las funciones del Hook (Referenced)
	 * @return	mixed	$params[0] || NULL
	 */
	function filter_apply ($key, &...$params)
	{
		global $JC_filters;
		$lista =& $JC_filters;
		
		if (empty($key))
		{
			throw new Exception ('Hook es requerido');
		}
		
		count($params) === 0 and $params[0] = NULL;
		
		if ( ! isset($lista[$key]) OR count($lista[$key]) === 0)
		{
			global $JC_filters_defs;

			$lista_defs =& $JC_filters_defs;

			if ( ! isset($lista_defs[$key]) OR count($lista_defs[$key]) === 0)
			{
				return $params[0];
			}

			$functions = $lista_defs[$key];
		}
		else
		{
			$functions = $lista[$key];
		}
		
		krsort($functions);
		
		$params_0 = $params[0]; ## Valor a retornar
		foreach($functions as $priority => $funcs){
			foreach($funcs as $func){
				$return = call_user_func_array($func, $params);
				
				if ( ! is_null($return) and $params_0 === $params[0])
				{
					## El parametro 0 no ha cambiado por referencia 
					## y en cambio la función ha retornado un valor no NULO 
					## por lo tanto le asigna el valor retornado
					$params[0] = $return;
				}
				
				$params_0 = $params[0]; ## Valor a retornar
			}
		}
		
		return $params_0;
	}
}

if ( ! function_exists('action_add'))
{
	/**
	 * action_add()
	 * Agrega funciones programadas
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (orden) a ejecutar la función
	 * @return bool
	 */
	function action_add ($key, $function, $priority = 50)
	{
		global $JC_actions;
		
		$lista =& $JC_actions;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('non_actioned'))
{
	/**
	 * non_actioned()
	 * Agrega funciones programadas
 	 * por defecto cuando no se hayan asignado alguno
	 *
	 * @param string	$key		Hook
	 * @param callable	$function	Función a ejecutar
	 * @param int		$priority	Prioridad (orden) a ejecutar la función
	 * @return bool
	 */
	function non_actioned ($key, $function, $priority = 50)
	{
		global $JC_actions_defs;
		
		$lista =& $JC_actions_defs;
		
		if (empty($key))
		{
			return FALSE;
		}
		
		is_numeric($priority) OR $priority = 50;
		$priority = (int)$priority;
		
		$lista[$key][$priority][] = $function;
		return TRUE;
	}
}

if ( ! function_exists('action_apply'))
{
	/**
	 * action_apply()
	 * Ejecuta las funciones programadas
	 *
	 * @since 0.2.2 Se ha agregado las funciones por defecto cuando
	 * @since 0.2.1 Se ha cambiado el $RESULT por defecto de FALSE a NULL
	 * @since 0.1
	 *
	 * @param string	$key	Hook
	 * @param	mixed	&...$params	Parametros a enviar en las funciones del Hook (Referenced)
	 * @return bool
	 */
	function action_apply ($key, ...$params)
	{
		global $JC_actions;
		$lista =& $JC_actions;
		
		empty($key) and user_error('Hook es requerido');
		
		$RESULT = NULL;
		
		if ( ! isset($lista[$key]) OR count($lista[$key]) === 0)
		{
			global $JC_actions_defs;

			$lista_defs =& $JC_actions_defs;

			if ( ! isset($lista_defs[$key]) OR count($lista_defs[$key]) === 0)
			{
				return $RESULT;
			}

			$functions = $lista_defs[$key];
		}
		else
		{
			$functions = $lista[$key];
		}
		
		krsort($functions);
		
		foreach($functions as $priority => $funcs){
			foreach($funcs as $func){
				$RESULT = call_user_func_array($func, $params);
			}
		}
		
		return $RESULT;
	}
}