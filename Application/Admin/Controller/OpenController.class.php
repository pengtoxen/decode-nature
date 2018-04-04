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
        $res = Util::upload(['savePath' => 'Specimen/Fossil/']);
        if (!$res[_c]) {
            $this->error($res['_m']);
        }
        $this->show($res);
    }
}