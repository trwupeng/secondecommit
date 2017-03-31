<?php
namespace Prj\Data;
/**
 * Description of User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Manager  extends \Sooh\DB\Base\KVObj{
	public $cameFrom;
	/**
	 * 
	 * @param string $account
	 * @param string $camefrom
	 * @return Manager
	 */
	public static function getCopy($account, $camefrom = 'local') {
		return parent::getCopy(array('cameFrom'=>$camefrom,'loginName'=>$account));
	}

    public static function getName($loginName){
        $manager = self::getCopy($loginName);
        $manager->load();
        if(!$manager->exists())return $loginName;
        return $manager->getField('nickname');
    }

    public static function getUnderLoginName($loginName){
        $manager = self::getCopy($loginName);
        $manager->load();
        if(!$manager->exists())return [];
        if(!$under = $manager->getField('underLoginName'))return [];
        return explode(',',$under);
    }

    public function getSubLoginName(){
        $str = $this->getField('subLoginName');
        if(empty($str))return [];
        return explode(',',$str);
    }
	
	//针对缓存，非缓存情况下具体的表的名字
	protected static function splitedTbName($n,$isCache)
	{
//		if($isCache)return 'tb_test_cache_'.($n % static::numToSplit());
//		else 
		return 'tb_managers_'.($n % static::numToSplit());
	}
//指定使用什么id串定位数据库配置
	protected static function idFor_dbByObj_InConf($isCache)
	{
		return 'manage';
	}
	//针对缓存，非缓存情况下具体的表的名字

	public function getAccountNum($where)
	{
		return static::loopGetRecordsCount($where);
	}
//	/**
//	 * 是否启用cache机制
//	 * cacheSetting=0：不启用
//	 * cacheSetting=1：优先从cache表读，每次更新都先更新硬盘表，然后更新cache表
//	 * cacheSetting>1：优先从cache表读，每次更新先更新cache表，如果达到一定次数，才更新硬盘表
//	 */
//	protected function initConstruct($cacheSetting=0,$fieldVer='iRecordVerID')
//	{
//		return parent::initConstruct($cacheSetting,$fieldVer);
//	}
	/**
	 * 
	 * @param string $managerId
	 * @return Manager
	 */
	public static function getCopyByManagerId($managerId) {
		list($loginName,$cameFrom) = explode('@', $managerId);
		return self::getCopy($loginName, $cameFrom);
	}
	/**
	 * 
	 * @var \Prj\Acl\Manage
	 */
	public $acl=null;
	public function load($fields='*')
	{
		$ret = parent::load($fields);
		$this->updRights($this->getField('rights',true));
		return $ret;
	}
	
	public function setField($field, $val) {
		$ret = parent::setField($field, $val);
		if($field==='rights'){
			$this->updRights($val);
		}
		return $ret;
	}
	protected function updRights($val)
	{
		if($this->acl===null){
			$this->acl = \Prj\Acl\Manage::getInstance();
		}
//		error_log("[debug]set rights to *.* for debug as ".__FILE__.'.line '.__LINE__);
//		$val = '*.*';
		
		if(is_array($val)){
			$this->acl->fromString(implode(',', $val));
		}else{
			$this->acl->fromString($val);
		}
	}
    public function resetPWD($pwd,$where = null)
    {
        return $this->db()->updRecords($this->tbname(),array('passwd'=>$pwd),$where);
    }

    /**
     * 是否存在下属
     * @param \Prj\Data\Manager $user
     * @return bool
     */
    public function hasLowUsers(){
        return $this->getField('checkUsers') ? true : false;
    }

    public $rights = [];

	public static function getDeptNameAndNickname($cameFrom, $loginName) {
		$managerModel = self::getCopy();
		$record = $managerModel->db()->getRecord($managerModel->tbname(),'dept,nickname',['cameFrom'=>$cameFrom,'loginName'=>$loginName]);
		$deptid = $record['dept'];
		$nickName = $record['nickname'];
		$deptModel = \Prj\Data\MBDepartment::getCopy();
		$deptName = $deptModel->db()->getOne($deptModel->tbname(), 'name', ['id'=>$deptid]);
		$ret = ['deptName'=>$deptName, 'nickName'=>$nickName];
		return $ret;
	}
}
