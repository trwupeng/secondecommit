<?php
use Sooh\Base\Form\Item as form_def;
/**
 * 管理员一览
 * By Hand
 */
class ManagersController extends \Prj\ManagerCtrl
{
	public function indexAction()
	{
		$pageid = $this->_request->get('pageId',1)-0;
		$pager = new \Sooh\DB\Pager($this->_request->get('pagesize',10),$this->pageSizeEnum,false);
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
		$frm->addItem('_dept_eq', form_def::factory('部门', '', form_def::select)->initMore(new \Sooh\Base\Form\Options(\Prj\Consts\Manage::$depts,'不限')))
			->addItem('_nickname_lk', form_def::factory('昵称关键词', '', form_def::text))
			->addItem('_dtForbidden_eq', form_def::factory('状态', '', form_def::select)->initMore(new \Sooh\Base\Form\Options(array('2147123124'=>'已禁用','0'=>'正常'),'不限')))
			->addItem('_lastYmd_eq', form_def::factory('最后登入日期', '', form_def::datepicker))
			->addItem('_loginname_lk', form_def::factory('帐号关键词', '', form_def::text))
			->addItem('pageid', $pageid)
			->addItem('pagesize', $this->pager->page_size);
		
		$frm->fillValues();

        $keysstr = $this->_request->get('ids');
        $keys = is_array($keysstr)?$keysstr:explode(',',$keysstr);

		if($frm->flgIsThisForm){ //submit
			$where = $frm->getWhere();
			$where['cameFrom']='local';
		}else {
			$where=array();
		}
		
        if(!empty($keysstr)) //导出选中
        {
            foreach($keys as $k => $v)
            {
                $keys[$k] = \Prj\Misc\View::decodePkey($v)['loginname'];
            }
            $where = array('loginName'=>$keys);
        }



		$pager->init($this->manager->getAccountNum($where), $pageid);
		
		$isDownloadExcel = $this->_request->get('__EXCEL__')==1;
		//TODO: 偷懒了，这里只考虑了一张表的情况

        if($isDownloadExcel) //全表导出
        {
            $search = $this->_request->get('where')?$this->_request->get('where'):array();
            $where = array_merge($where,$search);
            //var_log($where);
            $records = $this->manager->db()->getRecords($this->manager->tbname(),'*',$where,'sort cameFrom sort loginName');
        }
        else
        {
            $records = $this->manager->db()->getRecords($this->manager->tbname(),'*',$where,'sort cameFrom sort loginName',$pager->page_size,$pager->rsFrom());
        }
		var_log(\Sooh\DB\Broker::lastCmd(true));
		$headers = array('账号'=>70,'昵称'=>90,'部门'=>'90','最后登入时间'=>80,'最后登入IP'=>70,'权限'=>'','状态'=>90,);
		
		$menus = $this->manager->acl->getMenuEnum();
		$rightsMap=array();
		foreach($menus->children as $menu){
			foreach($menu->children as $child){
//				$v = $child->options['_CtrlAct_'];
				$v = $child->options['_ModCtrl_'];
				$rightsMap[$v]=$menu->capt.'.'.$child->capt;
			}
		}
		if(!empty($records)){
            //var_dump($records);
			foreach($records as $rowid=>$r){
			    //var_log($r['loginName'],'login>>>');
                $right = \Prj\Data\ManagerRight::getCopy($r['loginName']);
                $right->load();
                $rightsNames = [];
                if($right->exists()){
                    $rightsNames = \Prj\Data\RightsRole::getRightsNames(explode(',',$right->getField('roles')));
                }
                $tmp = implode(' | ',$rightsNames);
				$new = array($r['loginName'],$r['nickname'] ,\Prj\Consts\Manage::$depts[$r['dept']], \Prj\Misc\View::fmtYmd($r['lastYmd']),$r['lastIP'],);

				/*
				if ($r['rights'] == '*.*') {
					$tmp = '所有权限';
				}elseif(empty($r['rights'])){
					$tmp = '〖尚未设置权限〗';
				} else {
					$tmp = explode(',', $r['rights']);
					$ret = array();
					foreach($tmp as $k){
						list($i,$v) = explode('.', $rightsMap[$k]);
						$ret[$i][]=$v;
					}
					$tmp='';
					foreach($ret as $k=>$v){
						$tmp.= "【{$k}】:".implode(',', $v).' ';
					}
				}
				*/


				$new[] = $tmp;
                //tgh
                $_pkey_val_ = \Prj\Misc\View::encodePkey(array('camefrom'=>'local','loginname'=>$r['loginName']));
                $doDisable = $isDownloadExcel?'正常':1;
                $doEnable = $isDownloadExcel?'禁用':0;
				$new[] = $r['dtForbidden']?$doEnable:$doDisable;
				if(!$isDownloadExcel){
					$new['_pkey_val_']  = \Prj\Misc\View::encodePkey(array('camefrom'=>'local','loginname'=>$r['loginName']));
				}
				$records[$rowid]=$new;
			}
		}
		if($isDownloadExcel){

            return $this->downExcel($records,  array_keys($headers));
		}else{
            $this->_view->assign('where',$where); //tgh
			$this->_view->assign('headers',$headers);
			$this->_view->assign('records',$records);
			$this->_view->assign('pager',$pager);
		}
	}

	/**
	 * 将所有菜单项转话为tree，用于权限管理
	 * @param array $allMenu allMenu
	 * @param array $strRights rights
	 * @return array ['nodes', 'rightsToIds']
	 */
	public function getAllMenuToTree($allMenu, $strRights) {
		$ks = explode(',', $strRights);
		foreach ($ks as $k) {
			$k   = strtolower($k);
			$tmp = explode('.', $k);
			if (sizeof($tmp) == 1) {
				$rights[$k . '.*'] = 1;
			} else {
				$rights[$k] = 1;
			}
		}

		$temp = [];
		$ret = [];
		$new = [];



//		foreach ($allMenu as $menu => $r) {
//			list ($mainMenu, $subMain) = explode('.', $menu);
//			if (!isset($temp[$r[0]])) {
//				$temp[$r[0]][] = '';
//				$new[] = $r[0] . '.*';
//			}
//
//			if (!in_array($mainMenu, $temp[$r[0]])) {
//				$temp[$r[0]][] = $mainMenu;
//				$new[] = $r[0] . '.' . $r[1];
//			}
//		}



		foreach ($allMenu as $menu => $r) {
			list ($mainMenu, $subMain) = explode('.', $menu);
			if (!isset($temp[$mainMenu])) {
				$temp[$mainMenu][] = '';
				$new[] = $r[0] . '.*';
			}

			if (!in_array($subMain, $temp[$mainMenu])) {
				$temp[$mainMenu][] = $subMain;
				$new[] = $r[0] . '.' . $r[1];
			}
		}

		if ($rights['*.*'] == 1) {
			$_ids = array_keys($new);
			foreach ($_ids as $k => &$v) {
				$v++;
			}
		} else {
			foreach ($new as $k => $v) {
				if (isset($rights[$v])) {
					$_ids[] = $k + 1;
				}
			}
		}
		$mineRightsToId = implode(',', $_ids);

		$num = $i = 1;
		foreach ($temp as $k => $v) {
			foreach ($v as $_k => $_v) {
				if ($_v == '') {
					$ret[] = ['id' => $i, 'pId' => 0, 'name' => $k, 'open' => true];
				} else {
					$ret[] = ['id' => $i, 'pId' => $num, 'name' => $_v];
				}
				$i++;
			}
			$num = $num + count($v);
		}

		if ($rights['*.*'] == 1) {
			foreach ($ret as $k => &$v) {
				$v['checked'] = true;
			}
		} else {
			$i = 0;
			foreach ($ret as $k => &$v) {
				if ($rights[$new[$i]] == 1) {
					$v['checked'] = true;
				}
				$i++;
			}
		}

		return ['nodes' => $ret, 'rightsToIds' => $mineRightsToId];
	}
	
	/**
	 * 构建tree形式的权限的input
	 * @param \Sooh\Base\Acl\Menu $menu
	 * @param \Sooh\Base\Acl\Menu $child
	 */	
	public function renderTreeCheckbox($menu,$child)
	{
		$where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
		$_manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
		$_manager->load();
		$_rights = $_manager->getField('rights',true);
		if(is_null($_rights)){
			$_rights='*.*';
		}
		$_acl = \Prj\Acl\Manage::getInstance();

		$ret = $this->getAllMenuToTree($_acl->initMenu(), $_rights);

		$this->_view->assign('inputRights', json_encode($ret['nodes']));
//		$str = '<div class="zTreeDemoBackground"><input id="rights" type="hidden" name="rights" value="' . $ret['rightsToIds'] . '"><ul id="treeDemo" class="ztree"></ul></div>';
		$str = '<input id="rights" type="hidden" name="rights" value="' . $ret['rightsToIds'] . '">' . <<<HHHHHEAD
<div style="padding:20px;">
    <div class="clearfix">
        <div>
            <ul id="ztree1" class="ztree" data-check-enable="true" data-toggle="ztree"
                data-options="{
                    expandAll: true,
                    onCheck: 'zTreeOnCheck'
                }"
            >
HHHHHEAD;

		$arrRights = explode(',', $ret['rightsToIds']);
		foreach ($ret['nodes'] as $k => $v) {
			$str .= "<li data-id='{$v[id]}'";
			$str .= " data-pid='{$v[pId]}'";
			if (in_array($v['id'], $arrRights)) {
				$str .= ' data-checked="true"';
			}
			$str .= ">{$v[name]}</li>";
		}
		$str .= <<<HHHHFOOT
	                </ul>
	            </div>
	        </div>
	    </div>
HHHHFOOT;

		return $str;
	}
	protected function randPwd()
	{
		$str = null;
		$strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

		for($i=0;$i<8;$i++){
			$str.=$strPol[rand(0,62)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
		}

		return $str;
	}
	/**
	 * 负责添加，更新逻辑以及表单页面控制
	 */
	public function editAction()
	{
		$where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'get', empty($where)?\Sooh\Base\Form\Broker::type_c:\Sooh\Base\Form\Broker::type_u);
        $depts = \Prj\Consts\Manage::$depts;
		if($frm->type()==\Sooh\Base\Form\Broker::type_c){
			$frm->addItem('loginName', form_def::factory('帐号', '', form_def::text))
				->addItem('nickname', form_def::factory('昵称', '', form_def::text))
                ->addItem('dept', form_def::factory('部门', key($depts), form_def::select,$depts));
			$frm->addItem('passwd', form_def::factory('初始密码', '', form_def::text));
			$this->_view->assign('FormOp',$op='添加');
		}else{
			$frm->addItem('loginName', form_def::factory('帐号', '', form_def::constval))
				->addItem('nickname', form_def::factory('昵称', '', form_def::text))
                ->addItem('dept', form_def::factory('部门', key($depts), form_def::select,$depts+[''=>'']));
			$this->_view->assign('FormOp',$op='更新');
		}
		$frm->addItem('_pkey_val_', '') //tgh
			->addItem('cameFrom', 'local')
			->addItem('rights', form_def::factory('权限', '', array($this,'renderTreeCheckbox')));//->initMore(new \Sooh\Base\Form\Options($this->optionsOfRights()))

		$frm->fillValues();
		if($frm->flgIsThisForm){//submit
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val']);//tgh
            if(!empty($where))//tgh
            {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            }
            else
            {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
			try{
				$fields=$frm->getFields();
				if(!empty($fields['rights'])){
					$_rights = $this->manager->acl->getRightsFromForm($fields['rights']);
					$fields['rights'] = implode(',', $_rights);
//					unset($fields['rights']);
				}
				if($frm->type()==\Sooh\Base\Form\Broker::type_c){//add new manager
					$acc = \Prj\Data\Manager::getCopy($fields['loginName'], $fields['cameFrom']);
					//TODO check exists first
					foreach($fields as $k=>$v){
						$acc->setField($k, $v);
					}
					$acc->update($fields['loginname'], $fields['passwd'], $fields['camefrom']='local', 
									array('rights'=>$fields['rights'],'nickname'=>$fields['nickname']));
					$randPwd = $fields['passwd'];
				}else {//update manager
					//var_log($fields,'upd:');
                    $op = '更新';
					unset($fields['cameFrom']);
					unset($fields['loginName']);
					$acc = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
					$acc->load();
                    //var_dump($acc);
                    //var_dump($where);
					foreach($fields as $k=>$v){
						$acc->setField($k, $v);
					}
					$acc->update();
					$randPwd=null;
				}

				$this->closeAndReloadPage($this->tabname('index'));
				$this->returnOK($op.'成功'.($randPwd?',密码:'.$randPwd:''));

			}catch(\ErrorException $e){
				if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
					$this->returnError($op.'失败：该管理员帐号已经存在');
				}else{
					$this->returnError($op.'失败：'.$e->getMessage());
				}
			}
			
		}else{//show form
			if(!empty($where)){
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
				
				$fields=$manager->dump();
				$this->tmpVal = \Sooh\Base\Acl\Ctrl::_fromString($manager->getField('rights'));
				$ks = array_keys($frm->items);
				foreach($ks as $k){
					if(isset($fields[$k]) && is_object($frm->items[$k])){
						$frm->item($k)->value = $fields[$k];
					}
				}

				$frm->items['_pkey_val'] = \Prj\Misc\View::encodePkey(array('camefrom'=>$fields['cameFrom'],'loginname'=>$fields['loginName'])); //tgh
                //var_dump($fields);
			}else {
				$fields=array();
				$this->tmpVal=array();
			}
		}
	}
	protected $tmpVal;
	/**
	 * 重置某账号密码
	 */
	
	public function pwdresetAction()
	{
		$frm = \Sooh\Base\Form\Broker::getCopy('default')
				->init(\Sooh\Base\Tools::uri(), 'post', \Sooh\Base\Form\Broker::type_c);
		//$frm->addItem('camefrom', form_def::factory('', 'local', form_def::constval));
		$frm->addItem('loginname', form_def::factory('账号', '', form_def::constval));
		$frm->addItem('nickname', form_def::factory('昵称', '', form_def::constval));
		$frm->addItem('passwd', form_def::factory('新密码', '', form_def::text));
		$this->_view->assign('FormOp',$op='修改');
		$frm->addItem('_pkey_val', '');

		$frm->fillValues($this->getInputs());
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val'));
		if($frm->flgIsThisForm){//submit
			try{
				$fields=$frm->getFields();
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
                //$manager->db()->updRecords($manager->tbname(),[files],[wahrer]);
				$ret = $manager->resetPWD($fields['passwd'],array('loginName'=>$where['loginname']));
				if($ret===true){
                    $this->returnError('密码重置失败');
				}else{
                    $this->returnOK('密码已重置为: '.$fields['passwd']);
				}
				$this->closeAndReloadPage();
			}catch(\ErrorException $e){
				$this->returnError('密码重置失败:'.$e->getMessage());
			}
			
		}else{//show form
			if(!empty($where)){
				$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
				$manager->load();
				$fields=$manager->dump();
                $fields['loginname'] = $fields['loginName']; //tgh
				$ks = array('loginname','nickname');
				foreach($ks as $k){
					if(isset($fields[$k]) && is_object($frm->items[$k])){
						$frm->item($k)->value = $fields[$k];
					}
				}
                $frm->item('passwd')->value = $this->randPwd();
				$frm->items['_pkey_val'] = \Prj\Misc\View::encodePkey(array('camefrom'=>$fields['cameFrom'],'loginname'=>$fields['loginName']));
                //var_dump($frm);
			}else {
				$this->returnError('unknown manager');
			}
		}
	}
	public function showlogAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val'));
		$records = $this->getManagerLog(0, 0);
		$this->_view->assign('records',$records);
	}	
	/**
	 * 禁用某账号
	 */
	public function disableAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val'));
		$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
		$manager->load();
		
		
		if(!empty($where)){
			try{
				$manager->setField('dtForbidden', 2147123124);
				$manager->update();
				$ret=true;
			}catch(\ErrorException $e){
				$ret=false;
			}
		}else{
			$ret=false;
		}
		if($ret){
			$this->returnOK('已禁用');
		}else {
			$this->returnError('禁用失败，请联系技术人员');
		}
	}
	/**
	 * 启用某账号
	 */
	public function enableAction()
	{
		$where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val'));
		$manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
		$manager->load();
		
		
		if(!empty($where)){
			try{
				$manager->setField('dtForbidden', 0);
				$manager->update();
				$ret=true;
			}catch(\ErrorException $e){
				$ret=false;
			}
		}else{
			$ret=false;
		}

		if($ret){
			$this->returnOK('已启用');
		}else {
			$this->returnError('删除失败');
		}
	}
    //管理员 硬删除
    public function deleteAction()
    {
        return;
        $where = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $manager = \Prj\Data\Manager::getCopy($where['loginname'], $where['camefrom']);
        $manager->load();

        if(!empty($where))
        {
            try{
                $manager->delete();
                $ret = true;
            }catch (\ErrException $e){
                $ret = false;
            }
        }

        if($ret){
            $this->returnOK('已删除');
        }else {
            $this->returnError('删除失败');
        }
    }


	public function testAction()
    {
        \Sooh\Base\Session\Data::getInstance()->set('managerId','');
        $this->returnError(null,301);
    }	
}
