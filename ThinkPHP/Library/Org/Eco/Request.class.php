<?php
namespace Org\Eco;

class Request
{

    /**
     *
     * @var \Org\Eco\Request
     */
    protected static $_instance;

    /**
     *
     * @return \Org\Eco\Request
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        return self::$_instance;
    }

    private $_forceHtml = false;

    private $_isAjax = null;

    public $weixin = null;

    public $mobile = null;

    public $android = null;

    public $iPhone = null;

    private function __construct()
    {
        $u = $_SERVER['HTTP_USER_AGENT'];
        $this->mobile = preg_match("/AppleWebKit.*Mobile.*/", $_SERVER['HTTP_USER_AGENT']) == 1;
        $this->weixin = strpos($_SERVER['HTTP_USER_AGENT'], 'QQBrowser') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    public function forceHtml($force = false)
    {
        $this->_forceHtml = $force;
    }

    public function isAjax()
    {
        if (is_null($this->_isAjax)) {
            $this->_isAjax = false;
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                    $this->_isAjax = true;
            }
        }
        return $this->_forceHtml ? false : $this->_isAjax;
    }

    public function consanguinity()
    {
        if (strpos($_SERVER['HTTP_REFERER'], "http://" . $_SERVER['HTTP_HOST'] . "/") === 0) {
            return true;
        }
        if (strpos($_SERVER['HTTP_REFERER'], "https://" . $_SERVER['HTTP_HOST'] . "/") === 0) {
            return true;
        }
        return false;
    }
}
