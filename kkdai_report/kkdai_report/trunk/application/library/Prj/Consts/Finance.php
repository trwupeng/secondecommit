<?php
namespace Prj\Consts;
/**
 *
 */
class Finance {
	/**
     * 快快金融
     */
    const type_kkd = 1000;
    /**
     * 美豫
     */
    const type_my = 2000;
    /**
     * 线上投标
     */
    const type_xstb = 3000;
    /**
     * 线下投标
     */
    const type_xxtb = 4000;

    static $type_enum = [
        self::type_kkd=>'快快金融',
        self::type_my=>'美豫',
        self::type_xstb=>'线上投标',
        self::type_xxtb=>'线下投标',
    ];
}
