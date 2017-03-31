<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/31 0031
 * Time: 上午 10:33
 */
class PcsitetrafficController extends \Prj\ManagerCtrl {

    public function init(){
        parent::init();
    }

    public function indexAction () {
        $category = ['浏览量(PV)','访客数(UV)','IP数'];
        $this->_view->assign('category', json_encode($category));
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        if($this->ini->viewRenderType() == 'wap') {
            $this->_view->assign('_view', $this->ini->viewRenderType());
        }
        $rs =[];
        $legendData=[];
        if(empty($ymdFrom)) {
            $ymdFrom = date('Y-m-d', time()-7*86400);
        }

        if(empty($ymdTo)) {
            $ymdTo = date('Y-m-d', time()-86400);
        }

        if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
            $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-08');
            $this->_view->assign('rs', json_encode($rs));
            $this->_view->assign('legendData', json_encode($legendData));
            $this->returnError('日期格式错误, 格是如:2016-08-08');
            return;
        }

        $ymdFrom = \Rpt\Funcs::date_format($ymdFrom);
        $ymdTo = \Rpt\Funcs::date_format($ymdTo);

        if(date('Ymd', strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
            $this->_view->assign('errMsg','日期范围错误');
            $this->_view->assign('rs', json_encode($rs));
            $this->_view->assign('legendData', json_encode($legendData));
            $this->returnError('日期范围错误');
            return;
        }


        $dtFrom = strtotime($ymdFrom);
        $ymdTo1 = date('Ymd', $dtFrom-86400);
        $days = (strtotime($ymdTo) - $dtFrom) / 86400;
        $ymdFrom1 = date('Ymd', strtotime($ymdTo1) - $days*86400);

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);

        $rs = [];
        $record = $db->getRecord('db_kkrpt.tb_baidutongji',
            'sum(pv_count) as pv_count, sum(visitor_count) as visitor_count, sum(ip_count) as ip_count',
            ['ymd]'=>date('Ymd', strtotime($ymdFrom)), 'ymd['=>date('Ymd', strtotime($ymdTo))]);
        $key2 = date('Y.m.d', strtotime($ymdFrom)).'-'.date('Y.m.d', strtotime($ymdTo));
        $rs[$key2]['pv_count'] = ($record['pv_count']>0)?$record['pv_count']:0;
        $rs[$key2]['visitor_count'] = ($record['visitor_count']>0)?$record['visitor_count']:0;
        $rs[$key2]['ip_count'] = ($record['ip_count']>0)?$record['ip_count']:0;

        $record = $db->getRecord('db_kkrpt.tb_baidutongji',
            'sum(pv_count) as pv_count, sum(visitor_count) as visitor_count, sum(ip_count) as ip_count',
            ['ymd]'=>$ymdFrom1, 'ymd['=>$ymdTo1]);
        $key1 = date('Y.m.d', strtotime($ymdFrom1)).'-'.date('Y.m.d', strtotime($ymdTo1));
        $rs[$key1]['pv_count'] = ($record['pv_count']>0)?$record['pv_count']:0;
        $rs[$key1]['visitor_count'] = ($record['visitor_count']>0)?$record['visitor_count']:0;
        $rs[$key1]['ip_count'] = ($record['ip_count']>0)?$record['ip_count']:0;


//var_log($rs, 'rs####');
        $legendData = [$key2, $key1];

        $this->_view->assign('rs', json_encode($rs));
        $this->_view->assign('legendData', json_encode($legendData));

    }
}