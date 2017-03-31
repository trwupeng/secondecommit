<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/2 0002
 * Time: 上午 10:27
 */
use \Rpt\DataDig\VoucherrealtimeDataDig as VoucherDataDig;
class VouchergrantrealtimeController extends  \Prj\ManagerCtrl {
    public function init () {
        parent::init();
    }
    protected $pageSizeEnum = [20, 30, 50];
    public function monthAction () {
        $dataDig = new \Rpt\DataDig\VoucherrealtimeDataDig();
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForMonthHandedout($where, null, true);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForMonthHandedout)),
                ['ym'=>'string']
            );
        }else {
            $ymFrom = $this->_request->get('ymFrom');
            $ymTo = $this->_request->get('ymTo');
            $whereForDay = [];
            if(!empty($ymFrom)){
                if(!\Rpt\Funcs::dateYmCheck($ymFrom)){
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $whereForDay['ymdFrom'] = $ymFrom.'-01';
            }
            if(!empty($ymTo)){
                if ( !\Rpt\Funcs::dateYmCheck($ymTo)) {
                    return $this->returnError('日期格式不正确, 格式如:2016-08');
                }
                $lastDay = \Rpt\Funcs::monthOfLastDay(strtotime($ymTo.'-01'));
                $whereForDay['ymdTo'] = $ymTo.'-'.$lastDay;
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
            $records = $dataDig->dataForMonthHandedout($where, $pager, false);
            $this->_view->assign('ymFrom', $ymFrom);
            $this->_view->assign('ymTo', $ymTo);
            $this->_view->assign(
                'fieldsMap',
                $dataDig->getTableHeader($dataDig->tableHeaderForMonthHandedout)
            );
            $this->_view->assign('records', $records);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('where',
                urlencode(json_encode($where)));
            $this->_view->assign(
                'summary',
                $dataDig->summaryForMonthHandedout($where)
            );
            $this->_view->assign(
                'whereForNexPage',
                urlencode(json_encode($whereForDay))
            );
        }
    }

    /**
     * 日报表
     */
    public function dayAction () {
        $dataDig = new VoucherDataDig();
        $isDownload = $this->_request->get('__EXCEL__')+0;
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $dataDig->dataForDayHandedout($where, null, true);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDayHandedout)),
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
            $records = $dataDig->dataForDayHandedout($where, $pager);
            $this->_view->assign('ymdFrom', $ymdFrom);
            $this->_view->assign('ymdTo', $ymdTo);
            $this->_view->assign('records', $records);
            $this->_view->assign('summary',$dataDig->summaryForDayHandedout($where));
            $this->_view->assign('fieldsMap',
                $dataDig->getTableHeader($dataDig->tableHeaderForDayHandedout));
            $this->_view->assign('where', urlencode(json_encode($where)));
            $this->_view->assign('pager', $pager);
            $this->_view->assign(
                'whereForNexPage',
                urlencode(json_encode($whereForDetail))
            );
        }
    }

    /**
     * 用户发放详情
     */
    public function detailAction () {
        $ymdFrom = trim($this->_request->get('ymdFrom'));
        $ymdTo = trim($this->_request->get('ymdTo'));
        $phone = trim($this->_request->get('phone'));
        $realname = trim($this->_request->get('realname'));
        $_isDownload = $this->_request->get('__EXCEL__');
        $where = urldecode($this->_request->get('where'));
        $arr_voucherType = [0=>'不限类型'] + \Rpt\Funcs::$voucherEnum;
        $selectedVoucherType = $this->_request->get('voucherType', 0);
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
            !empty($selectedVoucherType) && $where['type'] = $selectedVoucherType;

        }else {
            $where = json_decode(urldecode($where), true);
        }
        $dataDig = new VoucherDataDig();
        $records = $dataDig->dataForDetailHandedout($where, $pager);
        if($_isDownload){
            $records = $dataDig->dataForDetailHandedout($where);
            return $this->downExcel(
                $records,
                array_keys($dataDig->getTableHeader($dataDig->tableHeaderForDetailHandedout))
            );
        }
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $this->_view->assign('phone', $phone);
        $this->_view->assign('realname', $realname);
        $this->_view->assign('arrVoucherType', $arr_voucherType);
        $this->_view->assign('selectedVoucherType', $selectedVoucherType);
        $this->_view->assign('records', $records);
        $this->_view->assign(
            'fieldsMap',
            $dataDig->getTableHeader($dataDig->tableHeaderForDetailHandedout)
        );
        $this->_view->assign('where', urlencode(json_encode($where)));
        $this->_view->assign('pager', $pager);
    }

}