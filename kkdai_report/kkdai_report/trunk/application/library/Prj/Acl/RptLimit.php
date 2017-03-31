<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/20
 * Time: 20:10
 */
namespace Prj\Acl;

class  RptLimit
{
    public static $map=[
        'fin_rpt'=>'财务',
        'bus_rpt'=>'业务',
		'opt_rpt'=>'运营',
		//'top_rpt'=>'管理',
    ];

    public $manager;
	/**
	 * 获取权限类
	 * @param \Prj\Data\Manager $manager
	 * @return \Prj\Acl\RptLimit
	 */
    public static function initWho($manager){
        $manager->load();
        $o = new RptLimit();
        $o->manager = $manager;
        return $o;
    }
	/**
	 * 是否有财务权限
	 * @return boolean
	 */
    public function hasFinance()
    {
        if(in_array('*',$this->getRptRights()))return true;
        if(in_array('fin_rpt',$this->getRptRights()))return true;
        return false;
    }
	/**
	 * 是否有业务权限
	 * @return boolean
	 */
    public function hasBusiness()
    {
        if(in_array('*',$this->getRptRights()))return true;
        if(in_array('bus_rpt',$this->getRptRights()))return true;
        return false;
    }
	/**
	 * 是否有运营权限
	 * @return boolean
	 */
    public function hasOperation()
    {
        if(in_array('*',$this->getRptRights()))return true;
        if(in_array('opt_rpt',$this->getRptRights()))return true;
        return false;
    }
	protected $_rights=null;
    protected function getRptRights(){
		if($this->_rights==null){
		  $this->_rights = \Prj\Data\ManagerRight::getRptRightsByType($this->manager->getField('loginName'),'rpt');
		}
		return $this->_rights;
    }
}
