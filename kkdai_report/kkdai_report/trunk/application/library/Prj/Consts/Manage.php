<?php
namespace Prj\Consts;

/**
 * 流水记录的状态码
 *
 * @author simon.wang
 */
class Manage {
    /**
     *  财务明细
     */
    const item_finance = 1000;
    /**
     *  财务线下
     */
    const item_finance_ground = 2000;
    /**
     * 业务
     */
    const item_bus = 3000;
    /**
     *  业务进展
     */
    const item_bus_progress = 4000;
    /**
     *  参数设置
     */
    const item_config = 5000;
    /**
     *  权限设置
     */
    const item_rights_set = 6000;
    /**
     * 更新
     */
    const update = 100;
    /**
     * 新增
     */
    const insert = 200;
    /**
     * 删除
     */
    const delete = 300;

    static $num = [
        self::item_finance=>'费用明细',
        self::item_finance_ground=>'线下业务收支',
        self::item_bus=>'融资业务周报',
        self::item_bus_progress=>'业务进展情况',
        self::update=>'更新',
        self::insert=>'新增',
    ];

    static $depts = [
        'fin'=>'财务',
        'bus'=>'业务',
		'top'=>'管理',
		'opt'=>'运营'
    ];
}
