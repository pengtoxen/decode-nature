<?php
namespace Org\Weixin;

class WxApi
{

    /**
     *
     * @var \Org\Weixin\WxApi
     */
    protected static $_instance;

    /**
     *
     * @return \Org\Weixin\WxApi
     */
    public static function instance()
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        return self::$_instance;
    }

    const KEY_SESSION_FLAG = 'openid_same';

    protected $wxConf = array();

    protected $_cache = array();

    protected function __construct()
    {
        $conf = array(
            'dev.unionglasses.com' => array(
                'appid' => 'wxfb08867ba4cabfdd',
                'appsecret' => '88ab711898614189e24cbb57e08ed523'
            ),
            'newwx.unionglasses.com' => array(
                'appid' => 'wx0a4bbe78c7cc23ab',
                'appsecret' => '74b618a2921a4c4f69a4d1a363537c17'
            )
        );
        $this->wxConf = $conf[$_SERVER[HTTP_HOST]];
    }

    public function cache()
    {
        switch (func_num_args()) {
            case 0:
                return $this->_cache;
            case 1:
                $name = func_get_arg(0);
                if (is_array($name)) {
                    $this->_cache = array_merge($this->_cache, $name);
                } else {
                    return $this->_cache[$name];
                }
                break;
            case 2:
            default:
                $this->_cache[func_get_arg(0)] = func_get_arg(1);
                break;
        }
        return $this;
    }

    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            if ($k != "sign") {
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        return $buff;
    }

    private function createOauthUrlForCode($redirectUrl, $conf = null)
    {
        $urlObj["appid"] = $conf["appid"];
        $urlObj["redirect_uri"] = $redirectUrl;
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE" . "#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
    }

    private function getContentByHttps($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    private function GetOpenidFromMp($code, $conf)
    {
        $url = $this->createOauthUrlForOpenid($code, $conf);
        $res = $this->getContentByHttps($url);
        $data = json_decode($res, true);
        $openid = $data['openid'];
        $this->cache($data);
        return $openid;
    }

    private function createOauthUrlForOpenid($code, $conf)
    {
        $urlObj["appid"] = $conf["appid"];
        $urlObj["secret"] = $conf["appsecret"];
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
    }

    private function uriScheme()
    {
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            return 'https://';
        }
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    }

    public function GetOpenid($conf = null)
    {
        if (empty($conf)) {
            if ($_SESSION[self::KEY_SESSION_FLAG]) {
                return true;
            }
            $conf = $this->wxConf;
        } else {
            if ($conf['appid'] == $this->wxConf['appid'] && $conf['appsecret'] == $this->wxConf['appsecret']) {
                $_SESSION[self::KEY_SESSION_FLAG] = true;
            } else {
                $_SESSION[self::KEY_SESSION_FLAG] = null;
            }
        }
        if (! isset($_GET['code'])) {
            $baseUrl = urlencode($this->uriScheme() . $_SERVER[HTTP_HOST] . $_SERVER['REQUEST_URI']);
            $url = $this->createOauthUrlForCode($baseUrl, $conf);
            Header("Location: $url");
            exit();
        } else {
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code, $conf);
            return $openid;
        }
    }

    public function GetCommonOpenid()
    {
        $conf = $this->wxConf;
        if (! isset($_GET['code'])) {
            $baseUrl = urlencode($this->uriScheme() . $_SERVER[HTTP_HOST] . $_SERVER['REQUEST_URI']);
            $url = $this->createOauthUrlForCode($baseUrl, $conf);
            Header("Location: $url");
            exit();
        } else {
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code, $conf);
            return $openid;
        }
    }

    public function getAccessToken($conf)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . urlencode($conf['appid']) . '&secret=' . urlencode($conf['appsecret']);
        $res = $this->getContentByHttps($url);
        $data = json_decode($res, true);
        return empty($data) ? array() : $data;
    }

    public function getJsapiTicket($conf)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . urlencode($conf['access_token']) . '&type=jsapi';
        $res = $this->getContentByHttps($url);
        $data = json_decode($res, true);
        return empty($data) ? array() : $data;
    }

    public function getCardTicket($conf)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . urlencode($conf['access_token']) . '&type=wx_card';
        $res = $this->getContentByHttps($url);
        $data = json_decode($res, true);
        return empty($data) ? array() : $data;
    }

    public function getUserinfo($conf)
    {
        $access_token = $conf['access_token'];
        $openid = $this->cache('openid');
        if (empty($openid) || empty($access_token)) {
            return array();
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . urlencode($access_token) . '&openid=' . urlencode($openid) . '&lang=zh_CN';
        $res = $this->getContentByHttps($url);
        $data = json_decode($res, true);
        return empty($data) ? array() : $data;
    }
}