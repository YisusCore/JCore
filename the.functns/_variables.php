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

/**
 * DIRECTORY_SEPARATOR
 *
 * Separador de Directorios para el sistema operativo de ejecución
 *
 * @global
 */
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------
// Variables Fijos
//--------------------------------------------------------------------
if ( ! function_exists('numeros'))
{
	/**
	 * numeros()
	 * Obtener los números
	 *
	 * @param int	$n
	 * @return int|array	 si $n es nulo entonces retorna la lista
	 */
	function numeros($n = NULL)
	{
		$return = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('letras'))
{
	/**
	 * letras()
	 * Obtener las letras en minúsculas
	 *
	 * @param int	$n
	 * @return string|array	 si $n es nulo entonces retorna la lista
	 */
	function letras($n = NULL)
	{
		$return = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('letras_may'))
{
	/**
	 * letras_may()
	 * Obtener las letras en mayúscula
	 *
	 * @param int
	 * @return mixed
	 */
	function letras_may($n = NULL)
	{
		$return = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('vocales'))
{
	/**
	 * vocales()
	 * Obtener las vocales
	 *
	 * @param int
	 * @return mixed
	 */
	function vocales($n = NULL)
	{
		$return = ['a', 'e', 'i', 'o', 'u'];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('tildes'))
{
	/**
	 * tildes()
	 * Obtener las tildes (letras) en minúsculas
	 *
	 * @param int
	 * @return mixed
	 */
	function tildes($n = NULL)
	{
		$return = ['á', 'é', 'í', 'ó', 'ú', 'ñ'];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('tildes_may'))
{
	/**
	 * tildes_may()
	 * Obtener las tildes (letras) en minúsculas
	 *
	 * @param int
	 * @return mixed
	 */
	function tildes_may($n = NULL)
	{
		$return = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'];
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('simbolos'))
{
	/**
	 * simbolos()
	 * Obtener las tildes (letras) en minúsculas
	 *
	 * @param int
	 * @param bool
	 * @return mixed
	 */
	function simbolos($n = NULL, $inc_hard = FALSE)
	{
		$return = ['#', '@', '$', '&', '¿', '?', '¡', '!', '%', '=', '+', '-', '*', '_', '/', '\\', '.', ',', ';', ':', '(', ')', '{', '}', '[', ']', '"', '\'', '<', '>'];
		
		if ($inc_hard)
		{
			$return = array_merge($return, ['°', '~', '|']);
		}
		
		if ( ! is_null($n))
		{
			$return = $return[$n];
		}
		return $return;
	}	
}

if ( ! function_exists('meses'))
{
	/**
	 * meses()
	 * Obtener los meses
	 *
	 * @param string
	 * @param string
	 * @param int
	 * @return mixed
	 */
	function meses($mode = NULL, $lang = NULL, $n = NULL)
	{
		$return = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
		
		if (is_null($lang))
		{
			if ( ! is_null($mode) and ! is_numeric($mode) and strlen($mode) == 2)
			{
				$lang = $mode;
				$mode=NULL;
			}
			else
			{
				$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'ES';
			}
		}
		
		if (is_null($mode))
		{
			$mode = 'normal';
		}
		
		foreach ($return as &$mes)
		{
			$mes = _t ($mes, $lang);
		}
		
		if ($mode == 'min.')
		{
			foreach ($return as &$mes)
			{
				$mes = substr($mes, 0, 3) . '.';
			}
		}
		elseif ($mode == 'min')
		{
			foreach ($return as &$mes)
			{
				$mes = substr($mes, 0, 3);
			}
		}
		
		unset($mes);
		
		if ( ! is_null($n))
		{
			$return = $return[(int)$n];
		}
		return $return;
	}	
}

if ( ! function_exists('mes'))
{
	/**
	 * mes()
	 * Obtener un mes
	 *
	 * @param int
	 * @param string
	 * @param string
	 * @return mixed
	 */
	function mes($n, $mode = NULL, $lang = NULL)
	{
		return meses($mode, $lang, $n);
	}	
}

if ( ! function_exists('dias'))
{
	/**
	 * dias()
	 * Obtener los días
	 *
	 * @param string
	 * @param string
	 * @param int
	 * @return mixed
	 */
	function dias($mode = NULL, $lang = NULL, $n = NULL)
	{
		$return = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sabado'];
		
		if (is_null($lang))
		{
			if ( ! is_null($mode) and ! is_numeric($mode) and strlen($mode) == 2)
			{
				$lang = $mode;
				$mode=NULL;
			}
			else
			{
				$lang = isset($_COOKIE['lang']) ? $_COOKIE['lang'] : 'ES';
			}
		}
		
		if (is_null($mode))
		{
			$mode = 'normal';
		}
		
		foreach ($return as &$dia)
		{
			$dia = _t ($dia, $lang);
		}
		
		if ($mode == 'min.')
		{
			foreach ($return as &$dia)
			{
				$dia = substr($dia, 0, 3) . '.';
			}
		}
		elseif ($mode == 'min')
		{
			foreach ($return as &$dia)
			{
				$dia = substr($dia, 0, 3);
			}
		}
		
		unset($dia);
		
		if ( ! is_null($n))
		{
			$return = $return[(int)$n];
		}
		return $return;
	}	
}

if ( ! function_exists('dia'))
{
	/**
	 * dia()
	 * Obtener un día
	 *
	 * @param int
	 * @param string
	 * @param string
	 * @return mixed
	 */
	function dia(int $n, $mode = NULL, $lang = NULL)
	{
		return dias($mode, $lang, $n);
	}	
}


if ( ! function_exists('vTab'))
{
	/**
	 * vTab()
	 * Obtener los caracteres de Tabulación cuantas veces se requiera
	 *
	 * @param int
	 * @return string
	 */
	function vTab (int $n = 1)
	{
		if ($n <= 0)
		{
			return '';
		}
		
		$return = str_repeat("\t", $n);
		return $return;
	}
}
defined('vTab') OR define('vTab', vTab());

if ( ! function_exists('vEnter'))
{
	/**
	 * vEnter()
	 * Obtener los caracteres de Salto de Linea cuantas veces se requiera
	 *
	 * @param int
	 * @return string
	 */
	function vEnter (int $n = 1)
	{
		if ($n <= 0)
		{
			return '';
		}
		
		$return = str_repeat("\r\n", $n);
		return $return;
	}
}
defined('vEnter') OR define('vEnter',vEnter());


//--------------------------------------------------------------------
// Aleatorios
//--------------------------------------------------------------------
if ( ! function_exists('na'))
{
	/**
	 * na()
	 * Obtener un numero aleatorio
	 *
	 * @param int Cantidad de dígitos
	 * @return string
	 */
	function na($digitos = 1)
	{
		static $int_max_digits = 9;
		
		if ($digitos <= 0)
		{
			return '';
		}
		
		$return = '';
		if ($digitos > $int_max_digits)
		{
			$digitos_extras = $digitos - $int_max_digits;
			$return = na($digitos_extras);
			$digitos -= $digitos_extras;
		}
		
		$rand_min = 1;
		if ($digitos > 1)
		{
			$rand_min = pow(10, $digitos - 1);
		}
		$rand_max = pow(10, $digitos) - 1;
		
		return rand($rand_min, $rand_max) . $return;
	}
}

if ( ! function_exists('la'))
{
	/**
	 * la()
	 * Obtener una letra aleatoria
	 *
	 * @param int Cantidad de dígitos
	 * @param bool $min Incluír mínimos
	 * @param bool $may Incluír mayúsculas
	 * @param bool $tilde Incluír Tildes
	 * @return string
	 */
	function la($digitos = 1, bool $min = TRUE, bool $may = TRUE, bool $tilde = FALSE)
	{
		if ($digitos <= 0)
		{
			return '';
		}
		
		$return = '';
		
		if ( ! $min and ! $may and ! $tilde)
		{
			$min = TRUE;
			$may = TRUE;
		}
		
		$letras = [];
		
		if ($min)
		{
			$letras = array_merge($letras, (array)letras());
		}
		
		if ($may)
		{
			$letras = array_merge($letras, (array)letras_may());
		}
		
		if ($tilde)
		{
			if ($min and ! $may)
			{
				$letras = array_merge($letras, (array)tildes());
			}
			elseif ( ! $min and $may)
			{
				$letras = array_merge($letras, (array)tildes_may());
			}
			else
			{
				$letras = array_merge($letras, (array)tildes(), (array)tildes_may());
			}
		}
		
		for($digito = 1; $digito <= $digitos; $digito++)
		{
			$return .= $letras[rand(0, count($letras) - 1)];
		}
		return $return;
	}
}

if ( ! function_exists('sa'))
{
	/**
	 * sa()
	 * Obtener un símbolo aleatorio
	 *
	 * @param int Cantidad de dígitos
	 * @return string
	 */
	function sa($digitos = 1)
	{
		if ($digitos <= 0)
		{
			return '';
		}
		
		$return = '';
		
		$simbolos = (array)simbolos();
		
		for($digito = 1; $digito <= $digitos; $digito++)
		{
			$return .= $simbolos[rand(0, count($simbolos) - 1)];
		}
		return $return;
	}
}

if ( ! function_exists('cs'))
{
	/**
	 * cs()
	 * Obtener un código seguro aleatorio
	 *
	 * @param int Cantidad de dígitos
	 * @param bool $min Incluír mínimos
	 * @param bool $may Incluír mayúsculas
	 * @param bool $tilde Incluír Tildes
	 * @param bool $num Incluír Números
	 * @param bool $sym Incluír Símbolos
	 * @return string
	 */
	function cs($digitos = 60, bool $min = TRUE, bool $may = TRUE, bool $tilde = FALSE, bool $num = TRUE, bool $sym = FALSE, bool $spc = FALSE)
	{
		if ($digitos <= 0)
		{
			return '';
		}
		
		$return = '';
		
		if ( ! $min AND ! $may AND ! $tilde AND ! $num AND ! $sym)
		{
			$min = TRUE;
			$may = TRUE;
			$num = TRUE;
		}
		
		for($digito = 1; $digito <= $digitos; $digito++)
		{
			switch(rand(1,10))
			{
				case 1:case 4:case 7://Letra
					if ( ! $min AND ! $may AND ! $tilde)
					{
						$digito--;
						break;
					}
					
					$return .= la(1, $min, $may, $tilde);
					break;

				case 2:case 5:case 8://Número
					if ( ! $num)
					{
						$digito--;
						break;
					}
					
					$return .= na(1);
					break;

				case 3:case 6:case 9://Símbolo
					if ( ! $sym)
					{
						$digito--;
						break;
					}
					
					$return .= sa(1);
					break;
				
				case 10://Space
					if ( ! $spc)
					{
						$digito--;
						break;
					}
					
					$return .= ' ';
					break;
					
			}
		}
		
		return $return;
	}
}

if ( ! function_exists('licencia'))
{
	/**
	 * licencia()
	 * Obtener un licencia aleatoria
	 *
	 * @param bool Modo genérico
	 * @return string
	 */
	function licencia($generic = FALSE)
	{
		$licencia = array(
			cs( $generic ? 5 : 6, FALSE, TRUE, FALSE, TRUE, FALSE),
			cs(5, FALSE, TRUE, FALSE, TRUE, FALSE),
			cs(5, FALSE, TRUE, FALSE, TRUE, FALSE),
			cs(5, FALSE, TRUE, FALSE, TRUE, FALSE),
			cs( $generic ? 5 : 9, FALSE, TRUE, FALSE, TRUE, FALSE)
		);
		
		return implode('-', $licencia);
	}
}

if ( ! function_exists('pswd'))
{
	/**
	 * pswd()
	 * Obtener una clave aleatoria
	 *
	 * @param int Cantidad de dígitos
	 * @return string
	 */
	function pswd($digitos = NULL)
	{
		if (is_null($digitos))
		{
			$digitos = rand(10, 15);
		}
		elseif ($digitos > 16)
		{
			$digitos = 16;
		}
		elseif ($digitos < 5)
		{
			$digitos = 5;
		}

		return cs($digitos, TRUE, TRUE, TRUE, TRUE, TRUE);
	}
}

if ( ! function_exists('pswd_percent'))
{
	/**
	 * pswd_percent()
	 * Obtener el porcentaje de seguridad de una clave
	 *
	 * Función pswd -> estadísticas de seguridad buena
	 *  -----------------------	    -----------------------	    -----------------------	    -----------------------
	 *  Dígitos	16	                Dígitos	15	                Dígitos	14					Dígitos	13
	 *  Percent	100.00	            Percent	100.00	            Percent	100.00	            Percent	100.00
	 *  Securit	99.99	            Securit	99.98	            Securit	99.74	            Securit	99.49
	 *  Hacking	16.32	            Hacking	15.95	            Hacking	15.19	            Hacking	14.15
	 *  -----------------------	    -----------------------	    -----------------------	    -----------------------
	 *  Dígitos	12	                Dígitos	11	                Dígitos	10	                Dígitos	9
	 *  Percent	100.00	            Percent	99.90	            Percent	99.85	            Percent	99.35
	 *  Securit	98.02	            Securit	95.33	            Securit	90.21	            Securit	82.89
	 *  Hacking	13.32	            Hacking	12.17	            Hacking	11.91	            Hacking	11.48
	 *  -----------------------	    -----------------------	    -----------------------	    -----------------------
	 *  Dígitos	8	                Dígitos	7					Dígitos	6					Dígitos	5
	 *  Percent	93.80	            Percent	82.65	            Percent	65.65	            Percent	49.90
	 *  Securit	74.06	            Securit	63.24	            Securit	54.09	            Securit	45.71
	 *  Hacking	20.68	            Hacking	20.90	            Hacking	19.75	            Hacking	17.97
	 *  -----------------------	    -----------------------	    -----------------------	    -----------------------
	 */
	function pswd_percent($pswd)
	{
		$pswd = utf8($pswd);
		$pswd_wo_tildes = replace_tildes($pswd);
		
		$calcs = [];
		$calcs['largo']  = mb_strlen($pswd);
		$calcs['mayus']  = mb_strlen(preg_replace('/[^A-Z]/', '', $pswd));
		$calcs['minus']  = mb_strlen(preg_replace('/[^a-z]/', '', $pswd));
		$calcs['numes']  = mb_strlen(preg_replace('/[^0-9]/', '', $pswd));
		$calcs['simbs']  = mb_strlen(preg_replace('/[a-z0-9 ]/i', '', $pswd_wo_tildes));
		
		$calcs['only_letters'] = only_letters($pswd);
		$calcs['only_numbers'] = only_numbers($pswd);
		
		$calcs['num_symb'] = 0;
		$calcs['reqs'] = 0;
		
		$calcs['repetidos'] = 0;//caracteres repetidos
		$calcs['consecutivo_may'] = 0;
		$calcs['consecutivo_min'] = 0;
		$calcs['consecutivo_num'] = 0;
		$calcs['secuencia_let'] = 0;
		$calcs['secuencia_num'] = 0;
		$calcs['palabras'] = 0;//Palabras comunes
		
		extract($calcs, EXTR_REFS);
		
		$num_symb = $numes + $simbs - 1;
		if ($only_letters OR $only_numbers)
		{
			$num_symb--;
		}
		if ($num_symb < 0)
		{
			$num_symb = 0;
		}
		
		if($largo >= 8)
		{
			$reqs++;
		}
        if($mayus>=1)
		{
			$reqs++;
		}
        if($minus>=1)
		{
			$reqs++;
		}
        if($numes>=1)
		{
			$reqs++;
		}
        if($simbs>=1)
		{
			$reqs++;
		}
		
		$letras = (array)letras();
		
		$char_h = ['', '', ''];//Historial
		$char_c = [];//Cantidad
		for ($i=0; $i<=$largo; $i++)
		{
			$char   = mb_substr($pswd, $i, 1);
			$char_l = mb_strtolower($char);
			
			$char_h[0] = $char_h[1];
			$char_h[1] = $char_h[2];
			$char_h[2] = $char;
			
			isset($char_c[$char]) or $char_c[$char_l] = 0;
			$char_c[$char_l]++;
			
			//letras_mayusculas_consecutivas
			if (preg_match('/^[A-Z]$/', $char_h[2]) AND preg_match('/^[A-Z]$/', $char_h[1]))
			{
				$consecutivo_may++;
			}
			
			//letras_minusculas_consecutivas
			if (preg_match('/^[a-z]$/', $char_h[2]) AND preg_match('/^[a-z]$/', $char_h[1]))
			{
				$consecutivo_min++;
			}
			
			//numeros_consecutivos
			if (preg_match('/^[0-9]$/', $char_h[2]) AND preg_match('/^[0-9]$/', $char_h[1]))
			{
				$consecutivo_num++;
			}
			
			
			//secuencia_letras
			if ( ! empty($char_h[0]) AND ! empty($char_h[1]))
			{
				if (preg_match('/^[a-z]$/', mb_strtolower($char_h[2])) AND 
					preg_match('/^[a-z]$/', mb_strtolower($char_h[1])) AND 
					preg_match('/^[a-z]$/', mb_strtolower($char_h[0])))
				{
					$i2 = array_keys($letras, mb_strtolower($char_h[2]), true);
					$i2 = $i2[0]; 
					
					$i1 = array_keys($letras, mb_strtolower($char_h[1]), true);
					$i1 = $i1[0];
					
					$i0 = array_keys($letras, mb_strtolower($char_h[0]), true);
					$i0 = $i0[0];
					
					if( (($i2 - 2) == ($i1 - 1) and ($i1 - 1) == ($i0 - 0)) or (($i2 - 0) == ($i1 - 1) and ($i1 - 1) == ($i0 - 2)) )
					{
						$secuencia_let++;
					}
				}
				
				if (preg_match('/^[0-9]$/', $char_h[2]) AND 
					preg_match('/^[0-9]$/', $char_h[1]) AND 
					preg_match('/^[0-9]$/', $char_h[0]))
				{
					$i2 = (int)$char_h[2];
					$i1 = (int)$char_h[1];
					$i0 = (int)$char_h[0];
					
					if( (($i2 - 2) == ($i1 - 1) and ($i1 - 1) == ($i0 - 0)) or (($i2 - 0) == ($i1 - 1) and ($i1 - 1) == ($i0 - 2)) )
					{
						$secuencia_num++;
					}
				}
			}
		}
		
		foreach (array_values($char_c) as $cantidad)
		{
			$repetidos += $cantidad * ($cantidad - 1) * 1;
		}
		
		$pswd_wo_tildes_lower = mb_strtolower($pswd_wo_tildes);
		$pswd_wo_tildes_nonum = mb_strtolower(replace_tildes($pswd, TRUE));
		
		static $common_words = [
			'password', '123456', '12345678', '2580', '0258', '123456789', '111111', 'iloveyou', '123123', 'adobe123', 'admin', 'root', '1234', 'photoshop', 'shadow', '12345', 'princess', '00000', 'azerty', 'qwerty', 'abc123', 'letmein', 'monkey', 'myspace1', 'password1', 'blink182', 'clave', 'link182', 'dios', 'love', 'god', 'amor'
		];
		$_regex = '/(' . implode('|', $common_words).')/';
		
		preg_match_all($_regex, $pswd_wo_tildes_lower, $matches);
		$palabras += count($matches[0]);
		
		preg_match_all($_regex, $pswd_wo_tildes_nonum, $matches);
		$palabras += count($matches[0]);
		
		$sumas  = 0;
		$restas = 0;
		
		$sumas += ($largo)            * 1;
		$sumas += ($largo - $mayus)   * 2;
		$sumas += ($largo - $minus)   * 2;
		$sumas += ($numes)            * 3;
		$sumas += ($simbs)            * 4;
		$sumas += ($num_symb)         * 3;
		$sumas += ($reqs)             * 2;
		
		$restas += ($only_letters)    * 2;
		$restas += ($only_numbers)    * 2;
		$restas += ($repetidos)       * 3;
		$restas += ($consecutivo_may) * 4;
		$restas += ($consecutivo_min) * 4;
		$restas += ($consecutivo_num) * 5;
		$restas += ($secuencia_let)   * 6;
		$restas += ($secuencia_num)   * 6;
		$restas += ($palabras)        * 8;
		
		$chars_q = (int)$largo;
			if ($chars_q ==  1)
		{
			$chars_q = 16;
		}
		elseif ($chars_q <=  2)
		{
			$chars_q = 8;
		}
		elseif ($chars_q <=  4)
		{
			$chars_q = 4;
		}
		elseif ($chars_q <=  8)
		{
			$chars_q = 2;
		}
		elseif ($chars_q <= 16)
		{
			$chars_q = 1;
		}
		elseif ($chars_q <= 32)
		{
			$chars_q = .5;
		}
		elseif ($chars_q <= 64)
		{
			$chars_q = .25;
		}
		else
		{
			$chars_q = .1;
		}
		
		$kacking = (($chars_q * $restas) / $sumas) * 100;
		if ($kacking > 100)
		{
			$kacking = 100;
		}
		
		$result = 0;
		$result_dats = [
			'seguridad' => number_format($sumas > 100 ? 100 : $sumas, 2),
			'hacking'   => number_format($kacking, 2)
		];
		
		if($result_dats['seguridad'] >= 75)
		{
			if($result_dats['hacking'] <= 35)
			{
				$result = 3;
			}
			elseif($result_dats['hacking'] <= 60)
			{
				$result = 2;
			}
			elseif($result_dats['hacking'] <= 75)
			{
				$result = 1;
			}
			else
			{
				$result = 0;
			}
		}
		elseif($result_dats['seguridad'] >= 60)
		{
			if($result_dats['hacking'] <= 25)
			{
				$result = 2;
			}elseif($result_dats['hacking'] <= 50)
			{
				$result = 1;
			}elseif($result_dats['hacking'] <= 65)
			{
				$result = 0;
			}else
			{
				$result = -1;
			}
		}
		elseif($result_dats['seguridad'] >= 45)
		{
			if($result_dats['hacking'] <= 15)
			{
				$result = 1;
			}elseif($result_dats['hacking'] <= 40)
			{
				$result = 0;
			}elseif($result_dats['hacking'] <= 55)
			{
				$result = -1;
			}else{
				$result = -2;
			}
		}
		elseif($result_dats['seguridad'] >= 35)
		{
			if($result_dats['hacking'] <= 5)
			{
				$result = 0;
			}elseif($result_dats['hacking'] <= 30)
			{
				$result = -1;
			}elseif($result_dats['hacking'] <= 45)
			{
				$result = -2;
			}else{
				$result = -3;
			}
		}
		else
		{
			$result = -3;
		}
		
		$return = [
			'result' => $result,
			'result_dats' => $result_dats,
//			'detalle' => [
//				'sumas' => $sumas,
//				'restas' => $restas,
//				'calcs' => $calcs,
//			],
//			'pswd' => $pswd
		];
		
		return $return;
	}
}
