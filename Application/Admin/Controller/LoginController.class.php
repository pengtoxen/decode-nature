<?php

namespace Admin\Controller;

use Common\Common\AccessToken;
use Common\Constant\AdminTbl;

class LoginController extends \Admin\Common\BaseController
{
    public function login()
    {
        $raw = $this->getFormParam();
        $uname = trim($raw['username']);
        $pass = trim($raw['password']);
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'username' => $uname,
        ];
        $o->where($w);
        $uinfo = $o->find();
        if (!$uinfo) {
            $this->error();
        }
        $pHash = $uinfo['password'];
        if (!password_verify($pass, $pHash)) {
            $this->error();
        }
        $token = AccessToken::instance()->generateToken($uinfo);
        $this->show($token);
    }

    public function register()
    {
        $raw = $this->getFormParam();
        $uname = trim($raw['username']);
        $pass = trim($raw['password']);
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'username' => $uname,
        ];
        $o->where($w);
        $uinfo = $o->find();
        if ($uinfo) {
            $this->error('用户名重复');
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $add = [
            'username' => $uname,
            'password' => $this->passwordHash($pass),
        ];
        $uid = $o->add($add);
        if ($uid === false) {
            $this->error();
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'id' => $uid,
        ];
        $o->where($w);
        $uinfo = $o->find();
        $token = AccessToken::instance()->generateToken($uinfo);
        $this->show($token);
    }

    protected function passwordHash($password)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }
}