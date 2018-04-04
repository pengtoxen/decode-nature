<?php
namespace Org\Crypt;

abstract class Base
{

    protected  $key = '';

    protected $iv = '';

    protected $rndnum = 6;

    public function __construct($keyiv = null, $num = null)
    {
        if (! empty($keyiv)) {
            $this->setKeyIv($keyiv);
        }
        if (! empty($num)) {
            $this->setRandNum($num);
        }
    }

    protected function getRandomStr($len)
    {
        $str = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $length = strlen($str) - 1;
        $key = "";
        for ($i = 0; $i < $len; $i ++) {
            $key .= $str{mt_rand(0, $length)};
        }
        return $key;
    }

    public function setKeyIv($conf)
    {
        if (is_array($conf)) {
            $this->key = $conf['key'];
            $this->iv = $conf['iv'];
        } elseif (is_string($conf)) {
            $this->key = substr($conf, 0, 16);
            $this->iv = substr($conf, 16, 16);
        }
    }

    public function setRandNum($num)
    {
        $this->rndnum = $num;
    }

    public function encrypt($plaintext)
    {}

    public function decrypt($ciphertext)
    {}
}
