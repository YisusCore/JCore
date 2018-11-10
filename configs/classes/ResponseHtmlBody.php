<?php
class ResponseHtmlBody
{
	protected $ResponseHtml;
	
	protected $variables = [];
	
	public $header = '';
	public $footer = '';
	protected $content = '';
	
	public function __construct($ResponseHtml = NULL, &$content)
	{
		$this->ResponseHtml = $ResponseHtml;
		
		$this->variables['attrs'] = [];
		
		$this->variables['attrs']['class'] = [];
		$this->variables['class'] =& $this->variables['attrs']['class'];
		
		$this->variables['do_before'] = [];
		$this->variables['do_after'] = [];
		$this->variables['clean_result'] = FALSE;
		
		$this->content =& $content;
	}
	
	public function addClass(...$classes)
	{
		foreach($classes as $class)
		{
			$this->class[] = $class;
		}
		return $this;
	}
	
	public function response()
	{
		ob_start();
		
		$attrs = $this->attrs;
		if (count($attrs) === 0)
		{
			$attrs = '';
		}
		else
		{
			$attrs = ' ' . implode(' ', array_map(function($key, $val){
				is_array($val) and $val = implode(' ', $val);
				
				return $key . (empty($val) ? '' : '="' . htmlspecialchars($val) . '"');
			}, array_keys($attrs), array_values($attrs)));
		}

		$header = $this->header;
		$content = $this->content;
		$footer = $this->footer;

		if ( ! empty($header))
		{
			$temp = @template($header);
			is_null($temp) or $header = $temp;
		}

		if ( ! empty($content))
		{
			$temp = @template($content);
			is_null($temp) or $content = $temp;
		}

		if ( ! empty($footer))
		{
			$temp = @template($footer);
			is_null($temp) or $footer = $temp;
		}

		$parsed = explode('<script>', $content);
		if (count($parsed) > 1)
		{
			$content = array_shift($parsed);
			foreach($parsed as $_temp)
			{
				$_temp = explode('</script>', $_temp, 2);
				$content.= $_temp[1];
				add_inline_js($_temp[0]);
			}
		}

		$parsed = explode('<style>', $content);
		if (count($parsed) > 1)
		{
			$content = array_shift($parsed);
			foreach($parsed as $_temp)
			{
				$_temp = explode('</style>', $_temp, 2);
				$content.= $_temp[1];
				add_inline_css($_temp[0]);
			}
		}
		
		?>

<body<?= $attrs; ?>>
	<?php
	foreach($this->do_before as $callback)
	{
		call_user_func($callback, $this);
	}
	?>
	
	<?= $header; ?>
	<?= $content; ?>
	<?= $footer; ?>
	
	<?php
	global $HTML_css, $HTML_js;
	
	$css = assets_reordered($HTML_css);
	
	$js = assets_reordered($HTML_js);

	foreach($css as $dats)
	{
		$dats = array_merge([
			'codigo'    => NULL,
			'uri'       => NULL,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => NULL,
			'loaded'    => FALSE,
			'inline'    => FALSE,
		], $dats);

		extract($dats);

		if ( ! $loaded or $position !== 'BODY')
		{
			continue;
		}

		$attr = array_merge([
			'rel' => 'stylesheet',
			'type' => 'text/css',
		], (array)$attr);
		
		$content = '';
		
		if ($inline)
		{
			$content = css_compressor($uri);
		}
		else
		{
			$attr['href'] = $uri;
		}
		
		if (empty($attr['href']) && empty($content))
		{
			continue;
		}
		
		$dats = ' ' . implode(' ', array_map(function($key, $val){
			is_array($val) and $val = implode(' ', $val);
			
			return $key . (empty($val) ? '' : '="' . htmlspecialchars($val) . '"');
		}, array_keys($attr), array_values($attr)));
		
		if ( ! empty($content))
		{
			?><style<?= $dats; ?>><?= $content; ?></style><?php
			echo vEnter . vTab;
			continue;
		}
		
		?><link<?= $dats; ?> /><?php
		echo vEnter . vTab;
	}
	
	echo vEnter . vTab;
	
	foreach($js as $dats)
	{
		$dats = array_merge([
			'codigo'    => NULL,
			'uri'       => NULL,
			'version'   => NULL,
			'prioridad' => 50,
			'attr'      => [],
			'position'  => NULL,
			'loaded'    => FALSE,
			'inline'    => FALSE,
		], $dats);

		extract($dats);

		if ( ! $loaded or $position !== 'BODY')
		{
			continue;
		}

		$attr = array_merge([
			'type' => 'application/javascript',
		], (array)$attr);
		
		$content = '';
		
		if ($inline)
		{
			$content = js_compressor($uri);
		}
		else
		{
			$attr['src'] = $uri;
		}
		
		if (empty($attr['src']) && empty($content))
		{
			continue;
		}
		
		$dats = ' ' . implode(' ', array_map(function($key, $val){
			is_array($val) and $val = implode(' ', $val);
			
			return $key . (empty($val) ? '' : '="' . htmlspecialchars($val) . '"');
		}, array_keys($attr), array_values($attr)));
		
		?><script<?= $dats; ?>><?= $content; ?></script><?php
		echo vEnter . vTab;
	}
	?>
	
	<?php
	foreach($this->do_after as $callback)
	{
		call_user_func($callback, $this);
	}
	?>
	
</body>

		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	//=====================================================
	// Funciones Mágicas
	//=====================================================
	
	/**
	 * Valida que la variable exista
	 * @see $variables
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}
	
	/**
	 * Elimina una variable
	 * @see $variables
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->variables[$name]);
	}
	
	/**
	 * Establece una variable
	 * @see $variables
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->variables[$name] = $value;
		
		if (is_callable([$this, '_' . $name . '_updated']))
		{
			call_user_func([$this, '_' . $name . '_updated'], __FUNCTION__);
		}
	}
	
	/**
	 * Devuelve una variable
	 * @see $variables
	 * @return mixed
	 */
	public function &__get($name)
	{
		if ( ! isset($this->variables[$name]))
		{
			$this->variables[$name] = NULL;
		}
		
		return $this->variables[$name];
	}
	
}