<?php

namespace Admin\Controller;

use Common\Common\Util;
use Common\Common\Pagination;
use Common\Common\ID;
use Common\Constant\AdminTbl;

class FossilController extends \Admin\Common\AdminController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function lists()
    {
        $o = M()->table('dn_fossil');
        $name = I('name_zh/s');
        if ($name) {
            $w['name_zh'] = ['like', '%' . $name . '%'];
        }
        $classification = I('classification/s');
        if ($classification) {
            $w['classification_id'] = end(explode("\n", $classification));
        }
        $geo_age = I('geo_age/s');
        if ($geo_age) {
            $w['geo_age_id'] = end(explode("\n", $geo_age));
        }
        $o->where($w);
        $o->limit(Pagination::instance()->getLimit());
        $o->order('ctime desc');
        $ret = $o->select();
        $o = M()->table('dn_fossil');
        $o->where($w);
        $total = $o->count();
        $ret = [
            'lists' => $ret,
            'total' => $total,
        ];
        $this->show($ret);
    }

    public function operate()
    {
        $raw = $this->getFormParam();
        $o = M()->table('dn_fossil');
        if (isset($raw['classification']) && $raw['classification']) {
            $raw['classification'] = explode("\n", $raw['classification']);
            $raw['classification_id'] = end($raw['classification']);
            $info = D('FClassification')->info($raw['classification_id']);
            $raw['classification_name'] = $info ? $info['name_zh'] : '';
        }
        if (isset($raw['district']) && $raw['district']) {
            $raw['district'] = explode("\n", $raw['district']);
            $raw['district_id'] = end($raw['district']);
            $info = D('Location')->locationName($raw['district']);
            $raw['district_name'] = $info ?: '';
        }
        if (isset($raw['geo_age']) && $raw['geo_age']) {
            $raw['geo_age'] = explode("\n", $raw['geo_age']);
            $raw['geo_age_id'] = end($raw['geo_age']);
            $info = D('GeoAge')->geoAgeName($raw['geo_age']);
            $raw['geo_age_name'] = $info ?: '';
        }
        $add = [
            'classification_id' => isset($raw['classification_id']) ? $raw['classification_id'] : 0,
            'classification_id_arr' => isset($raw['classification']) ? implode("\n", $raw['classification']) : 0,
            'district_id' => isset($raw['district_id']) ? $raw['district_id'] : 0,
            'district_id_arr' => isset($raw['district']) ? implode("\n", $raw['district']) : 0,
            'geo_age_id' => isset($raw['geo_age_id']) ? $raw['geo_age_id'] : 0,
            'geo_age_id_arr' => isset($raw['geo_age']) ? implode("\n", $raw['geo_age']) : 0,
            'serial_no' => ID::specimenNo(),
            'name_zh' => isset($raw['name_zh']) ? $raw['name_zh'] : '',
            'name_en' => isset($raw['name_en']) ? $raw['name_en'] : '',
            'owner' => isset($raw['owner']) ? $raw['owner'] : '',
            'address' => isset($raw['address']) ? $raw['address'] : '',
            'geo_layer' => isset($raw['geo_layer']) ? $raw['geo_layer'] : '',
            'longitude' => isset($raw['longitude']) ? $raw['longitude'] : '',
            'latitude' => isset($raw['latitude']) ? $raw['latitude'] : '',
            'altitude' => isset($raw['altitude']) ? $raw['altitude'] : '',
            'abstract' => isset($raw['abstract']) ? $raw['abstract'] : '',
            'description' => isset($raw['description']) ? $raw['description'] : '',
            'photo' => isset($raw['photo']) ? $this->photoData($raw['photo']) : '',
            'restore_photo' => isset($raw['rphoto']) ? $this->photoData($raw['rphoto']) : '',
            'storage_name' => isset($raw['storage_name']) ? $raw['storage_name'] : '',
            'storage_no' => isset($raw['storage_no']) ? $raw['storage_no'] : '',
            'num' => isset($raw['num']) ? $raw['num'] : 0,
            'material' => isset($raw['material']) ? $this->materialData($raw['material']) : '',
            'status' => 1,
            'get_time' => isset($raw['get_time']) ? strtotime($raw['get_time']) : 0,
            'ctime' => time(),
        ];
        $id = $raw['id'];
        if (!$id) {
            $ret = $o->add($add);
            if ($ret === false) {
                $this->error();
            }
            $this->show($ret);
        } else {
            $w = ['id' => $id];
            $o->where($w);
            $up = $raw;
            if (isset($up['classification'])) {
                $up['classification_id'] = end($raw['classification']);
                $up['classification_id_arr'] = implode("\n", $raw['classification']);
                unset($up['classification']);
            }
            if (isset($up['geo_age'])) {
                $up['geo_age_id'] = end($raw['geo_age']);
                $up['geo_age_id_arr'] = implode("\n", $raw['geo_age']);
                unset($up['geo_age']);
            }
            if (isset($up['district'])) {
                $up['district_id'] = end($raw['district']);
                $up['district_id_arr'] = implode("\n", $raw['district']);
                unset($up['district']);
            }
            if (isset($up['get_time'])) {
                $up['get_time'] = strtotime($raw['get_time']);
            }
            $up['photo'] = isset($raw['photo']) ? $this->photoData($raw['photo']) : '';
            $up['restore_photo'] = isset($raw['rphoto']) ? $this->photoData($raw['rphoto']) : '';
            $up['material'] = isset($raw['material']) ? $this->materialData($raw['material']) : '';
            $up['utime'] = time();
            $r = $o->save($up);
            if ($r === false) {
                $this->error();
            }
            return $this->detail($id);
        }
    }

    protected function photoData($data)
    {
        if (!$data) {
            return '';
        }
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $pattern = "/^{$host}/";
        foreach ($data as &$item) {
            $item['url'] = preg_replace($pattern, '', $item['url']);
            unset($item['status']);
        }
        return $data ? json_encode($data) : '';
    }

    protected function dePhotoData($data)
    {
        if (!$data) {
            return [];
        }
        $ret = json_decode($data, true);
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        foreach ($ret as &$item) {
            $item['url'] = $host . $item;
        }
        return $ret;
    }

    protected function materialData($data)
    {
        if (!$data) {
            return '';
        }
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        $pattern = "/^{$host}/";
        foreach ($data as &$item) {
            $item['url'] = preg_replace($pattern, '', $item['url']);
            unset($item['status']);
        }
        return $data ? json_encode($data) : '';
    }

    protected function deMaterialData($data)
    {
        if (!$data) {
            return [];
        }
        $ret = json_decode($data, true);
        $host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
        foreach ($ret as &$item) {
            $item['url'] = $host . $item;
        }
        return $ret;
    }

    public function detail($id = 0)
    {
        $id = I('id/d', $id);
        $o = M()->table('dn_fossil');
        $w = [
            'id' => $id,
        ];
        $o->where($w);
        $ret = $o->find();
        $ret = [
            'default' => [
                'status' => $ret['status'],
                'id' => $ret['id'],
                'is_comment' => $ret['is_comment'],
            ],
            'passport' => [
                'serial_no' => $ret['serial_no'],
                'name_zh' => $ret['name_zh'],
                'name_en' => $ret['name_en'],
                'owner' => $ret['owner'],
                'get_time' => $ret['get_time'],
                'classification' => $ret['classification_id_arr'],
            ],
            'identify' => [
                'district' => $ret['district_id_arr'],
                'address' => $ret['address'],
                'geo_age' => $ret['geo_age_id_arr'],
                'geo_layer' => $ret['geo_layer'],
                'longitude' => $ret['longitude'],
                'latitude' => $ret['latitude'],
                'altitude' => $ret['altitude'],
            ],
            'description' => [
                'abstract' => $ret['abstract'],
                'description' => $ret['description'] ?: '',
            ],
            'photoInfo' => [
                'photo' => $this->dePhotoData($ret['photo']),
                'restore_photo' => $this->dePhotoData($ret['restore_photo']),
            ],
            'storage' => [
                'storage_name' => $ret['storage_name'],
                'storage_no' => $ret['storage_no'],
                'num' => $ret['num'],
            ],
            'attachment' => [
                'material' => $this->deMaterialData($ret['material']),
            ],
        ];
        $this->show($ret);
    }

    public function switcher()
    {
        $raw = $this->getFormParam();
        if (!$raw['id']) {
            $this->error();
        }
        $o = M()->table(AdminTbl::TBL_DN_FOSSIL);
        $w = [
            'id' => $raw['id'],
        ];
        $o->where($w);
        $up = [
            $raw['field'] => $raw['value'],
        ];
        $r = $o->save($up);
        if ($r === false) {
            $this->error();
        }
        $this->show();
    }

    public function delete()
    {
        $raw = $this->getFormParam();
        if (!$raw['id']) {
            $this->error();
        }
        $o = M()->table(AdminTbl::TBL_DN_FOSSIL);
        $w = [
            'id' => $raw['id'],
        ];
        $o->where($w);
        $r = $o->delete();
        if ($r === false) {
            $this->error();
        }
        $this->show();
    }
}