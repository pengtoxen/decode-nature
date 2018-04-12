<?php

namespace Admin\Common;

use Common\Common\AccessToken;

class AdminController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->identify();
    }

    protected function identify()
    {
        $raw = $this->getFormParam();
        $token = $raw['token'];
        if (!AccessToken::instance()->verifyToken($token)) {
            $this->error('无权限');
        }
        if (AccessToken::instance()->expired()) {
            $this->error('access_token无效');
        }
    }
}