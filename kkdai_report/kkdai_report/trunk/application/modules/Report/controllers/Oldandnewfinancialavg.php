
<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class OldandnewfinancialavgController extends \Prj\ManagerCtrl
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
                
                $timestrans=\Rpt\Funcs::timetrans();
                array_unshift($this->channel,['all'=>'所有']);
                $rem=[];
                foreach ($this->channel as $v){
                    foreach ($v as $kk=>$vv){
                        $rem[$kk]=$vv;
                    }
                }
                
                
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
                
                var_log($channel_true,'chanenl##########################');
                
                if($channel_true==NULL || $channel_true=='所有'){

                    $where=['ymdStart'=>date('Ymd',strtotime($ymdFrom)),'ymdEnd'=>date('Ymd',strtotime($ymdTo))];
                     
                    $where1=['ymdStart'=>$ymdFrom1,'ymdEnd'=>$ymdTo1];
                    
                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_count','shelfId,count1Buy,
                        count5Buy,count6PlusBuy,amount1Buy,amount5Buy,amount6PlusBuy',$where);
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_count','shelfId,count1Buy,
                       count5Buy,count6PlusBuy,amount1Buy,amount5Buy,amount6PlusBuy',$where1);
                    
                }else{

                    $channel_id= $this->dbMysql->getOne('db_kkrpt.tb_contract_0','contractId',['remarks'=>$channel_true]);
                    
                    $where3=['ymdStart'=>date('Ymd',strtotime($ymdFrom)),
                        'ymdEnd'=>date('Ymd',strtotime($ymdTo)),
                        'contractId'=>$channel_id,
                    ];
                     
                    $where4=['ymdStart'=>$ymdFrom1,
                        'ymdEnd'=>$ymdTo1,
                        'contractId'=>$channel_id,
                    ];

                    $rs= $this->dbMysql->getRecords('db_kkrpt.tb_licai_count','shelfId,count1Buy,
                        count5Buy,count6PlusBuy,amount1Buy,amount5Buy,amount6PlusBuy',$where3);
                    
                    $rs1= $this->dbMysql->getRecords('db_kkrpt.tb_licai_count','shelfId,count1Buy,
                       count5Buy,count6PlusBuy,amount1Buy,amount5Buy,amount6PlusBuy',$where4);
                    
                }
                
               
                
                $records=[];
                $records1=[];
                 
                if(empty($rs)){
                    $records[$dtime]['dqb']['avgAmount1Buy']=0;
                    $records[$dtime]['dqb']['avgAmount5Buy']=0;
                    $records[$dtime]['dqb']['avgAmount6PlusBuy']=0;
                   
                    $records[$dtime]['fbb']['avgAmount1Buy']=0;
                    $records[$dtime]['fbb']['avgAmount5Buy']=0;
                    $records[$dtime]['fbb']['avgAmount6PlusBuy']=0;
                    
                    $records[$dtime]['jyb']['avgAmount1Buy']=0;
                    $records[$dtime]['jyb']['avgAmount5Buy']=0;
                    $records[$dtime]['jyb']['avgAmount6PlusBuy']=0;
                   
                }else{
                    foreach ($rs as $v){
                
                        if($v['shelfId']==1){
                            $records[$dtime]['dqb']['count1Buy']+=$v['count1Buy'];
                            $records[$dtime]['dqb']['count5Buy']+=$v['count5Buy'];
                            $records[$dtime]['dqb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records[$dtime]['dqb']['amount1Buy']+=$v['amount1Buy'];
                            $records[$dtime]['dqb']['amount5Buy']+=$v['amount5Buy'];
                            $records[$dtime]['dqb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                           
                        }
                         
                         
                        elseif ($v['shelfId']==2){
                            $records[$dtime]['fbb']['count1Buy']+=$v['count1Buy'];
                            $records[$dtime]['fbb']['count5Buy']+=$v['count5Buy'];
                            $records[$dtime]['fbb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records[$dtime]['fbb']['amount1Buy']+=$v['amount1Buy'];
                            $records[$dtime]['fbb']['amount5Buy']+=$v['amount5Buy'];
                            $records[$dtime]['fbb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                           
                        }
                         
                         
                        elseif($v['shelfId']==5){
                            $records[$dtime]['jyb']['count1Buy']+=$v['count1Buy'];
                            $records[$dtime]['jyb']['count5Buy']+=$v['count5Buy'];
                            $records[$dtime]['jyb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records[$dtime]['jyb']['amount1Buy']+=$v['amount1Buy'];
                            $records[$dtime]['jyb']['amount5Buy']+=$v['amount5Buy'];
                            $records[$dtime]['jyb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                        }
                
                    }
                }
                 
                if(empty($rs1)){
                    $records1[$dtime1]['dqb']['avgAmount1Buy']=0;
                    $records1[$dtime1]['dqb']['avgAmount5Buy']=0;
                    $records1[$dtime1]['dqb']['avgAmount6PlusBuy']=0;
                   
                
                    $records1[$dtime1]['fbb']['avgAmount1Buy']=0;
                    $records1[$dtime1]['fbb']['avgAmount5Buy']=0;
                    $records1[$dtime1]['fbb']['avgAmount6PlusBuy']=0;
                   
                
                    $records1[$dtime1]['jyb']['avgAmount1Buy']=0;
                    $records1[$dtime1]['jyb']['avgAmount5Buy']=0;
                    $records1[$dtime1]['jyb']['avgAmount6PlusBuy']=0;
                   
                }else{
                    foreach ($rs1 as $v){
                         
                        if($v['shelfId']==1){
                
                            $records1[$dtime1]['dqb']['count1Buy']+=$v['count1Buy'];
                            $records1[$dtime1]['dqb']['count5Buy']+=$v['count5Buy'];
                            $records1[$dtime1]['dqb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records1[$dtime1]['dqb']['amount1Buy']+=$v['amount1Buy'];
                            $records1[$dtime1]['dqb']['amount5Buy']+=$v['amount5Buy'];
                            $records1[$dtime1]['dqb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                        }
                
                         
                        elseif ($v['shelfId']==2){
                
                            $records1[$dtime1]['fbb']['count1Buy']+=$v['count1Buy'];
                            $records1[$dtime1]['fbb']['count5Buy']+=$v['count5Buy'];
                            $records1[$dtime1]['fbb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records1[$dtime1]['fbb']['amount1Buy']+=$v['amount1Buy'];
                            $records1[$dtime1]['fbb']['amount5Buy']+=$v['amount5Buy'];
                            $records1[$dtime1]['fbb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                           
                        }
                
                         
                        elseif($v['shelfId']==5){
                
                            $records1[$dtime1]['jyb']['count1Buy']+=$v['count1Buy'];
                            $records1[$dtime1]['jyb']['count5Buy']+=$v['count5Buy'];
                            $records1[$dtime1]['jyb']['count6PlusBuy']+=$v['count6PlusBuy'];
                            $records1[$dtime1]['jyb']['amount1Buy']+=$v['amount1Buy'];
                            $records1[$dtime1]['jyb']['amount5Buy']+=$v['amount5Buy'];
                            $records1[$dtime1]['jyb']['amount6PlusBuy']+=$v['amount6PlusBuy'];
                        }
                         
                    }
                }
              
                //var_log($records,'rs##########################');
                //var_log($records1,'rs1#########################');
                
                $key='dqb';
                $key1='fbb';
                $key2='jyb';
                $result=[];
                $result1=[];
                
                foreach ($records as $v){
                    if(array_key_exists($key, $v)){
                         
                        if($v['dqb']['count1Buy']==0){
                            $result[$dtime]['dqb']['avgAmount1Buy']=0;
                        }else{
                            $result[$dtime]['dqb']['avgAmount1Buy']=($v['dqb']['amount1Buy'])/($v['dqb']['count1Buy']);
                        }
                        if($v['dqb']['count5Buy']==0){
                            $result[$dtime]['dqb']['avgAmount5Buy']=0;
                        }else{
                            $result[$dtime]['dqb']['avgAmount5Buy']=($v['dqb']['amount5Buy'])/($v['dqb']['count5Buy']);
                        }
                         
                        if($v['dqb']['count6PlusBuy']==0){
                            $result[$dtime]['dqb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result[$dtime]['dqb']['avgAmount6PlusBuy']=($v['dqb']['amount6PlusBuy'])/($v['dqb']['count6PlusBuy']);
                        }
                        
                
                    }else{
                        $result[$dtime]['dqb']['avgAmount1Buy']=0;
                        $result[$dtime]['dqb']['avgAmount5Buy']=0;
                        $result[$dtime]['dqb']['avgAmount6PlusBuy']=0;

                    }
                     
                    if(array_key_exists($key1, $v)){
                         
                        if($v['fbb']['count1Buy']==0){
                            $result[$dtime]['fbb']['avgAmount1Buy']=0;
                        }else{
                            $result[$dtime]['fbb']['avgAmount1Buy']=($v['fbb']['amount1Buy'])/($v['fbb']['count1Buy']);
                        }
                        if($v['fbb']['count5Buy']==0){
                            $result[$dtime]['fbb']['avgAmount5Buy']=0;
                        }else{
                            $result[$dtime]['fbb']['avgAmount5Buy']=($v['fbb']['amount5Buy'])/($v['fbb']['count5Buy']);
                        }
                         
                        if($v['fbb']['count6PlusBuy']==0){
                            $result[$dtime]['fbb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result[$dtime]['fbb']['avgAmount6PlusBuy']=($v['fbb']['amount6PlusBuy'])/($v['fbb']['count6PlusBuy']);
                        }
                
                    }else{
                        $result[$dtime]['fbb']['avgAmount1Buy']=0;
                        $result[$dtime]['fbb']['avgAmount5Buy']=0;
                        $result[$dtime]['fbb']['avgAmount6PlusBuy']=0;
                    }
                     
                    if(array_key_exists($key2, $v)){
                         
                       if($v['jyb']['count1Buy']==0){
                            $result[$dtime]['jyb']['avgAmount1Buy']=0;
                        }else{
                            $result[$dtime]['jyb']['avgAmount1Buy']=($v['jyb']['amount1Buy'])/($v['jyb']['count1Buy']);
                        }
                        if($v['jyb']['count5Buy']==0){
                            $result[$dtime]['jyb']['avgAmount5Buy']=0;
                        }else{
                            $result[$dtime]['jyb']['avgAmount5Buy']=($v['jyb']['amount5Buy'])/($v['jyb']['count5Buy']);
                        }
                         
                        if($v['jyb']['count6PlusBuy']==0){
                            $result[$dtime]['jyb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result[$dtime]['jyb']['avgAmount6PlusBuy']=($v['jyb']['amount6PlusBuy'])/($v['jyb']['count6PlusBuy']);
                        }
                
                    }else{
                        $result[$dtime]['jyb']['avgAmount1Buy']=0;
                        $result[$dtime]['jyb']['avgAmount5Buy']=0;
                        $result[$dtime]['jyb']['avgAmount6PlusBuy']=0;
                    }
                }
                 
                foreach ($records1 as $v){
                 if(array_key_exists($key, $v)){
                         
                        if($v['dqb']['count1Buy']==0){
                            $result1[$dtime1]['dqb']['avgAmount1Buy']=0;
                        }else{
                            $result1[$dtime1]['dqb']['avgAmount1Buy']=($v['dqb']['amount1Buy'])/($v['dqb']['count1Buy']);
                        }
                        if($v['dqb']['count5Buy']==0){
                            $result1[$dtime1]['dqb']['avgAmount5Buy']=0;
                        }else{
                            $result1[$dtime1]['dqb']['avgAmount5Buy']=($v['dqb']['amount5Buy'])/($v['dqb']['count5Buy']);
                        }
                         
                        if($v['dqb']['count6PlusBuy']==0){
                            $result1[$dtime1]['dqb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result1[$dtime1]['dqb']['avgAmount6PlusBuy']=($v['dqb']['amount6PlusBuy'])/($v['dqb']['count6PlusBuy']);
                        }
                        
                
                    }else{
                        $result1[$dtime1]['dqb']['avgAmount1Buy']=0;
                        $result1[$dtime1]['dqb']['avgAmount5Buy']=0;
                        $result1[$dtime1]['dqb']['avgAmount6PlusBuy']=0;

                    }
                     
                    if(array_key_exists($key1, $v)){
                         
                        if($v['fbb']['count1Buy']==0){
                            $result1[$dtime1]['fbb']['avgAmount1Buy']=0;
                        }else{
                            $result1[$dtime1]['fbb']['avgAmount1Buy']=($v['fbb']['amount1Buy'])/($v['fbb']['count1Buy']);
                        }
                        if($v['fbb']['count5Buy']==0){
                            $result1[$dtime1]['fbb']['avgAmount5Buy']=0;
                        }else{
                            $result1[$dtime1]['fbb']['avgAmount5Buy']=($v['fbb']['amount5Buy'])/($v['fbb']['count5Buy']);
                        }
                         
                        if($v['fbb']['count6PlusBuy']==0){
                            $result1[$dtime1]['fbb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result1[$dtime1]['fbb']['avgAmount6PlusBuy']=($v['fbb']['amount6PlusBuy'])/($v['fbb']['count6PlusBuy']);
                        }
                
                    }else{
                        $result1[$dtime1]['fbb']['avgAmount1Buy']=0;
                        $result1[$dtime1]['fbb']['avgAmount5Buy']=0;
                        $result1[$dtime1]['fbb']['avgAmount6PlusBuy']=0;
                    }
                     
                    if(array_key_exists($key2, $v)){
                         
                       if($v['jyb']['count1Buy']==0){
                            $result1[$dtime1]['jyb']['avgAmount1Buy']=0;
                        }else{
                            $result1[$dtime1]['jyb']['avgAmount1Buy']=($v['jyb']['amount1Buy'])/($v['jyb']['count1Buy']);
                        }
                        if($v['jyb']['count5Buy']==0){
                            $result1[$dtime1]['jyb']['avgAmount5Buy']=0;
                        }else{
                            $result1[$dtime1]['jyb']['avgAmount5Buy']=($v['jyb']['amount5Buy'])/($v['jyb']['count5Buy']);
                        }
                         
                        if($v['jyb']['count6PlusBuy']==0){
                            $result1[$dtime1]['jyb']['avgAmount6PlusBuy']=0;
                        }else{
                            $result1[$dtime1]['jyb']['avgAmount6PlusBuy']=($v['jyb']['amount6PlusBuy'])/($v['jyb']['count6PlusBuy']);
                        }
                
                    }else{
                        $result1[$dtime1]['jyb']['avgAmount1Buy']=0;
                        $result1[$dtime1]['jyb']['avgAmount5Buy']=0;
                        $result1[$dtime1]['jyb']['avgAmount6PlusBuy']=0;
                    }
                }

                $record=[];
                $record1=[];
                
                foreach ($result as $k=>$v){
                    if(array_key_exists($key, $v)){
                    $v[$key]['avgAmount1Buy']=round($v[$key]['avgAmount1Buy']/100,2);
                    $v[$key]['avgAmount5Buy']=round($v[$key]['avgAmount5Buy']/100,2);
                    $v[$key]['avgAmount6PlusBuy']=round($v[$key]['avgAmount6PlusBuy']/100,2);
                 
                    }else{
                        $v[$key]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                    }
                     
                    if(array_key_exists($key1, $v)){
                        $v[$key1]['avgAmount1Buy']=round($v[$key1]['avgAmount1Buy']/100,2);
                        $v[$key1]['avgAmount5Buy']=round($v[$key1]['avgAmount5Buy']/100,2);
                        $v[$key1]['avgAmount6PlusBuy']=round($v[$key1]['avgAmount6PlusBuy']/100,2);
                    }else{
                        $v[$key1]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                         
                    }
                
                    if(array_key_exists($key2, $v)){
                        $v[$key2]['avgAmount1Buy']=round($v[$key2]['avgAmount1Buy']/100,2);
                        $v[$key2]['avgAmount5Buy']=round($v[$key2]['avgAmount5Buy']/100,2);
                        $v[$key2]['avgAmount6PlusBuy']=round($v[$key2]['avgAmount6PlusBuy']/100,2);
                    }else{
                        $v[$key2]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                    }
                     
                    $record[$k]=$v;
                }
                
                foreach ($result1 as $k=>$v){
                     
                    if(array_key_exists($key, $v)){
                        $v[$key]['avgAmount1Buy']=round($v[$key]['avgAmount1Buy']/100,2);
                        $v[$key]['avgAmount5Buy']=round($v[$key]['avgAmount5Buy']/100,2);
                        $v[$key]['avgAmount6PlusBuy']=round($v[$key]['avgAmount6PlusBuy']/100,2);
                    }else{
                        $v[$key]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                         
                    }
                
                    if(array_key_exists($key1, $v)){
                        $v[$key1]['avgAmount1Buy']=round($v[$key1]['avgAmount1Buy']/100,2);
                        $v[$key1]['avgAmount5Buy']=round($v[$key1]['avgAmount5Buy']/100,2);
                        $v[$key1]['avgAmount6PlusBuy']=round($v[$key1]['avgAmount6PlusBuy']/100,2);
                    }else{
                        $v[$key1]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                         
                    }
                    if(array_key_exists($key2, $v)){
                        $v[$key2]['avgAmount1Buy']=round($v[$key2]['avgAmount1Buy']/100,2);
                        $v[$key2]['avgAmount5Buy']=round($v[$key2]['avgAmount5Buy']/100,2);
                        $v[$key2]['avgAmount6PlusBuy']=round($v[$key2]['avgAmount6PlusBuy']/100,2);
                    }else{
                        $v[$key2]=[
                            "avgAmount1Buy"=>0,
                            'avgAmount5Buy'=>0,
                            'avgAmount6PlusBuy'=>0,
                        ];
                         
                    }
                
                    $record1[$k]=$v;
                }
                
             
                
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