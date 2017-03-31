<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/8 0008
 * Time: 上午 9:41
 */

class RegtoinvestmenttransrateController extends \Prj\ManagerCtrl {
    public function init () {
        parent::init();

    }

    public function  indexAction () {
        if($this->ini->viewRenderType() == 'wap') {
            $this->_view->assign('_view', $this->ini->viewRenderType());
        }
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $category = ['实名转化率','绑卡转化率', '充值转化率', '购买转化率'];
        $this->_view->assign('category', json_encode($category));

        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $selectedContractId = $this->_request->get('selectedContractId');
        if ($selectedContractId === null) {
            $selectedContractId='ALLCONTRACTS';
        }
        $this->_view->assign('selectedContractId', $selectedContractId);

        $arr_contractIds = $db->getCol('db_kkrpt.tb_licai_day', 'distinct(contractId)');
        if(!empty($arr_contractIds)) {
            $arr_contractIds = $db->getPair('db_kkrpt.tb_contract_0', 'contractId', 'remarks', ['contractId'=>$arr_contractIds]);
            $arr_contractIds['ALLCONTRACTS'] = '所有渠道';
        }
        $this->_view->assign('contractIds', $arr_contractIds);
        $rs = [];
        if(empty($ymdFrom) && empty($ymdTo)) {
            $ymdFrom = date('Y-m-d', time()-7*86400);
            $ymdTo= date('Y-m-d', time()-86400);
        }

        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
            $this->_view->assign('rs', json_encode($rs));
            $this->_view->assign('legendData', []);
            $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-08');
            $this->returnError('日期格式错误, 格是如:2016-08-08');
            return;
//            $this->returnError('日期格式错误');
        }
        if (date('Ymd', strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
            $this->_view->assign('rs', json_encode($rs));
            $this->_view->assign('legendData', []);
            $this->_view->assign('errMsg', '日期范围错误');
            $this->returnError('日期范围错误');
            return;
//            $this->returnError('日期错误');
        }


        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $dtFrom = strtotime($ymdFrom);
        $ymdTo1 = date('Y-m-d', $dtFrom-86400);
        $days = (strtotime($ymdTo) - $dtFrom) / 86400;
        $ymdFrom1 = date('Y-m-d', strtotime($ymdTo1) - $days*86400);

        $where1 = [
            'ymd]'=>\Rpt\Funcs::date_format($ymdFrom1),
            'ymd['=>\Rpt\Funcs::date_format($ymdTo1)
        ];
        $where2 = [
            'ymd]'=>\Rpt\Funcs::date_format($ymdFrom),
            'ymd['=>\Rpt\Funcs::date_format($ymdTo)
        ];

        if($selectedContractId!='ALLCONTRACTS') {
            $where1['contractId']= $where2['contractId']= $selectedContractId;
        }


        $record = $db->getRecord('db_kkrpt.tb_zhuanhua',
            'sum(registerCount) as registerCount, sum(realnameCount) as realnameCount, sum(bindcardCount) as bindcardCount, sum(newRechargeCount) as newRechargeCount, sum(newBuyCount) as newBuyCount ',
            $where2);
        $key2 = date('Y年m月d日', strtotime($ymdFrom)).' - '.date('Y年m月d日', strtotime($ymdTo));

        if($record['registerCount']>0) {
            $rs[$key2]['realnameCount'] = sprintf('%.2f', ($record['realnameCount']/$record['registerCount'])*100);
            $rs[$key2]['bindcardCount'] = sprintf('%.2f', ($record['bindcardCount']/$record['registerCount'])*100);
            $rs[$key2]['newRechargeCount'] = sprintf('%.2f', ($record['newRechargeCount']/$record['registerCount'])*100);
            $rs[$key2]['newBuyCount'] = sprintf('%.2f', ($record['newBuyCount']/$record['registerCount'])*100);
        }else {
            $rs[$key2]['realnameCount'] =0;
            $rs[$key2]['bindcardCount'] = 0;
            $rs[$key2]['newRechargeCount'] = 0;
            $rs[$key2]['newBuyCount'] = 0;
        }

        $record = $db->getRecord('db_kkrpt.tb_zhuanhua',
            'sum(registerCount) as registerCount, sum(realnameCount) as realnameCount, sum(bindcardCount) as bindcardCount, sum(newRechargeCount) as newRechargeCount, sum(newBuyCount) as newBuyCount ',
            $where1);


        $key1 = date('Y年m月d日', strtotime($ymdFrom1)).' - '.date('Y年m月d日', strtotime($ymdTo1));
        if($record['registerCount']>0) {
            $rs[$key1]['realnameCount'] = sprintf('%.2f', ($record['realnameCount']/$record['registerCount'])*100);
            $rs[$key1]['bindcardCount'] = sprintf('%.2f', ($record['bindcardCount']/$record['registerCount'])*100);
            $rs[$key1]['newRechargeCount'] = sprintf('%.2f', ($record['newRechargeCount']/$record['registerCount'])*100);
            $rs[$key1]['newBuyCount'] = sprintf('%.2f', ($record['newBuyCount']/$record['registerCount'])*100);
        }else {
            $rs[$key1]['realnameCount'] =0;
            $rs[$key1]['bindcardCount'] = 0;
            $rs[$key1]['newRechargeCount'] = 0;
            $rs[$key1]['newBuyCount'] = 0;
        }


        $legendData = [$key2, $key1];


        $this->_view->assign('rs', json_encode($rs));
        $this->_view->assign('legendData', json_encode($legendData));

    }

}