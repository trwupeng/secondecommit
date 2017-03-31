<?php
/**
 *标的放款明细
 *
 * @author wu.peng
 * @param time 2016/9/21
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class FangkuanController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [20];
            
            public function indexAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
               
                $ymdFromDefault='2014-10';
                $ymdToDefault=date('Y-m');
                $wheretrue=$this->_request->get('where');
      
              
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
                
                if(strtotime($where['ymdTo['])< strtotime($where['ymdForm]'])) {
                    return $this->returnError('起始日期应该大于结束日期');
                }
                //var_log($where,'where>>>>');
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'customer_name'=>['借款人昵称',null],
                    'customer_realname'=>['姓名',null],
                    'bid_title'=>['标的名称',null],
                    'amount'=>['放款金额',null],
                    'create_time'=>['放款时间',null],
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                if($isDownloadExcel){
                    $sql2="
                            SELECT * from fangkuan as a LEFT JOIN bid as b
                            USING(bid_id)
                            WHERE create_time>='{$wheretrue['ymdForm']}'
                            and create_time<='{$wheretrue['ymdTo[']}'
                            and `status`=2
                            ORDER BY create_time DESC
                          ";
                    
                    $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                    $sql2=$db_p2p->fetchAssocThenFree($sql2);
                    //var_log($sql1,'rs###################');
                    
                  foreach ($sql2 as $k=>$v){
                        $sql2="SELECT customer_name,customer_realname from fangkuan as a LEFT JOIN customer as b
                                USING(customer_id)
                                WHERE customer_id='{$v['customer_id']}'";
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                       
                     
                        $result[]=[
                            'customer_name'=>$sql2[0]['customer_name'],
                            'customer_realname'=>$sql2[0]['customer_realname'],
                            'bid_title'=>$v['bid_title'],
                            'amount'=>sprintf("%.2f", $v['amount']/100),
                            'create_time'=>$v['create_time'],
                        ];
                    }
                  
                }else{
                    
                    $sql="
                           SELECT count(*) as count from fangkuan as a LEFT JOIN bid as b
                            USING(bid_id)
                            WHERE create_time>='{$where['ymdForm]']}'
                            and create_time<='{$where['ymdTo[']}'
                            and `status`=2
                            ORDER BY create_time DESC
                           ";
                    
                    $sql=$db_p2p->execCustom(['sql'=>$sql]);
                    $sql=$db_p2p->fetchAssocThenFree($sql);
                    //var_log($sql1,'rs###################');
                    $count=$sql[0]['count'];
                    $pager->init($count, $pageid);
                    $rsform=$pager->rsFrom();
                    //var_log($pager,'rs###################');
                    
                    $sql1=<<<sql
                     SELECT * from fangkuan as a LEFT JOIN bid as b
                     USING(bid_id)
                     WHERE create_time>='{$where['ymdForm]']}'
                     and create_time<='{$where['ymdTo[']}'
                     and `status`=2
                     ORDER BY create_time DESC
                     limit {$rsform},{$pagesize};
sql;
                    
                    $sql1=$db_p2p->execCustom(['sql'=>$sql1]);
                    $sql1=$db_p2p->fetchAssocThenFree($sql1);
                            
                    foreach ($sql1 as $k=>$v){
                        $sql3="SELECT customer_name,customer_realname from fangkuan as a LEFT JOIN customer as b
                                USING(customer_id)
                                WHERE customer_id='{$v['customer_id']}'
                               ";
                        $sql3=$db_p2p->execCustom(['sql'=>$sql3]);
                        $sql3=$db_p2p->fetchAssocThenFree($sql3);
                        
                        $amount=sprintf("%.2f", $v['amount']/100);
                        $amountall+=$amount;
                     
                        $result[]=[
                            'customer_name'=>$sql3[0]['customer_name'],
                            'customer_realname'=>$sql3[0]['customer_realname'],
                            'bid_title'=>$v['bid_title'],
                            'amount'=>$amount,
                            'create_time'=>$v['create_time'],
                        ];
                    }
                   
                }
               
              //var_log($rs,'rs###################');
              
                if($isDownloadExcel){
                    return $this->downExcel($result, array_keys($headers));
                }
                
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $result);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('where', $where);
        }
 
}