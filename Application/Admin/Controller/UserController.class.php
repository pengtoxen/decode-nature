<?php

namespace Admin\Controller;

use Common\Common\AccessToken;
use Common\Common\Util;
use Common\Constant\AdminTbl;

class UserController extends \Admin\Common\AdminController
{
    public function logout()
    {
        AccessToken::instance()->destroy();
        $this->show();
    }

    public function modify()
    {
        $raw = $this->getFormParam();
        $id = $raw['uid'];
        if($raw['password']){
            $pass = $this->passwordHash(trim($raw['password']));
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'id' => $id,
        ];
        $o->where($w);
        $up = [
            'password' => $pass,
        ];
        $ret = $o->save($up);
        if ($ret === false) {
            $this->error();
        }
        $this->show();
    }

    protected function passwordHash($password)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
}