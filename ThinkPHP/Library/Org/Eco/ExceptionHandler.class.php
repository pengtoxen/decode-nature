<?php
namespace Org\Eco;

class ExceptionHandler
{

    /**
     *
     * @var \Org\Eco\ExceptionHandler
     */
    protected static $_instance;

    /**
     *
     * @return \Org\Eco\ExceptionHandler
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        return self::$_instance;
    }

    protected $callback = null;

    /**
     *
     * @return the $callback
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     *
     * @param field_type $callback            
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }
}