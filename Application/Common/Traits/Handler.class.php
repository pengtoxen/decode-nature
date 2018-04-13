<?php

namespace Common\Traits;

trait Handler
{
    protected $_handler;

    public function setHandler($handler = null)
    {
        $this->_handler = $handler;
    }
}