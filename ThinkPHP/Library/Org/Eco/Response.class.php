<?php
namespace Org\Eco;

class Response
{

    /**
     *
     * @var \Org\Eco\Response
     */
    protected static $_instance;

    /**
     *
     * @return \Org\Eco\Response
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        return self::$_instance;
    }

    protected $tVar = array();

    protected $view = null;

    protected function __construct()
    {
        $this->view = \Think\Think::instance('Think\View');
    }

    public function get($name = '')
    {
        return $this->view->get($name);
    }

    public function assign($name, $value = '')
    {
        $this->view->assign($name, $value);
        return $this;
    }

    public function assignList($name, $value = '')
    {
        $value = empty($value) ? array() : $value;
        $this->assign($name, $value);
    }

    public function assignJson($name, $value = '')
    {
        if (\Org\Eco\Request::instance()->isAjax()) {
            $this->assign($name, $value);
        } else {
            if (empty($value)) {
                $value = new \stdClass();
                $this->assign($name, json_encode($value, JSON_FORCE_OBJECT));
            } else {
                $this->assign($name, json_encode($value));
            }
        }
    }

    public function assignListJson($name, $value = '')
    {
        if (\Org\Eco\Request::instance()->isAjax()) {
            $this->assign($name, $value);
        } else {
            if (empty($value)) {
                $value = array();
            }
            $this->assign($name, json_encode($value));
        }
    }

    public function display($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '')
    {
        if (\Org\Eco\Request::instance()->isAjax()) {
            \Org\Eco\JsonOut::instance()->show($this->get());
        } else {
            $this->view->display($templateFile, $charset, $contentType, $content, $prefix);
        }
    }

    public function show($content = '', $code = 0)
    {
        ET($content, $code);
    }

    public function error($message = '', $code = 1)
    {
        ET($message, $code);
    }

    public function pageTpl($tplname)
    {
        $this->display(WEB_ROOT . '/Public/Tpl/' . $tplname . '.html');
        exit();
    }
}
