<?php

namespace Common\Common;

class AccessToken
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Cache;
    use \Common\Traits\Session;
    use \Common\Traits\Handler;

    public function generateToken($uinfo)
    {
        $plaintext = $uinfo['id'] . time();
        $token = crypt($plaintext);
        $access_token = [
            'token' => $token,
            'expire_time' => time() + 30 * 3600,
        ];
        $this->createToken($access_token);
        return $token;
    }

    public function expired($request_token = null)
    {
        $access_token = $this->getToken($request_token);
        if (!$access_token) {
            return true;
        }
        if ($access_token['expire_time'] < time()) {
            return true;
        }
        return false;
    }

    public function verifyToken($request_token)
    {
        $access_token = $this->getToken($request_token);
        if ($request_token !== $access_token['token']) {
            return false;
        }
        return true;
    }

    public function destroy($request_token)
    {
        $this->delToken($request_token);
    }

    protected function getToken($request_token)
    {
        return $this->_handler ? $this->_handler->getToken($request_token) : $this->getSession('access_token');
    }

    protected function createToken($access_token)
    {
        return $this->_handler ? $this->_handler->createToken($access_token) : ($this->setSession('access_token', $access_token));
    }

    protected function delToken($request_token)
    {
        if ($this->_handler) {
            $this->_handler->delToken($request_token);
        } else {
            $this->delSession('access_token');
        }
    }
}
