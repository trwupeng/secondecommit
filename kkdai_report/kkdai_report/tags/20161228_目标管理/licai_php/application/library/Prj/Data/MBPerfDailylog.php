<?php

namespace Prj\Data;

use Sooh\Base\Time;
use Sooh\DB\Base\KVObj;
use Sooh\DB\Pager;

/**
 * 工作日志
 * Class MBPerfDailylog
 * @package Prj\Data
 * @author lingtm <lingtima@gmail.com>
 */
class MBPerfDailylog extends KVObj
{
    /**
     * 添加数据
     * @param string  $name         项目名称
     * @param string  $content      工作内容
     * @param integer $level        优先级
     * @param integer $type         工作类型：1原始计划；2临时任务
     * @param integer $userid       日志所属的用户ID
     * @param string  $logDate      日志所属的日期
     * @param int     $planCost     计划用时（小时）
     * @param int     $realCost     实际用时（小时）
     * @param int     $finish       完成情况（0未完成；1完成；2其他）
     * @param string  $finishReason 完成情况的说明
     * @return \Sooh\DB\Interfaces\autoid 主键
     */
    public static function addData($name, $content, $level, $type, $userid, $logDate, $planCost = 0, $realCost = 0, $finish = 0, $finishReason = '')
    {
        $data = [
            'name'          => $name,
            'content'       => $content,
            'level'         => $level,
            'type'          => $type,
            'userid'        => $userid,
            'log_date'      => $logDate,
            'plan_cost'     => $planCost,
            'real_cost'     => $realCost,
            'finish'        => $finish,
            'finish_reason' => $finishReason,
            'create_time'   => Time::getInstance()->ymdhis(),
            'update_time'   => Time::getInstance()->ymdhis(),
            'del_time'      => Time::getInstance()->ymdhis(),
            'del'           => 0,
        ];

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

    public static function getCopy($k)
    {
        return parent::getCopy(['id' => $k]);
    }

    protected static function splitedTbName($n, $isCache)
    {
        return 'mb_perf_dailylog';
    }
}