<?php
/**
 * JCore.php
 * 
 * El núcleo inicializa todas las funciones básicas y todas las configuraciones mínimas.
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
 * @package		JCore
 * @author		YisusCore
 * @link		https://jcore.jys.pe/jcore
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


class BenchMark implements ArrayAccess, Iterator
{
	//-------------------------------------------
	// Statics
	//-------------------------------------------
	static $instance;
	
	static function &instance()
	{
		if ( ! isset(self::$instance))
		{
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	//-------------------------------------------
	// Variables
	//-------------------------------------------
	protected $points = [];
	private $position = 0;
	
	//-------------------------------------------
	// Constructor
	//-------------------------------------------
	protected function __construct()
	{
		$this->position = 0;
	}
	
	//-------------------------------------------
	// Funciones
	//-------------------------------------------
	public function mark ($key)
	{
		$this->points[$key] = microtime(TRUE);
	}
	
	public function between ($first = NULL, $second = NULL, $decimals = 4)
	{
		if (is_null($first))
		{
			return '{elapsed_time}';
		}
		
		if ( ! isset($this->points[$first]))
		{
			return '';
		}
		
		if ( ! isset($this->points[$second]))
		{
			$this->points[$second] = microtime(TRUE);
		}
		
		return number_format($this->points[$second] - $this->points[$first], $decimals);
	}
	
	//-------------------------------------------
	// Array Access
	//-------------------------------------------
	public function offsetExists ($offset)
	{
		return isset($this->points[$offset]);
	}
	
	public function offsetGet ($offset)
	{
		return $this->points[$offset];
	}
	
	public function offsetSet ($offset, $value)
	{
		$this->points[$offset] = $value;
	}
	
	public function offsetUnset ($offset)
	{
		unset ($this->points[$offset]);
	}
	
	//-------------------------------------------
	// Iterator
	//-------------------------------------------
    public function rewind() 
	{
        $this->position = 0;
    }

    public function current() 
	{
		$keys = array_keys($this->points);
        return $keys[$this->position];
    }

    public function key() 
	{
        return $this->position;
    }

    public function next() 
	{
        ++$this->position;
    }

    public function valid() 
	{
		$keys = array_keys($this->points);
        return isset($keys[$this->position]);
    }
}
