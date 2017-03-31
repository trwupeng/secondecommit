<?php
namespace Prj\Acl;
/**
 * 
 */
/**
 * 'url'=>'usercompany',		'acl'=>'company.auth'
	$_ignore_=$menuROOT = $_ignore_=$this->rights->getMenuEnum();
	foreach($menuROOT->children as $menus){
		foreach($menus->children as $_ignore_){
			if(!empty($_ignore_->url)){
				$tmp[]=$_ignore_->url;  $_ignore_->options['acl']
			}
		}
	}
 */
/**
 * 
 */
class User extends \Sooh\Base\Acl\Ctrl
{
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
	protected function initMenu()
	{
		$fullpath = realpath(__DIR__.'/../../../..').'/html/php/menu_define.php';
		$tmp = include $fullpath;
		$ret = array();
		foreach($tmp as $main=>$rs){
			foreach($rs as $sub=>$r){
				$acl = explode('.', $r['acl']);
				$ret["$main.$sub"]=[$acl[0],$acl[1],'',$r['url'],['acl'=>$r['acl']]];
			}
		}
		//var_log($ret,'menu original==');
		return $ret;
	}
	public $managerLevel=0;
	public function hasRightsFor($module,$ctrl)
	{
		error_log("[chkRights]$module,$ctrl # x.x=".$this->rights[strtolower("$module.$ctrl")] .' # x.*='.$this->rights[strtolower("$module.*")].' *.*='.$this->rights['*']);
		if($this->managerLevel== \Prj\Consts\ManagerLevel::supper || empty($module)){
			return true;
		}else {
			if(isset($this->rights[strtolower("$module.$ctrl")])){
				return true;
			}elseif(isset($this->rights[strtolower("$module.*")])){
				return true;
			}elseif(isset($this->rights['*.*'])){
				return true;
			}else{
				return false;
			}
		}
	}
	/**
	 * 
	 * @param \Sooh\Base\Acl\Menu $_ignore_
	 * @return type
	 */
	public function menuAccessableList($_ignore_=null)
	{
		$ret = [];
		$menuROOT = $this->getMenuMine();
		foreach($menuROOT->children as $menus){
			foreach($menus->children as $_ignore_){
				if(!empty($_ignore_->url)){
					$ret[] = $_ignore_->url;
				}
			}
		}
		return $ret;
	}
}