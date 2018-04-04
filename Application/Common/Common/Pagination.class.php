<?php

namespace Common\Common;

class Pagination
{
    use \Common\Traits\Singleton;
    use \Common\Traits\Cache;

    protected $_page = array(
        'page' => - 1,
        'limit' => 10
    );

    protected function __construct()
    {
        $this->pageParam();
    }

    protected function pageParam()
    {
        if ($this->_page['page'] == - 1) {
            $pn = I('page/d', 1);
            $pn = intval($pn);
            $pn = empty($pn) ? 1 : $pn;
            $this->_page['page'] = $pn;
            $lmt = I('limit', 0);
            if (! empty($lmt)) {
                $this->_page['limit'] = $lmt;
            }
        }
    }

    public function getPage()
    {
        return $this->_page;
    }

    public function setLimit($limit)
    {
        $this->_page['limit'] = $limit;
    }

    public function getStart()
    {
        return ($this->_page['page'] - 1) * $this->_page['limit'];
    }

    public function getLimit($single = false)
    {
        if ($single) {
            return $this->_page['limit'];
        } else {
            return $this->getStart() . ',' . $this->_page['limit'];
        }
    }
}
