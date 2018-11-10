<?php

class OutputBuffering
{
	private static $instance;
	public static function &instance()
	{
		isset(self::$instance) or self::$instance = new self();
		return self::$instance;
	}

	protected $_level;
	protected $_content;
	protected $_on;
	
    protected function __construct()
    {
        $this->_on = false;
		$this->start();
    }

    /**
     * This function will need to run at the top of all pages
     *
     * @return void
     */
    public function start()
    {
        if ( ! $this->_on)
		{
			$this->_level = ob_get_level();

//             if (function_exists('ob_gzhandler'))
// 			{
//                 ob_start('ob_gzhandler');
//             }

            ob_start();

            $this->_on = true;
        }
    }

    /**
     * This function will need to run at the bottom of all pages if output
     * buffering is turned on.  It also needs to be passed $mode from the
     * PMA_outBufferModeGet() function or it will be useless.
     *
     * @return void
     */
    public function stop()
    {
        if ($this->_on)
		{
			$this->_content = '';
			
			while (ob_get_level() > $this->_level)
			{
				$this->_content.= ob_get_contents();
            	ob_end_clean();
			}

			$this->_on = false;
        }
		
		return $this;
    }

    /**
     * Gets buffer content
     *
     * @return string buffer content
     */
    public function getContents()
    {
        return $this->_content;
    }

    /**
     * Flushes output buffer
     *
     * @return void
     */
    public function flush()
    {
        if (ob_get_status())
		{
            ob_flush();
        }
		else
		{
			flush();
        }
    }
}
