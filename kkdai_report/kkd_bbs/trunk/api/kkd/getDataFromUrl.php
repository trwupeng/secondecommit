<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/9/29
 * Time: 13:40
 */
loadcore();
header('Content-type: application/json;charset=utf-8');
$url = $_GET['url'];
$url = urldecode($url);
$data = file_get_contents($url);
var_log('查询直播状态:'.$url.' code:'.substr($data,0,10));
echo $data;
