<?php

namespace Common\Common;
require WEB_ROOT . '/ThinkPHP/Library/Vendor/qiniu/autoload.php';
use Common\Constant\AdminTbl;
use Qiniu\Auth;

class Qiniu
{
    use \Common\Traits\Singleton;

    protected $_param = [
        'accessKey' => 'gJPoWiewl11uOZytKk6SUQg-UiLP5V113mvkv9ML',
        'secretKey' => 'aL7KddQ7ZHS-2qrZgVBln9qOcVWEr66AHYnWLgvT',
        'bucket' => 'fossilhunter',
        'callbackUrl' => '',
        'callbackBody' => '',
        'expire' => 3600,
    ];
    protected $auth = null;
    protected $cache = 'upload_token_cache';
    protected $keyToOverwrite = null;
    protected $token = null;
    protected $resp = [
        '_c' => 1,
        'data' => [],
    ];

    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        $this->setAuth();
    }

    public function setParam($param)
    {
        foreach ($param as $k => $v) {
            $this->_param[$k] = $v;
        }
    }

    protected function setAuth()
    {
        $this->auth = new Auth($this->_param['accessKey'], $this->_param['secretKey']);
    }

    public function setCache($ext = '')
    {
        $this->cache = $this->cache . $ext;
        return $this;
    }

    public function keyToOverwrite($key = null)
    {
        $this->keyToOverwrite = $key;
        return $this;
    }

    protected function setPolicy()
    {
        $policy = [
            'callbackUrl' => $this->_param['callbackUrl'],
            'callbackBody' => $this->_param['callbackBody'],
            'callbackBodyType' => 'application/json',
        ];
        return $policy;
    }

    protected function setBucket()
    {
        return $this->_param['bucket'];
    }

    public function getToken($fresh = false)
    {
        if ($fresh) {
            return $this->genToken();
        }
        if (!$token = S($this->cache)) {
            $token = $this->genToken();
            S($this->cache, $token, $this->_param['expire']);
        }
        return $token;
    }

    protected function genToken()
    {
        return $this->auth->uploadToken($this->setBucket(), $this->keyToOverwrite, $this->_param['expire'], $this->setPolicy());
    }

    public function callbackFunc()
    {
        $_body = file_get_contents('php://input');
        $param = json_decode($_body, true);
        $o = M()->table(AdminTbl::TBL_DN_FILES_INFO);
        $add = [
            'bucket' => $param['bucket'],
            'fname' => $param['fname'],
            'fkey' => $param['fkey'],
            'fdes' => $param['fdes'],
            'ctime' => time(),
        ];
        $ret = $o->add($add);
        if ($ret === false) {
            return $this;
        }
        $this->resp = [
            '_c' => 0,
            'data' => [
                'cid' => $param['cid'],
                'bucket' => $param['bucket'],
                'fname' => $param['fname'],
                'fkey' => $param['fkey'],
                'fdes' => $param['fdes'],
            ],
        ];
        return $this;
    }

    public function getResp()
    {
        return $this->resp;
    }
}

