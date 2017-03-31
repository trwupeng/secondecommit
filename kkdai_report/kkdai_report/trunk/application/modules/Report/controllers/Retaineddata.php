<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class RetaineddataController extends \Prj\ManagerCtrl
{
            protected  $channel;
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
                $this->dbMysql = \Sooh\DB\Broker::getInstance();
                
                $channels= $this->dbMysql->getRecords('db_kkrpt.tb_liucun',' DISTINCT contractId');
                 
                foreach ($channels as $k=>$v){
                     
                    $channel= $this->dbMysql->getPair('db_kkrpt.tb_contract_0','contractId','remarks',['contractId'=>$v['contractId']]);
                    $this->channel[]=$channel;
                }
            }
            
            public function indexAction () {
             
                $ymdTo = $this->_request->get('ymdTo');
                $huanbi=['0'=>'自然周','1'=>'自然月'];
                $choosehuanbi=$this->_request->get('huanbi');
                $channel_true=$this->_request->get('select');
                
                array_unshift($this->channel,['all'=>'所有']);
                $rem=[];
                foreach ($this->channel as $v){
                    foreach ($v as $kk=>$vv){
                        $rem[$kk]=$vv;
                    }
                }

                if($this->ini->viewRenderType() == 'wap') {
                   
                    $this->_view->assign('_view', $this->ini->viewRenderType());

                    if(!\Rpt\Funcs::check_date($ymdTo) && !empty($ymdTo)){
                            $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-15');
                            $this->_view->assign('records', json_encode($records));
                            $this->_view->assign('records1', json_encode($records1));
                            $this->_view->assign('channels', $channel_true);
                            $this->_view->assign('rem', $rem);
                            $this->_view->assign('ymdTo', $ymdTo);
                            $this->_view->assign('huanbi', $huanbi);
                            $this->_view->assign('choosehuanbi', $choosehuanbi);
                            return;
                        }
                    
                    if($choosehuanbi=='自然周' ||$choosehuanbi==NULL){

                        if(empty($ymdTo)){
                            $timestrans=\Rpt\Funcs::timetrans($ymdTo);
                        
                            $ymdTo=$timestrans['last_end'];
                        }
                        $stance=\Rpt\Funcs::week($ymdTo);
                        if($stance==400){
                        
                            $this->_view->assign('errMsg', '环比自然周查询日期只能查询周日日期');
                            $this->_view->assign('records', json_encode($records));
                            $this->_view->assign('records1', json_encode($records1));
                            $this->_view->assign('channels', $channel_true);
                            $this->_view->assign('rem', $rem);
                            $this->_view->assign('ymdTo', $ymdTo);
                            $this->_view->assign('huanbi', $huanbi);
                            $this->_view->assign('choosehuanbi', $choosehuanbi);
                            return;
                        }
                        
                        $ymdTo1=date('Y-m-d',(strtotime($ymdTo)-86400*7));
                    
                    }else{
                        if(!empty($ymdTo)){
                             
                            $y=date('Y',strtotime($ymdTo));
                            $m=date('m',strtotime($ymdTo));
                            $d=date('d',strtotime($ymdTo));
                           
                            $verify=\Rpt\Funcs::verify($y,$m,$d);
                    
                            if($verify==400){
                                $this->_view->assign('errMsg', '环比自然月查询日期只能一月的最后一日');
                                $this->_view->assign('records', json_encode($records));
                                $this->_view->assign('records1', json_encode($records1));
                                $this->_view->assign('channels', $channel_true);
                                $this->_view->assign('rem', $rem);
                                $this->_view->assign('ymdTo', $ymdTo);
                                $this->_view->assign('huanbi', $huanbi);
                                $this->_view->assign('choosehuanbi', $choosehuanbi);
                                return;
                            }
                            $m1=date('m', strtotime(date('Y-m-1',strtotime($ymdTo)).' -1 month'));
                            if($m>1){
                                $y=$y;
                            }else{
                                $y=$y-1;
                            }
    
                            $To=\Rpt\Funcs::formday($m1,$y);
                            $ymdTo1=$y.'-'.$m1.'-'.$To;
                         }
                    
                    }
                }
                    
                if($choosehuanbi=='自然周' ||$choosehuanbi==NULL){
                    if(empty($ymdTo)){
                        $timestrans=\Rpt\Funcs::timetrans($ymdTo);
                
                        $ymdTo=$timestrans['last_end'];
                    }
                    $stance=\Rpt\Funcs::week($ymdTo);
                    if($stance==400){
                        $this->returnError('环比自然周查询日期只能查询周日日期');
                        return;
                    }
                     
                    $ymdTo1=date('Y-m-d',(strtotime($ymdTo)-86400*7));
                
                }else{
                    if(!empty($ymdTo)){
                         
                        $y=date('Y',strtotime($ymdTo));
                        $m=date('m',strtotime($ymdTo));
                        $d=date('d',strtotime($ymdTo));
                
                        $verify=\Rpt\Funcs::verify($y,$m,$d);
                
                        if($verify==400){
                            $this->returnError('环比自然月查询日期只能一月的最后一日');
                            return;
                        }
                        $m1=date('m', strtotime(date('Y-m-1',strtotime($ymdTo)).' -1 month'));
                        if($m>1){
                            $y=$y;
                        }else{
                            $y=$y-1;
                        }
                
                        $To=\Rpt\Funcs::formday($m1,$y);
                        $ymdTo1=$y.'-'.$m1.'-'.$To;
                         
                    }
                
                }
                
                   if(!\Rpt\Funcs::check_date($ymdTo)){
                        $this->returnError('日期选择不合法 例如：2016-11-11');
                        return;
                    }

                $dtime=$ymdTo;

                $dtime1=$ymdTo1;
                
                if($channel_true==NULL || $channel_true=='所有'){
                    
                    $where=['ymd'=>date('Ymd',strtotime($ymdTo))];
                    $where1=['ymd'=>date('Ymd',strtotime($ymdTo1))];

                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_liucun','ymd,notLicaiHasBalance,licaiNoBalance,licaiHasBalance',$where,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_liucun','ymd,notLicaiHasBalance,licaiNoBalance,licaiHasBalance',$where1,'sort ymd');
                    
                
                }else{
                    
                    $channel_id= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','contractId',['remarks'=>$channel_true]);
                    
                    $where3=['ymd'=>date('Ymd',strtotime($ymdTo)),'contractId'=>$channel_id];
                    $where4=['ymd'=>date('Ymd',strtotime($ymdTo1)),'contractId'=>$channel_id];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_liucun','ymd,notLicaiHasBalance,licaiNoBalance,licaiHasBalance',$where3,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_liucun','ymd,notLicaiHasBalance,licaiNoBalance,licaiHasBalance',$where4,'sort ymd');
                    
                }
                

                $records=[];
                $records1=[];
                
                if(empty($rs)){
                    $records[$dtime]['notLicaiHasBalance']=0;
                    $records[$dtime]['licaiNoBalance']=0;
                    $records[$dtime]['licaiHasBalance']=0;
                }else{
                     
                    foreach ($rs as $v){
                        $records[$dtime]['notLicaiHasBalance']+=$v['notLicaiHasBalance'];
                        $records[$dtime]['licaiNoBalance']+=$v['licaiNoBalance'];
                        $records[$dtime]['licaiHasBalance']+=$v['licaiHasBalance'];
                    }
                }
                
                if(empty($rs1)){
                    $records1[$dtime1]['notLicaiHasBalance']=0;
                    $records1[$dtime1]['licaiNoBalance']=0;
                    $records1[$dtime1]['licaiHasBalance']=0;
                }else{
                     
                    foreach ($rs1 as $v){
                        $records1[$dtime1]['notLicaiHasBalance']+=$v['notLicaiHasBalance'];
                        $records1[$dtime1]['licaiNoBalance']+=$v['licaiNoBalance'];
                        $records1[$dtime1]['licaiHasBalance']+=$v['licaiHasBalance'];
                    }
                }
                
               //  var_log($records,'rs#####################');
              //   var_log($records1,'rs1####################');
               
                $this->_view->assign('huanbi', $huanbi);
                $this->_view->assign('choosehuanbi', $choosehuanbi);
                
                $this->_view->assign('records', json_encode($records));
                $this->_view->assign('records1', json_encode($records1));
                
                $this->_view->assign('channels', $channel_true);
                $this->_view->assign('rem', $rem);
                
                $this->_view->assign('ymdTo', $ymdTo);
               
               
                
        }

}

