<?php
/*
 * JCore
 *
 * Entorno de desarrollo para aplicaciones en PHP
 *
 * Copyright (c) 2018 - 2022, JYS Perú
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
 */

defined('ABSPATH') or exit('Acceso directo al archivo no autorizado.');

/**
 * Clase BasicException
 * Extensión de la clase Exception con funciones extras
 *
 * @package		JCore\classes
 * @author		YisusCore
 * @link		https://jcore.jys.pe/guia/classes/basicexception
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2022, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

class BasicException extends Exception {
	
	protected $meta = [];
	
	public function __construct(string $message = '', int $code = 0, array $meta = [], Throwable $previous = NULL)
	{
		$this->meta = $meta;
		parent::__construct($message, $code, $previous);
	}
	
	public function getMeta()
	{
		return $this->meta;
	}
	
	public function setMeta($meta)
	{
		$this->meta = $meta;
	}
}
