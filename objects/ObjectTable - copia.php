<?php
class ObjectTable extends ArrayObject implements Countable, JsonSerializable, Serializable
{
	public function lista ($filtros = [], $return_objects = TRUE, $_where = NULL, $_select = NULL, $_qfrom = NULL, $_opts = [])
	{
		extract($_opts);
		
		$_from = $this->tbl;
		$_as = mb_strtolower(substr($_from, 0, 1));
		
		$query = '';

		$query.= 'SELECT ' . $_as . '.*';
		
		$columns = array_keys($this->tbl_structure['columns']);
		$_as2s = [];
		
		$last_refs_tbls = [];
		foreach($this->tbl_structure['key_column_usage'] as &$tbl)
		{
			$attr = $tbl['TABLE_NAME'];
			$attr = preg_replace('/^'.$this->tbl_structure['tblname_singular2'].'(e)?(s)?\_/', '', $attr);
			if (in_array($attr, $columns))
			{
				$attr .= '_tbl';
			}
			
			$n = '';
			while(in_array($attr.$n, $last_refs_tbls))
			{
				$n === '' and $n = 0;
				$n++;
			}
			$attr .= $n;
			$last_refs_tbls[] = $attr;
			
			$tbl['attr'] = $attr;
			
			$_from2 = esc($tbl['TABLE_NAME']);
			
			$_as2 = explode(' ', strtr($_from2, '-_', '  '));
			$_as2 = array_map(function($_as2_){return mb_strtolower(substr($_as2_, 0, 1));}, $_as2);
			$_as2 = implode('', $_as2);
			
			if (in_array($_as2, $_as2s))
			{
				$_as2 .= '2';
			}
			
			$_ts = $tbl['ts'] = sql_ts($tbl['TABLE_NAME'], $tbl['TABLE_SCHEMA']);
			$_columns = array_keys($_ts['columns']);
			
			$_columns = array_combine($_columns, $_columns);
			
			$_visible = isset($_columns['visible']);
			$_eliminado = isset($_columns['eliminado']);
			
			foreach(['creado', 'actualizado', 'eliminado'] as $_f)
			{
				if (isset($_columns[$_f]))
				{
					unset($_columns[$_f]);
				}
			}
			
			unset($_columns[$tbl['COLUMN_NAME']]);
			
			if (count($_columns) === 0)
			{
				continue;
			}
			
			$query.= ', (';
			
			$query.= 'SELECT GROUP_CONCAT(JSON_OBJECT(';
			$query.= implode(', ', array_map(function($c){
				return qp_esc($c) . ', `' . $c . '`';
			}, $_columns));
			
			$_select_ = '_select_' . $attr;
			isset($$_select_) and ! is_null($$_select_) and $query .= ' ' . $$_select_;
			
			$query.= ') SEPARATOR "|") ';
			$query.= 'FROM `'.esc($tbl['TABLE_SCHEMA']).'`.`'.esc($tbl['TABLE_NAME']).'` AS '.$_as2  .' ';
			
			$_from_ = '_from_' . $attr;
			isset($$_from_) and ! is_null($$_from_) and $query .= ' ' . $$_from_;
			
			$query.= 'WHERE `'.esc($tbl['COLUMN_NAME']).'` = ' . $_as . '.`' . $tbl['REFERENCED_COLUMN_NAME'] . '`';
			
			$_eliminado and $query.= ' AND `eliminado` = FALSE';
			$_visible and $query.= ' AND `visible` = TRUE';
			
			$_where_ = '_where_' . $attr;
			isset($$_where_) and ! is_null($$_where_) and $query .= ' ' . $$_where_;
			
			$query.= ') AS ' . $attr;
			
			unset($tbl);
		}
		
		is_null($_select) or $query .= ' ' . $_select;

		$query.= ' ';
		
		$query.= 'FROM `' . $_from . '` AS ' . $_as			.' ';
		
		is_null($_qfrom) or $query .= ' ' . $_qfrom;

		$query.= 'WHERE TRUE'								.' ';
		
		in_array('visible', $columns) and ! isset($filtros['visible']) and $filtros['visible'] = TRUE;
		in_array('eliminado', $columns) and ! isset($filtros['eliminado']) and $filtros['eliminado'] = FALSE;
		
		if (isset($filtros['orderby']))
		{
			$orderby = $filtros['orderby'];
			unset($filtros['orderby']);
			
			$orderby = (array)$orderby;
		}
		
		foreach($filtros as $campo => $valor)
		{
			if ( ! in_array($campo, $columns))
			{
				continue;
			}
			
			$clas = $this->tbl_structure['columns'][$campo]['clas'];
			
			$query .= ' AND ' . $_as . '.`'. esc($campo) .'`';
			
			if (is_array($valor))
			{
				if (in_array($clas, ['numeric', 'datetime']) AND count($valor) === 2)
				{
					$query .= ' BETWEEN ' . $valor[0] . ' AND ' . $valor[1] . '';
				}
				elseif ($clas === 'numeric' AND count($valor) === 3)
				{
					$query .= ' ' . $valor[1] . ' ' . $valor[0];
				}
				else
				{
					$query .= ' IN (' . implode(', ', array_map('qp_esc', $valor)) . ')';
				}
			}
			elseif (is_null($valor))
			{
				$query .= ' IS NULL';
			}
			elseif ($clas === 'datetime')
			{
				$query .= ' LIKE "' . esc($valor) . '%"';
			}
			else
			{
				$query .= ' = ' . qp_esc($valor);
			}
		}

		is_null($_where) or $query .= ' ' . $_where;
		
		isset($orderby) and 
		$query .= ' ORDER BY ' . implode(', ', array_map(function($o){
			$o = (array)$o;
			(isset($o[1]) and in_array($o[1], ['ASC', 'DESC'])) or $o[1] = 'DESC';
			return '`' . $o[0] . '` ' . $o[1];
		}, $orderby));
		
		$return = sql_data($query)
		->quitar_fields(['creado', 'actualizado', 'eliminado']);

		if ($return->count() === 0)
		{
			return $return;
		}
		
		foreach($return as &$reg)
		{
			foreach($this->tbl_structure['key_column_usage'] as $tbl)
			{
				$attr = $tbl['attr'];
				$ts   = $tbl['ts'];

				if (is_null($reg[$attr]))
				{
					$reg[$attr] = [];
				}
				else
				{
					$reg[$attr] = explode('|', $reg[$attr]);
					foreach($reg[$attr] as &$reg2)
					{
						$reg2 = json_decode($reg2, true);
					}
				}
			}
			
			if ($return_objects)
			{
				$obj = new $this->_class;
				$obj->data = $reg;
				$reg = $obj;
			}
		}
		
		return $return;
	}
	
	protected $data = [];

	private $_class = NULL;
	protected $tbl = NULL;
	protected $tbl_structure = NULL;

	protected $_finded = FALSE;
	protected $_ID_field = NULL;

	public function __construct ($tabla, ...$keys)
	{	
		$this->_class = get_called_class();

		$this->tbl = $tabla;
		$this->tbl_structure = sql_ts($tabla);

		$this->default_columns();

		if (count($keys) === 1 and is_empty($keys[0]))
		{
			array_shift($keys);
		}

		if (count($this->tbl_structure['keys']) === 1 
			and $key = $this->tbl_structure['keys'][0]
		    and $field = $this->tbl_structure['columns'][$key]
		    and $field['Extra'] === 'auto_increment')
		{
			$this->_ID_field = $key;
		}

		if (count($keys) > 0)
		{
			foreach($keys as $ind => $_val)
			{
				$_key = $this->tbl_structure['keys'][$ind];
				$this->data[$_key] = $_val;
			}
			
			$this->select();
		}
		
		parent::__construct($this->data);
	}
	
	private function default_columns ($clean = TRUE)
	{
		$columns = array_keys($this->tbl_structure['columns']);
		$hiddens = $this->tbl_structure['hiddens'];
		$protecteds = $this->tbl_structure['protecteds'];
		$referenceds = $this->tbl_structure['referenceds'];

		if ($clean)
		{
			foreach ($this as $key => $val)
			{
				if ( ! in_array($key, $columns))
				{
					continue; ## No limpia otro campo
				}
				
				if (in_array($key, $hiddens) or in_array($key, $protecteds))
				{
					continue;
				}

				$this[$key] = NULL;
			}
		}

		## añade solo los PKs
		foreach($this->tbl_structure['keys'] as $_key)
		{
			if (isset($this->data[$_key]) and  ! is_empty($this->data[$_key]))
			{
				continue;
			}
			
			$clas = $this->tbl_structure['columns'][$_key]['clas'];
			$deff = $this->tbl_structure['columns'][$_key]['Default'];
			$this[$_key] = is_empty($deff) ? ($clas === 'numeric' ? 0 : ($clas === 'array' ? [] : NULL)) : NULL;
		}

		## añade todos los Campos
		foreach($columns as $_key)
		{
			if (in_array($_key, $hiddens) or in_array($_key, $protecteds))
			{
				continue;
			}
			
			if (isset($this->data[$_key]) and ! is_empty($this->data[$_key]))
			{
				$this[$_key] = $this->data[$_key];
				continue;
			}
			
			$clas = $this->tbl_structure['columns'][$_key]['clas'];
			$deff = $this->tbl_structure['columns'][$_key]['Default'];
			
			if (in_array($_key, $referenceds))
			{
				$this[$_key] = NULL;
				continue;
			}
			
			$this[$_key] = is_empty($deff) ? ($clas === 'numeric' ? 0 : ($clas === 'array' ? [] : NULL)) : NULL;
		}

		$last_refs_tbls = [];
		foreach($this->tbl_structure['key_column_usage'] as $tbl)
		{
			$attr = $tbl['TABLE_NAME'];
			$attr = preg_replace('/^'.$this->tbl_structure['tblname_singular2'].'(e)?(s)?\_/', '', $attr);
			
			if (in_array($attr, $columns))
			{
				$attr .= '_tbl';
			}
			
			$n = '';
			while(in_array($attr.$n, $last_refs_tbls))
			{
				$n === '' and $n = 0;
				$n++;
			}
			$attr .= $n;
			$last_refs_tbls[] = $attr;
			
			if (isset($this->data[$attr]) and ! is_empty($this->data[$attr]))
			{
				$this[$attr] = $this->data[$attr];
				continue;
			}
			
			$this[$attr] = [];
		}
		
		return $this;
	}

	public function select()
	{
		$where = '';
		foreach($this->tbl_structure['keys'] as $_key)
		{
			$field_dats = $this->tbl_structure['columns'][$_key];
			
			if (is_null($this->data[$_key]) and $field_dats['Null'] !== 'NO')
			{
				$where .= ' AND `'.$_key.'` IS NULL';
				continue;
			}
			
			$where .= ' AND `'.$_key.'` = ' . qp_esc($this->data[$_key]);
		}
		
		if (in_array('eliminado', array_keys($this->tbl_structure['columns'])))
		{
			$where.= ' AND `eliminado` = FALSE';
		}

		if (in_array('visible', array_keys($this->tbl_structure['columns'])))
		{
			$visible = func_num_args() === 0 ? NULL : func_get_arg(0);
			is_null($visible) or $where.= ' AND `visible` = ' . qp_esc($visible);
		}

		$temp = sql_data('SELECT * FROM `'.$this->tbl.'` WHERE TRUE' . $where, TRUE);
		
		if (is_null($temp) or count($temp) === 0)
		{
			$this->default_columns();
			return;
		}

		$this->_finded = TRUE;
		$this->data = (array)$temp;
		
		$columns = array_keys($this->tbl_structure['columns']);
		$last_refs_tbls = [];
		foreach($this->tbl_structure['key_column_usage'] as $tbl)
		{
			$attr = $tbl['TABLE_NAME'];
			$attr = preg_replace('/^'.$this->tbl_structure['tblname_singular2'].'(e)?(s)?\_/', '', $attr);
			
			if (in_array($attr, $columns))
			{
				$attr .= '_tbl';
			}
			$n = '';
			while(in_array($attr.$n, $last_refs_tbls))
			{
				$n === '' and $n = 0;
				$n++;
			}
			$attr .= $n;
			$last_refs_tbls[] = $attr;
			
			$this->data[$attr] = (array)sql_data('
			SELECT * 
			FROM `'.esc($tbl['TABLE_SCHEMA']).'`.`'.esc($tbl['TABLE_NAME']).'` 
			WHERE `'.esc($tbl['COLUMN_NAME']).'` = ' . qp_esc($this->data[$this->tbl_structure['keys'][0]]))
				->quitar_fields($tbl['COLUMN_NAME']);
		}
		
		
		$this->default_columns(FALSE);
		return $this;
	}

	public function insert()
	{
		$requireds = $this->tbl_structure['requireds'];
		$protecteds = $this->tbl_structure['protecteds'];
		
		## Validar que los requeridos no esten vacíos
		$faltantes = [];
		foreach($requireds as $_field)
		{
			if ( ! isset($this->data[$_field]) or is_empty($this->data[$_field]))
			{
				$faltantes[] = $_field;
			}
		}
		if (count($faltantes) > 0)
		{
			throw new \BasicException(grouping($faltantes, [
				'prefix' => ['El campo ', 'Los campos '],
				'suffix' => [' es requerido', ' son requeridos'],
			]));
		}
		
		$columns = array_keys($this->tbl_structure['columns']);
		$columns = array_combine($columns, $columns);
		
		foreach($protecteds as $_column)
		{
			unset($columns[$_column]);
		}
		
		if ( ! is_null($this->_ID_field))
		{
			unset($columns[$this->_ID_field]);
		}
		
		$columns = array_values($columns);

		## Ejecutar la consulta
		$query = 'INSERT INTO `' . $this->tbl . '` (' . 
			
			implode(', ', array_map(function($field){
				return '`' . $field . '`';
			}, $columns)). 
			
			') VALUES (' . 
			
			implode(', ', array_map(function($field) use ($requireds){
				$field_dats = $this->tbl_structure['columns'][$field];
				
				$data =& $this->data;
				isset($data[$field]) or $data[$field] = NULL;
				
				if (in_array($field, $requireds))
				{
					return qp_esc($data[$field]);
				}
				elseif ($field_dats['Null'] === 'NO' or $field_dats['Extra'] === 'auto_increment')
				{
					return qp_esc($data[$field], 'DEFAULT');
				}
				
				return qp_esc($data[$field], TRUE);
			}, $columns)). 
			
			')';

		if ( ! is_null($this->_ID_field))
		{
			$new_id = sql($query, TRUE);
			
			if ($new_id === FALSE)
			{
				throw new \BasicException('No se pudo ingresar el registro ' . $this->tbl_structure['tblname_singular'], 0, ['query' => $query]);
			}
			
			$this->data[$this->_ID_field] = $new_id;
		}
		else
		{
			$exec = sql($query);
			
			if ( ! $exec)
			{
				throw new \BasicException('No se pudo ingresar el registro ' . $this->tbl_structure['tblname_singular'], 0, ['query' => $query]);
			}
		}
		
		$this->select();
		return TRUE;
	}

	public function update()
	{
		$requireds = $this->tbl_structure['requireds'];
		$protecteds = $this->tbl_structure['protecteds'];
		$keys = $this->tbl_structure['keys'];
		
		## Validar que los requeridos no esten vacíos
		$faltantes = [];
		foreach($requireds as $_field)
		{
			if ( ! isset($this->data[$_field]) or is_empty($this->data[$_field]))
			{
				$faltantes[] = $_field;
			}
		}
		if (count($faltantes) > 0)
		{
			throw new \BasicException(grouping($faltantes, [
				'prefix' => ['El campo ', 'Los campos '],
				'suffix' => [' es requerido', ' son requeridos'],
			]));
		}
		
		$columns = array_keys($this->tbl_structure['columns']);
		$columns = array_combine($columns, $columns);
		
		foreach($protecteds as $_column)
		{
			unset($columns[$_column]);
		}
		
		foreach($keys as $_column)
		{
			unset($columns[$_column]);
		}
		$columns = array_values($columns);
		
		## Ejecutar la consulta
		$query = 'UPDATE `' . $this->tbl . '` SET ' . 
			
			implode(', ', array_map(function($field) use ($requireds){
				$field_dats = $this->tbl_structure['columns'][$field];
				
				$campo = '`' . $field . '` = ';
				
				$data =& $this->data;
				isset($data[$field]) or $data[$field] = NULL;
				
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ($field_dats['Null'] === 'NO')
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				
				return $campo . qp_esc($data[$field], TRUE);
			}, $columns)). 
			
			' WHERE ' . 
			
			implode(' AND ', array_map(function($field) use ($requireds){
				$field_dats = $this->tbl_structure['columns'][$field];
				
				$campo = '`' . $field . '` = ';
				
				$data =& $this->data;
				isset($data[$field]) or $data[$field] = NULL;
				
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ($field_dats['Null'] === 'NO')
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				
				return $campo . qp_esc($data[$field], TRUE);
			}, $keys))
			;
		
		$exec = sql($query);
		
		if ( ! $exec)
		{
			throw new \BasicException('No se pudo actualizar el registro ' . $this->_class);
		}
		
		$this->select();
		return TRUE;
	}

	public function delete()
	{
		$requireds = $this->tbl_structure['requireds'];
		$protecteds = $this->tbl_structure['protecteds'];
		$keys = $this->tbl_structure['keys'];
		
		## Ejecutar la consulta
		$query = 'DELETE FROM `' . $this->tbl . '` ';
		
		$columns = array_keys($this->tbl_structure['columns']);
		
		if (in_array('eliminado', $columns))
		{
			$query = 'UPDATE `' . $this->tbl . '` SET `eliminado` = TRUE';
		}
		
		$query .= ' WHERE ' . 
			implode(' AND ', array_map(function($field) use ($requireds){
				$field_dats = $this->tbl_structure['columns'][$field];
				$campo = '`' . $field . '` = ';
				$data =& $this->data;
				isset($data[$field]) or $data[$field] = NULL;
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ($field_dats['Null'] === 'NO')
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				return $campo . qp_esc($data[$field], TRUE);
			}, $keys));
				;
		
		$exec = sql($query);
		
		if ( ! $exec)
		{
			throw new \BasicException('No se pudo actualizar el registro ' . $this->_class);
		}
		
		$this->select();
		return TRUE;
	}

	public function finded()
	{
		return $this->_finded;
	}

	public function found()
	{
		return $this->_finded;
	}

	public function toArray ():array
	{
		return $this->data;
	}

	public function __toArray()
	{
		return $this->data;
	}

	public function get_tbl_structure()
	{
		return $this->tbl_structure;
	}
	
	//=================================
	// Magic Functions (As Object)
	//=================================
	public function __call ($name, $args)
	{
		if (preg_match('#^set_#', $name))
		{
			$index = preg_replace('#^set_#', '', $name);
			return $this->__set($index, $args[0]);
		}

		if (preg_match('#^get_#', $name))
		{
			$index = preg_replace('#^get_#', '', $name);
			return $this->__get($index, $args[0]);
		}
	}

	public function __isset ($index)
	{
		$hiddens = $this->tbl_structure['hiddens'];
		$protecteds = $this->tbl_structure['protecteds'];

		if (in_array($index, $hiddens) or in_array($index, $protecteds))
		{
			return FALSE;
		}

		return isset($this->data[$index]);
	}

	public function &__get ($index)
	{
		$hiddens = $this->tbl_structure['hiddens'];
		if (in_array($index, $hiddens))
		{
			throw new \BasicException('No puede obtener el campo `'.$index.'`');
		}

		if ( ! isset($this->data[$index]))
		{
			$this->data[$index] = NULL;
		}

		return $this->data[$index];
	}

	public function __set ($index, $newval)
	{
		$hiddens = $this->tbl_structure['hiddens'];
		$protecteds = $this->tbl_structure['protecteds'];

		if (in_array($index, $hiddens) or in_array($index, $protecteds))
		{
			throw new \BasicException('No puede establecer el campo `'.$index.'`');
		}

		if (isset($this->tbl_structure['columns'][$index]) and $this->tbl_structure['columns'][$index]['tipo'] === 'DECIMAL' and is_string($newval))
		{
			$newval = strtonumber($newval);
		}
		
		$this->data[$index] = $newval;
		return $this;
	}

	public function __unset ($index)
	{
		$hiddens = $this->tbl_structure['hiddens'];
		$protecteds = $this->tbl_structure['protecteds'];

		if (in_array($index, $hiddens) or in_array($index, $protecteds))
		{
			throw new \BasicException('No puede eliminar el campo `'.$index.'`');
		}

		unset($this->data[$index]);
		return $this;
	}

	//=================================
	// Magic Functions (ArrayAccess)
	//=================================
	public function offsetExists ($index) : boolean
	{
		return $this->__isset($index);
	}

	public function &offsetGet ($index)
	{
		return $this->__get($index);
	}

	public function offsetSet ($index, $newval)
	{
		$this->__set($index, $newval);
		return $this;
	}

	public function offsetUnset ($index)
	{
		$this->__unset($index);
		return $this;
	}

	//=================================
	// Magic Functions (Debug)
	//=================================
	final public function __debugInfo ()
	{
		$array = [
			'_ID_field' => $this->_ID_field,
			'data' => $this->data,
			'tbl' => $this->tbl,
			'tbl_structure' => $this->tbl_structure,
		];
		
		return $array;
	}

	//=================================
	// Magic Functions (As Function)
	//=================================
    public function __invoke($name = NULL)
    {
        return is_null($name) ? $this->getArrayCopy() : $this->data[$name];
    }

	//=================================
	// Countable
	//=================================
	public function count ():int
	{
		return count($this->data);
	}

	//=================================
	// JsonSerializable
	//=================================
	public function jsonSerialize()
	{
		return $this->__toArray();
	}

	//=================================
	// Serializable
	//=================================
	public function serialize () : string
	{
		$array = [
			'_' => 2,
			'_ID_field' => $this->_ID_field,
			'data' => $this->data,
			'tbl' => $this->tbl,
			'tbl_structure' => $this->tbl_structure,
		];
		
		return serialize($array);
	}

	public function unserialize ($serialized)
	{
		$array = unserialize($serialized);

		isset($array['_']) or $array['_'] = 1;
		
		if ($array['_'] === 1)
		{
			$array['_ID_field'] = NULL;
			if (count($array['tbl_structure']['keys']) === 1 
				and $key = $array['tbl_structure']['keys'][0]
				and $field = $array['tbl_structure']['columns'][$key]
				and $field['Extra'] === 'auto_increment')
			{
				$array['_ID_field'] = $key;
			}
		}
		
		$this->_ID_field = $array['_ID_field'];
		$this->data = $array['data'];
		$this->tbl = $array['tbl'];
		$this->tbl_structure = $array['tbl_structure'];

		return $this;
	}
}



