<?php

namespace Admin\Controller;

use Common\Common\Qiniu;

class UploadController extends \Admin\Common\BaseController
{
    public function token()
    {
        $token = Qiniu::instance()
            ->setParam([
                'callbackUrl' => 'https://illic.serveo.net/admin/Diy/callbackF',
                'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "bucket":"$(bucket)", "fdes":"$(x:desc)", "cid":' . $cid . '}',
                'callbackBodyType' => 'application/json',
            ])
            ->setCache()
            ->getToken();
        $ret = [
            'uptoken' => $token,
        ];
        $this->show($ret);
    }

    public function callback()
    {
        $resp = Qiniu::instance()
            ->callbackFunc()
            ->getResp();
        $this->show($resp);
    }
}