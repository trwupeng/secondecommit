<?php

use Prj\ManagerCtrl;
use \Prj\Data\MBPerfReply as Reply;
use \Prj\Data\MBMessage as Message;

class PerfdstController extends ManagerCtrl
{
    public function init()
    {
        parent::init();
        if ($this->_request->get('__VIEW__') == 'json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

   
   
    /**
     * 季度目标
     */
    public function indexAction()
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
                    $content="已查看目标任务序列号：".$quest_perf_id;
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
        
        $this->_view->assign('targetTab', $this->getRequest()->get('targetTab', ''));
        $this->_view->assign('jumpParams', json_decode(urldecode($this->getRequest()->get('jumpParams', '')), true));
        $pager = new \Sooh\DB\Pager(1000);

        $createDate = $this->_request->get('create_date', date('Y', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = $this->_request->get('type_id', ceil(date('m', \Sooh\Base\Time::getInstance()->timestamp()) / 3));

        $where = ['type' => 4, 'type_num' => $typeId];
        empty($createDate) || $where['dst_date'] = $createDate . '-01-01';

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
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
        
        $perf_id=$this->getRequest()->get('perf_id','');
        if($perf_id)$where['id']=$perf_id;
        if(!is_numeric($where['id']))unset($where['id']);
        
        $reply_id=$this->_request->get('reply_id', '');
        $per_user=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$perf_id]);
        $per_user=$per_user[0]['userid'];
        
        if(!empty($per_user)&&$per_user!=$userid)$userid=$per_user;
        $where['userid'] = $userid;
        
        $loginName=explode('@', $userid)[0];
        $loginName=\Prj\Data\Manager::getName($loginName);
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        
        foreach ($data as $ret){
            $perf_ji=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$ret['id'],'del'=>0]);
        
            foreach ($perf_ji as $v){
                $perf_ji_num[$v['target_ji']]+=$v['process'];
            }
        
        }
        
        foreach ($data as $rs){
            if($perf_ji_num){
                foreach ($perf_ji_num as $k=>$v){
                    if($rs['id']==$k){
                        $ji_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$rs['id'],'del'=>0]);
                        $ji_num=count($ji_num);
                        $t = intval($v/$ji_num);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                    }else{
                        $ji_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$rs['id'],'del'=>0]);
                        if($ji_num_)continue;
                        $t=$rs['process'];
                        $t = intval($t);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                      
                    }
                }
                $rs['process'] = $tmp;
            }else{
                $t=$rs['process'];
                $t = intval($t);
                $t=!empty($t)?$t:0;
                $tmp = [ 'type' => 'process',	//类型
                    'percent' => intval($t), //进度（0-100）
                    'value' => $t.'%', //显示的文本
                    'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                $rs['process'] = $tmp;
            }
             
            $realData[] = $rs;
        
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local' ||count($zzjgName_tree)>1){
            $uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','typeid'=>$typeId],'tabindexmanager', 'perfdst');
        
            $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('只查看此人', $uri ) );
        
            $this->_view->assign('Managerlogin', 1);
            
            $view->setPk('id')
            ->setData($realData)
            ->setReple()
            ->setOperate(true)
            ->hideDefaultBtn()
            ->setAction('/plan/perfdst/update?type=4&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=4')
            ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
            ->addRow('settime', '日期', '', 'datepicker', [], 100)
            ->addRow('staffname', '员工姓名', '', 'text', [], 100)
            ->addRow('name', '项目', '', 'text', [], 100)
            ->addRow('content', '工作内容', '', 'textarea', [], 220)
            ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
            ->addRow('process', '工作进度', '', '',[] , 100);
            
            $this->_view->assign('view', $view->normalPerfTable());
            
        }else{
            if($quest_message_type==3){
                
                $view->setPk('id')
                ->setData($realData)
                ->setReple()
                ->setOperate(true)
                ->hideDefaultBtn()
                ->setAction('/plan/perfdst/update?type=4&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=4')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->normalPerfTable());
            }else{
                
                $view->setPk('id')
                ->setData($realData)
                ->setReple('quarter_dst_')
                ->setAction('/plan/perfdst/update?type=4&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=4')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->showPerf());
            }
          
        }
        
        $this->_view->assign('createDate', $createDate);
        $this->_view->assign('typeId', $typeId);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        
        
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'wangdong') {
            $this->_view->assign('thisManager', 1);
        }
        
       if(count($zzjgName_tree)>1){
            $this->_view->assign('zzjgStr', $zzjgStr);
            $this->_view->assign('zzjgName', $zzjgName);
            $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }else{
            isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
            isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
            isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }

        $userid = urldecode($userid);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(4, $createDate . '-01-01', $typeId);        
        $this->_view->assign('userid', $userid);
        $this->_view->assign('loginid', $this->session->get('managerId'));
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'quarter_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 4 );
        if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid,$reply_id);
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
        
        
    }

    /**
     *  查看的个人季度目标
     *
     */
    
    public  function tabindexmanagerAction(){
    
        $pager = new \Sooh\DB\Pager(1000);
    
        $ids=$this->_request->get('id');
        $typeId=$this->_request->get('typeid');
        $type_num=$typeId;
        $createDate=date('Y', \Sooh\Base\Time::getInstance()->timestamp());
        $zzjgName=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$ids]);
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
        $where['type_num']=$type_num;
        $where['type']=4;
        // $where['id']=$ids;
    
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
    
        foreach ($data as $ret){
            $perf_ji=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$ret['id'],'del'=>0]);
        
            foreach ($perf_ji as $v){
                $perf_ji_num[$v['target_ji']]+=$v['process'];
            }
        
        }
        
        foreach ($data as $rs){
            if($perf_ji_num){
                foreach ($perf_ji_num as $k=>$v){
                    if($rs['id']==$k){
                        $ji_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$rs['id'],'del'=>0]);
                        $ji_num=count($ji_num);
                        $t = intval($v/$ji_num);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                    }else{
                        $ji_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_ji'=>$rs['id'],'del'=>0]);
                        if($ji_num_)continue;
                        $t=$rs['process'];
                        $t = intval($t);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                       
                    }
                }
                $rs['process'] = $tmp;
            }else{
                $t=$rs['process'];
                $t = intval($t);
                $t=!empty($t)?$t:0;
                $tmp = [ 'type' => 'process',	//类型
                    'percent' => intval($t), //进度（0-100）
                    'value' => $t.'%', //显示的文本
                    'prefix' => 'quarter_dst_']; //进度条id的前缀，方便js查找
                $rs['process'] = $tmp;
            }
             
            $realData[] = $rs;
        
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        $view->setPk('id')
        ->setData($realData)
        ->setReple('quarter_dst_')
        //->setOperate(true)
        //->hideDefaultBtn()
        ->setAction('/plan/perfdst/update?type=4&userid='.$where['userid'].'', '/plan/perfdst/delete?type=4')
        ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
        ->addRow('settime', '日期', '', 'datepicker', [], 100)
        ->addRow('staffname', '员工姓名', '', 'select', [$realname=>$realname], 100)
        ->addRow('name', '项目', '', 'text', [], 100)
        ->addRow('content', '工作内容', '', 'textarea', [], 220)
        ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
        ->addRow('process', '工作进度', '', '',[] , 100);
    
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
    
        $this->_view->assign('view', $view->showPerf());
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
    
        $userid = urldecode($zzjgName);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(4, $createDate . '-01-01', $typeId);        
        $this->_view->assign('userid', $zzjgName);
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'quarter_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 4 );
       	if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($zzjgName, $batchid);            
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
    
    }
    
    
    /**
     * 月目标
     */
    public function tabmonthAction()
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
                    $content="已查看目标任务序列号：".$quest_perf_id;
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
        $createDate = $this->_request->get('create_date', date('Y', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = $this->_request->get('type_id', date('m', \Sooh\Base\Time::getInstance()->timestamp()));
        
        $daytime=date('t',\Sooh\Base\Time::getInstance()->timestamp());
        $monthtime='0'.$typeId;
        $startTime=$createDate.'-'.$monthtime.'-01';
        $endTime=$createDate.'-'.$monthtime.'-'.$daytime;
        
        $where = ['type' => 3, 'type_num' => $typeId];
        empty($createDate) || $where['dst_date'] = $createDate . '-01-01';

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
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
        
        
        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        $perf_id=$this->getRequest()->get('perf_id','');
        if($perf_id)$where['id']=$perf_id;
        if(!is_numeric($where['id']))unset($where['id']);
       
        $reply_id=$this->_request->get('reply_id', '');
        $per_user=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$perf_id]);
        $per_user=$per_user[0]['userid'];
        
        if(!empty($per_user)&&$per_user!=$userid)$userid=$per_user;
        $where['userid'] = $userid;
        
        $loginName=explode('@', $userid)[0];
        $loginName=\Prj\Data\Manager::getName($loginName);
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        
        foreach ($data as $ret){
            $perf_month=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$ret['id'],'del'=>0]);
        
            foreach ($perf_month as $v){
              $perf_month_num[$v['target_yue']]+=$v['process'];
            }
        
        }
        
        foreach ($data as $rs){
            if($perf_month_num){
            foreach ($perf_month_num as $k=>$v){
                if($rs['id']==$k){
                    $month_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$rs['id'],'del'=>0]);
                    $month_num=count($month_num);
                    $t = intval($v/$month_num);
                    $t=!empty($t)?$t:0;
                    $tmp = [ 'type' => 'process',	//类型
                        'percent' => intval($t), //进度（0-100）
                        'value' => $t.'%', //显示的文本
                        'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
                   
                }else{
                   $month_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$rs['id'],'del'=>0]);
                   if($month_num_)continue;
                   $t=$rs['process'];
                   $t = intval($t);
                   $t=!empty($t)?$t:0;
                   $tmp = [ 'type' => 'process',	//类型
                       'percent' => intval($t), //进度（0-100）
                       'value' => $t.'%', //显示的文本
                       'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
               }
              }
              $rs['process'] = $tmp;
            }else{
                   $t=$rs['process'];
                   $t = intval($t);
                   $t=!empty($t)?$t:0;
                   $tmp = [ 'type' => 'process',	//类型
                       'percent' => intval($t), //进度（0-100）
                       'value' => $t.'%', //显示的文本
                       'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
                   $rs['process'] = $tmp; 
               }    
             
            $realData[] = $rs;
            
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local' ||count($zzjgName_tree)>1){
            $uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','startTime'=>$startTime,'endTime'=>$endTime,'typeid'=>$typeId],'tabmonthmanager', 'perfdst');
        
            $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('只查看此人', $uri ) );
        
            $this->_view->assign('Managerlogin', 1);
            
            $view->setPk('id')
            ->setData($realData)
            ->setReple()
            ->setOperate(true)
            ->hideDefaultBtn()
            ->setAction('/plan/perfdst/update?type=3&type_num='.$typeId.'&userid='.$where['userid'].'', '/plan/perfdst/delete?type=3')
            ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
            ->addRow('settime', '日期', '', 'datepicker', [], 100)
            ->addRow('staffname', '员工姓名', '', 'text', [], 100)
            ->addRow('name', '项目', '', 'text', [], 100)
            ->addRow('content', '工作内容', '', 'textarea', [], 220)
            ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
            ->addRow('process', '工作进度', '', '',[] , 100);
            
            $this->_view->assign('view', $view->normalPerfTable());
            
            
        }else{
            if($quest_message_type==3){

                $view->setPk('id')
                ->setData($realData)
                ->setReple()
                ->setOperate(true)
                ->hideDefaultBtn()
                ->setAction('/plan/perfdst/update?type=3&type_num='.$typeId.'&userid='.$where['userid'].'', '/plan/perfdst/delete?type=3')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->normalPerfTable());
            }else{
                $view->setPk('id')
                ->setData($realData)
                ->setReple('month_dst_')
                ->setAction('/plan/perfdst/update?type=3&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=3')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->showPerf());
            } 
        }
       
       
        $this->_view->assign('createDate', $createDate);
        $this->_view->assign('typeId', $typeId);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'wangdong') {
            $this->_view->assign('thisManager', 1);
        }
        
        if(count($zzjgName_tree)>1){
            $this->_view->assign('zzjgStr', $zzjgStr);
            $this->_view->assign('zzjgName', $zzjgName);
            $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }else{
            isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
            isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
            isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }

        $userid = urldecode($userid);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(3, $createDate . '-01-01', $typeId);
        $this->_view->assign('userid', $userid);
        $this->_view->assign('loginid', $this->session->get('managerId'));
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'month_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 3 );
        if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid,$reply_id);
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
    }

    /**
     *  查看的个人月目标
     *
     */
    
    public  function tabmonthmanagerAction(){
    
        $pager = new \Sooh\DB\Pager(1000);
    
        $endTime=$this->_request->get('endTime');
        $startTime=$this->_request->get('startTime');
        $ids=$this->_request->get('id');
        $typeId=$this->_request->get('typeid');
        $createDate=date('Y', \Sooh\Base\Time::getInstance()->timestamp());
        $zzjgName=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$ids]);
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
        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        $where['type']=3;
        // $where['id']=$ids;
    
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
    
        foreach ($data as $ret){
            $perf_month=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$ret['id'],'del'=>0]);
        
            foreach ($perf_month as $v){
                $perf_month_num[$v['target_yue']]+=$v['process'];
            }
        
        }
        
        foreach ($data as $rs){
            if($perf_month_num){
                foreach ($perf_month_num as $k=>$v){
                    if($rs['id']==$k){
                        $month_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$rs['id'],'del'=>0]);
                        $month_num=count($month_num);
                        $t = intval($v/$month_num);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
                      
                    }else{
                        $month_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_yue'=>$rs['id'],'del'=>0]);
                        if($month_num_)continue;
                        $t=$rs['process'];
                        $t = intval($t);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
                       
                    }
                }
                $rs['process'] = $tmp;
            }else{
                $t=$rs['process'];
                $t = intval($t);
                $t=!empty($t)?$t:0;
                $tmp = [ 'type' => 'process',	//类型
                    'percent' => intval($t), //进度（0-100）
                    'value' => $t.'%', //显示的文本
                    'prefix' => 'month_dst_']; //进度条id的前缀，方便js查找
                $rs['process'] = $tmp;
            }
             
            $realData[] = $rs;
        
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        $view->setPk('id')
        ->setData($realData)
        ->setReple('month_dst_')
        //->setOperate(true)
        //->hideDefaultBtn()
        ->setAction('/plan/perfdst/update?type=3&userid='.$where['userid'].'', '/plan/perfdst/delete?type=3')
        ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
        ->addRow('settime', '日期', '', 'datepicker', [], 100)
        ->addRow('staffname', '员工姓名', '', 'select', [$realname=>$realname], 100)
        ->addRow('name', '项目', '', 'text', [], 100)
        ->addRow('content', '工作内容', '', 'textarea', [], 220)
        ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
        ->addRow('process', '工作进度', '', '',[] , 100);
    
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
    
        $this->_view->assign('view', $view->showPerf());
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
    
        $userid = urldecode($zzjgName);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(3, $createDate . '-01-01', $typeId);
        $this->_view->assign('userid', $zzjgName);
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'month_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 3 );
   		if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($zzjgName, $batchid);
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
    
    }
    
    /**
     * 周目标
     */
    public function tabweekAction()
    {
        
        //var_log($_REQUEST,'##########request#################');

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
                    $content="已查看目标任务序列号：".$quest_perf_id;
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


        //不能跨年
        $pager = new \Sooh\DB\Pager(1000);
        
        $errorMsg = false;

        $startDate = $this->_request->get('startDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $endDate = $this->_request->get('endDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        
        $startTime=$this->_request->get('startDate',date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp('-29 day')));
        $endTime=$this->_request->get('endDate',date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp()));

        $startWeekNum = date('W', strtotime($startDate));
        $endWeekNum = date('W', strtotime($endDate));

        if ($endWeekNum <= $startWeekNum) {
            $typeId = $endWeekNum;
        } else {
            if ($endWeekNum > $startWeekNum) {

                $this->_view->assign('errorMsg', '一次只能选择一周');
//                return 0;
            }
            
            $typeId = $endWeekNum;
            //            $arrTempNums = [];
            //            for ($i = $startWeekNum; $i <= $endWeekNum; $i++) {
            //                $arrTempNums[] = $i;
            //            }
            //            $typeId = $arrTempNums;
        }
        $where = ['type' => 2, 'type_num' => $typeId];

        if (substr($endDate, 0, 4) - substr($startDate, 0, 4) != 0) {
            $arrTempDates = [];
            for ($i = substr($startDate, 0, 4); $i <= substr($endDate, 0, 4); $i++) {
                $arrTempDates[] = $i . '-01-01';
            }
        } else {
            $arrTempDates = substr($startDate, 0, 4) . '-01-01';
        }
        $where['dst_date'] = $arrTempDates;

        $weekNums = $this->getRequest()->get('weekNums', 0);
        if (!empty($weekNums)) {
            $where['type_num'] = $typeId = $weekNums;
        }

        
        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
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
        }
        else {
            if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local'){
                $userList=\Prj\Data\Manager::isSpecialUserloginname();
                $userid = $userList;
            }else{
             $userid = $this->session->get('managerId');
            } 
        }
        
        $dateForm=date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp('-29 day'));
        $datTo=date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp());
        if($startTime!=$dateForm|| $endTime!=$datTo)unset($where['type_num']);

        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        $perf_id=$this->getRequest()->get('perf_id','');
        if($perf_id)$where['id']=$perf_id;
        if(!is_numeric($where['id']))unset($where['id']);
        $reply_id=$this->_request->get('reply_id', '');
        $per_user=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$perf_id]);
        $per_user=$per_user[0]['userid'];
        
        if(!empty($per_user)&&$per_user!=$userid)$userid=$per_user;
        $where['userid'] = $userid;
        unset($where['type_num']);
        
        $loginName=explode('@', $userid)[0];
        $loginName=\Prj\Data\Manager::getName($loginName);
  
        $data=\Prj\Data\MBPerfDst::paged($pager, $where);
        foreach ($data as $ret){
            $perf_week=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$ret['id'],'del'=>0]);
            
            foreach ($perf_week as $v){
                $perf_week_num[$v['target_week']]+=$v['process'];   
            } 
            
        }
        
        foreach ($data as $rs){
         if($perf_week_num){   
            foreach ($perf_week_num as $k=>$v){
               if($rs['id']==$k){ 
                   $week_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$rs['id'],'del'=>0]);
                   $week_num=count($week_num);
                   $t = intval($v/$week_num);
                   $t=!empty($t)?$t:0;
                   $tmp = [ 'type' => 'process',	//类型
                       'percent' => intval($t), //进度（0-100）
                       'value' => $t.'%', //显示的文本
                       'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找
               }else{
                   $week_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$rs['id'],'del'=>0]);
                   if($week_num_)continue;
                   $t=$rs['process'];
                   $t = intval($t);
                   $t=!empty($t)?$t:0;
                   $tmp = [ 'type' => 'process',	//类型
                       'percent' => intval($t), //进度（0-100）
                       'value' => $t.'%', //显示的文本
                       'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找  
               }
            }
            
            $rs['process'] = $tmp;
            
        }else{   
                   $t=$rs['process'];
                   $t = intval($t);
                   $t=!empty($t)?$t:0;
                   $tmp = [ 'type' => 'process',	//类型
                       'percent' => intval($t), //进度（0-100）
                       'value' => $t.'%', //显示的文本
                       'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找
                   $rs['process'] = $tmp;
                   
               } 
               
               $realData[] = $rs;
                
     }  
          
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        if($this->session->get('managerId')=='root@local' || $this->session->get('managerId')=='wangdong@local'||count($zzjgName_tree)>1){
            $uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','startTime'=>$startTime,'endTime'=>$endTime,'typeid'=>$typeId],'tabweekmanager', 'perfdst');
        
            $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('只查看此人', $uri ) );
        
            $this->_view->assign('Managerlogin', 1);
            
            $view->setPk('id')
            ->setData($realData)
            ->setReple()
            ->setOperate(true)
            ->hideDefaultBtn()
            ->setAction('/plan/perfdst/update?type=2&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=2')
            ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
            ->addRow('settime', '日期', '', 'datepicker', [], 100)
            ->addRow('staffname', '员工姓名', '', 'text', [], 100)
            ->addRow('name', '项目', '', 'text', [], 100)
            ->addRow('content', '工作内容', '', 'textarea', [], 220)
            ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
            ->addRow('process', '工作进度', '', '',[] , 100);
            
            $this->_view->assign('view', $view->normalPerfTable());
            
        }else{
            
            if($quest_message_type==3){
                
                $view->setPk('id')
                ->setData($realData)
                ->setReple()
                ->setOperate(true)
                ->hideDefaultBtn()
                ->setAction('/plan/perfdst/update?type=2&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=2')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                
                $this->_view->assign('view', $view->normalPerfTable());
            }else{
                $week_dst_="week_dst_";
                
                $view->setPk('id')
                ->setData($realData)
                ->setReple($week_dst_)
                ->setAction('/plan/perfdst/update?type=2&userid='.$where['userid'].'&type_num='.$typeId.'&enddate='.$endDate.'', '/plan/perfdst/delete?type=2')
                ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)//修复员工姓名选填
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->showPerf());
            }
        }
        
       
        $this->_view->assign('startDate', $startTime);
        $this->_view->assign('endDate', $endDate);

        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
 
        if(count($zzjgName_tree)>1){
            $this->_view->assign('zzjgStr', $zzjgStr);
            $this->_view->assign('zzjgName', $zzjgName);
            $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }else{
            isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
            isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
            isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }
      
        $userid = urldecode($userid);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(2, substr($startDate, 0, 4) . '-01-01', $typeId);
        $this->_view->assign('userid', $userid);
        $this->_view->assign('loginid', $this->session->get('managerId'));
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'week_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 2 );
        if (!empty($data)) {            
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid,$reply_id);
            $this->_view->assign('records', $records);
            $this->_view->assign( 'showReply', 1 );   
        }
        else
        {
        	$this->_view->assign( "records", [] );
        	$this->_view->assign( 'showReply', 0 );
        }
    }

/**
     *  查看的个人周目标
     *
     */
    
    public  function tabweekmanagerAction(){
        
        $pager = new \Sooh\DB\Pager(1000);
        
        $endTime=$this->_request->get('endTime');
        $startTime=$this->_request->get('startTime');
        $ids=$this->_request->get('id');
        $typeId=$this->_request->get('typeid');
        $reply_id=$this->_request->get('reply_id');
        
        $zzjgName=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$ids]);
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
        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        $where['type']=2;
       // $where['id']=$ids;
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        
        foreach ($data as $ret){
            $perf_week=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$ret['id'],'del'=>0]);
        
            foreach ($perf_week as $v){
                $perf_week_num[$v['target_week']]+=$v['process'];
            }
        
        }
        
        foreach ($data as $rs){
            if($perf_week_num){
                foreach ($perf_week_num as $k=>$v){
                    if($rs['id']==$k){
                        $week_num=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$rs['id'],'del'=>0]);
                        $week_num=count($week_num);
                        $t = intval($v/$week_num);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找
                    }else{
                        $week_num_=\Prj\Data\MBPerfDst::loopFindRecords(['type'=>1,'target_week'=>$rs['id'],'del'=>0]);
                        if($week_num_)continue;
                        $t=$rs['process'];
                        $t = intval($t);
                        $t=!empty($t)?$t:0;
                        $tmp = [ 'type' => 'process',	//类型
                            'percent' => intval($t), //进度（0-100）
                            'value' => $t.'%', //显示的文本
                            'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找                       
                    }
                }
                
                $rs['process'] = $tmp;
            }else{
                
                $t=$rs['process'];
                $t = intval($t);
                $t=!empty($t)?$t:0;
                $tmp = [ 'type' => 'process',	//类型
                    'percent' => intval($t), //进度（0-100）
                    'value' => $t.'%', //显示的文本
                    'prefix' => 'week_dst_']; //进度条id的前缀，方便js查找
                $rs['process'] = $tmp;
                 
            }
             
            $realData[] = $rs;
        
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        $view->setPk('id')
        ->setData($realData)
        ->setReple('week_dst_')
        //->setOperate(true)
        //->hideDefaultBtn()
        ->setAction('/plan/perfdst/update?type=2&userid='.$where['userid'].'', '/plan/perfdst/delete?type=2')
        ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
        ->addRow('settime', '日期', '', 'datepicker', [], 100)
        ->addRow('staffname', '员工姓名', '', 'select', [$realname=>$realname], 100)
        ->addRow('name', '项目', '', 'text', [], 100)
        ->addRow('content', '工作内容', '', 'textarea', [], 220)
        ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
        ->addRow('process', '工作进度', '', '',[] , 100);
        
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        
        $this->_view->assign('view', $view->showPerf());
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        
        $userid = urldecode($zzjgName);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(2, substr($startDate, 0, 4) . '-01-01', $typeId);        
        $this->_view->assign('userid', $zzjgName);
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'week_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 2 );
        if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($zzjgName, $batchid);
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
        
    }
    
    /**
     * 日目标
     */
    public function tabdayAction()
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
                    $content="已查看目标任务序列号：".$quest_perf_id;
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

        $startTime=$this->_request->get('startDate',date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp('-29 day')));
        $endTime=$this->_request->get('endDate',date('Y-m-d',\Sooh\Base\Time::getInstance()->timestamp()));
        
        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = date('z', strtotime($date));

        $where = ['type' => 1, 'type_num' => $typeId];
        $where['dst_date'] = $date;

        $perf_id=$this->getRequest()->get('perf_id','');
        if($perf_id)$where['id']=$perf_id;
        if(!is_numeric($where['id']))unset($where['id']);
        
        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');

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
        
        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        unset($where['type_num']);
        unset($where['dst_date']);
        
        $reply_id=$this->_request->get('reply_id', '');
        $per_user=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$perf_id]);
        $per_user=$per_user[0]['userid'];
        
        if(!empty($per_user)&&$per_user!=$userid)$userid=$per_user;
        $where['userid'] = $userid;
        
        $loginName=explode('@', $userid)[0];
        $loginName=\Prj\Data\Manager::getName($loginName);
        
        $arr=['未选择'];
        $target_ji=\Prj\Data\MBPerfDst::getpair(4,$userid);//季度目标
        $target_yue=\Prj\Data\MBPerfDst::getpair(3,$userid);//月度目标
        $target_week=\Prj\Data\MBPerfDst::getpair(2,$userid);//周度目标
        
        !empty($target_ji)?$target_ji[0]="未选择":$target_ji=$arr;
        !empty($target_yue)?$target_yue[0]="未选择":$target_yue=$arr;
        !empty($target_week)?$target_week[0]="未选择":$target_week=$arr;
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        foreach ($data as $rs){
            $t = intval($rs['process']);
            !empty($t)?$t:0;
        	$tmp = [ 'type' => 'process',	//类型
					 'percent' => intval($t), //进度（0-100）
					 'value' => $t.'%', //显示的文本
					 'prefix' => 'day_dst_']; //进度条id的前缀，方便js查找
        	$rs['process'] = $tmp;
        	
        	$realData[] = $rs;
            
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root' ||count($zzjgName_tree)>1) {
            
            $uri = \Sooh\Base\Tools::uri(['id'=>'{$id}','startTime'=>$startTime,'endTime'=>$endTime,'typeid'=>$typeId],'tabdaymanager', 'perfdst');
            $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('只查看此人', $uri ) );
            $this->_view->assign('Managerlogin', 1);
            
            $view->setPk('id')
                ->setData($realData)
                ->setReple()
                ->setOperate(true)
                ->hideDefaultBtn()
                ->setAction('/plan/perfdst/update?type=1&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=1')
                ->addRow('id', '序号', '', 'text', [], 40, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'text', [], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('target_ji', '季目标', '', 'select', $target_ji, 80)
                ->addRow('target_yue', '月目标', '', 'select',$target_yue, 80)
                ->addRow('target_week', '周目标', '', 'select', $target_week, 80)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->normalPerfTable());        
        }else{
            
            if($quest_message_type==3){
                
                $view->setPk('id')
                ->setData($realData)
                ->setReple()
                ->setOperate(true)
                ->hideDefaultBtn()
                ->setAction('/plan/perfdst/update?type=1&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=1')
                ->addRow('id', '序号', '', 'text', [], 40, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 100)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 100)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('target_ji', '季目标', '', 'select', $target_ji, 80)
                ->addRow('target_yue', '月目标', '', 'select',$target_yue, 80)
                ->addRow('target_week', '周目标', '', 'select', $target_week, 80)
                ->addRow('content', '工作内容', '', 'textarea', [], 220)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->normalPerfTable());
                
            }else{
                $view->setPk('id')
                ->setData($realData)
                ->setReple('day_dst_')
                ->setAction('/plan/perfdst/update?type=1&userid='.$where['userid'].'&type_num='.$typeId.'', '/plan/perfdst/delete?type=1')
                ->addRow('id', '序号', '', 'text', [], 40, ['readOnly' => 1])
                ->addRow('settime', '日期', '', 'datepicker', [], 150)
                ->addRow('staffname', '员工姓名', '', 'select', [$loginName=>$loginName], 80)
                ->addRow('name', '项目', '', 'text', [], 100)
                ->addRow('target_ji', '季目标', '', 'select', $target_ji, 80)
                ->addRow('target_yue', '月目标', '', 'select',$target_yue, 80)
                ->addRow('target_week', '周目标', '', 'select', $target_week, 80)
                ->addRow('content', '工作内容', '', 'textarea', [], 180)
                ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
                ->addRow('process', '工作进度', '', '',[] , 100);
                
                $this->_view->assign('view', $view->showPerf());
            } 
        }
       
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        
        $this->_view->assign('startDate', $startTime);
        $this->_view->assign('endDate', $endTime);
        
        
        if(count($zzjgName_tree)>1){
            $this->_view->assign('zzjgStr', $zzjgStr);
            $this->_view->assign('zzjgName', $zzjgName);
            $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }else{
            isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
            isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
            isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);
        }

        $userid = urldecode($userid);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(1, $date, $typeId);
        $this->_view->assign('userid', $userid);
        $this->_view->assign('loginid', $this->session->get('managerId'));
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'day_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 1 );
        if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid,$reply_id);           
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
    }

    
 /**
     *  查看的个人日目标
     *
     */
    
    public  function tabdaymanagerAction(){
    
      //  var_log($_REQUEST,'#############quest#########');
        
        $pager = new \Sooh\DB\Pager(1000);
    
        $endTime=$this->_request->get('endTime');
        $startTime=$this->_request->get('startTime');
        $ids=$this->_request->get('id');
        $typeId=$this->_request->get('typeid');
        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        
        $zzjgName=\Prj\Data\MBPerfDst::loopFindRecords(['id'=>$ids]);
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
        $where['settime]']=$startTime;
        $where['settime[']=$endTime;
        $where['type']=1;
        // $where['id']=$ids;
    
        $arr=['未选择'];
        $target_ji=\Prj\Data\MBPerfDst::getpair(4,$zzjgName);//季度目标
        $target_yue=\Prj\Data\MBPerfDst::getpair(3,$zzjgName);//月度目标
        $target_week=\Prj\Data\MBPerfDst::getpair(2,$zzjgName);//周度目标
        !empty($target_ji)?$target_ji[0]="未选择":$target_ji=$arr;
        !empty($target_yue)?$target_yue[0]="未选择":$target_yue=$arr;
        !empty($target_week)?$target_week[0]="未选择":$target_week=$arr;
        
        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
    
        foreach ($data as $rs){
            $t = intval($rs['process']);
            !empty($t)?$t:0;
            $tmp = [ 'type' => 'process',	//类型
                'percent' => intval($t), //进度（0-100）
                'value' => $t.'%', //显示的文本
                'prefix' => 'day_dst_']; //进度条id的前缀，方便js查找
            $rs['process'] = $tmp;
             
            $realData[] = $rs;
        
        }
        
        $view = new \Prj\Misc\ViewFK();
        $view->setEditCallback(null, 'myEditCallback' );
        $view->setPk('id')
        ->setData($realData)
        ->setReple('day_dst_')
        //->setOperate(true)
        //->hideDefaultBtn()
        ->setAction('/plan/perfdst/update?type=1&userid='.$where['userid'].'', '/plan/perfdst/delete?type=1')
        ->addRow('id', '序号', '', 'text', [], 40, ['readOnly' => 1])
        ->addRow('settime', '日期', '', 'datepicker', [], 100)
        ->addRow('staffname', '员工姓名', '', 'select', [$realname=>$realname], 100)
        ->addRow('name', '项目', '', 'text', [], 100)
        ->addRow('target_ji', '季目标', '', 'select', $target_ji, 80)
        ->addRow('target_yue', '月目标', '', 'select',$target_yue, 80)
        ->addRow('target_week', '周目标', '', 'select', $target_week, 80)
        ->addRow('content', '工作内容', '', 'textarea', [], 220)
        ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120)
        ->addRow('process', '工作进度', '', '',[] , 100);
        
        if ($this->manager->getField('loginName') == 'wangdong' || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
    
        $this->_view->assign('view', $view->showPerf());
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
    
        $userid = urldecode($zzjgName);
        $batchid = \Prj\Data\MBPerfDst::buildTypeId(1, $date, $typeId);     
        $this->_view->assign('userid', $zzjgName);
        $this->_view->assign('batchid', $batchid);
        $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
        $this->_view->assign('prefix', 'day_dst_');
        $this->_view->assign('createReply', 1);
        $this->_view->assign( 'dstType', 1 );
        if (!empty($data)) {
            $records = \Prj\Data\MBPerfReply::getMyReply($zzjgName, $batchid);
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
        else
        {
        	$this->_view->assign('records', []);
        	$this->_view->assign('showReply', 0);
        }
    
    }
    /**
     * 获取季度目标是否设置
     * @return int
     */
    public function getquartersetAction()
    {
        $year = $this->_request->get('year');
        $where['type'] = 4;
        $arrQuarterNums = [1, 2, 3, 4];
        $where['type_num'] = $arrQuarterNums;
        $where['userid'] = $this->session->get('managerId');
        $where['dst_date'] = $year . '-01-01';
        $where['del']=0;
       
        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);

        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrQuarterNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }

        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    /**
     * 获取月目标是否设置
     * @return int
     */
    public function getmonthsetAction()
    {
        $year = $this->_request->get('year');
        
        $where['type'] = 3;
        $arrMonthNums = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        
        $where['type_num'] = $arrMonthNums;
        $where['userid'] = $this->session->get('managerId');
        $where['dst_date'] = $year . '-01-01';
        $where['del']=0;
        
        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrMonthNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }

        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;

    }

    /**
     * 获取周目标是否设置
     * @return int
     */
    public function getweeksetAction()
    {
        $year = $this->_request->get('year');
        $month = $this->_request->get('month');

        
        $startWeek = (int)date('W', strtotime($year . '-' . $month . '-01'));
        $endWeek = (int)date('W', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))));
        $arrWeekNums = [];

        if ($startWeek > $endWeek) {
            $startWeek = 1;
        }

        for ($i = $startWeek; $i <= $endWeek; $i++) {
            $arrWeekNums[] = $i;
        }
        #######新增判断本周设置###############
        $date=date('W',\Sooh\Base\Time::getInstance()->timestamp());
        foreach ($arrWeekNums as $k=>$v){
            if($v==$date){
                $arrWeekNum[$k]=$v;
            }
        }
        ################################
        
        var_log($arrWeekNum, 'arra');
        $where['type'] = 2;
        $where['type_num'] = $arrWeekNum;
        $where['dst_date'] = $year . '-01-01';
        $where['userid'] = $this->session->get('managerId');
        $where['del']=0;
        
        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrWeekNum as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k] = 1;
                }
            }
        }
        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    /**
     * 获取日目标是否设置
     * @return int
     */
    public function getdaysetAction()
    {
        $year = $this->_request->get('year');
        $month = $this->_request->get('month');

        $startDay = date('z', strtotime($year . '-' . $month . '-01'));
        $endDay = date('z', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))));
        $arrDayNums = [];
        for ($i = $startDay; $i <= $endDay; $i++) {
            $arrDayNums[] = $i;
        }

        $where['type'] = 1;
        $where['type_num'] = $arrDayNums;
        $where['userid'] = $this->session->get('managerId');
        $where['del']=0;
        
     
        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrDayNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }
        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    public function getYearQuarterAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $year = $this->_request->get('year');
        //TODO ...

        $data = [
            ['name' => '第一季度', 'set' => 1],
            ['name' => '第二季度', 'set' => 0],
            ['name' => '第三季度', 'set' => 1],
            ['name' => '第四季度', 'set' => 0],
        ];
        $this->_view->assign('data', $data);

    }

    public function updateAction()
    {
        $addFlag = false;
        $data = $this->getRequest()->get('values')[0];
        $type_num=$this->getRequest()->get('type_num');
        $key = $this->getRequest()->get('id');
        $deltype=$this->getRequest()->get('type');
        
        if($deltype==3){
          $set_time=$data['settime'];
          $typenum=date('m',strtotime($set_time));
          if($typenum!=$type_num)$type_num=$typenum;
        }elseif($deltype==4){
            $set_time=$data['settime'];
            $typenum=ceil(date('m', strtotime($set_time)) / 3);
            if($typenum!=$type_num)$type_num=$typenum;
        }elseif($deltype==2){
            $set_time=$data['settime'];
            $typenum=date('W',strtotime($set_time));
            if($typenum!=$type_num)$type_num=$typenum;
        }elseif ($deltype==1){
            $set_time=$data['settime'];
            $typenum=date('z',strtotime($set_time));
            if($typenum!=$type_num)$type_num=$typenum;
        }
        
        if (empty($key)) {
            $addFlag = true;
        }
        unset($data['id']);

        if (empty($data)) {
            $this->returnError('没有更新的内容，更新失败');
            return 0;
        }
       
        $userId=$this->getRequest()->get('userid');
        $userid = $this->session->get('managerId');

        $this->_view->assign( 'type', $this->getRequest()->get( 'type' ) );
        
        
      // var_log($_REQUEST,'#########quest############');
        
        if ($addFlag) {
            try {
                if($userId==$userid)
                {
                	$data['type'] = $this->getRequest()->get('type');
                	$ret = \Prj\Data\MBPerfDst::addData($data['settime'],$data['staffname'],$data['name'], $data['content'], $data['level'], $data['type'],$data['target_ji'],$data['target_yue'],$data['target_week'],$data['process'],$type_num,
                	    \Sooh\Base\Time::getInstance()->ymdhis(),$userid, $userid);
                }else{
                    $data['type'] = $this->getRequest()->get('type');
                    $date=\Sooh\Base\Time::getInstance()->ymdhis();
//                     $ret = \Prj\Data\MBPerfDst::addData($data['name'], $data['content'], $data['level'], $data['type'], \Sooh\Base\Time::getInstance()
//                         ->ymdhis(), $userId, $userId);
                    $ret = \Prj\Data\MBPerfDst::addData($data['settime'],$data['staffname'],$data['name'], $data['content'], $data['level'], $data['type'],$data['target_ji'],$data['target_yue'],$data['target_week'],$data['process'],$type_num, $date, $userId, $userId);
                    
                    $typeNum = \Prj\Data\MBPerfDst::buildTypeNum($data['type'], strtotime($date));
                    !empty($type_num)&&$type_num!=$typeNum;
                    $typeNum=$type_num;
                    $typeId =  \Prj\Data\MBPerfDst::buildTypeId($data['type'], $date, $typeNum);

                    $where['type_id']=$typeId;
                    $goldbid=\Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000),$where);
                    rsort($goldbid);
                    $goldbid=$goldbid[0]['id'];
                   // var_log(\Sooh\DB\Broker::lastCmd(),'glodbidsql########################');
                    $batch_type=1;
                    //$content=$introduce.'&nbsp;&nbsp;'."目标任务序列号：".$goldbid;
                    $content="目标任务序列号：".$goldbid;
                    $result = Reply::addRecord($content, $userid, $userId, Reply::zhipai, $batch_type, $typeId,[$userId]);
                    
                    $reply_id=Reply::getIdnum();
                    
                    if($result) {
                       
                        Message::addRecord('', $content, $userid, $userId,$userId,\Prj\Data\MBPerfReply::zhipai, $batch_type,$typeId,$goldbid,$reply_id,[$userId]);
                       
                        //return $this->returnOK('指派成功');
                    }else {
                       // return $this->returnError('指派失败');
                    }
                }
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
            $model = \Prj\Data\MBPerfDst::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }
                     
            try {
                $data['type_num']=$type_num;
                if ($deltype == 4 || $deltype == 3 || $deltype == 2) {
                    $data['dst_date'] = date('Y', strtotime($set_time)) . '-01-01';
                }else{
                    $data['dst_date']=date('Y-m-d',strtotime($set_time));
                }
                $ret = \Prj\Data\MBPerfDst::updData($key, $data);
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

    protected function deleteAction()
    {
        $key = $this->getRequest()->get('_id');

        $perf_id=Message::loopFindRecords(['perf_id'=>$key]);
        if($perf_id){
         return $this->returnError('该目标已被管理员操作过，无法删除');
        }
        $perflog_id=\Prj\Data\MBPerfDailylog::loopFindRecords(['name'=>$key,'del'=>0]);
        if($perflog_id){
            return $this->returnError('该目标已经开展工作，无法删除');
        }
        if (\Prj\Data\MBPerfDst::delData($key)) {
            $this->returnOK('删除成功');
        } else {
            $this->returnError('记录不存在或者已经被删除');
        }
        return 0;
    }
    
    protected function detailAction()
    {
        $key = $this->getRequest()->get('id');
        $rs = \Prj\Data\MBPerfDst::getData($key);
        var_log( $rs, 'rs' );
        if ( $rs )
        {
            $this->_view->assign( 'perfdst_detail',
                [ 	'id' => $rs->getField('id'),
                    'settime' => $rs->getField( 'settime' ),
                    'staffname' => $rs->getField( 'staffname' ),
                    'name' => $rs->getField( 'name' ),
                    'content' => $rs->getField( 'content' ),
                    'level' => $rs->getField('level'),
                    'type' => $rs->getField('type'),
                    'type_num' => $rs->getField( 'type_num' ),
                    'target_ji' => $rs->getField( 'target_ji' ),
                    'target_yue' => $rs->getField( 'target_yue' ),
                    'target_week' => $rs->getField( 'target_week' ),
                    'process' => $rs->getField( 'process' ),
                    'create_userid' => $rs->getField( 'create_userid' ),
                    'userid' => $rs->getField( 'userid' ) ] );
        }
        else
        {
            $this->returnError( '记录不存在或者已经被删除' );
        }
         
        return 0;
    }
    
    protected function genRandFlag()
    {
    	$mtRandId = '_' . mt_rand( 1000000, 9999999 );
    	$this->_view->assign( 'mtfuncRandId', $mtRandId );
    }
}