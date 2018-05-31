<?php

namespace Admin\Controller;

use Common\Common\Qiniu;
use Common\Common\Util;
use Common\Constant\AdminConfig;

class OpenController extends \Admin\Common\BaseController
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
        $res = Util::upload(['savePath' => 'Specimen/Fossil/', 'exts' => ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'docx', 'doc', 'xlsx', 'xls']]);
        if (!$res[_c]) {
            $this->error($res['_m']);
        }
        $this->show($res);
    }

    public function callback()
    {
        $resp = Qiniu::instance()
            ->setParam([
                'callbackUrl' => AdminConfig::QINIU_CALLBACK_URL,
                'callbackBodyType' => 'application/json',
            ])
            ->callbackFunc()
            ->getResp();
        if(!$resp){
            $this->error();
        }
        $this->show($resp);
    }
}