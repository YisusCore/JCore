<?php
/**
 * BaseDatos.php
 * Archivo de funciones para temas de Base Datos
 *
 * @filesource
 */

/**
 * Requerimientos: para usar el archivo de manera independiente
 */
## function logger () {}
## function getUTC () {}
## function config () {}
## function is_empty () {}
## function action_add () {}
## class sql_data {}

/**
 * $CON
 * Primera Conección Detectada
 *
 * @global
 */
$CON = NULL;

/**
 * $CONs
 * Todas las conecciones
 *
 * @global
 */
$CONs = [];

/**
 * $MYSQL_QUERY
 * QUERY de ejecución de Query
 *
 * @global
 */
$MYSQL_QUERY = NULL;

/**
 * $MYSQL_ERROR
 * Error detectado de ejecución de Query
 *
 * @global
 */
$MYSQL_ERROR = NULL;

/**
 * $MYSQL_ERRNO
 * Número de error detectado de ejecución de Query
 *
 * @global
 */
$MYSQL_ERRNO = NULL;

if ( ! function_exists('mysqli_fetch_all'))
{
	/**
	 * mysqli_fetch_all()
	 * Retorna toda la data de un `mysqli_result`
	 *
	 * @param mysqli_result
	 * @param int
	 * @return array
	 */
	function mysqli_fetch_all(mysqli_result $result, int $resulttype = MYSQLI_NUM)
	{
		$return = [];
		while($tmp = mysqli_fetch_array($result, $resulttype))
		{
			$return[] = $tmp;
		}
		return $return;
	}
}

if ( ! function_exists('dbd'))
{
	/**
	 * dbd()
	 * Cierra una conección de base datos
	 *
	 * @param mysqli
	 * @return bool
	 */
	function dbd(mysqli $conection)
	{
		return mysqli_close($conection);
	}
}

if ( ! function_exists('cbd'))
{
	/**
	 * cbd()
	 * Inicia una conección de base datos
	 *
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return bool
	 */
	function cbd($host = 'localhost', $user = 'root', $password = NULL, $dbname = NULL, $charset = 'utf8')
	{
		global $CON, $CONs;

		$conection = mysqli_connect($host, $user, $password);

		if ( ! $conection)
		{
			throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
		}

		$conection->_host = $host;
		$conection->_user = $user;
		$conection->_password = $password;

		is_null($CON) and 
		$CON = $conection;

		$CONs[] = $conection;

		if ( ! is_empty($dbname) and ! mysqli_select_db($conection, $dbname))
		{
			throw new Exception(mysqli_error($conection), mysqli_errno($conection));
		}

		$conection->_dbname = $dbname;

		if ( ! mysqli_set_charset($conection, $charset))
		{
			logger(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET charset');
		}
		
		$conection->_charset = $charset;

		$time_zone = getUTC();
		
		if ( ! mysqli_query($conection, 'SET time_zone = "' . $time_zone . '";'))
		{
			logger(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET time_zone');
		}
		
		$conection->_time_zone = $time_zone;

		if ( ! mysqli_query($conection, 'SET SESSION group_concat_max_len = 1000000;'))
		{
			logger(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET group_concat_max_len');
		}

		static $_action_end = TRUE;
		if ($_action_end)
		{
			/**
			 * FINALIZADOR DE CONECCIONES
			 * Función ejecutada cuando se finalice la conección
			 */
			action_add('do_when_end', function(){
				global $CONs;
				
				foreach($CONs as $conection)
				{
					dbd($conection);
				}
			});
			
			$_action_end = FALSE;
		}

		return $conection;
	}
}

if ( ! function_exists('sql_start'))
{
	/**
	 * sql_start()
	 * Ejecuta la función `mysqli_real_escape_string`
	 *
	 * @usedBy qp_esc
	 * @param string
	 * @param mysqli
	 * @return string
	 */
	function sql_start (){
		global $CON;
		
		if ( ! is_null($CON))
		{
			return $CON;
		}
		
		$db =& config('db');

		if ( ! is_empty($db))
		{
			isset($db['host']) or $db['host'] = 'localhost';
			isset($db['user']) or $db['user'] = 'root';
			isset($db['pasw']) or $db['pasw'] = NULL;

			return cbd($db['host'], $db['user'], $db['pasw'], $db['name']);
		}

		return NULL;
	}
}

if ( ! function_exists('esc'))
{
	/**
	 * esc()
	 * Ejecuta la función `mysqli_real_escape_string`
	 *
	 * @usedBy qp_esc
	 * @param string
	 * @param mysqli
	 * @return string
	 */
	function esc ($valor = '', mysqli $conection = NULL){
		global $CON;

		is_null($conection) and 
		$conection = $CON;

		return mysqli_real_escape_string($conection, $valor);
	}
}

if ( ! function_exists('qp_esc'))
{
	/**
	 * qp_esc()
	 * Retorna el parametro correcto para una consulta de base datos
	 *
	 * @since 1.0.3 $or_null puede ser otro valor como DEFAULT, si es TRUE entonces será NULL
	 * @since 1.0.2 Valida que el $valor si es numero no comience con 0
	 * @since 1.0.0
	 * @param string
	 * @param bool
	 * @param mysqli
	 * @return string
	 */
	function qp_esc ($valor = '', $or_null = FALSE, mysqli $conection = NULL){
		global $CON;

		static $_reserveds = [
			'NOW()',
			'DEFAULT'
		];

		is_null($conection) and 
		$conection = $CON;

		$is_empty = is_empty($valor);
		
		if ($or_null !== FALSE and $is_empty)
		{
			$or_null = ($or_null === TRUE ? 'NULL' : $or_null);
			return $or_null;
		}
		
		if (in_array($valor, $_reserveds))
		{
			return $valor; ## Palabras Reservadas
		}
		
		if (is_bool($valor))
		{
			return $valor ? 'TRUE' : 'FALSE';
		}
		
		if (is_numeric($valor) and ! preg_match('/^0/i', (string)$valor))
		{
			return esc($valor, $conection);
		}
		
		is_array($valor) and $valor = json_encode($valor);
		
		return '"' . esc($valor, $conection) . '"';
	}
}

if ( ! function_exists('sql'))
{
	/**
	 * sql()
	 * Ejecuta una consulta a la Base Datos
	 *
	 * @param string
	 * @param bool
	 * @param mysqli
	 * @return mixed
	 */
	function sql(string $query, $is_insert = FALSE, mysqli $conection = NULL)
	{
		global $CON, $MYSQL_QUERY, $MYSQL_ERROR, $MYSQL_ERRNO;
		
		$MYSQL_QUERY = $query;
		$MYSQL_ERROR = NULL;
		$MYSQL_ERRNO = NULL;

		is_null($conection) and 
		$conection = $CON;

		$result =  mysqli_query($conection, $query);
		
		if ( ! $result)
		{
			$MYSQL_ERROR = mysqli_error($conection);
			$MYSQL_ERRNO = mysqli_errno($conection);

			logger($MYSQL_ERROR, $MYSQL_ERRNO, 'BBDD: SQL Excecuted', ['query' => $query], FALSE);
			
			return FALSE;
		}
		
		if ($is_insert)
		{
			return mysqli_insert_id($conection);
		}
		
		return TRUE;
	}
}

if ( ! function_exists('sql_data'))
{
	/**
	 * sql_data()
	 * Ejecuta una consulta a la Base Datos
	 *
	 * @param string
	 * @param bool
	 * @param string|array|null
	 * @param mysqli
	 * @return mixed
	 */
	function sql_data(string $query, $return_first = FALSE, $fields = NULL, mysqli $conection = NULL)
	{
		global $CON, $MYSQL_QUERY, $MYSQL_ERROR, $MYSQL_ERRNO;

		$MYSQL_QUERY = $query;
		$MYSQL_ERROR = NULL;
		$MYSQL_ERRNO = NULL;

		static $_executeds = [];

		if (is_a($return_first, 'mysqli'))
		{
			is_null($conection) and $conection = $return_first;
			$return_first = FALSE;
		}

		if (is_a($fields, 'mysqli'))
		{
			is_null($conection) and $conection = $fields;
			$fields = NULL;
		}

		is_null($conection) and $conection = $CON;

		isset($_executeds[$conection->thread_id]) or $_executeds[$conection->thread_id] = 0;

		$_executeds[$conection->thread_id]++;

		if($_executeds[$conection->thread_id] > 1)
		{
			@mysqli_next_result($conection);
		}

		$result =  mysqli_query($conection, $query);

		if ( ! $result)
		{
			$MYSQL_ERROR = mysqli_error($conection);
			$MYSQL_ERRNO = mysqli_errno($conection);

			logger($MYSQL_ERROR, $MYSQL_ERRNO, 'BBDD: SQL Excecuted', ['query' => $query], FALSE);

			$sql_data_result = new sql_data();
		}
		else
		{
			$sql_data_result = new sql_data($result);
		}

		if ( ! is_null($fields))
		{
			$sql_data_result->filter_fields($fields);
		}

		if ($return_first)
		{
			return $sql_data_result->first();
		}

		return $sql_data_result;
	}
}

if ( ! function_exists('sql_trans'))
{
	/**
	 * sql_trans()
	 * Procesa transacciones de Base Datos
	 * 
	 * WARNING: Si se abre pero no se cierra no se guarda pero igual incrementa AUTOINCREMENT
	 * WARNING: Se deben cerrar exitosamente la misma cantidad de los que se abren
	 * WARNING: El primero que cierra con error cierra todos los transactions activos 
	 *          (serìa innecesario cerrar exitosamente las demas)
	 *
	 * @param bool|null
	 * @param mysqli
	 * @return bool
	 */
	function sql_trans($do = NULL, mysqli $conection = NULL)
	{
		global $CON;
		
		static $_trans = []; ## levels de transacciones abiertas
		static $_auto_commit_setted = [];
		
		if (is_a($do, 'mysqli'))
		{
			is_null($conection) and $conection = $do;
			$do = NULL;
		}
		
		is_null($conection) and 
		$conection = $CON;
		
		isset($_trans[$conection->thread_id]) or 
		$_trans[$conection->thread_id] = 0;
		
		if ($do === 'NUMTRANS')
		{
			return $_trans[$conection->thread_id];
		}
		
		isset($_auto_commit_setted[$conection->thread_id]) or 
		$_auto_commit_setted[$conection->thread_id] = FALSE;
		
		if (is_null($do))
		{
			## Se está iniciando una transacción
			
			## Solo si el level es 0 (aún no se ha abierto una transacción), se ejecuta el sql
			$_trans[$conection->thread_id] === 0 and sql('START TRANSACTION', FALSE, $conection);
			
			$_trans[$conection->thread_id]++; ## Incrmentar el level
			
			if ( ! $_auto_commit_setted[$conection->thread_id])
			{
				sql('SET autocommit = 0') AND $_auto_commit_setted[$conection->thread_id] = TRUE;
			}
			
			return TRUE;
		}
		
		if ($_trans[$conection->thread_id] === 0)
		{
			return FALSE; ## No se ha abierto una transacción
		}
		
		if ( ! is_bool($do))
		{
			trigger_error('Se está enviando un parametro ' . gettype($do) . ' en vez de un BOOLEAN', E_USER_WARNING);
			$do = (bool)$do;
		}
		
		if ($do)
		{
			$_trans[$conection->thread_id]--; ## Reducir el level
		
			## Solo si el level es 0 (ya se han cerrado todas las conecciones), se ejecuta el sql
			$_trans[$conection->thread_id] === 0 and sql('COMMIT', FALSE, $conection);
		}
		else
		{
			$_trans[$conection->thread_id] = 0; ## Finalizar todas los levels abiertos
			
			sql('ROLLBACK', FALSE, $conection);
		}
		
		if ($_auto_commit_setted[$conection->thread_id])
		{
			sql('SET autocommit = 1') AND $_auto_commit_setted[$conection->thread_id] = FALSE;
		}
		
		return TRUE;
	}
}

if ( ! function_exists('sql_pswd'))
{
	/**
	 * sql_pswd()
	 * Obtiene el password de un texto
	 *
	 * @param string
	 * @param mysqli
	 * @return bool
	 */
	function sql_pswd ($valor, mysqli $conection = NULL)
	{
		global $CON;
		
		is_null($conection) and 
		$conection = $CON;
		
		return sql_data('SELECT PASSWORD(' . qp_esc($valor) . ') as `valor`;', TRUE, 'valor', $conection);
	}
}

if ( ! function_exists('sql_et'))
{
	/**
	 * sql_et()
	 * Valida si existe una tabla
	 *
	 * @param string
	 * @param mysqli
	 * @return bool
	 */
	function sql_et ($tabla, mysqli $conection = NULL)
	{
		global $CON;
		
		is_null($conection) and 
		$conection = $CON;
		
		$result = mysqli_query($conection, 'SELECT * FROM `' . $tabla . '` LIMIT 0');
		
		return (bool) $result;
	}
}

if ( ! function_exists('sql_ect'))
{
	/**
	 * sql_ect()
	 * Valida si existe un campo de una tabla
	 *
	 * @param string
	 * @param mysqli
	 * @return bool
	 */
	function sql_ect ($campo, $tabla, mysqli $conection = NULL)
	{
		global $CON;
		
		is_null($conection) and 
		$conection = $CON;
		
		$result = mysqli_query($conection, 'SELECT `' . $campo . '` FROM `' . $tabla . '` LIMIT 0');
		
		return (bool) $result;
	}
}
