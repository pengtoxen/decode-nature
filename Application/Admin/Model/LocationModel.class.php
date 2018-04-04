<?php

namespace Admin\Model;

use Think\Model;
use Common\Constant\AdminTbl;
use Common\Common\Util;

class LocationModel extends Model
{
    protected $trueTableName = AdminTbl::TBL_DN_LOCATION;

    public function info($id = 0)
    {
        $w = ['id' => $id];
        $this->where($w);
        $ret = $this->find();
        return $ret;
    }

    public function locationName($ids = [])
    {
        $w = ['id' => ['in', $ids]];
        $this->where($w);
        $ret = $this->getField('name', true);
        return $ret ? implode("/",$ret) : '';
    }

    public function formatData()
    {
        $o = M()->table(AdminTbl::TBL_DN_LOCATION);
        $field = [
            'id as value',
            'pid as pvalue',
            'name as label',
        ];
        $o = $o->field($field);
        $data = $o->select();
        $data = Util::toTree($data, 0, 'value', 'pvalue');
        return $data;
    }
}