<?php
class ObjectTable extends ArrayObject
{
    //========================================//
    // Atributos Estáticos                    //
    //========================================//

    /**
     * @static @var $_class
     */
    protected static $_class;

    /**
     * @static @var $tblname
     */
    protected static $tblname;

    /**
     * @static @var $tblname_singular
     */
    protected static $tblname_singular;

    /**
     * @static @var $tblname_plural
     */
    protected static $tblname_plural;

    /**
     * @static @var $columns
     */
    protected static $columns = [];

    /**
     * @static @var $keys
     */
    protected static $keys = [];

    /**
     * @static @var $key
     */
    protected static $key = NULL;

    /**
     * @static @var $protecteds
     */
    protected static $protecteds = [];

    /**
     * @static @var $requireds
     */
    protected static $requireds = [];

    /**
     * @static @var $referenceds
     */
    protected static $referenceds = [];

    /**
     * @static @var $hiddens
     */
    protected static $hiddens = [];

    /**
     * @static @var $key_column_usage
     */
    protected static $key_column_usage = [];

    /**
     * @static @var $key_column_usage_referenced
     */
    protected static $key_column_usage_referenced = [];

    /**
     * @static @var $Comment
     */
    protected static $Comment = '';

    //========================================//
    // Funciones Estáticas                    //
    //========================================//

    /**
     * ::tblname()
     * Obtiene un nombre de tabla
     *
     * @param String|NULL $as Default: Normal
     * @return [Usuario]
     */
    public static function tblname ($as = 'Normal')
    {
        switch($as)
        {
            case 'singular':
                return self::$tblname_singular;
                break;
            case 'plural':
                return self::$tblname_plural;
                break;
            default:
                return self::$tblname;
                break;
        }
    }
	
    //========================================//
    // Atributos de Objeto                    //
    //========================================//

	protected $_found = FALSE;
	
	protected $_data;
	
    //========================================//
    // Funciones de Objeto                    //
    //========================================//

    /**
     * Constructor
     */
    public function __construct (...$keys)
    {
		$this->_data = new JArray();
		
		parent::__construct($this->_data);
		
        $this->_repair_data();
		
		if (count($keys) === 0)
		{
			return $this;
		}
		
		do
		{
			$last = array_pop($keys);
		}
		while(is_null($last) and count($keys) > 0);
		
		is_null($last) or $keys[] = $last;
		
		if (count($keys) === 0)
		{
			return $this;
		}
		
		foreach($this::$keys as $ind => $index)
		{
			$this->_data[$index] = $keys[$ind];
		}
		
		$this->select();
    }

    /**
     * getData()
     * Obtiene un array con los campos requeridos
     *
     * @param Array $fields Campos requeridos
     * @return Array
     */
	public function getData ($fields = NULL)
	{
		if (is_null($fields))
		{
			$fields = array_keys($this::$columns);
			
			$fields = array_combine($fields, $fields);
			foreach($this::$hiddens as $index)
			{
				unset($fields[$index]);
			}
			$fields = array_values($fields);
		}
		
		$return = [];

		foreach((array)$fields as $field)
		{
			$function = [$this, 'get_' . $field] and
			is_callable($function) and 
			$return[$field] = call_user_func($function);
		}
		
		return (array)$return;
	}
	
    /**
     * lista()
     * Obtiene un listado de usuarios
     *
     * @param Array $filter Filtros de los campos
     * @param Int|NULL $limit Límite de Resultados, si NULL entonces no tiene Límite
     * @param String|Array|NULL $sortby Ordenado por el/los campos
     * @return JArray[$this]
     */
    public function lista ($filter = [], int $limit = NULL, $sortby = NULL, $getData = FALSE)
    {
		$columns = $this::$columns;
		$fields = array_keys($columns);

		isset($filter['visible']) or $filter['visible'] = NULL;
		isset($filter['eliminado']) or $filter['eliminado'] = FALSE;
		
		$_where = '';
		
		foreach($filter as $field => $val)
		{
			if ( ! in_array($field, $fields) 
				or in_array($field, ['visible', 'eliminado']) // tienen otro tipo de filtro
			   )
			{
				continue;
			}
			
			$field_dats = $columns[$field];
			$clas = $field_dats['clas'];
			
			$_where .= ' AND `'.$field.'`';
			
			if (is_array($val))
			{
				if ($clas === 'numeric' AND $val[0] === 'IN')
				{
					array_shift($val);
					$_where .= ' IN (' . implode(', ', array_map('qp_esc', $val)) . ')';
				}
				elseif (in_array($clas, ['numeric', 'datetime']) AND count($val) === 2)
				{
					$_where .= ' BETWEEN ' . $val[0] . ' AND ' . $val[1] . '';
				}
				elseif ($clas === 'numeric' AND count($val) === 3)
				{
					$_where .= ' ' . $val[1] . ' ' . $val[0];
				}
				else
				{
					$_where .= ' IN (' . implode(', ', array_map('qp_esc', $val)) . ')';
				}
			}
			elseif (is_null($val) and $field_dats['Null'])
			{
				$_where .= ' IS NULL';
			}
			elseif ($clas === 'datetime')
			{
				$_where .= ' LIKE "' . esc($val) . '%"';
			}
			else
			{
				$_where .= ' = ' . qp_esc($val);
			}
		}
		
		if (in_array('eliminado', $fields))
		{
			is_null($filter['eliminado']) or $_where .= ' AND `eliminado` = ' . qp_esc($filter['eliminado']);
		}

		if (in_array('visible', $fields))
		{
			is_null($filter['visible']) or $_where .= ' AND `visible` = ' . qp_esc($filter['visible']);
		}
		
		if (in_array('orden', $fields))
		{
			is_array($sortby) or $sortby = (array)$sortby;
			! isset($sortby[0]) and ! is_empty($sortby) and $sortby = [$sortby];
			
			$sortby[] = ['orden', 'ASC'];
		}
		
		if (in_array('creado', $fields))
		{
			is_array($sortby) or $sortby = (array)$sortby;
			! isset($sortby[0]) and ! is_empty($sortby) and $sortby = [$sortby];
			
			$sortby[] = ['creado', 'DESC'];
		}
		
		if ( ! is_null($sortby))
		{
			is_array($sortby) or $sortby = (array)$sortby;
			! isset($sortby[0]) and ! is_empty($sortby) and $sortby = [$sortby];
			
			$_where .= ' ORDER BY ' . implode(', ', array_map(function($o){
				$o = (array)$o;
				
				(isset($o[1]) and in_array($o[1], ['ASC', 'DESC'])) or $o[1] = 'DESC';
				
				return '`' . $o[0] . '` ' . $o[1];
			}, $sortby));
		}

		if ( ! is_null($limit))
		{
			$_where .= ' LIMIT ' . $limit;
		}

		$query = 'SELECT * FROM `'.$this::$tblname.'` WHERE TRUE' . $_where;
//		die_array($query);
		$data = (array)sql_data($query, FALSE);
		
		if (count ($data) === 0)
		{
			return new JArray($data);
		}
		
		foreach($data as &$reg)
		{
			$reg = $this -> fromArray($reg);
			
			if ($getData !== FALSE)
			{
				$reg = $reg -> getData($getData);
			}
		}
		
		return new JArray($data);
    }

    /**
     * select ()
     * Obtiene la DATA de la BBDD
     *
     * @param Boolean|NULL $visible Parametro solo funciona si el objeto cuenta con el atributo `visible`
     * return $this
     */
    public function select ($visible = NULL)
    {
		$keys = $this::$keys;
		$columns = $this::$columns;
		$fields = array_keys($columns);
		
		$_where = '';
		foreach($keys as $key)
		{
			$field_dats = $columns[$key];
			
			if (is_null($this->_data[$key]) and $field_dats['Null'])
			{
				$_where .= ' AND `'.$key.'` IS NULL';
				continue;
			}
			
			$_where .= ' AND `'.$key.'` = ' . qp_esc($this->_data[$key]);
		}
		
		if (in_array('eliminado', $fields))
		{
			$_where.= ' AND `eliminado` = FALSE';
		}

		if (in_array('visible', $fields))
		{
			is_null($visible) or $_where.= ' AND `visible` = ' . qp_esc($visible);
		}

		$data = sql_data('SELECT * FROM `'.$this::$tblname.'` WHERE TRUE' . $_where, TRUE);
		
		if (is_null($data) or count($data) === 0)
		{
			$this
				-> _repair_data ()
				-> _found = FALSE;
			
			return $this;
		}

		foreach($data as $key => $val)
		{
			$this->_data[$key] = $val;
		}

		$this
			-> _repair_data ()
			-> _found = TRUE;
		
		return $this;
    }

    /**
     * insert ()
     * Ingreso un registro a la TABLA de la BBDD
     */
    public function insert ()
    {
		$requireds_check = TRUE;
		if (func_num_args() > 0)
		{
			$requireds_check = func_get_arg(0);
		}
		
		$requireds = $this::$requireds;
		$protecteds = $this::$protecteds;
		
		## Validar que los requeridos no esten vacíos
		$faltantes = [];
		foreach($requireds as $index)
		{
			if ( ! isset($this->_data[$index]) or is_empty($this->_data[$index]))
			{
				$faltantes[] = $index;
			}
		}
		if ($requireds_check and count($faltantes) > 0)
		{
// 			sql_trans(false);
			
			throw new BasicException(grouping($faltantes, [
				'prefix' => ['El campo ', 'Los campos '],
				'suffix' => [' es requerido', ' son requeridos'],
			]));
		}
		
		$columns = array_keys($this::$columns);
		$columns = array_combine($columns, $columns);
		
		foreach($protecteds as $_column)
		{
			unset($columns[$_column]);
		}
		
		if ( ! is_null($this::$key))
		{
			unset($columns[$this::$key]);
		}
		
		foreach(array_values($columns) as $column)
		{
		    if (preg_match('/GENERATED/i', $this::$columns[$column]['Extra']))
		    {
		        unset($columns[$column]);
		    }
		}
		
		$columns = array_values($columns);

		## Ejecutar la consulta
		$query = 'INSERT INTO `' . $this::$tblname . '` (' . 
			
			implode(', ', array_map(function($index){
				return '`' . $index . '`';
			}, $columns)). 
			
			') VALUES (' . 
			
			implode(', ', array_map(function($index) use ($requireds){
				$index_dats = $this::$columns[$index];
				
				$data =& $this->_data;
				isset($data[$index]) or $data[$index] = NULL;
				
				if (in_array($index, $requireds))
				{
					return qp_esc($data[$index]);
				}
				elseif ( ! $index_dats['Null'] or $index_dats['Extra'] === 'auto_increment')
				{
					return qp_esc($data[$index], 'DEFAULT');
				}
				
				return qp_esc($data[$index], TRUE);
			}, $columns)). 
			
			')';

		if ( ! is_null($this::$key))
		{
			$new_id = sql($query, TRUE);
			
			if ($new_id === FALSE)
			{
// 				sql_trans(false);

				throw new BasicException('No se pudo ingresar el registro ' . $this::$tblname_singular, 0, ['query' => $query]);
			}
			
			$this->_data[$this::$key] = $new_id;
		}
		else
		{
			$exec = sql($query);
			
			if ( ! $exec)
			{
// 				sql_trans(false);
				
				throw new BasicException('No se pudo ingresar el registro ' . $this::$tblname_singular, 0, ['query' => $query]);
			}
		}
		
		$this->select();
		return TRUE;
    }

    /**
     * update ()
     * Actualiza el registro en la BBDD
     */
    public function update ()
    {
		$requireds = $this::$requireds;
		$protecteds = $this::$protecteds;
		$keys = $this::$keys;
		
		## Validar que los requeridos no esten vacíos
		$faltantes = [];
		foreach($requireds as $_field)
		{
			if ( ! isset($this->_data[$_field]) or is_empty($this->_data[$_field]))
			{
				$faltantes[] = $_field;
			}
		}
		if (count($faltantes) > 0)
		{
// 			sql_trans(false);
			throw new BasicException(grouping($faltantes, [
				'prefix' => ['El campo ', 'Los campos '],
				'suffix' => [' es requerido', ' son requeridos'],
			]));
		}
		
		$columns = array_keys($this::$columns);
		$columns = array_combine($columns, $columns);
		
		foreach($protecteds as $_column)
		{
			unset($columns[$_column]);
		}
		
		foreach($keys as $_column)
		{
			unset($columns[$_column]);
		}
	        
		foreach(array_values($columns) as $column)
		{
		    if (preg_match('/GENERATED/i', $this::$columns[$column]['Extra']))
		    {
		        unset($columns[$column]);
		    }
		}
		
		$columns = array_values($columns);
		
		## Ejecutar la consulta
		$query = 'UPDATE `' . $this::$tblname . '` SET ' . 
			
			implode(', ', array_map(function($field) use ($requireds){
				$field_dats = $this::$columns[$field];
				
				$campo = '`' . $field . '` = ';
				
				$data =& $this->_data;
				isset($data[$field]) or $data[$field] = NULL;
				
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ( ! $field_dats['Null'])
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				
				return $campo . qp_esc($data[$field], TRUE);
			}, $columns)). 
			
			' WHERE ' . 
			
			implode(' AND ', array_map(function($field) use ($requireds){
				$field_dats = $this::$columns[$field];
				
				$campo = '`' . $field . '` = ';
				
				$data =& $this->_data;
				isset($data[$field]) or $data[$field] = NULL;
				
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ( ! $field_dats['Null'])
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				
				return $campo . qp_esc($data[$field], TRUE);
			}, $keys))
			;
		
		$exec = sql($query);
		
		if ( ! $exec)
		{
// 			sql_trans(false);
			throw new BasicException('No se pudo actualizar el registro ' . $this->_class);
		}
		
		$this->select();
		return TRUE;
    }

    /**
     * delete ()
     * Elimina el registro en la BBDD
     */
    public function delete ()
    {
		/** @toDo Detectar si tiene dependientes no eliminados y mostrar error */
		
		$requireds = $this::$requireds;
		$protecteds = $this::$protecteds;
		$keys = $this::$keys;
		
		## Ejecutar la consulta
		$query = 'DELETE FROM `' . $this::$tblname . '` ';
		
		$columns = array_keys($this::$columns);
		
		if (in_array('eliminado', $columns))
		{
			$query = 'UPDATE `' . $this::$tblname . '` SET `eliminado` = TRUE';
		}
		
		$query .= ' WHERE ' . 
			implode(' AND ', array_map(function($field) use ($requireds){
				$field_dats = $this::$columns[$field];
				$campo = '`' . $field . '` = ';
				$data =& $this->_data;
				isset($data[$field]) or $data[$field] = NULL;
				if (in_array($field, $requireds))
				{
					return $campo . qp_esc($data[$field]);
				}
				elseif ( ! $field_dats['Null'])
				{
					return $campo . qp_esc($data[$field], 'DEFAULT');
				}
				return $campo . qp_esc($data[$field], TRUE);
			}, $keys));
				;
		
		$exec = sql($query);
		
		if ( ! $exec)
		{
// 			sql_trans(false);
			throw new BasicException('No se pudo actualizar el registro ' . $this->_class);
		}
		
		$this->select();
		return TRUE;
    }

    /**
     * found ()
	 * Permite conocer si el registro ha sido encontrado
     */
    public function found ()
    {
        return $this->_found;
    }
	
    /**
     * fromArray()
     * Permite tener una instanca de `usuario` desde un array de datos
     *
     * @param Array $data
     * @return self
     */
    public function fromArray ($data)
    {
		$instance = obj($this::$_class);
		
		foreach($data as $key => $val)
		{
			$instance->_data[$key] = $val;
		}

		$instance->_found = TRUE;
		
		return $instance;
    }

    //========================================//
    // Funciones Protejidas de Objeto         //
    //========================================//

    /**
     * _kcur ()
     */
    protected function _kcur ($index)
    {
        $kcur = $this::$key_column_usage_referenced;
        $key = $this->_data[$index];
		
		if (is_null($key))
		{
			return NULL;
		}
		
		$CLASS = NULL;
		
		foreach ($kcur as $ref)
		{
			if ($ref['COLUMN_NAME'] === $index)
			{
				$CLASS = $ref['REFERENCED_TABLE_CLASS'];
			}
		}

		if ( is_null($CLASS))
		{
			return NULL;
		}
		
        return obj($CLASS, $key);
    }

    /**
     * _kcu ()
     */
    protected function _kcu ($index, $arr = [])
    {
        $kcu = $this::$key_column_usage;
        $TABLE_NAME = $arr['TABLE_NAME'];
        $COLUMN_NAME = $arr['COLUMN_NAME'];
		
		$CLASS = NULL;
		
		foreach ($kcu as $ref)
		{
			if ($ref['TABLE_NAME'] === $TABLE_NAME)
			{
				$CLASS = $ref['TABLE_CLASS'];
			}
		}
		
		if ( is_null($CLASS))
		{
			return NULL;
		}
		
		return obj($CLASS) -> lista([
			$COLUMN_NAME => $this->_data[$index]
		]);
    }

    /**
     * _clear_data ()
     * Limpia los campos de la data
     */
    protected function _clear_data ()
    {
        $columns = array_keys($this::$columns);

        foreach($columns as $index)
        {
            $this->_data[$index] = NULL;
        }

        return $this;
    }

    /**
     * _repair_data ()
     * Repara los campos de la data
     */
    protected function _repair_data ($clear = FALSE)
    {
        if ($clear)
        {
            $this->_clear_data();
        }

        $keys = $this::$keys;
        $columns = array_keys($this::$columns);
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;
        $referenceds = $this::$referenceds;
        $key_column_usage = $this::$key_column_usage;

		## añade solo los PKs
        foreach($keys as $index)
        {
        	if (isset($this->_data[$index]) and ! is_empty($this->_data[$index]))
        	{
        		continue;
        	}

        	$clas = $this::$columns[$index]['clas'];
        	$deff = $this::$columns[$index]['Default'];
        	$this->_data[$index] = is_empty($deff) ? ($clas === 'numeric' ? 0 : ($clas === 'array' ? [] : NULL)) : $deff;
        }

        ## añade todos los Campos
        foreach($columns as $index)
        {
        	if (isset($this->_data[$index]) and ! is_empty($this->_data[$index]))
        	{
        		continue;
        	}
        	
        	$clas = $this::$columns[$index]['clas'];
        	$deff = $this::$columns[$index]['Default'];
        	
        	if (in_array($index, $referenceds))
        	{
        		$this->_data[$index] = is_empty($deff) ? NULL : $deff;
        		$this->_data[preg_replace('/((_)+id|id(_)+)/i', '', $index) . '_obj'] = ['exec' => 'kcur', 'index' => $index];
        		continue;
        	}
        	
        	$this->_data[$index] = is_empty($deff) ? ($clas === 'numeric' ? 0 : ($clas === 'array' ? [] : NULL)) : $deff;
        }

        $last_refs_tbls = [];
        foreach($key_column_usage as $tbl)
        {
			
        	$TABLE_NAME = $tbl['TABLE_NAME'];
        	$COLUMN_NAME = $tbl['COLUMN_NAME'];
        	$REFERENCED_COLUMN_NAME = $tbl['REFERENCED_COLUMN_NAME'];
			
			if ( ! isset($tbl['TABLE_AS_ATTR']) or is_empty($tbl['TABLE_AS_ATTR']))
			{
				$attr = preg_replace('/^' . $this::$tblname . '(e)?(s)?\_/', '', $TABLE_NAME);
        		$attr .= '_lista';
				
				$n = '';
				while(in_array($attr . $n, $last_refs_tbls))
				{
					$n === '' and $n = 0;
					$n++;
				}

				$attr .= $n;
				$last_refs_tbls[] = $attr;
			}
			else
			{
				$attr = $tbl['TABLE_AS_ATTR'];
			}

			$index = $attr;
        	
        	if (isset($this->_data[$index]) and ! is_empty($this->_data[$index]))
        	{
        		continue;
        	}
        	
        	$this->_data[$index] = ['exec' => 'kcu', 'index' => $REFERENCED_COLUMN_NAME, 'TABLE_NAME' => $TABLE_NAME, 'COLUMN_NAME' => $COLUMN_NAME];
        }

        return $this;
    }


    //========================================//
    // Funciones Mágicas                      //
    //========================================//

    /**
     * __call ()
     */
    public function __call ($name, $args)
    {
        if (preg_match('#^set_(.+)#', $name))
        {
        	$index = preg_replace('#^set_#', '', $name);
        	return $this->__set($index, $args[0]);
        }

        if (preg_match('#^get_(.+)#', $name))
        {
        	$index = preg_replace('#^get_#', '', $name);
        	return $this->__get($index);
        }
		
		if ($name === 'was_found')
		{
			return $this->found();
		}

		if ($name === 'not_found')
		{
			return ! $this->found();
		}

		throw new BasicException('Method `' . $name . '` not exists');
		
        return $this;
    }

    /**
     * __invoke ()
     */
    public function __invoke($name = NULL)
    {
        return is_null($name) ? $this->getArrayCopy() : $this->_data[$name];
    }
	
    /**
     * __toArray ()
     */
	public function __toArray()
	{
		return $this->_data;
	}
	
    /**
     * __isset ()
     */
    public function __isset ($index)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	return FALSE;
        }

        if ( ! isset($this->_data[$index]))
        {
        	$columns = $this::$columns;
        	
        	if ( ! isset($columns['metadata']))
        	{
        		return FALSE;
        	}
        	
        	return isset($this->metadata[$index]);
        }

        return isset($this->_data[$index]);
    }

    /**
     * __unset ()
     */
    public function __unset ($index)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	throw new BasicException('No puede eliminar el campo `'.$index.'`');
        }

        unset($this->_data[$index]);
        return $this;
    }

    /**
     * __get ()
     */
    public function &__get ($index)
    {
        $hiddens = $this::$hiddens;

        if (in_array($index, $hiddens))
        {
            throw new BasicException('No puede obtener el campo `'.$index.'`');
        }

        $columns = $this::$columns;
        $fields = array_keys($columns);
		
        if ( ! in_array($index, $fields) and ! isset($this->_data[$index]))
        {
           if ( ! isset($columns['metadata']))
			{
				throw new BasicException('No puede obtener el campo `'.$index.'`');
			}
       	
           if ( ! isset($this->metadata[$index]))
           {
           	$this->metadata[$index] = NULL;
           }
           
           return $this->metadata[$index];
       }
       
		$field = $this->_data[$index];
		
//       if (is_callable($field))
//	   {
//		   return $field();
//	   }

		if (is_array($field) and isset($field['exec']) and is_callable([$this, '_' . $field['exec']]))
	   {
		   return call_user_func([$this, '_' . $field['exec']], isset($field['index']) ? $field['index'] : $index, $field);
	   }
       
       return $this->_data[$index];
    }

    /**
     * __set ()
     */
    public function __set ($index, $newval)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	throw new BasicException('No puede establecer el campo `'.$index.'`');
        }

        $columns = $this::$columns;

        if (isset($columns[$index]) and $columns[$index]['tipo'] === 'DECIMAL' and is_string($newval))
        {
        	$newval = strtonumber($newval);
        }

        if ( ! isset($columns[$index]) and isset($columns['metadata']))
        {
        	$this->metadata[$index] = $newval;
        	return $this;
        }

        $this->_data[$index] = $newval;
        return $this;
    }

    //========================================//
    // Funciones ArrayAccess                  //
    //========================================//

    /**
     * offsetExists ()
     */
    public function offsetExists ($index)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	return FALSE;
        }

        if ( ! isset($this->_data[$index]))
        {
        	$columns = $this::$columns;
        	
        	if ( ! isset($columns['metadata']))
        	{
        		return FALSE;
        	}
        	
        	return isset($this->metadata[$index]);
        }

        return isset($this->_data[$index]);
    }

    /**
     * offsetUnset ()
     */
    public function offsetUnset ($index)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	throw new BasicException('No puede eliminar el campo `'.$index.'`');
        }

        unset($this->_data[$index]);
        return $this;
    }

    /**
     * offsetGet ()
     */
    public function offsetGet ($index)
    {
        $hiddens = $this::$hiddens;

        if (in_array($index, $hiddens))
        {
            throw new BasicException('No puede obtener el campo `'.$index.'`');
        }

        $columns = $this::$columns;
        $fields = array_keys($columns);
		
        if ( ! in_array($index, $fields) and ! isset($this->_data[$index]))
        {
           if ( ! isset($columns['metadata']))
		   {
       	    throw new BasicException('No puede obtener el campo `'.$index.'`');
		   }
       	
           if ( ! isset($this->metadata[$index]))
           {
           	$this->metadata[$index] = NULL;
           }
           
           return $this->metadata[$index];
       }

		$field = $this->_data[$index];
		
//       if (is_callable($field) and ! in_array(mb_strtolower($field), ['date', 'mes']))
//	   {
//		   try
//		   {
//			   $field = $field();
//			   return $field;
//		   }
//		   catch (\Exception $e)
//		   {
//			   return $field;
//		   }
//	   }

		if (is_array($field) and isset($field['exec']) and is_callable([$this, '_' . $field['exec']]))
	   {
		   return call_user_func([$this, '_' . $field['exec']], isset($field['index']) ? $field['index'] : $index, $field);
	   }
       
       return $this->_data[$index];
    }

    /**
     * offsetSet ()
     */
    public function offsetSet ($index, $newval)
    {
        $hiddens = $this::$hiddens;
        $protecteds = $this::$protecteds;

        if (in_array($index, $hiddens) or in_array($index, $protecteds))
        {
        	throw new BasicException('No puede establecer el campo `'.$index.'`');
        }

        $columns = $this::$columns;

        if (isset($columns[$index]) and $columns[$index]['tipo'] === 'DECIMAL' and is_string($newval))
        {
        	$newval = strtonumber($newval);
        }

        if ( ! isset($columns[$index]) and isset($columns['metadata']))
        {
        	$this->metadata[$index] = $newval;
        	return $this;
        }

        $this->_data[$index] = $newval;
        return $this;
    }

    //========================================//
    // Funciones Countable                    //
    //========================================//

    /**
     * count ()
     */
    public function count ()
    {
        return $this->_found ? 1 : 0;
    }

	/*
	public function update()
	{
		
	}

	public function delete()
	{
	}

	
	*/
	
    public function json ()
    {
		$json = json_decode(json_encode($this), true);
		
		$key_column_usage = $this::$key_column_usage;
		$last_refs_tbls = [];
        foreach($key_column_usage as $tbl)
        {
        	$TABLE_NAME = $tbl['TABLE_NAME'];
        	$COLUMN_NAME = $tbl['COLUMN_NAME'];
        	$REFERENCED_COLUMN_NAME = $tbl['REFERENCED_COLUMN_NAME'];
			
			if ( ! isset($tbl['TABLE_AS_ATTR']) or is_empty($tbl['TABLE_AS_ATTR']))
			{
				$attr = preg_replace('/^' . $this::$tblname . '(e)?(s)?\_/', '', $TABLE_NAME);
        		$attr .= '_lista';
				
				$n = '';
				while(in_array($attr . $n, $last_refs_tbls))
				{
					$n === '' and $n = 0;
					$n++;
				}

				$attr .= $n;
				$last_refs_tbls[] = $attr;
			}
			else
			{
				$attr = $tbl['TABLE_AS_ATTR'];
			}

			$index = $attr;
        	
        	if (isset($json[$index]))
        	{
        		unset($json[$index]);
        	}
        }
		
		
        return json_encode($json);
    }
}



