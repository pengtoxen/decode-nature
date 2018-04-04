<?php

namespace Common\Common;

use Common\Constant\AdminTbl;

class SMSServer
{
    use \Common\Traits\Singleton;

    protected $_param = [
        'account' => 'N6026464',
        'password' => 'BLlPvdKbeF248f',
        'sign' => '【汉高科技】',
        'api_send_url' => 'https://smssh1.253.com/msg/send/json',
        'api_variable_url' => 'https://smssh1.253.com/msg/variable/json',
        'api_balance_query_url' => 'https://smssh1.253.com/msg/balance/json',
    ];

    protected $_method = [];

    public function setParam($param)
    {
        foreach ($param as $k => $v) {
            $this->_param[$k] = $v;
        }
    }

    public function bindMethod($method, $name = null)
    {
        if (!$method) {
            return;
        }
        if ($name) {
            $this->_method[$name] = $method;
        }
        if (is_array($method)) {
            foreach ($method as $n => $m) {
                $this->_method[$n] = $m;
            }
        }
    }

    public function callbackMethod($methodName, $param = [])
    {
        if (!in_array($methodName, $this->_method)) {
            return $this->redirectMethod($methodName, $param);
        }
        $method = $this->_method[$methodName];
        if (is_callable($method)) {
            return $method($param);
        }
        if (is_object($method) && method_exists($method, $methodName)) {
            return $method->$methodName($param);
        }
    }

    public function send($mobile, $msg, $needstatus = false)
    {
        $data = [
            'account' => $this->_param['account'],
            'password' => $this->_param['password'],
            'msg' => $this->_param['sign'] . $msg,
            'phone' => $mobile,
            'report' => $needstatus,
        ];
        $ret = Util::curlPost($this->_param['api_send_url'], $data);
        if (!$ret) {
            return;
        }
        $param = [
            'mob' => $mobile,
            'msg' => $msg,
            'ret' => $ret,
        ];
        $this->callbackMethod('counter', $param);
        $this->callbackMethod('record', $param);
    }

    public function sendV($msg, $params, $needstatus = false)
    {
        $data = [
            'account' => $this->_param['account'],
            'password' => $this->_param['password'],
            'msg' => $this->_sign . $msg,
            'params' => $params,
            'report' => $needstatus,
        ];
        $ret = Util::curlPost($this->_param['api_variable_url'], $data);
        if (!$ret) {
            return;
        }
        $param = [
            'mob' => $params,
            'msg' => $msg,
            'ret' => $ret,
        ];
        $this->callbackMethod('counter', $param);
        $this->callbackMethod('recordV', $param);
    }

    protected function redirectMethod($methodName, $param)
    {
        switch ($methodName) {
            case 'record':
                return $this->record($param);
                break;
            case 'recordV':
                return $this->record($param);
                break;
            case 'counter':
                return $this->counter($param);
                break;
        }
    }

    protected function record($param)
    {
        if (!$this->_param['cid']) {
            return;
        }
        $ret = $param['ret'];
        $reto = json_decode($ret);
        $o = M()->table(AdminTbl::TBL_SMS_LOG);
        $data = [
            'cid' => $this->_param['cid'],
            'mobile' => $param['mob'],
            'content' => $param['msg'],
            'result' => $ret,
            'status' => isset($reto) && ($reto->code === '0') ? 1 : 2,
            'ctime' => time(),
        ];
        $o->add($data);
    }

    protected function counter()
    {
        if (!$this->_param['cid']) {
            return;
        }
        $o = M()->table(AdminTbl::TBL_SMS_COUNTER);
        $w = [
            'cid' => $this->_param['cid'],
        ];
        $o->where($w);
        $o->setDec('rest', 1);
    }

    //短信余量查询
    public function queryBalance()
    {
        $data = [
            'account' => $this->_param['account'],
            'password' => $this->_param['password'],
        ];
        $ret = Util::curlPost($this->_param['api_balance_query_url'], $data);
        $ret = json_decode($ret, true);
        return $ret;
    }
}

