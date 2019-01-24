<?php

class ImageMng
{
	protected $abspath;
	protected $path;
	protected $slug;
	
	public function __construct($abspath, $path = NULL)
	{
		if (is_array($abspath))
		{
			extract($abspath);
		}
		
		is_null($path) and $path = '';
		
		$this->abspath = $abspath;
		$this->path = $path;
		$this->slug = $slug;
	}
	
	public function processFile($file, $opt = [], $src_params = [])
	{
		$path = $this->path;
		$abspath = $this->abspath;
		$slug = $this->slug;

		$file = strtr($file, '\\/', DS.DS);
		$directorio = explode(DS, $file);
		count($directorio) and empty($directorio[0]) and array_shift($directorio);
		
		$file_name = array_pop($directorio);

		$file_name = explode('.', $file_name);
		$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
		$file_name = implode('.', $file_name);

		// Eliminar carpetas del path
		$path_array = explode('/', $path);
		count($path_array) and empty($path_array[0]) and array_shift($path_array);

		while (count($directorio) > 0 and count($path_array) > 0 and $directorio[0] === $path_array[0])
		{
			array_shift($path_array);
			array_shift($directorio);
		}
		unset($path_array);

		isset($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial
		empty($directorio[0]) or array_unshift($directorio, ''); ## Agrega el espacio inicial

		$directorio = implode(DS, $directorio);

		/**
		 * OBTENCIÓN DE LAS OPCIONES
		 */
		
		if ( ! is_array($opt))
		{
			if (is_callable($opt))
			{
				$opt = $opt($src, $url, $src_params);
			}

			if (is_string($opt) or ! is_array($opt))
			{
				$opt = (string)$opt;
				$opt = ['size' => $opt];
			}
		}
	
		$opt = array_merge([
			'size'    => NULL,
			'crop'    => NULL,
			'offset'  => NULL,

			'quality' => NULL,
		], (array)$opt);

		extract($opt, EXTR_REFS);

		// Buscar parametro de Quality en el nombre
		$opts_in_name = explode('@', $file_name);
		$opts_in_name_base = array_shift($opts_in_name);//Elimino el primero porque corresponde al nombre

		if (count($opts_in_name) > 0)
		{
			foreach($opts_in_name as $ind => $par)
			{
				if ( ! preg_match('#^[0-4]X$#i', $par))
				{
					// Valores autorizados:
					// @0X  ~ verylow
					// @1X  ~ low
					// @2X  ~ normal
					// @3X  ~ hight
					// @4X  ~ veryhight
					continue;
				}
				
				if (is_null($opt['quality']))
				{
					$opt['quality'] = $par;
				}
				
				unset($opts_in_name[$ind]);
			}
			
			$file_name = $opts_in_name_base;
			if (count($opts_in_name) > 0)
			{
				$file_name .= '@' . implode('@', $opts_in_name);
			}
		}

		// Buscar parametro no Quality en el nombre
		$opts_in_name = explode('.', $file_name);
		$opts_in_name_base = array_shift($opts_in_name);//Elimino el primero porque corresponde al nombre
		
		if (count($opts_in_name) > 0)
		{
			foreach($opts_in_name as $ind => $par)
			{
				if ( ! preg_match('#^(is([0-9]+)X([0-9]+)|ic[0-1]|io([0-9]+)X([0-9]+))$#i', $par))
				{
					// Valores autorizados:
					// is1234X4321
					// ic1	ic0
					// io12X0
					continue;
				}
				
				switch(mb_substr($par, 0, 2))
				{
					case 'is':
						if (is_null($opt['size']))
						{
							$opt['size'] = mb_substr($par, 2);
						}
						break;
					case 'ic':
						if (is_null($opt['crop']))
						{
							$opt['crop'] = (bool)(int)mb_substr($par, 2, 1);
						}
						break;
					case 'io':
						if (is_null($opt['offset']))
						{
							$opt['offset'] = mb_substr($par, 2);
						}
						break;
				}
				
				unset($opts_in_name[$ind]);
			}
			
			$file_name = $opts_in_name_base;
			if (count($opts_in_name) > 0)
			{
				$file_name .= '.' . implode('.', $opts_in_name);
			}
		}

		foreach(array_keys($opt) as $opt_name)
		{
			if (isset($src_params[$opt_name]))
			{
				if (is_null($opt[$opt_name]))
				{
					$opt[$opt_name] = $src_params[$opt_name];
				}

				if ( ! $externo)
				{
					unset($src_params[$opt_name]);
				}
				// Si es externo puede que el campo sea necesario
			}
		}

		/**
		 * El archivo real
		 */
		$real_file = $abspath . $path . $directorio . DS .$file_name . $file_ext;
		$real_file = strtr($real_file, '/\\', DS.DS);

		$file_size = [1, 1]; ## 1x1 para que no produzca errores
		if ( ! file_exists($real_file))
		{
			return NULL;
		}
		
		try
		{
			$mime = filemime($real_file);
			$extension = FT()->getExtensionByMime($mime);
			
			if ($extension === NULL)
			{
				throw new Exception('Extensión no encontrada');
			}
			
			if (is_null($file_ext))
			{
				$file_ext = $extension;
			}
			
			$tipo = $extension()['type'];
			
			if ($tipo !== 'IMAGEN')
			{
				throw new Exception('Archivo es ' . $tipo);
			}
			
			$file_size = getimagesize($real_file);
		}
		catch(Exception $e)
		{
			return $real_file;
		}

		/**
		 * Obtener datos de las opciones
		 */
		// Verificar las OPCIONES
		IF (is_null($size))
		{
			//Obteners el tamaño del archivo original
			$size = [$file_size[0], $file_size[1]];
		}
		
		IF (is_string($size))
		{
			//Obteners el tamaño del archivo original
			$size = preg_split('/x/i', $size, 2);
		}
		
		IF (is_numeric($size))
		{
			//Obteners el tamaño del archivo original
			$size = [$size, $size];
		}
		
		$size = (array)$size;
		
		if ( ! isset($size[1]))
		{
			$size[1] = $file_size[1];
		}
		
		if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[0], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[2];
			
			$width = $file_size[0];
			if ($matches[1] == '*')
			{
				if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[1], $matches_temp))
				{
					//obtener el porcentaje del width original
					$percent_temp = (double)$matches_temp[2];

					$height = $file_size[1];
					if ($matches_temp[1] == '*')
					{
						throw new Exception('No pueden haber dos * en los valores del Tamaño de imagen');
					}

					$size[1] = $height * $percent_temp / 100;
				}
				
				$size[1] = (int) $size[1];

				if ($size[1] == 0)
				{
					$size[1] = $file_size[1];
				}
				
				$width = $size[1] * $file_size[1] / $width;
			}
			
			$size[0] = $width * $percent / 100;
		}
		
		$size[0] = (int) $size[0];
		
		if ($size[0] == 0)
		{
			$size[0] = $file_size[0];
		}
		
		if (preg_match('#^(\*{0,1})([0-9\.]+)\%$#', $size[1], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[2];
			
			$height = $file_size[1];
			if ($matches[1] == '*')
			{
				$height = $size[0] * $file_size[0] / $height;
			}
			
			$size[1] = $height * $percent / 100;
		}
		
		$size[1] = (int) $size[1];
		
		if ($size[1] == 0)
		{
			$size[1] = $file_size[1];
		}
		
		if (is_null($crop))
		{
			$crop = FALSE;
		}
		
		if (is_null($offset))
		{
			$offset = [0, 0];
		}
		
		IF (is_string($offset))
		{
			//Obteners el tamaño del archivo original
			$offset = preg_split('/x/i', $offset, 2);
		}
		
		IF (is_numeric($offset))
		{
			//Obteners el tamaño del archivo original
			$offset = [$offset, $offset];
		}
		
		$offset = (array)$offset;
		
		if ( ! isset($offset[1]))
		{
			$offset[1] = 0;
		}
		
		if (preg_match('#^([0-9\.]+)\%$#', $offset[0], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[1];
			
			$width = $size[0];
			
			$offset[0] = $width * $percent / 100;
		}
		
		$offset[0] = (int) $offset[0];
		
		if (preg_match('#^([0-9\.]+)\%$#', $offset[1], $matches))
		{
			//obtener el porcentaje del width original
			$percent = (double)$matches[1];
			
			$height = $size[1];
			
			$offset[1] = $height * $percent / 100;
		}
		
		$offset[1] = (int) $offset[1];
		
		IF (is_null($quality))
		{
			$quality = '1X';
		}

		$quality = mb_strtoupper($quality);

		/**
		 * Formando la ruta del nuevo archivo
		 */
		$opt_uri = '';
		$opt_uri.= '.is' . implode('x', $size);
		
		if ($crop)
		{
			$opt_uri .= '.ic1';
		}
		
		if ($offset[0] > 0 and $offset[1] > 0)
		{
			$opt_uri .= '.io' . implode('x', $offset);
		}
		
		if ($quality <> '1X')
		{
			$opt_uri .= '@' . $quality .'X';
		}

		/**
		 * El archivo final
		 */
		$the_file = (is_empty($slug) ? '' : ('/' . $slug)) . $path . $directorio . '/' . $file_name . $opt_uri . $file_ext;
		$the_file = strtr($the_file, '/\\', '//');

		$the_file_path = $abspath . str_replace('/', DS, $the_file);

		if (file_exists($the_file_path))
		{
			return $the_file_path;
		}
		
		$this->process($real_file, $the_file_path, $opt);
		
		return $the_file_path;
	}

	public function processUrl()
	{
		$slug = $this->slug;

		// Obtener los parametros de la SRC
		parse_str(url('query'), $src_params);
		
		$directorio = RTR()->uri_parsed();
		count($directorio) and empty($directorio[0]) and array_shift($directorio);
		
		// Eliminar carpetas del slug
		if (count($directorio) > 0 and $directorio[0] === $slug)
		{
			array_shift($directorio);
		}

		$file_name = array_pop($directorio);

		$file_name = explode('.', $file_name);
		$file_ext  = count($file_name) > 1 ? ('.' . array_pop($file_name)) : '';
		$file_name = implode('.', $file_name);

		$directorio = implode(DS, $directorio);

		if (empty($file_name))
		{
			http_code(404);
			exit ('No se encontró la imagen buscada');
			return;
		}

		$local_file = $this->processFile(DS . $directorio . DS . $file_name . $file_ext, [], $src_params);
		
		if (empty($local_file) or ! file_exists($local_file))
		{
			http_code(404);
			exit ('No se encontró la imagen buscada');
			return;
		}

		try
		{
			$mime = filemime($local_file);
			$size = filesize($local_file);
		}
		catch(Exception $e)
		{
			http_code(404);
			exit ('No se encontró la imagen buscada');
			return;
		}
		
		RSP()
			-> setType('FILE', $mime)
// 			-> set_header('Content-Length', $size)
// 			-> set_header('Cache-Control', 'max-age=31536000, public')
			-> CONTENT = file_get_contents($local_file)
        	;
		
		die();
	}

	public function process ($real_file, $new_file, $opt = [])
	{
		mkdir2(dirname($new_file));
		
		$file_ext = NULL;
		$file_size = [1, 1];
		
		try
		{
			$mime = filemime($real_file);
			$extension = FT()->getExtensionByMime($mime);
			
			if ($extension === NULL)
			{
				throw new Exception('Extensión no encontrada');
			}
			
			if (is_null($file_ext))
			{
				$file_ext = (string) $extension;
			}
			
			$tipo = $extension()['type'];
			
			if ($tipo !== 'IMAGEN')
			{
				throw new Exception('Archivo es ' . $tipo);
			}
			
			$file_size = getimagesize($real_file);
		}
		catch(Exception $e)
		{
			return $real_file;
		}

		$color_trans = 127;
		switch($file_ext)
		{
			case "jpeg":
			case "jpg": $src_image = imagecreatefromjpeg($real_file);$color_trans=0;break;
			case "png": $src_image = imagecreatefrompng($real_file);break;
			case "bmp": $src_image = imagecreatefrombmp($real_file);break;
			case "gif": $src_image = imagecreatefromgif($real_file);break;
			default   : $src_image = imagecreatefromgd($real_file);break;
		}

		$size_s   = [$opt['size'][0], $opt['size'][1]];
		$size     = ['width' => $opt['size'][0], 'heigth' => $opt['size'][1]];
		$size_src = ['width' => $file_size[0], 'heigth' => $file_size[1], 'x' => 0, 'y' => 0];

		$width    = $opt['size'][0];
		
		$esc      = $size['width'] / $width;
		$prop     = $size['width'] / $size['heigth'];

		$size_dst = ['width' => $size['width'], 'heigth' => $size['heigth'], 'x' => 0, 'y' => 0];

		if ($opt['crop'])
		{
			if ($size_src['width'] / $size_src['heigth'] > $prop)
			{
				$size_dst['width'] = ceil($size_src['width'] * $size_dst['heigth'] / $size_src['heigth']);
				$size_dst['x']     = floor(($size['width'] - $size_dst['width']) / 2);
			}
			else
			{
				$size_dst['heigth']= ceil($size_src['heigth'] * $size_dst['width'] / $size_src['width']);
				$size_dst['y']     = floor(($size['heigth'] - $size_dst['heigth']) / 2);
			}
		}
		else
		{
			if ($size_src['width'] / $size_src['heigth'] > $prop)
			{
				$size_dst['heigth']= ceil($size_src['heigth'] * $size_dst['width'] / $size_src['width']);
				$size_dst['y']     = floor(($size['heigth'] - $size_dst['heigth']) / 2);
			}
			else
			{
				$size_dst['width'] = ceil($size_src['width'] * $size_dst['heigth'] / $size_src['heigth']);
				$size_dst['x']     = floor(($size['width'] - $size_dst['width']) / 2);
			}
		}

		$heigth = $size['heigth'] / $esc;

		$dst_image  = imagecreatetruecolor($size['width'], $size['heigth']);
		imagealphablending($dst_image, false);
		imagesavealpha($dst_image, true);

		$color = imagecolorallocatealpha($dst_image, 255, 255, 255, $color_trans);
		$trans = imagecolortransparent($dst_image, $color);
		imagefill($dst_image, 0, 0, $trans);

		imagecopyresampled($dst_image, $src_image, $size_dst['x'], $size_dst['y'], $size_src['x'], $size_src['y'], $size_dst['width'], $size_dst['heigth'], $size_src['width'], $size_src['heigth']);

		$dst_image2 = imagecreatetruecolor($width, $heigth);
		imagealphablending($dst_image2, false);
		imagesavealpha($dst_image2, true);

		$color      = imagecolorallocatealpha($dst_image2, 255, 255, 255, $color_trans);
		$trans = imagecolortransparent($dst_image2, $color);
		imagefill($dst_image2, 0, 0, $trans);

		imagecopyresampled($dst_image2, $dst_image, 0, 0, 0, 0, $width, $heigth, $size['width'], $size['heigth']);

		switch($file_ext){
			case "png": imagepng($dst_image2, $new_file, 9, PNG_NO_FILTER);break;
			case "bmp": imagewbmp($dst_image2, $new_file);break;
			case "gif": imagegif($dst_image2, $new_file);break;
			case "jpeg":
			case "jpg":
			default:    imagejpeg($dst_image2, $new_file, 80); break;
		}
	}
}
