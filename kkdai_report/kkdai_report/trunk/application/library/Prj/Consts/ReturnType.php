<?php
namespace Prj\Consts;
/**
 * 操作返回的code的值
 *
 * @author simon.wang
 */
class ReturnType {
	const unknow = 0; //未定
    const single = 1; //一次付息+本金
    const byMonth = 2; //按月付息到期还本
    public static $enum = array(
        self::unknow=>'未定',
        self::single=>'到期还本付息',
        self::byMonth=>'按月付息到期还本',
    );
}
