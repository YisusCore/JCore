<?php
/**
 * JArray.php
 * Archivo de clase JArray
 *
 * @filesource
 */
class JArray extends ArrayObject
{
	protected $_callbacks;
    public function __construct($data = [], $callbacks = [])
    {
        parent::__construct($data);
		
		$this->_callbacks = $callbacks;
    }
    public function first()
    {
        if (isset($this[0]))
        {
            return $this[0];
        }
        $return = NULL;
        foreach($this as $v)
        {
            $return = $v;
            break;
        }
        return $return;
    }
    function __toArray()
    {
		isset($this->_callbacks['before_toarray']) and
		$this->_callbacks['before_toarray']($this);
		$return = (array)$this;
		isset($this->_callbacks['toarray']) and
		$this->_callbacks['toarray']($return, $this);
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $this);
		return $return;
    }
	
	/**
     * __call ()
     */
    public function __call ($name, $args)
    {
        if (preg_match('#^set_(.+)#', $name))
        {
        	$index = preg_replace('#^set_#', '', $name);
        	return $this->__set($index, $args[0]);
        }
        if (preg_match('#^get_(.+)#', $name))
        {
        	$index = preg_replace('#^get_#', '', $name);
        	return $this->__get($index);
        }
		
		trigger_error('Call to undefined method ' . get_called_class() . '::' . $name . '()', E_USER_ERROR);
		
        return $this;
    }
    /**
     * __invoke ()
     */
    public function __invoke()
    {
		$_args = func_get_args();
		
		$_args[] = NULL;
		$_args[] = NULL;
		
		list($index, $newval) = $_args;
		
		if (is_null($index))
		{
			return $this->__toArray();
		}
		
		if (is_null($newval))
		{
			return $this->__get($index);
		}
		
        return $this->__set($index, $newval);
    }
    /**
     * __toString ()
     */
	public function __toString()
	{
		return json_encode($this);
	}
	//-------------------------------------------
	// Object Access
	//-------------------------------------------
    public function __isset ($index)
    {
		$return = $this->offsetExists($index);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $index, $this);
        return $return;
    }
    public function __get ($index)
    {
		$return = $this->offsetGet($index);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $index, $this);
        return $return;
    }
    public function __set ($index, $newval)
    {
		$this->offsetSet($index, $newval);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($newval, $index, $this);
		
        return $this;
    }
    public function __unset ($index)
    {
        $this->offsetUnset($index);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($index, $this);
		
        return $this;
    }
	//-------------------------------------------
	// Array Access
	//-------------------------------------------
	public function offsetExists ($index)
	{
		isset($this->_callbacks['before_exists']) and
		$this->_callbacks['before_exists']($index, $this);
		
		$return = parent::offsetExists($index);
		
		isset($this->_callbacks['exists']) and
		$this->_callbacks['exists']($return, $index, $this);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $index, $this);
        return $return;
	}
	public function offsetGet ($index)
	{
		isset($this->_callbacks['before_get']) and
		$this->_callbacks['before_get']($index, $this);
		
		isset($this->_callbacks['before_get_' . $index]) and
		$this->_callbacks['before_get_' . $index]($index, $this);
		
		$return = parent::offsetGet($index);
		
		isset($this->_callbacks['get']) and
		$this->_callbacks['get']($return, $index, $this);
		
		isset($this->_callbacks['get_' . $index]) and
		$this->_callbacks['get_' . $index]($return, $index, $this);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($return, $index, $this);
        return $return;
	}
	public function offsetSet ($index, $newval)
	{
		isset($this->_callbacks['before_set']) and
		$this->_callbacks['before_set']($newval, $index, $this);
		
		isset($this->_callbacks['before_set_' . $index]) and
		$this->_callbacks['before_set_' . $index]($newval, $this);
		
		parent::offsetSet($index, $newval);
		
		isset($this->_callbacks['set']) and
		$this->_callbacks['set']($newval, $index, $this);
		
		isset($this->_callbacks['set_' . $index]) and
		$this->_callbacks['set_' . $index]($newval, $this);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($newval, $index, $this);
	}
	public function offsetUnset ($index)
	{
		isset($this->_callbacks['before_unset']) and
		$this->_callbacks['before_unset']($index, $this);
		
		parent::offsetUnset($index);
		
		isset($this->_callbacks['unset']) and
		$this->_callbacks['unset']($index, $this);
		
		isset($this->_callbacks[__FUNCTION__]) and
		$this->_callbacks[__FUNCTION__]($index, $this);
	}
}
