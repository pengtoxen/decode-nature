<?php
namespace Org\Crypt;

class Erp
{

    protected $key = '';

    public function __construct($key = null)
    {
        if (! empty($key)) {
            $this->key = $key;
        }
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function encrypt($src = '')
    {
        $keyPos = - 1;
        $srcLen = mb_strlen($src);
        $keyLen = mb_strlen($this->key);
        $range = 255;
        $num = rand(0, $range);
        $dest = $this->getHex($num);
        // 十进制=>十六进制
        if ($src == null) {
            $src = '';
        }
        for ($i = 0; $i < $srcLen; $i ++) {
            // ascii码值+随机数再求余
            $SrcAsc = (ord($src[$i]) + $num) % 255;
            if ($keyPos < $keyLen - 1) {
                $keyPos = $keyPos + 1;
            } else {
                $keyPos = 0;
            }
            $ss = ord($this->key[$keyPos]);
            $SrcAsc = $SrcAsc ^ $ss;
            $dest .= $this->getHex($SrcAsc);
            $num = $SrcAsc;
        }
        return trim($dest);
    }

    public function decrypt($src = '')
    {
        $keyPos = - 1;
        $result = "";
        $srcLen = mb_strlen($src);
        $keyLen = mb_strlen($this->key);
        $TmpSrcAsc = "";
        if ($srcLen == 2) {
            return null;
        }
        try {
            $offset = intval(hexdec(mb_substr($src, 0, 2)));
        } catch (\Exception $e) {
            $offset = 0;
        }
        $srcPos = 3;
        do {
            try {
                $s = mb_substr($src, $srcPos - 1, 2);
                $srcAsc = intval(hexdec($s));
            } catch (\Exception $e) {
                $srcAsc = 0;
            }
            if ($keyPos < $keyLen - 1) {
                $keyPos = $keyPos + 1;
            } else {
                $keyPos = 0;
            }
            $ss = ord(mb_substr($this->key, $keyPos, 1));
            $TmpSrcAsc = $srcAsc ^ $ss; // 求异或
            if ($TmpSrcAsc <= $offset) {
                $TmpSrcAsc = 255 + $TmpSrcAsc - $offset;
            } else {
                $TmpSrcAsc = $TmpSrcAsc - $offset;
            }
            $d = chr(intval($TmpSrcAsc));
            $result .= $d;
            $offset = $srcAsc;
            $srcPos = $srcPos + 2;
        } while (! ($srcPos >= $srcLen));
        return $result;
    }

    protected function getHex($num)
    {
        $msg = (string) dechex($num); // 把十进制转换为十六进制
        $msg = strtoupper($msg);
        if (mb_strlen($msg) == 1) {
            $msg = "0" . $msg;
        }
        return $msg;
    }
}
