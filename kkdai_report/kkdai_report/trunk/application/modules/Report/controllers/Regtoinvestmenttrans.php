<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/8 0008
 * Time: 上午 9:41
 */

class RegtoinvestmenttransController extends \Prj\ManagerCtrl {
    public function init () {
        parent::init();
        $this->_view->assign('columnNames', json_encode($this->columnMap));
    }

    private $columnMap = [
        'registerCount'     => '注册',
        'realnameCount'     => '实名',
        'bindcardCount'     => '绑卡',
        'newRechargeCount'  => '充值',
        'newBuyCount'       => '购买',
    ];
    public function  indexAction () {
        if($this->ini->viewRenderType() == 'wap') {
            $this->_view->assign('_view', $this->ini->viewRenderType());
        }
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
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
        if(empty($ymdFrom) && empty($ymdTo)) {
            $ymdFrom = date('Y-m-d', time()-7*86400);
            $ymdTo= date('Y-m-d', time()-86400);
        }

        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);

//        $rs1='';
//        $rs2='';
        if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
            $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-08');
            $this->_view->assign('rs1', []);
            $this->_view->assign('rs2', []);
            $this->_view->assign('rs2TitleText', ''); // date('Y年m月d日', strtotime($ymdFrom1)));
            $this->_view->assign('rs1TitleText', '');
            $this->_view->assign('rs2SubText', '');
            $this->_view->assign('rs1SubText', '');
            $this->returnError('日期格式错误, 格是如:2016-08-08');
            return;
        }
        if (date('Ymd', strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
            $this->_view->assign('rs1',[]);
            $this->_view->assign('rs2', []);
            $this->_view->assign('rs2TitleText', ''); // date('Y年m月d日', strtotime($ymdFrom1)));
            $this->_view->assign('rs1TitleText', '');
            $this->_view->assign('errMsg', '日期范围错误');
            $this->returnError('日期范围错误');
            return;
//            $this->returnError('日期错误');
        }

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

        $rs1 = $this->getResult($db, $where1);
        $rs2 = $this->getResult($db, $where2);
        $this->_view->assign('rs1', json_encode($rs1));
        $this->_view->assign('rs2', json_encode($rs2));
        $this->_view->assign('rs2SubText', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日')); // date('Y年m月d日', strtotime($ymdFrom1)));
        $this->_view->assign('rs1SubText', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日')); // date('Y年m月d日', strtotime($ymdFrom1)));
        $this->_view->assign('rs2TitleText', '注册至理财人数（当日注册）');
        $this->_view->assign('rs1TitleText', '注册至理财人数（当日注册）');
    }

    private function dataTrans ($records) {

    }

    private function getResult ($db, $where){
        foreach($this->columnMap as $columnName => $desc) {
            $fields [] = 'sum('.$columnName.') as '.$columnName;
        }
        $rs = $db->getRecord('db_kkrpt.tb_zhuanhua', $fields, $where);
        if(!empty($rs)) {
            $max = max($rs);
            foreach($rs as $k=> $v){
                $rs[$k] = floor(($v/$max*100));
                $rs[$k.'_real_'] = $v;
            }
            $rs['_data_rate'] = $max/100;
        }else {
            foreach($rs as $k=> $v){
                $rs[$k] = 0;
                $rs[$k.'_real_'] = 0;
            }
            $rs['_data_rate'] = 0;
        }
        return $rs;
    }
}