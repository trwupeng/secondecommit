<?php

use Prj\ManagerCtrl;
use Prj\Data\MBMessage as Message;
use Prj\Data\MBPerfReply as Reply;
use Prj\Data\MBPerfDst as PerfDst;
use Prj\Misc\PlanGlobal as planDefine;


class PerfdailylogController extends ManagerCtrl
{
    public function init()
    {
        parent::init();
        if ($this->_request->get('__VIEW__') == 'json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    public function indexAction()
    {
        $this->_view->assign('targetTab', $this->getRequest()->get('targetTab', ''));
        $this->_view->assign('jumpParams', json_decode(urldecode($this->getRequest()->get('jumpParams', '')), true));
        
        $pager = new \Sooh\DB\Pager(1000);
        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $loginName=explode('@', $this->session->get('managerId'))[0];
        $loginName=\Prj\Data\Manager::getName($loginName);
        
         $userid=$this->session->get('managerId');
        
         $date_ji=ceil(date('m',strtotime($date)) / 3);
         $date_month=date('m',strtotime($date));
         $date_week=date('W', strtotime($date));
         $date_day=date('z', strtotime($date));
         $date_where=[$date_ji,$date_month,$date_week,$date_day];
         $PerfDst_where=['userid'=>$userid,'del'=>0,'type_num'=>$date_where];
         $PerfDst_ret=PerfDst::loopFindRecords($PerfDst_where);
         
        $work_name = [ 1=>[], 2=>[], 3=>[], 4=>[]];
        if($PerfDst_ret){
            foreach ($PerfDst_ret as $v){
                if($v['type']==4){
                    $PerfDst_ji=PerfDst::loopFindRecords(['userid'=>$userid,'target_ji'=>$v['id'],'type'=>1]);
                    if($PerfDst_ji)continue;
                }elseif($v['type']==3){
                    $PerfDst_yue=PerfDst::loopFindRecords(['userid'=>$userid,'target_yue'=>$v['id'],'type'=>1]);
                    if($PerfDst_yue)continue;
                }elseif($v['type']==2){
                    $PerfDst_week=PerfDst::loopFindRecords(['userid'=>$userid,'target_week'=>$v['id'],'type'=>1]);
                    if($PerfDst_week)continue;
                }
                    $ret[]=$v;
            }
            
            foreach ($ret as $v){
                $work_name[$v['type']][$v['id']] = $v['name'];
            }
        }else{
            $work_name_nomoment='暂无原始计划';
            $work_name[$work_name_nomoment]=$work_name_nomoment;
        }
        foreach ( $work_name as $k=>$v )
        {
            if ( count($v) == 0 )
            {
                $v[0] = '无';
                $work_name[$k] = $v;
            }
        }
        
        $data = \Prj\Data\MBPerfDailylog::paged($pager, [
            'log_date' => $date,
            'userid'   => $this->session->get('managerId'),
        ]);
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback('perflogOnBeforeEdit', null, 'perflogOnEditEnabled' );
        $view->setHtmlTableId( 'perflog_table_id' );
        
        $view->setPk('id')
             ->setData($data)
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', '', 'text', [], planDefine::$width['id'], ['readOnly' => 1])
             ->addRow('settime', '日期', '', 'select',[$date=>$date], planDefine::$width['date'])
             ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], planDefine::$width['name'])
             ->addRow('type', '类型', '', 'select', [1=>'日目标',2=>'周目标',3=>'月目标',4=>'季目标'], planDefine::$width['dsttype'], ['usercall'=>'selectorTypeCallback'])//[1 => '原始计划', 2 => '临时计划']
             ->addRow('name', '项目', '', 'select', $work_name[1], planDefine::$width['dstname'])
             ->addRow('content', '工作内容', '', 'textarea', [], planDefine::$width['content'])
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], planDefine::$width['level'])
             ->addRow('plan_cost', '实际用时(小时)', '', 'text', [], planDefine::$width['cost'])
             ->addRow('finish', '完成情况', '', 'select', ['0%'=>'0%','10%' => '10%', '20%'=>'20%','30%' => '30%','40%'=>'40%', '50' => '50%','60%'=>'60%', '70%'=>'70%','80%'=>'80%','90%'=>'90%','100%'=>'100%'], planDefine::$width['percent'])
             ->addRow('finish_reason', '完成情况的说明', '', 'text', [], planDefine::$width['desc']);
 
        $this->_view->assign('date', $date);
        $this->_view->assign('view', $view->showPerf());
        $this->_view->assign( 'nameList', json_encode($work_name) );
    }

    public function tabhistorylogAction()
    {
        
        $quest_userid=$this->_request->get('message_sendid','');
        $quest_userid=urldecode($quest_userid);
        $login_userid=$this->session->get('managerId');
        $quest_batch_type=$this->_request->get('batch_type','');
        $quest_flag=$this->_request->get('flag','');
        $quest_perf_id=$this->_request->get('perf_id','');
        $quest_reply_id=$this->_request->get('reply_id','');
        $quest_typeid=$this->_request->get('weekNums','');
        $quest_message_id=$this->_request->get('message_id','');
        $quest_message_type=$this->_request->get('message_type','');
        
        if($quest_flag==0){
            $message=Message::getCopy($quest_message_id);
            $message->load();
            if($message->exists()){
                $flag=$message->getField('flag');
                if($flag==0){
                    $message->setField('flag',1);
                    $message->update();
                    $content="已查看日志任务序列号：".$quest_perf_id;
                    $Reply=Reply::getCopy($quest_reply_id);
                    $Reply->load();
                    if($Reply->exists())
                        $batchid=$Reply->getField('batchid');
                    $ret = Reply::addRecord($content, $login_userid, $quest_userid, Reply::reply, $quest_batch_type, $batchid,[$quest_userid], $quest_reply_id);
                    if($ret) {
                        Message::addRecord('', $content, $login_userid, $quest_userid,$quest_userid,\Prj\Data\MBPerfReply::reply, 3,$batchid,$quest_perf_id,$quest_reply_id,[]);
                    }else {
                    }
                }
            }
        }
        
        $pager = new \Sooh\DB\Pager(1000);
        //$date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $endTime=$this->_request->get('endTime', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $startTime=$this->_request->get('startTime', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp('-6 day')));
        
        $where = [];
        
        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
        $perf_id=$this->_request->get('perf_id', '');
        if($perf_id)$where['id']=$perf_id;
        $reply_id=$this->_request->get('reply_id', '');
        $per_user=\Prj\Data\MBPerfDailylog::loopFindRecords(['id'=>$perf_id]);
        $per_user=$per_user[0]['userid'];
      
        empty($startTime) ||$where['log_date]']=$startTime;
        empty($endTime)||$where['log_date[']=$endTime;
       
        if(!empty($zzjgName)){
            $zzjgName_tree=explode(',', $zzjgName);
            if(is_array($zzjgName_tree) && count($zzjgName_tree)>1){
                foreach ($zzjgName_tree as $v){
                    $v=$v.'@local';
                    $zzjgName_tree_[]=$v;
                }  
                $userid= $zzjgName_tree_;
                $zzjgStr=$zzjgNameCh;
            } 
            else{
                $userid = $zzjgName . '@local';
                $user = \Prj\Data\Manager::getCopy($zzjgName);
                $user->load();
                $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
                $zzjgStr = '';
                foreach ($zzjgFlat as $k => $v) {
                    $zzjgStr .= $v['name'] . '--';
                }
                
                $zzjgStr .= $zzjgNameCh;
            }
        }else {
            if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local'){
                $userList=\Prj\Data\Manager::isSpecialUserloginname();
                $userid = $userList;
            }else{
             $userid = $this->session->get('managerId');
            } 
        }

        if(!empty($per_user)&&$per_user!=$userid)$userid=$per_user;
        
        $where['userid'] = $userid;

        $data = \Prj\Data\MBPerfDailylog::paged($pager, $where);
        
        foreach ($data as $v){
            if($v['name']){
                $perf_name=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$v['name']]);
                $v['name']=$perf_name[0]['name'];
            }
            if(empty($v['finish']))$v['finish']='0%';
            
            $realdata[]=$v;
        }
        
        $view = new \Prj\Misc\ViewFK();
        
        if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local' ||count($zzjgName_tree)>1){
        $uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','startdata'=>$startTime,'enddata'=>$endTime],'tabManager', 'perfdailylog');
        
        $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('只查看此人', $uri ) );
        
        $this->_view->assign('Managerlogin', 1);
        
        $histroy="";
        }elseif($quest_message_type==3){
        $histroy="";
        }
        else{
            $histroy="loghistory_dst_";
        }

        $view->setPk('id')
             ->setData($realdata)
             ->setReple($histroy)
             ->setOperate(true)
             ->hideDefaultBtn()
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', 'p1', 'text', [], 5)
             ->addRow('settime', '日期', 'p2', 'datepicker', [], 80)
             ->addRow('staffname', '员工姓名', 'p3', 'text', [], 50)
             ->addRow('type', '类型', 'p4', 'select', [1=>'日目标',2=>'周目标',3=>'月目标',4=>'季目标'], 50)
             ->addRow('name', '项目', 'p5', 'text', [], 50)
             ->addRow('content', '工作内容', 'p6', 'textarea', [], 100)
             ->addRow('level', '优先级', 'p7', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 50)
             ->addRow('plan_cost', '实际用时(小时)', 'p8', 'text', [], 40)
             ->addRow('finish', '完成情况', 'p9', 'select', ['0%'=>'0%','10%' => '10%', '20%'=>'20%','30%' => '30%','40%'=>'40%', '50' => '50%','60%'=>'60%', '70%'=>'70%','80%'=>'80%','90%'=>'90%','100%'=>'100%'], 20)
             ->addRow('finish_reason', '完成情况的说明', 'p10', 'text', [], 100);

        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        $this->_view->assign('view', $view->normalPerfTable());
       // $this->_view->assign('date', $date);
        $this->_view->assign('startTime', $startTime);
        $this->_view->assign('endTime', $endTime);
        
       if(count($zzjgName_tree)>1){
            $this->_view->assign('zzjgStr', $zzjgStr);
            $this->_view->assign('zzjgName', $zzjgName);
            $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }else{
            isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
            isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
            isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }
        
        
        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = date('Ymd', strtotime($date)); 
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid,$reply_id);
            //var_log(\Sooh\DB\Broker::lastCmd(),'sql#####################');
            $this->_view->assign('userid', $userid);
            $this->_view->assign('loginid', $this->session->get('managerId'));
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::logType);
            $this->_view->assign('prefix', 'loghistory_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
            $this->_view->assign('createReply', 1);
            $this->_view->assign( 'dstType', 0 );
        }
    }

    public function tabManagerAction(){
        
        //var_log($_REQUEST, __CLASS__.'#####_REQEUSt######');
        
        $pager = new \Sooh\DB\Pager(1000);
        
        $endTime=$this->_request->get('endTime');
        $startTime=$this->_request->get('startTime');
        
        
        $ids=$this->_request->get('id');
        
        $zzjgName=\Prj\Data\MBPerfDailylog::loopFindRecords(['id'=>$ids]);
        $zzjgName=$zzjgName[0]['userid'];
        
        if (!empty($zzjgName)) {
            $userid = explode('@', $zzjgName);
            $userid=$userid[0];
            $user = \Prj\Data\Manager::getCopy($userid);
            $user->load();
            $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
            
            $zzjgStr = '';
            foreach ($zzjgFlat as $k => $v) {
                $zzjgStr .= $v['name'] . '--';
            }
            $zzjgStr .= $zzjgNameCh;
        }
        
        $realname=\Prj\Data\Manager::getName($userid);
        $zzjgStr.=$realname;
        
        $where['userid']=$zzjgName;
        $startdata=$this->_request->get('startdata');
        $enddata=$this->_request->get('enddata');
        
        if(empty($startTime)){
            $where['log_date]']=$startdata;
        }else{
            $where['log_date]']=$startTime;
        }
        if(empty($startTime)){
            $where['log_date[']=$enddata;
        }else{
            $where['log_date[']=$endTime;
        }
        
       // $where['id']=$ids;
        $data = \Prj\Data\MBPerfDailylog::paged($pager, $where);
       
        foreach ($data as $v){
            if($v['name']){
                $perf_name=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$v['name']]);
                $v['name']=$perf_name[0]['name'];
            }
            if(empty($v['finish']))$v['finish']='0%';
        
            $realdata[]=$v;
        }
        
       $view = new \Prj\Misc\ViewFK();
       
       $view->setPk('id')
            ->setData($realdata)
            ->setReple('tabmanager_dst_')
            //->setOperate(true)
            //->hideDefaultBtn()
            ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', 'p1', 'text', [], 5)
             ->addRow('settime', '日期', 'p2', 'datepicker', [], 5)
             ->addRow('staffname', '员工姓名', 'p3', 'text', [], 5)
             ->addRow('type', '类型', 'p4', 'select', [1=>'日目标',2=>'周目标',3=>'月目标',4=>'季目标'], 100)
             ->addRow('name', '项目', 'p5', 'text', [], 10)
             ->addRow('content', '工作内容', 'p6', 'textarea', [], 10)
             ->addRow('level', '优先级', 'p7', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 100)
             ->addRow('plan_cost', '实际用时(小时)', 'p8', 'text', [], 40)
             ->addRow('finish', '完成情况', 'p9', 'select', ['0%'=>'0%','10%' => '10%', '20%'=>'20%','30%' => '30%','40%'=>'40%', '50' => '50%','60%'=>'60%', '70%'=>'70%','80%'=>'80%','90%'=>'90%','100%'=>'100%'], 100)
             ->addRow('finish_reason', '完成情况的说明', 'p10', 'text', [], 10);
                
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        
        $this->_view->assign('view', $view->normalPerfTable());
        
        $this->_view->assign('startTime', $where['log_date]']);
        $this->_view->assign('endTime', $where['log_date[']);
        
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        
        if (!empty($data)) {
            $userid = urldecode($zzjgName);
            $batchid = date('Ymd', strtotime($date));
            $records = \Prj\Data\MBPerfReply::getMyReply($zzjgName, $batchid);
            $this->_view->assign('userid', $zzjgName);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::logType);
            $this->_view->assign('prefix', 'tabmanager_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
            $this->_view->assign('createReply', 1);
            $this->_view->assign( 'dstType', 0 );
        }  
    }
    
    public function tabreportlogAction()
    {
        $pager = new \Sooh\DB\Pager(1000);
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
        } else {
            $startDate = $this->_request->get('startDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp('-6 day')));
            $endDate = $this->_request->get('endDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));

            $where = ['userid' => $this->session->get('managerId')];
            empty($startDate) || $where['log_date]'] = $startDate;
            empty($endDate) || $where['log_date['] = $endDate;
        }

        $data = \Prj\Data\MBPerfDailylog::paged($pager, $where);

        foreach ($data as $v){
            if($v['name']){
                $perf_name=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$v['name']]);
                $v['name']=$perf_name[0]['name'];
            }
            if(empty($v['finish']))$v['finish']='0%';
        
            $realdata[]=$v;
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($realdata)
             ->setPager($pager)
             ->hideDefaultBtn()
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', 'p1', 'text', [], 5)
             ->addRow('settime', '日期', 'p2', 'datepicker', [], 5)
             ->addRow('staffname', '员工姓名', 'p3', 'text', [], 5)
             ->addRow('type', '类型', 'p4', 'select', [1=>'日目标',2=>'周目标',3=>'月目标',4=>'季目标'], 100)
             ->addRow('name', '项目', 'p5', 'text', [], 10)
             ->addRow('content', '工作内容', 'p6', 'textarea', [], 10)
             ->addRow('level', '优先级', 'p7', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 100)
             ->addRow('plan_cost', '实际用时(小时)', 'p8', 'text', [], 40)
             ->addRow('finish', '完成情况', 'p9', 'select', ['0%'=>'0%','10%' => '10%', '20%'=>'20%','30%' => '30%','40%'=>'40%', '50' => '50%','60%'=>'60%', '70%'=>'70%','80%'=>'80%','90%'=>'90%','100%'=>'100%'], 100)
             ->addRow('finish_reason', '完成情况的说明', 'p10', 'text', [], 10);

        if ($isDownload) {
            $excel = $view->toExcel($data);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        empty($startDate) || $this->_view->assign('startDate', $startDate);
        empty($endDate) || $this->_view->assign('endDate', $endDate);
        empty($where) || $this->_view->assign('where', $where);
    }

    public function updateAction()
    {
        $addFlag = false;
        $data = $this->getRequest()->get('values')[0];
        $key = $this->getRequest()->get('id');
        if (empty($key)) {
            $addFlag = true;
        }
        unset($data['id']);

        if (empty($data)) {
            $this->returnError('没有更新的内容，更新失败');
            return 0;
        }

        $userid = $this->session->get('managerId');
        if(empty($data['finish']))$data['finish']='0%';
        //var_log($_REQUEST,'#######quest############');
       // $id=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$data['name'],'type'=>$data['type']]);
        if(is_numeric($data['name'])){
        $model_perfdst=\Prj\Data\MBPerfDst::getCopy($data['name']);
        $model_perfdst->load();
        $model_perfdst->setField('process',intval($data['finish']));
        $model_perfdst->update();
        }
        
        if ($addFlag) {
            try {
                $ret = \Prj\Data\MBPerfDailylog::addData($data['settime'],$data['staffname'],$data['name'], $data['content'], $data['level'], $data['type'], $userid, \Sooh\Base\Time::getInstance()->ymd, $data['plan_cost'], 0, $data['finish'],$data['finish_reason']);
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('新增失败');
                    return 0;
                }
            }
            $this->_view->assign('_id', $ret);
            $this->returnOK('新增成功');
            return 0;
        } else {
            //update
            $model = \Prj\Data\MBPerfDailylog::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $ret = \Prj\Data\MBPerfDailylog::updData($key, $data);
                $this->_view->assign('_id', $key);
                $this->returnOK('更新成功');
                return 0;
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('更新失败');
                    return 0;
                }
            }
        }
    }

    public function deleteAction()
    {
        $key = $this->getRequest()->get('_id');
        
        $perf_id=Message::loopFindRecords(['perf_id'=>$key]);
        if($perf_id){
            return $this->returnError('该目标已被管理员操作过，无法删除');
        }
        
        $perflog_id=\Prj\Data\MBPerfDailylog::loopFindRecords(['id'=>$key]);
        $perflog_name=$perflog_id[0]['name'];
        if(is_numeric($perflog_name)){
            $model_perfdst=\Prj\Data\MBPerfDst::getCopy($perflog_name);
            $model_perfdst->load();
            $model_perfdst->setField('process','');
            $model_perfdst->update();
        }

        if (\Prj\Data\MBPerfDailylog::delData($key)) {
            $this->returnOK('删除成功');
        } else {
            $this->returnError('记录不存在或者已经被删除');
        }
        return 0;
    }
    
    public function detailAction()
    {
    	$key = $this->getRequest()->get('id');
    	$rs = \Prj\Data\MBPerfDailylog::getData($key);
    	var_log( $rs, 'rs' );
    	if ( $rs )
    	{
    		$this->_view->assign( 'perfdailylog_detail',
    				[ 'id' => $rs->getField('id'),
    				'settime' => $rs->getField( 'settime' ),
    				'staffname' => $rs->getField( 'staffname' ),
    				'name' => $rs->getField( 'name' ),
    				'content' => $rs->getField( 'content' ),
    				'level' => $rs->getField('level'),
    				'type' => $rs->getField('type'),
    				'cost' => $rs->getField( 'plan_cost' ),
    				'finish' => $rs->getField( 'finish' ),
    				'finish_reation' => $rs->getField( 'finish_reason' ),
    				'userid' => $rs->getField( 'userid' ) ] );
    	}
    	else
    	{
    		$this->returnError( '记录不存在或者已经被删除' );
    	}
    	 
    	return 0;
    }
}
