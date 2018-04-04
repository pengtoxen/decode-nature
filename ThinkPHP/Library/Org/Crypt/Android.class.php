<?php
namespace Org\Crypt;

class Android extends \Org\Crypt\Base
{

    public function encrypt($plaintext)
    {
        $plaintext = $this->getRandomStr($this->rndnum) . $plaintext;
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $plaintext, MCRYPT_MODE_CBC, $this->iv);
        return base64_encode($ciphertext);
    }

    public function decrypt($ciphertext)
    {
        $ciphertext = base64_decode($ciphertext);
        $plaintext = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $ciphertext, MCRYPT_MODE_CBC, $this->iv);
        $n = strlen($plaintext);
        $fnd = - 1;
        for ($i = $n - 1; $i >= 0; $i --) {
            if (ord($plaintext{$i}) > 0) {
                $fnd = $i + 1;
                break;
            }
        }
        if ($fnd > - 1) {
            $plaintext = substr($plaintext, 0, $fnd);
        }
        return substr($plaintext, $this->rndnum);
    }
}
