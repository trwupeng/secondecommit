<?php
/**
 *充值/提现详情
 *
 * @author wu.peng
 * @param time 2016/9/20
 *
 */
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class ChardrawdetailsController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [50,100, 150, 300, 500, 1000];
            
            public function indexAction () {

               
                $rechdraw = $this->_request->get('_pkey_val_');
                $rechdrawone=date('Ymd',strtotime($rechdraw));
               
                
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                $ymdFrom = date('Y-m-d', $dt_instance->timestamp(-30));
                $ymdTo = $dt_instance->yesterday('Y-m-d');
                //$ymdOne=$dt_instance->yesterday('Ymd');
                // var_log($ymdOne,'one>>>>>>>>>>');
                
               // $ymdDefault = \Sooh\Base\Time::getInstance()->yesterday('Y-m-d');
                $form = \Sooh\Base\Form\Broker::getCopy('default')
                ->init(\Sooh\Base\Tools::uri(),'get',\Sooh\Base\Form\Broker::type_s);
                $form
                ->addItem('_ymdForm_g2', form_def::factory('日期从', $ymdFrom, form_def::datepicker))
                ->addItem('_ymdTo_l2', form_def::factory('到', $ymdTo, form_def::datepicker))
                ->addItem('pageid', $pageid)
                ->addItem('pagesize', $pager->page_size);
                
                $form->fillValues();
                $where = $form->getWhere();
                if($where['ymdForm]']) {
                    $where['ymdForm]'] = date('Ymd', strtotime($where['ymdForm]']));
                }
                if($where['ymdTo[']){
                    $where['ymdTo['] = date('Ymd', strtotime($where['ymdTo[']));
                }
                var_log($where,'where###################');
                
                $db_p2p=\Sooh\DB\Broker::getInstance();
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'ymd'=>['日期','40'],
                    'userId'=>['用户ID','40'],
                    'realname'=>['姓名', '30'],
                    'phone'=>['手机号','40'],
                    'optime'=>['操作时间','50'],
                    'opways'=>['操作方式','30'],
                    'amount'=>['金额','40'],  
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                
                if($isDownloadExcel){
                    if(empty($rechdraw)){
                    
                      
                         
                        $sql1="SELECT
                        ymd,
                    	userId,
                    	realname,
                    	phone,
                    	summary,
                    	ymd,
                    	hhiiss,
                    	amount/100
                    FROM
                    	tb_recharges_final AS a
                    LEFT JOIN tb_user_final AS b USING (userId)
                    WHERE
                    	ymd >={$where['ymdForm]']}
                    AND ymd<={$where['ymdTo[']}
                    AND orderStatus=39
                    ORDER BY ymd desc";
                        $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                        $sql1=$db_rpt->fetchAssocThenFree($sql1);
                    
                        foreach ($sql1 as $v){
                            $rs[]=[
                                'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                                'userId'=>$v['userId'],
                                'realname'=>$v['realname'],
                                'phone'=>$v['phone'],
                                'optime'=>$this->YmdhhiissConv($v['ymd'], $v['hhiiss']),
                                'opways'=>$v['summary'],
                                'amount'=>sprintf("%.2f",abs($v['amount/100'])),
                            ];
                        }
                         
                    }
                    else{
                      
                        $sql1="SELECT
                        ymd,
                        userId,
                        realname,
                        phone,
                        summary,
                        ymd,
                        hhiiss,
                        amount/100
                        FROM
                        tb_recharges_final AS a
                        LEFT JOIN tb_user_final AS b USING (userId)
                        WHERE
                        ymd={$rechdrawone}
                        AND orderStatus=39
                        ORDER BY ymd desc";
                        $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                        $sql1=$db_rpt->fetchAssocThenFree($sql1);
                    
                        foreach ($sql1 as $v){
                            $rs[]=[
                                'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                                'userId'=>$v['userId'],
                                'realname'=>$v['realname'],
                                'phone'=>$v['phone'],
                                'optime'=>$this->YmdhhiissConv($v['ymd'], $v['hhiiss']),
                                'opways'=>$v['summary'],
                                'amount'=>sprintf("%.2f",abs($v['amount/100'])),
                            ];
                        }
                    }
                }
                else{
                    if(empty($rechdraw)){
                    
                        $sql="SELECT
                        ymd,
                    	userId,
                    	realname,
                    	phone,
                    	summary,
                    	ymd,
                    	hhiiss,
                    	amount
                    FROM
                    	tb_recharges_final AS a
                    LEFT JOIN tb_user_final AS b USING (userId)
                    WHERE
                    ymd>={$where['ymdForm]']}
                    AND ymd<={$where['ymdTo[']}
                    AND orderStatus=39
                    ORDER BY ymd desc";
                         
                        $sql=$db_rpt->execCustom(['sql'=>$sql]);
                        $sql=$db_rpt->fetchAssocThenFree($sql);
                        $pager->init(Count($sql), $pageid);
                        //var_log($pager,'rs###################');
                        $rsform=$pager->rsFrom();
                         
                        $sql1="SELECT
                        ymd,
                    	userId,
                    	realname,
                    	phone,
                    	summary,
                    	ymd,
                    	hhiiss,
                    	amount/100
                    FROM
                    	tb_recharges_final AS a
                    LEFT JOIN tb_user_final AS b USING (userId)
                    WHERE
                    ymd >={$where['ymdForm]']}
                    AND ymd<={$where['ymdTo[']}
                    AND orderStatus=39
                    ORDER BY ymd desc"." limit ".$rsform.",".$pagesize;
                        $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                        $sql1=$db_rpt->fetchAssocThenFree($sql1);
                    
                        foreach ($sql1 as $v){
                            $rs[]=[
                                'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                                'userId'=>$v['userId'],
                                'realname'=>$v['realname'],
                                'phone'=>$v['phone'],
                                'optime'=>$this->YmdhhiissConv($v['ymd'], $v['hhiiss']),
                                'opways'=>$v['summary'],
                               'amount'=>sprintf("%.2f",abs($v['amount/100'])),
                            ];
                        }
                         
                    }else{
                        $sql="SELECT
                        ymd,
                        userId,
                        realname,
                        phone,
                        summary,
                        ymd,
                        hhiiss,
                        amount
                        FROM
                        tb_recharges_final AS a
                        LEFT JOIN tb_user_final AS b USING (userId)
                        WHERE
                        ymd={$rechdrawone}
                        AND orderStatus=39
                        ORDER BY ymd desc";
                         
                        $sql=$db_rpt->execCustom(['sql'=>$sql]);
                        $sql=$db_rpt->fetchAssocThenFree($sql);
                        $pager->init(Count($sql), $pageid);
                        // var_log($pager,'rs###################');
                        $rsform=$pager->rsFrom();
                         
                        $sql1="SELECT
                        ymd,
                        userId,
                        realname,
                        phone,
                        summary,
                        ymd,
                        hhiiss,
                        amount/100
                        FROM
                        tb_recharges_final AS a
                        LEFT JOIN tb_user_final AS b USING (userId)
                        WHERE
                        ymd={$rechdrawone}
                        AND orderStatus=39
                        ORDER BY ymd desc"." limit ".$rsform.",".$pagesize;
                        $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                        $sql1=$db_rpt->fetchAssocThenFree($sql1);
                    
                        foreach ($sql1 as $v){
                            $rs[]=[
                                'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                                'userId'=>$v['userId'],
                                'realname'=>$v['realname'],
                                'phone'=>$v['phone'],
                                'optime'=>$this->YmdhhiissConv($v['ymd'], $v['hhiiss']),
                                'opways'=>$v['summary'],
                                 'amount'=>sprintf("%.2f",abs($v['amount/100'])),
                            ];
                        }
                    } 
               }
              
              
                if($isDownloadExcel){
                    return $this->downExcel($rs, array_keys($headers));
                }
                
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $rs);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('where', $where); 
        }
        
        public  function  YmdhhiissConv($ymd,$hhiiss){
            $hhiiss=sprintf("%06d",$hhiiss);
            if(strpos($ymd,'-'))return ($ymd.' '.implode(':', str_split($hhiiss,2)));
            else return date('Y-m-d',strtotime($ymd)).' '.implode(':',str_split($hhiiss,2));
        }
        
}