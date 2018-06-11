<?php

namespace Common\Common;

class Util
{
    public static function arrayGroupByKey($arr, $key)
    {
        $grouped = [];
        foreach ($arr as $value) {
            $grouped[$value[$key]][] = $value;
        }
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = array_group_by_key($params);
            }
        }
        return $grouped;
    }

    public static function objToArr($obj)
    {
        return json_decode(json_encode($obj), true);
    }

    public static function console_log($data)
    {
        echo '<script>';
        echo 'console.log(' . json_encode($data, true) . ')';
        echo '</script>';
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public static function detectEnv()
    {
        if (IS_CLI) {
            return isset($argv[1]) ? trim($argv[1]) : 'pro';
        }
        $h = $_SERVER['HTTP_HOST'];
        if (strpos($h, 'dev') === 0) {
            $env = 'dev';
        } else if (strpos($h, 'loc') === 0) {
            $env = 'loc';
        } else {
            $env = 'pro';
        }
        return $env;
    }

    public static function valPhone($phone)
    {
        if (preg_match('/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/', $phone)) {
            return true;
        }
        return false;
    }

    //中文转数组
    public static function mbsToArr($str)
    {
        if (!$str) {
            return '';
        }
        return preg_split('//u', $str, null, PREG_SPLIT_NO_EMPTY);
    }

    //中文替换
    public static function mbsReplace($str, $st = 0, $et = 0, $s = '*')
    {
        if (!$str) {
            return '';
        }
        $arr = self::mbsToArr($str);
        $tot = count($arr);
        if ($et < 0) {
            $et = $tot + $et;
        } else if ($et == 0) {
            $et = $tot;
        }
        foreach ($arr as $k => $v) {
            if ($k >= $st && $k < $et) {
                $arr[$k] = $s;
            }
        }
        return implode('', $arr);
    }

    //中文替换
    public static function strReplace($str, $st = 0, $et = 0, $s = '*')
    {
        if (!$str) {
            return '';
        }
        $arr = str_split($str);
        $tot = count($arr);
        if ($et < 0) {
            $et = $tot + $et;
        } else if ($et == 0) {
            $et = $tot;
        }
        foreach ($arr as $k => $v) {
            if ($k >= $st && $k < $et) {
                $arr[$k] = $s;
            }
        }
        return implode('', $arr);
    }

    public static function valDate($d)
    {
        if (preg_match("/^[1-2][0-9][0-9][0-9][-\/\s][0-1]{0,1}[0-9][-\/\s][0-3]{0,1}[0-9](\s+[0-2][0-4][\:\s+][0-6][0-9][\:\s+][0-6][0-9])?$/", $d)) {
            return true;
        }
        return false;
    }

    public static function upload($param = [])
    {
        $config = [
            'mimes' => isset($param['mimes']) ? $param['mimes'] : [], //允许上传的文件MiMe类型
            'maxSize' => isset($param['maxSize']) ? $param['maxSize'] : 0, //上传的文件大小限制 (0-不做限制)
            'exts' => isset($param['exts']) ? $param['exts'] : ['jpg', 'gif', 'png', 'jpeg'], //允许上传的文件后缀
            'autoSub' => isset($param['autoSub']) ? $param['autoSub'] : true, //自动子目录保存文件
            'subName' => isset($param['subName']) ? $param['subName'] : ['date', 'Y-m-d'], //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => isset($param['rootPath']) ? $param['rootPath'] : 'Uploads/', //保存根路径
            'savePath' => isset($param['savePath']) ? $param['savePath'] : '',//保存路径
        ];
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if (!$info) {
            return ['_c' => 0, '_m' => $upload->getError()];
        } else {
            if (count($info) == 1) {
                $return = [
                    'url' => '/' . $config['rootPath'] . $info['file']['savepath'] . $info['file']['savename'],
                    'name' => $info['file']['name'],
                    'md5' => $info['file']['md5'],
                    'sha1' => $info['file']['sha1'],
                    'type' => $info['file']['type'],
                    'size' => $info['file']['size'],
                    'key' => $info['file']['key'],
                    'savename' => $info['file']['savename'],
                ];
            } else {
                $return = [];
                foreach ($info as $va) {
                    $data = [
                        'url' => '/' . $config['rootPath'] . $va['savepath'] . $va['savename'],
                        'name' => $va['name'],
                        'md5' => $va['md5'],
                        'sha1' => $va['sha1'],
                        'type' => $va['type'],
                        'size' => $va['size'],
                        'key' => $va['key'],
                        'savename' => $va['savename'],
                    ];
                    $return[] = $data;
                }
            }
            return ['_c' => 1, '_m' => '上传成功', 'data' => $return];
        }
        return ['_c' => 0, '_m' => '上传失败'];
    }

    //Null转为空字符串,数值类型转为字符串数字
    public static function parseNullAndNumber(&$item)
    {
        if (is_null($item)) {
            $item = '';
            return;
        }
        if (is_numeric($item)) {
            $item = strval($item);
            return;
        }
    }

    //解析数据
    public static function parseReturn(array $data = [], array $filters = [], $nested = true)
    {
        if (!$data) {
            return [];
        }
        foreach ($filters as $filter) {
            if ($nested) {
                array_walk_recursive($data, $filter);
            } else {
                array_walk($data, $filter);
            }
        }
        return $data;
    }

    //参数过滤
    public static function argFilter($arg, array $filters = [])
    {
        if (!$arg) {
            return false;
        }
        if (!$filters) {
            $filters = [
                'trim',
                'strip_tags',
            ];
        }
        if (is_array($arg) && $arg) {
            foreach ($arg as &$item) {
                $item = self::funcHandler($filters, $item);
            }
            return $arg;
        }
        return self::funcHandler($filters, $arg);
    }

    public static function funcHandler($filters, $arg)
    {
        return array_reduce($filters, function ($carry, $item) {
            if (is_callable($item)) {
                return $item($carry);
            }
            return $carry;
        }, $arg);
    }

    //二维数组中找指定值的键
    public static function recursive_array_search($needle, $column, $arr)
    {
        $target_arr = array_column($arr, $column);
        $found_key = array_search($needle, $target_arr);
        return $found_key;
    }

    /**
     * 无限极分类
     * @param $data
     * @return array
     *  $arr = [
     *  ['id' => 1, 'pid' => 0, 'name' => 'xiaomi'],
     *  ['id' => 2, 'pid' => 0, 'name' => 'tom'],
     *  ['id' => 3, 'pid' => 1, 'name' => 'jack'],
     *  ['id' => 4, 'pid' => 3, 'name' => 'jax'],
     *  ['id' => 5, 'pid' => 3, 'name' => 'rose'],
     *  ];
     */
    public static function toTree($arr, $start = 0, $id = 'id', $pid = 'pid', $children = 'children')
    {
        if (!is_array($arr)) {
            return '原数据不是数组';
        }
        if (count($arr) < 1) {
            return $arr;
        }
        //格式化
        $data = $tree = [];
        foreach ($arr as $v) {
            $data[$v[$id]] = $v;
        }
        ksort($data);
        //树形结构
        foreach ($data as $key => $value) {
            if ($value[$pid] == $start) {
                $tree[] = &$data[$value[$id]];//存的是内存地址
            } else {
                //数据变更,$return根据内存地址取的数据也变化了
                $data[$value[$pid]][$children][] = &$data[$value[$id]];
            }
        }
        return $tree;
    }

    public static function uriProtocol()
    {
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') {
            return 'https://';
        }
        return ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    }

    public static function uriHost()
    {
        return self::uriProtocol() . $_SERVER['HTTP_HOST'];
    }

    public static function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }
        return false;
    }

    public static function consanguinity()
    {
        if (strpos($_SERVER['HTTP_REFERER'], "http://" . $_SERVER['HTTP_HOST'] . "/") === 0) {
            return true;
        }
        if (strpos($_SERVER['HTTP_REFERER'], "https://" . $_SERVER['HTTP_HOST'] . "/") === 0) {
            return true;
        }
        return false;
    }

    public static function curlPost($url, $postFields)
    {
        $postFields = json_encode($postFields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
            ]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec($ch);
        if (false == $ret) {
            $result = curl_error($ch);
        } else {
            $rsp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 " . $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close($ch);
        return $result;
    }

    public static function makeCustomLog($dirName, $log, $ext = '.log')
    {
        $dir = str_replace('\\', '/', APP_PATH . '/Runtime/Logs/Custom/' . $dirName . '/');
        $fileName = $dir . date('Ymd', time()) . $ext;
        if (!file_exists($dir)) {
            @mkdir($dir, $mode = 0777, true);
            chmod($dir, 0777);
        }
        //日志信息
        if (is_array($log) || is_object($log)) {
            $log = json_encode($log);
        }
        $logHead = '[' . date('Y-m-d H:i:s', time()) . ']';
        $logTail = "\n";
        $log = $logHead . $log . $logTail;
        file_put_contents($fileName, $log, FILE_APPEND);
    }

    public static function getAge($birthday)
    {
        //格式化出生时间年月日
        $byear = date('Y', $birthday);
        $bmonth = date('m', $birthday);
        $bday = date('d', $birthday);
        //格式化当前时间年月日
        $tyear = date('Y');
        $tmonth = date('m');
        $tday = date('d');
        //开始计算年龄
        $age = $tyear - $byear;
        if ($bmonth > $tmonth || $bmonth == $tmonth && $bday > $tday) {
            $age--;
        }
        return $age;
    }

    public static function shortUrl($long_url)
    {
        $api = 'http://api.t.sina.com.cn/short_url/shorten.json'; // json
        $source = '4110768210';
        $url_long = $long_url;
        $request_url = sprintf($api . '?source=%s&url_long=%s', $source, $url_long);
        $data = file_get_contents($request_url);
        if (self::isJson($data)) {
            return json_decode($data, true);
        }
        return $data;
    }

    //对象复制(深复制和浅复制)
    public static function copy($obj, $deep = false)
    {
        if (!is_object($obj)) {
            return $obj;
        }
        return $deep ? unserialize(serialize($obj)) : clone $obj;
    }

    /*
     * 所有的组合
     * $arrays = [
     *  ['A1', 'A2', 'A3'],
     *  ['B1', 'B2', 'B3'],
     *  ['C1', 'C2'],
     * ]
     */
    public static function getCombinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }


    /*
     * 所有的组合
     * $arrays = [
     *  ['A1', 'A2', 'A3'],
     *  ['B1', 'B2', 'B3'],
     *  ['C1', 'C2'],
     * ]
     */
    public static function getCombinationsV2($arrays, $i = 0)
    {
        if (!isset($arrays[$i])) {
            return array();
        }
        if ($i == count($arrays) - 1) {
            return $arrays[$i];
        }
        $tmp = self::getCombinationsV2($arrays, $i + 1);
        $result = array();
        foreach ($arrays[$i] as $v) {
            foreach ($tmp as $t) {
                $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
            }
        }
        return $result;
    }

    public static function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
    $arr = [
        [
        'id' => 1,
        'pid' => 0,
        'title' => '父亲',
        ],
        [
        'id' => 2,
        'pid' => 1,
        'title' => '儿子',
        ],
        [
        'id' => 3,
        'pid' => 2,
        'title' => '孙子',
        ],
        [
        'id' => 4,
        'pid' => 0,
        'title' => '叔叔',
        ],
    ];
    $res = buildTree($arr, 0, [4]);
    echo '<pre/>';
    print_r($res);
    **/
    public static function buildTree(array $elements, $parentId = 0, $exclude = [])
    {
        $branch = array();
        foreach ($elements as $element) {
            if (in_array($element['id'], $exclude)) {
                continue;
            }
            if ($element['pid'] == $parentId) {
                $element['children'] = buildTree($elements, $element['id'], $exclude);
                $branch[] = $element;
            }
        }
        return $branch;
    }
}
