<?php
/**
 *用户理财明细(新浪)
 *
 * @author wu.peng
 * @param time 2016/9/21
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class UserfinancialdetailsController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [30];
            
            public function indexAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                $ymdFrom = date('Y-m-d', $dt_instance->timestamp(-6));
                $ymdTo = date('Y-m-d',time());
                $wheretrue=$this->_request->get('where');
               // var_log($wheretrue,'one>>>>>>>>>>');
                
              
                $form = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
               
                $form ->addItem('_ymdForm_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
                ->addItem('_ymdTo_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
                ->addItem('_UsrCustId_l2', form_def::factory('用户名/已验证手机', '', form_def::text))
                ->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);
                
                $form->fillValues();
                $where = $form->getWhere();
                if($where['ymdForm]']) {
                    $where['ymdForm]'] = date('Ymd', strtotime($where['ymdForm]']));
                    $where['ymdForm]']=$where['ymdForm]'].'000000';
                }
                if($where['ymdTo[']){
                    $where['ymdTo['] = date('Ymd', strtotime($where['ymdTo[']));
                    $where['ymdTo[']=$where['ymdTo['].'235959';
                }
                if($where['ymdTo[']< $where['ymdForm]']) {
                    return $this->returnError('起始日期应该大于结束日期');
                }
                
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
              
                if(strlen($where['UsrCustId['])==11){
                    $sql="SELECT DISTINCT UsrCustId FROM `customer`
                          WHERE customer_cellphone='{$where['UsrCustId[']}'";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $where['UsrCustId[']=$sql[0]['UsrCustId'];
                }else{
                    $sql="SELECT DISTINCT UsrCustId FROM `customer`
                    WHERE customer_name='{$where['UsrCustId[']}'";
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    $where['UsrCustId[']=$sql[0]['UsrCustId'];
                }
                
                $stance=(strtotime(date('Ymd',strtotime($where['ymdTo['])))-strtotime(date('Ymd',strtotime($where['ymdForm]']))))/86400;
                $stance1=(strtotime(date('Ymd',time()))-strtotime(date('Ymd',strtotime($where['ymdForm]']))))/86400;
                //var_log($stance,'stance21################');
                
                if($stance>=7){
                     return $this->returnError('日期选择范围小于7天');
                }elseif($stance1>90){
                    return $this->returnError('只能选择最近90天内的数据');
                }
             
                
                $fielsdMap=[
                    'zhaiyao'=>['摘要',null],
                    'jxamount'=>['交易金额',null],
                    'dqamount'=>['当前余额',null],
                    'licaidate'=>['理财日期',null],
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                if($isDownloadExcel){

                    $request_time=date('YmdHis',time());
                    
                    $data=[
                        'service'=>'query_account_details',
                        'version'=>'1.0',
                        'request_time'=>$request_time,
                        'partner_id'=>'200006907693',
                        '_input_charset'=>'UTF-8',
                        'sign_type'=>'RSA',
                        'identity_id'=>$wheretrue['UsrCustId['],
                        'identity_type'=>'UID',
                        'account_type'=>'SAVING_POT',
                        'start_time'=>$wheretrue['ymdForm'],
                        'end_time'=>$wheretrue['ymdTo['],
                        'page_no'=>$pageid,
                        'page_size'=>$pagesize,
                        'extend_param'=>''
                    ];
                     
                    
                    $sina=new \Api\Xinlang\controller\Sina();
                    
                    $result=$sina->query_account_details($data);
                    
                    $total_item=$result['total_item'];
                    $n=ceil($total_item/$pagesize);
                    
                    for ($i=1;$i<=$n;$i++){
                        
                        $data=[
                            'service'=>'query_account_details',
                            'version'=>'1.0',
                            'request_time'=>$request_time,
                            'partner_id'=>'200006907693',
                            '_input_charset'=>'UTF-8',
                            'sign_type'=>'RSA',
                            'identity_id'=>$wheretrue['UsrCustId['],
                            'identity_type'=>'UID',
                            'account_type'=>'SAVING_POT',
                            'start_time'=>$where['ymdForm]'],
                            'end_time'=>$where['ymdTo['],
                            'page_no'=>$i,
                            'page_size'=>$pagesize,
                            'extend_param'=>''
                        ];
                        $demo=$sina->query_account_details($data);
                        $remo[]=$demo['detail_list'];

                    }
                   
                    
                    if(empty($remo)){
                        $temp[]=[
                            'zhaiyao'=>'',
                            'jxamount'=>'',
                            'dqamount'=>'',
                            'licaidate'=>'',
                        ];
                    }else{
                        foreach ($remo as $v){
                            $ret=explode('|', $v);
                            foreach ($ret as $vv){
                                $vv=explode('^',$vv);
                                $rem[]=$vv;
                            }
                        }
                      
                        foreach ($rem as $v){
                        
                             
                            $temp[]=[
                                'zhaiyao'=>$v['0'],
                                'jxamount'=>$v['2'].$v['3'],
                                'dqamount'=>$v['4'],
                                'licaidate'=>date('Y-m-d H:i:s',strtotime($v['1'])),
                            ];
                        } 
                    }
                   
                }else{
                    $request_time=date('YmdHis',time());
                    
                    $data=[
                        'service'=>'query_account_details',
                        'version'=>'1.0',
                        'request_time'=>$request_time,
                        'partner_id'=>'200006907693',
                        '_input_charset'=>'UTF-8',
                        'sign_type'=>'RSA',
                        'identity_id'=>$where['UsrCustId['],
                        'identity_type'=>'UID',
                        'account_type'=>'SAVING_POT',
                        'start_time'=>$where['ymdForm]'],
                        'end_time'=>$where['ymdTo['],
                        'page_no'=>$pageid,
                        'page_size'=>$pagesize,
                        'extend_param'=>''
                    ];
                     
                    
                    $sina=new \Api\Xinlang\controller\Sina();
                    
                    $result=$sina->query_account_details($data);
                   // var_log($result,'resuly>>>>>>>>>>>');
                    $pager->init($result['total_item'], $pageid);

                    $rs=$result['detail_list'];
                    
                    if(empty($rs)){
                        $temp[]=[
                            'zhaiyao'=>'',
                            'jxamount'=>'',
                            'dqamount'=>'',
                            'licaidate'=>'',
                        ];
                    
                    }else{
                        $ret=explode('|', $rs);
                    
                        foreach ($ret as $v){
                            $v=explode('^',$v);
                            $rem[]=$v;
                        }
                         
                        foreach ($rem as $v){
                    
                             
                            $temp[]=[
                                'zhaiyao'=>$v['0'],
                                'jxamount'=>$v['2'].$v['3'],
                                'dqamount'=>$v['4'],
                                'licaidate'=>date('Y-m-d H:i:s',strtotime($v['1'])),
                            ];
                        }
                    }
                }
             

                if($isDownloadExcel){
                   //  var_log($temp,'temp########################');
                    return $this->downExcel($temp, array_keys($headers));
                }
                
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $temp);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('where', $where); 
        }
        
       
}