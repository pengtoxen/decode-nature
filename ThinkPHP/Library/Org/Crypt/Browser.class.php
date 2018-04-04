<?php
namespace Org\Crypt;

class Browser extends \Org\Crypt\Base
{

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
