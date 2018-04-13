<?php

namespace Admin\Common;

use Common\Constant\AdminTbl;

class UserEnv
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Session;
    use \Common\Traits\Handler;

    const LOGIN_WEB_UID = 'login_web_uid';

    protected $_uid = null;
    protected $_info = null;
    protected $_roles = null;

    public function setUid($uid)
    {
        $this->_uid = $uid;
        if (!$this->_handler) {
            $this->setSession(self::LOGIN_WEB_UID, $uid);
            return;
        }
        $this->_handler->setUid();
    }

    public function getUid()
    {
        return $this->_uid ? $this->_uid : $this->getSession(self::LOGIN_WEB_UID);
    }

    public function getInfo()
    {
        if (!$this->_info) {
            $o = M()->table(AdminTbl::TBL_DN_USER);
            $w = [
                'id' => $this->getUid(),
            ];
            $o->where($w);
            $this->_info = $o->find();
        }
        return $this->_info;
    }

    public function getRoles()
    {
        return [
            'admin',
        ];
    }

    public function logout()
    {
        $this->delSession(self::LOGIN_WEB_UID);
    }
}
