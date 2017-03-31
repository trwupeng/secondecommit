<?php
/**
 *好友返现/好友返现详情
 *
 * @author wu.peng
 * @param time 2016/9/21
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class CashbackController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];
            
            public function indexAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                $ymdFrom = date('Y-m').'-'.'01';
                $ymdTo = date('Y-m-d');
                $wheretrue=$this->_request->get('where');
                $detailswheretrue=$this->_request->get('detailswhere');
                var_log($detailswheretrue,'one>>>>>>>>>>');
                
              
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
                //var_log($where,'where>>>>');
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'ymd'=>['日期',null],
                    'cashbackAmount'=>['返现总金额',null],
                ];
                
                $detailsfielsdMap=[
                    'customer_id'=>['客户编号',null],
                    'customer_realname'=>['姓名',null],
                    'customer_phone'=>['手机号',null],
                    'create_time'=>['返现时间',null],
                    'amount'=>['返现金额',null],
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                $detailsheaders = [];
                foreach($detailsfielsdMap as $r) {
                    $detailsheaders[$r[0]] = $r[1];
                }
                
                
                if($isDownloadExcel){
                    if(!empty($wheretrue)){
                        $sql2="
                        SELECT create_time,amount/100 as amount from recharge_enchashment_water
                        WHERE trade_type = '3018'
                        AND create_time>='{$wheretrue['ymdForm']}'
                        AND create_time<='{$wheretrue['ymdTo[']}'
                        and `status`=1
                        ORDER BY create_time desc
                        ";
                        
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                     
                        foreach ($sql2 as $v){
                            $dtime=date('Y-m-d',strtotime($v['create_time']));
                            $v['create_time']=$dtime;
                            $amount=substr($v['amount'],0,-2);
                            $v['amount']=$amount;
                            $rs[]=$v;
                        }
                        
                        foreach ($rs as $v){
                             
                            $ret[$v['create_time']]+=$v['amount'];
                        }
                         
                        foreach ($ret as $k=>$v){
                            $v= sprintf("%.2f", $v);
                            $result[]=[
                                'ymd'=>$k,
                                'amount'=>$v,
                            ];
                        } 
                    }elseif(!empty($detailswheretrue)){
                        
                        foreach ($detailswheretrue as $v){
                            $detailsForm=$v.' 00:00:00';
                            $detailsTo=$v.' 23:59:59';
                            
                            $sql1="SELECT
                            customer_id,
                            customer_name,
                            customer_realname,
                            customer_cellphone,
                            amount / 100 as amount,
                            create_time
                            FROM
                            recharge_enchashment_water AS a
                            LEFT JOIN customer AS b USING (customer_id)
                            WHERE
                            trade_type = '3018'
                            AND create_time >= '{$detailsForm}'
                            AND create_time <= '{$detailsTo}'
                            AND `status` = 1
                            ORDER BY
                            create_time DESC
                            ";
                            
                            $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                            $sql1=$db_p2p->fetchAssocThenFree($sql1);
                            
                            foreach ($sql1 as $v){
                                $dtime=date('Y-m-d',strtotime($v['create_time']));
                                $v['create_time']=$dtime;
                            
                                $detailsresult[]=[
                                    'customer_id'=>$v['customer_id'],
                                    'customer_realname'=> $v['customer_realname'],
                                    'customer_cellphone'=>$v['customer_cellphone'],
                                    'create_time'=>$v['create_time'],
                                    'amount'=>sprintf("%.2f",$v['amount']),
                                ];
                            }
                        }
                       
                    }
                   
                     
                  
                }else{
                    
                    
                    $sql1="
                            SELECT create_time,amount/100 as amount from recharge_enchashment_water 
                            WHERE trade_type = '3018'
                            AND create_time>='{$where['ymdForm]']}'
                            AND create_time<='{$where['ymdTo[']}'
                            and `status`=1
                            ORDER BY create_time desc
                           ";
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    //var_log($sql1,'rs###################');
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['create_time']));
                        $v['create_time']=$dtime;
                        $amount=substr($v['amount'],0,-2);
                        $v['amount']=$amount;
                        $rs[]=$v;
                    }

                    foreach ($rs as $v){
                         
                        $ret[$v['create_time']]+=$v['amount'];
                    }
                    
                    $pager->init(Count($ret), $pageid);
                    $rsform=$pager->rsFrom();
                    //var_log($pager,'rs###################');
                    
                    $ret=array_slice($ret,$rsform,$pagesize);
                    
                    foreach ($ret as $k=>$v){
                        $detailswhere[]=$k;
                        
                        $amount= sprintf("%.2f", $v);
                        $amountall+=$amount;
                        
                        $result[]=[
                            'ymd'=>$k,
                             'amount'=>$amount,
                        ];
                        
                    }
                   
                }

                if($isDownloadExcel){
                    if(!empty($result)){
                        return $this->downExcel($result, array_keys($headers));
                    }elseif(!empty($detailsresult)){
                        return $this->downExcel($detailsresult, array_keys($detailsheaders));
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
            
            var_log($where,'where>>>>');
            $db_p2p=\Sooh\DB\Broker::getInstance('produce');
            $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
            
            $fielsdMap=[
                'customer_id'=>['客户编号',null],
                'customer_realname'=>['姓名',null],
                'customer_phone'=>['手机号',null],
                'create_time'=>['返现时间',null],
                'amount'=>['返现金额',null],
            ];
            
            $headers = [];
            foreach($fielsdMap as $r) {
                $headers[$r[0]] = $r[1];
            }
            
            if($isDownloadExcel){
               
                if(empty($details)){
                
              
                    $sql1="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$wheretrue['ymdForm']}'
                        AND create_time <= '{$wheretrue['ymdTo[']}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                        ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['create_time']));
                        $v['create_time']=$dtime;
                    
                        $result[]=[
                             'customer_id'=>$v['customer_id'],
                            'customer_realname'=> $v['customer_realname'],
                            'customer_cellphone'=>$v['customer_cellphone'],
                            'create_time'=>$v['create_time'],
                            'amount'=>sprintf("%.2f",$v['amount']),
                        ];
                    }
                }else{

                    $sql1="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$detailsForm}'
                        AND create_time <= '{$detailsTo}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                    ";
                
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                
                    foreach ($sql1 as $v){
                      $dtime=date('Y-m-d',strtotime($v['create_time']));
                        $v['create_time']=$dtime;
                    
                        $result[]=[
                             'customer_id'=>$v['customer_id'],
                            'customer_realname'=> $v['customer_realname'],
                            'customer_cellphone'=>$v['customer_cellphone'],
                            'create_time'=>$v['create_time'],
                            'amount'=>sprintf("%.2f",$v['amount']),
                        ];
                    }
                }
            }else{
                if(empty($details)){
                    
                    
                    
                    
                    
                    $sql="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$where['ymdForm]']}'
                        AND create_time <= '{$where['ymdTo[']}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                    ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                    var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$where['ymdForm]']}'
                        AND create_time <= '{$where['ymdTo[']}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['create_time']));
                        $v['create_time']=$dtime;
                    
                        $result[]=[
                             'customer_id'=>$v['customer_id'],
                            'customer_realname'=> $v['customer_realname'],
                            'customer_cellphone'=>$v['customer_cellphone'],
                            'create_time'=>$v['create_time'],
                            'amount'=>sprintf("%.2f",$v['amount']),
                        ];
                    } 
                }else{
                    
                    $sql="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$detailsForm}'
                        AND create_time <= '{$detailsTo}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                          ";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $pager->init(Count($sql), $pageid);
                    var_log($pager,'rs###################');
                    
                    $rsform=$pager->rsFrom();
                    
                    $sql1="SELECT
                        	customer_id,
                        	customer_name,
                        	customer_realname,
                        	customer_cellphone,
                        	amount / 100 as amount,
                        	create_time
                        FROM
                        	recharge_enchashment_water AS a
                        LEFT JOIN customer AS b USING (customer_id)
                        WHERE
                        	trade_type = '3018'
                        AND create_time >= '{$detailsForm}'
                        AND create_time <= '{$detailsTo}'
                        AND `status` = 1
                        ORDER BY
                        	create_time DESC
                    "." limit ".$rsform.",".$pagesize;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                    
                    foreach ($sql1 as $v){
                        $dtime=date('Y-m-d',strtotime($v['create_time']));
                        $v['create_time']=$dtime;
                        
                        $result[]=[
                            //'ymd'=>$v['payment_date'],
                            'customer_id'=>$v['customer_id'],
                            'customer_realname'=> $v['customer_realname'],
                            'customer_cellphone'=>$v['customer_cellphone'],
                            'create_time'=>$v['create_time'],
                            'amount'=>sprintf("%.2f",$v['amount']),
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