<?php

namespace Admin\Controller;

use Common\Common\Util;

class TestController extends \Admin\Common\AdminController
{
    public function initData()
    {
        D('FClassification')->initData();
    }

    public function formatData()
    {
        $ret = D('location')->formatData();
        $this->show($ret);
    }
}