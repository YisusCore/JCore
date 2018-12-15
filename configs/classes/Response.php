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
 * @package		JCore\Response
 * @author		YisusCore
 * @link		https://jcore.jys.pe/classes
 * @version		1.0.2
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
 * Response
 * Clase Principal JCore
 */

class Response
{
	/**
	 * Versión de la Clase
	 * @constant
	 * @global
	 */
	const version = '1.0.2';
	
	//===================================================================
	// Statics
	//===================================================================

	/**
	 * Función para llamar la instancia de la aplicación
	 * @static
	 * @return APP La instancia de la Aplicación
	 */
	public static function &instance()
	{
		static $instance;
		
		isset($instance) or $instance = new self();

		return $instance;
	}
	
	/**
	 * Headers
     * @access protected
     * @var array
	 */
	protected $_headers = [];

	/**
	 * Mime Type
	 * @global
	 */
	protected $_mime_type = 'text/html';

    /**
     * CONTENT data to be used in the response
     *
     * @access protected
     * @var string
     */
    public $CONTENT;

    /**
     * HTML data to be used in the response,
	 * posible uses $CONTENT
     *
     * @access protected
     * @var string
     */
    public $HTML;

    /**
     * An array of JSON key-value pairs
     * to be sent back for ajax requests
     *
     * @access protected
     * @var array
     */
    public $JSON;

    /**
     * Whether we are servicing an ajax request.
     *
     * @access private
     * @var bool
     */
    protected $_responseType = 'HTML';

	/**
	 * Comprimir
	 * @global
	 */
	protected $_compress = TRUE;

	/**
	 * ZLIB está inicializado?
	 * @global
	 */
	protected $_zlib_oc = TRUE;

	protected function __construct()
	{
		$this->CONTENT= '';

        if (defined('FORCE_RSP_TYPE'))
		{
			$this->setType(FORCE_RSP_TYPE);
		}
		elseif (isset($_GET['contentOnly']) or (isset($_GET['_']) and $_GET['_'] === 'co'))
		{
			$this->setType('CONTENT');
		}
		elseif (
			(
				isset($_SERVER['HTTP_X_REQUESTED_WITH']) and 
			 	(mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' or mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'androidapp')
			) or isset($_GET['json'])
		)
		{
			$this->setType('JSON');
		}
		else
		{
			$this->setType('HTML');
		}

        $this->HTML   = new ResponseHtml($this);
        $this->JSON   = new ResponseJson($this);

		$this->_zlib_oc = (bool) ini_get('zlib.output_compression');
		$this->_compress = $this->_zlib_oc === FALSE && extension_loaded('zlib');

		OPB();

		action_add('shutdown', [$this, 'response']);
	}

	public function exit($status = NULL)
	{
		exit ($status);
	}

	public function exit_ifhtml($status = NULL)
	{
		if ($this->_responseType === 'HTML')
		{
			exit ($status);
		}
		
		return $this;
	}

	public function exit_ifjson($status = NULL)
	{
		if ($this->_responseType === 'JSON')
		{
			exit ($status);
		}
		
		return $this;
	}

	public function nocache()
	{
		header('Cache-Control: no-cache, must-revalidate'); //HTTP 1.1
		header('Pragma: no-cache'); //HTTP 1.0
		header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		
		return $this;
	}
	
	public function cache($days = 365, $for = 'private', $rev = 'no-revalidate')
	{
		$time = 60 * 60 * 24 * $days;
    	$cache_expire_date = gmdate("D, d M Y H:i:s", time() + $time);
		
		header('User-Cache-Control: max-age=' . $time. ', ' . $for . ', ' . $rev); //HTTP 1.1
		header('Cache-Control: max-age=' . $time. ', ' . $for . ', ' . $rev); //HTTP 1.1
		header('Pragma: cache'); //HTTP 1.0
		header('Expires: '.$cache_expire_date.' GMT'); // Date in the future
		
		return $this;
	}
	
	public function download($filename = '')
	{
		if (@is_file($filename) AND ($filesize = @filesize($filename)) !== FALSE)
		{
			$this -> setContent(@file_get_contents($filename));
			$filename = basename($filename);
		}
		
		$filesize = mb_strlen($this->CONTENT);
		
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header('Expires: 0');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: '.$filesize);
		header('Cache-Control: private, no-transform, no-store, must-revalidate');
		
		return $this;
	}
	
    public function setType($type, $mime = NULL, $charset = NULL)
    {
		$type = mb_strtoupper($type);
		
		if (is_null($mime))
		{
			switch ($type)
			{
				case 'JSON';
					$mime = 'application/json';
					break;
				case 'HTML';case 'CONTENT';
					$mime = 'text/html';
					break;
				default;
					$mime = $this->_mime_type;
					break;
			}
		}

		is_null($charset) AND $charset = APP()->charset;

		$this->_responseType = $type;
		$this->set_content_type($mime, $charset);

		return $this;
    }

    public function jsonStatus($status, $message = NULL, $code = NULL)
    {
        $this->JSON->setStatus($status, $message, $code);
		return $this;
    }

    public function success($message = NULL, $code = NULL)
    {
        $this->JSON->success($message, $code);
        $this->addHtml(Alert::success($message, $code));
		return $this;
    }

    public function error($error = NULL, $code = NULL)
    {
        $this->JSON->error($error, $code);
        $this->addHtml(Alert::error($error, $code));
		return $this;
    }

    public function notice($message = NULL, $code = NULL)
    {
        $this->JSON->notice($message, $code);
        $this->addHtml(Alert::notice($message, $code));
		return $this;
    }

    public function setTitle($title, $shortTitle = NULL)
    {
        $this->HTML->Head->title = $title;
        is_null($shortTitle) or $this->HTML->Head->short_title = $shortTitle;
		
		return $this;
    }

    public function getTitle()
    {
        return $this->HTML->Head->title;
    }

    public function getShortTitle()
    {
		if (is_null($this->HTML->Head->short_title))
		{
			if (is_null($this->HTML->Head->title))
			{
				return ucfirst(strtr(RTR()->uri_parsed()[0], '-_', '  '));
			}
			
			return $this->HTML->Head->title;
		}
		
        return $this->HTML->Head->short_title;
    }

	protected $_breadcrumb = [];
    public function setBreadcrumb($array)
    {
        $this->_breadcrumb = $array;
		return $this;
    }
	
    public function addBread($link)
    {
        $this->_breadcrumb[] = $link;
		return $this;
    }

    public function getBreadcrumb()
    {
		if (count($this->_breadcrumb) === 0)
		{
			$uri = RTR()->uri_parsed();
			
			empty($uri[0]) and array_shift($uri);
			
			$_lnk = url();
			$breads = array_map(function($lnk) use (&$_lnk) {
				$_lnk .= '/' . $lnk;
				
				return [
					'link' => $_lnk,
					'desc' => ucfirst(strtr($lnk, '-_', '  '))
				];
			}, $uri);
			
			if (count($breads) === 0)
			{
				array_unshift($breads, [
					'link' => url(),
					'desc' => 'Inicio'
				]);
			}

			array_unshift($breads, [
				'link' => url(),
				'desc' => 'Oficina'
			]);
			
			return $breads;
		}
		
        return $this->_breadcrumb;
    }

    /**
     * Returns true or false depending on whether
     * we are servicing an ajax request
     *
     * @return bool
     */
    public function isJson()
    {
        return $this->_responseType === 'JSON';
    }

    /**
     * Returns true or false depending on whether
     * we are servicing an ajax request
     *
     * @return bool
     */
    public function responseType()
    {
        return $this->_responseType;
    }
	
    /**
     * set CONTENT to the response
     *
     * @param string $content A string to be appended to
     *                        the current output buffer
     *
     * @return void
     */
    public function setContent($content)
    {
        $this->CONTENT = $content;
		return $this;
    }

    /**
     * Add HTML code to the response
     *
     * @param string $content A string to be appended to
     *                        the current output buffer
     *
     * @return void
     */
    public function addHTML($content)
    {
        if (is_array($content)) {
            foreach ($content as $msg) {
                $this->addHTML($msg);
            }
        } elseif ($content instanceof Alert) {
            $this->CONTENT .= $content->getHtml();
        } else {
            $this->CONTENT .= $content;
        }
		return $this;
    }

    /**
     * Add JSON code to the response
     *
     * @param mixed $json  Either a key (string) or an
     *                     array or key-value pairs
     * @param mixed $value Null, if passing an array in $json otherwise
     *                     it's a string value to the key
     *
     * @return void
     */
    public function addJSON($json, $value = null)
    {
        if (is_array($json)) {
            foreach ($json as $key => $value) {
                $this->addJSON($key, $value);
            }
        } else {
            if ($value instanceof Message) {
                $this->JSON[$json] = $value->getArray();
            } else {
                $this->JSON[$json] = $value;
            }
        }
		return $this;
    }

	public function force_uri($uri)
	{
		$this->HTML->force_uri($uri);
		return $this;
	}

	/**
	 * Añade nuevo header
	 * @param	string	$header		Header
	 * @param	bool	$replace	Whether to replace the old header value, if already set
	 * @return	self
	 */
	public function set_header($header, $replace = TRUE)
	{
		if (strncasecmp($header, 'content-length', 14) === 0 and $this->zlib_oc)
		{
			return $this;
		}

		$this->_headers[] = array($header, $replace);
		return $this;
	}

	/**
	 * Establece la cabecera Content-Type
	 * @param	string	$mime_type
	 * @param	string	$charset
	 * @return	self
	 */
	public function set_content_type($mime_type, $charset = NULL)
	{
		if (strpos($mime_type, '/') === FALSE)
		{
			$extension = ltrim($mime_type, '.');

			if (isset(FT()[$extension]))
			{
				$mime_type = get_mime($extension);
			}
		}

		$this->_mime_type = $mime_type;

		empty($charset) and $charset = APP()->charset;

		$header = 'Content-Type: ' . $mime_type . '; charset=' . $charset;

		$this->_headers[] = array($header, TRUE);
		return $this;
	}

	/**
	 * Obtiene el Content-Type
	 * @return	string
	 */
	public function get_content_type()
	{
		for ($i = 0, $c = count($this->_headers); $i < $c; $i++)
		{
			if (sscanf($this->_headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1)
			{
				return $content_type;
			}
		}

		return $this->_mime_type;
	}

	/**
	 * Obtiene el Charset
	 * @return	string
	 */
	public function get_charset()
	{
		for ($i = 0, $c = count($this->_headers); $i < $c; $i++)
		{
			if (preg_match('/Content-Type: ([^;]+)(; charset\=(.+))?/', $this->_headers[$i][0], $matches))
			{
				return trim($matches[3]);
			}
		}

		return NULL;
	}

	/**
	 * Obtener los headers
	 * @param	string	$header
	 * @return	string
	 */
	public function get_header($header)
	{
		$headers = array_merge(
			array_map('array_shift', $this->_headers),
			headers_list()
		);

		if (empty($headers) OR empty($header))
		{
			return NULL;
		}

		for ($c = count($headers) - 1; $c > -1; $c--)
		{
			if (strncasecmp($header, $headers[$c], $l = mb_strlen($header)) === 0)
			{
				return trim(mb_substr($headers[$c], $l+1));
			}
		}

		return NULL;
	}

	/**
	 * 
	 */
	protected $_redirects_it = [];

	/**
	 * Establecer una redirección en caso el Tipo sea
	 * @param	string	$type
	 * @param	string	$link
	 * @return	self
	 */
	public function redirect_iftype($type, $link)
	{
		$this->_redirects_it[$type] = $link;
		return $this;
	}

	/**
	 * Establecer una redirección en caso el Tipo sea
	 * @param	string	$link
	 * @return	self
	 */
	public function redirect_ifhtml($link)
	{
		return $this->redirect_iftype('HTML', $link);
	}

	/**
	 * Establecer una redirección en caso el Tipo sea
	 * @param	string	$link
	 * @return	self
	 */
	public function redirect($link)
	{
		return $this->redirect_iftype($this->_responseType, $link);
	}

	/**
	 * Establece el HTTP Status Header
	 * @param	int	$code
	 * @param	string	$text
	 * @return	self
	 */
	public function http_code($code = 200, $text = '')
	{
		http_code($code, $text);
		return $this;
	}

    /**
     * Sends an HTML response to the browser
     *
     * @return void
     */
    public function response()
    {
		action_apply('do_before_response');
		
		class2('OutputBuffering', 'class')->stop();

		if (empty($this->CONTENT))
		{
			$this->CONTENT = class2('OutputBuffering', 'class')->getContents();
        }
		
		if (isset($this->_redirects_it[$this->_responseType]))
		{
			redirect($this->_redirects_it[$this->_responseType]);
		}
		
		ob_start();
		if ($this->_responseType === 'CONTENT')
		{
			echo $this->CONTENT;
		}
		else
		{
			$var = $this->_responseType;
			isset($this->$var) or $this->$var = new ResponseFile($this);
			echo $this->$var->response();
		}
		
		$result = ob_get_contents();
		ob_end_clean();
		
		if (count($this->_headers) > 0)
		{
			foreach ($this->_headers as $header)
			{
				@header($header[0], $header[1]);
			}
		}
		
//		if (in_array($this->_responseType, ['CONTENT', 'HTML']) and $this->_compress === TRUE)
//		{
//			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
//			{
//				@header('Content-Length: ' . mb_strlen($result));
//			}
//			else
//			{
//				try
//				{
//					$result_temp = gzinflate(mb_substr($result, 10, -8));
//					$result = $result_temp;
//				}
//				catch (Exception $e){}
//			}
//		}

		echo $result;
        class2('OutputBuffering', 'class')->flush();
        exit;
    }
	
	
	/**
	 * Retorna el nombre y la versión de la clase
	 * @return string
	 */
	public function __toString()
	{
		return get_class() . ' v' . self::version . ' by JYS Perú';
	}
	
	/**
	 * Permite retornar datos de desarrollador
	 * @return Array
	 */
	public function __debugInfo()
	{
		return [
			'_' => $this->__toString(),
		];
	}
}