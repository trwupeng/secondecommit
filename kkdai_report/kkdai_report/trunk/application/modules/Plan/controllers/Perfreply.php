<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/15 0015
 * Time: 下午 2:16
 */
use \Prj\Data\MBPerfReply as Reply;
use \Prj\Data\MBMessage as Message;
use \Prj\Data\MBPerfDst as PerfDst;
use \Prj\Data\MBPerfDailylog as PerfDailylog;

class PerfreplyController extends \Prj\ManagerCtrl {


    public function init() {
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    public function myreplyAction() {
        $userid = urldecode($this->_request->get('userid'));
        $batchid = $this->_request->get('batchid');
        $records = Reply::getMyReply($userid, $batchid);
        $this->_view->assign('prefix', $this->_request->get('prefix'));
        $this->_view->assign('records', $records);
    }

//    public function commentateAction () {
//        // 评论
//        Reply::addRecord('很不错', 'wudaoxue@local','lilianqi@local',1,1,20160510);
//        Message::addRecord('', '很不错','wudaoxue@local', 'lilianqi@local',1,1,20160510);
//        // 跟踪
//        Reply::addRecord('很不错', 'wudaoxue@local','lilianqi@local',2,1,20160510,[1,3,5]);
//        Message::addRecord('', '任务序列1，3，5','wudaoxue@local', 'lilianqi@local',2,1,20160510, [1,3,5]);
//
//        // @
//        Reply::addRecord('很不错', 'wudaoxue@local','lilianqi@local',3,1,20160510,['wangdong@local','gaoluntan@local']);
//        Message::addRecord('', '跟进一下','wudaoxue@local', 'lilianqi@local',3,1,20160510, ['wangdong@local','gaoluntan@local']);
//
//    }


    // 测试用
    public function getallAction() {
        $records = Message::myMessage(['receiverid'=>'lilianqi@local','batchid'=>'20160510']);
        echo "<pre>".var_export($records, true)."</pre>";
    }


    // 回复
    public function replyAction(){
        $id = $this->_request->get('id');
        $batchid = $this->_request->get('batchid');
        $parentid = $this->_request->get('parentid');
        $batch_type = $this->_request->get('batch_type');
        $receiverid = $this->_request->get('receiverid');
        $dstid = $this->_request->get('dstid');
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $sendid = $loginName.'@'.$cameFrom;
        $content = htmlspecialchars(trim($this->_request->get('content')));
        $idParentDiv = $this->_request->get('idParentDiv');
        $commentStyle = $this->_request->get('commentStyle');
        if(empty($content)){
            return $this->returnError('内容不能为空');
        }

        $rem_reply=Reply::loopFindRecords(['id'=>$id]);
        $rem_repl=$rem_reply[0]['content'];
        if(preg_match_all('/([0-9]+)/',$rem_repl,$reg)){
          $rem_perf_id=$reg[0][0];
        }
        //$rem_perf_id=implode('目标任务序列号：',$rem_reply);
        //var_log($rem_perf_id,'##############');
        
        $ret = Reply::addRecord($content, $sendid, $receiverid, Reply::reply, $batch_type, $batchid,[$dstid], $parentid);
        
       // $reply_id=Reply::getIdnum();
        
        if($ret) {
            $ret['idParentDiv'] = $idParentDiv;
            $ret['commentStyle'] = $commentStyle;

//            Message::addRecord('', $content, $sendid, $receiverid,\Prj\Data\MBPerfReply::reply, $batch_type,$batchid, [$dstid]);
            Message::addRecord('', $content, $sendid, $dstid,$receiverid,\Prj\Data\MBPerfReply::reply, $batch_type,$batchid,$rem_perf_id,$id,[]);
            $this->returnOK();
            $this->_view->assign('record', $ret);
        }else {
            return $this->returnError('回复失败');
        }
    }

    // 评论
    public function commentAction() {
        $batchid = $this->_request->get('batchid');
        $batch_type = $this->_request->get('batch_type');
        $receiverid = $this->_request->get('userid');
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $sendid = $loginName.'@'.$cameFrom;
        $content = htmlspecialchars(trim($this->_request->get('content')));
        $ids = $this->_request->get('ids');
   
        $commentStyle = $this->_request->get('commentStyle');
        $idParentDiv = $this->_request->get('idParentDiv');
        if(empty($content)){
            return $this->returnError('内容不能为空');
        }
      
        if($batch_type==1){
            $content = '目标任务序列号：'.$ids.'              '.'点评内容: '.$content;
        }else{
            $content = '日志任务序列号：'.$ids.'              '.'点评内容: '.$content;
        }

        $ret = Reply::addRecord($content, $sendid, $receiverid, Reply::comment, $batch_type, $batchid,[$receiverid]);

        $reply_id=Reply::getIdnum();
        
        if($ret) {
            $ret['idParentDiv'] = $idParentDiv;
            $ret['commentStyle'] = $commentStyle;
            if($sendid != $receiverid) {
                Message::addRecord('', $content, $sendid, $receiverid, $receiverid,\Prj\Data\MBPerfReply::comment, $batch_type,$batchid,$ids,$reply_id,[$receiverid]);
            }
            $this->_view->assign('record', $ret);
            $this->returnOK();
        }else {
            return $this->returnError('评论失败');
        }
    }

    // 跟踪
    public function traceAction () {
//var_log($_REQUEST, __CLASS__.'#####_REQEUSt######');

        $traceTargetUser = $this->_request->get('userid');
        $batchid = $this->_request->get('batchid');
        $batch_type = $this->_request->get('batch_type');
        $ids = $this->_request->get('ids');
        $idParentDiv = $this->_request->get('idParentDiv');
        $commentStyle = $this->_request->get('commentStyle');
        $userId = $this->session->get('managerId');

        if(empty($ids)) {
            return $this->returnError('无选中项');
        }elseif($userId == $traceTargetUser){
            return $this->returnError('不能跟踪自己');
        }

        $content = '任务序列号：'.implode(',', $ids);
        
        
        $ret = Reply::addRecord($content, $userId, $traceTargetUser, Reply::trace, $batch_type, $batchid, []);
//var_log($ret, 'add @ record ret####');
        $reply_id=Reply::getIdnum();
        
        if($ret) {
            $ret['idParentDiv'] = $idParentDiv;
            $ret['commentStyle'] = $commentStyle;
            Message::addRecord('', $content, $userId, $traceTargetUser,$traceTargetUser,\Prj\Data\MBPerfReply::trace, $batch_type,$batchid,$ids[0],$reply_id,[]);
            $this->_view->assign('record', $ret);
            return $this->returnOK('跟踪成功');
        }else {
            return $this->returnError('跟踪失败');
        }
    }

    // @
    public function atAction() {

        $atTargetUser = $this->_request->get('userid');
        $batchid = $this->_request->get('batchid');
        $batch_type = $this->_request->get('batch_type');
        $idParentDiv = $this->_request->get('idParentDiv');
        $userId = $this->session->get('managerId');
        $content = htmlspecialchars(trim($this->_request->get('content')));
        $dstid = $this->_request->get('dstid');
        if(in_array($userId, $dstid)) {
            return $this->returnError('不能@自己,请重新选择');
        }elseif(empty($dstid)){
            return $this->returnError('未选择要@的人员');
        }elseif(empty($content)) {
            return $this->returnError('@的评论内容不能为空');
        }
        
        $ids = $this->_request->get('ids');
        $ids=implode(',', $ids);
        if($batch_type==1){
            $content = '目标任务序列号：'.$ids.'              '.'@内容: '.$content;
        }else{
            $content = '日志任务序列号：'.$ids.'              '.'@内容: '.$content;
        }
        
        $ret = Reply::addRecord($content, $userId, $atTargetUser, Reply::at, $batch_type, $batchid, $dstid);
        $reply_id=Reply::getIdnum();
        
        if($ret) {
            $ret['idParentDiv'] = $idParentDiv;
            foreach($dstid as $user) {
                Message::addRecord('', $content, $userId, $user,$atTargetUser,\Prj\Data\MBPerfReply::at, $batch_type,$batchid,$ids,$reply_id,$dstid);
            }
            $this->_view->assign('record', $ret);
            return $this->returnOK('@成功');
        }else {
            return $this->returnError('@失败');
        }

    }
}