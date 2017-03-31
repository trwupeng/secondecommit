<?php
namespace Prj;
use Sooh\Base\ErrException;

/**
 * 
 */
class ManagerCtrl  extends \Prj\BaseCtrl {
//	protected function getFromRaw()
//	{
//		$s = file_get_contents('php://input');
//		if(!empty($s)){
//			parse_str($s,$inputs);
//			return $inputs;
//		}else{
//			return $inputs=array();
//		}
//	}
	public function init()
	{
		define('SOOH_USE_REWRITE', 1);
		parent::init();
        self::$currentManagerCtrl = $this;
		$this->onInit_chkLogin();
        //$this->checkRights();
//		$render = $this->ini->viewRenderType();
	}
	
	protected function tabname($act=null,$ctrl=null,$mod=null)
	{
		if($act===null){
			$act = $this->_request->getActionName();
		}
		if($ctrl===null){
			$ctrl = $this->_request->getControllerName();
		}
		if($mod===null){
			$mod = $this->_request->getModuleName();
		}
		return strtolower("{$mod}_{$ctrl}_{$act}");
//		$ret = $this->manager->acl->getMenuPath($act,$ctrl,$mod);
//		if($ret){
//			$tmp=explode('.',$ret);
//			return 'page_'.array_pop($tmp);
//		}else{
//			throw new \ErrorException("unknown tabname for $mod/$ctrl/$act");
//		}
	}
	protected $pageSizeEnum=[10,50,100];
	protected function useJsonIfNotSet()
	{
		$tmp = $this->ini->viewRenderType();
		if($tmp!=='json' && $tmp!=='jsonp'){
			$this->ini->viewRenderType('json');
		}
	}
	protected function returnError($msg='',$code=300)
	{
		$this->useJsonIfNotSet();
		$this->_view->assign('statusCode',$code);
		if(!empty($msg)){
			$this->_view->assign('message',$msg);
		}
	}
	protected function returnOK($msg='',$code=200)
	{
		$this->useJsonIfNotSet();
		$this->_view->assign('statusCode',$code);
		if(!empty($msg)){
			$this->_view->assign('message',$msg);
		}
	}
	/**
	 * 关闭当前页面或窗口，如果指定了$tabPageId，则刷新对应的tab页
	 * @param string $tabPageId
	 */
	protected function closeAndReloadPage($tabPageId=null)
	{
		//$this->_view->assign ('callbackType', 'closeCurrent');
		$this->_view->assign ('closeCurrent', true);
		if($tabPageId){
			//$this->_view->assign ('navTabId', $tabPageId);
			$this->_view->assign ('tabid', $tabPageId);
			//$this->_view->assign ('tabid', '');
		}
	}
	
	protected function downExcel($records,$title=null,$config = [] ,$filename=null,$scientificFlg=true)
	{
		if($filename===null){
			$filename = str_replace('page_', '', $this->tabname()).'_'.date('Y_m_d');
		}
		$this->ini->viewRenderType('echo');
		header("Pragma:public");
		header("Expires:0");
		header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl;charset=utf8");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition:attachment;filename="'.$filename.'.xls"');
		header("Content-Transfer-Encoding:binary");

		reset($records);
		if(empty($title)){
			$title = array_keys(current($records));
		}
		//echo iconv('utf-8', 'gbk', implode("\t", $title))."\n";
        $htmlHead = <<<html
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <!--[if gte mso 9]><xml>
    <x:ExcelWorkbook>
    <x:ExcelWorksheets>
      <x:ExcelWorksheet>
      <x:Name></x:Name>
      <x:WorksheetOptions>
        <x:DisplayGridlines/>
      </x:WorksheetOptions>
      </x:ExcelWorksheet>
    </x:ExcelWorksheets>
    </x:ExcelWorkbook>
    </xml><![endif]-->
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
</head>
html;
        echo $htmlHead;

        echo "<table>";
        echo iconv('utf-8', 'utf-8', "<tr><td>".implode("</td><td>", $title).'</td></tr>');
		foreach($records as $r){
            foreach ($r as $k => $v) {
                if($config[$k] == 'string'){
                    $r[$k] = '<td style="vnd.ms-excel.numberformat:@">' . $v .'</td>';
                    continue;
                }else if($config[$k] == 'int'){
                    $r[$k] = '<td>'. $v .'</td>';
                    continue;
                }

			    if (strlen($v) > 11 && $scientificFlg && is_numeric($v)){
                    $r[$k] = '<td style="vnd.ms-excel.numberformat:@">' . $v .'</td>';
			    }else {
			        $r[$k] = '<td>'. $v .'</td>';
			    }
			}
            echo "<tr>";
			//echo iconv('utf-8', 'gbk', implode("\t", $r))."\n";
            echo iconv('utf-8', 'utf-8', implode("", $r));
            echo "</tr>";
		}
        echo "</table>";
	}
	
	protected function getInputs()
	{
		return array_merge($this->_request->getQuery(),$this->_request->getPost(),$this->_request->getParams());
	}

	protected function getManagerLog($managerId,$pageid)
	{
        unset($managerId);
        unset($pageid);
		return array(
			array('managerId'=>'191704475514345770','evt'=>'login'),
			array('managerId'=>'502774624898912753','evt'=>'logout'),
		);
	}

    protected static $accessModule = ['risk','report','manage']; //权限控制作用域

	public static $accessPre = ''; //权限控制器标识

	public static $currentManagerCtrl; //当前控制器实例

	protected function onInit_chkLogin()
	{
		$this->session = \Sooh\Base\Session\Data::getInstance();
		if($this->session){
			$userId = $this->session->get('managerId');
			if ($userId){
			    self::$accessPre = strtolower($this->_request->getModuleName().'_'.$this->_request->getControllerName()); //权限前缀(当前controllername)
				list($loginName,$cameFrom) = explode('@', $userId);
				$this->manager = \Prj\Data\Manager::getCopy($loginName,$cameFrom);
				$this->manager->load();
                $this->setRights(); //权限初始化
                if($loginName != 'root')$this->manager->acl->newRights = $this->manager->rights;
                $this->manager->acl->accessModule = self::$accessModule;
                if(in_array(strtolower($this->_request->getModuleName()),self::$accessModule)){
                    //$this->_saveRights($this->_rightName()); //保存权限名
                    if($loginName != 'root'){
                        $this->checkRights();//权限检查
                        \Prj\Misc\ViewFK::checkRight($this->manager->rights,strtolower($this->_request->getModuleName().'_'.$this->_request->getControllerName()));//viewFK权限设置
                    }
                }
				$wapMenu = [];
				$m = strtolower($this->_request->getModuleName());
				$c = strtolower($this->_request->getControllerName());
				$a = strtolower($this->_request->getActionName());
				$curView = 'wap';
				$wapMenu['__']='个人中心';
				if($this->manager->acl->hasRightsFor('report', 'rptdailybasic')){
                    $wapMenu['日报(整合)'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'recent', 'rptdailybasic', 'report');
					if($m=='report' && $c=='rptdailybasic'){
						$wapMenu['__']='日报';
					}
					$wapMenu['日报(数字)'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'recent2', 'rptdailybasic', 'report');
					if($m=='report' && $c=='rptdailybasic'){
						$wapMenu['__']='日报';
					}
				}
                
				$callin=['pcsitetraffic','umengdata','regtoinvestmenttrans',
				        'regtoinvestmenttransrate','newfinancial','newlicaiamount','newfinancialavg',
				        'oldandnewfinancial','oldandnewfinancialamount','oldandnewfinancialavg',
				        'fundsdata','retaineddata','compounddata','compoundrate'];
				
				$wapMenu['可视化报表'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'visualreport', 'manager', 'manage');
				if($m=='report' && in_array($c, $callin)){
				    switch ($c){
				       case 'pcsitetraffic':
				            $wapMenu['__'] = '可视化报表';
				            break;
				        
				       case 'umengdata':
				            $wapMenu['__'] = '可视化报表';
				            break;
				        
				       case 'regtoinvestmenttrans':
				            $wapMenu['__'] = '可视化报表';
				            break;
				            
				       case 'regtoinvestmenttransrate':
				            $wapMenu['__'] = '可视化报表';
				            break;
				        
				       case 'newfinancial':
				            $wapMenu['__'] = '可视化报表';
				            break;
				        
				       case 'newlicaiamount':
				            $wapMenu['__'] = '可视化报表';
				            break;
				            
		               case 'newfinancialavg':
		                    $wapMenu['__'] = '可视化报表';
		                    break;
				        
				       case 'oldandnewfinancial':
				            $wapMenu['__'] = '可视化报表';
				            break;

				       case 'oldandnewfinancialamount':
				            $wapMenu['__'] = '可视化报表';
				            break;
				       
        	           case 'oldandnewfinancialavg':
        	                $wapMenu['__'] = '可视化报表';
        	                break;
        	                
    	               case 'fundsdata':
    	                    $wapMenu['__'] = '可视化报表';
    	                    break;
    	                    
	                   case 'retaineddata':
	                        $wapMenu['__'] = '可视化报表';
	                        break;
    	                        
                       case 'compounddata':
                            $wapMenu['__'] = '可视化报表';
                            break;
                            
                        case 'compoundrate':
                            $wapMenu['__'] = '可视化报表';
                            break;
				    }  
				}elseif($m=='manage' && $c =='manager' && $a =='visualreport'){
				           $wapMenu['__'] = '可视化报表';
				}
				
				error_log("手机版月报菜单暂时关闭");
//				if($this->manager->acl->hasRightsFor('report', 'monthreport')){
//                    $wapMenu['月报'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'recent', 'monthreport', 'report');
//					if($m=='report' && $c=='monthreport'){
//						$wapMenu['__']='月报';
//					}
//				}
//				if($this->manager->acl->hasRightsFor('report', 'rptconf')){
//                    $wapMenu['报表权限'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'conf', 'rptconf', 'report');
//					if($m=='report' && $c=='rptconf'){
//						$wapMenu['__']='报表权限';
//					}
//				}
				$wapMenu['个人中心'] = \Sooh\Base\Tools::uri(['__VIEW__'=>$curView], 'welcome', 'manager', 'manage');
				if($this->ini->viewRenderType()=='wap'){
					$this->_view->assign('wapMenuList',$wapMenu);
				}
//				if(!$this->manager->acl->hasRightsFor($this->_request->getModuleName(), $this->_request->getControllerName())){
				if(!$this->manager->acl->hasRightsFor($m, $c)){
//				$this->manager->rights = \Sooh\Base\Session\Data::getInstance()->get('rights')?\Sooh\Base\Session\Data::getInstance()->get('rights'):[];
				
					//$this->returnError(\Prj\ErrCode::errNoRights,300);
					//throw new \ErrorException(\Prj\ErrCode::errNoRights,300);
				}
				
			}else{
				//$this->returnError(\Prj\ErrCode::errNotLogin,301);
                throw new \ErrorException(\Prj\ErrCode::errNotLogin,301);
			}
		}
	}

	protected function checkPwd($pwd){
        return $pwd === $this->manager->getField('passwd') ? true : false;
    }

    //tgh
    public function getImageAction()
    {
        $this->ini->viewRenderType('echo');
        $fileId = $this->_request->get('fileId');
        header('Content-type: image/jpg');
        echo \Prj\Data\Files::getDataById($fileId);
    }

    /**
     * @param string $type
     * 要求的权限
     * hand 160113
     */
    protected function needRights($type){
        $str = strtolower($type);
        $rightsName = self::$accessPre.'_'.$str;
        error_log('###need access:'.$rightsName);
        if($this->manager->getField('loginName') != 'root')$this->checkRights($rightsName);
        //echo "没有权限！";
        /*
        if(in_array('*',$this->manager->rights))return;
        if(is_array($str)){
            if(!count(array_intersect($str,$this->manager->rights))){
                \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
                echo "没有权限！";
            }
        }else{
            if(!in_array($str,$this->manager->rights)){
                \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
                echo "没有权限！";
            }
        }
        */
    }

    public static function hasRights($type){
        $str = strtolower($type);
        $managerCtrl = self::$currentManagerCtrl;
        $needRight = self::$accessPre.'_'.$str;
        try{
            $managerCtrl->checkRights($needRight);
            return true;
        }catch (\ErrorException $e){
            return false;
        }
    }

    protected function _rightName(){
        return strtolower($this->_request->getModuleName().'_'.$this->_request->getControllerName().'_'.$this->_request->getActionName());
    }

    protected function checkRights($rightsName = null){
        $needRight = $rightsName ? $rightsName : $this->_rightName();
        $white = ['manage_manager_welcome','manage_manager_index'];
        if(in_array($needRight , $white))return;
        if(!in_array($needRight , \Prj\Data\Menu::getAllAction()))return; //未注册的action不受权限的控制
        if(!in_array($needRight,$this->manager->rights)){
            var_log('#没有权限#');
            throw new \ErrorException('没有权限',408);
        }
    }

    protected function _saveRights($needRight){
        $right = \Prj\Data\Rights::getCopy($needRight);
        $right->load();
        if(!$right->exists()){
            $right->setField('rightsName',$needRight);
            try{
                $right->update();
            }catch (\ErrException $e){}
        }
    }

    /**
     * 数据库权限表完整度检查
     */
    /*
    protected function checkRightsDB(){
        var_log($this->rights,'this->rights>>>>');
        if(empty($this->rights))return;
        foreach($this->rights as $k=>$v){
            $conf = \Prj\Data\Rights::getCopy($k);
            $conf->load();
            if(!$conf->exists()){
                $conf->setField('rightsType',explode('_',$k)[0]);
                $conf->setField('rightsName',$v);
                try{
                    $conf->update();
                }catch (\ErrorException $e){
                    var_log($e->getMessage());
                }
            }
        }
    }
    */


    protected function getRights(){
        return [];
        /*
        if(empty($this->modelName))return [];
        $loginName = $this->manager->getField('loginName');
        return \Prj\Data\ManagerRight::getRightsByType($loginName,$this->modelName);
        */
    }

    /**
     * 初始化权限-tgh-161017
     */
    protected function setRights(){
        $this->session = \Sooh\Base\Session\Data::getInstance();
        $rights = (array)$this->session->get('rights');
        $rights = []; //test
        if(empty($rights)){
            $rights = $this->_getRights();
            //$this->session->set('rights',$rights,300);
        }
        $this->manager->rights = $rights;
        error_log('用户权限>>>'.implode(',',$rights));
    }

    /**
     * 获取权限-tgh-161017
     */
    protected function _getRights(){
        $loginName = $this->manager->getField('loginName');
        $manageRight = \Prj\Data\ManagerRight::getCopy($loginName,'all');
        $manageRight->load();
        if(!$manageRight->exists())return [];
        $roleIds = explode(',',$manageRight->getField('roles'));
        $roleRights = [];
        foreach ((array)$roleIds as $roleId){
            $role = \Prj\Data\RightsRole::getCopy($roleId);
            $role->load();
            if($role->exists()){
                $roleRights = array_merge($roleRights , explode(',',$role->getField('rightsIds')));
            }
        }
        $otRights = explode(',',$manageRight->getField('rights'));
        $rights = array_unique(array_merge($roleRights,$otRights));
        array_walk($rights,function($v,$k)use(&$rights){
            if(empty($v))unset($rights[$k]);
        });
        return $rights;
    }

	/**
	 *
	 * @var \Sooh\Base\Session\Data 
	 */
	protected $session=null;
	/**
	 *
	 * @var \Prj\Data\Manager
	 */
	protected $manager=null;
//	protected function getUriBase()
//	{
//		return '/manage';
//	}
}