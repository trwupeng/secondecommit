<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class NewfinancialavgController extends \Prj\ManagerCtrl
{
           
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
                //var_log($channel_true,'chanlll>>>>>>>>>>>');
                
                $timestrans=\Rpt\Funcs::timetrans();
                
                array_unshift($this->channel,['all'=>'所有']);
                $rem=[];
                foreach ($this->channel as $v){
                    foreach ($v as $kk=>$vv){
                        $rem[$kk]=$vv;
                    }
                }
                
                $fieldsMapArr=[
                    '0' => ['协议名', '50' ],
                
                    '1' => ['新增房宝宝人数(默认展示所有协议新增房宝宝人数的前10名)','50'],
                     
                ];
                
                $header = [];
                foreach ($fieldsMapArr as $k => $v) {
                    $header[$v[0]] = $v[1];
                }
                 
                 
                if(empty($ymdFrom)){
                   // $ymdFrom=date('Y-m-d',time()-7*84600);
                    // $ymdFrom='20160823';
                    $ymdFrom=$timestrans['last_start'];
                }
                 
                if(empty($ymdTo)){
                   // $ymdTo=date('Y-m-d',time()-84600);
                    // $ymdTo='20160824';
                    $ymdTo=$timestrans['last_end'];
                }
                if($this->ini->viewRenderType() == 'wap') {
                    $this->_view->assign('_view', $this->ini->viewRenderType());
                    if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                        $this->_view->assign('errMsg', '日期格式错误, 格是如:2016-08-15');
                        $this->_view->assign('record', json_encode($record));
                        $this->_view->assign('record1', json_encode($record1));
                        $this->_view->assign('dtime', json_encode($dtime));
                        $this->_view->assign('dtime1', json_encode($dtime1));
                        $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                        $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                        $this->_view->assign('channels', $channel_true);
                        $this->_view->assign('rem', $rem);
                        $this->_view->assign('ymdFrom', $ymdFrom);
                        $this->_view->assign('ymdTo', $ymdTo);
                        return;
                    }
                    if(strtotime($ymdFrom)>strtotime($ymdTo)){
                        $this->_view->assign('errMsg','日期范围错误');
                        $this->_view->assign('record', json_encode($record));
                        $this->_view->assign('record1', json_encode($record1));
                        $this->_view->assign('dtime', json_encode($dtime));
                        $this->_view->assign('dtime1', json_encode($dtime1));
                        $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                        $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                        $this->_view->assign('channels', $channel_true);
                        $this->_view->assign('rem', $rem);
                        $this->_view->assign('ymdFrom', $ymdFrom);
                        $this->_view->assign('ymdTo', $ymdTo);
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
                 
                $stance=(strtotime($ymdTo)-strtotime($ymdFrom))/86400;
                if($stance==0){
                    $ymdTo1=date('Ymd',(strtotime($ymdFrom)-86400));
                    $ymdFrom1=date('Ymd',(strtotime($ymdTo1)-$stance));
                }else{
                    $ymdTo1=date('Ymd',(strtotime($ymdFrom)-86400));
                    $ymdFrom1=date('Ymd',strtotime($ymdTo1) - $stance*86400);
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
                    
                    $where=['ymd]'=>date('Ymd',strtotime($ymdFrom)),'ymd['=>date('Ymd',strtotime($ymdTo))];
                     
                    $where1=['ymd]'=>$ymdFrom1,'ymd['=>$ymdTo1];

                    $rs=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day',
                    'ymd,shelfId,amountReg0Day,countReg0Day,amountReg1To5,countReg1To5,
                        amountReg6To30,countReg6To30,amountReg31Plus,countReg31Plus
                        ',$where,'sort ymd');
                    
                    $rs1=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day',
                        'ymd,shelfId,amountReg0Day,countReg0Day,amountReg1To5,countReg1To5,
                        amountReg6To30,countReg6To30,amountReg31Plus,countReg31Plus
                        ',$where1,'sort ymd');
                    
                   // $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,avgAmountReg0Day,avgAmountReg1To5,avgAmountReg6To30,avgAmountReg31Plus',$where,'sort ymd');
                    
                   // $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,avgAmountReg0Day,avgAmountReg1To5,avgAmountReg6To30,avgAmountReg31Plus',$where1,'sort ymd');
                    
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
                    
                    $rs=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day',
                        'ymd,shelfId,amountReg0Day,countReg0Day,amountReg1To5,countReg1To5,
                        amountReg6To30,countReg6To30,amountReg31Plus,countReg31Plus
                        ',$where3,'sort ymd');
                    
                    $rs1=$this->dbMysql->getRecords('db_kkrpt.tb_licai_day',
                        'ymd,shelfId,amountReg0Day,countReg0Day,amountReg1To5,countReg1To5,
                        amountReg6To30,countReg6To30,amountReg31Plus,countReg31Plus
                        ',$where4,'sort ymd');
                    
                    
                   // $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,avgAmountReg0Day,avgAmountReg1To5,avgAmountReg6To30,avgAmountReg31Plus',$where3,'sort ymd');
                    
                   //$rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_day','ymd,shelfId,avgAmountReg0Day,avgAmountReg1To5,avgAmountReg6To30,avgAmountReg31Plus',$where4,'sort ymd');
                }
               
                
               // var_log($rs,'rs#####################');
              //  var_log($rs1,'rs1#####################');
                
                $records=[];
                $records1=[];
                 
                if(empty($rs)){
                    $records[$dtime]['dqb']['avgAmountReg0Day']=0;
                    $records[$dtime]['dqb']['avgAmountReg1To5']=0;
                    $records[$dtime]['dqb']['avgAmountReg6To30']=0;
                    $records[$dtime]['dqb']['avgAmountReg31Plus']=0;
                    
                    $records[$dtime]['fbb']['avgAmountReg0Day']=0;
                    $records[$dtime]['fbb']['avgAmountReg1To5']=0;
                    $records[$dtime]['fbb']['avgAmountReg6To30']=0;
                    $records[$dtime]['fbb']['avgAmountReg31Plus']=0;
                    
                    $records[$dtime]['jyb']['avgAmountReg0Day']=0;
                    $records[$dtime]['jyb']['avgAmountReg1To5']=0;
                    $records[$dtime]['jyb']['avgAmountReg6To30']=0;
                    $records[$dtime]['jyb']['avgAmountReg31Plus']=0;
                }else{
                    foreach ($rs as $v){
                      
                        if(($v['shelfId']==1)==true){
                            $records[$dtime]['dqb']['countReg0Day']+=$v['countReg0Day'];
                            $records[$dtime]['dqb']['countReg1To5']+=$v['countReg1To5'];
                            $records[$dtime]['dqb']['countReg6To30']+=$v['countReg6To30'];
                            $records[$dtime]['dqb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records[$dtime]['dqb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records[$dtime]['dqb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records[$dtime]['dqb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records[$dtime]['dqb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
                     
                         
                        if (($v['shelfId']==2)==true){
                            $records[$dtime]['fbb']['countReg0Day']+=$v['countReg0Day'];
                            $records[$dtime]['fbb']['countReg1To5']+=$v['countReg1To5'];
                            $records[$dtime]['fbb']['countReg6To30']+=$v['countReg6To30'];
                            $records[$dtime]['fbb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records[$dtime]['fbb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records[$dtime]['fbb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records[$dtime]['fbb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records[$dtime]['fbb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
                     
                         
                        if(($v['shelfId']==5)==true){
                            $records[$dtime]['jyb']['countReg0Day']+=$v['countReg0Day'];
                            $records[$dtime]['jyb']['countReg1To5']+=$v['countReg1To5'];
                            $records[$dtime]['jyb']['countReg6To30']+=$v['countReg6To30'];
                            $records[$dtime]['jyb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records[$dtime]['jyb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records[$dtime]['jyb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records[$dtime]['jyb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records[$dtime]['jyb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
 
                    }
                }
                 
                if(empty($rs1)){
                    $records1[$dtime1]['dqb']['avgAmountReg0Day']=0;
                    $records1[$dtime1]['dqb']['avgAmountReg1To5']=0;
                    $records1[$dtime1]['dqb']['avgAmountReg6To30']=0;
                    $records1[$dtime1]['dqb']['avgAmountReg31Plus']=0;
                    
                    $records1[$dtime1]['fbb']['avgAmountReg0Day']=0;
                    $records1[$dtime1]['fbb']['avgAmountReg1To5']=0;
                    $records1[$dtime1]['fbb']['avgAmountReg6To30']=0;
                    $records1[$dtime1]['fbb']['avgAmountReg31Plus']=0;
                    
                    $records1[$dtime1]['jyb']['avgAmountReg0Day']=0;
                    $records1[$dtime1]['jyb']['avgAmountReg1To5']=0;
                    $records1[$dtime1]['jyb']['avgAmountReg6To30']=0;
                    $records1[$dtime1]['jyb']['avgAmountReg31Plus']=0;
                }else{
                    foreach ($rs1 as $v){
                       
                        if(($v['shelfId']==1)==true){
                            $records1[$dtime1]['dqb']['countReg0Day']+=$v['countReg0Day'];
                            $records1[$dtime1]['dqb']['countReg1To5']+=$v['countReg1To5'];
                            $records1[$dtime1]['dqb']['countReg6To30']+=$v['countReg6To30'];
                            $records1[$dtime1]['dqb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records1[$dtime1]['dqb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records1[$dtime1]['dqb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records1[$dtime1]['dqb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records1[$dtime1]['dqb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
                        
                         
                        if (($v['shelfId']==2)==true){
                          
                            $records1[$dtime1]['fbb']['countReg0Day']+=$v['countReg0Day'];
                            $records1[$dtime1]['fbb']['countReg1To5']+=$v['countReg1To5'];
                            $records1[$dtime1]['fbb']['countReg6To30']+=$v['countReg6To30'];
                            $records1[$dtime1]['fbb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records1[$dtime1]['fbb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records1[$dtime1]['fbb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records1[$dtime1]['fbb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records1[$dtime1]['fbb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
                      
                         
                        if(($v['shelfId']==5)==true){
                            
                            $records1[$dtime1]['jyb']['countReg0Day']+=$v['countReg0Day'];
                            $records1[$dtime1]['jyb']['countReg1To5']+=$v['countReg1To5'];
                            $records1[$dtime1]['jyb']['countReg6To30']+=$v['countReg6To30'];
                            $records1[$dtime1]['jyb']['countReg31Plus']+=$v['countReg31Plus'];
                            $records1[$dtime1]['jyb']['amountReg0Day']+=$v['amountReg0Day'];
                            $records1[$dtime1]['jyb']['amountReg1To5']+=$v['amountReg1To5'];
                            $records1[$dtime1]['jyb']['amountReg6To30']+=$v['amountReg6To30'];
                            $records1[$dtime1]['jyb']['amountReg31Plus']+=$v['amountReg31Plus'];
                        }
                       
                    }
                }
                
               $key='dqb';
               $key1='fbb';
               $key2='jyb';
                
               $result=[];
               $result1=[];
               foreach ($records as $v){
                   if(array_key_exists($key, $v)){
                 
                   if($v['dqb']['countReg0Day']==0){
                   $result[$dtime]['dqb']['avgAmountReg0Day']=0;
                   }else{
                   $result[$dtime]['dqb']['avgAmountReg0Day']=($v['dqb']['amountReg0Day'])/($v['dqb']['countReg0Day']);
                   }
                   if($v['dqb']['countReg1To5']==0){
                   $result[$dtime]['dqb']['avgAmountReg1To5']=0;
                   }else{
                   $result[$dtime]['dqb']['avgAmountReg1To5']=($v['dqb']['amountReg1To5'])/($v['dqb']['countReg1To5']);
                   }
                   
                   if($v['dqb']['countReg6To30']==0){
                   $result[$dtime]['dqb']['avgAmountReg6To30']=0;
                   }else{
                   $result[$dtime]['dqb']['avgAmountReg6To30']=($v['dqb']['amountReg6To30'])/($v['dqb']['countReg6To30']);
                   }
                   
                   if($v['dqb']['countReg31Plus']==0){
                   $result[$dtime]['dqb']['avgAmountReg31Plus']=0;
                   }else{
                   $result[$dtime]['dqb']['avgAmountReg31Plus']=($v['dqb']['amountReg31Plus'])/($v['dqb']['countReg31Plus']);
                   }
                  
                   }else{
                        $result[$dtime]['dqb']['avgAmountReg0Day']=0;
                        $result[$dtime]['dqb']['avgAmountReg1To5']=0;
                        $result[$dtime]['dqb']['avgAmountReg6To30']=0;
                        $result[$dtime]['dqb']['avgAmountReg31Plus']=0;
                        
                   }
                   
                   if(array_key_exists($key1, $v)){
             
                   if($v['fbb']['countReg0Day']==0){
                   $result[$dtime]['fbb']['avgAmountReg0Day']=0;
                   }else{
                   $result[$dtime]['fbb']['avgAmountReg0Day']=($v['fbb']['amountReg0Day'])/($v['fbb']['countReg0Day']);
                   }
                   
                   if($v['fbb']['countReg1To5']==0){
                   $result[$dtime]['fbb']['avgAmountReg1To5']=0;
                   }else{
                   $result[$dtime]['fbb']['avgAmountReg1To5']=($v['fbb']['amountReg1To5'])/($v['fbb']['countReg1To5']);
                   }
                   
                   if($v['fbb']['countReg6To30']==0){
                   $result[$dtime]['fbb']['avgAmountReg6To30']=0;
                   }else{
                   $result[$dtime]['fbb']['avgAmountReg6To30']=($v['fbb']['amountReg6To30'])/($v['fbb']['countReg6To30']);
                   }
                   
                   if($v['fbb']['countReg31Plus']==0){
                   $result[$dtime]['fbb']['avgAmountReg31Plus']=0;
                   }else{

                   $result[$dtime]['fbb']['avgAmountReg31Plus']=($v['fbb']['amountReg31Plus'])/($v['fbb']['countReg31Plus']);
                   }

                   }else{
                        $result[$dtime]['fbb']['avgAmountReg0Day']=0;
                        $result[$dtime]['fbb']['avgAmountReg1To5']=0;
                        $result[$dtime]['fbb']['avgAmountReg6To30']=0;
                        $result[$dtime]['fbb']['avgAmountReg31Plus']=0;
                   }
                   
                   if(array_key_exists($key2, $v)){
                       
                   if($v['jyb']['countReg0Day']==0){
                       $result[$dtime]['jyb']['avgAmountReg0Day']=0;
                   }else{
                        $result[$dtime]['jyb']['avgAmountReg0Day']=($v['jyb']['amountReg0Day'])/($v['jyb']['countReg0Day']);
                   }
                    
                   if($v['jyb']['countReg1To5']==0){
                       $result[$dtime]['jyb']['avgAmountReg1To5']=0;
                   }else{
                      $result[$dtime]['jyb']['avgAmountReg1To5']=($v['jyb']['amountReg1To5'])/($v['jyb']['countReg1To5']);
                   }
                    
                   if($v['jyb']['countReg6To30']==0){
                       $result[$dtime]['jyb']['avgAmountReg6To30']=0;
                   }else{
                      $result[$dtime]['jyb']['avgAmountReg6To30']=($v['jyb']['amountReg6To30'])/($v['jyb']['countReg6To30']);
                   }
                    
                   if($v['jyb']['countReg31Plus']==0){
                       $result[$dtime]['jyb']['avgAmountReg31Plus']=0;
                   }else{
                   
                      $result[$dtime]['jyb']['avgAmountReg31Plus']=($v['jyb']['amountReg31Plus'])/($v['jyb']['countReg31Plus']);
                   }

                   }else{
                        $result[$dtime]['jyb']['avgAmountReg0Day']=0;
                        $result[$dtime]['jyb']['avgAmountReg1To5']=0;
                        $result[$dtime]['jyb']['avgAmountReg6To30']=0;
                        $result[$dtime]['jyb']['avgAmountReg31Plus']=0;
                   }
               }
               
               foreach ($records1 as $v){
                   if(array_key_exists($key, $v)){
                   
                   if($v['dqb']['countReg0Day']==0){
                        $result1[$dtime1]['dqb']['avgAmountReg0Day']=0;
                   }else{
                        $result1[$dtime1]['dqb']['avgAmountReg0Day']=($v['dqb']['amountReg0Day'])/($v['dqb']['countReg0Day']);
                   }
                   if($v['dqb']['countReg1To5']==0){
                       $result1[$dtime1]['dqb']['avgAmountReg1To5']=0;
                   }else{
                       $result1[$dtime1]['dqb']['avgAmountReg1To5']=($v['dqb']['amountReg1To5'])/($v['dqb']['countReg1To5']);
                   }
                    
                   if($v['dqb']['countReg6To30']==0){
                       $result1[$dtime1]['dqb']['avgAmountReg6To30']=0;
                   }else{
                        $result1[$dtime1]['avgAmountReg6To30']=($v['dqb']['amountReg6To30'])/($v['dqb']['countReg6To30']);
                   }
                    
                   if($v['dqb']['countReg31Plus']==0){
                       $result1[$dtime1]['dqb']['avgAmountReg31Plus']=0;
                   }else{
                       $result1[$dtime1]['avgAmountReg31Plus']=($v['dqb']['amountReg31Plus'])/($v['dqb']['countReg31Plus']);
                   }
                   

                   }else{
                       $result1[$dtime1]['dqb']['avgAmountReg0Day']=0;
                       $result1[$dtime1]['dqb']['avgAmountReg1To5']=0;
                       $result1[$dtime1]['dqb']['avgAmountReg6To30']=0;
                       $result1[$dtime1]['dqb']['avgAmountReg31Plus']=0;
                   }
                   
                   if(array_key_exists($key1, $v)){
                       
                   if($v['fbb']['countReg0Day']==0){
                        $result1[$dtime1]['fbb']['avgAmountReg0Day']=0;
                   }else{
                       $result1[$dtime1]['fbb']['avgAmountReg0Day']=($v['fbb']['amountReg0Day'])/($v['fbb']['countReg0Day']);
                   }
                    
                   if($v['fbb']['countReg1To5']==0){
                        $result1[$dtime1]['fbb']['avgAmountReg1To5']=0;
                   }else{
                        $result1[$dtime1]['fbb']['avgAmountReg1To5']=($v['fbb']['amountReg1To5'])/($v['fbb']['countReg1To5']);
                   }
                    
                   if($v['fbb']['countReg6To30']==0){
                        $result1[$dtime1]['fbb']['avgAmountReg6To30']=0;
                   }else{
                       $result1[$dtime1]['fbb']['avgAmountReg6To30']=($v['fbb']['amountReg6To30'])/($v['fbb']['countReg6To30']);
                   }
                    
                   if($v['fbb']['countReg31Plus']==0){
                        $result1[$dtime1]['fbb']['avgAmountReg31Plus']=0;
                   }else{
                   
                      $result1[$dtime1]['fbb']['avgAmountReg31Plus']=($v['fbb']['amountReg31Plus'])/($v['fbb']['countReg31Plus']);
                    }
                       

                   }else{
                       $result1[$dtime1]['fbb']['avgAmountReg0Day']=0;
                       $result1[$dtime1]['fbb']['avgAmountReg1To5']=0;
                       $result1[$dtime1]['fbb']['avgAmountReg6To30']=0;
                       $result1[$dtime1]['fbb']['avgAmountReg31Plus']=0;
                   }
                   
                   if(array_key_exists($key2, $v)){

                   if($v['jyb']['countReg0Day']==0){
                       $result1[$dtime1]['jyb']['avgAmountReg0Day']=0;
                   }else{
                       $result1[$dtime1]['jyb']['avgAmountReg0Day']=($v['jyb']['amountReg0Day'])/($v['jyb']['countReg0Day']);
                   }
                   
                   if($v['jyb']['countReg1To5']==0){
                      $result1[$dtime1]['jyb']['avgAmountReg1To5']=0;
                   }else{
                      $result1[$dtime1]['jyb']['avgAmountReg1To5']=($v['jyb']['amountReg1To5'])/($v['jyb']['countReg1To5']);
                   }
                   
                   if($v['jyb']['countReg6To30']==0){
                       $result1[$dtime1]['jyb']['avgAmountReg6To30']=0;
                   }else{
                      $result1[$dtime1]['jyb']['avgAmountReg6To30']=($v['jyb']['amountReg6To30'])/($v['jyb']['countReg6To30']);
                   }
                   
                   if($v['jyb']['countReg31Plus']==0){
                      $result1[$dtime1]['jyb']['avgAmountReg31Plus']=0;
                   }else{
                        
                      $result1[$dtime1]['jyb']['avgAmountReg31Plus']=($v['jyb']['amountReg31Plus'])/($v['jyb']['countReg31Plus']);
                   }
                       
                   }else{
                       $result1[$dtime1]['jyb']['avgAmountReg0Day']=0;
                       $result1[$dtime1]['jyb']['avgAmountReg1To5']=0;
                       $result1[$dtime1]['jyb']['avgAmountReg6To30']=0;
                       $result1[$dtime1]['jyb']['avgAmountReg31Plus']=0;
                   }
               }
               
               

//                 var_log($result,'rs#####################');
//                 var_log($result1,'rs1#####################');
                
                $record=[];
                $record1=[];
                
                foreach ($result as $k=>$v){
                    if(array_key_exists($key, $v)){
                       $v[$key]['avgAmountReg0Day']=round($v[$key]['avgAmountReg0Day']/100,2);
                       $v[$key]['avgAmountReg1To5']=round($v[$key]['avgAmountReg1To5']/100,2);
                       $v[$key]['avgAmountReg6To30']=round($v[$key]['avgAmountReg6To30']/100,2);
                       $v[$key]['avgAmountReg31Plus']=round($v[$key]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                    }
                       
                    if(array_key_exists($key1, $v)){
                        $v[$key1]['avgAmountReg0Day']=round($v[$key1]['avgAmountReg0Day']/100,2);
                        $v[$key1]['avgAmountReg1To5']=round($v[$key1]['avgAmountReg1To5']/100,2);
                        $v[$key1]['avgAmountReg6To30']=round($v[$key1]['avgAmountReg6To30']/100,2);
                        $v[$key1]['avgAmountReg31Plus']=round($v[$key1]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key1]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                       
                    }
                    
                    if(array_key_exists($key2, $v)){
                        $v[$key2]['avgAmountReg0Day']=round($v[$key2]['avgAmountReg0Day']/100,2);
                        $v[$key2]['avgAmountReg1To5']=round($v[$key2]['avgAmountReg1To5']/100,2);
                        $v[$key2]['avgAmountReg6To30']=round($v[$key2]['avgAmountReg6To30']/100,2);
                        $v[$key2]['avgAmountReg31Plus']=round($v[$key2]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key2]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                    }
                     
                     $record[$k]=$v;
                }
              
                foreach ($result1 as $k=>$v){
                     
                    if(array_key_exists($key, $v)){
                        $v[$key]['avgAmountReg0Day']=round($v[$key]['avgAmountReg0Day']/100,2);
                        $v[$key]['avgAmountReg1To5']=round($v[$key]['avgAmountReg1To5']/100,2);
                        $v[$key]['avgAmountReg6To30']=round($v[$key]['avgAmountReg6To30']/100,2);
                        $v[$key]['avgAmountReg31Plus']=round($v[$key]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                         
                    }
                    
                    if(array_key_exists($key1, $v)){
                        $v[$key1]['avgAmountReg0Day']=round($v[$key1]['avgAmountReg0Day']/100,2);
                        $v[$key1]['avgAmountReg1To5']=round($v[$key1]['avgAmountReg1To5']/100,2);
                        $v[$key1]['avgAmountReg6To30']=round($v[$key1]['avgAmountReg6To30']/100,2);
                        $v[$key1]['avgAmountReg31Plus']=round($v[$key1]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key1]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                         
                    }
                    if(array_key_exists($key2, $v)){
                        $v[$key2]['avgAmountReg0Day']=round($v[$key2]['avgAmountReg0Day']/100,2);
                        $v[$key2]['avgAmountReg1To5']=round($v[$key2]['avgAmountReg1To5']/100,2);
                        $v[$key2]['avgAmountReg6To30']=round($v[$key2]['avgAmountReg6To30']/100,2);
                        $v[$key2]['avgAmountReg31Plus']=round($v[$key2]['avgAmountReg31Plus']/100,2);
                    }else{
                        $v[$key2]=[
                            "avgAmountReg0Day"=>0,
                            'avgAmountReg1To5'=>0,
                            'avgAmountReg6To30'=>0,
                            'avgAmountReg31Plus'=>0
                        ];
                         
                    }
                
                    $record1[$k]=$v;
                }
                

              // var_log($record,'record#####################');
              // var_log($record1,'record111#####################');
             
              
                $this->_view->assign('record', json_encode($record));
                $this->_view->assign('record1', json_encode($record1));
                $this->_view->assign('dtime', json_encode($dtime));
                $this->_view->assign('dtime1', json_encode($dtime1));
                $this->_view->assign('dtime2', \Rpt\Funcs::date_format($ymdFrom, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo, 'Y年m月d日'));
                $this->_view->assign('dtime3', \Rpt\Funcs::date_format($ymdFrom1, 'Y年m月d日').' - '.\Rpt\Funcs::date_format($ymdTo1, 'Y年m月d日'));
                $this->_view->assign('channels', $channel_true);
                $this->_view->assign('rem', $rem);
                $this->_view->assign('ymdFrom', $ymdFrom);
                $this->_view->assign('ymdTo', $ymdTo);
              
                
        }

}