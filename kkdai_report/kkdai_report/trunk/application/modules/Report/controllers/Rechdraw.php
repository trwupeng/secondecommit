<?php
/**
 *日用户充值/提现统计
 * 
 * @author wu.peng
 * @param time 2016/9/20
 * 
*/
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class RechdrawController extends \Prj\ManagerCtrl
{
           
            public function init(){
                parent::init();
                if($this->_request->get('__VIEW__')=='json') {
                    \Sooh\Base\Ini::getInstance()->viewRenderType('json');
                }
    
            }
            
            protected $pageSizeEnum = [20];//[50,100, 150, 300, 500, 1000];
            protected $pageSizeEnum01 = [20,31];
            protected $pageSizeEnum02 = [50,100, 150, 300, 500, 1000];
            
            public function indexAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
            
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
            
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                $ymdFromDefault='2014-06';
                 
                $ymdToDefault=date('Y-m');
                $wheretrue=json_decode(urldecode($this->_request->get('where')), true);

                if(empty($ymdFrom) && empty($ymdTo) && !$isDownloadExcel) {
                    $where['ymdForm]']=$ymdFromDefault;
                    $where['ymdTo[']=$ymdToDefault;
                }else{
                    
                    if(!self::check_date($ymdFrom) || !self::check_date($ymdTo)){
                        $this->returnError('日期格式错误  输入如：2016-11');
                    }
                    if(strtotime($ymdTo)< strtotime($ymdFrom)) {
                        $this->returnError('起始日期不应该晚于截止日期');
                    }
                    
                    $where['ymdForm]']=$ymdFrom;
                    $where['ymdTo[']=$ymdTo;
                }
            
            
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
            
                $fielsdMap=[
                    'ym'=>['日期',null],
                    'superuesrrecharge'=>['超级用户充值',null],
                    'normaluesrrecharge'=>['普通用户充值',null],
                    'superuesrwithdraw'=>['超级用户提现',null],
                    'normaluesrwithdraw'=>['普通用户提现',null],
                    'rechargeAmount'=>['充值总金额',null],
                    'withdrawAmount'=>['提现总金额',null],
                ];
            
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                 
            
                if($isDownloadExcel){
            
                    $FromDefault_year='2014';
                    $FromDefault_month='06';
                    $ToDefault_year=date('Y');
                    $ToDefault_month=date('m');
            
                    $beginYear=date('Y',strtotime($wheretrue['ymdForm']));
                    $beginMonth=date('m',strtotime($wheretrue['ymdForm']));
                    $nowYear=date('Y',strtotime($wheretrue['ymdTo[']));
                    $nowMonth=date('m',strtotime($wheretrue['ymdTo[']));
                    $beginYearo1=date('Y',strtotime($wheretrue['ymdForm']));
            
                    for ($beginYear;$beginYear<=$nowYear;$beginYear++){
                        for ($i=1,$j=12;$i<=$j;$i++){
                            if($i<10){
                                $i='0'.$i;
                            }
                            if($beginYear==$beginYearo1 && $i<$beginMonth)continue;
                            if($beginYear==$nowYear && $i>$nowMonth)continue;
                            if($beginYear==$FromDefault_year && $i<$FromDefault_month)continue;
                            if($beginYear==$ToDefault_year && $i>$ToDefault_month)continue;
                            if($beginYear<$FromDefault_year || $beginYear>$ToDefault_year)continue;
                            $choosedate[]=$beginYear.$i;
                        }
                    }
                    arsort($choosedate);
                    reset($choosedate);
                    
                    $Totime=current($choosedate).'31';
                    $Formtime=end($choosedate).'01';
                    
                            $sql=<<<sql
                            SELECT
                            DATE_FORMAT(a.ymd,'%Y-%m') as ym,
                            SUM(CASE when flagUser=1 and summary='充值' and orderStatus=39 then amount/100 END) as superuesrrecharge,
                            SUM(CASE when flagUser!=1 and summary='充值' and orderStatus=39 then amount/100 END) as normaluesrrecharge,
                            SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as superuesrwithdraw,
                            SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as normaluesrwithdraw,
                            SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as rechargeAmount,
                            SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as withdrawAmount
                            from tb_recharges_final as a LEFT JOIN tb_user_final as b
                            USING(userId)
                            WHERE a.ymd>={$Formtime}
                            and a.ymd<={$Totime}
                            group by ym order by ym desc;
sql;
                            $rs=\Rpt\Funcs::execSql($sql, $db_rpt);
                            
                            foreach ($rs as $v){
                                $v['superuesrrecharge']=sprintf("%.2f",$v['superuesrrecharge']);
                                $v['normaluesrrecharge']=sprintf("%.2f",$v['normaluesrrecharge']);
                                $v['superuesrwithdraw']=sprintf("%.2f",$v['superuesrwithdraw']);
                                $v['normaluesrwithdraw']=sprintf("%.2f",$v['normaluesrwithdraw']);
                                $v['rechargeAmount']=sprintf("%.2f",$v['rechargeAmount']);
                                $v['withdrawAmount']=sprintf("%.2f",$v['withdrawAmount']);
                            
                                $records[]=$v;
                            }
                            
                         
                }else{

                    $first_day='20140630';
                    $last_day=date('Ymd');
                    
                    $sql_all=<<<sql
                    SELECT
                    SUM(CASE when flagUser=1 and summary='充值' and orderStatus=39 then amount/100 END) as t1,
                    SUM(CASE when flagUser!=1 and summary='充值' and orderStatus=39 then amount/100 END) as t2,
                    SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as t3,
                    SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as t4,
                    SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as t5,
                    SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as t6
                    from tb_recharges_final as a LEFT JOIN tb_user_final as b
                    USING(userId)
                    WHERE a.ymd>="{$first_day}"
                    AND a.ymd<="{$last_day}"
                    ;
sql;
                    $sql_all=\Rpt\Funcs::execSql($sql_all, $db_rpt);
                   
                    $superuesrrecharge=sprintf("%.2f",$sql_all[0]['t1']);
                    $normaluesrrecharge=sprintf("%.2f",$sql_all[0]['t2']);
                    $superuesrwithdraw=sprintf("%.2f",abs($sql_all[0]['t3']));
                    $normaluesrwithdraw=sprintf("%.2f",abs($sql_all[0]['t4']));
                    $rechargeAmount=sprintf("%.2f",$sql_all[0]['t5']);
                    $withdrawAmount=sprintf("%.2f",abs($sql_all[0]['t6']));
                    
                    $FromDefault_year='2014';
                    $FromDefault_month='06';
                    $ToDefault_year=date('Y');
                    $ToDefault_month=date('m');
                    
                    $beginYear=date('Y',strtotime($where['ymdForm]']));
                    $beginMonth=date('m',strtotime($where['ymdForm]']));
                    $nowYear=date('Y',strtotime($where['ymdTo[']));
                    $nowMonth=date('m',strtotime($where['ymdTo[']));
                    $beginYearo1=date('Y',strtotime($where['ymdForm]']));

                    for ($beginYear;$beginYear<=$nowYear;$beginYear++){
                        for ($i=1,$j=12;$i<=$j;$i++){
                            if($i<10){
                                $i='0'.$i;
                            }
                            if($beginYear==$beginYearo1 && $i<$beginMonth)continue;
                            if($beginYear==$nowYear && $i>$nowMonth)continue;
                            if($beginYear==$FromDefault_year && $i<$FromDefault_month)continue;
                            if($beginYear==$ToDefault_year && $i>$ToDefault_month)continue;
                            if($beginYear<$FromDefault_year || $beginYear>$ToDefault_year)continue;
                            $choosedate[]=$beginYear.$i;
                        }
                    }       
                    arsort($choosedate);
                    reset($choosedate);
                    
                    $pager->init(Count($choosedate), $pageid);
                    $rsform=$pager->rsFrom();
                    $choosedate=array_slice($choosedate, $rsform,$pagesize);

                    $Totime=current($choosedate).'31';
                    $Formtime=end($choosedate).'01';
                    
                    $sql=<<<sql
                            SELECT
                            DATE_FORMAT(a.ymd,'%Y-%m') as ym,
                            SUM(CASE when flagUser=1 and summary='充值' and orderStatus=39 then amount/100 END) as superuesrrecharge,
                            SUM(CASE when flagUser!=1 and summary='充值' and orderStatus=39 then amount/100 END) as normaluesrrecharge,
                            SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as superuesrwithdraw,
                            SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as normaluesrwithdraw,
                            SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as rechargeAmount,
                            SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as withdrawAmount
                            from tb_recharges_final as a LEFT JOIN tb_user_final as b
                            USING(userId)
                            WHERE a.ymd>={$Formtime}
                            and a.ymd<={$Totime}
                            group by ym order by ym desc
                            ;
sql;
                    $rs=\Rpt\Funcs::execSql($sql, $db_rpt);
                    
                
                    foreach ($rs as $v){
                        $v['superuesrrecharge']=sprintf("%.2f",$v['superuesrrecharge']);
                        $v['normaluesrrecharge']=sprintf("%.2f",$v['normaluesrrecharge']);
                        $v['superuesrwithdraw']=sprintf("%.2f",abs($v['superuesrwithdraw']));
                        $v['normaluesrwithdraw']=sprintf("%.2f",abs($v['normaluesrwithdraw']));
                        $v['rechargeAmount']=sprintf("%.2f",$v['rechargeAmount']);
                        $v['withdrawAmount']=sprintf("%.2f",abs($v['withdrawAmount']));
                    
                        $records[]=$v;
                    }
            
                }
            
                if($isDownloadExcel){
                    if(!empty($records)){
                        return $this->downExcel($records, array_keys($headers),['ym'=>'string']);
                    }
            
                }
            
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $records);
                $this->_view->assign('pageid', $pageid);
                $this->_view->assign('pagesize', $pagesize);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('ymdFrom', $ymdFrom);
                $this->_view->assign('ymdTo', $ymdTo);
                $this->_view->assign('where', urlencode(json_encode($where)));
                $this->_view->assign('superuesrrecharge', $superuesrrecharge);
                $this->_view->assign('normaluesrrecharge', $normaluesrrecharge);
                $this->_view->assign('superuesrwithdraw', $superuesrwithdraw);
                $this->_view->assign('normaluesrwithdraw', $normaluesrwithdraw);
                $this->_view->assign('rechargeAmount', $rechargeAmount);
                $this->_view->assign('withdrawAmount', $withdrawAmount);
            
            }

            public function rechdrawdayAction () {
                $isDownloadExcel = $this->_request->get('__EXCEL__');
                
                $ymdFrom = $this->_request->get('ymdFrom');
                $ymdTo = $this->_request->get('ymdTo');
                
                $pageid = $this->_request->get('pageId',1)-0;
                $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum01))-0;
                $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum01,false);
                $dt_instance = \Sooh\Base\Time::getInstance();
                $ymdFromDefault='2014-06-30';
               
                $ymdToDefault=date('Y-m-d');
                $wheretrue=$this->_request->get('where');
                $pkey_val=$this->_request->get('_pkey_val_');
                $pkey_val_=explode('-',$pkey_val);
                $pkey_val_year=$pkey_val_[0];
                $pkey_val_month=$pkey_val_[1];
                if($pkey_val_month==date('m')){
                    $pkey_val_day=date('d');
                }else{
                    $pkey_val_day=\Rpt\Funcs::formday($pkey_val_month,$pkey_val_year);
                }
                $detailswheretrue=$this->_request->get('detailswhere');
                //var_log($pkey_val,'pkeyVal###########');
                
                if(empty($ymdFrom) && empty($ymdTo) && !$isDownloadExcel && empty($pkey_val)) {
                    $ymdTo = $ymdToDefault;
                    $ymdFrom = $ymdFromDefault;
                }elseif(!empty($pkey_val) && !$isDownloadExcel){
                    $ymdFrom=$pkey_val_year.'-'.$pkey_val_month.'-'.'01';
                    $ymdTo=$pkey_val_year.'-'.$pkey_val_month.'-'.$pkey_val_day;
                }elseif(!empty($ymdFrom) && !empty($ymdTo)){
                    $FromDefault_year='2014';
                    $FromDefault_month='06';
                    $FromDefault_day='30';
                    $ToDefault_year=date('Y');
                    $ToDefault_month=date('m');
                    $ToDefault_day=date('d');
                    
                    $ymdFrom_year=date('Y',strtotime($ymdFrom));
                    $ymdFrom_month=date('m',strtotime($ymdFrom));
                    $ymdFrom_day=date('d',strtotime($ymdFrom));
                    
                    $ymdTo_year=date('Y',strtotime($ymdTo));
                    $ymdTo_month=date('m',strtotime($ymdTo));
                    $ymdTo_day=date('d',strtotime($ymdTo));
                    
                    if($ymdFrom_year<$FromDefault_year){
                        $ymdFrom_year=$FromDefault_year;
                        $ymdFrom_month=$FromDefault_month;
                        $ymdFrom_day=$FromDefault_day;
                    }elseif($ymdFrom_year==$FromDefault_year && $ymdFrom_month<$FromDefault_month){
                        $ymdFrom_month=$FromDefault_month;
                        $ymdFrom_day=$FromDefault_day;
                    }elseif($ymdFrom_year==$FromDefault_year && $ymdFrom_month==$FromDefault_month && $ymdFrom_day<$FromDefault_day){
                        $ymdFrom_day=$FromDefault_day;
                    }elseif($ymdTo_year>$ToDefault_year){
                        $ymdTo_year=$ToDefault_year;
                        $ymdTo_month=$ToDefault_month;
                        $ymdTo_day=$ToDefault_day;
                    }elseif($ymdTo_year==$ToDefault_year && $ymdTo_month>$ToDefault_month){
                        $ymdTo_month=$ToDefault_month;
                        $ymdTo_day=$ToDefault_day;
                    }elseif($ymdTo_year==$ToDefault_year && $ymdTo_month==$ToDefault_month && $ymdTo_day>$ToDefault_day){
                        $ymdTo_day=$ymdTo_day;
                    }
                     
                    $ymdFrom=$ymdFrom_year.'-'.$ymdFrom_month.'-'.$ymdFrom_day;
                    $ymdTo=$ymdTo_year.'-'.$ymdTo_month.'-'.$ymdTo_day;
                }
                
                
                if(!\Rpt\Funcs::check_date($ymdFrom) || !\Rpt\Funcs::check_date($ymdTo)){
                    $this->returnError('日期格式错误');
                }
                if(strtotime($ymdTo)< strtotime($ymdFrom)) {
                    $this->returnError('起始日期不应该晚于截止日期');
                }
                
                $ymdFromDBFormat = date('Ymd' ,strtotime($ymdFrom));
                $ymdToDBFormat = date('Ymd', strtotime($ymdTo));
                
                $where['ymdForm]']=$ymdFromDBFormat;
                $where['ymdTo[']=$ymdToDBFormat;
                
               
                $db_p2p=\Sooh\DB\Broker::getInstance('produce');
                $db_rpt= \Sooh\DB\Broker::getInstance('dbForRpt');
                
                $fielsdMap=[
                    'ymd'=>['日期',null],
                    'superuesrrecharge'=>['超级用户充值',null],
                    'normaluesrrecharge'=>['普通用户充值',null],
                    'superuesrwithdraw'=>['超级用户提现',null],
                    'normaluesrwithdraw'=>['普通用户提现',null],
                    'rechargeAmount'=>['充值总金额',null],
                    'withdrawAmount'=>['提现总金额',null],
                    'withdrawcount'=>['当日提现笔数',null],
                    'winwithdrawcount'=>['当日成功提现笔数',null],
                    'withdrawusvoucher'=>['当日成功提现用券数量',null],
                    'copAmount'=>['公司手续费',null],
                ];
                
                $headers = [];
                foreach($fielsdMap as $r) {
                    $headers[$r[0]] = $r[1];
                }
                
                $fielsdMap01=[
                    'ymd'=>['日期','40'],
                    'userId'=>['用户ID','40'],
                    'realname'=>['姓名', '30'],
                    'phone'=>['手机号','40'],
                    'optime'=>['操作时间','50'],
                    'opways'=>['操作方式','30'],
                    'amount'=>['金额','40'],
                ];
                
                $headers01 = [];
                foreach($fielsdMap01 as $r) {
                    $headers01[$r[0]] = $r[1];
                }
                
                if($isDownloadExcel){
                    if(!empty($wheretrue)){
                        $sql2="SELECT
                        ymd,
                        SUM(CASE when flagUser=1 and summary='充值'  then amount/100 END) as t1,
                        SUM(CASE when flagUser!=1 and summary='充值' then amount/100 END) as t2,
                        SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as t3,
                        SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as t4,
                        SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as t5,
                        SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as t6,
                        COUNT(CASE when summary='提现' AND orderStatus=39 THEN userId END) as t7
                        from tb_user_final as a LEFT JOIN tb_recharges_final as b
                        USING(userId)
                        WHERE
                        ymd>={$wheretrue['ymdForm']}
                        and ymd<={$wheretrue['ymdTo[']}
                        GROUP BY ymd
                        ORDER BY ymd desc";
                        
                        $sql2=$db_rpt->execCustom(['sql'=>$sql2]);
                        $sql2=$db_rpt->fetchAssocThenFree($sql2);
                        
                        // var_log($sql2,'sql22##########');
                        
                        if($wheretrue['ymdTo[']>=date('Ymd') && $wheretrue['ymdForm']<=date('Ymd')){
                            $onetime=date('Ymd');
                            $sql0=<<<sql
                                SELECT 
                                SUM(CASE when flagUser=1 and summary='提现'  and orderStatus<>4 then amount/100 END)as supertx,
                                SUM(CASE when flagUser!=1 and summary='提现' and orderStatus<>4  then amount/100 END) as normaltx,
                                SUM(CASE when summary='提现' and orderStatus<>4 then amount/100 END) as txamount,
                                COUNT(CASE when summary='提现' AND orderStatus<>4 THEN userId END) as txcount
                                FROM tb_recharges_final as a LEFT JOIN
                                tb_user_final as b
                                USING(userId)
                                where
                                a.ymd like '{$onetime}%';
sql;
                            $sql00=$db_p2p->execCustom(['sql'=>$sql0]);
                            $sql00=$db_p2p->fetchAssocThenFree($sql00);
                        }
                        
                        
                        foreach ($sql2 as $v){
                            if($v['ymd']==date('Ymd')){
                                if(!empty($sql00)){
                                foreach ($sql00 as $vv){
                                    $v['t3']=$vv['supertx'];
                                    $v['t4']=$vv['normaltx'];
                                    $v['t6']=$vv['txamount'];
                                    $v['t7']=$vv['txcount'];
                                }
                              }
                            }
                            $sql22[]=$v;
                        }
                        
                        $sql3="SELECT
                        finishYmd,
                        COUNT(CASE WHEN summary='提现' THEN userId END) as t8
                        from tb_recharges_final  as a LEFT JOIN tb_user_final as b
                        USING(userId)
                        WHERE
                        finishYmd>={$wheretrue['ymdForm']}
                        AND finishYmd<={$wheretrue['ymdTo[']}
                        AND orderStatus=39
                        GROUP BY finishYmd
                        ORDER BY finishYmd DESC";
                        
                        $sql3=$db_rpt->execCustom(['sql'=>$sql3]);
                        $sql3=$db_rpt->fetchAssocThenFree($sql3);
                        //var_log($sql3,'sql3333333333##########');
                        
                        foreach ($sql22 as $v){
                        foreach ($sql3 as $vv){
                        if($vv['finishYmd']==$v['ymd']){
                        $v['t8']=$vv['t8'];
                        }
                        
                        }
                        $sql4[]=$v;
                        }
                         
                        $handle_date1=date('Y-m-d',strtotime($where['ymdForm]'])).' 00:00:00';
                        $handle_date2=date('Y-m-d',strtotime($where['ymdTo['])).' 23:59:59';
                        
                        $sql5="SELECT
                        handle_date,
                        amount/100 as amount,
                        COUNT(*) as count
                        from customer_coupon WHERE
                        handle_date>='{$handle_date1}'
                        and handle_date<='{$handle_date2}'
                        AND type=4
                        AND `status`=1
                        GROUP BY handle_date
                        ORDER BY handle_date
                        ";
                        $sql5=$db_p2p->execCustom(['sql'=>$sql5]);
                        $sql5=$db_p2p->fetchAssocThenFree($sql5);
        
                        foreach ($sql5 as $k=>$v){
                        $dtime=date('Ymd',strtotime($v['handle_date']));
                        $v['handle_date']=$dtime;
                        $sql6[$k]['handle_date']=$v['handle_date'];
                        $sql6[$k]['count']+=$v['count'];
                        }
                        
                        
                        foreach ($sql4 as $v){
                        foreach ($sql6 as $vv){
                        if($vv['handle_date']==$v['ymd']){
                        $v['t9']+=$vv['count'];
                        }
                        }
                        $sql7[]=$v;
                        }
                        
                        foreach ($sql7 as $v){

                        $v['t10']=$v['t9']*1.5;
                         
                        
                        $rs[]=[
                        'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                        'superuesrrecharge'=>sprintf("%.2f",$v['t1']),//round($v['t1'],2),
                        'normaluesrrecharge'=>sprintf("%.2f",$v['t2']),
                        'superuesrwithdraw'=>sprintf("%.2f",abs($v['t3'])),
                        'normaluesrwithdraw'=>sprintf("%.2f",abs($v['t4'])),
                        'rechargeAmount'=>sprintf("%.2f",$v['t5']),
                        'withdrawAmount'=>sprintf("%.2f",abs($v['t6'])),
                        'withdrawcount'=>$v['t7'],
                        'winwithdrawcount'=>$v['t8'],
                        'withdrawusvoucher'=>$v['t9'],
                        'copAmount'=>sprintf("%.2f",$v['t10']),
                         ];
                        
                        }
                    }elseif (!empty($detailswheretrue)){
                      
                            $sql1="SELECT
                            ymd,
                            userId,
                            realname,
                            phone,
                            summary,
                            ymd,
                            hhiiss,
                            amount/100 as amount
                            FROM
                            tb_recharges_final AS a
                            LEFT JOIN tb_user_final AS b USING (userId)
                            WHERE
                            ymd >={$detailswheretrue['ymdForm']}
                            AND ymd<={$detailswheretrue['ymdTo']}
                            AND orderStatus=39
                            ORDER BY ymd desc";
                            $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                            $sql1=$db_rpt->fetchAssocThenFree($sql1);
                          
                         if($detailswheretrue['ymdTo']>=date('Ymd') && $detailswheretrue['ymdForm']<=date('Ymd')){
                                $onetime=date('Ymd');
                                
                                $sql2=<<<sql
                                SELECT
                                ymd,
                                userId,
                                realname,
                                phone,
                                summary,
                                ymd,
                                hhiiss,
                                amount/100 as amount
                                FROM
                                tb_recharges_final AS a
                                LEFT JOIN tb_user_final AS b USING (userId)
                                WHERE
                                ymd={$onetime}
                                AND orderStatus<>4
                                ORDER BY ymd desc
sql;
                                $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                                $sql2=$db_p2p->fetchAssocThenFree($sql2);
                            }
                            
                            if(!empty($sql2)){
                            foreach ($sql2 as $v){
                                $v['ymd']=date('Ymd');
                                $sql22[]=$v;
                            }
                            }
                           
                            foreach ($sql1 as $v){
                                if($v['ymd']==date('Ymd')){
                                    continue;
                                }
                                 
                                $one[]=$v;
                            }
                            
                            if(isset($sql22)){
                             $sql11=array_merge($sql22,$one2);
                            }else{
                             $sql11=$one;
                            }
                          
                            
                            foreach ($sql11 as $v){
                               
                                $v['ymd']=$this->YmdhhiissConv($v['ymd'], $v['hhiiss']);
        
                                $rs01[]=[
                                    'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                                    'userId'=>$v['userId'],
                                    'realname'=>$v['realname'],
                                    'phone'=>$v['phone'],
                                    'optime'=>$v['ymd'],
                                    'opways'=>$v['summary'],
                                    'amount'=>sprintf("%.2f",abs($v['amount'])),
                                ];
                            }
                        
                        }    
                }else{
                    
                  $detailswhere=['ymdForm'=>$where['ymdForm]'],'ymdTo'=>$where['ymdTo[']];
                  
                  if(date('Ymd')>=$where['ymdForm]'] && date('Ymd')<=$where['ymdTo[']){
                      $onetime=date('Ymd');
                      $sql_all_one="SELECT
                      ymd,
                      SUM(CASE when flagUser=1 and summary='充值'  and orderStatus=39 then amount/100 END) as t1,
                      SUM(CASE when flagUser!=1 and summary='充值' and orderStatus=39 then amount/100 END) as t2,
                      SUM(CASE when flagUser=1 and summary='提现'  and orderStatus<>4 then amount/100 END) as t3,
                      SUM(CASE when flagUser!=1 and summary='提现' and orderStatus<>4  then amount/100 END) as t4,
                      SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as t5,
                      SUM(CASE when  summary='提现'  and orderStatus<>4  then amount/100 END) as t6
                      from tb_user_final as a LEFT JOIN tb_recharges_final as b
                      USING(userId)
                      WHERE
                      ymd={$onetime}
                      group by ymd
                      ";
                      
                      $sql_all_one=$db_rpt->execCustom(['sql'=>$sql_all_one]);
                      $sql_all_one=$db_rpt->fetchAssocThenFree($sql_all_one);
                  }
                  
                      $sql_all_two="SELECT
                      ymd,
                      SUM(CASE when flagUser=1 and summary='充值'  and orderStatus=39 then amount/100 END) as t1,
                      SUM(CASE when flagUser!=1 and summary='充值' and orderStatus=39 then amount/100 END) as t2,
                      SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as t3,
                      SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as t4,
                      SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as t5,
                      SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as t6
                      from tb_user_final as a LEFT JOIN tb_recharges_final as b
                      USING(userId)
                      WHERE
                      ymd>={$where['ymdForm]']}
                      and ymd<={$where['ymdTo[']}
                      group by ymd
                      ";
                      
                      $sql_all_two=$db_rpt->execCustom(['sql'=>$sql_all_two]);
                      $sql_all_two=$db_rpt->fetchAssocThenFree($sql_all_two);
                  
               
                  if(!empty($sql_all_two)){
                  foreach ($sql_all_two as $v){
                    if(date('Ymd')==$v['ymd']){
                        if(!empty($sql_all_one)){
                            foreach ($sql_all_one  as $vv){
                                $v['t3']=$vv['t3'];
                                $v['t4']=$vv['t4'];
                                $v['t6']=$vv['t6'];
                            }
                        }
                    }
                    
                    $superuesrrecharge+=$v['t1'];
                    $normaluesrrecharge+=$v['t2'];
                    $superuesrwithdraw+=abs($v['t3']);
                    $normaluesrwithdraw+=abs($v['t4']);
                    $rechargeAmount+=$v['t5'];
                    $withdrawAmount+=abs($v['t6']);
                  }
                }
                  
                if(strtotime(date('Y-m-d'))>=strtotime($where['ymdForm]']) && strtotime($where['ymdTo['])>strtotime(date('Y-m-d'))){
                    $where['ymdForm]']=$where['ymdForm]'];
                    $where['ymdTo[']=date('Ymd');
                }
                  $date_rem=self::choose_date($where['ymdForm]'],$where['ymdTo[']);
                  arsort($date_rem);
                  $pager->init(Count($date_rem), $pageid);
                  $rsform=$pager->rsFrom();
                  $date_rem=array_slice($date_rem, $rsform,$pagesize);
                 
                  $date_To=current($date_rem);
                  $date_Form=end($date_rem);
                 
                  
                    $sql2="SELECT 
                            ymd,
                            SUM(CASE when flagUser=1 and summary='充值'  then amount/100 END) as t1,
                            SUM(CASE when flagUser!=1 and summary='充值' then amount/100 END) as t2,
                            SUM(CASE when flagUser=1 and summary='提现'  and orderStatus=39 then amount/100 END) as t3,
                            SUM(CASE when flagUser!=1 and summary='提现' and orderStatus=39  then amount/100 END) as t4,
                            SUM(CASE when  summary='充值' and orderStatus=39  then amount/100 END) as t5,
                            SUM(CASE when  summary='提现'  and orderStatus=39  then amount/100 END) as t6,
                            COUNT(CASE when summary='提现' AND orderStatus=39 THEN userId END) as t7
                            from tb_user_final as a LEFT JOIN tb_recharges_final as b
                            USING(userId)
                            WHERE
                            ymd>={$date_Form} 
                            and ymd<={$date_To}
                            GROUP BY ymd
                            ORDER BY ymd desc";
                    
                    $sql2=$db_rpt->execCustom(['sql'=>$sql2]);
                    $sql2=$db_rpt->fetchAssocThenFree($sql2);
                   // var_log($sql2,'sql00############');
                    
                    if($date_Form<=date('Ymd') && $date_To>=date('Ymd')){
                        $onetime=date('Ymd');
                        $sql0=<<<sql
                                SELECT 
                                SUM(CASE when flagUser=1 and summary='提现'  and orderStatus<>4 then amount/100 END)as supertx,
                                SUM(CASE when flagUser!=1 and summary='提现' and orderStatus<>4  then amount/100 END) as normaltx,
                                SUM(CASE when  summary='提现'  and orderStatus<>4  then amount/100 END) as txamount,
                                COUNT(CASE when summary='提现' AND orderStatus<>4 THEN userId END) as txcount
                                from tb_user_final as a LEFT JOIN tb_recharges_final as b
                                USING(userId)
                                where
                                ymd like '{$onetime}%';
sql;
                        $sql00=$db_rpt->execCustom(['sql'=>$sql0]);
                        $sql00=$db_rpt->fetchAssocThenFree($sql00);
                    }
                    
                    
                    foreach ($sql2 as $v){
                       if($v['ymd']==date('Ymd')){
                          if(!empty($sql00)){
                           foreach ($sql00 as $vv){
                               $v['t3']=$vv['supertx'];
                               $v['t4']=$vv['normaltx'];
                               $v['t6']=$vv['txamount'];
                               $v['t7']=$vv['txcount'];
                           }
                          }
                       }
                       $sql22[]=$v;
                    }
                    
                    $sql3="SELECT 
                            finishYmd,
                            COUNT(CASE WHEN summary='提现' THEN userId END) as t8 
                            from tb_recharges_final  as a LEFT JOIN tb_user_final as b 
                            USING(userId)
                            WHERE
                            finishYmd>={$date_Form}
                            AND finishYmd<={$date_To}
                            AND orderStatus=39
                            GROUP BY finishYmd
                            ORDER BY finishYmd DESC";
                    
                    $sql3=$db_rpt->execCustom(['sql'=>$sql3]);
                    $sql3=$db_rpt->fetchAssocThenFree($sql3);
                   //var_log($sql3,'sql3333333333##########');
                    
                    foreach ($sql22 as $v){
                       foreach ($sql3 as $vv){
                           if($vv['finishYmd']==$v['ymd']){
                               $v['t8']=$vv['t8'];
                           }
                              
                       }
                       $sql4[]=$v;
                    }
                   
                    $handle_date1=date('Y-m-d',strtotime($date_Form)).' 00:00:00';
                    $handle_date2=date('Y-m-d',strtotime($date_To)).' 23:59:59';
                    
                    $sql5="SELECT
                            handle_date,
                            amount/100 as amount,
                            COUNT(*) as count
                            from customer_coupon WHERE
                            handle_date>='{$handle_date1}'
                            and handle_date<='{$handle_date2}'
                            AND type=4
                            AND `status`=1
                            GROUP BY handle_date
                            ORDER BY handle_date
                           ";
                    $sql5=$db_p2p->execCustom(['sql'=>$sql5]);
                    $sql5=$db_p2p->fetchAssocThenFree($sql5);

                    foreach ($sql5 as $k=>$v){
                        $dtime=date('Ymd',strtotime($v['handle_date']));
                        $v['handle_date']=$dtime;
                       $sql6[$k]['handle_date']=$v['handle_date']; 
                       $sql6[$k]['count']+=$v['count'];
                    }

                    
                    foreach ($sql4 as $v){
                        foreach ($sql6 as $vv){
                            if($vv['handle_date']==$v['ymd']){
                                $v['t9']+=$vv['count'];
                            }
                        }
                        $sql7[]=$v;
                      }
                      
                      
                      foreach ($sql7 as $v){

                         $v['t10']=$v['t9']*1.5;
                       
                          $rs[]=[
                              'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                              'superuesrrecharge'=>sprintf("%.2f",$v['t1']),//round($v['t1'],2),
                              'normaluesrrecharge'=>sprintf("%.2f",$v['t2']),
                              'superuesrwithdraw'=>sprintf("%.2f",abs($v['t3'])),
                              'normaluesrwithdraw'=>sprintf("%.2f",abs($v['t4'])),
                              'rechargeAmount'=>sprintf("%.2f",$v['t5']),
                              'withdrawAmount'=>sprintf("%.2f",abs($v['t6'])),
                              'withdrawcount'=>$v['t7'],
                              'winwithdrawcount'=>$v['t8'],
                              'withdrawusvoucher'=>$v['t9'],
                              'copAmount'=>sprintf("%.2f",$v['t10']),
                          ];
                          
                      }
                 //   var_log($sql10,'slq7#################');                   
                    
                }

                if($isDownloadExcel){
                    if(!empty($rs)){
                        return $this->downExcel($rs, array_keys($headers));
                    }elseif(!empty($rs01)){
                        return $this->downExcel($rs01, array_keys($headers01));
                    }
                  
                }
                
                $this->_view->assign('headers', $headers);
                $this->_view->assign('records', $rs);
                $this->_view->assign('pageid', $pageid);
                $this->_view->assign('pagesize', $pagesize);
                $this->_view->assign('pager', $pager);
                $this->_view->assign('ymdFrom', $ymdFrom);
                $this->_view->assign('ymdTo', $ymdTo);
                $this->_view->assign('where', $where); 
                $this->_view->assign('detailswhere', $detailswhere);
                $this->_view->assign('superuesrrecharge', $superuesrrecharge);
                $this->_view->assign('normaluesrrecharge', $normaluesrrecharge);
                $this->_view->assign('superuesrwithdraw', $superuesrwithdraw);
                $this->_view->assign('normaluesrwithdraw', $normaluesrwithdraw);
                $this->_view->assign('rechargeAmount', $rechargeAmount);
                $this->_view->assign('withdrawAmount', $withdrawAmount);
                
        }
        
        
        public function chardrawdetailsAction () {
 
            $rechdraw = $this->_request->get('_pkey_val_');
            $rechdrawone=date('Ymd',strtotime($rechdraw));
        
            $isDownloadExcel = $this->_request->get('__EXCEL__');
            $pageid = $this->_request->get('pageId',1)-0;
            $pagesize = $this->_request->get('pageSize',current($this->pageSizeEnum02))-0;
            $pager = new \Sooh\DB\Pager($pagesize,$this->pageSizeEnum02,false);
            $dt_instance = \Sooh\Base\Time::getInstance();
           
            $ymdFrom=date('Y-m-d',strtotime($rechdraw));
            $ymdTo=date('Y-m-d',strtotime($rechdraw));
            $wheretrue=$this->_request->get('where');
          
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
            }else{
                $where['ymdForm]']=$rechdrawone;
            }
            if($where['ymdTo[']){
                $where['ymdTo['] = date('Ymd', strtotime($where['ymdTo[']));
            }else{
                $where['ymdTo[']=$rechdrawone;
            }
  
            if($where['ymdTo[']< $where['ymdForm]']) {
                   return $this->returnError('起始日期应该大于结束日期');
              }
              
          //  var_log($rechdraw,'where###################');
           // var_log($where,'where###################');
        
            $db_p2p=\Sooh\DB\Broker::getInstance('produce');
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
              
                    $sql="SELECT
                    ymd,
                    userId,
                    realname,
                    phone,
                    summary,
                    ymd,
                    hhiiss,
                    amount/100 as amount
                    FROM
                    tb_recharges_final AS a
                    LEFT JOIN tb_user_final AS b USING (userId)
                    WHERE
                    ymd >={$wheretrue['ymdForm']}
                    AND ymd<={$wheretrue['ymdTo[']}
                    AND orderStatus=39
                    ORDER BY hhiiss desc";
                    $sql=$db_rpt->execCustom(['sql'=>$sql]);
                    $sql=$db_rpt->fetchAssocThenFree($sql);
                    
              if($wheretrue['ymdTo[']>=date('Ymd') && $wheretrue['ymdForm]']<=date('Ymd')){
                        $onetime=date('Ymd');
                        
                        $sql2=<<<sql
                        SELECT
                        ymd,
                        userId,
                        realname,
                        phone,
                        summary,
                        ymd,
                        hhiiss,
                        amount/100 as amount
                        FROM
                        tb_recharges_final AS a
                        LEFT JOIN tb_user_final AS b USING (userId)
                        WHERE
                        ymd={$onetime}
                        AND orderStatus<>4
                        ORDER BY hhiiss desc
sql;
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                        
                        if(!empty($sql2)){
                        foreach ($sql2 as $v){
                            $v['ymd']=date('Ymd');
                            $sql22[]=$v;
                        }
                        }
                        
                     
                        if(!empty($sql22)){
                            $sql1=$sql22;
                        }else{
                            $sql1=[
                                'userId'=>'',
                                'realname'=>'',
                                'phone'=>'',
                                'create_time'=>'',
                                'summary'=>'',
                                'amount'=>'',
                                'ymd'=>''
                            ];
                        }
                    }
                    
                    foreach ($sql as $v){
                        if($v['ymd']==date('Ymd')){
                            continue;
                        }
                        $one[]=$v;
                    }
                    
                    if(isset($sql1)){
                     $sqlone=array_merge($sql1,$one); 
                    }else{
                     $sqlone=$one;
                    }
                   
                    foreach ($sqlone as $v){
                    $v['ymd']=$this->YmdhhiissConv($v['ymd'], $v['hhiiss']);
                    $rs[]=[
                        'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                        'userId'=>$v['userId'],
                        'realname'=>$v['realname'],
                        'phone'=>$v['phone'],
                        'optime'=>$v['ymd'],
                        'opways'=>$v['summary'],
                        'amount'=>sprintf("%.2f",abs($v['amount'])),
                    ];
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
                    amount/100 as amount
                    FROM
                    tb_recharges_final AS a
                    LEFT JOIN tb_user_final AS b USING (userId)
                    WHERE
                    ymd>={$where['ymdForm]']}
                    AND ymd<={$where['ymdTo[']}
                    AND orderStatus=39
                    ORDER BY hhiiss desc";
                     
                    $sql=$db_rpt->execCustom(['sql'=>$sql]);
                    $sql=$db_rpt->fetchAssocThenFree($sql);
                    
                  if($where['ymdTo[']>=date('Ymd') && $where['ymdForm]']<=date('Ymd')){
                        $onetime=date('Ymd');
                        
                        $sql2=<<<sql
                        SELECT
                        ymd,
                        userId,
                        realname,
                        phone,
                        summary,
                        ymd,
                        hhiiss,
                        amount/100 as amount
                        FROM
                        tb_recharges_final AS a
                        LEFT JOIN tb_user_final AS b USING (userId)
                        WHERE
                        ymd={$onetime}
                        AND orderStatus<>4
                        ORDER BY hhiiss desc
sql;
                        $sql2=$db_p2p->execCustom(['sql'=>$sql2]);
                        $sql2=$db_p2p->fetchAssocThenFree($sql2);
                        
                        if(!empty($sql2)){
                        foreach ($sql2 as $v){
                            $v['ymd']=date('Ymd');
                            $sql22[]=$v;
                        }
                        }
                        
                     
                        if(!empty($sql22)){
                            $sql1=$sql22;
                        }else{
                            $sql1=[
                                'userId'=>'',
                                'realname'=>'',
                                'phone'=>'',
                                'create_time'=>'',
                                'summary'=>'',
                                'amount'=>'',
                                'ymd'=>''
                            ];
                        }
                    }
                    
                    foreach ($sql as $v){
                        if($v['ymd']==date('Ymd')){
                            continue;
                        }
                        $one[]=$v;
                    }
                    
                    if(isset($sql1)){
                      $sqlone=array_merge($sql1,$one);
                    }else{
                      $sqlone=$one;
                    }
                   
                    $pager->init(Count($sqlone), $pageid);
                    $rsform=$pager->rsFrom();
                    
                    $sqlone=array_slice($sqlone,$rsform,$pagesize);
                    
                    foreach ($sqlone as $v){
                        if($v['ymd']==date('Ymd')){
                            $v['ymd']=$v['create_time'];
                        }else{
                            $v['ymd']=$this->YmdhhiissConv($v['ymd'], $v['hhiiss']);
                        }
                        $rs[]=[
                            'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                            'userId'=>$v['userId'],
                            'realname'=>$v['realname'],
                            'phone'=>$v['phone'],
                            'optime'=>$v['ymd'],
                            'opways'=>$v['summary'],
                            'amount'=>sprintf("%.2f",abs($v['amount'])),
                        ];
                    }
                     
                }else{
     
                        if($rechdrawone==date('Ymd')){

                            $onetime=date('Ymd',strtotime($rechdrawone));

                            $sql2=<<<sql
                            SELECT
                            ymd,
                            userId,
                            realname,
                            phone,
                            summary,
                            ymd,
                            hhiiss,
                            amount/100 as amount
                            FROM
                            tb_recharges_final AS a
                            LEFT JOIN tb_user_final AS b USING (userId)
                            WHERE
                            ymd={$onetime}
                            AND orderStatus<>4
                            ORDER BY hhiiss desc
sql;
                            $sql2=$db_p2p->execCustom(['sql'=>$sql3]);
                            $sql2=$db_p2p->fetchAssocThenFree($sql3);
                            
                            if(!empty($sql2)){
                            foreach ($sql2 as $v){
                                $v['ymd']=date('Ymd');
                                $sql22[]=$v;
                            }
                            }
                            
                         
                            if(!empty($sql22)){
                              $sql1=$sql22;
                            }else{
                                $sql1=[
                                    'userId'=>'',
                                    'realname'=>'',
                                    'phone'=>'',
                                    'create_time'=>'',
                                    'summary'=>'',
                                    'amount'=>'',
                                    'ymd'=>''
                                ];
                            } 
                           
                        }else{
                            $sql1="SELECT
                            ymd,
                            userId,
                            realname,
                            phone,
                            summary,
                            ymd,
                            hhiiss,
                            amount/100 as amount
                            FROM
                            tb_recharges_final AS a
                            LEFT JOIN tb_user_final AS b USING (userId)
                            WHERE
                            ymd={$rechdrawone}
                            AND orderStatus=39
                            ORDER BY hhiiss desc";
                            $sql1=$db_rpt->execCustom(['sql'=>$sql1]);
                            $sql1=$db_rpt->fetchAssocThenFree($sql1);
                        }
                       
                      
                    
                    $pager->init(Count($sql1), $pageid);
                    $rsform=$pager->rsFrom();
                    $sql1=array_slice($sql1,$rsform,$pagesize);
                    
                    foreach ($sql1 as $v){

                    $v['ymd']=$this->YmdhhiissConv($v['ymd'], $v['hhiiss']);
                      
                    $rs[]=[
                        'ymd'=>date('Y-m-d',strtotime($v['ymd'])),
                        'userId'=>$v['userId'],
                        'realname'=>$v['realname'],
                        'phone'=>$v['phone'],
                        'optime'=>$v['ymd'],
                        'opways'=>$v['summary'],
                        'amount'=>sprintf("%.2f",abs($v['amount'])),
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
        
        public function  check_date($ym){
            if(strpos($ym, '-')==false && sizeof(explode('-', $ym))!=2) {
                return false;
            }else{
                return true;
            }
             
        }
         
        public  function choose_date($dateForm,$dateTo){
            $dateForm_dafault='20140630';
            $dateTo_dafault=date('Ymd');
            empty($dateForm)?$dateForm_dafault:$dateForm;
            empty($dateTo)?$dateTo_dafault:$dateTo;
            $stance=(strtotime($dateTo)-strtotime($dateForm))/86400;
            $rs=[];
            for ($i=0;$i<=$stance;$i++){
                $date_stance=strtotime($dateForm)+86400*$i;
                $date_rs=date('Ymd',$date_stance);
                $rs[]=$date_rs;
            }
            return $rs;
        }
        
}