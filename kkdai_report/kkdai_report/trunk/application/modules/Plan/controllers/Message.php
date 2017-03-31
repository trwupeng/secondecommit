<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/21 0021
 * Time: 上午 10:50
 */
use \Prj\Data\MBMessage as MbMessage;
use Prj\Data\MBPerfReply as Reply;

class MessageController extends \Prj\ManagerCtrl{
    public function init(){
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }
    public function indexAction () {
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $unreadCount = MbMessage::unreadNum($userId);
        var_log( $unreadCount, 'unreadCount' );
        if ( $unreadCount == 0 ) {
        	$this->_view->assign( 'allid', 'firstAjaxHerf' );
        	$this->_view->assign( 'unreadid', 'none' );
        }
        else {
        	$this->_view->assign( 'unreadid', 'firstAjaxHerf' );
        	$this->_view->assign( 'allid', 'none' );
        }
    }

    public function mymessageAction () {
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $where=[
            'receiverid'=>$loginName.'@'.$cameFrom,
        ];
        $records = MbMessage::myMessage($where, null, true);
        $this->_view->assign('records', $records);
    }
    
    public function myUnreadMessageAction() 
    {
    	$userId = $this->session->get('managerId');
    	list($loginName,$cameFrom) = explode('@', $userId);
    	$where=[
    	'receiverid'=>$loginName.'@'.$cameFrom,
    	'flag' => [0,2],//新增flag=2也为未读消息
    	];
    	$recordsUnread = MbMessage::myMessage($where, null, true);
    	$this->_view->assign('recordsUnread', $recordsUnread);
    }

    public function mytraceAction() {
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $where=[
            'sendid'=>$loginName.'@'.$cameFrom,
            'type'=>2
        ];
        $records = \Prj\Data\MBPerfReply::getMyTrace($where, null,true);
        $this->_view->assign('records', $records);
    }
    

    public function allrecordsAction() {
        $userId = $this->session->get('managerId');
        list($loginName,$cameFrom) = explode('@', $userId);
        $where=[
            'receiverid'=>$loginName.'@'.$cameFrom,
        ];
        $records = MbMessage::myMessage($where);
        $this->_view->assign('records', $records);
    }

    public function markreadedAction () {
        
        //var_log($_REQUEST,'########quest############');
       // var_log($jumpParams,'########quest############');
        $jumpParams=$this->_request->get('jumpParams');
        $jumpParams=urldecode($jumpParams);
        $jumpParams=json_decode($jumpParams,true);
        $quest_userid=$jumpParams['message_sendid'];
        $quest_batch_type=$jumpParams['batch_type'];
        $quest_perf_id=$jumpParams['perf_id'];
        $quest_reply_id=$jumpParams['reply_id'];
        $quest_message_type=$jumpParams['message_type'];
        $login_userid=$this->session->get('managerId');
        
        $id = $this->_request->get('id');
        if(empty($id)){
            return $this->returnError('参数错误');
        }

        $model = MbMessage::getCopy($id);
        $model->load();
        $flag = $model->getField('flag');
        if($flag==1){
            $this->_view->assign('id', $id);
            return $this->returnOK();
        }elseif($flag==2){
            $model->setField('flag', 3);
            $model->setField('read_time', date('Y-m-d H:i:s'));
            $model->setField('update_time', date('Y-m-d H:i:s'));
        }else{
            $model->setField('flag', 1);
            $model->setField('read_time', date('Y-m-d H:i:s'));
            $model->setField('update_time', date('Y-m-d H:i:s'));
        }

        try{
            $model->update();
            $this->_view->assign('id', $id);
           // $message_Id=MbMessage::getIdnum();
            $message_Model=MbMessage::getCopy($id);
            $message_Model->load();
            $message_Flag = $message_Model->getField('flag');
            if($message_Flag!=3){
            if($quest_batch_type==1)$content="已查看目标任务序列号：".$quest_perf_id;
            else $content="已查看目志任务序列号：".$quest_perf_id;
            $Reply=Reply::getCopy($quest_reply_id);
            $Reply->load();
            if($Reply->exists())
            $batchid=$Reply->getField('batchid');
            $ret = Reply::addRecord($content, $login_userid, $quest_userid, Reply::reply, $quest_batch_type, $batchid,[$quest_userid], $quest_reply_id);
            if($ret) {
                MbMessage::addRecord('', $content, $login_userid, $quest_userid,$quest_userid,\Prj\Data\MBPerfReply::reply, 3,$batchid,$quest_perf_id,$quest_reply_id,[]);
                $message_id=MbMessage::getIdnum();
                $message_model=MbMessage::getCopy($message_id);
                $message_model->load();
                $message_model->setField('flag', 2);
                $message_model->update();
            }else {
          }
            }
            return $this->returnOk();
        }catch (\ErrorException $e){
            return $this->returnError();
        }
    }
    
    public function msgNumAction() {
    	$userid = $this->_request->get( 'userid' );
    	$num = MbMessage::unreadNum( $userid );
    	$this->_view->assign( 'num', $num );
    	$this->_view->assign( 'result', 0 );
    }

}