<?php
/**
 * _variables.php
 * 
 * El archivo `_variables` contiene funciones de conjuntos de Variables
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
 * @link		https://jcore.jys.pe/functions/_variables
 * @version		1.0.0
 * @copyright	Copyright (c) 2018 - 2023, JYS Perú (https://www.jys.pe/)
 * @filesource
 */

defined('ABSPATH') or exit('Acceso directo al archivo no autorizado');

class Crypter
{
	private static $instance;
	public static function instance ()
	{
		isset(self::$instance) or self::$instance = new self();
		
		return self::$instance;
	}
	
	/**
	 * Encrypt a message
	 * 
	 * @param string $message - message to encrypt
	 * @param string $key - encryption key
	 * @return string
	 */
	function encrypt(string $message, string $key): string
	{
		$iv = random_bytes (16);
		$key = $this -> getKey($key);

		$encrypted = $this -> sign(openssl_encrypt($message, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv), $key);
		return bin2hex($iv) . bin2hex($encrypted);
	}

	/**
	 * Decrypt a message
	 * 
	 * @param string $encrypted - message encrypted with safeEncrypt()
	 * @param string $key - encryption key
	 * @return string
	 */
	function decrypt(string $encrypted, string $key): string
	{
		$iv = hex2bin(substr($encrypted, 0, 32));
		$data = hex2bin(substr($encrypted, 32));

		$key = $this -> getKey($key);

		if ( ! $this -> verify($data, $key))
		{
			return '';
		}

		return openssl_decrypt(mb_substr($data, 64, null, '8bit'), 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);
	}

	
	private function sign ($message, $key) {
		return hash_hmac('sha256', $message, $key) . $message;
	}

	private function verify($bundle, $key) {
		return hash_equals(
		  hash_hmac('sha256', mb_substr($bundle, 64, null, '8bit'), $key),
		  mb_substr($bundle, 0, 64, '8bit')
		);
	}

	private function getKey($key, $keysize = 16) {
		return hash_pbkdf2('sha256', $key, 'some_token', 100000, $keysize, true);
	}
}

/**
 * Encrypt a message
 * 
 * @param string $message - message to encrypt
 * @param string $key - encryption key
 * @return string
 */
if ( ! function_exists('encrypt'))
{
	function encrypt(string $message, string $key): string
	{
		return Crypter::instance() -> encrypt($message, $key);
	}
}

/**
 * Decrypt a message
 * 
 * @param string $encrypted - message encrypted with safeEncrypt()
 * @param string $key - encryption key
 * @return string
 */
if ( ! function_exists('decrypt'))
{
	function decrypt(string $encrypted, string $key): string
	{
		return Crypter::instance() -> decrypt($encrypted, $key);
	}
}
