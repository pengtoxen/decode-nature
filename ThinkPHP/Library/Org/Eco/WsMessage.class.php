<?php
namespace Org\Eco;

use Org\Crypt\Browser;

class WsMessage extends \Org\Eco\WsPushBase
{
    use \Common\Traits\Singleton;

    protected function __construct()
    {
        $this->wsuri['push'] = [
            'domain' => 'mqs.unionglasses.com',
            'port' => '9538'
        ];
        $this->wsuri['ws'] = [
            'uri' => 'wss://mqs.unionglasses.com:9537'
        ];
        $this->crypt['push'] = new \Org\Crypt\Browser('V4JMeOIdml6vzuITThPYZ3gvu8ZmNkFn');
    }

    public function encrypt($plaintext)
    {
        return $this->crypt['push']->encrypt($plaintext);
    }

    public function decrypt($ciphertext)
    {
        return $this->crypt['push']->decrypt($ciphertext);
    }
}