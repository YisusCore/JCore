<?php
class ResponseHtml
{	
	static $_doctypes = [
		'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
		'xhtml1-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		'xhtml1-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'xhtml1-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
		'xhtml-basic11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "https://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">',
		'html5' => '<!DOCTYPE html>',
		'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd">',
		'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">',
		'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "https://www.w3.org/TR/html4/frameset.dtd">',
		'mathml1' => '<!DOCTYPE math SYSTEM "https://www.w3.org/Math/DTD/mathml1/mathml.dtd">',
		'mathml2' => '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "https://www.w3.org/Math/DTD/mathml2/mathml2.dtd">',
		'svg10' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "https://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">',
		'svg11' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "https://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">',
		'svg11-basic' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN" "https://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd">',
		'svg11-tiny' => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN" "https://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd">',
		'xhtml-math-svg-xh' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "https://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">',
		'xhtml-math-svg-sh' => '<!DOCTYPE svg:svg PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "https://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">',
		'xhtml-rdfa-1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "https://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">',
		'xhtml-rdfa-2' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "https://www.w3.org/MarkUp/DTD/xhtml-rdfa-2.dtd">'
	];

	protected $Response;
	
	protected $variables = [];
	
	protected $head_variables = [
		'charset', 'viewport', 'css'
	];
	
	protected $body_variables = [
		'header', 'footer', 'content', 'js', 'do_before', 'do_after'
	];
	
	public function __construct($Response = NULL)
	{
		$this->Response = $Response;
		
		$this->variables['SITENAME'] = '';
		$this->variables['SITECOLOR'] = '';
		$this->variables['SITEDESCR'] = '';
		$this->variables['SITEAUTHOR'] = '';
		$this->variables['SITELOGO'] = '';
		
		$this->variables['doctype'] = 'html5';
		$this->variables['attrs'] = [];
		$this->variables['attrs']['lang'] = 'es-ES';
		$this->variables['attrs']['class'] = [];
		$this->variables['attrs']['xmlns'] = 'https://www.w3.org/1999/xhtml';
		$this->variables['attrs']['prefix'] = 'og: https://ogp.me/ns#';
		
		$this->variables['lang'] =& $this->variables['attrs']['lang'];
		$this->variables['class'] =& $this->variables['attrs']['class'];
		
		$Head = $this->variables['Head'] = new ResponseHtmlHead($this);
		$Body = $this->variables['Body'] = new ResponseHtmlBody($this, $this->Response->CONTENT);

		foreach($this->head_variables as $var)
		{
			$this->variables[$var] =& $this->variables['Head']->$var;
		}

		foreach($this->body_variables as $var)
		{
			$this->variables[$var] =& $this->variables['Body']->$var;
		}
		
		$this->_uri = url('path');
	}
	

	public function addClass(...$classes)
	{
		foreach($classes as $class)
		{
			$this->class[] = $class;
			$this->Body->class[] = $class;
		}
		return $this;
	}
	
	private $_uri;
	public function force_uri ($uri)
	{
		if (preg_match('#^Display#', $uri))
		{
			$uri = str_replace('Display\\', '', $uri);
			$uri = mb_strtolower($uri);
			$uri = explode('::', $uri);
			array_unshift($uri, '');
			$uri = implode('/', $uri);
		}
		
		if (preg_match('#^http#', $uri))
		{
			$uri = str_replace(url(), '', $uri);
		}
		
		if (preg_match('#^http#', $uri))
		{
			$uri = str_replace(filter_apply('basenmspapp_url', url()), '', $uri);
		}
		
		$this->_uri = $uri;
		return $this;
	}
	
    /**
     * Sends an HTML response to the browser
     *
     * @return void
     */
    public function response()
    {
		add_inline_js(_o(function(){
			if (false) { ?><script><?php }
			
//			return;
			?>history.replaceState([], '', '<?= url('base') . '/' . ltrim($this->_uri, '/'); ?>');<?php
			
			if (false) { ?></script><?php }
		}));
		
		ob_start();

		## Obtención de Variables
		$doctype = $this->doctype;
		isset(self::$_doctypes[$doctype]) and $doctype = self::$_doctypes[$doctype];

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
		
		$Head = $this->Head;
		$Body = $this->Body;

		
		## Impresión de Variables en la estructura HTML
		echo $doctype;
		
		?>

<html<?= $attrs; ?>>
<?= $Head->response(); ?>
<?= $Body->response(); ?>
</html>

		<?php
		
		$html = ob_get_contents();
		ob_end_clean();

		if ( ! is_localhost() and config('compress_html'))
		{
			$html = html_compressor($html);
		}
		
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
		if (in_array($name, $this->head_variables))
		{
			$this->Head->$name = $value;
			return;
		}
		
		if (in_array($name, $this->body_variables))
		{
			$this->Body->$name = $value;
			return;
		}
		
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