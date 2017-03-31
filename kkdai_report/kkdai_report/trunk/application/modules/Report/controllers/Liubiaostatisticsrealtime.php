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
use \Rpt\DataDig\LiubiaostatisticsrealtimeDataDig as LiubiaoDataDig;
class LiubiaostatisticsrealtimeController extends \Prj\ManagerCtrl {

    protected $db_rpt;
    protected $pageSizeEnum =[20, 30, 50];

    public function init () {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    public function dayAction () {
        $dataDig = new LiubiaoDataDig();
        $isDownload = $this->_request->get('__EXCEL__');
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForDay($where, null, true);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDay))
            );
        }

        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);

        $where =[];
        if(!empty($ymdFrom)) {
            if (!\Rpt\Funcs::check_date($ymdFrom)){
                return $this->returnError('日期格式不正确, 格式如:2016-08');
            }
            $where['ymdFrom'] = \Rpt\Funcs::date_format($ymdFrom, 'Y-m-d');
        }
        if(!empty($ymdTo)) {
            if(!\Rpt\Funcs::check_date($ymdTo)){
                return $this->returnError('日期格式不正确, 格式如:2016-08');
            }
            $where['ymdTo'] =  \Rpt\Funcs::date_format($ymdTo, 'Y-m-d');
        }
        if($ymdTo< $ymdFrom) {
            return $this->returnError('起始日期不应该晚于截止日期');
        }

        $records = $dataDig->dataForDay($where, $pager, false);

        $this->_view->assign('records', $records);
        $this->_view->assign('fieldsMap',
            $dataDig->getTableHeader($dataDig->tableHeaderForDay));
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', urlencode(json_encode($where)));
        $this->_view->assign('whereForNextPage', urlencode(json_encode($where)));
        $this->_view->assign('summary', $dataDig->summaryForDay());
    }

    /**
     * 流标投标详情
     */
    public function detailAction () {
        $dataDig = new LiubiaoDataDig();
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForDetail($where);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDetail))
            );
        }else {
            $bid_id = $this->_request->get('bid_id');
            $where = ['bid_id'=>$bid_id];
            $pageid = $this->_request->get('pageId');
            $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum))-0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $pager->init(-1, $pageid);
            $records = $dataDig->dataForDetail($where, $pager);
            $this->_view->assign('records', $records);
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('pager', $pager);
            $this->_view->assign('bid_id', $bid_id);
            $this->_view->assign(
                'fieldsMap',
                $dataDig->getTableHeader($dataDig->tableHeaderForDetail)
            );

        }
    }

}