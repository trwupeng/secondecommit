<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/29
 * Time: 14:19
 */

namespace Prj\Consts;

class Discuz
{
    const img_top_left = 1;
    const img_top_right = 4;
    const img_bottom_up = 8;
    const img_bottom_down = 16;
    static $img_types = [
        self::img_top_left=>'Banner',
        self::img_top_right=>'推荐',
        self::img_bottom_up=>'讨论',
        self::img_bottom_down=>'底部',
    ];
}