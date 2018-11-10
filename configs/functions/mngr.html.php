<?php
/**
 * mngr.html.php
 * 
 * El archivo `mngr.html` permite gestionar todos los contenidos HTML
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
 * @link		https://jcore.jys.pe/functions/mngr.html
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
 * $HTML_css
 * Variable que almacena todas los estilos cargados
 * @internal
 */
$HTML_css = [];

if ( ! function_exists('register_css'))
{
	function register_css ($codigo, $uri = NULL, $version = NULL, $prioridad = NULL, $attr = NULL, $position = NULL)
	{
		global $HTML_css;

		//===========================================
		// ARREGLANDO LOS PARAMETROS
		//===========================================
		if (is_array($uri))
		{
			is_null($attr) and $attr = $uri;
			$uri = NULL;
		}
		
		if (is_null($uri))
		{
			$uri = $codigo;
			
			$path = parse_url($uri, PHP_URL_PATH);
			$codigo = preg_replace('/\.min$/i', '', basename($path, '.css'));
		}

		isset($HTML_css[$codigo]) or $HTML_css[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => 'BODY',
			'loaded'    => FALSE,
			'inline'    => FALSE,
		];

		if ( ! is_version($version))
		{
			$prioridad = $version;
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$version = NULL;
		}
		
		is_null($version) and $version = $HTML_css[$codigo]['version'];
		
		if (is_null($HTML_css[$codigo]['version']) or version_compare($HTML_css[$codigo]['version'], $version, '<'))
		{
			$uri = $HTML_css[$codigo]['uri'];
		}
		
		if ( ! is_numeric($prioridad))
		{
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = $HTML_css[$codigo]['prioridad'];
		
		if ( ! is_array($attr))
		{
			$position = 'BODY';
			$attr = NULL;
		}
		
		is_array($attr) or $attr = $HTML_css[$codigo]['attr'];
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = $HTML_css[$codigo]['position'];

		$HTML_css[$codigo] = array_merge($HTML_css[$codigo], [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => $version,
			'prioridad' => $prioridad,
			'attr'      => $attr,
			'position'  => $position,
		]);
	}
}

if ( ! function_exists('add_css'))
{
	function add_css ($codigo, $uri = NULL, $version = NULL, $prioridad = NULL, $attr = NULL, $position = NULL)
	{
		global $HTML_css;

		//===========================================
		// ARREGLANDO LOS PARAMETROS
		//===========================================
		if (is_array($uri))
		{
			is_null($attr) and $attr = $uri;
			$uri = NULL;
		}
		
		if (is_null($uri))
		{
			$uri = $codigo;
			
			$path = parse_url($uri, PHP_URL_PATH);
			$codigo = preg_replace('/\.min$/i', '', basename($path, '.css'));
		}

		isset($HTML_css[$codigo]) or $HTML_css[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => 'BODY',
			'loaded'    => FALSE,
			'inline'    => FALSE,
		];

		if ( ! is_version($version))
		{
			$prioridad = $version;
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$version = NULL;
		}
		
		is_null($version) and $version = $HTML_css[$codigo]['version'];
		
		if (is_null($HTML_css[$codigo]['version']) or version_compare($HTML_css[$codigo]['version'], $version, '<'))
		{
			$uri = $HTML_css[$codigo]['uri'];
		}
		
		if ( ! is_numeric($prioridad))
		{
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = $HTML_css[$codigo]['prioridad'];
		
		if ( ! is_array($attr))
		{
			$position = 'BODY';
			$attr = NULL;
		}
		
		is_array($attr) or $attr = $HTML_css[$codigo]['attr'];
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = $HTML_css[$codigo]['position'];

		$HTML_css[$codigo] = array_merge($HTML_css[$codigo], [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => $version,
			'prioridad' => $prioridad,
			'attr'      => $attr,
			'position'  => $position,
			'loaded'    => TRUE,
		]);
	}
}

if ( ! function_exists('add_inline_css'))
{
	function add_inline_css ($content, $prioridad = 80, $position = 'BODY')
	{
		global $HTML_css;

		static $codes = [];
		
		if ( ! is_numeric($prioridad))
		{
			$position = $prioridad;
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = 80;
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = 'BODY';

		isset($codes[$position.'-'.$prioridad]) or $codes[$position.'-'.$prioridad] = cs(50);
		
		$codigo = $codes[$position.'-'.$prioridad];

		isset($HTML_css[$codigo]['uri']) and $content = $HTML_css[$codigo]['uri'] . $content;
		
		$HTML_css[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $content,
			'version'   => NULL,
			'prioridad' => $prioridad,
			'attr'      => [],
			'position'  => $position,
			'loaded'    => TRUE,
			'inline'    => TRUE,
		];
	}
}

/**
 * $HTML_js
 * Variable que almacena todas los estilos cargados
 * @internal
 */
$HTML_js = [];

if ( ! function_exists('register_js'))
{
	function register_js ($codigo, $uri = NULL, $version = NULL, $prioridad = NULL, $attr = NULL, $position = NULL)
	{
		global $HTML_js;

		//===========================================
		// ARREGLANDO LOS PARAMETROS
		//===========================================
		if (is_array($uri))
		{
			is_null($attr) and $attr = $uri;
			$uri = NULL;
		}
		
		if (is_null($uri))
		{
			$uri = $codigo;
			
			$path = parse_url($uri, PHP_URL_PATH);
			$codigo = preg_replace('/\.min$/i', '', basename($path, '.js'));
		}

		isset($HTML_js[$codigo]) or $HTML_js[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => 'BODY',
			'loaded'    => FALSE,
			'inline'    => FALSE,
		];

		if ( ! is_version($version))
		{
			$prioridad = $version;
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$version = NULL;
		}
		
		is_null($version) and $version = $HTML_js[$codigo]['version'];
		
		if (is_null($HTML_js[$codigo]['version']) or version_compare($HTML_js[$codigo]['version'], $version, '<'))
		{
			$uri = $HTML_js[$codigo]['uri'];
		}
		
		if ( ! is_numeric($prioridad))
		{
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = $HTML_js[$codigo]['prioridad'];
		
		if ( ! is_array($attr))
		{
			$position = 'BODY';
			$attr = NULL;
		}
		
		is_array($attr) or $attr = $HTML_js[$codigo]['attr'];
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = $HTML_js[$codigo]['position'];

		$HTML_js[$codigo] = array_merge($HTML_js[$codigo], [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => $version,
			'prioridad' => $prioridad,
			'attr'      => $attr,
			'position'  => $position,
		]);
	}
}

if ( ! function_exists('add_js'))
{
	function add_js (string $codigo, $uri = NULL, $version = NULL, $prioridad = NULL, $attr = NULL, $position = NULL)
	{
		global $HTML_js;

		//===========================================
		// ARREGLANDO LOS PARAMETROS
		//===========================================
		if (is_array($uri))
		{
			is_null($attr) and $attr = $uri;
			$uri = NULL;
		}
		
		if (is_null($uri))
		{
			$uri = $codigo;
			
			$path = parse_url($uri, PHP_URL_PATH);
			$codigo = preg_replace('/\.min$/i', '', basename($path, '.js'));
		}

		isset($HTML_js[$codigo]) or $HTML_js[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => 'BODY',
			'loaded'    => FALSE,
			'inline'    => FALSE,
		];

		if ( ! is_version($version))
		{
			$prioridad = $version;
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$version = NULL;
		}
		
		is_null($version) and $version = $HTML_js[$codigo]['version'];
		
		if (is_null($HTML_js[$codigo]['version']) or version_compare($HTML_js[$codigo]['version'], $version, '<'))
		{
			$uri = $HTML_js[$codigo]['uri'];
		}
		
		if ( ! is_numeric($prioridad))
		{
			is_null($attr) and $attr = $position;
			$position = 'BODY';
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = $HTML_js[$codigo]['prioridad'];
		
		if ( ! is_array($attr))
		{
			$position = 'BODY';
			$attr = NULL;
		}
		
		is_array($attr) or $attr = $HTML_js[$codigo]['attr'];
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = $HTML_js[$codigo]['position'];

		$HTML_js[$codigo] = array_merge($HTML_js[$codigo], [
			'codigo'    => $codigo,
			'uri'       => $uri,
			'version'   => $version,
			'prioridad' => $prioridad,
			'attr'      => $attr,
			'position'  => $position,
			'loaded'    => TRUE,
		]);
	}
}

if ( ! function_exists('add_inline_js'))
{
	function add_inline_js ($content, $prioridad = 80, $position = 'BODY')
	{
		global $HTML_js;

		static $codes = [];
		
		if ( ! is_numeric($prioridad))
		{
			$position = $prioridad;
			$prioridad = NULL;
		}
		
		is_numeric($prioridad) or $prioridad = 80;
		
		in_array(mb_strtoupper($position), ['HEAD', 'BODY']) or $position = 'BODY';

		isset($codes[$position.'-'.$prioridad]) or $codes[$position.'-'.$prioridad] = cs(50);
		
		$codigo = $codes[$position.'-'.$prioridad];

		isset($HTML_js[$codigo]['uri']) and $content = $HTML_js[$codigo]['uri'] . ';' . $content;
		
		$HTML_js[$codigo] = [
			'codigo'    => $codigo,
			'uri'       => $content,
			'version'   => NULL,
			'prioridad' => $prioridad,
			'attr'      => [],
			'position'  => $position,
			'loaded'    => TRUE,
			'inline'    => TRUE,
		];
	}
}


if ( ! function_exists('assets_reordered'))
{
	function assets_reordered($assets){
		$list = [];
		
		foreach($assets as $_item)
		{
			extract($_item);
			
			isset($list[$prioridad]) or $list[$prioridad] = [];
			$list[$prioridad][] = $_item;
		}
		
		$return = [];
		
		foreach($list as $prioridad => $assets)
		{
			foreach($assets as $_item)
			{
				$return[] = $_item;
			}
		}
		
		return $return;
	}
}

if ( ! function_exists('html_esc'))
{
	function html_esc($str){
		return htmlspecialchars($str);
	}
}

if ( ! function_exists('extracto'))
{
	//$tags_allowed = '<a><p>'
	function extracto($str, $lenght = 50, $position = 1, $dots = '&hellip;', $tags_allowed = ''){
		// Strip tags
		$html = trim(strip_tags($str, $tags_allowed));
		$strn = trim(strip_tags($str));
		$inc_tag = FALSE;
		
		if (mb_strlen($html) > mb_strlen($strn))
		{
			$inc_tag = TRUE;
			$o = 0;
			$v = [];
			for($i=0; $i<=mb_strlen($html); $i++)
			{
				$html_char = mb_substr($html, $i, 1);
				$strn_char = mb_substr($strn, $i, 1);
				
				if ($html_char == '<')
				{
					$tag = '';
					$c = 0;
					
					do
					{
						$html_char = mb_substr($html, $i + $c, 1);
						$tag .= $html_char;
						
						$c++;
					}while($html_char <> '>');
					
					$v[$o] = $tag;
					$i+=$c - 1;
				}
				else
				{
					$o++;
				}
			}
		}
		
		// Is the string long enough to ellipsize?
		if (mb_strlen($strn) <= $lenght)
		{
			return $html;
		}
		
		$position = $position > 1 ? 1 : ($position < 0 ? 0 : $position);
		
		$beg = mb_substr($strn, 0, floor($lenght * $position));
		if ($position === 1)
		{
			$end = mb_substr($strn, 0, -($lenght - mb_strlen($beg)));
		}
		else
		{
			$end = mb_substr($strn, -($lenght - mb_strlen($beg)));
		}
		
		if ($inc_tag)
		{
			$beg_e = mb_strlen($beg);
			$end_s = mb_strlen($end);
			$spc_l = mb_strlen($strn) - $end_s - $beg_e;
			$end_s = $beg_e + $spc_l;

			$return = '';
			$opened_lvl = 0;
			for($i=0; $i<=mb_strlen($strn); $i++)
			{
				if ($i>=$beg_e and $i<$end_s)
				{
					while($opened_lvl > 0)
					{
						for($ti = $beg_e; $ti <= $end_s; $ti++)
						{
							if (isset($v[$ti]))
							{
								if ($v[$ti][1] == '/')
								{
									$opened_lvl--;
								}
								else
								{
									$opened_lvl++;
								}

								$is_br = preg_match('#<br( )*(/){0,1}>#', $v[$ti]);
								if ($is_br)
								{
									$opened_lvl--;
									continue;
								}
					
								$return .= $v[$ti];
							}
						}
					}
					
					$return .= $dots;
					$i += $spc_l - 1;
					continue;
				}
				
				$char = mb_substr($strn, $i, 1);

				if (isset($v[$i]))
				{
					if ($v[$i][1] == '/')
					{
						$opened_lvl--;
					}
					else
					{
						$opened_lvl++;
					}
					
					$is_br = preg_match('#<br( )*(/){0,1}>#', $v[$i]);
					if ($is_br)
					{
						$opened_lvl--;
					}
					
					$return .= $v[$i];
				}
				
				if ($i < $beg_e or $i >= $end_s)
				{
					$return .= $char;
				}
			}

			return $return;
		}
		else
		{
			return $beg . $dots . $end;
		}
	}
}

if ( ! function_exists('youtube'))
{
	/**
	 * Obtener el código de YouTube
	 */
	function youtube($link)
	{
		static $_regex = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';

		if ( ! preg_match($_regex, $link, $v))
		{
			return preg_match('youtu', $link) ? NULL : $link;
		}
		
		return $v[1];
	}
}

if ( ! function_exists('compare'))
{
	function compare($str, $txt, $success = 'selected="selected"', $echo = TRUE)
	{
		$equal = $str == $txt;
		
		if ($equal)
		{
			if (is_callable($success))
			{
				$success = $success($str, $txt, $echo);
			}
			
			$success = (string)$success;
			
			if ($echo)
			{
				echo  $success;
				return TRUE;
			}
			else
			{
				return $success;
			}
		}
		
		if ($echo)
		{
			echo  '';
			return FALSE;
		}
		else
		{
			return '';
		}
	}
}


//=======================================
// PARTES DE HTML
//=======================================
if ( ! function_exists('html_row'))
{
	function html_row($body, $opts = [])
	{
		is_array($opts) or $opts = (array)$opts;
		
		$opts = array_merge([
			'main_tag' => 'div',
			'main_class' => NULL,
			'main_id' => NULL,
		], $opts);
		
		return _o(function() use ($body, $opts){
			extract($opts);
			
			echo '<' . $main_tag;
			
			$force_class = ['row'];
			is_array ($main_class) and $main_class = implode(' ', $main_class);
			$main_class = trim(implode(' ', $force_class) . ' ' .$main_class);
			
			echo ' class="' . htmlspecialchars($main_class) . '"';
			
			if ( ! empty($main_id))
			{
				echo ' id="' . htmlspecialchars($main_id) . '"';
			}
			echo '>';
			
			echo $body;

			echo '</' . $main_tag . '>';
			
		});
	}
}

if ( ! function_exists('html_col'))
{
	function html_col($body, $sm = NULL, $md = NULL, $lg = NULL, $xl = NULL, $xs = NULL, $opts = [])
	{
		is_array($opts) or $opts = (array)$opts;
		
		$opts = array_merge([
			'main_tag' => 'div',
			'main_class' => NULL,
			'main_id' => NULL,
		], $opts);
		
		return _o(function() use ($body, $xs, $sm, $md, $lg, $xl, $opts){
			extract($opts);
			
			echo '<' . $main_tag;
			
			$force_class = [];
			$force_class[] = 'col' . (is_null($xs) ? '' : '-' . $xs);
			is_null($sm) or $force_class[] = 'col-sm-' . $sm;
			is_null($md) or $force_class[] = 'col-md-' . $md;
			is_null($lg) or $force_class[] = 'col-lg-' . $lg;
			is_null($xl) or $force_class[] = 'col-xl-' . $xl;
			
			is_array ($main_class) and $main_class = implode(' ', $main_class);
			$main_class = trim(implode(' ', $force_class) . ' ' .$main_class);
			
			echo ' class="' . htmlspecialchars($main_class) . '"';
			
			if ( ! empty($main_id))
			{
				echo ' id="' . htmlspecialchars($main_id) . '"';
			}
			echo '>';
			
			echo $body;

			echo '</' . $main_tag . '>';
			
		});
	}
}

if ( ! function_exists('html_widget'))
{
	function html_widget($body, $title = NULL, $opts = [])
	{
		is_array($opts) or $opts = (array)$opts;
		
		$opts = array_merge([
			'card_tag' => 'div',
			'card_class' => 'card',
			'card_id' => NULL,
			'card_header_tag' => 'div',
			'card_header_class' => 'card-header',
			'card_title_tag' => 'h3',
			'card_title_class' => 'card-title',
			'card_body_tag' => 'div',
			'card_body_class' => 'card-body',
		], $opts);
		
		return _o(function() use ($body, $title, $opts){
			extract($opts);
			
			echo '<' . $card_tag;
			
			if ( ! empty($card_class))
			{
				is_array ($card_class) and $card_class = implode(' ', $card_class);
				echo ' class="' . htmlspecialchars($card_class) . '"';
			}
			
			if ( ! empty($card_id))
			{
				echo ' id="' . htmlspecialchars($card_id) . '"';
			}
			echo '>';
			
			if ( ! empty($title))
			{
				echo '<' . $card_header_tag;

				if ( ! empty($card_header_class))
				{
					is_array ($card_header_class) and $card_header_class = implode(' ', $card_header_class);
					echo ' class="' . htmlspecialchars($card_header_class) . '"';
				}

				echo '>';
				
					echo '<' . $card_title_tag;

					if ( ! empty($card_title_class))
					{
						is_array ($card_title_class) and $card_title_class = implode(' ', $card_title_class);
						echo ' class="' . htmlspecialchars($card_title_class) . '"';
					}

					echo '>';
				
					echo $title;

					echo '</' . $card_title_tag . '>';

				echo '</' . $card_header_tag . '>';
			}
			
			if ( ! empty($body))
			{
				echo '<' . $card_body_tag;

				if ( ! empty($card_body_class))
				{
					is_array ($card_body_class) and $card_body_class = implode(' ', $card_body_class);
					echo ' class="' . htmlspecialchars($card_body_class) . '"';
				}

				echo '>';
				
				echo '<div>' . $body . '</div>';

				echo '</' . $card_body_tag . '>';
			}

			echo '</' . $card_tag . '>';
			
		});
	}
}



//=======================================
// COMPRESORES
//=======================================
if ( ! function_exists('html_compressor'))
{
	function html_compressor($buffer)
	{
//		return $buffer;
		$search = [
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		];
		
		$replace = [
			'>',
			'<',
			'\\1',
			''
		];
		
		$buffer = preg_replace($search, $replace, $buffer);
		
		return $buffer;
	}
}

if ( ! function_exists('js_compressor'))
{
	function js_compressor ($content = '', $cache = FALSE, $cachetime = NULL)
	{
		static $APPPATH;
		if ( ! isset($APPPATH))
		{
			global $BASES_path;
			$APPPATH = $BASES_path[0];
		}
		
		if (is_empty($content))
		{
			return $content;
		}
		
		if ($cache !== FALSE)
		{
			$cache_file = $APPPATH . '/.cache/js/' . ($cache !== TRUE ? $cache : md5($content)) . '.js';
			mkdir2(dirname($cache_file), $APPPATH);
			
			if($cache !== TRUE and ! is_null($cachetime) and (time() - filemtime($cache_file)) >= $cachetime)
			{
				unlink($cache_file);
			}

			if (file_exists($cache_file))
			{
				return file_get_contents($cache_file);
			}
		}

		if ($file = ROOTPATH . '/the.libs/minify/require.php' and file_exists($file))
		{
			try
			{
//				if (is_localhost())
//				{
//					throw new Exception('Localhost');
//				}

				require_once $file;
				
				$temp = (new MatthiasMullie\Minify\JS($content))->minify();
				$content = $temp;
			}
			catch (Exception $e)
			{}
		}

		try
		{
//			if (is_localhost())
			{
				throw new Exception('Localhost');
			}

			$temp = url_post('https://javascript-minifier.com/raw', array('input' => (string)$content));
			
			if (preg_match('/^\/\/ Error/i', $temp))
			{
				throw new BasicException($temp, 0, ['code' => (string)$content]);
			}
			
			if ($temp === FALSE or is_empty($temp))
			{
				throw new Exception('Error: No Content');
			}
			
			$content = $temp;
		}
		catch (BasicException $e)
		{
			APP()->log($e, FALSE);
		}
		catch (Exception $e)
		{}

		isset($cache_file) and file_put_contents($cache_file, $content);
		return $content;
	}

}

if ( ! function_exists('css_compressor'))
{
	function css_compressor ($content = '', $cache = FALSE, $cachetime = NULL)
	{
		static $APPPATH;
		if ( ! isset($APPPATH))
		{
			global $BASES_path;
			$APPPATH = $BASES_path[0];
		}
		
		if (is_empty($content))
		{
			return $content;
		}
		
		if ($cache !== FALSE)
		{
			$cache_file = $cache !== TRUE ? $cache : $APPPATH . '/.cache/css/' . md5($content) . '.css';
			mkdir2(dirname($cache_file), $APPPATH);
			
			if($cache !== TRUE and ! is_null($cachetime) and (time() - filemtime($cache_file)) >= $cachetime)
			{
				unlink($cache_file);
			}

			if (file_exists($cache_file))
			{
				return file_get_contents($cache_file);
			}
		}

		if ($file = ROOTPATH . '/the.libs/minify/require.php' and file_exists($file))
		{
			try
			{
				require_once $file;
				
				$temp = (new MatthiasMullie\Minify\CSS($content))->minify();
				$content = $temp;
			}
			catch (Exception $e)
			{}
		}

		try
		{
//			if (is_localhost())
			{
				throw new Exception('Localhost');
			}

			$temp = url_post('https://cssminifier.com/raw', array('input' => (string)$content));
			
			if (preg_match('/^\/\/ Error/', $temp))
			{
				throw new BasicException($temp, 0, ['code' => (string)$content]);
			}
			
			if ($temp === FALSE or is_empty($temp))
			{
				throw new Exception('Error: No Content');
			}
			
			$content = $temp;
		}
		catch (BasicException $e)
		{
			APP()->log($e, FALSE);
		}
		catch (Exception $e)
		{}

		isset($cache_file) and file_put_contents($cache_file, $content);
		return $content;
	}

}

if ( ! function_exists('json_compressor'))
{
	function json_compressor ($content = '')
	{
		if (is_empty($content))
		{
			return $content;
		}
		
		try
		{
			$temp = json_decode($content);
			$content = json_encode($temp);
		}
		catch (Exception $e)
		{
			return $content;
		}

		return $content;
	}
}

if ( ! function_exists('array_html'))
{
	/**
	 * array_html()
	 * Convierte un Array en un formato nestable para HTML
	 *
	 * @param array $arr Array a mostrar
	 * @return string
	 */
	function array_html (array $arr, $lvl = 0)
	{
		static $_instances = 0;

		$lvl = (int)$lvl;

		$lvl_child = $lvl + 1 ;
		$str = [];

		$lvl===0 and $str[] = '<div class="array_html" id="array_html_'.(++$_instances).'">';

		$str[] = '<ol data-lvl="'.($lvl).'" class="array'.($lvl>0?' child':'').'">';

		foreach ($arr as $key => $val)
		{
			$hash = md5(json_encode([$lvl, $key]));
			$ctype = gettype($val);
			$class = gettype($val)==='object' ? get_class($val) : $ctype;
			
			$_str = '';

			$_str.= '<li class="detail" data-hash="' . htmlspecialchars($hash) . '">';
			$_str.= '<span class="key'.(is_numeric($key)?' num':'').(is_integer($key)?' int':'').'">';
			$_str.= $key;
			$_str.= '<small class="info">'.$class.'</small>';
			$_str.= '</span>';
			
			if ( $ctype === 'object')
			{
				$asarr = NULL;
				foreach(['getArrayCopy', 'toArray', '__toArray'] as $f)
				{
					if (method_exists($val, $f))
					{
						try
						{
							$t = call_user_func([$val, $f]);
							if( ! is_array($t))
							{
								throw new Exception('No es Array');
							}
							$asarr = $t;
						}
						catch(Exception $e)
						{}
					}
				}
				is_null($asarr) or $val = $asarr;
			}
			
			if (is_array($val))
			{
				$_str .= array_html($val, $lvl_child);
			}
			
			elseif ( $ctype === 'object')
			{
				$_str.= '<pre data-lvl="'.$lvl_child.'" class="'.$ctype.' child'.($ctype === 'object' ? (' ' . $class) : '').'">';
				$_str.= htmlentities(print_r($val, true));
				$_str.= '</pre>';
			}
			else
			{
				$_str.= '<pre data-lvl="'.$lvl_child.'" class="'.$ctype.' child-inline">';
				if (is_null($val))
				{
					$_str.= '<small style="color: #888">[NULO]</small>';
				}
				elseif (is_string($val) and empty($val))
				{
					$_str.= '<small style="color: #888">[VACÍO]</small>';
				}
				elseif (is_bool($val))
				{
					$_str.= '<small style="color: #888">['.($val?'TRUE':'FALSE').']</small>';
				}
				else
				{
					$_str.= htmlentities(print_r($val, true));
				}
				$_str.= '</pre>';
			}

			$str[] = $_str;
		}

		$str[] = '</ol>';

		$lvl===0 and $str[] = '</div>';

		$str = implode('', $str);

		$_instances===1 and $lvl===0 and add_inline_css(
			'.array_html {display: block;text-align: left;color: #444;background: white;position:relative}'.
			'.array_html * {margin:0;padding:0}'.
			'.array_html .array {list-style: none;margin: 0;padding: 0;}'.
			'.array_html .array .array {margin: 10px 0 10px 10px;}'.
			'.array_html .key {padding: 5px 10px;display:block;border-bottom: solid 1px #ebebeb}'.
			'.array_html .detail {display: block;border: solid 1px #ebebeb;margin: 0 0 0;}'.
			'.array_html .detail + .detail {margin-top: 10px}'.
			'.array_html .array .array .detail {border-right: none}'.
			'.array_html .child:not(.array), .array_html .child-inline {padding:10px}'.
			'.array_html .info {color: #ccc;float: right;margin: 4px 0 4px 4px;user-select:none}'.
			'.array_html.js .detail.has-child:not(.open)>.child {display:none}'.
			'.array_html.js .detail.has-child:not(.open)>.key {border-bottom:none}'.
			'.array_html.js .detail.has-child>.key {cursor:pointer}'.
			'.array_html.js .detail.has-child:before {content: "▼";float: left;padding: 5px;color: #ccc;}'.
			'.array_html.js .detail.has-child.open:before {content: "▲";}'
		);

		$lvl===0 and $str .= '<script>;(function(){'.
			'var div = document.getElementById("array_html_'.$_instances.'");'.
			'var open = function(e){if(e.defaultPrevented){return;};var t = e.target;if(/info/.test(t.classList)){t = t.parentElement;};if(!(/key/.test(t.classList))){return;};t.parentElement.classList.toggle("open");e.preventDefault()};'.
			'div.classList.add("js");'.
			'div.querySelectorAll(".child").forEach(function(d){var p = d.parentElement, c = p.classList;c.add("has-child");c.add("open");p.onclick = open;});'.
		'}());</script>';

		return $str;
	}

}
