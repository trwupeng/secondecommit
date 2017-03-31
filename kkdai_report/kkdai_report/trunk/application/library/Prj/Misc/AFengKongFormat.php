<?php

namespace Prj\Misc;

use \Sooh\DB\Base\KVObj as KVObj;

/**
 * 风控-M中格式化字段的相关操作
 * Class AFengKongFormat
 * @package Prj\Misc
 * @author  lingtm <lingtima@gmail.com>
 */
abstract class AFengKongFormat extends KVObj
{
    /**
     * 百分比类型的字段
     * @var array
     */
    public static $formatPercentageType = [];

    /**
     * decimal类型的字段
     * @var array
     */
    public static $formatDecimalType = [];

    /**
     * 金钱类型的字段，以元为单位
     * @var array
     */
    public static $formatMoneyType = [];

    /**
     * 整型的字段，例如int、tinyint
     * @var array
     */
    public static $formatIntType = [];

    /**
     * 浮点数, 默认处理两位
     * @var array
     */
    public static $formatDoubleType = [];

    /**
     * 时间类型的数据
     * @example
     *      'startTime' => 'Y-m-d',//显示到表单的格式-默认使用<br>
     *      'endTime' => [<br>
     *              'YMD',//存入DB中时的格式<br>
     *              'Y-m-d'//显示到表单的格式<br>
     *          ],<br>
     * @var array
     */
    public static $formatDateType = [];

    /**
     * 显示在表单上需要其他参数的字段
     * @var array
     * @example
     * array {
     *  'field1' => [
     *       1 => ['style' => 'color:red',],
     *       2 => ['style' => 'color:blue',],
     * }
     */
    public static $formatEnumAttr = [];

    /**
     * 多选下拉
     * @var array
     */
    public static $formatSelectsAttr = [];

    /**
     * 将枚举型转化为字符串，用于显示在页面上
     * @param string  $type  字段名
     * @param integer $value 字段值
     * @return string
     */
    public static function parseEnumToString($type, $value)
    {
        //枚举型的默认值为【无】
        if ($value == 0) {
            return '无';
        }

        $enum = FengKongEnum::getInstance()->get($type);
        if ($enum === false) {
            return $value;
        } else {
            return $enum[$value];
        }
    }

    /**
     * 将字段转化为易读的字符串，用于显示在页面上
     * @param string $type  字段名
     * @param mixed  $value 字段值
     * @return string
     */
    public static function parseFieldToString($type, $value)
    {
        if (empty($value)) {
            return $value;
        }

        if (in_array($type, static::$formatPercentageType)) {
            return sprintf('%.2f', $value / 100);
        } else if (array_key_exists($type, static::$formatMoneyType)) {
            return round($value / (static::$formatMoneyType[$type] * 100), 6);
        } else if (array_key_exists($type, static::$formatDateType)) {
            if (is_string(static::$formatDateType[$type])) {
                return date(static::$formatDateType[$type] ? : 'Y-m-d', $value);
            } else if (is_array(static::$formatDateType[$type])) {
                return date(static::$formatDateType[$type][1] ? : 'Y-m-d', strtotime($value));
            } else {
                return $value;
            }
        } else if (in_array($type, static::$formatSelectsAttr)) {
            return json_decode($value, true);
        } else if (array_key_exists($type, static::$formatEnumAttr)) {
            if (isset(static::$formatEnumAttr[$type][$value])) {
                //解析-显示在表单上需要其他参数的字段
                $tmpArr = static::$formatEnumAttr[$type][$value];
                $tmpArr['value'] = $value;
                return $tmpArr;
            }
        } else if (array_key_exists($type, static::$formatDoubleType)) {
            return round($value / pow(10, static::$formatDoubleType[$type] ? : 2), static::$formatDoubleType[$type]);
        } else if (array_key_exists($type, static::$formatDecimalType)) {
            return $value * 1;
        } else {
            return $value;
        }

        return $value;
    }

    /**
     * 将表单中提交的数据转化为数据库可用的字段
     * @param string $type  字段名
     * @param mixed  $value 字段值
     * @return bool|int|string
     */
    public static function parseStringToField($type, $value)
    {
        if ($value === '0' || $value === 0) {
            return 0;
        }

        if (in_array($type, static::$formatPercentageType)) {
            $tmpRet = ($value + 0) * 100;
        } else if (array_key_exists($type, static::$formatMoneyType)) {
            $tmpRet = ($value + 0) * (static::$formatMoneyType[$type] * 100);
        } else if (array_key_exists($type, static::$formatDateType)) {
            if (is_string(static::$formatDateType[$type])) {
                $tmpRet = $value ? strtotime($value) : 0;
            } else if (is_array(static::$formatDateType[$type])) {
                $tmpRet = $value ? date(static::$formatDateType[$type][0], strtotime($value)) : 0;
            } else {
                $tmpRet = 0;
            }
        } else {
            //检测枚举值是否合法
            $enum = FengKongEnum::getInstance()->get($type);
            if ($enum === false) {
                //不是枚举类型
                //字符串转整形
                if (in_array($type, static::$formatIntType)) {
                    $tmpRet = $value ? : 0;
                } else if (array_key_exists($type, static::$formatDoubleType)) {
                    $tmpRet = $value ? ($value * (pow(10, static::$formatDoubleType[$type] ? : 2))) : 0;
                } else {
                    $tmpRet = $value ? : '';
                }
            } else {
                if (!array_key_exists($value, $enum)) {
                    error_log("[check enum field for update error] $type:$value");
                    $tmpRet = false;
                } else {
                    $tmpRet = $value;
                }
            }
        }

        return $tmpRet;
    }

    /**
     * 格式化增加日期
     * @param     int   $time
     * @param string $m +2
     * @param string $d +5
     * @return int
     */
    public static function formatTimeAdd($time, $m = '', $d = '')
    {
        if (empty($m)) {
            $tmp = $time;
        } else {
            $tmp = strtotime("$m month", $time);
            $m1 = date('m', $time);
            $m2 = date('m', $tmp);
            if ($m2 < $m1) {
                $m2 += 12;
            }

            if ($m2 - $m1 != $m) {
                $tmp = strtotime("last day of $m month", $time);
            }
        }

//        var_dump(date('Y-m-d', $tmp));

        if (empty($d)) {
            $ret = $tmp;
        } else {
            $ret = strtotime("$d day", $tmp);
        }

        return $ret;
    }
}
