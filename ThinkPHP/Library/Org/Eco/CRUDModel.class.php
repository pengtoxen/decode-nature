<?php
namespace Org\Eco;

class CRUDModel implements \Org\Eco\CRUDEnum
{

    protected static $_debug = false;

    /**
     *
     * @var \Org\Eco\CRUDModel
     */
    protected static $_instance = null;

    static public function debug($debug = true)
    {
        self::$_debug = $debug;
        \Org\Eco\DbModel::instance()->debug();
    }

    static public function run($w, $conf, $wormhole, $transaction = false)
    {
        if (empty(self::$_instance)) {
            $cls = __CLASS__;
            self::$_instance = new $cls();
        }
        self::$_instance->hook($w, $conf, $wormhole, $transaction);
        return self::$_instance;
    }

    /**
     *
     * @var \Org\Eco\DbModel
     */
    protected $_db;

    protected $_loopMax = 100;

    protected $_idx = 0;

    private $_next = array();

    protected $_cbkey = 'cb_';

    protected $_cb = array();

    protected $_tblas = false;

    protected $_cache = array();

    public function cache()
    {
        switch (func_num_args()) {
            case 0:
                return $this->_cache;
            case 1:
                $name = func_get_arg(0);
                if (is_null($name)) {
                    $this->_cache = array();
                } else 
                    if (is_array($name)) {
                        $this->_cache = array_merge($this->_cache, $name);
                    } else {
                        return isset($this->_cache[$name]) ? $this->_cache[$name] : null;
                    }
                break;
            case 2:
            default:
                $this->_cache[func_get_arg(0)] = func_get_arg(1);
                break;
        }
        return $this;
    }

    public function __construct()
    {}

    protected function addNext($next)
    {
        $this->_next[] = $next;
    }

    protected function addPretreatment($cb)
    {
        $no = count($this->_next) - 1;
        $this->_cb[$this->_cbkey . $no] = $cb;
    }

    public function hasNext()
    {
        $n = count($this->_next);
        return $n > 0 && $this->_idx < $n;
    }

    public function pretreat()
    {
        $idx = $this->_cbkey . $this->_idx;
        return isset($this->_cb[$idx]) ? $this->_cb[$idx] : '';
    }

    public function next()
    {
        $cb = $this->_next[$this->_idx];
        if (! empty($cb) && method_exists($this, $cb)) {
            call_user_func(array(
                $this,
                $cb
            ));
        }
        $this->_idx ++;
    }

    public function noop()
    {}

    protected function conf($conf)
    {
        if (empty($conf)) {
            return $this->_cf;
        } else {
            $this->_cf = $conf;
        }
    }

    protected function _initialize($w, $conf)
    {
        $this->conf($conf);
        $method = array(
            self::UI_LIST => array(
                'before' => '',
                'model' => 'M_UI_LIST',
                'after' => 'M_UI_LIST_A'
            ),
            self::UI_ITEM => array(
                'before' => '',
                'model' => 'M_UI_ITEM',
                'after' => 'M_UI_ITEM_A'
            ),
            self::UI_DEL => array(
                'referer' => true,
                'before' => 'M_UI_DEL_B',
                'model' => 'M_DB',
                'after' => 'M_UI_DEL_A'
            ),
            self::UI_SAVE_ITEM => array(
                'referer' => true,
                'before' => 'UI_ITEM_SAVE_B',
                'model' => 'M_DB',
                'after' => 'UI_ITEM_SAVE_A'
            ),
            self::UI_SAVE_ADD => array(
                'referer' => true,
                'before' => 'M_SAVE_ADD_B',
                'model' => 'M_DB',
                'after' => 'M_SAVE_ADD_A'
            ),
            self::UI_SAVE_EDIT => array(
                'referer' => true,
                'before' => 'M_SAVE_EDIT_B',
                'model' => 'M_DB',
                'after' => 'M_SAVE_EDIT_A'
            ),
            self::UI_SAVE_ADDALL => array(
                'referer' => true,
                'model' => 'M_UI_SAVE_ADDALL'
            ),
            self::UI_SAVE_RELATION => array(
                'referer' => true,
                'before' => 'M_UI_SAVE_RELATION_B',
                'model' => 'M_DB',
                'after' => 'M_UI_SAVE_RELATION_A'
            )
        );
        if (! empty($method[$w]['referer'])) {
            if (! \Org\Eco\Request::instance()->consanguinity()) {
                \Org\Eco\Response::instance()->error('illegal');
            }
        }
        if (! empty($method[$w]['before'])) {
            call_user_func(array(
                $this,
                $method[$w]['before']
            ));
        }
        if (! empty($method[$w]['model'])) {
            $this->addNext($method[$w]['model']);
            if (! empty($this->_cf['cb']) && ! empty($this->_cf['cb']['before'])) {
                $this->addPretreatment($this->_cf['cb']['before']);
            }
            if (! empty($this->_cf['cb']) && ! empty($this->_cf['cb']['model'])) {
                $this->addNext('noop');
                $this->addPretreatment($this->_cf['cb']['model']);
            }
        }
        if (! empty($method[$w]['after'])) {
            $this->addNext($method[$w]['after']);
        }
        if (! empty($this->_cf['cb']) && ! empty($this->_cf['cb']['after'])) {
            $this->addNext('noop');
            $this->addPretreatment($this->_cf['cb']['after']);
        }
    }

    protected function M_DB()
    {
        if ($this->_tblas) {
            $this->_db = \Org\Eco\DbModel::instance()->table($this->_cf['tbl'] . ' as a');
        } else {
            $this->_db = \Org\Eco\DbModel::instance()->table($this->_cf['tbl']);
        }
        if (! empty($this->_cf['where'])) {
            $this->_db->where($this->_cf['where']);
        }
        $this->cache('db', $this->_db);
    }

    protected function M_UI_LIST()
    {
        $this->_tblas = true;
        $this->M_DB();
        $fields = '';
        $idcol = $this->getIdCol();
        if (! empty($idcol)) {
            $fields .= ',' . $idcol;
        }
        if (! empty($this->_cf['fields'])) {
            $fields .= ',' . \Org\Eco\DbModel::instance()->fields2no($this->_cf['fields'], $this->_tblas);
        }
        if (! empty($fields)) {
            $fields = substr($fields, 1);
            $this->_db->field($fields);
        }
    }

    protected function M_UI_LIST_A()
    {
        $pagination = new \Org\Eco\Pagination();
        $data = $pagination->sqlQuery($this->_db);
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->cache('data', $data);
    }

    protected function M_UI_ITEM()
    {
        $this->_tblas = true;
        $this->M_DB();
        $fields = '';
        $idcol = $this->getIdCol();
        if (! empty($idcol)) {
            $fields .= ',' . $idcol;
        }
        if (! empty($this->_cf['fields'])) {
            $fields .= ',' . \Org\Eco\DbModel::instance()->fields2no($this->_cf['fields'], $this->_tblas);
        }
        if (! empty($fields)) {
            $fields = substr($fields, 1);
            $this->_db->field($fields);
        }
    }

    protected function M_UI_ITEM_A()
    {
        $idkey = empty($this->_cf['idcol']) ? 'id' : $this->_cf['idcol'];
        $id = I('get.' . $idkey . '/d', 0);
        $data = array();
        $data[$idkey] = $id;
        if (empty($id)) {
            $data['item'] = new \stdClass();
        } else {
            $this->_db->where($data);
            $row = $this->_db->find();
            if (self::$_debug) {
                var_dump(\Org\Eco\DbModel::instance()->_sql());
            }
            $this->_db->checkResult($row);
            $data['item'] = $row;
        }
        $data['idkey'] = $idkey;
        $this->cache('data', $data);
    }

    protected function M_UI_DEL_B()
    {
        $this->putIdVal();
    }

    protected function M_UI_DEL_A()
    {
        $idval = $this->getIdVal();
        $idcol = $this->getIdCol();
        $this->_db->where(array(
            $idcol => $idval
        ));
        $result = $this->_db->delete();
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->_db->checkResult($result);
        $data = array(
            'tid' => $idval
        );
        $this->cache('data', $data);
    }

    protected function UI_ITEM_SAVE_B()
    {
        $postid = empty($this->_cf['idcol']) ? 'id' : $this->_cf['idcol'];
        $id = I('post.' . $postid . '/d', 0);
        $this->cache('id', $id);
        $this->cache('idcol', $postid);
        $data = \Org\Eco\DbModel::instance()->fields2val($this->_cf['fields'], $this->_cf['cols'], $this->_cf['ignore']);
        if (empty($data)) {
            $this->out_incomplete();
        }
        $this->cache('data', $data);
    }

    protected function UI_ITEM_SAVE_A()
    {
        $data = $this->cache('data');
        $idcol = $this->cache('idcol');
        $idval = $this->cache('id');
        if (empty($idval)) {
            $result = $this->_db->add($data);
            $data = array(
                'tid' => $result
            );
        } else {
            $this->_db->where(array(
                $idcol => $idval
            ));
            $result = $this->_db->save($data);
            $data = array(
                'tid' => $idval
            );
        }
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->_db->checkResult($result);
        $this->cache('data', $data);
    }

    protected function M_SAVE_ADD_B()
    {
        $data = \Org\Eco\DbModel::instance()->fields2val($this->_cf['fields'], $this->_cf['ignore']);
        if (empty($data)) {
            $this->out_incomplete();
        }
        $this->cache('data', $data);
    }

    protected function M_SAVE_ADD_A()
    {
        $data = $this->cache('data');
        $result = $this->_db->add($data);
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->_db->checkResult($result);
        $this->cache('lastInsertId', $result);
        $data = array(
            'tid' => $result
        );
        $this->cache('data', $data);
    }

    protected function M_SAVE_EDIT_B()
    {
        $this->putIdVal();
        if (empty($this->_cf['cols'])) {
            $this->_cf['cols'] = $this->_cf['fields'];
        }
        $data = \Org\Eco\DbModel::instance()->fields2val($this->_cf['fields'], $this->_cf['cols'], $this->_cf['ignore']);
        if (empty($data)) {
            $this->out_incomplete();
        }
        $this->cache('data', $data);
    }

    protected function M_SAVE_EDIT_A()
    {
        $idval = $this->getIdVal();
        $idcol = $this->getIdCol();
        $data = $this->cache('data');
        $this->_db->where(array(
            $idcol => $idval
        ));
        $result = $this->_db->save($data);
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->_db->checkResult($result);
        $data = array(
            'tid' => $idval
        );
        $this->cache('data', $data);
    }

    protected function M_UI_SAVE_RELATION_B()
    {
        if (empty($this->_cf['idcol'])) {
            \Org\Eco\Response::instance()->error('未定义idcol');
        }
        $this->putIdVal();
        $dataList = \Org\Eco\DbModel::instance()->fields2all($this->_cf['fields'], $this->_cf['cols'], $this->_cf['ignore']);
        if (! empty($dataList)) {
            $idval = $this->getIdVal();
            $idcol = $this->getIdCol();
            $n = count($dataList);
            for ($i = 0; $i < $n; $i ++) {
                $dataList[$i][$idcol] = $idval;
            }
        }
        $this->cache('data', $dataList);
    }

    protected function M_UI_SAVE_RELATION_A()
    {
        $idcol = $this->getIdCol();
        $idval = $this->getIdVal();
        $dataList = $this->cache('data');
        $this->_db->where(array(
            $idcol => $idval
        ));
        $result = $this->_db->delete();
        if (self::$_debug) {
            var_dump(\Org\Eco\DbModel::instance()->_sql());
        }
        $this->_db->checkResult($result);
        if (! empty($dataList)) {
            $this->_db = \Org\Eco\DbModel::instance()->table($this->_cf['tbl']);
            $result = $this->_db->addAll($dataList);
            if (self::$_debug) {
                var_dump(\Org\Eco\DbModel::instance()->_sql());
            }
            $this->_db->checkResult($result);
        }
        $data = array(
            'cnt' => $result
        );
        $this->cache('data', $data);
    }

    public function hook($w, $conf, $wormhole, $transaction = false)
    {
        if ($transaction) {
            \Org\Eco\DbModel::instance()->isError(false);
            \Org\Eco\DbModel::instance()->startTrans();
        }
        $this->_initialize($w, $conf);
        $loop = 0;
        while ($this->hasNext()) {
            if ($loop ++ > $this->_loopMax) {
                break;
            }
            $cb = $this->pretreat();
            if (! empty($cb)) {
                call_user_func(array(
                    $wormhole,
                    '_wormhole'
                ), $cb, $this->cache(), $this);
            }
            $this->next();
            if ($transaction && \Org\Eco\DbModel::instance()->isError()) {
                break;
            }
        }
        if ($transaction) {
            if(\Org\Eco\DbModel::instance()->isError()){
                \Org\Eco\DbModel::instance()->rollback();
            }else{
                \Org\Eco\DbModel::instance()->commit();
            }
        }
    }

    protected function putIdVal()
    {
        if (empty($this->_cf['idcol'])) {
            $this->_cf['idcol'] = 'id';
            $postid = $this->_cf['idcol'];
        } else {
            $cols = empty($this->_cf['cols']) ? $this->_cf['fields'] : $this->_cf['cols'];
            $postid = \Org\Eco\DbModel::instance()->getPostNo($cols, $this->_cf['idcol']);
        }
        $id = I('post.' . $postid, 0);
        if (empty($id)) {
            $this->out_incomplete();
        }
        $this->cache('id', $id);
        return $id;
    }

    protected function getIdVal()
    {
        return $this->cache('id');
    }

    protected function getIdCol()
    {
        if (! isset($this->_cf['idcol'])) {
            $this->_cf['idcol'] = 'id';
        }
        if (empty($this->_cf['idcol'])) {
            return '';
        }
        if ($this->_tblas) {
            return 'a.' . $this->_cf['idcol'];
        } else {
            return $this->_cf['idcol'];
        }
    }

    protected function out_incomplete()
    {
        \Org\Eco\Response::instance()->error('信息不完整');
    }
}
