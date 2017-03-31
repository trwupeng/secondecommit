<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class CompoundrateController extends \Prj\ManagerCtrl
{
            protected  $channel;
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
                $this->dbMysql = \Sooh\DB\Broker::getInstance();
                $channels= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day',' DISTINCT contractId');
                 
                foreach ($channels as $k=>$v){
                     
                    $channel= $this->dbMysql->getPair('db_kkrpt.tb_contract_0','contractId','remarks',['contractId'=>$v['contractId']]);
                    $this->channel[]=$channel;
                }
            
            }
            
            public function indexAction () {
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
                $channel_true=$this->_request->get('select');
                $huanbi=['0'=>'自然周','1'=>'自然月'];
                $choosehuanbi=$this->_request->get('huanbi');
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
                        $this->_view->assign('huanbi', $huanbi);
                        $this->_view->assign('choosehuanbi', $choosehuanbi);
                        $this->_view->assign('record', json_encode($record));
                        $this->_view->assign('record1', json_encode($record1));
                        $this->_view->assign('channels', $channel_true);
                        $this->_view->assign('rem', $rem);
                        $this->_view->assign('ymdTo', $ymdTo);
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
                            $this->_view->assign('huanbi', $huanbi);
                            $this->_view->assign('choosehuanbi', $choosehuanbi);
                            $this->_view->assign('record', json_encode($record));
                            $this->_view->assign('record1', json_encode($record1));
                            $this->_view->assign('channels', $channel_true);
                            $this->_view->assign('rem', $rem);
                            $this->_view->assign('ymdTo', $ymdTo);
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
                                $this->_view->assign('huanbi', $huanbi);
                                $this->_view->assign('choosehuanbi', $choosehuanbi);
                                $this->_view->assign('record', json_encode($record));
                                $this->_view->assign('record1', json_encode($record1));
                                $this->_view->assign('channels', $channel_true);
                                $this->_view->assign('rem', $rem);
                                $this->_view->assign('ymdTo', $ymdTo);
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
                    $this->returnError('日期格式错误, 格是如:2016-08-06');
                    return;
                }
                  
                $dtime=$ymdTo;
                $dtime1=$ymdTo1;
               
                if($channel_true==NULL || $channel_true=='所有'){
                    
                    $where=['ymd'=>date('Ymd',strtotime($ymdTo))];
                    $where1=['ymd'=>date('Ymd',strtotime($ymdTo1))];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_futou','ymd,n1,n2,n3,n4,n5',$where,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_futou','ymd,n1,n2,n3,n4,n5',$where1,'sort ymd');
                
                    
                }else{
                    
                    $channel_id= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','contractId',['remarks'=>$channel_true]);
                    
                    $where3=['ymd'=>date('Ymd',strtotime($ymdTo)),'contractId'=>$channel_id];
                    $where4=['ymd'=>date('Ymd',strtotime($ymdTo1)),'contractId'=>$channel_id];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_futou','ymd,n1,n2,n3,n4,n5',$where3,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_futou','ymd,n1,n2,n3,n4,n5',$where4,'sort ymd');
                    
                }

                $records=[];
                $records1=[];
                
                if(empty($rs)){
                    $records[$dtime]['n1']=0;
                    $records[$dtime]['n2']=0;
                    $records[$dtime]['n3']=0;
                    $records[$dtime]['n4']=0;
                    $records[$dtime]['n5']=0;
                }else{
                     
                    foreach ($rs as $v){
                        $records[$dtime]['n1']+=$v['n1'];
                        $records[$dtime]['n2']+=$v['n2'];
                        $records[$dtime]['n3']+=$v['n3'];
                        $records[$dtime]['n4']+=$v['n4'];
                        $records[$dtime]['n5']+=$v['n5'];
                    }
                }
                
                if(empty($rs1)){
                    $records1[$dtime1]['n1']=0;
                    $records1[$dtime1]['n2']=0;
                    $records1[$dtime1]['n3']=0;
                    $records1[$dtime1]['n4']=0;
                    $records1[$dtime1]['n5']=0;
                }else{
                     
                    foreach ($rs1 as $v){
                        $records1[$dtime1]['n1']+=$v['n1'];
                        $records1[$dtime1]['n2']+=$v['n2'];
                        $records1[$dtime1]['n3']+=$v['n3'];
                        $records1[$dtime1]['n4']+=$v['n4'];
                        $records1[$dtime1]['n5']+=$v['n5'];
                    }
                }
                
                $record=[];
                $record1=[];
                
                foreach ($records as $v){
                    
                 if(!empty($v['n1'])){
                     $record[$dtime][n1]=sprintf('%.2f', ($v['n2']/$v['n1'])*100);
                 }else{
                     $record[$dtime][n1]='0.00';
                 }
                 
                 if(!empty($v['n2'])){
                     $record[$dtime][n2]=sprintf('%.2f', ($v['n3']/$v['n2'])*100);
                 }else{
                     $record[$dtime][n2]='0.00';
                 }
                 
                 if(!empty($v['n3'])){
                     $record[$dtime][n3]=sprintf('%.2f', ($v['n4']/$v['n3'])*100);
                 }else{
                     $record[$dtime][n3]='0.00';
                 }
                 
                 if(!empty($v['n4'])){
                     $record[$dtime][n4]=sprintf('%.2f', ($v['n5']/$v['n4'])*100);
                 }else{
                     $record[$dtime][n4]='0.00';
                 }
                    
                }
                
                
                foreach ($records1 as $v){
                
                    if(!empty($v['n1'])){
                        $record1[$dtime1][n1]=sprintf('%.2f', ($v['n2']/$v['n1'])*100);
                    }else{
                        $record1[$dtime1][n1]='0.00';
                    }
                     
                    if(!empty($v['n2'])){
                        $record1[$dtime1][n2]=sprintf('%.2f', ($v['n3']/$v['n2'])*100);
                    }else{
                        $record1[$dtime1][n2]='0.00';
                    }
                     
                    if(!empty($v['n3'])){
                        $record1[$dtime1][n3]=sprintf('%.2f', ($v['n4']/$v['n3'])*100);
                    }else{
                        $record1[$dtime1][n3]='0.00';
                    }
                     
                    if(!empty($v['n4'])){
                        $record1[$dtime1][n4]=sprintf('%.2f', ($v['n5']/$v['n4'])*100);
                    }else{
                        $record1[$dtime1][n4]='0.00';
                    }
                
                }
                
                $this->_view->assign('huanbi', $huanbi);
                $this->_view->assign('choosehuanbi', $choosehuanbi);
                
                $this->_view->assign('record', json_encode($record));
                $this->_view->assign('record1', json_encode($record1));
                
                $this->_view->assign('channels', $channel_true);
                $this->_view->assign('rem', $rem);
                
                $this->_view->assign('ymdTo', $ymdTo);
               
                
        }

}

