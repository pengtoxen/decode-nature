<?php

namespace Common\Common;

use Jg\Common\User;
use Common\Common\Util;

require_once(VENDOR_PATH . 'phpqrcode/phpqrcode.php');

class QR
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Cache;

    protected $tempDir = RUNTIME_PATH . 'local/';
    protected $baseDir = '';
    protected $errorCorrectionLevel = 'L';
    protected $matrixPointSize = 8;
    protected $margin = 1;

    public function __construct()
    {
        $this->localDir();
    }

    protected function localDir()
    {
        $uid = User::instance()->getUid();
        if (!$uid) {
            return false;
        }
        $base = 2000;
        $subDir = $uid % $base;
        $this->tempDir = $this->tempDir . '/' . $subDir . '/' . $uid . '/';
        $this->baseDir = $subDir . '/' . $uid . '/';
        Util::makeDir($this->temp);
    }

    public function base64($data)
    {
        ob_start();
        \QRcode::png($data, null, $this->errorCorrectionLevel, $this->matrixPointSize, $this->margin);
        $imageString = base64_encode(ob_get_contents());
        ob_end_clean();
        return 'data:image/png;base64,' . $imageString;
    }

    public function tempPath($data)
    {
        $name = uniqid() . '.jpg';
        $path = $this->tempDir . $name;
        \QRcode::png($data, $path, $this->errorCorrectionLevel, $this->matrixPointSize, $this->margin);
        return 'http://127.0.0.1/local/' . $this->baseDir . $name;
        //return $path;
    }
}