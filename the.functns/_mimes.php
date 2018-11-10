<?php
/**
 * _mimes.php
 * 
 * El archivo `_mimes` contiene funciones y clase manipuladora de los mimes
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
 * @link		https://jcore.jys.pe/functions/_mimes
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

//===========================================================================
// Variables Generales
//===========================================================================
define ('FT_COMPRESS', 'COMPRIMIDO');
define ('FT_IMAGES', 'IMAGEN');
define ('FT_VIDEOS', 'VIDEOS');
define ('FT_DOCS', 'DOCUMENTOS');
define ('FT_WEBS', 'WEBS');
define ('FT_OTHERS', 'OTROS');

define ('FT_DEF_MIME', 'application/octet-stream');

//===========================================================================
// Clase FT
//===========================================================================
class FT implements ArrayAccess
{
	const version = '1.0';
	
	/**
	 * Instancia Única de la clase
	 * @var FT
	 * @static
	 */
	private static $instance;
	
	/**
	 * instance()
	 * Retorna la instancia Única de la clase
	 *
	 * @static
	 * @return FT instance
	 */
	public static function instance()
	{
		isset(self::$instance) or self::$instance = new self();
		
		return self::$instance;
	}
	
	/**
	 * Data de los mimes
	 *
	 * UPDATED AT 2018-06-30
	 * @protected
	 * @var array
	 */
	protected $mimes = [
		'txt' => [
			'text/plain'
		],
		'csv' => [
			'text/x-comma-separated-values',
			'text/comma-separated-values',
			'application/octet-stream',
			'application/vnd.ms-excel',
			'application/x-csv',
			'text/x-csv',
			'text/csv',
			'application/csv',
			'application/excel',
			'application/vnd.msexcel',
			'text/plain'
		],
		'bin' => [
			'application/macbinary',
			'application/mac-binary',
			'application/octet-stream',
			'application/x-binary',
			'application/x-macbinary'
		],
		'exe' => [
			'application/octet-stream',
			'application/x-msdownload'
		],
		'psd' => [
			'application/x-photoshop',
			'image/vnd.adobe.photoshop'
		],
		'dll' => [
			'application/octet-stream',
			'application/x-msdownload'
		],
		'pdf' => [
			'application/pdf',
			'application/force-download',
			'application/x-download',
			'binary/octet-stream'
		],
		'xls' => [
			'application/vnd.ms-excel',
			'application/msexcel',
			'application/x-msexcel',
			'application/x-ms-excel',
			'application/x-excel',
			'application/x-dos_ms_excel',
			'application/xls',
			'application/x-xls',
			'application/excel',
			'application/download',
			'application/vnd.ms-office',
			'application/msword'
		],
		'ppt' => [
			'application/powerpoint',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-office',
			'application/msword'
		],
		'pptx' => [
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/x-zip',
			'application/zip'
		],
		'js' => [
			'application/x-javascript',
			'text/plain',
			'application/javascript'
		],
		'tar' => [
			'application/x-tar',
			'application/tar'
		],
		'xhtml' => [
			'application/xhtml+xml'
		],
		'xht' => [
			'application/xhtml+xml'
		],
		'zip' => [
			'application/x-zip',
			'application/zip',
			'application/x-zip-compressed',
			'application/s-compressed',
			'multipart/x-zip'
		],
		'rar' => [
			'application/x-rar',
			'application/rar',
			'application/x-rar-compressed',
			'application/octet-stream'
		],
		'wav' => [
			'audio/x-wav',
			'audio/wave',
			'audio/wav'
		],
		'gif' => [
			'image/gif'
		],
		'jpeg' => [
			'image/jpeg',
			'image/pjpeg'
		],
		'jpg' => [
			'image/jpeg',
			'image/pjpeg'
		],
		'png' => [
			'image/png',
			'image/x-png'
		],
		'css' => [
			'text/css',
			'text/plain'
		],
		'html' => [
			'text/html',
			'text/plain'
		],
		'htm' => [
			'text/html',
			'text/plain'
		],
		'text' => [
			'text/plain'
		],
		'log' => [
			'text/plain',
			'text/x-log'
		],
		'rtf' => [
			'text/rtf',
			'application/rtf'
		],
		'xml' => [
			'application/xml',
			'text/xml',
			'text/plain'
		],
		'xsl' => [
			'application/xml',
			'text/xsl',
			'text/xml'
		],
		'mpeg' => [
			'video/mpeg'
		],
		'doc' => [
			'application/msword',
			'application/vnd.ms-office'
		],
		'docx' => [
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/zip',
			'application/msword',
			'application/x-zip'
		],
		'dot' => [
			'application/msword',
			'application/vnd.ms-office',
		],
		'xlsx' => [
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/zip',
			'application/vnd.ms-excel',
			'application/msword',
			'application/x-zip'
		],
		'json' => [
			'application/json',
			'text/json'
		],
		'3g2' => [
			'video/3gpp2'
		],
		'3gp' => [
			'video/3gp',
			'video/3gpp'
		],
		'mp4' => [
			'video/mp4'
		],
		'flv' => [
			'video/x-flv'
		],
		'wmv' => [
			'video/x-ms-wmv',
			'video/x-ms-asf'
		],
		'jar' => [
			'application/java-archive',
			'application/x-java-application',
			'application/x-jar',
			'application/x-compressed'
		],
		'svg' => [
			'image/svg+xml',
			'application/xml',
			'text/xml'
		],
		'ico' => [
			'image/x-icon',
			'image/x-ico',
			'image/vnd.microsoft.icon'
		],
		'mpg4' => [
			'video/mp4'
		],
		'vob' => [
			'video/x-ms-vob'
		],
		'conf' => [
			'text/plain'
		],
		'in' => [
			'text/plain'
		],
		'list' => [
			'text/plain'
		],
		'def' => [
			'text/plain'
		],
		'sql' => [
			'application/x-sql'
		],
		'vcard' => [
			'text/vcard'
		],
		'pps' => [
			'application/vnd.ms-powerpoint'
		],
		'pot' => [
			'application/vnd.ms-powerpoint'
		],
		'xlm' => [
			'application/vnd.ms-excel'
		],
		'xla' => [
			'application/vnd.ms-excel'
		],
		'xlc' => [
			'application/vnd.ms-excel'
		],
		'xlt' => [
			'application/vnd.ms-excel'
		],
		'xlw' => [
			'application/vnd.ms-excel'
		],
		'accdb' => [
			'application/msaccess'
		],
		'ppsx' => [
			'application/vnd.openxmlformats-officedocument.pres'
		],
		'rss' => [
			'application/rss+xml'
		],
		'bat' => [
			'application/x-msdownload'
		],
		'msi' => [
			'application/x-msdownload'
		],
		'pkt' => [
			'application/octet-stream'
		],
		'dump' => [
			'application/octet-stream'
		],
		'apk' => [
			'application/vnd.android.package-archive'
		],
		'eot' => [
			'application/vnd.ms-fontobject'
		]
	];
	
	/**
	 * Data de los tipos de mimes
	 *
	 * UPDATED AT 2018-06-30
	 * @protected
	 * @var array
	 */
	protected $types = [
		'zip' => FT_COMPRESS,
		'rar' => FT_COMPRESS,
		
		'jpg' => FT_IMAGES,
		'jpeg' => FT_IMAGES,
		'gif' => FT_IMAGES,
		'png' => FT_IMAGES,
		'ico' => FT_IMAGES,
		
		'svg' => FT_VIDEOS,
		'3gp' => FT_VIDEOS,
		'3g2' => FT_VIDEOS,
		'mp4' => FT_VIDEOS,
		'mpg4' => FT_VIDEOS,
		'mpeg' => FT_VIDEOS,
		'flv' => FT_VIDEOS,
		'wmv' => FT_VIDEOS,
		'vob' => FT_VIDEOS,
		
		'txt' => FT_DOCS,
		'text' => FT_DOCS,
		'conf' => FT_DOCS,
		'log' => FT_DOCS,
		'in' => FT_DOCS,
		'list' => FT_DOCS,
		'def' => FT_DOCS,
		'sql' => FT_DOCS,
		'pdf' => FT_DOCS,
		'vcard' => FT_DOCS,
		'ppt' => FT_DOCS,
		'pps' => FT_DOCS,
		'pot' => FT_DOCS,
		'doc' => FT_DOCS,
		'dot' => FT_DOCS,
		'xls' => FT_DOCS,
		'xlm' => FT_DOCS,
		'xla' => FT_DOCS,
		'xlc' => FT_DOCS,
		'xlt' => FT_DOCS,
		'xlw' => FT_DOCS,
		'accdb' => FT_DOCS,
		'ppsx' => FT_DOCS,
		'pptx' => FT_DOCS,
		'docx' => FT_DOCS,
		'xlsx' => FT_DOCS,
		
		'html' => FT_WEBS,
		'htm' => FT_WEBS,
		'css' => FT_WEBS,
		'csv' => FT_WEBS,
		'js' => FT_WEBS,
		'rss' => FT_WEBS,
		'xhtml' => FT_WEBS,
		'xht' => FT_WEBS,
		'xml' => FT_WEBS,
		'xsl' => FT_WEBS,
		
		'jar' => FT_OTHERS,
		'json' => FT_OTHERS,
		'rtf' => FT_OTHERS,
		'tar' => FT_OTHERS,
		'psd' => FT_OTHERS,
		'exe' => FT_OTHERS,
		'dll' => FT_OTHERS,
		'bat' => FT_OTHERS,
		'msi' => FT_OTHERS,
		'pkt' => FT_OTHERS,
		'bin' => FT_OTHERS,
		'dump' => FT_OTHERS,
		'apk' => FT_OTHERS,
		'eot' => FT_OTHERS,
		'wav' => FT_OTHERS
	];
	
	/**
	 * consulta()
	 * Función que permite buscar y retornar datos de una extensión
	 *
	 * @param string
	 * @return Mixed
	 */
	public function consulta($ext)
	{
		isset($this->mimes[$ext]) OR $this->mimes[$ext] = [FT_DEF_MIME];
		isset($this->types[$ext]) OR $this->types[$ext] = FT_OTHERS;
		
		return (new class($ext, $this->mimes[$ext], $this->types[$ext]) implements ArrayAccess 
		{
			public $data = [];

			public function __construct($ext, $mime, $type)
			{
				$this->data = [
					'ext' => $ext,
					'mime' => &$mime,
					'type' => &$type
				];
			}

			public function __toString()
			{
				return $this->data['mime'][0];
			}

			public function offsetSet($offset, $valor)
			{
				$this->data[$offset] = $valor;
			}

			public function offsetExists($offset)
			{
				return in_array($offset, $this->data);
			}

			public function offsetUnset($offset)
			{
				unset($this->data[$offset]);
			}

			public function offsetGet($offset)
			{
				return $this->data[$offset];
			}
	
			public function __debugInfo()
			{
				return $this->data;
			}
	  	});
	}
	
	/**
	 * getExtensionByMime()
	 * Permite buscar una extensión basada desde un MIME
	 *
	 * @param string
	 * @return Mixed
	 */
	public function getExtensionByMime($mime)
	{
		$extensiones = [];
		foreach($this->mimes as $ext => $mimes)
		{
			if ( ! in_array($mime, $mimes))
			{
				continue;
			}
			
			$extensiones[] = $ext;
		}
		
		if (count($extensiones) === 0)
		{
			return NULL;	
		}
		
		return (new class ($extensiones){
			public $extensiones;
			
			public function __construct($extensiones)
			{
				$this->extensiones = $extensiones;
			}
			
			public function __toString()
			{
				return $this->extensiones[0];
			}
			
			public function __debugInfo() 
			{
				return $this->extensiones;
			}
			
			public function FT($i = 0)
			{
				if ( ! isset($this->extensiones[$i]))
				{
					$i = 0;
				}
				
				return FT::instance()->consulta($this->extensiones[$i]);
			}
			
			public function __invoke($i = 0)
			{
				return $this->FT($i);
			}

		});
	}
	
	//======================================================================
	// Magic Functions
	//======================================================================
	
	/**
	 * __toString()
	 * Retorna el nombre y la versión de la clase
	 *
	 * @return string
	 */
	public function __toString()
	{
		return get_class() . ' v' . self::version . ' by JYS Perú';
	}
	
	/**
	 * __invoke()
	 * Permite considerar a la clase como función 
	 * y de ese modo hacer la consulta de la extensión
	 *
	 * @param string
	 * @return Mixed
	 */
	public function __invoke($ext)
	{
        return $this->consulta($ext);
    }

	/**
	 * __get()
	 * El usuario puede obtener la información de una extensión
	 * considerando a la extensión como una variable pública de la clase
	 *
	 * @param string
	 * @return Mixed
	 */
    public function __get($ext)
	{
        return $this->consulta($ext);
    }

	/**
	 * __isset()
	 * Permite validar si la data de mimes cuenta con la extensión requerida
	 *
	 * @param string
	 * @return bool
	 */
    public function __isset($ext)
	{
        return isset($this->mimes[$ext]);
    }
	
	/**
	 * __debugInfo()
	 * Permite retornar la data de mimes para su validación
	 *
	 * @return Array
	 */
	public function __debugInfo() 
	{
		return $this->mimes;
	}
	
	//======================================================================
	// Array Access
	// Transforma a la clase como un array
	//======================================================================
	
	/**
	 * offsetSet()
	 * Inserta o actualiza la data de un mime
	 *
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function offsetSet($ext, $valor) 
	{
		$this->mimes[$ext] = $valor;
	}
	
	/**
	 * offsetExists()
	 * Valida que la extensión exista en la data
	 *
	 * @param string
	 * @return bool
	 */
    public function offsetExists($ext) 
	{
        return isset($this->mimes[$ext]);
    }

	/**
	 * offsetUnset()
	 * Elimina la información de la extensión
	 *
	 * @param string
	 * @return void
	 */
    public function offsetUnset($ext) 
	{
        unset($this->mimes[$ext], $this->types[$ext]);
    }

	/**
	 * offsetGet()
	 * Obtiene la información de la extensión
	 *
	 * @param string
	 * @return Mixed
	 */
    public function offsetGet($ext) 
	{
        return $this->consulta($ext);
    }
}

if ( ! function_exists('FT'))
{
	/**
	 * FT()
	 * Retorna la instancia de la clase FT
	 *
	 * @return FT
	 */
	function FT ()
	{
		return FT::instance();
	}
}

if ( ! function_exists('get_mime'))
{
	/**
	 * get_mime()
	 * Obtiene la información de una extensión
	 *
	 * @return Mixed
	 */
	function get_mime ($ext)
	{
		return FT::instance() -> consulta($ext);
	}
}

if ( ! function_exists('filemime'))
{
	/**
	 * filemime()
	 * Obtiene el mime de un archivo indistinto a la extensión que tenga
	 *
	 * @param string
	 * @return string
	 */
	function filemime ($file)
	{
		static $_use_finfo = FALSE;
		
		if ( ! $_use_finfo OR ! file_exists('/usr/share/misc/magic'))
		{
			return mime_content_type($file);
		}
		
		try
		{
			$finfo = finfo_open(FILEINFO_MIME, '/usr/share/misc/magic');
			if ( ! $finfo)
			{
				throw new Exception('! FINFO');
			}
			
			$mime = finfo_file($finfo, $file);
			finfo_close($finfo);
			return $mime;
		}
		catch(Exception $e)
		{
			$_use_finfo = FALSE;
			return mime_content_type($file);
		}
	}
}
