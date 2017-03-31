<?php
/**
 *
 * 二期需求 投标统计
 *
 * TODO: 月报表已经ok, 日报表和详细统计可能需要修改, 需要去确认.
 *  还差个每一行的pk
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/19 0019
 * Time: 上午 9:50
 */

class BidstatisticsController extends \Prj\ManagerCtrl {

    protected $db_rpt;
    protected $pageSizeEnum =[50, 100, 200];
    public function init () {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    protected $fieldsMap = [
        'ymd'=>'日期',
        'amount_succ_normal'                     =>'非超级用户成功投标金额(元)',
        'count_succ_normal'                      =>'非超级用户成功投标笔数(元)',
        'amount_fail_normal'                     =>'非超级用户投资失败金额(元)',
        'count_fail_normal'                       =>'非超级用户投资失败笔数(元)',
        'amount_succ_super'                       =>'超级用户成功投标金额(元)',
        'count_succ_super'                        =>'超级用户成功投标笔数',
        'amount_fail_super'                       =>'超级用户投资失败金额(元)',
        'count_fail_super'                        =>'超级用户成功投标笔数',
        '(amount_succ_normal+amount_succ_super) as sumSuccAmount'      => '合计成功金额(元)',
        '(count_succ_normal+count_succ_super) as sumSuccCount'         =>'合计成功笔数',
        '(amount_fail_normal+amount_fail_super)as sumFailedAmount'     =>'合计失败金额(元)',
        '(count_fail_normal+count_fail_super) as sumFailedCount'       =>'合计失败笔数',
    ];

    /**
     * 月报
     */
    public function monthbidAction () {
        $fieldsMap = [
            'ym' => '日期',
            'amount_succ_super' => '超级用户成功购买金额(元)',
            'amount_succ_normal' => '普通用户成购买金额(元)',
            'total' => '合计(元)',
        ];
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $this->db_rpt->getRecords('db_kkrpt.tb_bidmonth',
                array_keys($fieldsMap), $where, 'rsort ym');
            $records = $this->transDataForMonth($records);
            return $this->downExcel($records, $fieldsMap, ['ym'=>'string']);
        }else {
            $ymFrom = $this->_request->get('ymFrom');
            $ymTo = $this->_request->get('ymTo');
            $pageid = $this->_request->get('pageId', 1) - 0;
            $pagesize = $this->_request->get('pageSize',
                    current($this->pageSizeEnum)) - 0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);

            $whereForNextPage = [];
            if(!empty($ymFrom)) {
                if(!\Rpt\Funcs::dateYmCheck($ymFrom)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $tmpYmd = date('Ym', strtotime($ymFrom.'-01'));
                $where['ym]'] = $tmpYmd;
                $whereForNextPage['ymd]'] = date('Ymd', strtotime($ymFrom.'-01'));
            }

            if(!empty($ymTo)) {
                if(!\Rpt\Funcs::dateYmCheck($ymTo)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $tmpYmd = date('Ym', strtotime($ymTo.'-01'));
                $where['ym['] = $tmpYmd;
                $lastDay= \Rpt\Funcs::monthOfLastDay(strtotime($ymTo.'-01'));
                $whereForNextPage['ymd['] = date('Ymd', strtotime($ymTo.'-'.$lastDay));
            }

            if(!empty($ymFrom) && !empty($ymTo)) {
                if($ymFrom > $ymTo) {
                    return $this->returnError('日期范围错误');
                }
            }

            $count = $this->db_rpt->getRecordCount('db_kkrpt.tb_bidmonth',
                $where);

            $pager->init($count, $pageid);

            $fields = array_merge(array_keys($fieldsMap), ['ym as __PKEY__']);
            $records = $this->db_rpt->getRecords('db_kkrpt.tb_bidmonth',
                $fields, $where, 'rsort ym',
                $pagesize, $pager->rsFrom());

            $records= $this->transDataForMonth($records);
            $total = $this->total($ymFrom, $ymTo);
            $this->_view->assign('ymFrom', $ymFrom);
            $this->_view->assign('ymTo', $ymTo);
            $this->_view->assign('fieldsMap', $fieldsMap);
            $this->_view->assign('records', $records);
            $this->_view->assign('sum_succ_super', $total['sum_succ_super']);
            $this->_view->assign('sum_succ_normal', $total['sum_succ_normal']);
            $this->_view->assign('sum_succ_total', $total['sum_succ_total']);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('whereForNextPage', urlencode(json_encode($whereForNextPage)));
        }
    }


    // 金额转换
    protected function transDataForMonth ($records) {
        $fields_money = ['amount_succ_super', 'amount_succ_normal', 'total'];
        foreach($records as $k => $r) {
            $records[$k]['ym'] = \Rpt\Funcs::dateYmTrans($r['ym']);
            foreach($fields_money as $fieldname) {
                if($r[$fieldname] == 0){
                    $records[$k][$fieldname] = '';
                }else {
                    $records[$k][$fieldname]= number_format($r[$fieldname]*0.01, 2);
                }
            }
        }
       return $records;
    }

    /**
     * 合计
     * @param $ymFrom
     * @param $ymTo
     */
    protected function total ($ymFrom , $ymTo) {
        $fields = [
            'sum(amount_succ_super)*0.01 as sum_succ_super',
            'sum(amount_succ_normal)*0.01 as sum_succ_normal',
            'sum(total)*0.01 as sum_succ_total',
        ];
        !empty($ymFrom) && $where['ym]'] = \Rpt\Funcs::dateYmTrans($ymFrom);
        !empty($ymTo) && $where['ym['] = \Rpt\Funcs::dateYmTrans($ymTo);
        $record =  $this->db_rpt->getRecord ('db_kkrpt.tb_bidmonth', $fields, $where);
        $record['sum_succ_super'] = number_format($record['sum_succ_super'], 2);
        $record['sum_succ_normal']= number_format($record['sum_succ_normal'], 2);
        $record['sum_succ_title']= number_format($record['sum_succ_title'], 2);
        return $record;
    }


    /**
     * 日报表
     */
    public function dailybidAction () {
        $isDownload = $this->_request->get('__EXCEL__')+0;
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $this->db_rpt->getRecords('db_kkrpt.tb_bidstatistics', array_keys($this->fieldsMap),
                $where, 'rsort ymd');
        }else {
            $whereForNextPage = '';
            $ym = $this->_request->get('ym');
            if(!empty($ym)) {
                $ym = \Rpt\Funcs::dateYmTrans($ym);
                $ymdFrom = $ym.'-01';
                $lastDay = \Rpt\Funcs::monthOfLastDay(strtotime($ymdFrom));
                $ymdTo = $ym.'-'.sprintf('%02d', $lastDay);
            }else {
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
            }
            // 检测日期格式以及起始日期结束日期范围是否正确
            if(!empty($ymdFrom) ) {
                if (!\Rpt\Funcs::check_date($ymdFrom)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08-08');
                }
                $where['ymd]']= date('Ymd', strtotime($ymdFrom));
                !empty($whereForNextPage) && $whereForNextPage .= ' and ';
                $whereForNextPage .= 'ymd >='.date('Ymd', strtotime($ymdFrom));
            }
            if(!empty($ymdTo)) {
                if(!\Rpt\Funcs::check_date($ymdTo)){
                    return $this->returnError('日期格式不正确, 格式如:2016-08-08');
                }
                $where['ymd['] = date('Ymd', strtotime($ymdTo));
                !empty($whereForNextPage) && $whereForNextPage .= ' and ';
                $whereForNextPage .= 'ymd <='.date('Ymd', strtotime($ymdTo));
                $whereForNextPage = ' where '.$whereForNextPage;
            }
            if(!empty($ymdFrom) && !empty($ymdTo)) {
                if(strtotime($ymdFrom) > strtotime($ymdTo)){
                    return $this->returnError('起始日期不能大于截止日期');
                }
            }

            $count = $this->db_rpt->getRecordCount('db_kkrpt.tb_bidstatistics', $where);
            $pagesize = $this->_request->get('pageSize',
                    current($this->pageSizeEnum)) - 0;
            $pageid = $this->_request->get('pageId', 1);
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $pager->init($count, $pageid);
            $records = $this->db_rpt->getRecords('db_kkrpt.tb_bidstatistics', array_keys($this->fieldsMap)+['ymd as __PKEY__'],
                $where, 'rsort ymd', $pagesize, $pager->rsFrom());

            $summary = $this->db_rpt->getRecord('db_kkrpt.tb_bidstatistics',
                'sum(amount_succ_normal) as succ_normal_amount, sum(amount_succ_super) as succ_super_amount',
                $where);
            $this->_view->assign('succ_super_amount', number_format($summary['succ_super_amount']*0.01, 2));
            $this->_view->assign('succ_normal_amount', number_format($summary['succ_normal_amount']*0.01, 2));
            $this->_view->assign('sumSuccAmount', number_format($summary['succ_normal_amount']*0.01+$summary['succ_super_amount']*0.01, 2));
//var_log($summary, 'summary#######');

        }
        foreach($records as $k => $v) {

            if(!$isDownload){
                $records[$k]['__PKEY__'] = date('Y-m-d', strtotime($v['ymd']));
            }
            foreach ($v as $key => $value) {
                if ($value==0) {
                    $records[$k][$key] = '';
                }else {
                    switch ($key) {
                        case 'ymd':
                            $records[$k]['ymd'] = date('Y-m-d', strtotime($value));
                            break;
                        case 'amount_succ_normal':
                        case 'amount_fail_normal':
                        case 'amount_succ_super':
                        case 'amount_fail_super':
                        case 'sumSuccAmount':
                        case 'sumFailedAmount':
                            $records[$k][$key] = number_format($value*0.01, 2);
                            break;
                        default:break;
                    }
                }
            }
        }


        if ($isDownload) {
            return $this->downExcel($records, $this->fieldsMap, ['ymd'=>'string']);
        }else {
            $this->_view->assign('ymdFrom', $ymdFrom);
            $this->_view->assign('ymdTo', $ymdTo);
            $this->_view->assign('records', $records);
            $this->_view->assign('fieldsMap', $this->fieldsMap);
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('pager', $pager);
            $this->_view->assign('whereForNextPage', urlencode($whereForNextPage));
        }
    }


    /**
     * 用户投标详情
     */
    public function detailbidAction () {
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $userId = $this->_request->get('userId');
        $phone = $this->_request->get('phone');
        $realname = $this->_request->get('realname');
        $waresName = $this->_request->get('waresName');
        $_isDownload = $this->_request->get('__EXCEL__');
        $where = urldecode($this->_request->get('where'));
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $this->_view->assign('pager', $pager);
        $fieldsMap = [
            'ordersId'=>'订单号',
            'tb_user_final.userId'=>'用户ID',
            'realname'=>'姓名',
            'phone'=>'手机号',
            'concat(tb_orders_final.ymd, LPAD(tb_orders_final.hhiiss, 6, \'0\')) as ymdhis' => '操作时间',
            'round(tb_orders_final.amount/100, 2)as amount'=>'实际投资金额(元)',
            'round(tb_orders_final.amountExt/100, 2) as amountExt'=>'使用红包(元)',
            'contractId'=>'推广渠道',
            'tb_orders_final.clientType'=>'投标渠道',
            'orderStatus'=>'投标状态',
            'tb_products_final.waresName' => '标的名称',
            'tb_products_final.shelfId'=>'标的类型',
            'round(tb_products_final.amount/100, 2) as waresAmount'=>'标的总额(元)',
            'concat(tb_products_final.deadLine, tb_products_final.dlUnit) as deadLine'=>'标的期限',
            'tb_products_final.yieldStatic'=>'标的利率',
            'tb_products_final.statusCode'=>'标的状态',
        ];
        if(empty($where)){

            $tmp_where = [];
            if(!empty($ymdFrom) && !empty($ymdTo)) {
                if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                    $this->returnError('日期不合法');
                }
                if($ymdTo< $ymdFrom) {
                    $this->returnError('起始日期不应该晚于截止日期');
                }
                $ymdFromDBFormat = date('Ymd', strtotime($ymdFrom));
                $ymdToDBFormat = date('Ymd', strtotime($ymdTo));
                $tmp_where[] = 'ymd>='.$ymdFromDBFormat;
                $tmp_where[] = 'ymd<='.$ymdToDBFormat;
            }
            if(!empty($phone)) {
                if(!is_numeric($phone)){
                    return $this->returnError('手机号码含有非数字字符');
                }
            }

            empty($userId) ? '' : ($tmp_where[]='tb_user_final.userId=\''.$userId.'\'');
            empty($phone) ? '' : ($tmp_where[]='phone='.$phone);
            empty($realname) ? '' : ($tmp_where[]='realname like\'%'.$realname.'%\'');
            empty($waresName) ? '' : ($tmp_where[]='tb_products_final.waresName like \'%'.$waresName.'%\'');
            $where = '';
            foreach($tmp_where as $v){
                if (empty($where)) {
                    $where = ' where ';
                }else {
                    $where .= ' and ';
                }
                $where .= $v.' ';
            }
            if (empty($where)) {
                $where = 'where tb_products_final.mainType!=501 and tb_products_final.statusCode!=4011 ';
            }else {
                $where .= 'and tb_products_final.mainType!=501 and tb_products_final.statusCode!=4011 ';
            }

            $rs = $this->execSql($this->generateSql('count(*) as n', $where), $this->db_rpt);
//error_log(\Sooh\DB\Broker::lastCmd());
            $recordsCount = $rs[0]['n'];
            $pager->init($recordsCount, $pageid);
            $limit = 'limit '.$pager->rsFrom().', '.$pagesize;
            $sql = $this->generateSql(implode(',', array_keys($fieldsMap)), $where, 'order by ymdhis desc', $limit);
        }else {
            $sql = $this->generateSql(implode(',', array_keys($fieldsMap)), $where, 'order by ymdhis desc');
        }
        $rs = $this->execSql($sql, $this->db_rpt);
        /** 获取当前条件下, 所有投资用户的协议号和协议号名称 */
        $contractIdSql = $this->generateSql('distinct(contractId)', $where);
        $contractIds = $this->execSql($contractIdSql, $this->db_rpt);

        if(!empty($contractIds)) {
            foreach($contractIds as $k=>$v){
                $contractIds[$k]=$v['contractId'];
            }
            $arr_contract = $this->db_rpt->getPair('db_kkrpt.tb_contract_0', 'contractId', 'remarks', ['contractId'=>$contractIds]);
        }

        $indexFrom = ($pageid-1)*$pagesize;
        foreach($rs as $key=> $v){
            $rs[$key] = array_merge(['index_num'=>++$indexFrom], $rs[$key]);
            $rs[$key]['ymdhis'] = date('Y-m-d H:i:s', strtotime($v['ymdhis']));
            if(!($v['amountExt']>0)){
                $rs[$key]['amountExt']='';
            }
            if(isset($arr_contract[$v['contractId']])){
                $rs[$key]['contractId'] = $arr_contract[$v['contractId']];
            }
            $rs[$key]['clientType'] = \Prj\Consts\ClientType::clientTypes($v['clientType']);
            $rs[$key]['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$v['orderStatus']];
            $rs[$key]['shelfId'] = \Rpt\Funcs::product_type($v['shelfId']);
            $rs[$key]['yieldStatic'] .='%';
//var_log($v, 'v########');
            $rs[$key]['statusCode'] = \Prj\Consts\Wares::returnStatus($v['statusCode']);
        }

        $fieldsMap = array_merge(['index_num'=>'序号'], $fieldsMap);
        if($_isDownload){
            return $this->downExcel($rs, $fieldsMap);
        }
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('phone', $phone);
        $this->_view->assign('realname', $realname);
        $this->_view->assign('records', $rs);
        $this->_view->assign('fieldsMap', $fieldsMap);
        $this->_view->assign('waresName',$waresName);
        $this->_view->assign('where', urlencode($where));
    }

    private function generateSql($fields, $where, $order='', $limit='') {
        $sql = 'select '.$fields
            .' from tb_orders_final'
            .' left join tb_user_final'
            .' on tb_orders_final.userId = tb_user_final.userId'
            .' left join tb_products_final'
            .' on tb_orders_final.waresId = tb_products_final.waresId '
            .' '.$where
            .' '.$order
            .' '.$limit;
        return $sql;
    }

    private function execSql ($sql,$db){
        $result = $db->execCustom(['sql' => $sql]);
        return $db->fetchAssocThenFree($result);
    }

}