<?php
class Alert
{
	public static function success ($message, $code = 0)
	{
		return new self($message, 'success', $code);
	}
	
	public static function notice ($message, $code = 0)
	{
		return new self($message, 'notice', $code);
	}

	public static function error ($message, $code = 0)
	{
		return new self($message, 'error', $code);
	}

	public static function format()
    {
        $params = func_get_args();

        if (isset($params[1]) && is_array($params[1])) {
            array_unshift($params[1], $params[0]);
            $params = $params[1];
        }

        return call_user_func_array('sprintf', $params);
    }

	protected $type;
	protected $message;
	protected $code;

	protected $params = [];
	protected $hash = NULL;

	public function __construct($message, $type = 'notice', $code = 0)
	{
		$this->type = $type;
		$this->code = $code;
		$this->message = $message;
	}

	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setCode($code)
	{
		$this->code = $code;
		return $this;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setMessage($message)
	{
		$this->message = $message;
		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getMessageFormatted()
	{
		$message = $this->message;
		$params = $this->params;
		
		if (count($params) > 0)
		{
			$message = self::format($message, $params);
		}
		
		return $message;
	}

	public function __toString()
	{
		return $this->getMessageFormatted();
	}

	public function addParam($param)
	{
		is_a($param, get_class()) or $param = htmlspecialchars($param);
		
		$this->params[] = $param;
		return $this;
	}

	public function setParams($params)
	{
		$this->params = $params;
		return $this;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getArray()
	{
		return [
			'message' => $this->getMessageFormatted(),
			'type' => $this->getType(),
			'code' => $this->getCode()
		];
	}

	public function getHtml()
	{
		$array = $this->getArray();
		extract($array);

		$type === 'info' and $type = 'notice';
		$type === 'danger' and $type = 'error';

		$html = @template('alert', TRUE, $array);

		if (is_null($html))
		{
			$class = [];
			$class[] = 'alert';
			$class[] = 'alert-' . strtoslug($type);

			if ($type === 'notice')
			{
				$class[] = 'alert-info';
			}
			elseif ($type === 'error')
			{
				$class[] = 'alert-danger';
			}

			$b = '<b>';
			
			switch($type)
			{
				case 'success':
					$b.= 'ÉXITO';
					break;
				case 'notice':
					$b.= 'ALERTA';
					break;
				case 'error':
					$b.= 'ERROR';
					break;
				default:
					$b.= mb_strtoupper($type);
					break;
			}
			
			if ($code > 0)
			{
				$b.= ' ' . $code;
			}

			$b.= '</b> ';
			
			$html = '<div class="' . implode(' ', $class) . '">' . $b . $message . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
		}

		return $html;
	}

	public function getHash()
	{
		if ( ! is_null($this->hash))
		{
			return $this->hash;
		}
		
		return md5(json_encode($this->getArray()));
	}
}