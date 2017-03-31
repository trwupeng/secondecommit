<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=RptDaily.EDVoucher&ymdh=20160126"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/5 0005
 * Time: 下午 2:36
 */

class EDVoucher extends \Sooh\Base\Crond\Task
{
    public function init() {
        parent::init();
        $this->toBeContinue = true;
        $this->_secondsRunAgain = 700;
        $this->_iissStartAfter = 55;

        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->db = \Sooh\DB\Broker::getInstance();
    }

    public function free() {
        $this->db = null;
        parent::free();
    }

    protected function onRun($dt) {
//        if ($this->_isManual)
        $this->oneday($dt->YmdFull);
//        if (!$this->_isManual && $dt->hour <= 6) {
//            $dt0 = strtotime($dt->YmdFull);
//            switch($dt->hour) {
//                case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
//                case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
//                case 3: $this->oneday(date('Ymd',$dt0-86400*4));break;
//                case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
//                case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
//                case 6: $this->oneday(date('Ymd',$dt0-86400*1));break;
//            }
//        }
        return true;
    }


    protected function oneday($ymd) {
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        // 券的发放
        $voucherGrant = \Rpt\EvtDaily\ChargeAmount::getCopy('VoucherGrant');
        $sql = 'select sum(amount)/100 as sum, voucherType, amount, tb_user_final.copartnerId, count(*) as count from tb_vouchers_final '
              .'left join tb_user_final on tb_vouchers_final.userId = tb_user_final.userId '
              .'where ymdCreate='.$ymd.' '
              .'and tb_vouchers_final.userId not in (select userId from tb_user_final where flagUser = 1) '
              .'group by copartnerId, voucherType, amount';

        $records = $this->getResult($sql);
        if (!empty($records)) {
            foreach($records as $k => $r) {
                // 金额之和， 渠道，类型，面值

                // 加息券 的话，n字段用加息券的个数填充
                if ($r['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $r['sum'] = $r['count'];
                }
                $voucherGrant->add($r['sum'], 0, $r['copartnerId'], $r['voucherType'], $r['amount']);
            }
            $voucherGrant->save($db,  $ymd);
        }

        // 当日发放的券使用统计
        $voucherNUsed = \Rpt\EvtDaily\ChargeAmount::getCopy('VoucherNUsed');
        $sql = 'select sum(tb_vouchers_final.amount) as sum, voucherType, tb_vouchers_final.amount, tb_user_final.copartnerId, shelfId, tb_orders_final.clientType  from tb_vouchers_final '
              .'left join tb_user_final on tb_vouchers_final.userId = tb_user_final.userId '
              .'left join tb_orders_final on tb_vouchers_final.orderId = tb_orders_final.ordersId '
              .'where ymdCreate='.$ymd.' and ymdUsed='.$ymd.' '
              .'and tb_vouchers_final.userId not in (select userId from tb_user_final where flagUser = 1) '
              .'group by copartnerId, voucherType,tb_vouchers_final.amount';
        $records = $this->getResult($sql);
        if (!empty($records)) {
            foreach($records as $k => $r) {
                // 金额之和， 渠道，类型，面值

                // 加息券 的话，n字段用加息券的个数填充
                if ($r['voucherType'] == \Prj\Consts\Voucher::type_yield) {
                    $r['sum'] = $r['count'];
                }
                $voucherNUsed->add($r['sum'], $r['clientType'], $r['copartnerId'], $r['voucherType'], $r['shelfId']);
            }
            $voucherNUsed->save($db,  $ymd);
        }
    }


    protected function getResult($sql){
        $result = $this->db->execCustom(['sql'=>$sql]);
        $rs = $this->db->fetchAssocThenFree($result);
        return $rs;
    }

}
