<?php

use Prj\BaseCtrl;
use Sooh\Base\Session\Data as SessionData;
use Prj\Data\Manager as ManagerModel;
use Prj\Data\SyncUser as SyncUserModel;
use Prj\Data\OaTicket as OaTicketModel;
use Prj\Tool\Func as Func;

class OaController extends \Prj\BaseCtrl {

	public function indexAction()
    	{
       		//build ticket and store session
    		$oaConf = \Sooh\Base\Ini::getInstance()->get( 'oa' );
        	$ticket = SyncUserModel::buildTicket( $oaConf['svrid']);
        	list($localName,$cameFrom) = explode('@', SessionData::getInstance()->get('managerId'));
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
			OaTicketModel::save( $ticket, $oaLoginName );
			if ( isset( $oaConf['center'] ) )
			{
				//回调不在本地
				Func::request($oaConf['center'] . '/manage/oa/notifyticket', ['ticket' => $ticket, 'oaloginname' => $oaLoginName, '__VIEW__' => 'json'] );
			}
			$this->_view->assign( 'ticket', $ticket );	
        	return 0;

    	}
    	
    public function notifyTicketAction()
    {
    	var_log( $_REQUEST, 'request' );
    	$ticket = $this->_request->get( 'ticket' );
    	$oaLoginName = $this->_request->get( 'oaloginname' );
    	OaTicketModel::save( $ticket, $oaLoginName );
    	return 0;
    }

	public function checkTicketAction()
    {
		var_log( 'entry', 'checkTicketAction' );
        $ticket = $this->_request->get('ticket');
        if (empty($ticket)) {
            return $this->returnError('invalid ticket');
        }

		$oaTicket = OaTicketModel::getCopy( $ticket );
		$oaTicket->load();
		if ( !$oaTicket->exists() ) {
            	return $this->returnError('ticket not exists');
        }
        $oaLoginName = $oaTicket->getField('oaLoginName');
        if (empty($oaLoginName)) {
            return $this->returnError('未找到OA用户的登录名');
        }

        Yaf_Dispatcher::getInstance()->autoRender(false);
        Yaf_Dispatcher::getInstance()->disableView();
        echo $oaLoginName;
    }
}

