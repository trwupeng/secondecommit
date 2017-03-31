<?php
/**
 *服务费/服务费详情
 *
 * @author wu.peng
 * @param time 2016/9/21
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class ServicefeeController extends \Prj\ManagerCtrl
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
                
                $ymdFromDefault='2015-05-11';
                 
                $ymdToDefault=date('Y-m-d');
                
                $wheretrue=$this->_request->get('where');
                $detailswheretrue=$this->_request->get('detailswhere');

                
              
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
                //var_log($where,'where>>>>');
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'ymd'=>['日期',null],
                    'servicefee'=>['服务费',null],
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                $fielsdMap01=[
                    // 'ymd'=>['日期',null],
                    'customer_realname'=>['借款人名称',null],
                    'create_time'=>['收费时间',null],
                    'bid_title'=>['标的名称',null],
                    'bid_amount'=>['标的金额',null],
                    'bid_interest'=>['标的利率',null],
                    'bid_period'=>['标的期限',null],
                    'bid_credit_level'=>['信用级别',null],
                    'bid_serviceFee'=>['服务费',null],
                ];
                
                $headers01 = [];
                foreach($fielsdMap01 as $r) {
                    $headers01[$r[0]] = $r[1];
                }
                
                if($isDownloadExcel){
                    if(!empty($wheretrue)){
                        $sql2="
                        SELECT full_time,bid_serviceFee/100 as bid_serviceFee from bid
                        WHERE bid_status=5004
                        and full_time>='{$wheretrue['ymdForm']}'
                        and full_time<='{$wheretrue['ymdTo[']}'
                        ORDER BY full_time DESC
                        ";
                        
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                        
                        foreach ($sql2 as $v){
                            $dtime=date('Y-m-d',strtotime($v['full_time']));
                            $v['full_time']=$dtime;
                            $amount=substr($v['bid_serviceFee'],0,-2);
                            $v['bid_serviceFee']=$amount;
                            $rs[]=$v;
                        }
                        
                        foreach ($rs as $v){
                             
                            $ret[$v['full_time']]+=$v['bid_serviceFee'];
                        }
                         
                        foreach ($ret as $k=>$v){
                            $v= sprintf("%.2f", $v);
                             
                            $result[]=[
                                'ymd'=>$k,
                                'bid_serviceFee'=>$v,
                            ];
                        }
                    }elseif(!empty($detailswheretrue)){
                       
                            
                            $sql1="SELECT
                            *
                            FROM
                            `bid` AS a
                            LEFT JOIN fangkuan AS b USING (bid_id)
                            WHERE
                            bid_status = 5004
                            AND full_time >='{$detailswheretrue['ymdForm']}'
                            AND full_time <='{$detailswheretrue['ymdTo']}'
                            ORDER BY
                            create_time DESC
                            ";
                            
                            $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                            $sql1=$db_p2p->fetchAssocThenFree($sql1);
                            
                            foreach ($sql1 as $v){
                                $sqlrealname="SELECT DISTINCT
                                customer_realname
                                FROM
                                customer
                                WHERE
                                customer_id = '{$v['customer_id']}'";
                                 
                                $sqlture=$db_p2p->execCustom(['sql'=>$sqlrealname]);
                                $customer_realname=$db_p2p->fetchAssocThenFree($sqlture);
                                 
                                $v['bid_interest']=sprintf("%.2f",$v['bid_interest']/100);
                                $v['bid_interest']=$v['bid_interest'].'%';
                                 
                                $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                                $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                                $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                                $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                                 
                                $result01[]=[
                                     
                                    'customer_realname'=>$customer_realname[0]['customer_realname'],
                                    'create_time'=> $v['create_time'],
                                    'bid_title'=>$v['bid_title'],
                                    'bid_amount'=>sprintf("%.2f",$v['bid_amount']/100),
                                    'bid_interest'=>$v['bid_interest'],
                                    'bid_period'=>$v['bid_period'],
                                    'bid_credit_level'=>$v['bid_credit_level'],
                                    'bid_serviceFee'=>sprintf("%.2f",$v['bid_serviceFee']/100),
                                ];
                            }
                        }

                }else{
                    
                    $detailswhere=['ymdForm'=>$where['ymdForm]'],'ymdTo'=>$where['ymdTo[']];
                    
                    $sql_all="
                    SELECT sum(bid_serviceFee/100) as bid_serviceFee from bid
                    WHERE bid_status=5004
                    and full_time>='{$where['ymdForm]']}'
                    and full_time<='{$where['ymdTo[']}'
                    ";
                    
                    $sql_all=$db_p2p->execCustom(['sql'=>$sql_all]);
                    $sql_all=$db_p2p->fetchAssocThenFree($sql_all);
                    $amountall=sprintf("%.2f",$sql_all[0]['bid_serviceFee']);
 
                    $sql1="
                           SELECT full_time,sum(bid_serviceFee/100) as bid_serviceFee from bid
                            WHERE bid_status=5004
                            and full_time>='{$where['ymdForm]']}'
                            and full_time<='{$where['ymdTo[']}'
                            GROUP BY full_time
                            ORDER BY full_time DESC
                           ";
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['full_time']));
                        $v['full_time']=$dtime;
                        $bid_serviceFee=substr($v['bid_serviceFee'],0,-2);
                        $v['bid_serviceFee']=$bid_serviceFee;
                        $rs[]=$v;
                    }

                    foreach ($rs as $v){
                         
                        $ret[$v['full_time']]+=$v['bid_serviceFee'];
                    }
                    
                    $pager->init(Count($ret), $pageid);
                    $rsform=$pager->rsFrom();
                    $ret=array_slice($ret, $rsform,$pagesize);
                    
                    foreach ($ret as $k=>$v){
                       
                        $amount= sprintf("%.2f", $v);
                         
                        $result[]=[
                            'ymd'=>$k,
                             'bid_serviceFee'=>$amount,
                        ];
                    }
                   
                }
               
              //var_log($rs,'rs###################');
              
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

            //var_log($details,'ddddddddddddd######');
            
            
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
          //  var_log($where,'where>>>>');
            $db_p2p=\Sooh\DB\Broker::getInstance('produce');
            $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
            
            $fielsdMap=[
               // 'ymd'=>['日期',null],
                'customer_realname'=>['借款人名称',null],
                'create_time'=>['收费时间',null],
                'bid_title'=>['标的名称',null],
                'bid_amount'=>['标的金额',null],
                'bid_interest'=>['标的利率',null],
                'bid_period'=>['标的期限',null],
                'bid_credit_level'=>['信用级别',null],
                'bid_serviceFee'=>['服务费',null],
            ];
            
            $headers = [];
            foreach($fielsdMap as $r) {
                $headers[$r[0]] = $r[1];
            }
            
            if($isDownloadExcel){
             
                if(empty($details)){
                
              
                    $sql1="SELECT
                           *
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$wheretrue['ymdForm']}'
                        AND full_time <= '{$wheretrue['ymdTo[']}'
                        ORDER BY
                        	create_time DESC
                        ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                         $sqlrealname="SELECT DISTINCT
                                	customer_realname
                                FROM
                                customer 
                                WHERE
                                	customer_id = '{$v['customer_id']}'";
                     
                       $sqlture=$db_p2p->execCustom(['sql'=>$sqlrealname]);
                       $customer_realname=$db_p2p->fetchAssocThenFree($sqlture);
                     
                       $v['bid_interest']=sprintf("%.2f",$v['bid_interest']/100);
                       $v['bid_interest']=$v['bid_interest'].'%';
                       
                       $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                       $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                       $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                       $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                       
                        $result[]=[
                            
                            'customer_realname'=>$customer_realname[0]['customer_realname'],
                            'create_time'=> $v['create_time'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=>sprintf("%.2f",$v['bid_amount']/100),
                            'bid_interest'=>$v['bid_interest'],
                            'bid_period'=>$v['bid_period'],
                            'bid_credit_level'=>$v['bid_credit_level'],
                            'bid_serviceFee'=>sprintf("%.2f",$v['bid_serviceFee']/100),
                        ];
                    }
                }else{

                    $sql1="SELECT
                           *
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$detailsForm}'
                        AND full_time <= '{$detailsTo}'
                        ORDER BY
                        	create_time DESC
                    ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                          $sqlrealname="SELECT DISTINCT
                                	customer_realname
                                FROM	
                                customer 
                                WHERE
                                	customer_id = '{$v['customer_id']}'";
                     
                       $sqlture=$db_p2p->execCustom(['sql'=>$sqlrealname]);
                       $customer_realname=$db_p2p->fetchAssocThenFree($sqlture);
                     
                       $v['bid_interest']=sprintf("%.2f",$v['bid_interest']/100);
                       $v['bid_interest']=$v['bid_interest'].'%';
                       
                       $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                       $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                       $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                       $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                       
                        $result[]=[
                         
                            'customer_realname'=>$customer_realname[0]['customer_realname'],
                            'create_time'=> $v['create_time'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=>sprintf("%.2f",$v['bid_amount']/100),
                            'bid_interest'=>$v['bid_interest'],
                            'bid_period'=>$v['bid_period'],
                            'bid_credit_level'=>$v['bid_credit_level'],
                            'bid_serviceFee'=>sprintf("%.2f",$v['bid_serviceFee']/100),
                        ];
                    }
                }
            }else{
                if(empty($details)){
                    
                    $sql="SELECT
                        	*
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$where['ymdForm]']}'
                        AND full_time <= '{$where['ymdTo[']}'
                        ORDER BY
                        	create_time DESC
                    ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                    //var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT
                           *
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$where['ymdForm]']}'
                        AND full_time <= '{$where['ymdTo[']}'
                        ORDER BY
                        	create_time DESC
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                      
                     $sqlrealname="SELECT DISTINCT
                                	customer_realname
                                FROM
                                customer
                                WHERE
                                	customer_id = '{$v['customer_id']}'";
                     
                       $sqlture=$db_p2p->execCustom(['sql'=>$sqlrealname]);
                       $customer_realname=$db_p2p->fetchAssocThenFree($sqlture);
                      // var_log($sqlture,'true#####################');
                       $v['bid_interest']=sprintf("%.2f",$v['bid_interest']/100);
                       $v['bid_interest']=$v['bid_interest'].'%';
                       
                       $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                       $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                       $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                       $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                       
                        $result[]=[
                            //'ymd'=>$v['payment_date'],
                            'customer_realname'=>$customer_realname[0]['customer_realname'],
                            'create_time'=> $v['create_time'],//sprintf("%.2f",$v['bid_amount']/100),
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=>sprintf("%.2f",$v['bid_amount']/100),
                            'bid_interest'=>$v['bid_interest'],
                            'bid_period'=>$v['bid_period'],
                            'bid_credit_level'=>$v['bid_credit_level'],
                            'bid_serviceFee'=>sprintf("%.2f",$v['bid_serviceFee']/100),
                        ];
                    } 
                }else{
                    
                    $sql="SELECT
                        	*
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$detailsForm}'
                        AND full_time <= '{$detailsTo}'
                        ORDER BY
                        	create_time DESC
                          ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                   // var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT
                           *
                        FROM
                        	`bid` AS a
                        LEFT JOIN fangkuan AS b USING (bid_id)
                        WHERE
                        	bid_status = 5004
                        AND full_time >= '{$detailsForm}'
                        AND full_time <= '{$detailsTo}'
                        ORDER BY
                        	create_time DESC
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                     $sqlrealname="SELECT DISTINCT
                                	customer_realname
                                FROM	
                                customer 
                                WHERE
                                	customer_id = '{$v['customer_id']}'";
                     
                       $sqlture=$db_p2p->execCustom(['sql'=>$sqlrealname]);
                       $customer_realname=$db_p2p->fetchAssocThenFree($sqlture);
                       //var_log($customer_realname,'true#####################');
                       $v['bid_interest']=sprintf("%.2f",$v['bid_interest']/100);
                       $v['bid_interest']=$v['bid_interest'].'%';
                      // var_log( $v['bid_interest'],'true#####################');
                       $sqltrue="SELECT dlUnit FROM `tb_products_final` where waresId='{$v['bid_id']}'";
                       $sqltrue=$db_rpt->execCustom(['sql'=>$sqltrue]);
                       $sqltrue=$db_rpt->fetchAssocThenFree($sqltrue);
                       $v['bid_period']=$v['bid_period'].$sqltrue[0]['dlUnit'];
                       
                        $result[]=[
                            'customer_realname'=>$customer_realname[0]['customer_realname'],
                            'create_time'=> $v['create_time'],
                            'bid_title'=>$v['bid_title'],
                            'bid_amount'=>sprintf("%.2f",$v['bid_amount']/100),
                            'bid_interest'=>$v['bid_interest'],
                            'bid_period'=>$v['bid_period'],
                            'bid_credit_level'=>$v['bid_credit_level'],
                            'bid_serviceFee'=>sprintf("%.2f",$v['bid_serviceFee']/100),
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