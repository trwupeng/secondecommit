<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class UmengdataController extends \Prj\ManagerCtrl
{
            protected  $channel;
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            public function indexAction () {
                
                $this->dbMysql = \Sooh\DB\Broker::getInstance();
                $channels= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data',' DISTINCT channels');
                foreach ($channels as $k=>$v){
                    $this->channel[]=$v['channels'];
                }
                
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
                $channel=$this->_request->get('select');
                array_unshift($this->channel,'所有');
                
                $timestrans=\Rpt\Funcs::timetrans();
                if(empty($ymdFrom)){
                    $ymdFrom=$timestrans['last_start'];
                }
                 
                if(empty($ymdTo)){
                    $ymdTo=$timestrans['last_end'];
                }
                
                if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                    $ymdFrom=$ymdFrom;
                    $ymdTo=$ymdTo;
                }else{
                    $ymdFrom = date('Y-m-d',strtotime($ymdFrom));
                    $ymdTo = date('Y-m-d',strtotime($ymdTo));
                }
               
                $stance=(strtotime($ymdTo)-strtotime($ymdFrom))/86400;
                if($stance==0){
                    $ymdTo1=date('Y-m-d',(strtotime($ymdFrom)-86400));
                    $ymdFrom1=date('Y-m-d',(strtotime($ymdTo1)-$stance));
                }else{
                    $ymdTo1=date('Y-m-d',(strtotime($ymdFrom)-86400));
                    $ymdFrom1=date('Y-m-d',strtotime($ymdTo1) - $stance*86400);
                }
                
                if($this->ini->viewRenderType() == 'wap') {
                    $this->_view->assign('_view', $this->ini->viewRenderType());
                    if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                        $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-15');
                        $this->_view->assign('rem', json_encode($rem));
                        $this->_view->assign('rem1', json_encode($rem1));
                        $this->_view->assign('records', json_encode($records));
                        $this->_view->assign('max', $max_10);
                        $this->_view->assign('channel', $this->channel);
                        $this->_view->assign('channels', $channel);
                        $this->_view->assign('ymdFrom', $ymdFrom);
                        $this->_view->assign('ymdTo', $ymdTo);
                        $this->_view->assign('header', $header);
                        $this->_view->assign('dtime', json_encode($dtime));
                        $this->_view->assign('dtime1', json_encode($dtime1));
                        return;
                    }
                    if(strtotime($ymdFrom)>strtotime($ymdTo)){
                        $this->_view->assign('errMsg','日期范围错误');
                        $this->_view->assign('rem', json_encode($rem));
                        $this->_view->assign('rem1', json_encode($rem1));
                        $this->_view->assign('records', json_encode($records));
                        $this->_view->assign('max', $max_10);
                        $this->_view->assign('channel', $this->channel);
                        $this->_view->assign('channels', $channel);
                        $this->_view->assign('ymdFrom', $ymdFrom);
                        $this->_view->assign('ymdTo', $ymdTo);
                        $this->_view->assign('header', $header);
                        $this->_view->assign('dtime', json_encode($dtime));
                        $this->_view->assign('dtime1', json_encode($dtime1));
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

             
               //var_log($tt,'1111>>>>');
               $fieldsMapArr=[
                   '0' => [
                       '协议名',
                       '50'
                   ],
                   
                   '1' => [
                       '激活用户数(默认展示所有渠道激活用户数的前10名)',
                       '50'
                   ],
                  
               ];
               $header = [];
               foreach ($fieldsMapArr as $k => $v) {
                   $header[$v[0]] = $v[1];
               }
               
              // var_log($ymdTo1,'1111111');
              // var_log($ymdFrom1,'22222');
               
               if($channel==NULL || $channel=='所有'){
                   
                   $where=['ymd]'=>$ymdFrom,
                       'ymd['=>$ymdTo,
                   ];
                   
                   $where1=[
                       'ymd]'=>$ymdFrom1,
                       'ymd['=>$ymdTo1,
                   ];
                   
                   
                   $rs= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','*',$where,'sort ymd');

                   $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','*',$where1,'sort ymd');
                 
                   $ret= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','channels,new_user',$where);
                   
               }else{
                   $where2=[
                       'ymd]'=>$ymdFrom,
                       'ymd['=>$ymdTo,
                       'channels'=>$channel,
                   ];
                   
                   $where3=[
                       'ymd]'=>$ymdFrom1,
                       'ymd['=>$ymdTo1,
                       'channels'=>$channel,
                   ];
         
                   $rs= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','*',$where2,'sort ymd');
                   
                   $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','*',$where3,'sort ymd');
                   
                   $ret= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','channels,new_user',$where2);
        
               }
             // var_log(\Sooh\DB\Broker::lastCmd(),'r1111111############>>>>>>>>>>');
              // var_log($rs,'rs##################');
             //  var_log($rs1,'rs111111##############');
               
              
               
               $arrnew = array();
               foreach($ret as $val){
                   $arrnew[$val['channels']] += $val['new_user'];
               
               }
               
               arsort($arrnew);
               $max_10=array_slice($arrnew,0, 10);
               
              // var_log($max_10,'RS##########>>>>>>>>>>');
               
              $records=[];
               foreach ($rs as $v){
                   if($v['clientType']=='901'){
                       $records[$v['ymd']][901]['new_user']+=$v['new_user'];
                       $records[$v['ymd']][901]['active_user']+=$v['active_user'];
                       $records[$v['ymd']][901]['launches_user']+=$v['launches_user'];
                   }else{
                       $records[$v['ymd']][902]['new_user']+=$v['new_user'];
                       $records[$v['ymd']][902]['active_user']+=$v['active_user'];
                       $records[$v['ymd']][902]['launches_user']+=$v['launches_user'];
                   }
               }
             
               $records1=[];
               foreach ($rs1 as $v){
                   if($v['clientType']=='901'){
                       $records1[$v['ymd']][901]['new_user']+=$v['new_user'];
                       $records1[$v['ymd']][901]['active_user']+=$v['active_user'];
                       $records1[$v['ymd']][901]['launches_user']+=$v['launches_user'];
                   }else{
                       $records1[$v['ymd']][902]['new_user']+=$v['new_user'];
                       $records1[$v['ymd']][902]['active_user']+=$v['active_user'];
                       $records1[$v['ymd']][902]['launches_user']+=$v['launches_user'];
                   }
               }
               
               //var_log($records,'>>>>>>>>>>');
             
                $dtime=$ymdFrom.'--'.$ymdTo;
                $dtime1= $ymdFrom1.'--'.$ymdTo1;
         
                foreach ($records as $k=>$v){
                    
                    $rem[$dtime][901]['new_user']+=$v[901]['new_user'];
                    $rem[$dtime][901]['active_user']+=$v[901]['active_user'];
                    $rem[$dtime][901]['launches_user']+=$v[901]['launches_user'];
                    $rem[$dtime][902]['new_user']+=$v[902]['new_user'];
                    $rem[$dtime][902]['active_user']+=$v[902]['active_user'];
                    $rem[$dtime][902]['launches_user']+=$v[902]['launches_user'];
                }
                
                foreach ($records1 as $k=>$v){
                
                    $rem1[$dtime1][901]['new_user']+=$v[901]['new_user'];
                    $rem1[$dtime1][901]['active_user']+=$v[901]['active_user'];
                    $rem1[$dtime1][901]['launches_user']+=$v[901]['launches_user'];
                    $rem1[$dtime1][902]['new_user']+=$v[902]['new_user'];
                    $rem1[$dtime1][902]['active_user']+=$v[902]['active_user'];
                    $rem1[$dtime1][902]['launches_user']+=$v[902]['launches_user'];
                }
               
                if(empty($rem)){
                    $rem[$dtime][901]['new_user']=0;
                    $rem[$dtime][901]['active_user']=0;
                    $rem[$dtime][901]['launches_user']=0;
                    $rem[$dtime][902]['new_user']=0;
                    $rem[$dtime][902]['active_user']=0;
                    $rem[$dtime][902]['launches_user']=0;
                }else{
                    $rem=$rem;
                }
                
              if(empty($rem1)){
                    $rem1[$dtime1][901]['new_user']=0;
                    $rem1[$dtime1][901]['active_user']=0;
                    $rem1[$dtime1][901]['launches_user']=0;
                    $rem1[$dtime1][902]['new_user']=0;
                    $rem1[$dtime1][902]['active_user']=0;
                    $rem1[$dtime1][902]['launches_user']=0;
                }else{
                    $rem1=$rem1;
                }
              
              //var_log($rem,'r1111111############>>>>>>>>>>');
          
             // var_log($rem1,'r22222222222###########>>>>>>>>>>');
          
                $this->_view->assign('rem', json_encode($rem));
                $this->_view->assign('rem1', json_encode($rem1));
                $this->_view->assign('records', json_encode($records));
                $this->_view->assign('max', $max_10);
                $this->_view->assign('channel', $this->channel);
                $this->_view->assign('channels', $channel);
                $this->_view->assign('ymdFrom', $ymdFrom);
                $this->_view->assign('ymdTo', $ymdTo);
                $this->_view->assign('header', $header);
                $this->_view->assign('dtime', json_encode($dtime));
                $this->_view->assign('dtime1', json_encode($dtime1));
               
              
        }
}