<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/21 0021
 * Time: 上午 10:50
 */
use \Prj\Data\MBMessage as MbMessage;
class MessageController extends \Prj\ManagerCtrl{
    public function init(){
        parent::init();
        if($this->_request->get('__VIEW__')=='json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }
    public function indexAction () {
//        $userId = $this->session->get('managerId');
//        list($loginName,$cameFrom) = explode('@', $userId);
//        $unreadCount = MbMessage::unReadMessage($userId);
//        $this->_view->assign('unreadCount', $unreadCount);
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
        }

        $model->setField('flag', 1);
        $model->setField('read_time', date('Y-m-d H:i:s'));
        $model->setField('update_time', date('Y-m-d H:i:s'));
        try{
            $model->update();
            $this->_view->assign('id', $id);
            return $this->returnOk();
        }catch (\ErrorException $e){
            return $this->returnError();
        }
    }

}