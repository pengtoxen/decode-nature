<?php
namespace Org\Eco;

class Route
{

    public static function reroute($space, $class, $method, $param = null, $reroute = true)
    {
        $cls = "\\" . $space . "\\Controller\\" . $class . "Controller";
        if (! class_exists($cls)) {
            throw new \Exception('The class ' . $cls . ' do not exist');
        }
        $appobj = new $cls();
        if (! method_exists($appobj, $method)) {
            throw new \Exception('The method ' . $method . ' of ' . $cls . ' do not exist');
        }
        if (! is_callable(array(
            $appobj,
            $method
        ))) {
            throw new \Exception('The method ' . $method . ' of ' . $cls . ' is not callable');
        }
        if (method_exists($appobj, '_initialize')) {
            call_user_func(array(
                $appobj,
                '_initialize'
            ));
        }
        if ($reroute) {
            // MODULE_NAME CONTROLLER_NAME ACTION_NAME
        }
        if (! empty($param)) {
            call_user_func(array(
                $appobj,
                $method
            ), $param);
        } else {
            call_user_func(array(
                $appobj,
                $method
            ));
        }
        exit();
    }
}