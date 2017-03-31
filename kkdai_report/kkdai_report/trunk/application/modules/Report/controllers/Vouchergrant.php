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

class VouchergrantController extends \Prj\ManagerCtrl {

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

        if(empty($ymdFrom) && empty($ymdTo)&&!$_isDownload){
            $ymdTo = date('Y-m-d', strtotime('-1 day'));
            $ymdFrom = date('Y-m-d', strtotime('-30 day'));
        }
        $where = [
            '(dixian_grant_amount+tixian_grant_amount+jiaxi_grant_num+fanxian_grant_amount)>'=>0,
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
            'ymd'                                                       => '发放日期',
            'round(dixian_grant_amount/100,2) as dixian_grant_amount'   => '抵现券发放金额(元)',
            'round(tixian_grant_amount/100,2) as tixian_grant_amount'   => '提现券发放金额(元)',
            'jiaxi_grant_num'                                           => '加息券发放数量',
            'round(fanxian_grant_amount/100,2) as fanxian_grant_amount' => '返现券发放金额(元)',
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
        $sumDixian = 0;
        $sumTixian = 0;
        $sumJiaXi = 0;
        $sumFanxian = 0;
        foreach($rs as $key=> $r) {

            if(!$_isDownload){
                $rs[$key]['__PKEY__'] = $r['ymd'];
                $sumDixian += $r['dixian_grant_amount'];
                $sumTixian += $r['tixian_grant_amount'];
                $sumJiaXi += $r['jiaxi_grant_num'];
                $sumFanxian += $r['fanxian_grant_amount'];
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
     * 发放详情
     */
    public function detailAction () {
        $fieldsMap = [
            'tb_user_final.userId'=>'用户Id',
            'tb_user_final.nickname'=>'用户昵称',
            'tb_user_final.realname'=>'真实姓名',
            'tb_user_final.phone'=>'手机号',
            'tb_vouchers_final.ymdCreate'=>'券赠送时间',
            'tb_vouchers_final.voucherType'=>'券类型',
            'tb_vouchers_final.amount'=>'券金额(元)/加息(%)',

        ];
        $fields = implode(',', array_keys($fieldsMap));
        $_isDownload = $this->_request->get('__EXCEL__');
        if(empty($_isDownload)){
            $ymdCreate = $this->_request->get('ymdCreate');
            if(empty($ymdCreate)){
                $this->returnError('参数错误');
            }
            $pageid = $this->_request->get('pageId', 1) - 0;
            $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $this->_view->assign('pager', $pager);
            $where = 'where ymdCreate='.$ymdCreate;
            $order = 'order by ymdCreate desc';
            $rs = $this->getDetailData($this->db_rpt, 'count(*) as n', $where);
            $recordsCount = $rs[0]['n'];
            $pager->init($recordsCount, $pageid);
            $limits = 'limit '.$pager->rsFrom().', '.$pagesize;
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order, $limits);
        }else {
            $where = urldecode($this->_request->get('where'));
            $order = 'order by  ymdCreate desc';
            $rs = $this->getDetailData($this->db_rpt, $fields, $where, $order);

        }
//var_log($rs, 'rs########');
        foreach($rs as $key=> $v){
            $rs[$key]['ymdCreate'] = date('Y-m-d', strtotime($v['ymdCreate']));
            $rs[$key]['voucherType'] = \Rpt\Funcs::voucherName($v['voucherType']);
            $rs[$key]['amount'] = round($v['amount']/100, 2);
            if($v['voucherType']==2){
                $rs[$key]['amount'].='%';
            }
        }

        if($_isDownload){
            return $this->downExcel($rs, $fieldsMap);
        }
        $this->_view->assign('records', $rs);
        $this->_view->assign('fieldsMap', $fieldsMap);
        $this->_view->assign('where', urlencode($where));
        $this->_view->assign('ymdCreate', $ymdCreate);
    }

    private function getDetailData ($db, $fields, $where, $order='', $limits='') {

        $sql = 'select '.$fields.' from tb_vouchers_final'
            .' left join tb_user_final'
            .' on tb_vouchers_final.userId = tb_user_final.userId';
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