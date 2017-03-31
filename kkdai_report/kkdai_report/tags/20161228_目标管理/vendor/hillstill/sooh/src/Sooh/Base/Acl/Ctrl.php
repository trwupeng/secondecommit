<?php
namespace Sooh\Base\Acl;
/**
 * AclCtrl
 *
 * @author Simon Wang <hillstill_simon@163.com>
 * 遍历所有菜单的参考代码
			$this->acl = \Prj\Acl\Manage::getInstance();
			
			$menu = $this->acl->getMenuEnum();
			foreach($menu->children as $menu1){
				$capt = $menu1->capt;
				$options=$menu1->options;//除了预定义的options，增加了$options['MCA']="{$mod}_{$ctrl}_{$act}";
				foreach($menu1->children as $menu2){
					$capt2 = $menu2->capt;
				}
			}
 */
class Ctrl {
	public $mineRightsToId;
	public function initMenu()
	{
		return array(
			'一级菜单a.二级菜单1'=>array('m','ctrl','act1', array('arg1'=>1),array('options1'=>1)),
			'一级菜单a.二级菜单2'=>array('m','ctrl', 'act2',array(),array()),
			'一级菜单b.二级菜单1'=>array('m','ctrl', 'act3',array(),array()),
		);
	}
	protected static $_instance=null;
	/**
	 * 
	 * @return Ctrl
	 */
	public static function getInstance()
	{
		if(self::$_instance===null){
			$cc = get_called_class();
			self::$_instance = new $cc;
			self::$_instance->allMenu = self::$_instance->initMenu();
			foreach(self::$_instance->allMenu as $k=>$r){
				if(is_array($r[3])){
					self::$_instance->allMenu[$k][3] = \Sooh\Base\Tools::uri($r[3],$r[2],$r[1],$r[0]);
				}
			}
		}
		return self::$_instance;
	}
	protected $allMenu;
	public function getMenuPath($act,$ctrl,$mod)
	{
		foreach($this->allMenu as $k=>$r){
			if(strtolower("$mod/$ctrl/$act")==  strtolower("{$r[0]}/{$r[1]}/{$r[2]}")){
				return $k;
			}
		}
		return null;
	}
	/**
	 * 获取本人可以访问的菜单
	 * @param type $chkRights
	 * @return \Sooh\Base\Acl\Menu
	 */
	public function getMenuMine()
	{
		$root = \Sooh\Base\Acl\Menu::factory('root');
		$lastMenu=null;

		foreach($this->allMenu as $menu=>$r){
			list($mainMenu,$subMenu)=explode('.',$menu);
			$mod = $r[0];
			$ctrl = $r[1];
			$act = $r[2];
			$url = $r[3];
			$options=$r[4];
			$tabname=$r[5];
			if(/*$this->hasRightsFor($mod, $ctrl) && */ $this->hasNewRightsFor($mod , $ctrl)){
				if($lastMenu===null){
					$lastMenu = \Sooh\Base\Acl\Menu::factory($mainMenu);
				}elseif($lastMenu->capt!==$mainMenu){
					$root->addChild($lastMenu);
					$lastMenu = \Sooh\Base\Acl\Menu::factory($mainMenu);
				}
				if(!empty($tabname)) {
					$options['MCA']=$tabname;
				}else {
					$options['MCA']="{$mod}_{$ctrl}_{$act}";
				}
				$lastMenu->addChild($subMenu, $url, $options);
			}
		}
		if($lastMenu!==null){
			$root->addChild($lastMenu);
		}

		return $root;
	}
	/**
	 * 获取完整菜单（用于权限设置）
	 * @param type $chkRights
	 * @return \Sooh\Base\Acl\Menu
	 */
	public function getMenuEnum()
	{
		$root = \Sooh\Base\Acl\Menu::factory('root');
		$lastMenu=null;

		foreach($this->allMenu as $menu=>$r){
			list($mainMenu,$subMenu)=explode('.',$menu);
			$mod = $r[0];
			$ctrl = $r[1];
			$act = $r[2];
			$url = $r[3];
			$options=$r[4];
			if($lastMenu===null){
				$lastMenu = \Sooh\Base\Acl\Menu::factory($mainMenu);
			}elseif($lastMenu->capt!==$mainMenu){
				$root->addChild($lastMenu);
				$lastMenu = \Sooh\Base\Acl\Menu::factory($mainMenu);
			}
			$options['_ModCtrl_'] = "$mod.$ctrl";
			$options['_CtrlAct_'] = "$ctrl.$act";
			$options['MCA']="{$mod}_{$ctrl}_{$act}";
			$lastMenu->addChild($subMenu, $url, $options);
		}
		if($lastMenu!==null){
			$root->addChild($lastMenu);
		}
		return $root;
	}



	public function getRightsFromForm($rights) {
		if (is_string($rights)) {
			$rights = explode(',', $rights);
		}

		$temp = [];
		$new = [];

		foreach ($this->allMenu as $menu => $r) {
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

//		foreach ($this->allMenu as $menu => $r) {
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


		foreach ($rights as $k => $v) {
			if (isset($new[$v - 1]) && strpos($new[$v - 1], '*') === false ) {
				$_retRgt[] = $new[$v - 1];
			}
		}

		return $_retRgt;
	}

	protected $rights=array();
	public function fromString($str)
	{
		$ks = self::_fromString($str);
		foreach($ks as $k){
			$k = strtolower($k);
			$tmp = explode('.', $k);
			if(sizeof($tmp)==1){
				$this->rights[$k.'.*']=1;
			}else{
				$this->rights[$k]=1;
			}
		}
	}
	public static function _fromString($str)
	{
		return  explode(',',$str);
	}


	public function toString()
	{
		return implode(',', array_keys($this->rights));
	}
	public function hasRightsFor($mod,$ctrl)
	{
		if(true===isset($this->rights[strtolower("$mod.$ctrl")])){
			return true;
		}elseif(true===isset($this->rights[strtolower("$mod.*")])){
			return true;
		}elseif(true===isset($this->rights[strtolower("*.*")])){
			return true;
		}else{
			return false;
		}
	}
    public $newRights;
    public $accessModule = [];
    public function hasNewRightsFor($mod , $ctrl){
        if(!in_array($mod , $this->accessModule))return true;
        if($this->newRights === null)return true;
        if(!in_array($mod.'_'.$ctrl.'_index',$this->newRights)){
            return false;
        }else{
            return true;
        }
    }
}
