<?php
namespace Org\Eco;

class Pagination
{

    protected $_page = array(
        '_pn' => - 1,
        '_lmt' => 10,
        '_tot' => 0
    );

    protected $_sort = '';

    /**
     *
     * @var \Org\Eco\DbModel
     */
    protected $_db;

    protected $_need_total = false;

    protected $_cache = array();

    public function __construct()
    {
        $this->pageParam();
        $this->sortParam();
    }

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

    protected function pageParam()
    {
        if ($this->_page['_pn'] == - 1) {
            $pn = I('_pn/d', 1);
            $pn = intval($pn);
            $pn = empty($pn) ? 1 : $pn;
            $this->_page['_pn'] = $pn;
            $need = I('_nt', 0);
            $this->_need_total = $need == 1;
            if (! $this->_need_total) {
                $tot = I('_tot/d', 0);
                $this->_need_total = $tot == - 1;
            }
            $lmt = I('_lmt', 0);
            if (! empty($lmt)) {
                $this->_page['_lmt'] = $lmt;
            }
        }
    }

    protected function sortParam()
    {
        $sort = I('_st/s', '');
        if (! empty($sort)) {
            $this->_sort = str_replace(':', ' ', $sort);
        }
    }

    public function sqlQuery($db)
    {
        $this->_db = $db;
        $pagi = array();
        $pagi['_pn'] = $this->_page['_pn'];
        $pagi['_lmt'] = $this->_page['_lmt'];
        $lmtstart = ($this->_page['_pn'] - 1) * $this->_page['_lmt'];
        $this->_db->limit($lmtstart, $pagi['_lmt']);
        if (! empty($this->_sort)) {
            $this->_db->order($this->_sort, true);
        }
        $sort = $this->_db->getOrder(true);
        if (! empty($sort)) {
            $this->cache('sort', $sort);
        }
        if ($this->_need_total || ! \Org\Eco\Request::instance()->isAjax()) {
            $result = $this->_db->pagination();
            $this->_db->checkResult($result);
            $pagi['_tot'] = $result['tot'];
            $this->cache('rows', $result['rows']);
        } else {
            $list = $this->_db->select();
            $this->_db->checkResult($list);
            $this->cache('rows', $list);
        }
        $this->cache('pagi', $pagi);
        return $this->cache();
    }
}
