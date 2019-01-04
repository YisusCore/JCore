<?php
use BasicException;

class Upload extends ArrayObject
{
	protected function sql ($query, $insert = FALSE)
	{
		static $MCON;
		
		if ( ! isset($MCON))
		{
			try
			{
				if ($config['bbdd'] === FALSE)
				{
					throw new Exception('Sin datos de BBDD');
				}
				
				$config['bbdd'] === TRUE and $config['bbdd'] = config('bd');
				
				extract($config['bbdd']);
		
				isset($host) or $host === 'localhost';
				
				if ( ! isset($user) or ! isset($pasw) or ! isset($name))
				{
					throw new Exception('No hay datos de BBDD');
				}
				
				$MCON = cbd($host, $user, $pasw, $name);
				
				if ( ! sql_et('uploads', $MCON))
				{
					sql('
				CREATE TABLE `uploads` (
				  `id` Bigint NOT NULL AUTO_INCREMENT,
				  
				  `name` Text, 
				  `type` Text,
				  `error` Text,
				  `size` Bigint,
				  
				  `id_usuario` BIGINT NOT NULL,
				  
				  `estado` Enum ("Registrado", "Error", "PorCargar", "Cargado") NOT NULL DEFAULT "Registrado",
				  `estado_log` Text,
				  
				  `imagen` Boolean NOT NULL DEFAULT FALSE,
				  `uri` Text,
				  `abspath` Text,
				  `dir` Text,
				  `fname` Text,
				  `fext` Text,
				  `fpath` Text,
				  
				  `href` Text,
				  
				  `creado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `actualizado` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),

				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC', FALSE, $MCON);
				}
			}
			catch (Exception $e)
			{
				$MCON = FALSE;
			}
		}
		
		if ($MCON !== FALSE)
		{
			return sql($query, $insert, $MCON);
		}
		
		return NULL;
	}
	
	private $_id = NULL;
	
	private $_name = NULL;
	private $_type = NULL;
	private $_error= NULL;
	private $_size = NULL;
	
	private $_estado     = 'Registrado';
	private $_estado_log = NULL;
	
	private $_imagen = FALSE;
	
	private $_uri     = NULL;
	private $_abspath = NULL;
	private $_dir     = NULL;
	
	private $_fname = NULL;
	private $_fext  = NULL;
	private $_fpath = NULL;
	private $_href  = NULL;
	
	public function __construct ($_FILE)
	{
		$this->_name = $_FILE['name'];
		$this->_type = $_FILE['type'];
		$this->_error= $_FILE['error'];
		$this->_size = $_FILE['size'];
		
		$id_usuario = (isset(APP()->Usuario) and isset(APP()->Usuario->id)) ? APP()->Usuario->id : 0;
		
		$this->_id = self::sql('
		INSERT INTO `uploads` (`name`, `type`, `error`, `size`, `estado`, `id_usuario`) 
		VALUES (' . qp_esc($this->_name) . ', ' . qp_esc($this->_type) . ', ' . qp_esc($this->_error) . ', ' . qp_esc($this->_size) . ', ' . qp_esc($this->_estado) . ', ' . qp_esc($id_usuario) . ')', TRUE);

		if ($this->_error > 0)
		{
			switch($this->_error)
			{
				case 1:
					$this->_estado_log = 'UPLOAD_ERR_INI_SIZE';
					break;
				case 2:
					$this->_estado_log = 'UPLOAD_ERR_FORM_SIZE';
					break;
				case 3:
					$this->_estado_log = 'UPLOAD_ERR_PARTIAL';
					break;
				case 6:
					$this->_estado_log = 'UPLOAD_ERR_NO_TMP_DIR';
					break;
				case 7:
					$this->_estado_log = 'UPLOAD_ERR_CANT_WRITE';
					break;
				case 8:
					$this->_estado_log = 'UPLOAD_ERR_EXTENSION';
					break;
				default:
					$this->_estado_log = 'NOT_IDENTIFIED';
					break;
			}
			
			$this->_estado = 'Error';
			
			self::sql('UPDATE `uploads` SET `estado_log` = ' . qp_esc($this->_estado_log) . ', `estado` = ' . qp_esc($this->_estado) . ' WHERE `id` = ' . qp_esc($this->_id));
			
			throw new BasicException ('Error al cargar Archivo (' . $this->_estado_log . ')');
		}
		
		$this->_imagen = preg_match('/^image\/(.*)/i', $this->_type);
		
		$zone = [config('files')];
		
		$this->_imagen and $zone = config('images_zones');
		
		$zone = end($zone);
		
		$this->_uri = $zone['uri'];
		$this->_abspath = $zone['abspath'];
		
		$upload = isset($zone['upload']) ? $zone['upload'] : '_';
		$upload_yearmonth = isset($zone['upload_yearmonth']) ? $zone['upload_yearmonth'] : TRUE;
		
		$this->_dir = DS . $upload . ($upload_yearmonth ? (DS . date('Y') . DS . date('m')) : '');
		
		mkdir2($this->_dir, $this->_abspath);
		
		$this->_fname = $this->_name;
		$this->_fname = mb_strtolower($this->_fname);
		
		$this->_fname = explode('.', $this->_fname);
		$this->_fext = count($this->_fname) === 1 ? NULL : array_pop($this->_fname);
		$this->_fname = implode('.', $this->_fname);

		self::sql('UPDATE `uploads` SET `imagen` = ' . qp_esc($this->_imagen) . ', `uri` = ' . qp_esc($this->_uri) . ', `abspath` = ' . qp_esc($this->_abspath) . ', `dir` = ' . qp_esc($this->_dir) . ', `fname` = ' . qp_esc($this->_fname) . ', `fext` = ' . qp_esc($this->_fext) . ' WHERE `id` = ' . qp_esc($this->_id));
		
		if (is_null($this->_fext))
		{
			$this->_estado_log = 'Archivo no tiene extensión';
			$this->_estado = 'Error';
			
			self::sql('UPDATE `uploads` SET `estado_log` = ' . qp_esc($this->_estado_log) . ', `estado` = ' . qp_esc($this->_estado) . ' WHERE `id` = ' . qp_esc($this->_id));
			
			throw new BasicException ($this->_estado_log);
		}

		$this->_fname = uniqid(strtoslug($this->_fname) . '_');
		if( preg_match('/^php/i', $this->_fext))
		{
			$this->_fext = 'html';
		}

		$this->_fpath = $this->_dir . DS . $this->_fname . '.' . $this->_fext;

		if (file_exists($this->_abspath . $this->_fpath)){
			$this->_fname = na(5) . "_" . $this->_fname;
			$this->_fpath = $this->_dir . DS . $this->_fname . '.' . $this->_fext;
		}

		$this->_estado = 'PorCargar';

		self::sql('UPDATE `uploads` SET `fname` = ' . qp_esc($this->_fname) . ', `fext` = ' . qp_esc($this->_fext) . ', `fpath` = ' . qp_esc($this->_fpath) . ', `estado` = ' . qp_esc($this->_estado) . ' WHERE `id` = ' . qp_esc($this->_id));

		if( ! move_uploaded_file($_FILE['tmp_name'], $this->_abspath . $this->_fpath))
		{
			$this->_estado_log = 'Error al realizar update de file - Origen o Destino no leible.';
			$this->_estado = 'Error';
			
			self::sql('UPDATE `uploads` SET `estado_log` = ' . qp_esc($this->_estado_log) . ', `estado` = ' . qp_esc($this->_estado) . ' WHERE `id` = ' . qp_esc($this->_id));
			
			throw new BasicException ($this->_estado_log);
		}

		$href = url('array');
		$href['host'] = rtrim($this->_uri, '/');
		$href['path'] = '/' .ltrim(str_replace(DS, '/', $this->_fpath), '/');

		$this->_href = build_url($href);

		$this->_estado = 'Cargado';

		self::sql('UPDATE `uploads` SET `href` = ' . qp_esc($this->_href) . ', `estado` = ' . qp_esc($this->_estado) . ' WHERE `id` = ' . qp_esc($this->_id));
		
		parent::__construct([
			'name'       => $this->_name,
			'type'       => $this->_type,
//			'error'      => $this->_error,
			'size'       => $this->_size,
//			'estado'     => $this->_estado,
//			'estado_log' => $this->_estado_log,
			'imagen'     => $this->_imagen,
//			'uri'        => $this->_uri,
//			'abspath'    => $this->_abspath,
//			'dir'        => $this->_dir,
//			'name'       => $this->_fname,
			'ext'        => $this->_ext,
			'path'       => $this->_path,
			'href'       => $this->_href,
			
			'preview'    => $this->_imagen ? get_image($this->_href, ['size' => '300x300']) : NULL,
			'favicon'    => $this->_imagen ? get_image($this->_href, ['size' => '50x50']) : NULL,
		]);
	}
}