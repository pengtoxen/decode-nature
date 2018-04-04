<?php
namespace Common\Traits;

trait Cache {

    protected $_cache = array();

    public function cache()
    {
        switch (func_num_args()) {
            case 0:
                return $this->_cache;
            case 1:
                $name = func_get_arg(0);
                if (is_array($name)) {
                    $this->_cache = array_merge($this->_cache, $name);
                } else {
                    return $this->_cache[$name];
                }
                break;
            case 2:
            default:
                $this->_cache[func_get_arg(0)] = func_get_arg(1);
                break;
        }
        return $this;
    }

    public function uncache($key = null)
    {
        switch (func_num_args()) {
            case 0:
                $this->_cache = array();
            case 1:
                $name = func_get_arg(0);
                unset($this->_cache[$name]);
                break;
            case 2:
            default:
                break;
        }
        return $this;
    }
}