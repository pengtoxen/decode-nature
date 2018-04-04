<?php

namespace Admin\Controller;

use Common\Common\Util;
use Common\Constant\AdminTbl;

class OpenController extends \Admin\Common\AdminController
{
    public function fClassification()
    {
        $data = D('FClassification')->formatData();
        $this->show($data);
    }

    public function geoAge()
    {
        $data = D('GeoAge')->formatData();
        $this->show($data);
    }

    public function location()
    {
        $data = D('location')->formatData();
        $this->show($data);
    }

    public function upload()
    {
        $res = Util::upload(['savePath' => 'my/fossil/']);
        if (!$res[_c]) {
            echo json_encode($res['_m']);
            die;
        }
        echo json_encode($res);
        die;
    }
}