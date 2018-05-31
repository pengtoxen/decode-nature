<?php

namespace Admin\Controller;

use Admin\Common\UserEnv;
use Common\Common\Qiniu;
use Common\Constant\AdminConfig;

class UploadController extends \Admin\Common\AdminController
{
    public function token()
    {
        $uid = UserEnv::instance()->getUid();
        $token = Qiniu::instance()
            ->setParam([
                'callbackUrl' => AdminConfig::QINIU_CALLBACK_URL,
                'callbackBody' => '{"fname":"$(fname)", "fkey":"$(key)", "bucket":"$(bucket)", "fdes":"$(x:desc)", "uid":' . $uid . '}',
                'callbackBodyType' => 'application/json',
            ])
            ->setCache()
            ->getToken();
        $this->show($token);
    }
}