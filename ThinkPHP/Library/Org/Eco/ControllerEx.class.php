<?php

namespace Org\Eco;

use Think\Controller;

include_once COMMON_PATH . 'DefineVar/template.php';

class ControllerEx extends Controller
{

    const PAGE = 0;

    const PAGE_SIZE = 20;

    const INVALIDATE_OPERATE_DATA = '没有可以操作的数据';

    const OPERATE_ERROR = '操作错误';

    const OPERATE_FAIL = '操作失败';

    const OPERATE_SUCCESS = '操作成功';

    protected $_cache = array();

    protected function cache()
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

    protected function isAjax()
    {
        return \Org\Eco\Request::instance()->isAjax();
    }

    protected function show($content = '', $msg = 'ok')
    {
        $ret = [
            'code' => 0,
            'data' => $content,
            'msg' => $msg,
        ];
        $this->ajaxReturn($ret);
    }


    protected function error($msg = 'fail', $content = '')
    {
        $ret = [
            'code' => 1,
            'data' => $content,
            'msg' => $msg,
        ];
        $this->ajaxReturn($ret);
    }

    protected function relocation($url)
    {
        header("Location:$url");
        exit();
    }
}