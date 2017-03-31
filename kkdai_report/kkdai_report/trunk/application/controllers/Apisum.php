<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/18 0018
 * Time: 上午 11:20
 */

class ApisumController extends Yaf_Controller_Abstract {

    public function getdataAction() {
        $arr_sum = [];
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);

        /**
         *
         * 总投资金额
         */

        // 体验标
        $arr_ware_id = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        $sum = $db->getOne(\Rpt\Tbname::tb_orders_final, 'sum(amount)/100', ['waresId!'=>$arr_ware_id]);
        $arr_sum['sumInvest'] = sprintf("%.2f", $sum);

        /**
         *
         * 总收益
         */
        $sum = $db->getOne(\Rpt\Tbname::tb_user_final, 'sum(total_income+bid_income)/100')
            + $db->getOne(\Rpt\Tbname::tb_yuebao_out, 'sum(amount)/100', ['type'=>2]);
        $arr_sum['sumInterest'] = sprintf("%.2f", $sum);

        /**
         *
         * 总注册
         */
        $arr_sum['sumUser'] = $db->getRecordCount(\Rpt\Tbname::tb_user_final, ['flagUser!'=>1]);

        echo json_encode($arr_sum);
    }

}