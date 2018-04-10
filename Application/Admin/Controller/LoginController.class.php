<?php

namespace Admin\Controller;

use Common\Common\Util;
use Common\Constant\AdminTbl;

class LoginController extends \Admin\Common\AdminController
{
    public function login()
    {
        $raw = $this->getFormParam();
        $uname = $raw['uname'];
        $pass = sha1($raw['pass']);
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'uname' => $uname,
            'pass' => $pass,
        ];
        $o->where($w);
        $ret = $o->find();
        if (!$ret) {
            $this->error();
        }
        $this->show($ret);
    }

    public function modify()
    {
        $raw = $this->getFormParam();
        $id = $raw['uid'];
        $pass = sha1($raw['pass']);
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'id' => $id
        ];
        $o->where($w);
        $up = [
            'pass' => $pass,
        ];
        $ret = $o->save($up);
        if ($ret === false) {
            $this->error();
        }
        $this->show();
    }

    public function getToken()
    {
        
    }
}