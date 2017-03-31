<?php
/**
 * 新老用户理财金额
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/9/7 0007
 * Time: 上午 11:47
 */

class OldandnewfinancialamountController extends \Prj\ManagerCtrl {
    public function init () {
        parent::init();
        $arrProductTypes = \Rpt\Funcs::product_type();
        $this->_view->assign('productTypeWithName', json_encode($arrProductTypes));
        $this->_view->assign('columnNames', json_encode($this->columnMap));
    }

    private $columnMap = [
        'amount1Buy'=> '投资1次',
        'amount5Buy'=> '投资2-5次',
        'amount6PlusBuy'=> '投资5次以上',
    ];
    public function indexAction () {

        if($this->ini->viewRenderType() == 'wap') {
            $this->_view->assign('_view', $this->ini->viewRenderType());
        }
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');

        $selectedContractId = $this->_request->get('selectedContractId');
        $this->_view->assign('selectedContractId', $selectedContractId);
        if ($selectedContractId === null) {
            $selectedContractId='ALLCONTRACTS';
        }

        if(empty($ymdFrom) && empty($ymdTo)){
            $timestrans=\Rpt\Funcs::timetrans();
            $ymdFrom=$timestrans['last_start'];
            $ymdTo=$timestrans['last_end'];

        }
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        if(!empty($ymdFrom) && !empty($ymdTo)) {
            if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                $this->_view->assign('errMsg', '日期格式错误, 格式如:2016-08-08');
                $this->_view->assign('contractIds', []);
                $this->_view->assign('rs1', []);
                $this->_view->assign('rs2', []);
                $this->_view->assign('rs2TitleText', '');
                $this->_view->assign('rs1TitleText','');
                $this->returnError('日期格式错误, 格是如:2016-08-08');
                return;
//                $this->returnError('日期格式错误');
            }
            if (date('Ymd', strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
                $this->_view->assign('errMsg', '日期范围错误');
                $this->_view->assign('contractIds', []);
                $this->_view->assign('rs1', []);
                $this->_view->assign('rs2', []);
                $this->_view->assign('rs2TitleText', '');
                $this->_view->assign('rs1TitleText','');
                $this->returnError('日期范围错误');
//                $this->returnError('日期错误');
                return;
            }


            $dtFrom = strtotime($ymdFrom);
            $ymdTo1 = date('Y-m-d', $dtFrom-86400);
            $days = (strtotime($ymdTo) - $dtFrom) / 86400;
            $ymdFrom1 = date('Y-m-d', strtotime($ymdTo1) - $days*86400);

            $where1 = [
                'ymdStart'=>\Rpt\Funcs::date_format($ymdFrom1),
                'ymdEnd'=>\Rpt\Funcs::date_format($ymdTo1)
            ];
            $where2 = [
                'ymdStart'=>\Rpt\Funcs::date_format($ymdFrom),
                'ymdEnd'=>\Rpt\Funcs::date_format($ymdTo)
            ];

            if($selectedContractId!='ALLCONTRACTS') {
                $where1['contractId']= $where2['contractId']= $selectedContractId;
            }

            $rs1 = $this->getResult($db, $where1);
            $rs2 = $this->getResult($db, $where2);
        }

        $arr_contractIds = $db->getCol('db_kkrpt.tb_licai_day', 'distinct(contractId)');
        if(!empty($arr_contractIds)) {
            $arr_contractIds = $db->getPair('db_kkrpt.tb_contract_0', 'contractId', 'remarks', ['contractId'=>$arr_contractIds]);
            $arr_contractIds['ALLCONTRACTS'] = '所有渠道';
        }


        $this->_view->assign('contractIds', $arr_contractIds);
        $this->_view->assign('rs1', json_encode($rs1));
        $this->_view->assign('rs2', json_encode($rs2));
        $this->_view->assign('rs2TitleText', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日')); // date('Y年m月d日', strtotime($ymdFrom1)));
        $this->_view->assign('rs1TitleText', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日')); // date('Y年m月d日', strtotime($ymdFrom1)));


    }

    private function getResult ($db, $where) {
        foreach($this->columnMap as $columm => $mapname) {
            $fields[] = 'round(sum('.$columm.')/100,2) as '.$columm;
        }
        $rs = $db->getAssoc('db_kkrpt.tb_licai_count','shelfId', $fields
            /*'shelfId','shelfId, sum(amountReg0Day) as sumAmountRegDay, sum(amountReg1To5) as sumAmountReg1To5, sum(amountReg6To30) as sumAmountReg6To30, sum(amountReg31Plus) as sumAmountReg31Plus'*/,$where,
            'groupby shelfId sort shelfId');
        error_log(\Sooh\DB\Broker::lastCmd());
        return $rs;
    }
}