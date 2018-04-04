<?php
namespace Org\Eco;

class JsonOut
{

    const KEY_CODE = '_c';

    const KEY_MESSAGE = '_m';

    const KEY_URL = 'url';

    const KEY_REDIRECT = 'redir';

    const CODE_SUCCESS = 0;

    const CODE_ERROR = 1;

    const CODE_FAILED = 2;

    /**
     *
     * @var \Org\Eco\JsonOut
     */
    protected static $_instance;

    /**
     *
     * @return \Org\Eco\JsonOut
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        return self::$_instance;
    }

    public function ajaxReturn($data)
    {
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($data));
    }

    public function show($data = null, $content = null, $code = null)
    {
        if (empty($data)) {
            $data = array();
        }
        $data[self::KEY_MESSAGE] = empty($content) ? '' : $content;
        $data[self::KEY_CODE] = empty($code) ? self::CODE_SUCCESS : intval($code);
        $this->ajaxReturn($data);
    }

    public function error($data = null, $msg = '', $code = null)
    {
        if (empty($data)) {
            $data = array();
        }
        $data[self::KEY_MESSAGE] = empty($msg) ? '' : $msg;
        $data[self::KEY_CODE] = empty($code) ? self::CODE_ERROR : intval($code);
        $this->ajaxReturn($data);
    }
}