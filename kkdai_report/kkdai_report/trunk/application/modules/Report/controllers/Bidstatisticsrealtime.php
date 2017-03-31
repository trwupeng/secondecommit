<?php
/**
 *
 * 二期需求 投标统计(实时)
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/19 0019
 * Time: 上午 9:50
 */
use \Rpt\DataDig\BidstatisticsrealtimeDataDig as BidDataDig;
class BidstatisticsrealtimeController extends \Prj\ManagerCtrl {

    protected $pageSizeEnum =[20, 30, 50];
    public function init () {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }
    /**
     * 月报
     */
    public function monthbidAction () {
        $dataDig = new BidDataDig();
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForMonth($where, null, true);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForMonth)),
                ['ym'=>'string']
            );
        }else {
            $ymFrom = $this->_request->get('ymFrom');
            $ymTo = $this->_request->get('ymTo');
            $whereForDaily = [];
            if(!empty($ymFrom)){
                if(!\Rpt\Funcs::dateYmCheck($ymFrom)){
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $whereForDaily['ymdFrom'] = $ymFrom.'-01';
            }
            if(!empty($ymTo)){
                if ( !\Rpt\Funcs::dateYmCheck($ymTo)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $lastDay = \Rpt\Funcs::monthOfLastDay(strtotime($ymTo.'-01'));
                $whereForDaily['ymdTo'] = $ymTo.'-'.$lastDay;
            }

            if(!empty($ymFrom) && !empty($ymTo) && $ymTo<$ymFrom) {
                return $this->returnError('起始日期不能大于截止日期');
            }
            $pageid = $this->_request->get('pageId', 1) - 0;
            $pagesize = $this->_request->get('pageSize',
                    current($this->pageSizeEnum)) - 0;
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $pager->init(-1, $pageid);
            $where = ['ymFrom'=>$ymFrom, 'ymTo'=>$ymTo];
            $records = $dataDig->dataForMonth($where, $pager, false);
            $this->_view->assign('ymFrom', $ymFrom);
            $this->_view->assign('ymTo', $ymTo);
            $this->_view->assign(
                'fieldsMap',
                $dataDig->getTableHeader($dataDig->tableHeaderForMonth)
            );
            $this->_view->assign('records', $records);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('where',
                urlencode(json_encode($where)));
            $this->_view->assign('summary', $dataDig->summaryForMonth($where));
            $this->_view->assign(
                'whereForNexPage',
                urlencode(json_encode($whereForDaily))
            );
        }
    }

    /**
     * 日报表
     */
    public function dailybidAction () {
        $dataDig = new BidDataDig();
        $isDownload = $this->_request->get('__EXCEL__')+0;
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForDay($where, null, true);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDay)),
                ['ymd'=>'string']
            );
        }else {
            $ym = $this->_request->get('ym');
            if(!empty($ym)) {
                $ymdFrom = $ym.'-01';
                $lastDay = \Rpt\Funcs::monthOfLastDay(strtotime($ymdFrom));
                $ymdTo = $ym.'-'.sprintf('%02d', $lastDay);

            }else {
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
            }
            $whereForDetail=[];
            // 检测日期格式以及起始日期结束日期范围是否正确
            if(!empty($ymdFrom) ) {
                if (!\Rpt\Funcs::check_date($ymdFrom)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08-08');
                }
                $whereForDetail['ymdFrom'] = $ymdFrom;
            }
            if(!empty($ymdTo)) {
                if(!\Rpt\Funcs::check_date($ymdTo)){
                    return $this->returnError('日期格式不正确, 格式如:2016-08-08');
                }
                $whereForDetail['ymdTo'] = $ymdTo;
            }
            if(!empty($ymdFrom) && !empty($ymdTo)) {
                if(strtotime($ymdFrom) > strtotime($ymdTo)){
                    return $this->returnError('起始日期不能大于截止日期');
                }
            }
            $where = ['ymdFrom'=>$ymdFrom, 'ymdTo'=>$ymdTo];
            $pagesize = $this->_request->get('pageSize',
                    current($this->pageSizeEnum)) - 0;
            $pageid = $this->_request->get('pageId', 1);
            $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
            $pager->init(-1, $pageid);
            $records = $dataDig->dataForDay($where, $pager);
            $this->_view->assign('ymdFrom', $ymdFrom);
            $this->_view->assign('ymdTo', $ymdTo);
            $this->_view->assign('records', $records);
            $this->_view->assign('summary',$dataDig->summaryForDay($where));
            $this->_view->assign('fieldsMap',
                $dataDig->getTableHeader($dataDig->tableHeaderForDay));
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('pager', $pager);
            $this->_view->assign(
                'whereForNexPage',
                urlencode(json_encode($whereForDetail))
            );
        }
    }


    /**
     * 用户投标详情
     */
    public function detailbidAction () {
        $ymdFrom = trim($this->_request->get('ymdFrom'));
        $ymdTo = trim($this->_request->get('ymdTo'));
        $userId = trim($this->_request->get('userId'));
        $phone = trim($this->_request->get('phone'));
        $realname = trim($this->_request->get('realname'));
        $waresId = trim($this->_request->get('waresId'));
        $waresName = trim($this->_request->get('waresName'));
        $_isDownload = $this->_request->get('__EXCEL__');
        $where = urldecode($this->_request->get('where'));

        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $pager->init(-1, $pageid);
        if(empty($where)){
            if(!empty($ymdFrom)) {
                if(!\Rpt\Funcs::check_date($ymdFrom)){
                    return $this->returnError('日期错误, 格式如:2016-08-08');
                }
                $where['ymdFrom'] = $ymdFrom;
            }
            if(!empty($ymdTo)){
                if(!\Rpt\Funcs::check_date($ymdTo)){
                    return $this->returnError('日期错误, 格式如:2016-08-08');
                }
                $where['ymdTo'] = $ymdTo;
            }
            if(!empty($ymdFrom) && !empty($ymdTo)) {
                if($ymdTo< $ymdFrom) {
                    $this->returnError('起始日期不应该晚于截止日期');
                }
            }
            if(!empty($phone)) {
                if(!is_numeric($phone)){
                    return $this->returnError('手机号码含有非数字字符');
                }
                $where['customer_cellphone'] = $phone;
            }

            !empty($userId)&& $where['customer_id'] = $userId;
            !empty($realname) && $where['customer_realname']=$realname;
            !empty($waresId) && $where['bid_id'] = $waresId;
            !empty($waresName) && $where['bid_title'] = $waresName;

        }else {
            $where = json_decode(urldecode($where), true);
        }
        $dataDig = new BidDataDig();
        $records = $dataDig->dataForDetail($where, $pager);
        if($_isDownload){
            $records = $dataDig->dataForDetail($where);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDetail))
            );
        }
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('userId', $userId);
        $this->_view->assign('phone', $phone);
        $this->_view->assign('realname', $realname);
        $this->_view->assign('records', $records);
        $this->_view->assign(
            'fieldsMap',
            $dataDig->getTableHeader($dataDig->tableHeaderForDetail)
        );
        $this->_view->assign('waresId',$waresId);
        $this->_view->assign('waresName',$waresName);
        $this->_view->assign('where', urlencode(json_encode($where)));
        $this->_view->assign('pager', $pager);
        $this->_view->assign('summary', $dataDig->summaryForDetail());
    }
}