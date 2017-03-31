<?php
namespace Prj\Data;
/**
 * Description of User
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Manager  extends \Sooh\DB\Base\KVObj{
	public $cameFrom;
	
	//特别关注的用户昵称
	private static $specialNickName = [ '吴道学'
										, '金超'
										, '王慧敏'
										, '王惠敏'
										, '李庚'
										, '奚薇'
										, '解雪梅'
										, '徐卫林' ];
	
	//拥有特别关注权限的登录用户名
	private static $specialLoginName = [ 'wangdong'
										, 'root' ];
	
	//特别关注的用户id
	private static $specialUserId = [ 	'wudaoxue@local'
										, 'jinchao@local'
										, 'wanghuimin@local'
										, 'ligeng@local'
										, 'xiwei@local'
										, 'xiexuemei@local'
										, 'xuweilin@local' ];
	
	//同部门的部门id
	private static $specialDept = [	'-8143555650784768858' //总裁办
									, '-4876168953563036833' //上海行政人事中心 
									, '7989730476136810824' //上海财务中心
									, '-5140034645003974415' //上海风控中心
									, '5489691341797739831' //上海营销中心
									, '4105293561522439714' //上海运营中心
									, '-6467627429044138575' //北京分公司
									, '6051987613394028599' //郑州融资业务部
									, '-8707217282667625106' //郑州业务巡查组
									, '-7355316428119061544' //郑州粉丝营销部
									, '-6175329987673765135' //郑州风险控制部
									, '-1430848157632674751' //郑州运营支持部
									, '3882175782266883852' //郑州投资业务部
									, '7317802554872579300' //郑州人事行政部
									, '-1520264543812041469' //郑州财务部
									, '-4920755728614470352' //郑州法务部
									, '4429904052621297931' //河南竞合
									, '-7875073667777019078' //郑州快客餐饮
									];
	
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

	public static function isSpecialLoginUser( $loginName ) {
		return in_array( $loginName, self::$specialLoginName );
	}

	public static function isSpecialUser( $nickname ) {
		return in_array( $nickname, self::$specialNickName );
	}

	public static function getSpecialUserList() {
		$users = \Prj\Data\Manager::loopFindRecords([]);
		$userList = array();
		foreach( $users as $user )
		{
			if ( self::isSpecialUser( $user['nickname'] ) )
			{
				array_push( $userList, $user );
			}
		}

		return $userList;
	}
	public static function getLoginNameByNickName( $nickname ) {
		$loginNames = [];
		$managerModel = self::getCopy( 'root' );
		$ret = $managerModel->db()->getRecords( $managerModel->tbname(), '*', array( 'nickname like \'%' . $nickname . '%\'' ) );
		foreach( $ret as $v ) {
			array_push( $loginNames, $v['loginName'] );
		}

		return $loginNames;
	}
	
	public static function isSpecialUserloginname() {
	    return self::$specialUserId;
	}
	
	//获取同属于一个大部门的用户列表
	public static function getSameDeptLoginName( $deptId )
	{
		var_log( $deptId, 'getSameDeptLoginName' );
		$captDeptId = null;
		if ( in_array( $deptId, self::$specialDept ) )
		{
			//属于大部门
			$captDeptId = $deptId;
		}
		else
		{
			//不属于大部门，寻找上级部门
			$parentDeptId = $deptId;
			do 
			{
				$parentDeptId = \Prj\Data\MBDepartment::getParentDeptIdByDeptId($parentDeptId);
				var_log( $parentDeptId, '$parentDeptId' );
				if ( !$parentDeptId )
				{
					return false;
				}
				$captDeptId = $parentDeptId;
			} while( !in_array( $parentDeptId, self::$specialDept) );
		}
		
		$deptIdList = \Prj\Data\MBDepartment::getAllSameDeptIdByDeptId($captDeptId);
		
		$model = self::getCopy( 'root' );
		$result = [];
		foreach( $deptIdList as $id )
		{
			$ret = $model->db()->getRecords( $model->tbname(), 'loginName', array( 'dept' => $id  ) );
			foreach( $ret as $rs )
			{
				$result[] = $rs['loginName'];
			}
		}
		
		return $result;
	}
	
	public static function getCopyByEcId( $EcId )
	{
		$managerModel = self::getCopy( 'root' );
		return $managerModel->db()->getRecord( $managerModel->tbname(), '*', array( 'ec'=>$EcId ) );
	}
	
}
