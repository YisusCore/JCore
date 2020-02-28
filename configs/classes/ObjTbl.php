<?php
//use Exception;

class ObjTbl_SubLista extends ArrayObject
{}

abstract class ObjTbl extends JArray
{
	/**
     * Constantes
     */
	const Numero = 1;
	const Texto = 2;
	const Arreglo = 4;
	const FechaHora = 8;
	const Fecha = 16;
	const Hora = 32;
	const Boolean = 64;

	private static function gcc ()
	{
		return get_called_class();
	}

	/**
     * @static @var $_tblname
	 * Nombre de la tabla del objeto
     */
    protected static $_tblname = NULL;
	protected static function tblname()
	{
		$that = self::gcc();
		return $that::$_tblname;
	}

    /**
     * @static @var $_keys
	 * Listado de los campos claves
     */
    protected static $_keys = [];
	public static function keys()
	{
		$that = self::gcc();
		return $that::$_keys;
	}

    /**
     * @static @var $_key
	 * Si solo hay un campo clave, entonces es este
     */
    protected static $_key = NULL;
	protected static function key()
	{
		$that = self::gcc();
		return $that::$_key;
	}

    /**
     * @static @var $_toString
	 * Si hay un campo o función que permita retornar el objeto como string
     */
    protected static $_toString = NULL;
	protected static function toString()
	{
		$that = self::gcc();
		return $that::$_toString;
	}

    /**
     * @static @var $_columns
	 * Listado de todas las columnas mediante un array:
	 *
	 * Formato de los valores dentro del array
	 * [
	 * 	 'nombre'   => 'field',        // Nombre del campo
	 *   'tipo'     => ObjTbl::Texto,  // El tipo del campo, por defecto será asumido como ObjTbl::Texto
	 *   'largo'    => 99999999,       // El mb_strlen del valor del campo, por defecto es el máximo posible
	 *   'opciones' => NULL,           // Si el campo solo tiene autorizado uno de los valores dentro de esta lista, por defecto NULO
	 *   'defecto'  => NULL,           // El valor que tomará por defecto si no se ha asignado algún valor al campo, por defecto NULO
	 *   'attr'     => NULL,           // Alguna atribución especial al campo tal como UNSIGNED ZEROFILL, por defecto NULO
	 *   'nn'       => FALSE,          // Identificador si el campo es NULLABLE o no, por defecto siempre es NULLABLE
	 *   'ne'       => TRUE,           // Si el campo es NOT NULL, se va a permitir campo vacío o no
	 *   'ai'       => FALSE,          // Si el campo es el único KEY identifica si el valor es AUTO_INCREMENT, caso contrario no sirve
	 *   'ag'       => NULL,           // Si el campo es autogenerado, este es el nombre de la función que devolverá el valor del campo
	 *   'dg'       => FALSE,          // Si el campo es autogenerado mediante DATA_BASE, por tanto no se puede hacer un INSERT o UPDATE
	 * ]
     */
    protected static $_columns = [];
	
	protected static function columns_real()
	{
		$that = self::gcc();
		return $that::$_columns;
	}
	
	protected static function columns()
	{
		static $_columns = [];
		$that = self::gcc();
		isset($_columns[$that]) or $_columns[$that] = [];
		if ($columns = self::columns_real() and count($_columns[$that]) !== count($columns))
		{
			foreach($columns as $column)
			{
				// Añadiendo posibles atributos faltantes
				$column = array_merge([
					'nombre'   => NULL,
					'tipo'     => ObjTbl::Texto,
					'largo'    => 99999999,
					'opciones' => NULL,
					'defecto'  => NULL,
					'attr'     => NULL,
					'nn'       => FALSE,
					'ne'       => TRUE,
					'ai'       => FALSE,
					'ag'       => NULL,
					'dg'       => FALSE,
				], $column);

				// Validando que exista el atributo NOMBRE
				if (is_null($column['nombre']))
				{
					throw new Exception ('Algunos campos no tienen nombre');
				}

				// Corrigiendo posibles atributos dañados
				is_null ($column['tipo'])     and $column['tipo']     = ObjTbl::Texto;
				is_null ($column['largo'])    and $column['largo']    = 99999999;
				is_empty($column['opciones']) and $column['opciones'] = NULL;
				is_empty($column['defecto'])  and $column['defecto']  = NULL;
				is_empty($column['attr'])     and $column['attr']     = NULL;
				is_bool ($column['nn'])        or $column['nn']       = FALSE;
				is_bool ($column['ai'])        or $column['ai']       = FALSE;
				is_empty($column['ag'])       and $column['ag']       = NULL;
				is_bool ($column['dg'])        or $column['dg']       = FALSE;

				// Las posibilidades por las cuales sería innecesario que NOT_EMPTY sea TRUE
				if(
					 ! $column['nn'] // Si NOT_NULL = FALSE
					or $column['dg'] // Si DB_GENERATED = TRUE
					or $column['ai'] // Si AUTO_INCREMENTE = TRUE
				)
				{
					$column['ne'] = FALSE;
				}

				// Si NOT_NULL = TRUE entonces el valor por defecto no puede ser NULL
				// Si el atributo es PK BIGINT NOT NULL AUTO_INCREMENT y el valor es CERO este no se pondrá al momento de insertar
				if ($column['nn'] and is_null($column['defecto']))
				{
					switch($column['tipo'])
					{
						case ObjTbl::Boolean:
							$column['defecto'] = false;
							break;
						case ObjTbl::Arreglo:
							$column['defecto'] = [];
							break;
						case ObjTbl::Numero:
							$column['defecto'] = $column['ai'] ? NULL : 0;
							break;
						case ObjTbl::FechaHora:
							$column['defecto'] = date('Y-m-d H:i:s');
							break;
						case ObjTbl::Fecha:
							$column['defecto'] = date('Y-m-d');
							break;
						case ObjTbl::Hora:
							$column['defecto'] = date('H:i:s');
							break;
						case ObjTbl::Texto: default:
							$column['defecto'] = '';
							break;
					}
				}

//				if (in_array($column['tipo'], [ObjTbl::FechaHora, ObjTbl::Fecha, ObjTbl::Hora]) and 
//					in_array($column['defecto'], ['CURRENT_TIMESTAMP', 'NOW', 'NOW()']))
//				{
//					switch($column['tipo'])
//					{
//						case ObjTbl::FechaHora:
//							$column['defecto'] = date('Y-m-d H:i:s');
//							break;
//						case ObjTbl::Fecha:
//							$column['defecto'] = date('Y-m-d');
//							break;
//						case ObjTbl::Hora:
//							$column['defecto'] = date('H:i:s');
//							break;
//					}
//				}

				// Añadiendo la columna corregida
				$_columns[$that][$column['nombre']] = $column;
			}
		}
		
		return $_columns[$that];
	}
	
	protected static function fields()
	{
		static $_fields = [];
		$that = self::gcc();
		isset($_fields[$that]) or $_fields[$that] = [];
		if ($columns = self::columns() and count($_fields[$that]) !== count($columns))
		{
			$_fields[$that] = array_map(function($o){
				return $o['nombre'];
			}, $columns);
		}

		return $_fields[$that];
	}
	
    /**
     * @static @var $_rxs_hijo
	 * Listado de todas las referencias en las cuales este objeto es el padre
	 * (los valores de las llaves de este objeto son campos del otro objeto)
	 *
	 * Formato de los objetos dentro del array
	 * [
	 * 	 'tabla'   => 'tabla',        // Nombre de la tabla que relacinó su campo con este objeto
	 * 	 'clase'   => 'clase',        // Nombre de la clase a la cual llamar cuando se consulte el listado de los objetos hijos
	 * 	 'columnas'=> [               // Listado de todos los campos relacionados entre sí de la tabla de este objeto como el del hijo
	 *     'campo' => 'campo_hijo'
	 *   ],
	 *   'field'   => NULL,           // Un valor del cual como se quiere que aparezca como campo en este objeto
     *   'on_update' => 'CASCADE',    // Acción relacionado con el hijo una vez que este objeto cambie los valores de los campos llaves
     *   'on_delete' => 'CASCADE',    // Acción relacionado con el hijo una vez que el registro db de este objeto es eliminado
     *   'r11'       => FALSE,        // La relación con el hijo es 1-1, si ese es el caso se intenta agregar un registro automáticamente
	 * ]
     */
    protected static $_rxs_hijo = [];
	
	protected static function rxs_hijo_real()
	{
		$that = self::gcc();
		return $that::$_rxs_hijo;
	}
	
	protected static function rxs_hijo()
	{
		static $_rxs_hijo = [];
		$that = self::gcc();
		isset($_rxs_hijo[$that]) or $_rxs_hijo[$that] = [];
		if ($rxs_hijo = self::rxs_hijo_real() and count($_rxs_hijo[$that]) !== count($rxs_hijo))
		{
			$fields = self::fields();

			foreach($rxs_hijo as $rx)
			{
				// Añadiendo posibles atributos faltantes
				$rx = array_merge([
					'tabla'   => NULL,
					'clase'   => NULL,
					'columnas'=> [],
					'field'   => NULL,
					'on_update' => 'CASCADE',
					'on_delete' => 'CASCADE',
					'r11'     => FALSE,
				], $rx);

				// Validando que exista el atributo NOMBRE y CLASE
				if (is_null($rx['tabla']) or is_null($rx['clase']))
				{
					throw new Exception ('Algunas relaciones no tienen nombre o clase');
				}

				// Validando que exista el atributo COLUMNAS
				if (is_empty($rx['columnas']))
				{
					throw new Exception ('Algunas relaciones no tienen campos');
				}

				$rx['c1'] = count($rx['columnas']) === 1;

				// Corrigiendo posibles atributos dañados
				if (is_null ($rx['field']))
				{
					$tbl_subname = preg_replace('/^' . self::tblname() . '(e)?(s)?\_/', '', $rx['tabla']);
					$tbl_subname_idx = '';
					do
					{
						$_temp_field = $tbl_subname . $tbl_subname_idx . '_lista';
						$tbl_subname_idx === '' and $tbl_subname_idx = 1;
						$tbl_subname_idx++;
					}
					while (in_array($_temp_field, $fields));
					
					$rx['field'] = $_temp_field;
				}

				$fields[] = $rx['field'];

				is_null ($rx['on_update']) and $rx['on_update'] = 'CASCADE';
				is_null ($rx['on_delete']) and $rx['on_delete'] = 'CASCADE';
				is_bool ($rx['r11'])        or $rx['r11']       = FALSE;

				// Añadiendo la columna corregida
				$_rxs_hijo[$that][] = $rx;
			}
		}
		
		return $_rxs_hijo[$that];
	}

    /**
     * @static @var $_rxs_padre
	 * Listado de todas las referencias en las cuales este objeto es el hijo
	 * (algunos de los campos del este objeto son los valores de las llaves de otro objeto)
	 *
	 * Formato de los objetos dentro del array
	 * [
	 * 	 'tabla'   => 'tabla',        // Nombre de la tabla con el cual esta relacionado este objeto
	 * 	 'clase'   => 'clase',        // Nombre de la clase a la cual llamar cuando se consulte el objeto padre
	 * 	 'columnas'=> [               // Listado de todos los campos relacionados entre sí de la tabla de este objeto como el del padre
	 *     'campo' => 'campo_padre'
	 *   ],
	 *   'field'   => NULL,           // Un valor del cual como se quiere que aparezca como campo en este objeto
	 *   'r11'     => FALSE,          // La relación con el padre es 1-1, si ese es el caso se intenta eliminar el registro automáticamente
	 * ]
     */
    protected static $_rxs_padre = [];
	protected static function rxs_padre_real()
	{
		$that = self::gcc();
		return $that::$_rxs_padre;
	}
	
	protected static function rxs_padre()
	{
		static $_rxs_padre = [];
		$that = self::gcc();
		isset($_rxs_padre[$that]) or $_rxs_padre[$that] = [];
		if ($rxs_padre = self::rxs_padre_real() and count($_rxs_padre[$that]) !== count($rxs_padre))
		{
			$fields = self::fields();

			foreach($rxs_padre as $rx)
			{
				// Añadiendo posibles atributos faltantes
				$rx = array_merge([
					'tabla'   => NULL,
					'clase'   => NULL,
					'columnas'=> [],
					'field'   => NULL,
					'r11'     => FALSE,
				], $rx);

				// Validando que exista el atributo NOMBRE y CLASE
				if (is_null($rx['tabla']) or is_null($rx['clase']))
				{
					throw new Exception ('Algunas relaciones no tienen nombre o clase');
				}

				// Validando que exista el atributo COLUMNAS
				if (is_empty($rx['columnas']))
				{
					throw new Exception ('Algunas relaciones no tienen campos');
				}

				$rx['c1'] = count($rx['columnas']) === 1;
				if ($rx['c1'])
				{
					$rxc1_cam_padre = array_keys($rx['columnas']) [0];
					$rxc1_cam_hijo  = array_values($rx['columnas']) [0];
				}

				// Corrigiendo posibles atributos dañados
				if (is_null ($rx['field']))
				{
					$tbl_subname = $rx['c1'] ? $rxc1_cam_padre : $rx['tabla'];
					$tbl_subname_idx = '';
					do
					{
						$_temp_field = $tbl_subname . $tbl_subname_idx . '_obj';
						$tbl_subname_idx === '' and $tbl_subname_idx = 1;
						$tbl_subname_idx++;
					}
					while (in_array($_temp_field, $fields));
					
					$rx['field'] = $_temp_field;
				}

				$fields[] = $rx['field'];

				is_bool ($rx['r11'])        or $rx['r11']       = FALSE;

				// Añadiendo la columna corregida
				$_rxs_padre[$that][] = $rx;
			}
		}
		
		return $_rxs_padre[$that];
	}

	protected static function rxs_padre_nexto_fields()
	{
		static $_nexto_fields = [];
		$that = self::gcc();
		isset($_nexto_fields[$that]) or $_nexto_fields[$that] = [];
		if (count($_nexto_fields[$that]) === 0)
		{
			$rxs_padre = self::rxs_padre();

			foreach($rxs_padre as $rx)
			{
				if ( ! $rx['c1'])
				{
					continue;
				}

				$cam_padre = array_keys($rx['columnas']) [0];
				$cam_hijo  = array_values($rx['columnas']) [0];

				$_nexto_fields[$that][$cam_padre] = $rx['field'];
			}
		}

		return $_nexto_fields[$that];
	}

	protected static function rxs_padre_nonexto_fields()
	{
		static $_nonexto_fields = [];
		$that = self::gcc();
		isset($_nonexto_fields[$that]) or $_nonexto_fields[$that] = [];
		if (count($_nonexto_fields[$that]) === 0)
		{
			$rxs_padre = self::rxs_padre();

			foreach($rxs_padre as $rx)
			{
				if ($rx['c1'])
				{
					continue;
				}

				$_nonexto_fields[$that][] = $rx['field'];
			}
		}

		return $_nonexto_fields[$that];
	}

	public static function FromArray($data)
	{
		$that = self::gcc();
		$instance = new $that();
		
		foreach($data as $k => $v)
		{
			$instance -> _data_original [$k] = $v;
			$instance -> _data_instance [$k] = $v;
		}
		
		// [ToDo] Identificar posibles hijos agregados
		
		$instance -> _found = TRUE;
		$instance -> _from_array = TRUE;

		return $instance;
	}

    //========================================//
    // Atributos de Objeto                    //
    //========================================//

	protected $_found = FALSE;
	public function found()
	{
		return $this->_found;
	}

	protected $_from_array = FALSE;
	public function from_array()
	{
		return $this->_from_array;
	}

	protected $_data_original;
	protected $_data_instance;
	
	// Listado de campos que el usuario establecio manualmente
	// Si un campo autogenerado es seteado manualmente entonces ya no se generará mas y lo mismo sucede con las datas de los RX
	// Solo al momento de que el usuario estable un valor NULL, ese campo se quitará de la lista
	// Al hacerse un SELECT la lista queda vacía nuevamente 
	// pero se debe detectar los campos autogenerados que no corresponden con lo calculados 
	// ya que pueden ser porque se editaron manualmente en un pasado
	// Al hacerse un INSERT o un UPDATE y se ha establecido un array de datos "rxs_hijo", 
	// estos serán intentados ser insertados o actualizados también
	protected $_manual_setted = [];

    //========================================//
    // Funciones de Objeto                    //
    //========================================//

	protected function _añadir_callbacks()
	{
		$this->_callbacks['offsetSet'] = function ($newval, $index, $that)
		{
			$this->_calc_ag ();
		};

//		$this->_callbacks['toarray'] = function (&$arr, $that)
//		{
//			$arr = [];
//			print_array('toarray', $arr);
//		};
//
//		$this->_callbacks['exists'] = function (&$return, $index, $that)
//		{
//			print_array('exists', $return, $index);
//		};
//
//		$this->_callbacks['get'] = function (&$return, &$index, $that)
//		{
//			print_array('get', $return, $index);
//		};
//
//		$this->_callbacks['before_set'] = function (&$newval, &$index, $that)
//		{
//			print_array('set', $newval, $index);
//		};
//
//
//		$this->_callbacks['before_unset'] = function (&$index, $that)
//		{
//			print_array('unset', $index);
//		};
	}
	
    /**
     * Constructor
     */
    public function __construct (...$data_keys)
    {
		//=== Habilitando los atributos que contendrán la data del objeto

		// Aquí estará la data original que será comparada al hacer un insert, update o delete;
		// Se reestablece al hacer un select o un rollback
		$this->_data_original = new JArray();

		// Aquí estará la data cambiable de la instancia
		$this->_data_instance = new JArray();

		//=== Instancio los atributos de las datas en el objeto
		parent::__construct($this->_data_instance);
		
		// Aquí cargaré todos los callbacks que me ayudarán a identificar todos los cambios generados
		$this->_añadir_callbacks();

		//=== Reparo todos los atributos del objeto basado en los campos de la db
        $this->_repair_data();

		//=== Validando si se ha enviado atributos llaves para el objeto

		if (count($data_keys) === 0)
		{
			// No se ha enviado atributos llaves, así que el objeto esta listo para crear uno nuevo
			return;
		}
		
		// Limpiando de posibles ALL IS NULL
		do
		{
			$last = array_pop($data_keys);
		}
		while(is_null($last) and count($data_keys) > 0);
		
		// Si el último analizado no era NULL entonces se agregará nuevamente al array
		is_null($last) or $data_keys[] = $last;
		
		if (count($data_keys) === 0)
		{
			// Se habían enviado puros valores NULO
			return;
		}

		//=== Agregando los datos de los atributos llaves al objeto
		foreach(self::keys() as $_ind => $_field)
		{
			if ( ! isset($data_keys[$_ind]) or is_null($data_keys[$_ind]))
			{
				// El dato no se envió en el array o es NULO
				continue;
			}

			// Agregando a la data de la instancia
			$this->_data_instance[$_field] = $data_keys[$_ind];
		}

		//=== Buscando la información del objeto basado en los campos laves
		$this->select();
    }

    /**
     * _calc_ag
	 * Permite calcular todos los autogenerados
     */
	protected function _calc_ag ()
	{
		$columns = self::columns();
		foreach($columns as $column)
		{
			if (is_empty($column['ag']))
        	{
        		continue;
        	}

			$method = $column['ag'];
			is_callable($method) or $method = [$this, $method];
			
			if ( ! is_callable($method))
			{
				continue;
			}

			$field = $column['nombre'];
			if (in_array($field, $this->_manual_setted))
			{
				continue;
			}

			try
			{
				$valor = call_user_func_array($method, [$this]);
				
				if ($this[$field] <> $valor)
				{
					$this->offsetSet($field, $valor, false);
				}
			}
			catch (Exception $e)
			{}
		}
	}

    /**
     * _uid
	 * Hasheo único basado en los atributos llaves
     */
	protected function _uid ()
	{
		if ($_key = self::key() and ! is_empty($_key))
		{
			return $this->_data_original[$_key];
		}

		$_llaves = [];
		$_keys = self::keys();

		$_llaves[] = self::gcc();
		foreach($_keys as $_key)
		{
			$_llaves[] = $this->_data_original[$_key];
		}

		return md5(json_encode($_llaves));
	}

	protected $_errors = [];
	public function get_errors ()
	{
		return $this->_errors;
	}

	public function get_error ($join_by = '<br>')
	{
		return implode($join_by, array_map(function($o){
			return '- ' . $o;
		}, $this->_errors));
	}

	public function get_last_error ()
	{
		$_errors = $this->_errors;
		return array_pop($_errors);
	}

    /**
     * _repair_data
	 * Permite añadir o corregir atributos faltantes
     */
	protected function _repair_data ()
	{
		// === Alojando los valores necesarios para reparar la data
		
		// Alojando las columnas
		$columns = self::columns();

		// Alojando todos los objetos de las cuales alguno de estos campos dependen
		$rxs_padre_nexto_fields = self::rxs_padre_nexto_fields();

		// Alojando todos los objetos de las cuales alguno de estos campos dependen
		$rxs_padre_nonexto_fields = self::rxs_padre_nonexto_fields();

		// Alojando las relaciones de objetos que dependen de este objeto
		$rxs_hijo = self::rxs_hijo();

		foreach($columns as $column)
		{
			$field = $column['nombre'];
			
			if (isset($this->_data_instance[$field]) and ! is_empty($this->_data_instance[$field]))
        	{
        		continue;
        	}
			
			$default = $column['defecto'];
			
			$this->_data_instance[$field] = $default;
//			$this->_data_original[$field] = $default;

			if (isset($rxs_padre_nexto_fields[$field]))
			{
				$this->_data_instance[$rxs_padre_nexto_fields[$field]] = NULL;
			}
		}

		foreach($rxs_padre_nonexto_fields as $field)
		{
			$this->_data_instance[$field] = NULL;
		}

		foreach($rxs_hijo as $rx)
		{
			$field = $rx['field'];
			$this->_data_instance[$field] = [];
		}
		
		// Calculando los AG
		$this->_calc_ag();

		foreach($columns as $column)
		{
			$field = $column['nombre'];

			if (isset($this->_data_original[$field]) and ! is_empty($this->_data_original[$field]))
        	{
        		continue;
        	}

			$this->_data_original[$field] = $this->_data_instance[$field];
		}
	}

    /**
     * select
	 * Permite hacer una consulta SELECT a la DB 
     */
	public function select (&$_sync = [])
	{
		$keys = self::keys();
		$columns = self::columns();
		
		$query = '';
		$query.= 'SELECT *' . PHP_EOL;
		$query.= 'FROM `' . self::tblname() . '`' . PHP_EOL;
		$query.= 'WHERE TRUE' . PHP_EOL;

		foreach($keys as $key)
		{
			$campo = $columns[$key];
			
			if (is_null($this->_data_instance[$key]) and ! $campo['nn'])
			{
				$query.= ' AND `'.$key.'` IS NULL' . PHP_EOL;
			}
			else
			{
				$query.= ' AND `'.$key.'` = ' . qp_esc($this->_data_instance[$key]) . PHP_EOL;
			}
		}

		$query = filter_apply ('ObjTbl::Select', $query, self::gcc(), $this);

		$data = sql_data($query, TRUE);
		
		if (is_null($data) or count($data) === 0)
		{
			$this->_found = FALSE;
		}
		else
		{
			$this->_found = TRUE;
			foreach($data as $k => $v)
			{
				$this->_data_instance[$k] = $v;
				$this->_data_original[$k] = $v;
			}
		}

		$this -> _repair_data ();
		
		$this -> _manual_setted = [];
		
		// Todos los que han cambiado por ser autogenerados se agregarán a _manual_setted
		foreach($this->_data_original as $k => $v)
		{
			if ($this->_data_instance[$k] <> $v)
			{
				$this -> _manual_setted[] = $k;
				$this->_data_instance[$k] = $this->_data_original[$k];
			}
		}

		$_sync[self::gcc()][$this->_uid()] = $this->__toArray();
		return $this;
	}

    /**
     * verify
	 * Permite validar que todos los campos requeridos esten llenos
     */
	public function verify ()
	{
		$columns = self::columns();
		$columns_ne = array_keys(array_filter($columns, function($o){
			return $o['ne'];
		}));

		$not_valids = [];
		foreach($columns_ne as $column)
		{
			if (is_empty($this->_data_instance[$column]))
			{
				$not_valids[] = $column;
			}
		}

		if (count($not_valids) > 0)
		{
			$this->_errors[] = grouping($not_valids, [
				'prefix' => ['El campo ', 'Los campos '],
				'suffix' => [' es requerido', ' son requeridos'],
			]);
			return false;
		}

		return true;
	}

    /**
     * insert
	 * Permite hacer una consulta INSERT a la DB 
     */
	public function insert (&$_sync = [], &$_changes = [], $_op_level = 1, $forced = false)
	{
		if ($this->_found and ! $forced)
		{
			$this->_errors[] = '[' . self::gcc() . '](' . __FUNCTION__ . ') El objeto ya existe';
			return false;
		}

		sql_trans();

		$_valid = $this->verify();
		if ( ! $_valid)
		{
			sql_trans(false);
			return false;
		}

		$_insert_data = [];

		$columns = self::columns();

		$_ai_key = NULL;
		if ($_key = self::key() and ! is_empty($_key) and $columns[$_key]['ai'])
		{
			$_ai_key = $_key;
			unset($columns[$_key]);
		}

		foreach($columns as $column)
		{
			if ($column['dg'])
			{
				continue;
			}

			$field = $column['nombre'];
			$value = isset($this[$field]) ? $this[$field] : NULL;

			if ( ! preg_match('/(CURRENT_TIMESTAMP|NOW)(\()?(\))?/i', $value))
			{
				$value = qp_esc($value, ! $column['nn']);
			}

			$_insert_data[$field] = $value;
		}

		$query = '';
		$query.= 'INSERT INTO `' . self::tblname() . '` ' . PHP_EOL;
		$query.= '(';
		$query.= implode(', ', array_map(function($o){
			return PHP_EOL . '  `' . $o . '`';
		}, array_keys($_insert_data))) . PHP_EOL;
		$query.= ')' . PHP_EOL;
		$query.= 'VALUES' . PHP_EOL;
		$query.= '(';
		$query.= implode(', ', array_map(function($o){
			return PHP_EOL . '  ' . $o;
		}, array_values($_insert_data))) . PHP_EOL;
		$query.= ')' . PHP_EOL;

		$query = filter_apply ('ObjTbl::Insert', $query, self::gcc(), $this);

		$_exec = @sql($query,  ! is_null($_ai_key));
		
		if ( ! $_exec)
		{
			sql_trans(false);
			$this->_errors[] = 'No se pudo ingresar el registro `' . self::gcc() . '`';
			return false;
		}

		is_null($_ai_key) or 
		$this[$_ai_key] = $_exec;

		// Obteniendo las posibles relaciones hijo que se hayan agregado mnanualmente
		$rxs_hijo_editeds = [];
		$rxs_hijo = self::rxs_hijo();
		foreach($rxs_hijo as $rx)
		{
			$field = $rx['field'];
			if (in_array($field, $this->_manual_setted))
			{
				$rxs_hijo_editeds[$field] = [
					'data' => $this->_data_instance[$field],
					'rx' => $rx
				];
			}
		}

		$this->select($_sync);

		$_changes[] = [
			'accion' => 'insert',
			'tabla' => self::gcc(),
			'anterior' => [],
			'nuevo' => $this->__toArray(),
		];

		foreach($rxs_hijo_editeds as $_rx)
		{
			$rx = $_rx['rx'];
			$data = $_rx['data'];
			$class = 'Object\\' . $rx['clase'];

			foreach($data as $reg)
			{
				$reg_o = $reg;

				if ( ! is_object($reg_o) or ! is_a($reg_o, $class))
				{
					$reg_d = $reg_o;
					$reg_o = new $class();

					foreach($reg_d as $k => $v)
					{
						$reg_o[$k] = $v;
					}
				}

				foreach($rx['columnas'] as $_padre => $_hijo)
				{
					$reg_o[$_hijo] = $this->_data_instance[$_padre];
				}

				$_exec = $reg_o->insert_update($_sync, $_changes, ($_op_level + 1));
				if ( ! $_exec)
				{
					$_errors = $reg_o->get_errors();
					foreach($_errors as $_error)
					{
						$this->_errors[] = $_error;
					}
					return false;
				}
			}
		}

		sql_trans(true);

		if ($_op_level === 1)
		{
			action_apply('ObjTbl::Changes', $_changes, self::gcc(), $this);
		}

		return TRUE;
	}

    /**
     * insert_forced
	 * Permite hacer una consulta INSERT aún si existe el registro
     */
	public function insert_forced (&$_sync = [], &$_changes = [], $_op_level = 1)
	{
		return $this->insert($_sync, $_changes, $_op_level, true);
	}

    /**
     * update
	 * Permite hacer una consulta UPDATE a la DB 
     */
	public function update (&$_sync = [], &$_changes = [], $_op_level = 1)
	{
		if ( ! $this->_found)
		{
			$this->_errors[] = '[' . self::gcc() . '](' . __FUNCTION__ . ') El objeto no existe aún';
			return false;
		}

		
	}

    /**
     * insert_update
	 * Permite hacer una consulta INSERT si el registro no existe o UPDATE si existe
     */
	public function insert_update (&$_sync = [], &$_changes = [], $_op_level = 1)
	{
		return $this->_found ? $this->update($_sync, $_changes, $_op_level) : $this->insert($_sync, $_changes, $_op_level);
	}

    /**
     * delete
	 * Permite hacer una consulta DELETE a la DB 
     */
	public function delete (&$_sync = [], &$_changes = [], $_op_level = 1)
	{
		if ( ! $this->_found)
		{
			$this->_errors[] = '[' . self::gcc() . '](' . __FUNCTION__ . ') El objeto no existe aún';
			return false;
		}

		
	}

    /**
     * reset
	 * Permite deshacer cualquier cambio realizado hasta el último select realizado
	 * Esto permite que previo a hacer un update o delete, la información regrese a como era el ultimo select
     */
	public function reset ()
	{
		
	}

	public function offsetGet ($index)
	{
		$_founded = NULL;
		$_founded_obj = NULL;

		// Alojando todos los objetos de las cuales alguno de estos campos dependen
		$rxs_padre = self::rxs_padre();
		
		// Alojando las relaciones de objetos que dependen de este objeto
		$rxs_hijo = self::rxs_hijo();
		
		if (is_null($_founded))
		{
			foreach($rxs_padre as $rx)
			{
				if ($rx['field'] === $index)
				{
					$_founded = 'rxs_padre';
					$_founded_obj = $rx;
					break;
				}
			}
		}

		if (is_null($_founded))
		{
			foreach($rxs_hijo as $rx)
			{
				if ($rx['field'] === $index)
				{
					$_founded = 'rxs_hijo';
					$_founded_obj = $rx;
					break;
				}
			}
		}

		if (in_array($_founded_obj['field'], $this->_manual_setted))
		{
			$_founded = NULL;
		}

		if (is_null($_founded))
		{
			return parent::offsetGet($index);
		}

		isset($this->_callbacks['before_get']) and
		$this->_callbacks['before_get']($index, $this);

		isset($this->_callbacks['before_get_' . $index]) and
		$this->_callbacks['before_get_' . $index]($index, $this);

		switch($_founded)
		{
			case 'rxs_padre':
				$return = 'rxs_padre';
				break;
			case 'rxs_hijo':
				$return = 'rxs_hijo';
				break;
		}
		

		isset($this->_callbacks['get']) and
		$this->_callbacks['get']($return, $index, $this);

		isset($this->_callbacks['get_' . $index]) and
		$this->_callbacks['get_' . $index]($return, $index, $this);

		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $index, $this);
        return $return;
    }
	
	public function offsetSet ($index, $newval, $manual = TRUE)
	{
		if ($manual)
		{
			if (is_null($newval))
			{
				$this->_manual_setted = array_diff($this->_manual_setted, [$index]);
			}
			else
			{
				$this->_manual_setted[] = $index;
			}
		}

		return parent::offsetSet($index, $newval);
	}

    function __toArray()
    {
		isset($this->_callbacks['before_toarray']) and
		$this->_callbacks['before_toarray']($this);

		$return = [];
		$fields = self::fields();
		foreach($fields as $field)
		{
			$return[$field] = $this->_data_instance[$field];
		}

		isset($this->_callbacks['toarray']) and
		$this->_callbacks['toarray']($return, $this);
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $this);
		return $return;
    }

    /**
     * count ()
     */
    public function count ()
    {
        return $this->_found ? 1 : 0;
    }
}