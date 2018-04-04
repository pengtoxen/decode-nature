<?php
namespace Common\Traits;

trait Singleton {

    /**
     *
     * @var self
     */
    protected static $inst;

    /**
     *
     * @return self
     */
    public static function instance()
    {
        if (empty(self::$inst)) {
            $cls = __CLASS__;
            self::$inst = new $cls();
        }
        return self::$inst;
    }
}