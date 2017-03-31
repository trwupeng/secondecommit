<?php
/**
 *
 * 二期需求 流标统计
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/20
 * Time: 下午 15:50
 */

class LiubiaostatisticsController extends \Prj\ManagerCtrl {

    protected $db_rpt;
    protected $pageSizeEnum =[50, 100, 200];

    public function init () {
        parent::init();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $this->db_produce = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_p2p);
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    protected $fieldsMap = [
        'ymdStartReal'                           =>'上架日期',
        'waresId'                                =>'标的Id',
        'waresName'                              =>'标的标题',
        'shelfId'                                =>'标的类型',
        'concat(deadLine,dlunit) as deadLine'    =>'期限',
        'yieldStatic'                            =>'利率',
        'round(amount/100, 2) as amount'         =>'标的金额',
        'round(realRaise/100, 2) as realRaise'   =>'实际募集金额',
        'statusCode'                             =>'状态',
    ];

    /**
     * 月报表
     */
    public function summaryAction () {
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $_isDownload = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $this->_view->assign('pager', $pager);

        if(empty($ymdFrom) && empty($ymdTo) && !$_isDownload) {
            $ymdTo = date('Y-m-d', strtotime('-1 day'));
            $ymdFrom = date('Y-m-d', strtotime('-30 day'));
        }
        if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
            $this->returnError('日期格式错误, 格式如:2016-08-08');
        }
        if($ymdTo< $ymdFrom) {
            $this->returnError('起始日期不应该晚于截止日期');
        }

        /** 获取数据记录 */
        $ymdFromDBFormat = date('Ymd' ,strtotime($ymdFrom));
        $ymdToDBFormat = date('Ymd', strtotime($ymdTo));

        if ($_isDownload) {
            $where = $this->_request->get('where');
            $where = json_decode(urldecode($where), true);
            $rs = $this->db_rpt->getRecords(\Rpt\Tbname::tb_products_final, array_keys($this->fieldsMap),
                $where, 'rsort ymdStartReal');

        }else {
            $where = ['ymdStartReal]' => $ymdFromDBFormat, 'ymdStartReal[' => $ymdToDBFormat, 'statusCode'=>4011];
            $recordsCount = $this->db_rpt->getRecordCount(\Rpt\Tbname::tb_products_final, $where);
            $pager->init($recordsCount, $pageid);

            $rs = $this->db_rpt->getRecords(\Rpt\Tbname::tb_products_final, array_keys($this->fieldsMap),
                $where, 'rsort ymdStartReal', $pagesize, $pager->rsFrom());
        }
        foreach($rs as $key=> $r) {
            if(!$_isDownload){
                $rs[$key][__PKEY__] = $r['waresId'];
            }
            if(!($r['realRaise']>0)){
                $rs[$key]['realRaise']=0;
            }
            $rs[$key]['ymdStartReal'] = date('Y-m-d', strtotime($r['ymdStartReal']));
            $rs[$key]['yieldStatic'] .='%';
            $rs[$key]['statusCode'] = \Prj\Consts\Wares::returnStatus($r['statusCode']);
            $rs[$key]['shelfId']=\Rpt\Funcs::product_type($r['shelfId']);
        }
        if($_isDownload) {
            return $this->downExcel($rs, $this->fieldsMap);
        }

        $this->_view->assign('records', $rs);
        $this->_view->assign('fieldsMap', $this->fieldsMap);
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('pagesize', $pagesize);
        $this->_view->assign('where', urlencode(json_encode($where)));
    }

    public function detailAction () {

        $fieldsMap = [
            'userId' => '客户编号',
            'realname' => '客户姓名',
            'phone' => '手机号',
            'waresName' => '标的名称',
            'waresAmount' => '标的总额(元)',
            'deadLine' => '标的期限',
            'yieldStatic' => '标的利率(%)',
            'buyAmount' => '购买金额(元)',
            'orderStatus' => '状态',
        ];

        $isDownload = $this->_request->get('__EXCEL__');
        $where  = '';
        $records = [];
        $fields = 'userId, realname, phone, tb_products_final.waresName, dlUnit,tb_products_final.amount as waresAmount, deadLine, tb_orders_final.yieldStatic, (tb_orders_final.amount + tb_orders_final.amountExt) as buyAmount , tb_orders_final.orderStatus';
        if ($isDownload) {
            $arr_where = json_decode(urldecode($this->_request->get('where')), true);
            !empty($arr_where['ymdFrom']) && $where .= ' and ';
            $where .= ' ymd >= '.date('Ymd', strtotime($where['ymdFrom']));
            !empty($arr_where['ymdTo']) && $where.= ' and ';
            $where .= ' ymd <= '.date('Ymd', strtotime($where['ymdTo']));

            !empty($arr_where['waresId']) && $where.=' and ';
            $where .= ' tb_orders_final.waresId=\''.$where['waresId'].'\'';
            $sql = $this->buildSqlForDetail(
                $fields,
                'left join tb_user_final using(userId) left join tb_products_final using (waresId)',
                $where,
                ' order by ymd desc, hhiiss desc'
            );

        }else {
            $waresId = $this->_request->get('waresId');
            $where = ' tb_orders_final.waresId=\''.$waresId.'\'';
            $pageid = $this->_request->get('pageId');
            $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum))-0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $recordsCount = $this->db_rpt->getRecordCount('db_kkrpt.tb_orders_final', ['waresId'=>$waresId]);
            $pager->init($recordsCount, $pageid);

            $sql = $this->buildSqlForDetail(
                $fields,
                'left join tb_user_final using(userId) left join tb_products_final using (waresId)',
                $where,
                'order by ymd desc, hhiiss desc',
                $pager
            );
        }

        $result = \Rpt\Funcs::execSql($sql, $this->db_rpt);
        foreach($result as $k => $r) {
            $tmp = array_fill_keys(array_keys($fieldsMap), '');
            $tmp['userId'] = $r['userId'];
            $tmp['realname'] = $r['realname'];
            $tmp['phone'] = $r['phone'];
            $tmp['waresName'] = $r['waresName'];
            !empty($r['waresAmount']) &&  $tmp['waresAmount'] = number_format($r['waresAmount']*0.01 ,2);
            $tmp['deadLine'] = $r['deadLine'].$r['dlUnit'];
            $tmp['yieldStatic'] = $r['yieldStatic'];
            !empty($r['buyAmount']) && $tmp['buyAmount'] = number_format($r['buyAmount']*0.01, 2);
            $tmp['orderStatus'] = \Prj\Consts\OrderStatus::$enum[$r['orderStatus']];
            $records[] = $tmp;
        }

        if ($isDownload) {
            return $this->downExcel($records, $fieldsMap);
        }else {
            $this->_view->assign('records', $records);
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('pager', $pager);
            $this->_view->assign('fieldsMap',$fieldsMap);
            $this->_view->assign('waresId', $waresId);
        }
    }

    protected function buildSqlForDetail ($fields, $join='', $where=null, $order='', $pager=null) {
        $sql = ' select '.$fields.' from tb_orders_final';
        !empty($join) && $sql.= ' '.$join;
        !empty($where) && $sql .= ' where '.$where;
        !empty($order) && $sql .= ' '.$order;
        !empty($pager) && $sql.= ' limit '.$pager->rsFrom().','.$pager->page_size;
        return $sql;
    }

}