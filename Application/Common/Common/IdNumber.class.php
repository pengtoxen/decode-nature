<?php
namespace Common\Common;

class IdNumber
{

    public static function createUniqueId()
    {
        return md5(uniqid(mt_rand(), true));
    }

    public static function companyNo()
    {
        $no = time() - strtotime('2017-10-30 00:00:00');
        $no *= 1000;
        $rand = rand(0, 999);
        $no += $rand;
        return base_convert($no, 10, 36);
    }

    public static function orderNo()
    {
        $no = date('YmdHi');
        $no .= str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT );
        return $no;
    }

    public static function payNo()
    {
        $no = time();
        $no .= str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return 'ZF' . $no;
    }
}
