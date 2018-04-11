<?php
namespace Common\Common;

class Crypt
{

    /**
     *
     * @var \Common\Common\Crypt
     */
    protected static $_instance;

    protected static $_keyiv = array(
        'def' => 'p4cltBnjEed1VLvbpBBFcmWmKichmjzF'
    );

    /**
     *
     * @return \Common\Common\Crypt
     */
    public static function instance($conf = null)
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        if (is_string($conf)) {
            if (! isset(self::$_keyiv[$conf])) {
                $conf = 'def';
            }
        } elseif (is_array($conf)) {
            if (! isset($conf['key']) || ! isset($conf['iv'])) {
                $conf = 'def';
            }
        } else {
            $conf = 'def';
        }
        if (is_string($conf)) {
            $keys = self::$_keyiv[$conf];
            $conf['key'] = substr($keys, 0, 16);
            $conf['iv'] = substr($keys, 16, 16);
        }
        self::$_instance->setKeyIv($conf);
        return self::$_instance;
    }

    private $key = '';

    private $iv = '';

    private $rndnum = 6;

    protected function __construct()
    {}

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
        $this->key = $conf['key'];
        $this->iv = $conf['iv'];
    }

    public function encrypt($plaintext)
    {
        $plaintext = $this->getRandomStr($this->rndnum) . $plaintext;
        $ciphertext = openssl_encrypt($plaintext, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        return base64_encode($ciphertext);
    }

    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        $plaintext = openssl_decrypt($ciphertext, 'AES-128-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        return substr($plaintext, $this->rndnum);
    }
}
