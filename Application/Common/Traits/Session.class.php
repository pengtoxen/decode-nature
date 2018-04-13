<?php

namespace Common\Traits;

trait Session
{
    protected function setSession($name, $val)
    {
        $_SESSION[$name] = $val;
    }

    protected function getSession($name, $def = '')
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $def;
    }

    protected function delSession($name)
    {
        unset($_SESSION[$name]);
    }
}