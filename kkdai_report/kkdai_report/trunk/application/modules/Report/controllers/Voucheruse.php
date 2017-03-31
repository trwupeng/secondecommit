<?php
/**
 *
 * 二期需求 优惠券发放 - 投资人
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/26
 * Time: 上午 9:50
 */

class VoucheruseController extends \Prj\ManagerCtrl {

    protected $db_rpt;
    protected $pageSizeEnum =[50, 100, 200];

    public function init () {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function summaryAction () {
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $_isDownload = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $this->_view->assign('pager', $pager);

        if(empty($ymdFrom) && empty($ymdTo)&&!$_isDownload) {
            $ymdTo = date('Y-m-d', strtotime('-1 day'));
            $ymdFrom = date('Y-m-d', strtotime('-30 day'));
        }
        $where = [
            '(dixian_use_amount+tixian_use_amount+tixian_use_amount+jiaxi_use_num+fanxian_use_amount)>'=>0,
        ];
        if(!empty($ymdFrom)) {
            if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                $this->returnError('日期格式错误');
            }
            if($ymdTo< $ymdFrom) {
                $this->returnError('起始日期不应该晚于截止日期');
            }
            $ymdFromDBFormat = date('Ymd' ,strtotime($ymdFrom));
            $ymdToDBFormat = date('Ymd', strtotime($ymdTo));

            $where['ymd]']=$ymdFromDBFormat;
            $where['ymd[']=$ymdToDBFormat;
        }

        $fieldsMap = [
            'ymd'                                                       => '使用日期',
            'round(dixian_use_amount/100,2) as dixian_use_amount'       => '抵现券使用金额(元)',
            'round(tixian_use_amount/100,2) as tixian_use_amount'       => '提现券使用金额(元)',
            'jiaxi_use_num'                                             => '加息券使用数量',
            'round(fanxian_use_amount/100,2) as fanxian_use_amount'     => '返现券使用金额(元)',
        ];


        /** 获取数据记录 */

        if ($_isDownload) {
            $where = $this->_request->get('where');
            $where = json_decode(urldecode($where), true);
            $rs = $this->db_rpt->getRecords('db_kkrpt.tb_voucher_statistics', array_keys($fieldsMap), $where, 'rsort ymd');

        }else {
            $recordsCount = $this->db_rpt->getRecordCount('db_kkrpt.tb_voucher_statistics', $where);

            $pager->init($recordsCount, $pageid);
            $rs = $this->db_rpt->getRecords('db_kkrpt.tb_voucher_statistics', array_keys($fieldsMap), $where, 'rsort ymd', $pagesize, $pager->rsFrom());
        }
//var_log($rs, 'rs##########');
        $sumDixian =0;
        $sumTixian =0;
        $sumJiaXi =0;
        $sumFanxian =0;
        foreach($rs as $key=> $r) {
            $tmp=$r;
            unset($tmp['ymd']);
            if(array_sum($tmp)==0){
                unset($rs[$key]);
                continue;
            }

            if(!$_isDownload){
                $rs[$key]['__PKEY__'] = $r['ymd'];
                $sumDixian += $r['dixian_use_amount'];
                $sumTixian += $r['tixian_use_amount'];
                $sumJiaXi += $r['jiaxi_use_num'];
                $sumFanxian += $r['fanxian_use_amount'];
            }
            $rs[$key]['ymd']= date('Y-m-d', strtotime($r['ymd']));
            foreach($r as $k => $v){
                if(!($v>0)){
                    $rs[$key][$k]='';
                }
            }
        }
        if($_isDownload) {
            return $this->downExcel($rs, $fieldsMap);
        }

        $this->_view->assign('records', $rs);
        $this->_view->assign('fieldsMap', $fieldsMap);
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
        $this->_view->assign('where', urlencode(json_encode($where)));
        $this->_view->assign("sumDixian", $sumDixian);
        $this->_view->assign("sumTixian", $sumTixian);
        $this->_view->assign("sumJiaXi", $sumJiaXi);
        $this->_view->assign("sumFanxian", $sumFanxian);
    }


    /**
     * 优惠券使用详情
     */
    public function detailAction () {
        $fieldsMap = [
            'userId'=>'用户Id',
            'nickname'=>'用户昵称',
            'realname'=>'真实姓名',
            'phone'=>'手机号',
            'ymdUsed'=>'使用时间',
            'voucherType'=>'券类型',
            'source'=>'奖励来源',
            'amount'=>'券金额(元)/加息(元)',

        ];
        $records = [];
        $fields = 'tb_user_final.userId, tb_user_final.nickname, tb_user_final.realname,
            tb_user_final.phone,tb_vouchers_final.ymdUsed,tb_vouchers_final.voucherType,
            tb_vouchers_final.source,tb_vouchers_final.amount, tb_orders_final.shelfId,
            (tb_orders_final.amount+tb_orders_final.amountExt) as buyAmount, waresId';
        $_isDownload = $this->_request->get('__EXCEL__');
        $arr_wares_info = [];
        if(empty($_isDownload)){
            $ymdUsed = $this->_request->get('ymdUsed');
            if(empty($ymdUsed)){
                $this->returnError('参数错误');
            }
            $pageid = $this->_request->get('pageId', 1) - 0;
            $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $this->_view->assign('pager', $pager);
            $where = 'where ymdUsed='.$ymdUsed.' and status=1';
            $order = 'order by ymdUsed desc';
            $rs = $this->getDetailData($this->db_rpt, 'count(*) as n', $where);
            $recordsCount = $rs[0]['n'];
            $pager->init($recordsCount, $pageid);
            $limits = 'limit '.$pager->rsFrom().', '.$pagesize;
            $result = $this->getDetailData($this->db_rpt, 'orderId', $where, $limits);
            foreach ($result as $v) {
                $arr_orderId[] = $v['orderId'];
            }
            if (!empty($arr_orderId)) {
//                $arr_order_info = $this->db_rpt->getAssoc('db_kkrpt.tb_orders_final', 'ordersId', '(amount+amountExt)as amount, shelfId,waresId', ['ordersId'=>$arr_orderId]);
                $arr_waresId = $this->db_rpt->getCol('db_kkrpt.tb_orders_final', 'distinct(waresId)', ['ordersId'=>$arr_orderId]);
                if (!empty($arr_waresId)) {
                    $arr_wares_info = $this->db_rpt->getPair('db_kkrpt.tb_products_final', 'waresId', 'deadLine', ['waresId'=>$arr_waresId]);
                }
            }
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order, $limits);
        }else {
            $where = urldecode($this->_request->get('where'));
            $order = 'order by  ymdUsed desc';
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order);

        }
//var_log($rs, 'rs########');
        foreach($rs as $key=> $v){
            $tmp = array_fill_keys(array_keys($fieldsMap), '');
            $tmp['userId'] = $v['userId'];
            $tmp['nickname'] = $v['nickname'];
            $tmp['realname'] = $v['realname'];
            $tmp['phone'] = $v['phone'];
            $tmp['ymdUsed']= date('Y-m-d', strtotime($v['ymdUsed']));
            $tmp['voucherType'] = \Rpt\Funcs::voucherName($v['voucherType']);
            $tmp['amount'] = number_format($v['amount']*0.01, 2);
            $tmp['source'] = $v['source'];
            if($v['voucherType']==2){
                if($v['shelfId'] == 1 && $arr_wares_info[$v['waresId']]['deadLine']) {
                    $tmp['amount'] = round($v['amount']*0.01*$v['buyAmount']*0.01/12*$arr_wares_info[$v['waresId']]['deadLine'], 2);
                }elseif($v['shelfId'] == 2 && $arr_wares_info[$v['waresId']]['deadLine']) {
                    $tmp['amount']= round($v['amount']*0.01*$v['buyAmount']*0.01/372*$arr_wares_info[$v['waresId']]['deadLine'], 2);
                }else {
                    $tmp['amount']='<span style="color:red">'.$v['amount']*0.01.'%</span>';
                }
            }else {
                $tmp['amount'] = number_format($v['amount']*0.01, 2);
            }
            $records[] = $tmp;
        }

        if($_isDownload){
            return $this->downExcel($records, $fieldsMap);
        }
        $this->_view->assign('records', $records);
        $this->_view->assign('fieldsMap', $fieldsMap);
        $this->_view->assign('where', urlencode($where));
        $this->_view->assign('ymdUsed', $ymdUsed);
    }

    private function getDetailData ($db, $fields, $where, $order='', $limits='') {

        $sql = 'select '.$fields.' from tb_vouchers_final'
            .' left join tb_user_final using(userId)'
            .' left join tb_orders_final on tb_orders_final.ordersId = tb_vouchers_final.orderId';
        if (!empty($where)) {
            $sql.=' '.$where;
        }
        if(!empty($order)) {
            $sql.=' '.$order;
        }
        if(!empty($limits)){
            $sql.=' '.$limits;
        }
        $rs =  $this->execSql($sql, $db);
        return $rs;
    }

    private function execSql ($sql,$db){
        $result = $db->execCustom(['sql' => $sql]);
        return $db->fetchAssocThenFree($result);
    }

}