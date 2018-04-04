<?php

namespace Admin\Controller;

use Common\Common\Util;

class FossilController extends \Admin\Common\AdminController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function lists()
    {
        $page = I('page');
        $limit = I('limit', 20);
        $o = M()->table('dn_fossil');
        $o = $o->page($page);
        $o = $o->limit($limit);
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
        $o = $o->where($w);
        $ret = $o->select();
        $o = M()->table('dn_fossil');
        $total = $o->count();
        $ret = [
            'lists' => $ret,
            'total' => $total,
        ];
        $this->show($ret);
    }

    function operate()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $final = $GLOBALS['HTTP_RAW_POST_DATA'];
        } else {
            $final = file_get_contents('php://input');
        }
        $raw = json_decode($final, true);
        $o = M()->table('dn_fossil');
        if (isset($raw['classification']) && $raw['classification']) {
            $raw['classification'] = explode("\n", $raw['classification']);
        }
        if (isset($raw['district']) && $raw['district']) {
            $raw['district'] = explode("\n", $raw['district']);
        }
        if (isset($raw['geo_age']) && $raw['geo_age']) {
            $raw['geo_age'] = explode("\n", $raw['geo_age']);
        }
        $add = [
            'classification_id' => isset($raw['classification']) ? end($raw['classification']) : 0,
            'classification_id_arr' => isset($raw['classification']) ? implode("\n", $raw['classification']) : 0,
            'district_id' => isset($raw['district']) ? end($raw['district']) : 0,
            'district_id_arr' => isset($raw['district']) ? implode("\n", $raw['district']) : 0,
            'geo_age_id' => isset($raw['geo_age']) ? end($raw['geo_age']) : 0,
            'geo_age_id_arr' => isset($raw['geo_age']) ? implode("\n", $raw['geo_age']) : 0,
            'serial_no' => uniqid(),
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
            'photo' => isset($raw['photo']) ? $raw['photo'] : '',
            'storage_name' => isset($raw['storage_name']) ? $raw['storage_name'] : '',
            'storage_no' => isset($raw['storage_no']) ? $raw['storage_no'] : '',
            'num' => isset($raw['num']) ? $raw['num'] : 0,
            'material' => isset($raw['material']) ? $raw['material'] : '',
            'status' => 1,
            'get_time' => isset($raw['get_time']) ? strtotime($raw['get_time']) : 0,
            'ctime' => time(),
        ];
        $id = $raw['id'];
        if (!$id) {
            $ret = $o->add($add);
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
            $r = $o->save($up);
            return $this->detail($id);
        }
        $this->show($ret);
    }

    public function detail($id = 0)
    {
        $id = I('id/d', $id);
        $o = M()->table('dn_fossil');
        $w = [
            'id' => $id,
        ];
        $o = $o->where($w);
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
                'photoInfo' => $ret['photoInfo'] ?: '',
                'restore_photo' => $ret['restore_photo'] ?: '',
            ],
            'storage' => [
                'storage_name' => $ret['storage_name'],
                'storage_no' => $ret['storage_no'],
                'num' => $ret['num'],
            ],
            'attachment' => [
                'material' => $ret['material'],
            ],
        ];
        $this->show($ret);
    }

    public function upload()
    {
        $res = Util::upload(['savePath' => 'my/fossil/']);
        if (!$res[_c]) {
            echo json_encode($res['_m']);
            die;
        }
        echo json_encode($res);
        die;
    }
}