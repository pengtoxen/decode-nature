<?php

namespace Admin\Controller;

use Common\Common\AccessToken;
use Admin\Common\UserEnv;
use Common\Common\Util;
use Common\Constant\AdminConfig;
use Common\Constant\AdminTbl;

class UserController extends \Admin\Common\AdminController
{
    public function info()
    {
        $info = UserEnv::instance()->getInfo();
        $roles = UserEnv::instance()->getRoles();
        $ret = [
            'username' => $info['username'],
            'name' => $info['nickname'],
            'avatar' => $info['avatar'],
            'introduction' => '',
            'roles' => $roles,
        ];
        $this->show($ret);
    }

    public function logout()
    {
        $token = $_SERVER['HTTP_X_TOKEN'];
        AccessToken::instance()->destroy($token);
        UserEnv::instance()->logout();
        $this->show();
    }

    public function edit()
    {
        $raw = $this->getFormParam();
        $username = $raw['username'];
        $oldP = $raw['opassword'];
        $newP = $raw['npassword'];
        if ($newP) {
            $newP = $this->passwordHash(trim($newP));
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'username' => $username,
        ];
        $o->where($w);
        $r = $o->find();
        if (!password_verify($oldP, $r['password'])) {
            $this->error('原始密码错误', ['field' => 'opassword']);
        }
        if (!$newP) {
            $this->error('密码不能为空', ['field' => 'npassword']);
        }
        if (!$raw['nickname']) {
            $this->error('昵称不能为空', ['field' => 'nickname']);
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'username' => $username,
        ];
        $o->where($w);
        $up = [
            'password' => $newP,
            'nickname' => $raw['nickname'],
            'avatar' => $this->avatarData($raw['avatar']),
        ];
        $ret = $o->save($up);
        if ($ret === false) {
            $this->error();
        }
        $o = M()->table(AdminTbl::TBL_DN_USER);
        $w = [
            'username' => $username,
        ];
        $o->where($w);
        $field = [
            'nickname',
            'avatar',
        ];
        $o->field($field);
        $data = $o->find();
        $this->show($data);
    }

    protected function passwordHash($password)
    {
        $options = [
            'cost' => 12,
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    protected function avatarData($url)
    {
        if (!$url) {
            return '';
        }
        $host = str_replace('/','\/',AdminConfig::QINIU_BASE_URL);
        $pattern = '/^'.$host.'/';
        return  preg_replace($pattern, '', $url);
    }
}