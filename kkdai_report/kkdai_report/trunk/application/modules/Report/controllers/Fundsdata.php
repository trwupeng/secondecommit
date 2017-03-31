<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class FundsdataController extends \Prj\ManagerCtrl
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
                
                //var_log($this->channel,'chaenll############');
               
                
                array_unshift($this->channel,['all'=>'所有']);
                $rem=[];
                foreach ($this->channel as $v){
                    foreach ($v as $kk=>$vv){
                        $rem[$kk]=$vv;
                    }
                }
                
               $timestrans=\Rpt\Funcs::timetrans();
                if(empty($ymdFrom)){
                    //$ymdFrom=date('Y-m-d',time()-7*84600);
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
                        $this->_view->assign('records', json_encode($records));
                        $this->_view->assign('records1', json_encode($records1));
                        $this->_view->assign('channels', $channel_true);
                        $this->_view->assign('rem', $rem);
                        $this->_view->assign('ymdFrom', $ymdFrom);
                        $this->_view->assign('ymdTo', $ymdTo);
                        return;
                    }
                    if(strtotime($ymdFrom)>strtotime($ymdTo)){
                        $this->_view->assign('errMsg','日期范围错误');
                        $this->_view->assign('records', json_encode($records));
                        $this->_view->assign('records1', json_encode($records1));
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
               
                if($channel_true==NULL || $channel_true=='所有'){

                    $where=[
                        'ymd]'=>date('Ymd',strtotime($ymdFrom)),
                        'ymd['=>date('Ymd',strtotime($ymdTo)),
                        'flagUser!'=>1,
                    ];
                     
                    $where1=[
                        'ymd]'=>$ymdFrom1,
                        'ymd['=>$ymdTo1,
                        'flagUser!'=>1,
                    ];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_financial_situation','ymd,rechargeAmount,withdrawAmount',$where,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_financial_situation','ymd,rechargeAmount,withdrawAmount',$where1,'sort ymd');
                    
                }else{
                    
                    $channel_id= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','contractId',['remarks'=>$channel_true]);
                    
                    $where3=['ymd]'=>date('Ymd',strtotime($ymdFrom)),
                        'ymd['=>date('Ymd',strtotime($ymdTo)),
                        'flagUser!'=>1,
                        'contractId'=>$channel_id,
                    ];
                     
                    $where4=['ymd]'=>$ymdFrom1,
                        'ymd['=>$ymdTo1,
                        'flagUser!'=>1,
                        'contractId'=>$channel_id,
                    ];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_financial_situation','ymd,rechargeAmount,withdrawAmount',$where3,'sort ymd');
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_financial_situation','ymd,rechargeAmount,withdrawAmount',$where4,'sort ymd');
                    
                }
              
                
                $records=[];
                $records1=[];
                
                if(empty($rs)){
                   $records[$dtime]['rechargeAmount']=0;
                   $records[$dtime]['withdrawAmount']=0;
                }else{
                   
                    foreach ($rs as $v){
                        $records[$dtime]['rechargeAmount']+=$v['rechargeAmount'];
                        $records[$dtime]['withdrawAmount']+=$v['withdrawAmount'];
                    }
                }
                
                if(empty($rs1)){
                    $records1[$dtime1]['rechargeAmount']=0;
                    $records1[$dtime1]['withdrawAmount']=0;
                }else{
                     
                    foreach ($rs1 as $v){
                        $records1[$dtime1]['rechargeAmount']+=$v['rechargeAmount'];
                        $records1[$dtime1]['withdrawAmount']+=$v['withdrawAmount'];
                    }
                }
                
                //var_log($records,'rs########################');
                //var_log($records1,'rs111111111111########################');
                
              $this->_view->assign('records', json_encode($records));
              $this->_view->assign('records1', json_encode($records1));
              
              $this->_view->assign('channels', $channel_true);
              $this->_view->assign('rem', $rem);
              
              $this->_view->assign('ymdFrom', $ymdFrom);
              $this->_view->assign('ymdTo', $ymdTo);
             
        }

}