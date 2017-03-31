<?php

/**
 * 管理员基本类，主要处理登入，登出
 *
 */
class ManagerController extends \Prj\ManagerCtrl
{

    public function indexAction()
    {
        $view = $this->_request->get('__VIEW__');
        if (empty($view)) {
            if ($this->_isMobileRequest()) {
                \Sooh\Base\Ini::getInstance()->viewRenderType('wap');
            }
        }
        if ($this->_request->get('dowhat') == 'logout') {
            $this->_view->assign('useTpl', 'logout');
            \Sooh\Base\Session\Data::getInstance()->set('managerId', '');
            \Sooh\Base\Session\Data::getInstance()->set('rights', '');
        } elseif ($this->manager) {//已经登入,进主页
            $this->manager->load();
            $this->manager->setField('lastIP', $_SERVER['REMOTE_ADDR']);
            $this->manager->setField('lastYmd', time());
            $this->menu();
            $this->_view->assign('leftmenus', $this->manager->acl->getMenuMine());
            $this->_view->assign('useTpl', 'homepage');
            $ajaxData = array();
            $ajaxData['url'] = 'http://'.$_SERVER['HTTP_HOST'];
            $ajaxData['param'] = json_encode( array( 
            		'userid' => $this->manager->getField( 'loginName' ) . '@' . $this->manager->getField( 'cameFrom' ), 
            		'__VIEW__' => 'json',
            ) );
            $this->_view->assign( 'ajax_data', $ajaxData );
            $this->_view->assign( 'loginName', $this->manager->getField( 'loginName' ) );
            if ($this->_request->get('__VIEW__') == 'wap') {
                var_log('this is wap >>>>>>>>>>>>>');
                setcookie('divicetype','wap',time()+86400);
                switch ($this->_request->get('selectA')) {
                    case 'rb':
                        $slectA = '手机版';
                        break;
                    case 'pc':
                        $slectA = 'PC版';
                        break;
                    case 'cms':
                        $slectA = 'CMS系统';
                        break;
                    default:
                        '日报';
                }
                $this->_view->assign('selectA', $slectA);
            }else{
                setcookie('divicetype','pc',time()+86400);
            }
            try {
                $this->manager->update();
            } catch (\Exception $e) {
                //TODO
            }
            $this->_view->assign('nickname', $this->manager->getField('nickname'));
        } else {//尚未登入，去登入页
			$this->ini->viewRenderType('wap');
			throw new \ErrorException('needs login',301);
        }
    }

    protected function _isMobileRequest()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) $mobile_browser++;
        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)) $mobile_browser++;
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) $mobile_browser++;
        if (isset($_SERVER['HTTP_PROFILE'])) $mobile_browser++;
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array('w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-');
        if (in_array($mobile_ua, $mobile_agents)) $mobile_browser++;
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false) $mobile_browser++;
        // Pre-final check to reset everything if the user is on Windows
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false) $mobile_browser = 0;
        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false) $mobile_browser++;
        if ($mobile_browser > 0) return true; else  return false;
    }

    /**
     * @param  \Sooh\Base\Acl\Menu $_ignore_
     */
    protected function menu($_ignore_ = null)
    {
        $menus = $this->manager->acl->getMenuMine();
        $this->_view->assign('menus', $menus);
    }
	/**
	 * 
	 * @param \Prj\Data\Manager $acc
	 * @param string $p
	 * @return bool
	 */
	protected function _chkPwd($acc, $p)
	{
		if($acc->exists()){
			$dtForbidden = $acc->getField('dtForbidden');
			return $acc->getField('passwd') == $p && ($dtForbidden==0 || $dtForbidden<\Sooh\Base\Time::getInstance()->timestamp());
		}else{
			return false;
		}
	}
	public function loginagainAction()
	{
		$u = $this->_request->get('u');
        $p = $this->_request->get('p');
        $f = $this->_request->get('from', 'local');
        if (!empty($u) && !empty($p)) {
            $acc = \Prj\Data\Manager::getCopy($u, $f);
            $acc->load();
            if ($this->_chkPwd($acc,$p)) {
                $this->session->set('managerId', $u . '@' . $f);
                $this->loger->ret = 'login ok';
                $this->loger->ext = $u . '@' . $f;
                if (!empty($r)) {
                    setcookie('u', $u, time() + 86400 * 30);
                } else {
                    setcookie('u', '');
                }
                
				$this->manager = \Prj\Data\Manager::getCopyByManagerId($u . '@' . $f);
				$this->manager->load();
				$this->manager->setField('lastIP', \Sooh\Base\Tools::remoteIP());
				$this->manager->setField('lastYmd', \Sooh\Base\Time::getInstance()->timestamp());
				$this->manager->update();
				$this->returnOK("欢迎回来");
				$this->closeAndReloadPage();
            } else {
                $this->returnError('用户名错误或密码错误或帐号已禁用');
            }
			$this->ini->viewRenderType('json');
        } else {
            $acc = \Prj\Data\Manager::getCopy('fdg');
            $n = $acc->db()->getRecordCount($acc->tbname());
            if ($n == 0) {
                $acc->db()->addRecord($acc->tbname(),
                    ['cameFrom' => 'local', 'loginName' => 'root', 'passwd' => '123456','rights'=>'*.*']);
            }
        }
		$this->_view->assign('errTrans',$this->_request->get('errTrans'));
	}
    public function loginAction()
    {
        $u = $this->_request->get('u');
        $p = $this->_request->get('p');
        $rember = $this->_request->get('remember',0);
		$choseViewType=$this->_request->get('viewType','www');
        $f = $this->_request->get('from', 'local');
        if (!empty($u) && !empty($p)) {
            $acc = \Prj\Data\Manager::getCopy($u, $f);
            $acc->load();
            if ($this->_chkPwd($acc,$p)) {
                $this->session->set('managerId', $u . '@' . $f);
                //var_log($sessionData,'$sessionData>>>>>>>>>>>>');
                $this->loger->ret = 'login ok';
                $this->loger->ext = $u . '@' . $f;
                if (!empty($r)) {
                    setcookie('u', $u, time() + 86400 * 30);
                } else {
                    setcookie('u', '');
                }
				if($rember){
					setcookie('last_login_name', $u, time() + 86400 * 30);
				}else{
					setcookie('last_login_name', '', time() - 86400 * 30);
				}
				setcookie('last_login_view', $choseViewType, time() + 86400 * 30);
				$this->session->set('chosedView', $choseViewType);
                $this->returnOK();
				$this->manager = \Prj\Data\Manager::getCopyByManagerId($u . '@' . $f);
				$this->manager->load();
				$this->manager->setField('lastIP', \Sooh\Base\Tools::remoteIP());
				$this->manager->setField('lastYmd', \Sooh\Base\Time::getInstance()->timestamp());
				$this->manager->update();
            } else {
                $this->returnError('用户名错误或密码错误或帐号已禁用');
            }
        } else {
            $acc = \Prj\Data\Manager::getCopy('fdg');
            $n = $acc->db()->getRecordCount($acc->tbname());
            if ($n == 0) {
                $acc->db()->addRecord($acc->tbname(),
                    ['cameFrom' => 'local', 'loginName' => 'root', 'passwd' => '123456','rights'=>'*.*']);
            }
            $this->_view->assign('useTpl', 'login');
        }
		$this->_view->assign('errTrans',$this->_request->get('errTrans'));
    }

    public function welcomeAction()
    {
		$sessionData = \Sooh\Base\Session\Data::getInstance();
        $this->manager->load();
        $data['ip'] = $this->manager->getField('lastIP');
        $data['ymd'] = date('Y-m-d H:i:s', $this->manager->getField('lastYmd'));        
        $this->_view->assign('data', $data);
        
        \Prj\Data\DataTest::test();
    }

    public function resetpwdAction()
    {

    }

    public function testAction()
    {
        //\Sooh\Base\Session\Data::getInstance()->set('managerId', '');
        //$this->returnError(null,301);
        $this->ini->viewRenderType('json');
        $this->_view->assign('get', $this->_request->getQuery());
        $this->_view->assign('_', SOOH_INDEX_FILE);
        $this->_view->assign('dt', time());
    }

    /**
     * 改造：没登入的情况下不丢异常
     */
    protected function onInit_chkLogin()
    {
		try{
			parent::onInit_chkLogin();
		} catch (\ErrorException $ex) {
			$code = $ex->getCode()-0;
			$act = strtolower($this->_request->getActionName());
			
			if($code===300 || $code==301){
				if($act==='login' || $act==='loginagain' || $act==='index' || $act==='welcome' || $act == 'visualreport'){
					if($this->manager && !$this->manager->exists()){
						$this->manager = null;
					}
				}else{
					error_log("[A-$code]$act:".$ex->getTraceAsString());
					throw $ex;
				}
			}else{
				error_log("[B-$code]$act:".$ex->getTraceAsString());
				throw $ex;
			}
		}
    }

    protected function _wap()
    {

    }

    public function visualreportAction () {
        error_log('visualreport action #################');
    }

}
