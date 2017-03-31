<?php
namespace Rpt;
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/3/25 0025
 * Time: 下午 8:52
 */

class LimitRules
{
    /**
     * 无规则限制
     */
    const unlimitrule = 0;
    /**
     * 注册规则限制
     */
    const regrule = 1;
    /**
     * 认证规则限制
     */
    const bindrule = 2;
    /**
     * 购买规则限制
     */
    const buyrule = 3;
    public static $displayRule = [
        self::unlimitrule => '无',
        self::regrule => '按照注册',
        self::bindrule => '按照绑卡',
        self::buyrule => '按照购买',
    ];
}