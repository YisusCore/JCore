<?php

class JArray extends ArrayObject
{
	public function __construct($data = [])
	{
		parent::__construct($data);
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
		return (array)$this;
	}
	
	public function __isset ($index)
	{
		return $this->offsetExists($index);
	}

	public function __get ($index)
	{
		return $this->offsetGet($index);
	}

	public function __set ($index, $newval)
	{
		$this->offsetSet($index, $newval);
		return $this;
	}

	public function __unset ($index)
	{
		$this->offsetUnset($index);
		return $this;
	}
}