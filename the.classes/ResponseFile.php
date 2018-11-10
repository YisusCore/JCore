<?php
class ResponseFile
{	
	protected $Response;
	
	protected $variables = [];
	
	public function __construct($Response = NULL)
	{
		$this->Response = $Response;
	}
	
    /**
     * Sends an HTML response to the browser
     *
     * @return void
     */
    public function response()
    {
		return $this->Response->CONTENT;
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