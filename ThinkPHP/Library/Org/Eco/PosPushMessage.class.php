<?php
namespace Org\Eco;

use Org\Crypt\Browser;

class PosPushMessage extends \Org\Eco\WsPushBase
{
    use \Common\Traits\Singleton;

    protected function __construct()
    {
        $this->wsuri['push'] = [
            'domain' => 'mqs.unionglasses.com',
            'port' => '9538'
        ];
        $this->crypt['push'] = new \Org\Crypt\Browser('V4JMeOIdml6vzuITThPYZ3gvu8ZmNkFn');
    }
}