<?php
namespace Cli\Controller;

use Common\Constant\Mall;

class IndexController extends \Cli\Common\CliController
{

    public function index()
    {
        $db = M()->table('menus');
        $rows = $db->select();
        var_dump($rows);
    }

    public function WxAccessToken()
    {
        $db = M()->table(Mall::TBL_WX_INF);
        $rows = $db->field('id,appid,appsecret')->select();
        if (! empty($rows)) {
            foreach ($rows as $conf) {
                \Common\Wechat\WxConf::instance()->saveAccessToken($conf);
            }
        }
    }
}