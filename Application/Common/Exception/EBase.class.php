<?php
namespace Common\Exception;

class EBase extends \Exception
{

    protected $tVar = array();

    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->tVar = \Think\Think::instance('Think\View')->get();
    }

    public function assign($name, $value = '')
    {
        if (is_array($name)) {
            $this->tVar = array_merge($this->tVar, $name);
        } else {
            $this->tVar[$name] = $value;
        }
    }

    public function get($name = '')
    {
        if ('' === $name) {
            return $this->tVar;
        }
        return isset($this->tVar[$name]) ? $this->tVar[$name] : false;
    }

    public function getTemplate()
    {
        if ($this->getCode() === 0) {
            return 'success';
        } else {
            return 'error';
        }
    }
}