<?php
/**
 *
 * 二期需求 还款统计 - 借款人
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/22 0019
 * Time: 上午 9:50
 */

class PaymentofborrowerController extends \Prj\ManagerCtrl {

    protected $db_rpt;
    protected $pageSizeEnum =[50, 100, 200];
    protected $arr_shelf=['ALLSHELFID'=>'所有类型'];

    public function init () {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $shelfIds = $this->db_rpt->getCol(\Rpt\Tbname::tb_products_final, 'distinct(shelfId)',
            ['shelfId!'=>0], 'sort shelfId');
        foreach($shelfIds as $id) {
            $this->arr_shelf[$id] = \Rpt\Funcs::product_type($id);
        }
    }


    public function summaryAction () {
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $shelfIdselected = $this->_request->get('shelfId');
        if(empty($shelfIdselected)&&$shelfIdselected!==0){
            $shelfIdselected='ALLSHELFID';
        }
        $this->_view->assign('shelfIds', $this->arr_shelf);
        $this->_view->assign('shelfIdSelected', $shelfIdselected);

        $realname = trim($this->_request->get('realname'));
        $waresName = trim($this->_request->get('waresName'));
        $_isDownload = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $this->_view->assign('pager', $pager);

        $tmp_where = [];
        if(!empty($ymdFrom)) {
            if(!\Rpt\Funcs::check_date($ymdFrom)){
                return $this->returnError('日期格式错误, 格式如:2016-08-08');
            }
            $tmp_where[] ='ymdShouldPay>='.date('Ymd', strtotime($ymdFrom));
        }

        if(!empty($ymdTo)) {
            if(!\Rpt\Funcs::check_date($ymdTo)){
                return $this->returnError('日期格式错误, 格式如:2016-08-08');
            }
            $tmp_where[] = 'ymdShouldPay<='.date('Ymd', strtotime($ymdTo));
        }

        if(!empty($ymdFrom) && !empty($ymdTo)) {
            if(date('Ymd', strtotime($ymdTo))< date('Ymd', strtotime($ymdFrom))) {
                return $this->returnError('起始日期不应该晚于截止日期');
            }
        }

        empty($realname)?'':($tmp_where[]='tb_user_final.realname like \'%'.$realname.'%\'');
        empty($waresName)?'':($tmp_where[]='tb_products_final.waresName like \'%'.$waresName.'%\'');
        if($shelfIdselected !== 'ALLSHELFID'){
            $tmp_where[] = 'tb_account_bill.shelfId='.$shelfIdselected;
        }
        $where = '';
        if(!empty($tmp_where)){
            $where = ' where '.array_shift($tmp_where)." ";
        }
        foreach($tmp_where as $v) {
            $where .= 'and '.$v.' ';
        }

        $fieldsMap = [
            'nickname'                                          => '借款人昵称',
            'realname'=>'姓名',
            'tb_account_bill.waresId'                           => '标的Id',
            'waresName'                                         => '标的名称',
            'tb_account_bill.shelfId'                           => '标的类型',
            'tb_account_bill.billNum'                                           => '第几期还款',
            'tb_account_bill.ymdShouldPay'=>'合约还款日',
            'round(tb_account_bill.principal/100, 2) as principal'              => '本金(元)',
            'round(tb_account_bill.interest/100, 2) as interest'                => '利息(元)',
            'round(tb_account_bill.serviceCharge/100, 2) as serviceCharge'      => '管理费(元)',
            'round(tb_account_bill.penaltyInteret/100, 2) as penaltyInteret'    => '罚息(元)',
            'round(tb_account_bill.overheadCharges/100, 2) as overheadCharges'  => '逾期管理费(元)',
            'round((tb_account_bill.principal+tb_account_bill.interest+tb_account_bill.serviceCharge+tb_account_bill.penaltyInteret+tb_account_bill.overheadCharges)/100, 2) as sumAmount' =>'合计(元)',
            'round(tb_account_bill.paymentMoney/100, 2) as paymentMoney'        => '实际还款金额(元)',
            'ymdPayment'                                        => '实际还款日期',
            'tb_account_bill.finish'                                            => '是否还清',
        ];


        $fields = implode(',', array_keys($fieldsMap));
        /** 获取数据记录 */

        if ($_isDownload) {
            $where = $this->_request->get('where');
            $where = json_decode(urldecode($where), true);
            $rs = $this->getData($this->db_rpt, $fields, $where, 'order by ymdShouldPay');

        }else {
            $rs = $this->getData($this->db_rpt, 'count(*) as n', $where);
            $recordsCount = $rs[0]['n'];
//var_log($recordsCount, 'recordsCount##########');
            $pager->init($recordsCount, $pageid);
            $rs = $this->getData($this->db_rpt, $fields, $where, 'order by ymdShouldPay desc', 'limit '.$pager->rsFrom().', '.$pagesize);
error_log(\Sooh\DB\Broker::lastCmd());
        }
//var_log($rs, 'rs##########');
        foreach($rs as $key=> $r) {

            $rs[$key]['ymdPayment'] = ($r['ymdPayment'] >0?date('Y-m-d', strtotime($r['ymdPayment'])):'');
            $rs[$key]['shelfId'] = \Rpt\Funcs::product_type($r['shelfId']);
            $rs[$key]['ymdShouldPay'] = date('Y-m-d', strtotime($r['ymdShouldPay']));

            $rs[$key]['principal'] = ( $r['principal'] >0 ? number_format($r['principal'], 2) : '');
            $rs[$key]['interest'] = ( $r['interest'] >0 ? number_format($r['interest'], 2) : '');
            $rs[$key]['serviceCharge'] = ( $r['serviceCharge'] >0 ? number_format($r['serviceCharge'], 2) : '');
            $rs[$key]['principal'] = ( $r['principal'] >0 ? number_format($r['principal'], 2) : '');
            $rs[$key]['interest'] = ( $r['interest'] >0 ? number_format($r['interest'], 2) : '');
            $rs[$key]['penaltyInteret'] = ( $r['penaltyInteret'] >0 ? number_format($r['penaltyInteret'], 2) : '');
            $rs[$key]['overheadCharges'] = ( $r['overheadCharges'] >0 ? number_format($r['overheadCharges'], 2) : '');
            $rs[$key]['paymentMoney'] = ( $r['paymentMoney'] >0 ? number_format($r['paymentMoney'], 2) : '');
            $rs[$key]['paymentMoney'] =  number_format($r['sumAmount'], 2);

            if($r['finish']==0){
                $rs[$key]['finish']='否';
            }else{
                $rs[$key]['finish']='是';
            }
        }
//var_log($rs, 'rs#################');
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
        $this->_view->assign('realname', $realname);

    }

    private function getData ($db, $fields, $where, $order='', $limits='') {

        $sql = 'select '.$fields. ' from tb_account_bill'
            .' left join tb_products_final'
            .' on tb_account_bill.waresId=tb_products_final.waresId'
            .' left join tb_user_final'
            .' on tb_account_bill.userId=tb_user_final.userId';

        if (!empty($where)) {
            $sql.=' '.$where;
        }
        if(!empty($order)) {
            $sql.=' '.$order;
        }
        if(!empty($limits)){
            $sql.=' '.$limits;
        }
//var_log($sql, 'sql#############');
        $rs =  $this->execSql($sql, $db);
        return $rs;
    }

    private function execSql ($sql,$db){
        $result = $db->execCustom(['sql' => $sql]);
        return $db->fetchAssocThenFree($result);
    }

}