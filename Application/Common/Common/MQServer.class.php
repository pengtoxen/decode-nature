<?php
namespace Common\Common;

use Org\Eco\WsMessage;

class MQServer
{
    use \Common\Traits\Singleton;

    public function encrypt($plaintext)
    {
        return WsMessage::instance()->encrypt($plaintext);
    }

    public function decrypt($ciphertext)
    {
        return WsMessage::instance()->decrypt($ciphertext);
    }

    public function getWsTokenUri()
    {
        return WsMessage::instance()->tokenWs();
    }

    public function qrcLogin($cltId, $openid ,$uid = 0)
    {
        $data = [
            'cmd' => 'login',
            'to' => $cltId,
            'm' => $this->encrypt($openid),
            'u' => $uid,
        ];
        return WsMessage::instance()->send($data);
    }

    public function qrcLoginFail($cltId)
    {
        $data = [
            'cmd' => 'login',
            'to' => $cltId,
            'm' => '',
        ];
        return WsMessage::instance()->send($data);
    }
}

