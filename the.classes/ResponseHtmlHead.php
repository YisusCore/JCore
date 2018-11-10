<?php
class ResponseHtmlHead
{
	protected $ResponseHtml;
	
	protected $variables = [];
	
	public function __construct($ResponseHtml = NULL)
	{
		$this->ResponseHtml = $ResponseHtml;
		
		$this->variables['attrs'] = [];
		
		$this->variables['attrs']['itemscope'] = '';
		$this->variables['attrs']['itemtype'] = 'http://schema.org/WebSite';
		
		$this->variables['meta'] = [];
		$this->variables['meta']['name'] = [];
		$this->variables['meta']['property'] = [];
		
		$this->variables['meta_name'] =& $this->variables['meta']['name'];
		$this->variables['meta_property'] =& $this->variables['meta']['property'];
		
		$this->variables['charset'] =& APP()->charset;
		$this->variables['content_type'] = 'text/html';
		
		$this->variables['meta_name']['viewport'] = 'width=device-width, initial-scale=1, shrink-to-fit=no';
		$this->variables['viewport'] =& $this->variables['meta_name']['viewport'];
		
		$this->variables['meta_name']['HandheldFriendly'] = 'True';
		$this->variables['meta_name']['MobileOptimized'] = '320';
		$this->variables['meta_name']['mobile-web-app-capable'] = 'yes';
		$this->variables['meta_name']['apple-mobile-web-app-capable'] = 'yes';
		$this->variables['meta_name']['apple-mobile-web-app-capable'] = 'yes';
		
		$this->variables['meta_name']['robots'] = 'index, follow';
		$this->variables['robots'] =& $this->variables['meta_name']['robots'];
		
		$this->variables['meta']['http-equiv'] = [];
		$this->variables['meta']['http-equiv']['X-UA-Compatible'] = 'IE=edge,chrome=1';
		
		$this->variables['base_url'] =& url('base');
		$this->variables['abs_url'] =& url('abs');
		$this->variables['full_url'] =& url('full');
		$this->variables['cookie_base'] =& url('cookie-base');
		
		$this->variables['title'] = $ResponseHtml->SITENAME;
		$this->variables['short_title'] = NULL;
		$this->variables['canonical'] = NULL;
		
		$this->variables['meta_name']['apple-mobile-web-app-title'] =& $this->variables['title'];
		$this->variables['meta_name']['application-name'] =& $ResponseHtml->SITENAME;
		$this->variables['meta_name']['msapplication-TileColor'] =& $ResponseHtml->SITECOLOR;
		$this->variables['meta_name']['theme-color'] =& $ResponseHtml->SITECOLOR;
		
		$this->variables['jsonld'] = NULL;
		$this->variables['favicon'] = NULL;
		
		$this->variables['do_before'] = [];
		$this->variables['do_after'] = [];
		$this->variables['clean_result'] = FALSE;
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
		
		$canonical = $this->variables['canonical'];
		is_null($canonical) and $canonical = $this->variables['full_url'];
		
		$jsonld = $this->variables['jsonld'];
		is_null($jsonld) and $jsonld = '';
		
		$favicon = $this->variables['favicon'];
		is_null($favicon) and $favicon = '';
		
		if ( ! empty($favicon))
		{
			$temp = @template($favicon);
			is_null($temp) or $favicon = $temp;
		}

		?>

<head<?= $attrs; ?>>
	<?php
	foreach($this->do_before as $callback)
	{
		call_user_func($callback, $this);
	}
	?>
	
	<meta charset="<?= $this->charset; ?>">
	<meta http-equiv="Content-Type" content="<?= $this->content_type . '; charset=' . $this->charset; ?>" />
	
	<?php
	foreach($this->meta as $param => $dats)
	{
		foreach($dats as $key => $val)
		{
			is_array($val) and $val = implode(',', $val);
			?>
	<meta <?= $param; ?>="<?= htmlspecialchars($key); ?>" content="<?= htmlspecialchars($val); ?>">
			<?php
		}
	}
	?>
	
	<base href="<?= $this->base_url; ?>" />
	<script>location.base='<?= $this->base_url; ?>';location.abs='<?= $this->abs_url; ?>';location.full='<?= $this->full_url; ?>';location.cookie='<?= $this->cookie_base; ?>';</script>
	
	<title itemprop="name"><?= $this->title; ?></title>
	<link rel="canonical" href="<?= $canonical; ?>" itemprop="url" />
	
	<?= $jsonld; ?>
	
	<?= $favicon; ?>
	
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

		if ( ! $loaded or $position !== 'HEAD')
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
			continue;
		}
		
		?><link<?= $dats; ?> /><?php
	}
	
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

		if ( ! $loaded or $position !== 'HEAD')
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
	}
	?>
	
	<?php
	foreach($this->do_after as $callback)
	{
		call_user_func($callback, $this);
	}
	?>
	
</head>

		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		is_callable($this->clean_result) and $html = $this->clean_result($html, $this);
		
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