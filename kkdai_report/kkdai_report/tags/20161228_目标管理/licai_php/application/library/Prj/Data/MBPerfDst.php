<?php

namespace Prj\Data;

use Sooh\Base\Time;
use Sooh\DB\Base\KVObj;
use Sooh\DB\Broker;
use Sooh\DB\Pager;

/**
 * 工作目标表
 * Class MBPerfDst
 * @package Prj\Data
 * @author  lingtm <lingtima@gmail.com>
 */
class MBPerfDst extends KVObj
{
    public static $typeEnum = [
        1 => '日目标',
        2 => '周目标',
        3 => '月目标',
        4 => '季度目标',
    ];

    /**
     * 添加数据
     * @param string  $name         项目名字
     * @param string  $content      工作内容
     * @param integer $level        优先级
     * @param integer $type         目标类型
     * @param string  $date         目标所属日期 Ymd H:i:s
     * @param integer $createUserId 创建者的用户ID
     * @param integer $userId       目标所属用户ID
     * @return \Sooh\DB\Interfaces\autoid 主键
     */
    public static function addData($name, $content, $level, $type, $date, $createUserId, $userId)
    {
        $typeNum = self::buildTypeNum($type, strtotime($date));
        $typeId = self::buildTypeId($type, $date, $typeNum);
        $data = [
            'name'          => $name,
            'content'       => $content,
            'level'         => $level,
            'type'          => $type,
            'type_id'       => $typeId,
            'type_num'      => $typeNum,
            'dst_date'      => $date,
            'create_time'   => Time::getInstance()->ymdhis(),
            'update_time'   => Time::getInstance()->ymdhis(),
            'del_time'      => '0000-00-00 00:00:00',
            'create_userid' => $createUserId,
            'userid'        => $userId,
            'del'           => 0,
        ];
        var_log($data);

        if ($type == 4 || $type == 3 || $type == 2) {
            $data['dst_date'] = date('Y', strtotime($date)) . '-01-01';
        }

        $model = self::getCopy('');
        $ret = $model->db()->addRecord($model->tbname(), $data);

        return $ret;
    }

    /**
     * 获取列表
     * @param Pager  $pager 分页类
     * @param array  $where 查询条件
     * @param null   $order 排序条件
     * @param string $fields 要查询的字段
     * @return mixed
     */
    public static function paged($pager, $where = [], $order = null, $fields = '*')
    {
        $model = self::getCopy('');
        $maps = ['del' => 0];
        $maps = array_merge($maps, $where);
        $pager->init($model->db()->getRecordCount($model->tbname(), $maps), -1);

        if (empty($order)) {
            $order = 'sort id';
        } else {
            $order = str_replace('_', '', $order);
        }

        $data = $model->db()->getRecords($model->tbname(), $fields, $maps, $order, $pager->page_size, $pager->rsFrom());
        return $data;
    }

    /**
     * 更新数据
     * @param integer $id
     * @param array $data
     * @return bool|int
     */
    public static function updData($id, $data = [])
    {
        if (empty($data)) {
            return 0;
        }

        $model = self::getCopy($id);
        $model->load();
        if ($model->exists()) {
            foreach ($data as $k => $v) {
                $model->setField($k, $v);
            }
            $model->setField('update_time', Time::getInstance()->ymdhis());
            $model->update();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 软删除数据
     * @param integer $id id
     * @return bool
     */
    public static function delData($id)
    {
        $model = self::getCopy($id);
        $model->load();
        if ($model->exists()) {
            $model->setField('del', 1);
            $model->setField('update_time', Time::getInstance()->ymdhis());
            $model->update();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 根据批次ID获取批次完整名字
     * @param string $typeId 批次ID
     * @return string
     */
    public static function getTypeName($typeId)
    {
        $type = (int)substr($typeId, -2);
        $num = (int)substr($typeId, -5, 3);
        $typeName = '';
        switch ($type) {
            case 1:
                $typeName = '第' . $num . '日';
                break;
            case 2:
                $typeName = '第' . $num . '周';
                break;
            case 3:
                $typeName = '第' . $num . '月';
                break;
            case 4:
                $typeName = '第' . $num . '季度';
                break;
        }

        return $typeName . '目标';
    }

    /**
     * 编造目标批次ID
     * @param integer $type type
     * @param string  $date date
     * @param integer $num  num
     * @return string
     */
    public static function buildTypeId($type, $date, $num = 1)
    {
        switch ($type) {
            case 1:
                $id = date('Ymd', strtotime($date)) . sprintf('%03d', $num) . '01';
                break;
            case 2:
                $id = date('Y', strtotime($date)) . '0101' . sprintf('%03d', $num) . '02';
                break;
            case 3:
                $id = date('Y', strtotime($date)) . '0101' . sprintf('%03d', $num) . '03';
                break;
            case 4:
                $id = date('Y', strtotime($date)) . '0101' . sprintf('%03d', $num) . '04';
                break;
        }
        return $id;
    }

    /**
     * 解析批次ID
     * @param string $typeId 批次ID
     * @return array [日期， 数目， 类型]
     */
    public static function parseTypeId($typeId) {
        $date = substr($typeId, 0, 8);
        $typeNum = (int)substr($typeId, 8, 3);
        $type = (int)substr($typeId, -2);

        return ['date' => $date, 'typeNum' => $typeNum, 'type' => $type];
    }

    /**
     * 获取批次数
     * @param integer $type type
     * @param string $timestamp timestamp
     * @return false|float|string
     */
    public static function buildTypeNum($type, $timestamp)
    {
        $num = 1;
        switch($type) {
            case 1:
                $num = date('z', $timestamp);
                break;
            case 2:
                $num = date('W', $timestamp);
                break;
            case 3:
                $num = date('n', $timestamp);
                break;
            case 4:
                $num = ceil(date('n', $timestamp) / 3);
                break;
        }

        return $num;
    }

    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'mb_perf_dst';
    }
}
