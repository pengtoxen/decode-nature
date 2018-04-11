<?php

namespace Common\Common;

class AccessToken
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Cache;

    public function generateToken($uinfo)
    {
        $plaintext = $uinfo['id'] . time();
        $token = Crypt::instance()->encrypt($plaintext);
        $access_token = [
            'token' => $token,
            'expire_time' => time() + 30 * 3600,
        ];
        $_SESSION['access_token'] = $access_token;
        return $token;
    }

    public function expired()
    {
        $access_token = $_SESSION['access_token'];
        if (!$access_token) {
            return true;
        }
        if ($access_token['expire_time'] < time()) {
            return true;
        }
        return false;
    }

    public function verifyToken($token)
    {
        $access_token = $_SESSION['access_token'];
        if ($token !== $access_token['token']) {
            return false;
        }
        return true;
    }

    public function destroy()
    {
        unset($_SESSION['access_token']);
    }
}
