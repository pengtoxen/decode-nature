<?php

namespace Admin\Model;

use Think\Model;
use Common\Constant\AdminTbl;
use Common\Common\Util;

class GeoAgeModel extends Model
{
    protected $trueTableName = AdminTbl::TBL_DN_GEO_AGE;

    public function info($id = 0)
    {
        $w = ['id' => $id];
        $this->where($w);
        $ret = $this->find();
        return $ret;
    }

    public function geoAgeName($ids = [])
    {
        $w = ['id' => ['in', $ids]];
        $this->where($w);
        $ret = $this->getField('name_zh', true);
        return $ret ? implode("/", $ret) : '';
    }

    public function formatData()
    {
        $o = M()->table(AdminTbl::TBL_DN_GEO_AGE);
        $field = [
            'id as value',
            'pid as pvalue',
            'name_zh as label',
        ];
        $o = $o->field($field);
        $data = $o->select();
        $data = Util::toTree($data, 0, 'value', 'pvalue');
        $ret = $data[3]['children'];
        return $ret;
    }
}