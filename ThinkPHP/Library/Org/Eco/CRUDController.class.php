<?php
namespace Org\Eco;

class CRUDController extends \Org\Eco\ControllerEx implements \Org\Eco\CRUDEnum
{

    protected $_swap = array();

    protected $_template = '';

    protected $_db_debug = false;

    protected function swap()
    {
        switch (func_num_args()) {
            case 0:
                return $this->_swap;
            case 1:
                $name = func_get_arg(0);
                if (is_null($name)) {
                    $this->_swap = array();
                } else 
                    if (is_array($name)) {
                        $this->_swap = array_merge($this->_swap, $name);
                    } else {
                        return isset($this->_swap[$name]) ? $this->_swap[$name] : null;
                    }
                break;
            case 2:
            default:
                $this->_swap[func_get_arg(0)] = func_get_arg(1);
                break;
        }
        return $this;
    }

    protected function crud($w, $conf, $template = null, $transaction = false)
    {
        if ($this->_db_debug) {
            \Org\Eco\CRUDModel::debug();
        }
        if (! is_null($template)) {
            $this->_template = $template;
        }
        $dbmodel = \Org\Eco\CRUDModel::run($w, $conf, $this, $transaction);
        $data = $dbmodel->cache('data');
        if ($this->isAjax()) {
            parent::assign($data);
        } else {
            if (! isset($conf['tplVarFun'])) {
                $conf['tplVarFun'] = 'tplVarMulti';
            }
            if (! empty($conf['tplVarFun']) && method_exists($this, $conf['tplVarFun'])) {
                call_user_func(array(
                    $this,
                    $conf['tplVarFun']
                ), $conf);
            }
            parent::assign('data', json_encode($data));
        }
        $this->display($this->_template);
    }

    public function _wormhole($cb, $cache, $dbmodel)
    {
        if (! is_string($cb) || ! is_array($cache) || ! method_exists($dbmodel, 'noop')) {
            exit('illegal');
        }
        if (method_exists($this, $cb)) {
            $this->swap(null);
            $this->swap($cache);
            call_user_func(array(
                $this,
                $cb
            ));
            $dbmodel->cache($this->swap());
        }
    }

    protected function tplVarMulti($conf)
    {
        if (! empty($conf['tpl'])) {
            foreach ($conf['tpl'] as $key => $tpl) {
                if (is_array($tpl)) {
                    $tpl = implode($tpl, ",");
                }
                $this->assign('tpl_' . $key, $tpl);
            }
        }
        if (! empty($conf['btn'])) {
            $conf['btn'] = str_replace(",", " ", $conf['btn']);
            $conf['btn'] = preg_replace("/\\s{2,}/i", " ", $conf['btn']);
            $arr = explode(" ", $conf['btn']);
            foreach ($arr as $key) {
                if (! empty($key)) {
                    $this->assign('btn_' . $key, '1');
                }
            }
        }
        if (! empty($conf['btnExt'])) {
            $tpl = $conf['btnExt'];
            if (is_array($tpl)) {
                $tpl = implode($tpl, ",");
            }
            $this->assign('btn_ext', $tpl);
        }
        if (! empty($conf['url'])) {
            $this->assign('url', $conf['url']);
        }
    }

    /**
     *
     * @return \Org\Eco\DbModel
     */
    protected function swapDB()
    {
        $db = $this->swap('db');
        if (empty($db)) {
            $db = \Org\Eco\DbModel::instance();
        }
        return $db;
    }

    protected function swapData()
    {
        switch (func_num_args()) {
            case 0:
                return $this->swap('data');
            case 1:
                $this->swap('data', func_get_arg(0));
                break;
            case 2:
            default:
                break;
        }
        return $this;
    }

    protected function lastInsertId()
    {
        return $this->swap('lastInsertId');
    }

    protected function dblike($key, $field)
    {
        if (empty($field)) {
            return $this;
        }
        $key = strtolower($key);
        $key = str_replace("#", "##", $key);
        $key = str_replace("%", "#%", $key);
        $key = str_replace("_", "#_", $key);
        $key = str_replace("'", "\\'", $key);
        $key = '%' . $key . '%';
        $like = 'LCASE(' . $field . ')';
        $like .= " LIKE BINARY '" . $key . "' ESCAPE '#'";
        return $like;
    }
}