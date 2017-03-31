<?php

use Prj\BaseCtrl;
use Sooh\Base\Session\Data as SessionData;
use Prj\Data\Manager as ManagerModel;
use Prj\Data\SyncUser as SyncUserModel;

class OaController extends BaseCtrl
{
    public function checkTicketAction()
    {
        $ticket = $this->_request->get('ticket');
        if (empty($ticket)) {
            return $this->returnError('invalid ticket');
        }

        $sessionTicket = SessionData::getInstance()->get('ticket');
        if (empty($sessionTicket) || $sessionTicket != $ticket) {
            return $this->returnError('invalid ticket');
        }

        list($localName,$cameFrom) = explode('@', SessionData::getInstance()->get('managerId'));

        //get OA id from userId
        $managerModel = ManagerModel::getCopy($localName);
        $managerModel->load();
        if (!$managerModel->exists()) {
            error_log('not found this user:' . __CLASS__ . '\\' . __FUNCTION__);
            return $this->returnError('用户状态异常');
        }
        $oaId = $managerModel->getField('oa');
        if (empty($oaId)) {
            return $this->returnError('未找到OA用户的ID');
        }

        //get oa loginName from oa id
        $syncUserModel = SyncUserModel::getCopy($oaId, 'oa');
        $syncUserModel->load();
        if (!$syncUserModel->exists()) {
            error_log('DB SyncUser not found the user, now get from API');
            $syncUser = (new \Prj\Sync\SyncFactory('OA'))->getUserInfo($oaId);
            $oaLoginName = $syncUser->loginName;

            // save to DB
            try {
                SyncUserModel::save($syncUser);
            } catch (\Exception $e) {
                error_log('save to DB error:' . __CLASS__ . '\\' . __FUNCTION__);
            }
        } else {
            $oaLoginName = $syncUserModel->getField('loginName');
        }
        if (empty($oaLoginName)) {
            return $this->returnError('未找到OA用户的登录名');
        }

        Yaf_Dispatcher::getInstance()->autoRender(false);
        Yaf_Dispatcher::getInstance()->disableView();
//        header('Content-type:text/html;charset=utf-8');
        echo $oaLoginName;
    }
}
