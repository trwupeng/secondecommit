<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/2/26 0026

 */

class ApicopartnerinquiryController extends \Yaf_Controller_Abstract {

    // 新浪微支付爱加密注册用户查询
    public function ajmusersAction () {
        $o = new \Api\Copartners\Xinlangweicaifu();
        echo json_encode( $o->inquiryUsers($_GET));
    }

    // 新浪微支付爱加密用户订单查询
    public function ajmordersAction () {
        $o = new \Api\Copartners\Xinlangweicaifu();
        echo json_encode($o->inquriyOrders($_GET));
    }
}