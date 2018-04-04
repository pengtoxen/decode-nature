<?php

namespace Common\Common;

class Util
{
    public static function getKey($arr, $id, $idName = 'id')
    {
        foreach ($arr as $key => $val) {
            if ($val[$idName] == $id) {
                return $key;
            }
        }
        return null;
    }

    public static function getParent($arr, $id, $pidName = 'parent_id', $idName = 'id')
    {
        $key = self::getKey($arr, $id, $idName);
        if ($key === null) {
            return false;
        }
        if ($arr[$key][$pidName] == 0) {
            return $arr[$key];
        } else {
            $id = $arr[$key][$pidName];
            return self::getParent($arr, $id, $pidName);
        }
    }

    public static function getParentArr($arr, $id, $pidName = 'parent_id', $idName = 'id', $return = [])
    {
        $key = self::getKey($arr, $id, $idName);
        if ($key === null) {
            return false;
        }
        if ($arr[$key][$pidName] == 0) {
            return $return;
        } else {
            $id = $arr[$key][$pidName];
            array_unshift($return, $id);
            return self::getParentArr($arr, $id, $pidName, $idName, $return);
        }
    }

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

    public static function formatSQLToPHPTime($date, $timestamp = false)
    {
        if (strpos($date, 'T') !== false) {
            $date = str_replace('T', ' ', $date);
            $date = preg_replace('/\d{3}$/', '', $date);
        }
        $date = date('Y-m-d H:i:s', strtotime($date));
        if ($timestamp) {
            return strtotime($date);
        }
        return $date;
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
            'rootPath' => isset($param['rootPath']) ? $param['rootPath'] : 'uploads/', //保存根路径
            'savePath' => isset($param['savePath']) ? $param['savePath'] : '',//保存路径
        ];
        $upload = new \Think\Upload($config);
        $info = $upload->upload();
        if (!$info) {
            return ['_c' => 0, '_m' => $upload->getError()];
        } else {
            foreach ($info as $va) {
                $url = '/' . $config['rootPath'] . $va['savepath'] . $va['savename'];
                return ['_c' => 1, '_m' => '上传成功', 'data' => $url];
            }
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
}
