<?php

namespace Common\Common;

use Admin\Common\UserEnv;

require_once(VENDOR_PATH . 'TCPDF/tcpdf_config_alt.php');
require_once(VENDOR_PATH . 'TCPDF/tcpdf.php');

class PDF
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Cache;

    protected $pdf = null;
    protected $page = 1000;
    protected $baseName = '';
    protected $htmlCallback = null;
    protected $temp = RUNTIME_PATH . 'local/';
    protected $zip = null;
    protected $zipName = 'data';

    public function __construct()
    {
        $this->localDir();
    }

    public function __destruct()
    {

    }

    protected function localDir()
    {
        $uid = UserEnv::instance()->getUid();
        if (!$uid) {
            return false;
        }
        $base = 2000;
        $subDir = $uid % $base;
        $this->temp = $this->temp . '/' . $subDir . '/' . $uid . '/';
        Util::makeDir($this->temp);
    }

    public function initZip()
    {
        $this->zip = new \ZipArchive();
        $filename = $this->temp . $this->zipName . ".zip";
        if ($this->zip->open($filename, \ZipArchive::CREATE) !== true) {
            exit("cannot open <$filename>\n");
        }
    }

    public function setPage($page = 1000)
    {
        $this->page = $page;
    }

    public function setName($name = '')
    {
        $this->baseName = $name;
    }

    public function setZipName($name = '')
    {
        $this->zipName = $name;
    }

    public function setHtml($htmlFunc = [])
    {
        $this->htmlCallback = $htmlFunc;
    }

    protected function getHtml()
    {
        return $this->htmlCallback;
    }

    public function task(\Closure $format = null)
    {
        try {
            $this->checkProc();
            $chunk = $format ? $format($this->cache('lists')) : $this->cache('lists');
            foreach ($chunk as $flag => $list) {
                if (!$list) {
                    continue;
                }
                $this->initPDF();
                $this->rendData($list, $flag);
                $this->pdf = null;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function output()
    {
        $this->zip->close();
        $filename = $this->temp . $this->zipName . ".zip";
        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="' . $this->zipName . '.zip"');
        readfile($filename);
    }

    protected function checkProc()
    {
        if (!$this->cache('lists')) {
            throw new \Exception('没有设置数据');
        }
        if (!$this->htmlCallback) {
            throw new \Exception('没有设置html');
        }
    }

    protected function initPDF()
    {
        $this->pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('Hencego');
        $this->pdf->SetTitle('Student Guide');
        $this->pdf->SetSubject('Guide Tutorial');
        $this->pdf->SetKeywords('Guide Tutorial Hencego');
        //$this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
        $this->pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $this->pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $this->pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
        $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $l['a_meta_charset'] = 'UTF-8';
        $l['a_meta_dir'] = 'ltr';
        $l['a_meta_language'] = 'en';
        $l['w_page'] = 'page';
        $this->pdf->setLanguageArray($l);
        //$fontname = \TCPDF_FONTS::addTTFfont('F:\php\spgc\Application\Common\Util\TCPDF\fonts\DroidSansFallback.ttf', 'TrueTypeUnicode', '', 32);
        $this->pdf->SetFont('DroidSansFallback', '', 10);
    }

    public function rendData($list = [], $flag)
    {
        foreach ($list as $k => $item) {
            $this->pdf->AddPage();
            foreach ($this->getHtml() as $html) {
                $this->pdf->writeHTML($html($item), true, false, true, false, '');
            }
            $this->pdf->lastPage();
        }
        $this->pdf->Output($this->temp . $this->baseName . $flag . '.pdf', 'F');
        $this->zip->addFile($this->temp . $this->baseName . $flag . '.pdf', $this->baseName . $flag . '.pdf');
    }
}