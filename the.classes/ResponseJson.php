<?php

class ResponseJson extends ArrayIterator
{
	protected $Response;
	protected $_status = NULL;
	public $__extra_data = [];
	
	public function __construct($Response = NULL)
	{
		$this->Response = $Response;
		parent::__construct([]);
	}
	
	public function hasStatus()
	{
		return ! is_null($this->_status);
	}
	
	public function setStatus($status, $message = NULL, $code = NULL)
	{
		$this->_status = $status;
		
		if ( ! is_null($message))
		{
			$this['message'] = $message;
		}

		if ( ! is_null($code))
		{
			$this['code'] = $code;
		}
		return $this;
	}

	public function success($message = NULL, $code = NULL)
	{
		return $this->setStatus('success', $message, $code);
	}

	public function error($error = NULL, $code = NULL)
	{
		return $this->setStatus('error', $error, $code);
	}

	public function notice($message = NULL, $code = NULL)
	{
		return $this->setStatus('notice', $message, $code);
	}

    /**
     * Sends an JSON response to the browser
     *
     * @return void
     */
    public function response()
    {
		if (count($this) === 0)
		{
			$this['message'] = $this->Response->CONTENT;
		}
		else
		{
			if ( ! isset($this['message']))
			{
				$this['message'] = '';
				is_null($this->_status) and $this->_status = TRUE;
			}
		}
		
		if ($this['message'] instanceof Alert)
		{
			$this->_status = $this['message']->getType();
			$this['code'] = $this['message']->getCode();
			$this['message'] = $this['message']->getMessage();
		}

		if ( ! is_null($this->_status))
		{
			isset($this['message']) or $this['message'] = $this->Response->CONTENT;

			if ($this['message'] instanceof Alert) {
				$this->_status = $this['message']->getType();
				$this['code'] = $this['message']->getCode();
				$this['message'] = $this['message']->getMessage();
			}

			if (is_bool($this->_status))
			{
				$this->_status = $this->_status ? 'success' : 'error';
			}

			$this['status'] = $this->_status;

			if ($this->_status === 'error')
			{
				$this['error'] = $this['message'];
				unset($this['message']);
			}
		}
		
		foreach((array)$this->__extra_data as $key => $val)
		{
			$this[$key] = $val;
		}
		
		$result = json_encode($this);
        if ($result === false)
		{
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    $error = 'No errors';
                break;
                case JSON_ERROR_DEPTH:
                    $error = 'Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                case JSON_ERROR_RECURSION:
                    $error = 'One or more recursive references in the value to be encoded';
                break;
                case JSON_ERROR_INF_OR_NAN:
                    $error = 'One or more NAN or INF values in the value to be encoded';
                break;
                case JSON_ERROR_UNSUPPORTED_TYPE:
                    $error = 'A value of a type that cannot be encoded was given';
                default:
                    $error = 'Unknown error';
                break;
            }

            return json_encode([
				'status' => false,
				'error'  => 'JSON encoding failed: ' . $error
			]);
        }
		else
		{
			return $result;
        }
	}
	
}