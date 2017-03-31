<?php
/**
 *
 * 二期需求 还款统计 - 投资人
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/22 0019
 * Time: 上午 9:50
 */

class PaymentofinvestorController extends \Prj\ManagerCtrl {

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
        $waresId = $this->_request->get('waresId');
        $waresName = trim($this->_request->get('waresName'));
        $_isDownload = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $this->_view->assign('pager', $pager);


//        if(empty($ymdFrom) && empty($ymdTo)) {
//            $ymdTo = date('Y-m-d', strtotime('-1 day'));
//            $ymdFrom = date('Y-m-d', strtotime('-30 day'));
//        }
        $tmp_where = [];
        if(!empty($ymdFrom)) {
            if(!\Rpt\Funcs::check_date($ymdFrom)) {
                return $this->returnError('日期格式错误, 格式如:2016-08-08');
            }
            $tmp_where[]='ymdPayment>='.date('Ymd' ,strtotime($ymdFrom));
        }
        if(!empty($ymdTo)) {
            if (!\Rpt\Funcs::check_date($ymdTo)) {
                return $this->returnError('日期格式错误, 格式如:2016-08-08');
            }
            $tmp_where[]='ymdPayment<'.date('Ymd', strtotime($ymdTo));
        }

        if(!empty($ymdFrom) && !empty($ymdTo)) {
            if (date('Ymd', $ymdFrom) > date('Ymd',$ymdTo)) {
                return $this->returnError('起始日期不应该晚于截止日期');
            }
        }

        empty($waresId)?'':($tmp_where[]='tb_account_bill.waresId=\''.$waresId.'\'');
        empty($waresName)?'':($tmp_where[]='tb_products_final.waresName like \'%'.$waresName.'%\'');
        $tmp_where[] = 'tb_bill_repay_history.status=5';
        $where = '';
        if(!empty($tmp_where)){
            $where = ' where '.array_shift($tmp_where)." ";
        }
        foreach($tmp_where as $v) {
            $where .= 'and '.$v.' ';
        }

        $fieldsMap = [
            'billId as PKEY__'                                => '账单ID',
            'ymdPayment'                                        => '实际还款日期',
            'tb_bill_repay_history.waresId'                           => '标的Id',
            'waresName'                                         => '标的名称',
            'tb_products_final.shelfId'                           => '标的类型',
            'billNum'                                           => '第几期还款',
            'sum(principal) as principal'              => '本金(元)',
            'sum(tb_bill_repay_history.interest) as interest'                => '利息(元)',
            'sum(tb_bill_repay_history.amount) as paymentMoney'        => '还款总金额(元)',
        ];


        $fields = implode(',', array_keys($fieldsMap));
        /** 获取数据记录 */

        if ($_isDownload) {
            $where = $this->_request->get('where');
            $where = json_decode(urldecode($where), true);
            $rs = $this->getData($this->db_rpt, $fields, $where, 'group by ymdPayment, waresId, billNum order by  ymdPayment desc');

        }else {
            $rs = $this->getData($this->db_rpt, 'count(*) as n', $where);
            $recordsCount = $rs[0]['n'];
            $pager->init($recordsCount, $pageid);
            $rs = $this->getData($this->db_rpt, $fields, $where, 'group by ymdPayment, waresId, billNum order by  ymdPayment desc', 'limit '.$pager->rsFrom().', '.$pagesize);
        }

        /** 前端页面不展示账单Id billId */
        unset($fieldsMap['billId as PKEY__']);
        foreach($rs as $key=> $r) {
            if($_isDownload) {
                unset($rs[$key]['PKEY__']);
            }
            $rs[$key]['ymdPayment'] = ($r['ymdPayment'] ? date('Y-m-d', strtotime($r['ymdPayment'])): '');
            !empty($rs[$key]['shelfId']) && $rs[$key]['shelfId'] = \Rpt\Funcs::product_type($r['shelfId']);
            $rs[$key]['principal'] = (!empty($r['principal']) ? number_format($r['principal']*0.01, 2) : '');
            $rs[$key]['interest'] = ($r['interest'] >0 ? number_format($r['interest']*0.01, 2) : '');
            $rs[$key]['paymentMoney'] = ($r['paymentMoney'] >0 ? number_format($r['paymentMoney']*0.01, 2) : '');

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
        $this->_view->assign('waresName', $waresName);
        $this->_view->assign('waresId', $waresId);
    }

    private function getData ($db, $fields, $where, $order='', $limits='') {

        $sql = 'select '.$fields
            .' from tb_bill_repay_history'
            .' left join tb_products_final'
            .' on tb_bill_repay_history.waresId = tb_products_final.waresId';
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


    /**
     * 还款详情
     */
    public function detailAction () {
//        $ymdFrom = $this->_request->get('ymdFrom');
//        $ymdTo = $this->_request->get('ymdTo');
//        $userId = $this->_request->get('userId');
//        $phone = $this->_request->get('phone');
//        $realname = $this->_request->get('realname');
//        $waresId = $this->_request->get('waresId');
        $fieldsMap = [
            'tb_bill_repay_history.ordersId'=>'订单Id',
            'tb_bill_repay_history.userId'=>'用户ID',
            'tb_bill_repay_history.ordersId'=>'订单号',
            'realname'=>'姓名',
            'phone'=>'手机号',
            'tb_products_final.waresName' => '标的名称',
            'concat(tb_products_final.deadLine, tb_products_final.dlUnit) as deadLine' => '标的期限',
            'tb_bill_repay_history.billNum' => '第几期还款',
            'ymdShouldPay' => '合约还款日',
            'ymdPayment' => '实际还款日',
            'round(orderAmount/100,2) as orderAmount'=>'实际投标金额',
            'round(orderAmountExt/100,2) as orderAmountExt'=>'使用红包',
            'round(orderAmountSum/100,2) as orderAmountSum' => '总投标金额',
            'round(tb_bill_repay_history.amount/100,2)as amount'=>'还款金额',
            'round(tb_bill_repay_history.interest/100,2) as interest'=>'利息',
            'round(tb_bill_repay_history.addInterest/100,2) as addInterest'=>'加息',
            'round(tb_bill_repay_history.penaltyInteret/100,2) as penaltyInteret'=>'罚息',

        ];

        $fields = implode(',', array_keys($fieldsMap));
        $_isDownload = $this->_request->get('__EXCEL__');
        if(empty($_isDownload)){
            $billId = $this->_request->get('billId');
            if(empty($billId)){
                $this->returnError('参数错误');
            }
            $pageid = $this->_request->get('pageId', 1) - 0;
            $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $this->_view->assign('pager', $pager);
            $where = 'where billId=\''.$billId.'\' and tb_bill_repay_history.status=5';
            $order = 'order by ymdPayment desc';
            $rs = $this->getDetailData($this->db_rpt, 'count(*) as n', $where);
            $recordsCount = $rs[0]['n'];
            $pager->init($recordsCount, $pageid);
            $limits = 'limit '.$pager->rsFrom().', '.$pagesize;
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order, $limits);
        }else {
            $where = urldecode($this->_request->get('where'));
            $order = 'order by ymdPayment desc';
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order);

        }
//var_log($rs, 'rs########');
        foreach($rs as $key=> $v){
            if(!empty($v['ymdShouldPay'])){
                $rs[$key]['ymdShouldPay'] = date('Y-m-d', strtotime($v['ymdShouldPay']));
            }
            if(!empty($v['ymdPayment'])){
                $rs[$key]['ymdPayment'] = date('Y-m-d', strtotime($v['ymdPayment']));
            }
            if(!($v['orderAmount']>0)){
                $rs[$key]['orderAmount']='';
            }
            if(!($v['orderAmountExt']>0)){
                $rs[$key]['orderAmountExt']='';
            }
            if(!($v['orderAmountSum']>0)){
                $rs[$key]['orderAmountSum']='';
            }
            if(!($v['amount']>0)){
                $rs[$key]['amount']='';
            }
            if(!($v['interest']>0)){
                $rs[$key]['interest']='';
            }
            if(!($v['addInterest']>0)){
                $rs[$key]['addInterest']='';
            }
            if(!($v['penaltyInteret']>0)){
                $rs[$key]['penaltyInteret']='';
            }
        }

        if($_isDownload){
            return $this->downExcel($rs, $fieldsMap);
        }
        $this->_view->assign('records', $rs);
        $this->_view->assign('fieldsMap', $fieldsMap);
        $this->_view->assign('billId', $billId);
        $this->_view->assign('where', urlencode($where));
    }

    private function getDetailData ($db, $fields, $where, $order='', $limits='') {

        $sql = 'select '.$fields.' from tb_bill_repay_history'
            .' left join tb_user_final'
            .' on tb_bill_repay_history.userId = tb_user_final.userId'
            .' left join tb_products_final'
            .' on tb_bill_repay_history.waresId = tb_products_final.waresId';
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