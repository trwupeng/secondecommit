<?php
/**
 *管理费/管理费详情
 *
 * @author wu.peng
 * @param time 2016/9/21
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class ServicechargeController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [20,50,100];
            
            public function indexAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                
                $ymdFromDefault='2014-07-11';
                $ymdToDefault=date('Y-m-d');
               // $ymdFrom='2014-07-11';
              
               // $ymdTo=date('Y-m-d');
                $wheretrue=$this->_request->get('where');
                $detailswheretrue=$this->_request->get('detailswhere');
               // var_log($wheretrue,'one>>>>>>>>>>');
                
              
                $form = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
               
                $form ->addItem('_ymdForm_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
                ->addItem('_ymdTo_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
                ->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);
                
                $form->fillValues();
                $where = $form->getWhere();
                if(empty($where['ymdForm]'])) {
                    $where['ymdForm]'] = $ymdFromDefault.' 00:00:00';
                }
                if(empty($where['ymdTo['])){
                    $where['ymdTo['] = $ymdToDefault.' 23:59:59';
                }
                
                if($where['ymdTo[']< $where['ymdForm]']) {
                    return $this->returnError('起始日期应该大于结束日期');
                }

               // var_log($where,'where>>>>');
                
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'ymd'=>['日期',null],
                    'servicecharge'=>['管理费',null],
                ];
                
                $fielsdMap01=[
                'ymd'=>['日期',null],
                'bid_title'=>['标的名称',null],
                'bid_amount'=>['标的金额',null],
                'bid_unit'=>['标的期限',null],
                'service_recharge'=>['管理费',null],
                ];
                
                
                $headers01 = [];
                foreach($fielsdMap01 as $r) {
                    $headers01[$r[0]] = $r[1];
                }
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                if($isDownloadExcel){
                    if(!empty($wheretrue)){
                        $sql2="SELECT payment_date,SUM(service_charge)/100 as service_charge FROM `account_bill`
                        WHERE payment_date>='{$wheretrue['ymdForm']}'
                        and payment_date<='{$wheretrue['ymdTo[']}'
                        GROUP BY payment_date
                        ORDER BY payment_date DESC
                        ";
                        
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                        //var_log($sql1,'rs###################');
                        
                        foreach ($sql2 as $v){
                            $dtime=date('Y-m-d',strtotime($v['payment_date']));
                            $v['payment_date']=$dtime;
                            $service_charge=substr($v['service_charge'],0,-2);
                            $v['service_charge']=$service_charge;
                            $rs[]=$v;
                        }
                        
                        foreach ($rs as $v){
                             
                            $ret[$v['payment_date']]+=$v['service_charge'];
                        }
                         
                        foreach ($ret as $k=>$v){
                            $v= sprintf("%.2f", $v);
                             
                            $result[]=[
                                'ymd'=>$k,
                                'servicecharge'=>$v,
                            ];
                        }
                         
                    }elseif(!empty($detailswheretrue)){
              
                            $sql1="SELECT bid_id,payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                            USING(bid_id)
                            WHERE payment_date>='{$detailswheretrue['ymdForm']}'
                            and payment_date<='{$detailswheretrue['ymdTo']}'
                            ";
                            
                            $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                            $sql1=$db_p2p->fetchAssocThenFree($sql1);
                            
                            foreach ($sql1 as $v){
                                $dtime=date('Y-m-d',strtotime($v['payment_date']));
                                $v['payment_date']=$dtime;
                                $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                                $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                                $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                                $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                            
                                $result01[]=[
                                    'ymd'=>$v['payment_date'],
                                    'bid_title'=>$v['bid_title'],
                                    'bid_amount'=> sprintf("%.2f",$v['bid_amount']/100),
                                    'bid_unit'=>$v['bid_period'],
                                    'service_recharge'=>sprintf("%.2f",$v['service_charge']/100),
                                ];
                            } 
                        } 
                  
                }else{
                     
                    $detailswhere=['ymdForm'=>$where['ymdForm]'],'ymdTo'=>$where['ymdTo[']];
                    
                    
                    $sql_all="SELECT SUM(service_charge)/100 as service_charge FROM `account_bill`
                    WHERE payment_date>='{$where['ymdForm]']}'
                    and payment_date<='{$where['ymdTo[']}'
                    ";
                    
                    $sql_all=$db_p2p->execCustom(['sql'=>$sql_all]);
                    $sql_all=$db_p2p->fetchAssocThenFree($sql_all);
                    $amountall=$sql_all[0]['service_charge'];
                    
                  
                    $sql1="SELECT payment_date,SUM(service_charge)/100 as service_charge FROM `account_bill`
                          WHERE payment_date>='{$where['ymdForm]']}'
                          and payment_date<='{$where['ymdTo[']}'
                          GROUP BY payment_date
                          ORDER BY payment_date DESC";
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['payment_date']));
                        $v['payment_date']=$dtime;
                        $service_charge=substr($v['service_charge'],0,-2);
                        $v['service_charge']=$service_charge;
                        $rs[]=$v;
                    }

                    foreach ($rs as $v){
                         
                        $ret[$v['payment_date']]+=$v['service_charge'];
                    }
                    
                    $pager->init(Count($ret), $pageid);
                    $rsform=$pager->rsFrom();
                    $ret=array_slice($ret, $rsform,$pagesize);
                    
                    foreach ($ret as $k=>$v){

                        $amount= sprintf("%.2f", $v);
                         
                        $result[]=[
                            'ymd'=>$k,
                             'servicecharge'=>$amount,
                        ];
                    }
                   
                }
               
            //  var_log($rs,'rs###################');
              
                if($isDownloadExcel){
                    if(!empty($result)){
                        return $this->downExcel($result, array_keys($headers));
                    }elseif(!empty($result01)){
                        return $this->downExcel($result01, array_keys($headers01));
                    }
                
                }
                
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $result);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('where', $where); 
                $this->_view->assign('amount', $amountall);
                $this->_view->assign('detailswhere', $detailswhere);
        }
        
        public  function detailsAction(){
            
            $details = $this->_request->get('_pkey_val_');
            $detailsForm=$details.' 00:00:00';
            $detailsTo=$details.' 23:59:59';

           // var_log($details,'ddddddddddddd######');
            
            
            $isDownloadExcel = $this->_request->get('__EXCEL__');
            
            $pageid = $this->_request->get('pageId',1)-0;
            $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
            $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
            $dt_instance = \Sooh\Base\Time::getInstance();
            $ymdFrom = date('Y-m-d',strtotime($detailsForm));
            $ymdTo = date('Y-m-d',strtotime($detailsTo));
            $wheretrue=$this->_request->get('where');
          
            // var_log($ymdOne,'one>>>>>>>>>>');
            
            
            $form = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
             
            $form ->addItem('_ymdForm_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
            ->addItem('_ymdTo_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
            ->addItem('pageid', $pageid)
            ->addItem('pagesize', $pager->page_size);
            
            $form->fillValues();
            $where = $form->getWhere();
            if($where['ymdForm]']) {
                $where['ymdForm]'] = date('Y-m-d', strtotime($where['ymdForm]']));
                $where['ymdForm]']=$where['ymdForm]'].' 00:00:00';
            }
            if($where['ymdTo[']){
                $where['ymdTo['] = date('Y-m-d', strtotime($where['ymdTo[']));
                $where['ymdTo[']=$where['ymdTo['].' 23:59:59';
            }
            if($where['ymdTo[']< $where['ymdForm]']) {
                return $this->returnError('起始日期应该大于结束日期');
            }
           // var_log($where,'where>>>>');
            $db_p2p=\Sooh\DB\Broker::getInstance('produce');
            $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
            
            $fielsdMap=[
                'ymd'=>['日期',null],
                'bid_title'=>['标的名称',null],
                'bid_amount'=>['标的金额',null],
                'bid_unit'=>['标的期限',null],
                'service_recharge'=>['管理费',null],
            ];
            
            $headers = [];
            foreach($fielsdMap as $r) {
                $headers[$r[0]] = $r[1];
            }
            
            if($isDownloadExcel){
               
                if(empty($details)){
                
              
                    $sql1="SELECT bid_id,payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                    USING(bid_id)
                    WHERE payment_date>='{$wheretrue['ymdForm']}'
                    and payment_date<='{$wheretrue['ymdTo[']}'
                    order by payment_date desc
                    ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['payment_date']));
                        $v['payment_date']=$dtime;
                        $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                        $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                        $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                        $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                        
                        $result[]=[
                            'ymd'=>$v['payment_date'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=> sprintf("%.2f",$v['bid_amount']/100),
                            'bid_unit'=>$v['bid_period'],
                            'service_recharge'=>sprintf("%.2f",$v['service_charge']/100),
                        ];
                    }
                }else{

                    $sql1="SELECT bid_id,payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                    USING(bid_id)
                    WHERE payment_date>='{$detailsForm}'
                    and payment_date<='{$detailsTo}'
                    ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['payment_date']));
                        $v['payment_date']=$dtime;
                        $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                        $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                        $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                        $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                        
                        $result[]=[
                            'ymd'=>$v['payment_date'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=> sprintf("%.2f",$v['bid_amount']/100),
                            'bid_unit'=>$v['bid_period'],
                            'service_recharge'=>sprintf("%.2f",$v['service_charge']/100),
                        ];
                    }
                }
            }else{
                if(empty($details)){
                    
                    $sql="SELECT payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                    USING(bid_id)
                    WHERE payment_date>='{$where['ymdForm]']}'
                    and payment_date<='{$where['ymdTo[']}'
                    ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                    //var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT bid_id,payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                    USING(bid_id)
                    WHERE payment_date>='{$where['ymdForm]']}'
                    and payment_date<='{$where['ymdTo[']}'
                    order by payment_date desc
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['payment_date']));
                        $v['payment_date']=$dtime;
                        $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                        $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                        $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                        $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                        
                        $result[]=[
                            'ymd'=>$v['payment_date'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=> sprintf("%.2f",$v['bid_amount']/100),
                            'bid_unit'=>$v['bid_period'],
                            'service_recharge'=>sprintf("%.2f",$v['service_charge']/100),
                        ];
                    } 
                }else{
                    
                    $sql="SELECT payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                          USING(bid_id)
                          WHERE payment_date>='{$detailsForm}'
                          and payment_date<='{$detailsTo}'
                          ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                   // var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT bid_id,payment_date,bid_title,bid_period,bid_amount,service_charge FROM `account_bill` as a LEFT JOIN bid as b
                    USING(bid_id)
                    WHERE payment_date>='{$detailsForm}'
                    and payment_date<='{$detailsTo}'
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['payment_date']));
                        $v['payment_date']=$dtime;
                        $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                        $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                        $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                        $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                        
                        $result[]=[
                            'ymd'=>$v['payment_date'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=> sprintf("%.2f",$v['bid_amount']/100),
                            'bid_unit'=>$v['bid_period'],
                            'service_recharge'=>sprintf("%.2f",$v['service_charge']/100),
                        ];
                    }
                    
                }
            }
             
            //  var_log($rs,'rs###################');
            
            if($isDownloadExcel){
                return $this->downExcel($result, array_keys($headers));
            }
            
            $this->_view->assign('headers', $headers);
            $this->_view->assign('records', $result);
            $this->_view->assign('pager', $pager);
            $this->_view->assign('where', $where);
            
        }
        
}