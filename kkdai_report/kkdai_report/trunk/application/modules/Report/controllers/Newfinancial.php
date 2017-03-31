<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class NewfinancialController extends \Prj\ManagerCtrl
{
            protected  $channel;
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
                $this->dbMysql = \Sooh\DB\Broker::getInstance();
                $channels= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day',' DISTINCT contractId');
                //var_log($channels,'channels#############');
                foreach ($channels as $k=>$v){
                  //  $this->channel[]=$v['channels'];
                    $channel= $this->dbMysql->getPair('db_kkrpt.tb_contract_0','contractId','remarks',['contractId'=>$v['contractId']]);
                    $this->channel[]=$channel;
                }
         
            }
            
            public function indexAction () {
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
                $channel_true=$this->_request->get('select');
         
              var_log($channel_true,'cha#############');
              
              $timestrans=\Rpt\Funcs::timetrans();
              
              array_unshift($this->channel,['all'=>'所有']);
              $rem=[];
               foreach ($this->channel as $v){
                   foreach ($v as $kk=>$vv){
                       $rem[$kk]=$vv;
                   }
               }
               
               
               $fieldsMapArr=[
                   '0' => [
                       '协议名',
                       '50'
                   ],
                    
                   '1' => [
                       '新增房宝宝人数(默认展示所有协议新增房宝宝人数的前10名)',
                       '50'
                   ],
               
               ];
               $header = [];
               foreach ($fieldsMapArr as $k => $v) {
                   $header[$v[0]] = $v[1];
               }
               
               if(empty($ymdFrom)){
                    $ymdFrom=$timestrans['last_start'];
                }else{
                    $ymdFrom=date('Y-m-d',(strtotime($ymdFrom)));
                }
                 
                if(empty($ymdTo)){
                    $ymdTo=$timestrans['last_end'];
                }else{
                    $ymdTo=date('Y-m-d',(strtotime($ymdTo)));
                }
               
               $stance=(strtotime($ymdTo)-strtotime($ymdFrom))/86400;
               if($stance==0){
                   $ymdTo1=date('Ymd',(strtotime($ymdFrom)-86400));
                   $ymdFrom1=date('Ymd',(strtotime($ymdTo1)-$stance));
               }else{
                   $ymdTo1=date('Ymd',(strtotime($ymdFrom)-86400));
                   $ymdFrom1=date('Ymd',strtotime($ymdTo1) - $stance*86400);
               }
               
               if($this->ini->viewRenderType() == 'wap') {
                   $this->_view->assign('_view', $this->ini->viewRenderType());
                   if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                       $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-15');
                       $this->_view->assign('records', json_encode($record));
                       $this->_view->assign('records1', json_encode($record1));
                       $this->_view->assign('ymdFrom', $ymdFrom);
                       $this->_view->assign('ymdTo', $ymdTo);
                       $this->_view->assign('dtime', json_encode($dtime));
                       $this->_view->assign('dtime1', json_encode($dtime1));
                       $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                       $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                       $this->_view->assign('channels', $channel_true);
                       $this->_view->assign('rem', $rem);
                       $this->_view->assign('header', $header);
                       $this->_view->assign('max', $max_10);
                       return;
                   }
                   if(strtotime($ymdFrom)>strtotime($ymdTo)){
                       $this->_view->assign('errMsg','日期范围错误');
                       $this->_view->assign('records', json_encode($record));
                       $this->_view->assign('records1', json_encode($record1));
                       $this->_view->assign('ymdFrom', $ymdFrom);
                       $this->_view->assign('ymdTo', $ymdTo);
                       $this->_view->assign('dtime', json_encode($dtime));
                       $this->_view->assign('dtime1', json_encode($dtime1));
                       $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                       $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                       $this->_view->assign('channels', $channel_true);
                       $this->_view->assign('rem', $rem);
                       $this->_view->assign('header', $header);
                       $this->_view->assign('max', $max_10);
                       return;
                   }
               }
              
                 if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                        $this->returnError('日期格式错误, 格是如:2016-08-06');
                        return;
                    }
                    if(strtotime($ymdFrom)>strtotime($ymdTo)){
                        $this->returnError('日期范围错误');
                        return;
                    } 
               
            
               
               
               $d1=date('m-d',strtotime($ymdFrom));
               $d2=date('m-d',strtotime($ymdTo));
               $dtime=$d1.'--'.$d2;
               
               $d3=date('m-d',strtotime($ymdFrom1));
               $d4=date('m-d',strtotime($ymdTo1));
               $dtime1=$d3.'--'.$d4;
               
               $d11=date('Y-m-d',strtotime($ymdFrom));
               $d22=date('Y-m-d',strtotime($ymdTo));
               $dtime2=$d11.'--'.$d22;
               
               $d33=date('Y-m-d',strtotime($ymdFrom1));
               $d44=date('Y-m-d',strtotime($ymdTo1));
               $dtime3=$d33.'--'.$d44;
               
               
               if($channel_true==NULL || $channel_true=='所有'){

                   $where=['ymd]'=>date('Ymd',strtotime($ymdFrom)),
                       'ymd['=>date('Ymd',strtotime($ymdTo)),
                   ];
                   
                   $where1=['ymd]'=>$ymdFrom1,
                       'ymd['=>$ymdTo1,
                   ];
                   
                   $where2=['ymd]'=>date('Ymd',strtotime($ymdFrom)),
                       'ymd['=>date('Ymd',strtotime($ymdTo)),
                       'shelfId'=>2,
                   ];
                    
                   
                   $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where,'sort ymd');
                   
                   $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where1,'sort ymd');
                   
                   $ret=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day','contractId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where2);
                   
                   
               }else{
                   
                   $channel_id= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','contractId',['remarks'=>$channel_true]);
                   
                   $where3=['ymd]'=>date('Ymd',strtotime($ymdFrom)),
                       'ymd['=>date('Ymd',strtotime($ymdTo)),
                       'contractId'=>$channel_id,
                   ];
                   
                   $where4=['ymd]'=>$ymdFrom1,
                       'ymd['=>$ymdTo1,
                       'contractId'=>$channel_id,
                   ];
                   
                   $where5=['ymd]'=>date('Ymd',strtotime($ymdFrom)),
                       'ymd['=>date('Ymd',strtotime($ymdTo)),
                       'shelfId'=>2,
                       'contractId'=>$channel_id,
                   ];
                   $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where3,'sort ymd');
                   
                   $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where4,'sort ymd');
                   
                   $ret=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day','contractId,countReg0Day,countReg1To5,countReg6To30,countReg31Plus',$where5);
                   
               }
              
               // var_log($ret,'rs####################');
              
              $result=[];
              foreach ($ret as $v){
                  
                  $channel= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','remarks',['contractId'=>$v['contractId']]);
                  
                  $result[$channel]+=$v['countReg0Day']+$v['countReg1To5']+$v['countReg6To30']+$v['countReg31Plus'];
                  
              }
               
             
              arsort($result);
              //var_log($result,'result######################');
              $max_10=array_slice($result,0, 10);
            
               $records=[];
               $records1=[];
               
               if(empty($rs)){
                   $records[$dtime]['dqb']['countReg0Day']=0;
                   $records[$dtime]['dqb']['countReg1To5']=0;
                   $records[$dtime]['dqb']['countReg6To30']=0;
                   $records[$dtime]['dqb']['countReg31Plus']=0;
                   $records[$dtime]['fbb']['countReg0Day']=0;
                   $records[$dtime]['fbb']['countReg1To5']=0;
                   $records[$dtime]['fbb']['countReg6To30']=0;
                   $records[$dtime]['fbb']['countReg31Plus']=0;
                   $records[$dtime]['jyb']['countReg0Day']=0;
                   $records[$dtime]['jyb']['countReg1To5']=0;
                   $records[$dtime]['jyb']['countReg6To30']=0;
                   $records[$dtime]['jyb']['countReg31Plus']=0;
               }else{
               foreach ($rs as $v){
                   //array_key_exists("Volvo",$a)
                   if(($v['shelfId']==1)==true){
                      $records[$dtime]['dqb']['countReg0Day']+=$v['countReg0Day'];
                      $records[$dtime]['dqb']['countReg1To5']+=$v['countReg1To5'];
                      $records[$dtime]['dqb']['countReg6To30']+=$v['countReg6To30'];
                      $records[$dtime]['dqb']['countReg31Plus']+=$v['countReg31Plus'];
                   }

                   
                   elseif (($v['shelfId']==2)==true){
                       $records[$dtime]['fbb']['countReg0Day']+=$v['countReg0Day'];
                       $records[$dtime]['fbb']['countReg1To5']+=$v['countReg1To5'];
                       $records[$dtime]['fbb']['countReg6To30']+=$v['countReg6To30'];
                       $records[$dtime]['fbb']['countReg31Plus']+=$v['countReg31Plus'];
                   }

                   
                   elseif(($v['shelfId']==5)==true){
                       $records[$dtime]['jyb']['countReg0Day']+=$v['countReg0Day'];
                       $records[$dtime]['jyb']['countReg1To5']+=$v['countReg1To5'];
                       $records[$dtime]['jyb']['countReg6To30']+=$v['countReg6To30'];
                       $records[$dtime]['jyb']['countReg31Plus']+=$v['countReg31Plus'];
                   }
                   
               }
               }
               
               if(empty($rs1)){
                   $records1[$dtime1]['dqb']['countReg0Day']=0;
                   $records1[$dtime1]['dqb']['countReg1To5']=0;
                   $records1[$dtime1]['dqb']['countReg6To30']=0;
                   $records1[$dtime1]['dqb']['countReg31Plus']=0;
                   $records1[$dtime1]['fbb']['countReg0Day']=0;
                   $records1[$dtime1]['fbb']['countReg1To5']=0;
                   $records1[$dtime1]['fbb']['countReg6To30']=0;
                   $records1[$dtime1]['fbb']['countReg31Plus']=0;
                   $records1[$dtime1]['jyb']['countReg0Day']=0;
                   $records1[$dtime1]['jyb']['countReg1To5']=0;
                   $records1[$dtime1]['jyb']['countReg6To30']=0;
                   $records1[$dtime1]['jyb']['countReg31Plus']=0;
               }else{
               foreach ($rs1 as $v){
                   

                   
                   if(($v['shelfId']==1)==true){
                      // var_log('good1#################');
                       $records1[$dtime1]['dqb']['countReg0Day']+=$v['countReg0Day'];
                       $records1[$dtime1]['dqb']['countReg1To5']+=$v['countReg1To5'];
                       $records1[$dtime1]['dqb']['countReg6To30']+=$v['countReg6To30'];
                       $records1[$dtime1]['dqb']['countReg31Plus']+=$v['countReg31Plus'];
                   }

                   elseif (($v['shelfId']==2)==true){
                      // var_log('good2#################');
                       $records1[$dtime1]['fbb']['countReg0Day']+=$v['countReg0Day'];
                       $records1[$dtime1]['fbb']['countReg1To5']+=$v['countReg1To5'];
                       $records1[$dtime1]['fbb']['countReg6To30']+=$v['countReg6To30'];
                       $records1[$dtime1]['fbb']['countReg31Plus']+=$v['countReg31Plus'];
                   }

                   
                   elseif(($v['shelfId']==5)==true){
                       //var_log('good5#################');
                       $records1[$dtime1]['jyb']['countReg0Day']+=$v['countReg0Day'];
                       $records1[$dtime1]['jyb']['countReg1To5']+=$v['countReg1To5'];
                       $records1[$dtime1]['jyb']['countReg6To30']+=$v['countReg6To30'];
                       $records1[$dtime1]['jyb']['countReg31Plus']+=$v['countReg31Plus'];
                   }

               }
               }
               
               //array_key_exists($key, $array)
               $key='dqb';
               $key1='fbb';
               $key2='jyb';
               $record=[];
               $record1=[];
               foreach ($records as $k=>$v){
               if(array_key_exists($key, $v)){
                  // var_log('dqb####################');
               }else{
                   $v[$key]=[
                       "countReg0Day"=>0,
                       'countReg1To5'=>0,
                       'countReg6To30'=>0,
                       'countReg31Plus'=>0
                   ];
                   
               }
               if(array_key_exists($key1, $v)){
                   //var_log('fbb####################');
               }else{
                   $v[$key1]=[
                       "countReg0Day"=>0,
                       'countReg1To5'=>0,
                       'countReg6To30'=>0,
                       'countReg31Plus'=>0
                   ];
                  
               }
               if(array_key_exists($key2, $v)){
                   //var_log('jyb####################');
               }else{
                   $v[$key2]=[
                       "countReg0Day"=>0,
                       'countReg1To5'=>0,
                       'countReg6To30'=>0,
                       'countReg31Plus'=>0
                   ];
                 
               }
               
               $record[$k]=$v;
               }
               
               foreach ($records1 as $k=>$v){
                   
                   if(array_key_exists($key, $v)){
                       
                   }else{
                       $v[$key]=[
                           "countReg0Day"=>0,
                           'countReg1To5'=>0,
                           'countReg6To30'=>0,
                           'countReg31Plus'=>0
                       ];
                       
                   }
                   if(array_key_exists($key1, $v)){
                       
                   }else{
                       $v[$key1]=[
                           "countReg0Day"=>0,
                           'countReg1To5'=>0,
                           'countReg6To30'=>0,
                           'countReg31Plus'=>0
                       ];
                       
                   }
                   if(array_key_exists($key2, $v)){
                      // var_log('jyb####################');
                   }else{
                       $v[$key2]=[
                           "countReg0Day"=>0,
                           'countReg1To5'=>0,
                           'countReg6To30'=>0,
                           'countReg31Plus'=>0
                       ];
                     
                   }
                    
                   $record1[$k]=$v;
               }
              //var_log($record1,'records#######################');
              //var_log($records1,'records11111111#######################');
               
               
               
                $this->_view->assign('records', json_encode($record));
                $this->_view->assign('records1', json_encode($record1));
                $this->_view->assign('ymdFrom', $ymdFrom);
                $this->_view->assign('ymdTo', $ymdTo);
                $this->_view->assign('dtime', json_encode($dtime));
                $this->_view->assign('dtime1', json_encode($dtime1));
                $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                $this->_view->assign('channels', $channel_true);
                $this->_view->assign('rem', $rem);
                $this->_view->assign('header', $header);
                $this->_view->assign('max', $max_10);
               
                
        }
        


}

