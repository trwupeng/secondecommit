<?php
use Sooh\Base\Form\Item as form_def;
use Sooh\Base\Form\Options as options_def;
require_once __DIR__.'/Rptsimple2Ctrl.php';

class MonthreportController extends \Rptsimple2Ctrl {

    protected $year;
    protected $month;
    protected $copartners;
    public function init () {
        parent::init();
        $year = date('Y');
        for ($y = $year; $y>= 2014; $y--) {
            $this->year[$y] = $y;
        }
        for ($m = 1; $m<=12; $m++) {
            $this->month[$m] = $m;
        }
        $this->copartners = \Rpt\DataDig\CopartnerWorthData::getCopartners();
    }
    
    public function indexAction () {
        
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $saveAsExcel = $this->_request->get('__EXCEL__');
        
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $formEdit->addItem('_yearFrom_eq', form_def::factory('年', date('Y'), form_def::select)->initMore(new \Sooh\Base\Form\Options($this->year)))
                 ->addItem('_monthFrom_eq', form_def::factory('月', '1', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->month)))
                 ->addItem('_yearTo_eq', form_def::factory('到，年', date('Y'), form_def::select)->initMore(new \Sooh\Base\Form\Options($this->year)))
                 ->addItem('_monthTo_eq', form_def::factory('月', date('n'), form_def::select)->initMore(new \Sooh\Base\Form\Options($this->month)))
//                  ->addItem('txt', form_def::factory('月','',form_def::constval))
                 ->addItem('_copartnerId_eq', form_def::factory('渠道', '', form_def::select)->initMore(new \Sooh\Base\Form\Options($this->copartners,'全部')));
        $formEdit->fillValues();
// var_log($where, __FILE__.'>>>where>>>>');
        if ($formEdit->flgIsThisForm) {
            $where = $formEdit->getWhere();
            $where['ymd]'] = $where['yearFrom='].sprintf("%02d", $where['monthFrom=']);
            unset ($where['yearFrom=']);
            unset($where['monthFrom=']);
            $where['ymd['] = $where['yearTo='].sprintf("%02d", $where['monthTo=']);
            unset($where['yearTo=']);
            unset($where['monthTo=']);
        }else {
            $where['ymd]'] = date('Y')."01";
            $where['ymd['] = date('Ym');
        } 
        
        if ($where['ymd['] < $where['ymd]']) {
            $this->returnError('起始时间应该小于或等于结束时间');
        }
        if ($saveAsExcel) {
            // 导出复选框选中的记录， 未做 
//             $keyStr = $this->_request->get('ids');
//             if (!empty($keyStr)) {
//                 foreach ($keyStr as $kk => $vv) {
//                     $keyStr[$kk] = \Prj\Misc\View::decodePkey($vv);
//                 }
//             }
            
            $where = $this->_request->get('where');
            $where = json_decode($where, true);
        }
        
        $o = new \Rpt\DataDig\MonthreportData();
        $headers = $o->getAllMonthHeader();
        $records = $o->allMonth($where);
        if ($saveAsExcel) {
            foreach ($records as $k => $r) {
                unset($records[$k]['_pkey_val_']);
            }
            return  $this->downEXCEL($records, array_keys($headers), null, false);
        }
        
        $pager->init(sizeof($records), $pageid);
        
        $records = array_slice($records, ($pageid-1)*$pagesize, $pagesize);
        $this->_view->assign('records', $records);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', json_encode($where));
    }
    
    public function detailAction () {
        $_pkey_val_ = $this->_request->get('_pkey_val_');
        $saveAsExcel = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        
        if (!empty($_pkey_val_)) {
            $_pkey_val_ = \Prj\Misc\View::decodePkey($_pkey_val_);
            $ymdFrom = $_pkey_val_['ym'].'01';
            $ymdTo = date('Ymd', strtotime("+1 month", strtotime($ymdFrom)) - 86400);
            unset($_pkey_val_['ym']);
            $_pkey_val_['ymd]'] = $ymdFrom;
            $_pkey_val_['ymd['] = $ymdTo;
            $copartnerId = isset($_pkey_val_['copartnerId']) ? $_pkey_val_['copartnerId'] : null;
        }
        
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
        $formEdit->addItem('_ymd_g2', form_def::factory('从', $ymdFrom, form_def::datepicker))
                 ->addItem('_ymd_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
                 ->addItem('_copartnerId_eq',form_def::factory('渠道Id', $copartnerId?$copartnerId:null, form_def::hidden));
        $formEdit->fillValues();
        
        if ($formEdit->flgIsThisForm) {
            $where = $formEdit->getWhere();
            $where['ymd]'] = date('Ymd', strtotime($where['ymd]']));
            $where['ymd['] = date('Ymd', strtotime($where['ymd[']));
        }else {
            $where = $_pkey_val_;
        }

        
        if ($saveAsExcel) {
            $where = $this->_request->get('where');
            $where = json_decode($where, true);
// var_log($where, 'where>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');            
        }
        
        $o = new \Rpt\DataDig\MonthreportData();
        $headers = $o->getDetailHeaders();
        $records = $o->detailRecords($where);
// var_log($records, 'records>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>');         
        if ($saveAsExcel) {
            return  $this->downEXCEL($records, array_keys($headers), null, false);
        }
         
        $pager->init(sizeof($records), $pageid);
        $records = array_slice($records, ($pageid-1)*$pagesize, $pagesize);
        $this->_view->assign('headers', $headers);
        $this->_view->assign('records', $records);
        $this->_view->assign('where', json_encode($where));
        $this->_view->assign('pager', $pager);
    }
}







