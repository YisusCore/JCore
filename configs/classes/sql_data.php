<?php
/**
 * sql_data.php
 * Archivo de clase sql_data
 *
 * @filesource
 */

class sql_data extends JArray
{
	/**
	 * campos
	 * (Array) Listado de todos los nombres de los campos
	 */
	protected $campos = [];

	/**
	 * fields
	 * (Array) Listado de todos los datos de los campos
	 */
	protected $fields = [];

	/**
	 * data
	 * (Array) Data (formato numérico)
	 */
	protected $data = [];

	/**
	 * doto
	 * (Array) Doto (formato letra)
	 */
	protected $doto;

	public function __construct ()
	{
		$this->doto = new ArrayObject();
		
		if (func_num_args() === 0)
		{
			return;
		}
		
		$_data = func_get_arg(0);
		
		if (is_array($_data))
		{
			$this->addArray($_data);
		}
		elseif (is_a($_data, 'mysqli_result'))
		{
			$this->addMysqliResult($_data);
		}
		
		parent::__construct($this->doto);
	}

	public function addArrayItem (array $array)
	{
		if (count($array) === 0)
		{
			return $this;
		}

		return $this->addArray([
			$array
		]);
	}

	public function addArray (array $array)
	{
		if (count($array) === 0)
		{
			return $this;
		}
		
		$campos = [];
		$campos_indexs = [];

		$array_tmp = $array[0];
		foreach (array_keys($array_tmp) as $field_index => $_key)
		{
			$campo = [];
			$campo['field_index'] = $field_index;
			
			$is_num = is_numeric($_key);

			if ($is_num)
			{
				$campo['name'] = $this->campos[$_key];
				$campos_indexs[$_key] = array_search($campo['name'], $this->campos);
				continue;
			}
			
			$campo_num = '';
			while (in_array($_key . $campo_num, $campos))
			{
				$campo_num === '' and
				$campo_num = 0;
				
				$campo_num++;
			}

			$campo['name'] = $_key . $campo_num;

			$campos[] =& $campo['name'];

			if (in_array($campo['name'], $this->campos))
			{
				$campos_indexs[$field_index] = array_search($campo['name'], $this->campos);
				continue;
			}

			$campo['index'] = count($this->fields);
			$campos_indexs[$field_index] = $campo['index'];

			$this->campos[] =& $campo['name'];
			$this->fields[] =& $campo;

			$campo['nombre'] = ucwords(implode(' ', explode('_', $campo['name'])));

			mb_strlen($campo['nombre']) <= 3 and 
			$campo['nombre'] = mb_strtoupper($campo['nombre']);

			$campo['orgname']    = $_key; ## El nombre original de la columna en caso que se haya especificado un alias
			$campo['table']      = ''; ## El nombre de la tabla al que este campo pertenece (si no es calculado)
			$campo['orgtable']   = ''; ## El nombre original de la tabla en caso que se haya especificado un alias
			$campo['def']        = ''; ## Reservado para el valor por omisión, por ahora es siempre ""
			$campo['db']         = ''; ## Base de datos (desde PHP 5.3.6)
			$campo['catalog']    = 'def'; ## El nombre del catálogo, siempre "def" (desde PHP 5.3.6)
			$campo['max_length'] = 24; ## El largo máximo del campo en el resultset
			$campo['length']     = 196605; ## El largo del campo, tal como se especifica en la definición de la tabla.
			$campo['charsetnr']  = 33; ## El número del juego de caracteres del campo.
			$campo['flags']      = 16; ## Un entero que representa las banderas de bits del campo.
			$campo['type']       = 252; ## El tipo de datos que se usa en este campo
			$campo['decimals']   = 0; ## El número de decimales utilizado (para campos de tipo integer)
			
			$campo['flag_desc'] = ['BLOB'];
			$campo['type_desc'] = 'BLOB';
			$campo['type_clas'] = 'STRING';
			
			unset($campo);
		}

		$data = $array;

		foreach($data as &$row)
		{
			$reg_N = [];
			$reg_L = [];

			$col_c = 0;
			foreach(array_values($row) as $col => $dta)
			{
				$col_r = $campos_indexs[$col];

				while($col_r > $col_c)
				{
					$field = $this->fields[$col_c];
					
					$reg_N[] = NULL;
					$reg_L[$field['name']] = NULL;

					$col_c++;
				}

				$field = $this->fields[$col_c];

				if (is_null($dta))
				{}
				elseif ($field['type_desc'] === 'JSON')
				{
					$dta = json_decode($dta, true);
				}
				elseif ($field['type_clas'] === 'NUMERIC')
				{
					switch($field['type_desc'])
					{
						case 'DECIMAL':case 'DOUBLE':
							$dta = (double)$dta;
							break;
						case 'FLOAT':
							$dta = (float)$dta;
							break;
						default:
							$dta = $dta *1;
							break;
					}
				}

				$reg_N[] = $dta;
				$reg_L[$field['name']] = $dta;

				$col_c = $col_r + 1;
			}

			$this->data[] = $reg_N;
			$this->doto[] = $reg_L;
		}

		return $this;
	}

	public function addMysqliResult (mysqli_result $result)
	{
		$campos = [];
		$campos_indexs = [];

		while ($field = mysqli_fetch_field($result))
		{
			$campo = [];
			$campo['field_index'] = mysqli_field_tell($result);

			$campo_num = '';
			while (in_array($field->name . $campo_num, $campos))
			{
				$campo_num === '' and
				$campo_num = 0;
				
				$campo_num++;
			}

			$campo['name'] = $field->name . $campo_num;

			$campos[] =& $campo['name'];

			if (in_array($campo['name'], $this->campos))
			{
				$campos_indexs[$campo['field_index']-1] = array_search($campo['name'], $this->campos);
				continue;
			}

			$campo['index'] = count($this->fields);
			$campos_indexs[$campo['field_index']-1] = $campo['index'];

			$this->campos[] =& $campo['name'];
			$this->fields[] =& $campo;

			$campo['nombre'] = ucwords(implode(' ', explode('_', $campo['name'])));

			mb_strlen($campo['nombre']) <= 3 and 
			$campo['nombre'] = mb_strtoupper($campo['nombre']);

			$campo['name']       = $field->name; ## El nombre de la columna
			$campo['orgname']    = $field->orgname; ## El nombre original de la columna en caso que se haya especificado un alias
			$campo['table']      = $field->table; ## El nombre de la tabla al que este campo pertenece (si no es calculado)
			$campo['orgtable']   = $field->orgtable; ## El nombre original de la tabla en caso que se haya especificado un alias
			$campo['def']        = $field->def; ## Reservado para el valor por omisión, por ahora es siempre ""
			$campo['db']         = $field->db; ## Base de datos (desde PHP 5.3.6)
			$campo['catalog']    = $field->catalog; ## El nombre del catálogo, siempre "def" (desde PHP 5.3.6)
			$campo['max_length'] = $field->max_length; ## El largo máximo del campo en el resultset
			$campo['length']     = $field->length; ## El largo del campo, tal como se especifica en la definición de la tabla.
			$campo['charsetnr']  = $field->charsetnr; ## El número del juego de caracteres del campo.
			$campo['flags']      = $field->flags; ## Un entero que representa las banderas de bits del campo.
			$campo['type']       = $field->type; ## El tipo de datos que se usa en este campo
			$campo['decimals']   = $field->decimals; ## El número de decimales utilizado (para campos de tipo integer)
			
			$campo['flag_desc'] = $this->_flag_desc($campo['flags']);
			$campo['type_desc'] = $this->_type_desc($campo['type'], $campo['flag_desc'], $campo['length']);
			$campo['type_clas'] = $this->_type_clas($campo['type_desc'], $campo['flag_desc']);
			
			unset($campo);
		}

		$data = mysqli_fetch_all($result, MYSQLI_NUM);

		foreach($data as &$row)
		{
			$reg_N = [];
			$reg_L = [];

			$col_c = 0;
			foreach($row as $col => $dta)
			{
				$col_r = $campos_indexs[$col];

				while($col_r > $col_c)
				{
					$field = $this->fields[$col_c];
					
					$reg_N[] = NULL;
					$reg_L[$field['name']] = NULL;

					$col_c++;
				}

				$field = $this->fields[$col_c];
				
				if (is_null($dta))
				{}
				elseif ($field['type_desc'] === 'JSON')
				{
					$dta = json_decode($dta, true);
				}
				elseif ($field['type_clas'] === 'NUMERIC')
				{
					switch($field['type_desc'])
					{
						case 'DECIMAL':case 'DOUBLE':
							$dta = (double)$dta;
							break;
						case 'FLOAT':
							$dta = (float)$dta;
							break;
						default:
							$dta = $dta *1;
							break;
					}
				}
				
				$reg_N[] = $dta;
				$reg_L[$field['name']] = $dta;

				$col_c = $col_r + 1;
			}
			
			$this->data[] = $reg_N;
			$this->doto[] = $reg_L;
		}

		@mysqli_free_result($result); ## Libera Memoria

		return $this;
	}

	public function getData ()
	{
		return $this->data;
	}

	public function getFields ()
	{
		return $this->fields;
	}

	public function getCampos ()
	{
		return $this->campos;
	}

	public function getArray ()
	{
		return (array)$this->doto;
	}

	public function toArray ()
	{
		return $this->getArray();
	}
	
	public function quitar_fields($fields)
	{
		
	}
	
	public function filter_fields($fields)
	{
		
	}

	protected function _flag_desc($num)
	{
		static $flags;

		if ( ! isset($flags))
		{
			$flags = [];
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

		$result = [];
		foreach ($flags as $n => $t)
		{
			if ($num & $n)
			{
				$result[] = $t;
			}
		}

		return $result;
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
}