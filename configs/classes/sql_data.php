<?php
class sql_data extends ArrayIterator
{
	/**
	 * fromArray()
	 * Obtiene una instancia creada desde un array
	 *
	 * @param array $data
	 * @param bool $arreglo Si TRUE entonces es un listado de objetos
	 */
	public static function fromArray ($data, $arreglo = TRUE)
	{
		$campos = [];
		$valores = [];
		
		if (count($data) === 0)
		{
			$campos['log'] = 'TEXT';
		}
		elseif ($arreglo)
		{
			foreach($data as $dts)
			{
				$row = [];
				foreach($dts as $campo => $valor)
				{
					if (is_array($valor))
					{
						$campos[$campo] = 'JSON';
					}
					elseif (is_integer($valor))
					{
						$campos[$campo] = 'BIGINT';
					}
					else
					{
						$campos[$campo] = 'TEXT';
					}
					
					$row[$campo] = $valor;
				}
				
				$valores[] = $row;
			}
		}
		else
		{
			$row = [];
			foreach($data as $campo => $valor)
			{
				if (is_array($valor))
				{
					$campos[$campo] = 'JSON';
				}
				elseif (is_integer($valor))
				{
					$campos[$campo] = 'BIGINT';
				}
				else
				{
					$campos[$campo] = 'TEXT';
				}
				
				$row[$campo] = $valor;
			}
			$valores[] = $row;
		}
		
		
		$table = cs(10);
		
		sql('
CREATE TEMPORARY TABLE `' . $table . '` 
(
	' . implode(', ', array_map(function($campo, $type){
			return '`' . $campo . '` ' . $type;
		}, array_keys($campos), array_values($campos))) . '
);');
		
		foreach($valores as $valor)
		{
			sql('INSERT INTO `' . $table . '` (' . implode(', ', array_map(function($campo){
				return '`' . $campo . '`';
			}, array_keys($campos))) . ') VALUES (' . implode(', ', array_map(function($v){
				return qp_esc($v, TRUE);
			}, array_values($valor))) . ')');
		}
		
		return sql_data('SELECT * FROM `' . $table . '`');
	}
	
	const version = '1.0';
	
	protected $is_array = TRUE;
	protected $data = [];
	protected $data_result = [];
	protected $campos = [];
	protected $fields = [];
	
	public function __construct($result = NULL)
	{
		if ( ! is_a($result, 'mysqli_result'))
		{
			is_array($result) or $result = [];
			parent::__construct($result);
			return;
		}

		while ($field = mysqli_fetch_field($result))
		{
			$campo = [];
			$this->fields[] =& $campo;
			
			$field_index = mysqli_field_tell($result);
			
			$campo['field_index'] = $field_index;
			
			$campo_num = '';
			while (in_array($field->name . $campo_num, $this->campos))
			{
				if ($campo_num === '')
				{
					$campo_num = 1;
				}
				else
				{
					$campo_num++;
				}
			}
			
			$campo_name = $field->name . $campo_num;
			
			$campo['field_index'] = $field_index;
			$campo['campo'] = $campo_name;
			
			$this->campos[] =& $campo['campo'];
			
			$campo['tblname'] = ucwords(implode(' ', explode('_', $campo['campo'])));
			mb_strlen($campo['tblname']) <= 3 and $campo['tblname'] = mb_strtoupper($campo['tblname']);
			
			$campo['name'] = $field->name; ## El nombre de la columna
			$campo['orgname'] = $field->orgname; ## El nombre original de la columna en caso que se haya especificado un alias
			$campo['table'] = $field->table; ## El nombre de la tabla al que este campo pertenece (si no es calculado)
			$campo['orgtable'] = $field->orgtable; ## El nombre original de la tabla en caso que se haya especificado un alias
			$campo['def'] = $field->def; ## Reservado para el valor por omisión, por ahora es siempre ""
			$campo['db'] = $field->db; ## Base de datos (desde PHP 5.3.6)
			$campo['catalog'] = $field->catalog; ## El nombre del catálogo, siempre "def" (desde PHP 5.3.6)
			$campo['max_length'] = $field->max_length; ## El largo máximo del campo en el resultset
			$campo['length'] = $field->length; ## El largo del campo, tal como se especifica en la definición de la tabla.
			$campo['charsetnr'] = $field->charsetnr; ## El número del juego de caracteres del campo.
			$campo['flags'] = $field->flags; ## Un entero que representa las banderas de bits del campo.
			$campo['type'] = $field->type; ## El tipo de datos que se usa en este campo
			$campo['decimals'] = $field->decimals; ## El número de decimales utilizado (para campos de tipo integer)
			
			$campo['flag_desc'] = $this->_flag_desc($campo['flags']);
			$campo['type_desc'] = $this->_type_desc($campo['type'], $campo['flag_desc'], $campo['length']);
			$campo['type_clas'] = $this->_type_clas($campo['type_desc'], $campo['flag_desc']);
			
			unset($campo);
		}

		$this->data = $this->data_result = mysqli_fetch_all($result, MYSQLI_NUM);

// 		global $MYSQL_QUERY;
// 		if ($MYSQL_QUERY === 'SELECT * FROM `empresa` WHERE 1'){define('DEBUG', TRUE);}
// 		defined('DEBUG') and DEBUG and die_array($MYSQL_QUERY, $this->campos, false);

		foreach($this->data_result as &$row)
		{
			foreach(array_keys($row) as $col)
			{
				if (is_null($row[$col]))
				{}
				elseif ($this->fields[$col]['type_desc'] === 'JSON')
				{
					$row[$col] = json_decode($row[$col], true);
				}
				elseif ($this->fields[$col]['type_clas'] === 'NUMERIC')
				{
					switch($this->fields[$col]['type_desc'])
					{
						case 'DECIMAL':case 'DOUBLE':
							$row[$col] = (double)$row[$col];
							break;
						case 'FLOAT':
							$row[$col] = (float)$row[$col];
							break;
						default:
							$row[$col] = $row[$col] *1;
							break;
					}
				}
				
				$row[$this->campos[$col]] = $row[$col];
				unset($row[$col]);
			}
		}

		unset($campo, $field_index, $campo_num, $campo_name, $field, $row);

		@mysqli_free_result($result); ## Libera Memoria

		parent::__construct($this->data_result);
	}
	
	public function getFields()
	{
		return $this->fields;
	}
	
	public function getCampos()
	{
		return $this->campos;
	}
	
	public function quitar_fields($fields)
	{
		$fields = (array)$fields;
		
		$_campos = $this->campos and $this->campos = [];
		$_fields = $this->fields and $this->fields = [];
		$_index_del = [];
		$_campos_del = [];
		
		$_index = 0;
		foreach($_campos as $index => $campo)
		{
			if (in_array($campo, $fields))
			{
				$_index_del[] = $index;
				$_campos_del[] = $campo;
				continue;
			}
			
			$_fields[$index]['field_index'] = ++$_index;
			
			$this->campos[] = $campo;
			$this->fields[] = $_fields[$index];
		}
		
		foreach($this->data as &$arr)
		{
			$arr_temp = $arr and $arr = [];
			
			foreach($arr_temp as $ind => $dts)
			{
				if (in_array($ind, $_index_del))
				{
					continue;
				}
				
				$arr[] = $dts;
			}
		}
		
		foreach($this->data_result as $index => &$arr)
		{
			$arr_temp = $arr and $arr = [];
			
			foreach($arr_temp as $campo => $dts)
			{
				if (in_array($campo, $_campos_del))
				{
					continue;
				}
				
				$arr[$campo] = $dts;
			}
			
			$this[$index] = $arr;
		}
		
		return $this;
	}
	
	public function filter_fields($fields)
	{
		if ( ! is_array($fields))
		{
			$process = $this->is_array;
			$this->is_array = FALSE;
		}
		
		$fields = (array)$fields;
		
		$_campos_del = [];
		foreach($this->campos as $index => $campo)
		{
			if ( ! in_array($campo, $fields))
			{
				$_campos_del[] = $campo;
				continue;
			}
		}
		
		defined('debug') and debug and ! defined('debug2') and define('debug2', true);
		
		$this->quitar_fields($_campos_del);
		
		if ( ! $this->is_array and $process)
		{
			foreach($this as $index => &$arr)
			{
				if ( ! is_array($arr) or count($arr) === 0)
				{
					$arr = NULL;
					$this->data[$index] = NULL;
					$this->data_result[$index] = NULL;
					continue;
				}
				
				$arr = array_shift($arr);
				$this->data[$index] = array_shift($this->data[$index]);
				$this->data_result[$index] = array_shift($this->data_result[$index]);
			}
		}
		
		return $this;
	}
	
	public function count()
	{
		return count($this->data);
	}
	
	public function first()
	{
		if (count($this->data_result) === 0)
		{
			if ( ! $this->is_array)
			{
				return NULL;
			}
			
			return [];
		}
		
		return $this->data_result[0];
	}
	
	protected function _type_desc($num, $flags = [], $length = 0)
	{
		static $types;

		if ( ! isset($types))
		{
			$types = array();
			$constants = get_defined_constants(true)['mysqli'];
			foreach ($constants as $c => $n)
			{
				if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m))
				{
					$types[$n] = $m[1];
				}
			}
		}

		if ($length === 4294967295 and $num === 252 and in_array('BINARY', $flags))
		{
			## JSON en MariaDB
			return 'JSON';
		}
		
		return array_key_exists($num, $types)? $types[$num] : NULL;
	}
	
	protected function _type_clas($tipo, $flags = [])
	{
		switch ($tipo)
		{
			case 'CHAR':
				if (in_array('NUM', $flags))
				{
					return 'NUMERIC';
				}
				
				return 'STRING';
				break;
				
			case 'DECIMAL':case 'SHORT':case 'LONG':case 'FLOAT':case 'DOUBLE':case 'LONGLONG':case 'INT24':case 'YEAR':case 'NEWDECIMAL':
				if (in_array('ZEROFILL', $flags))
				{
					return 'STRING';
				}
				
				return 'NUMERIC';
				break;
				
			case 'INTERVAL':case 'TINY_BLOB':case 'MEDIUM_BLOB':case 'LONG_BLOB':case 'BLOB':case 'VAR_STRING':case 'STRING':
				return 'STRING';
				break;
				
			case 'JSON':case 'SET':
				return 'ARRAY';
				break;
				
			case 'TIMESTAMP':case 'DATE':case 'TIME':case 'DATETIME':case 'NEWDATE':
				return 'DATETIME';
				break;
		}
		
		return 'NULL';
	}
	
	protected function _flag_desc($num)
	{
		static $flags;

		if ( ! isset($flags))
		{
			$flags = array();
			$constants = get_defined_constants(true)['mysqli'];
			foreach ($constants as $c => $n)
			{
				if (preg_match('/MYSQLI_(.*)_FLAG$/', $c, $m))
				{
					if ( ! array_key_exists($n, $flags))
					{
						$flags[$n] = $m[1];
					}
				}
			}
		}

		$result = array();
		foreach ($flags as $n => $t)
		{
			if ($num & $n)
			{
				$result[] = $t;
			}
		}
		return $result;
	}
	
	//===================================================================
	// Magic Functions
	//===================================================================
	
	/**
	 * Retorna el nombre y la versión de la clase
	 *
	 * @return string
	 */
	public function __toString()
	{
		return get_class() . ' v' . self::version . ' by JYS Perú';
	}
	
	/**
	 * Permite retornar la data de mimes para su validación
	 *
	 * @return Array
	 */
	public function __debugInfo()
	{
		return [
			'class' => get_class(),
			'data' => $data,
			'data_result' => $data_result,
			'campos' => $campos,
			'fields' => $fields
		];
	}
	
	//===================================================================
	// Array Access
	//===================================================================
	
	/**
	 * Valida que la extensión exista en la data
	 *
	 * @param string
	 * @return bool
	 */
	public function offsetExists ($offset)
	{
		return isset($this->data_result[$offset]);
	}
	
	/**
	 * Obtiene la información de la extensión
	 *
	 * @param string
	 * @return Mixed
	 */
	public function offsetGet ($offset)
	{
		return $this->data_result[$offset];
	}
	
	/**
	 * Inserta o actualiza la data de un mime
	 *
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function offsetSet ($offset, $value)
	{
		$this->data_result[$offset] = $value;
	}
	
	/**
	 * Elimina la información de la extensión
	 *
	 * @param string
	 * @return void
	 */
	public function offsetUnset ($offset)
	{
		unset ($this->data_result[$offset]);
	}
	
	public function clone ()
	{
		return $this->__clone();
	}
	
	public function __clone ()
	{
		$return = new self ($this->data_result);
		
		$return->is_array = $this->is_array;
		$return->data = $this->data;
		$return->data_result = $this->data_result;
		$return->campos = $this->campos;
		$return->fields = $this->fields;
		
		return $return;
	}
	
	public function toArray ()
	{
		return $this->data_result;
	}
	
}