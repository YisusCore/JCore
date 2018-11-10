<?php
/**
 * mngr.bbdd.php
 * 
 * El archivo `mngr.bbdd` contiene todas las funciones de gestión de base datos
 *
 * Copyright (c) 2018 - 2023, JYS Perú
 *
 * Se otorga permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software 
 * y archivos de documentación asociados (el "Software"), para tratar el Software sin restricciones, 
 * incluidos, entre otros, los derechos de uso, copia, modificación y fusión. , publicar, distribuir, 
 * sublicenciar y / o vender copias del Software, y permitir a las personas a quienes se les 
 * proporciona el Software que lo hagan, sujeto a las siguientes condiciones:
 *
 * El aviso de copyright anterior y este aviso de permiso se incluirán en todas las copias o 
 * porciones sustanciales del software.
 *
 * EL SOFTWARE SE PROPORCIONA "TAL CUAL", SIN GARANTÍA DE NINGÚN TIPO, EXPRESA O IMPLÍCITA, INCLUIDAS,
 * ENTRE OTRAS, LAS GARANTÍAS DE COMERCIABILIDAD, IDONEIDAD PARA UN PROPÓSITO PARTICULAR Y NO INFRACCIÓN.
 * EN NINGÚN CASO LOS AUTORES O PROPIETARIOS DE DERECHOS DE AUTOR SERÁN RESPONSABLES DE CUALQUIER RECLAMO, 
 * DAÑO O CUALQUIER OTRO TIPO DE RESPONSABILIDAD, YA SEA EN UNA ACCIÓN CONTRACTUAL, AGRAVIO U OTRO, 
 * DERIVADOS, FUERA DEL USO DEL SOFTWARE O EL USO U OTRAS DISPOSICIONES DEL SOFTWARE.
 *
 * @package		JCore\Functions
 * @author		YisusCore
 * @link		https://jcore.jys.pe/functions/mngr.bbdd
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

defined('ABSPATH') or exit('Acceso directo al archivo no autorizado');

/**
 * DIRECTORY_SEPARATOR
 *
 * Separador de Directorios para el sistema operativo de ejecución
 *
 * @global
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

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
	function cbd($host = 'localhost', $usuario = 'root', $password = NULL, $base_datos = NULL, $charset = 'utf8')
	{
		global $CON, $CONs;
		
		$conection = mysqli_connect($host, $usuario, $password);
		
		if ( ! $conection)
		{
			throw new BasicException(mysqli_connect_error(), mysqli_connect_errno());
		}

		$conection->_host = $host;
		$conection->_usuario = $usuario;
		$conection->_password = $password;

		is_null($CON) and $CON = $conection;
		$CONs[] = $conection;
		
		if ( ! is_empty($base_datos) and ! mysqli_select_db($conection, $base_datos))
		{
			APP()->log(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SELECT DB');
		}
		
		$conection->_base_datos = $base_datos;

		if ( ! mysqli_set_charset($conection, $charset))
		{
			APP()->log(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET charset');
		}
		
		$conection->_charset = $charset;

		if ( ! mysqli_query($conection, 'SET time_zone = "' . getUTC() . '";'))
		{
			APP()->log(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET time_zone');
		}
		
		$conection->_utc = getUTC();

		if ( ! mysqli_query($conection, 'SET SESSION group_concat_max_len = 1000000;'))
		{
			APP()->log(mysqli_error($conection), mysqli_errno($conection), 'BBDD: SET group_concat_max_len');
		}

		return $conection;
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
		
		is_null($conection) and $conection = $CON;

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
		
		is_null($conection) and $conection = $CON;

		if ($or_null !== FALSE and is_empty($valor))
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
		
		is_null($conection) and $conection = $CON;
		
		$result =  mysqli_query($conection, $query);
		
		if ( ! $result)
		{
			$MYSQL_ERROR = mysqli_error($conection);
			$MYSQL_ERRNO = mysqli_errno($conection);
			
			APP()->log($MYSQL_ERROR, $MYSQL_ERRNO, 'BBDD: SQL Excecuted', ['query' => $query]);
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
			
			APP()->log($MYSQL_ERROR, $MYSQL_ERRNO, 'BBDD: SQL Excecuted', ['query' => $query]);
			
			$sql_data_result = sql_data::fromArray([])
				-> quitar_fields('log');
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
		
		is_null($conection) and $conection = $CON;
		
		isset($_trans[$conection->thread_id]) or $_trans[$conection->thread_id] = 0;
		
		if ($do === 'NUMTRANS')
		{
			return $_trans[$conection->thread_id];
		}
		
		isset($_auto_commit_setted[$conection->thread_id]) or $_auto_commit_setted[$conection->thread_id] = FALSE;
		
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
		
		is_null($conection) and $conection = $CON;
		
		return sql_data('SELECT PASSWORD('.qp_esc($valor).') as `valor`;', TRUE, 'valor', $conection);
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
		
		is_null($conection) and $conection = $CON;
		
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
		
		is_null($conection) and $conection = $CON;
		
		$result = mysqli_query($conection, 'SELECT `' . $campo . '` FROM `' . $tabla . '` LIMIT 0');
		
		return (bool) $result;
	}
}

if ( ! function_exists('sql_ts'))
{
	/**
	 * sql_ts()
	 * Obtiene la estructura de una tabla
	 *
	 * @param string
	 * @param mysqli
	 * @return array
	 */
	function sql_ts ($tabla, $TABLE_SCHEMA = NULL, mysqli $conection = NULL)
	{
		static $_structures = [];

		global $CON;

		is_null($conection) and $conection = $CON;
		is_null($TABLE_SCHEMA) and $TABLE_SCHEMA = $conection->_base_datos;

		isset($_structures[$conection->thread_id]) or $_structures[$conection->thread_id] = [];

		if (isset($_structures[$tabla]))
		{
			return $_structures[$tabla];
		}

		$_structures[$conection->thread_id][$tabla] = [
			'tblname' => $tabla,
			'keys' => [],
			'requireds' => [],
			'protecteds' => [],
			'referenceds' => [],
			'hiddens' => [],
			'columns' => [],
			'key_column_usage' => [],
			'key_column_usage_referenced' => [],
			'tblname_singular' => $tabla,
			'tblname_singular2' => $tabla,
			'tblname_plural' => $tabla,
		];

		extract($_structures[$conection->thread_id][$tabla], EXTR_REFS);

		$columns = (array)sql_data('SHOW FULL COLUMNS FROM `' . $tabla . '`', FALSE, NULL, $conection);

		$key_column_usage = (array)sql_data('
SELECT TABLE_SCHEMA, TABLE_NAME, REFERENCED_COLUMN_NAME, COLUMN_NAME, CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_SCHEMA = '.qp_esc($TABLE_SCHEMA).' AND REFERENCED_TABLE_NAME = '.qp_esc($tabla).';', FALSE, NULL, $conection);

		$key_column_usage_referenced = (array)sql_data('
SELECT REFERENCED_TABLE_SCHEMA, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, COLUMN_NAME, CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = '.qp_esc($TABLE_SCHEMA).' AND TABLE_NAME = '.qp_esc($tabla).'
	AND CONSTRAINT_NAME <> "PRIMARY" AND REFERENCED_TABLE_SCHEMA IS NOT NULL AND REFERENCED_TABLE_NAME IS NOT NULL;', FALSE, NULL, $conection);

		foreach($columns as &$column)
		{
			if ($column['Type'] === 'longtext' and $column['Collation']==='utf8mb4_bin')
			{
				$column['Type'] = 'json';
				$column['Collation'] = 'utf8_general_ci';
				
				if (is_localhost())
				{
					trigger_error('Está usando la configuración de JSON en MariaDB para el campo `'.$column['Field'].'`', E_USER_WARNING);
				}
			}
			
			if ($column['Type'] === 'tinyint(1)')
			{
				$column['Type'] = 'bool';
			}
			
			if ($column['Type'] === 'bigint(20)')
			{
				$column['Type'] = 'bigint';
			}
			
			$column['tipo'] = NULL;
			$column['largo'] = NULL;
			
			$column['clas'] = NULL;
			$column['catg'] = NULL;
			$column['detl'] = NULL;
			
			$column['FieldName'] = ucwords(implode(' ', explode('_', $column['Field'])));
			mb_strlen($column['FieldName']) <= 3 and $column['FieldName'] = mb_strtoupper($column['FieldName']);
			
			extract($column, EXTR_REFS);

			if ($Key === 'PRI')
			{
				$keys[] = $Field;
			}

			if ($Null === 'NO' and is_empty($Default) and $Extra !== 'auto_increment')
			{
				$requireds[] = $Field;
			}

			if (in_array($Field, ['creado', 'actualizado']))
			{
				$protecteds[] = $Field;
			}

			if (in_array($Field, ['clave']))
			{
				$hiddens[] = $Field;
			}

			preg_match('/([a-zA-Z]+)(\((.*)\))?/', $Type, $matches, PREG_OFFSET_CAPTURE);
			
			$tipo = mb_strtoupper($matches[1][0]);
			if (isset($matches[3]))
			{
				$largo = $matches[3][0];
			}
			
			switch ($tipo)
			{
				case 'TINYINT':case 'SMALLINT':case 'MEDIUMINT':case 'INT':case 'BIGINT':
					$clas = 'numeric';
					$catg = 'integer';
					$detl = FALSE;
					break;
				case 'DECIMAL':
					$clas = 'numeric';
					$catg = 'double';
					$detl = TRUE;
					break;
				case 'FLOAT':case 'DOUBLE':case 'REAL':
					$clas = 'numeric';
					$catg = 'float';
					$detl = TRUE;
					break;
				case 'BIT':
					$clas = 'numeric';
					$catg = 'integer';
					$detl = FALSE;
					break;
				case 'YEAR':
					$clas = 'numeric';
					$catg = 'integer';
					$detl = FALSE;
					break;
				case 'DATE':case 'DATETIME':case 'TIME':case 'TIMESTAMP':
					$clas = 'datetime';
					break;
				case 'CHAR':case 'VARCHAR':
				case 'BINARY':case 'VARBINARY':
				case 'TINYBLOB':case 'BLOB':case 'MEDIUMBLOB':case 'LONGBLOB':
				case 'TINYTEXT':case 'TEXT':case 'MEDIUMTEXT':case 'LONGTEXT':
					$clas = 'string';
					break;
				case 'ENUM':case 'SET':
					$clas = 'string';
					$catg = array_map(function($i){
						return trim($i, '\'');
					}, explode(',', $largo));
					break;
				case 'BOOLEAN':case 'BOOL':
					$clas = 'boolean';
					break;
				case 'JSON':
					$clas = 'array';
					break;
			}
		}
		
		foreach($key_column_usage_referenced as $reference)
		{
			$referenceds[] = $reference['COLUMN_NAME'];
		}
		
		$columns = array_combine(array_map(function($col){
			return $col['Field'];
		}, $columns), $columns);

		$tblname_singular = preg_replace('/s$/i', '', $tblname_singular);
		$tblname_singular2 = preg_replace('/es$/i', '', $tblname_singular2);
		
		$tblname_singular === $tblname_plural and $tblname_plural .= 's';
		$tblname_singular2 === $tblname_plural and $tblname_plural .= 'es';
		
		return $_structures[$conection->thread_id][$tabla];
	}
}

/**
 * FINALIZADOR DE CONECCIONES
 * Función ejecutada cuando se finalice la conección
 */
action_add('do_when_end', function() use ($CONs){
	foreach($CONs as $conection)
	{
		dbd($conection);
	}
});
