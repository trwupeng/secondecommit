<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/12/19
 * Time: 14:24
 */

class UserController extends \Prj\ManagerCtrl {

    /**
     * 按组织架构查看
     * range = all 查看全部
     * typr = single 单选
     */
    public function indexAction(){
    	$loginName = $this->manager->getField('loginName');
        if( \Prj\Data\Manager::isSpecialLoginUser($loginName)
        		|| $this->_request->get('range') == 'all' ){
            $users = \Prj\Data\Manager::loopFindRecords([]);
            $departTree = \Prj\Data\MBDepartment::getDepartTree();
        }else{
            $checkUsers = $this->manager->getField('checkUsers') ? explode(',',$this->manager->getField('checkUsers')) : [];
            $ext = $this->_request->get( 'ext' );
            if ( isset($ext) && 'comment' == $ext )
            {
           		$sameDeptUsers = \Prj\Data\Manager::getSameDeptLoginName($this->manager->getField( 'dept') );
            	foreach( $sameDeptUsers as $user )
            	{
            		if ( !in_array( $user, $checkUsers )  )
            		{
            			$checkUsers[] = $user;
            		}
            	}
            }
            //var_log($checkUsers , '$checkUsers>>>');
            if($checkUsers){
                $departs = [];
                $rs = \Prj\Data\Manager::loopFindRecords(['loginName'=>$checkUsers]);
                foreach ($rs as $v){
                    if(in_array($v['dept'] , $departs))continue;
                    $departs[] = $v['dept'];
                }
                //var_log($departs , '$departs>>>');
                $departRs = \Prj\Data\MBDepartment::getRecords(['id'=>$departs]);
                $retDeparts = \Prj\Data\MBDepartment::getAllDepartsByDepartIds($departRs);
                $departTree = \Prj\Data\MBDepartment::getDepartTree($retDeparts);
                $users = $rs;
            }else{
                $departTree = [];
                $users = [];
            }
            //var_log($retDeparts , '$retDeparts>>>');
        }
        
        if ( self::isSpecialLoginUser() )
        {
			 $tmpDept = array( '0' => array(
				'id' => '0',
				'supId' => '670869647114347',
				'name' => '重点关注人员',
				'oa_sort' => '0',
				'statusCode' => '0',
				'updateTime' => '20161219141744',
				'iRecordVerID' => '2',
				)	
			);
			$departTree = array_merge( $tmpDept, $departTree );

			$addUsers = [];
			foreach( $users as $user )
			{
				if ( self::isSpecialUser( $user['nickname'] ) )
				{
					$tmp = array();
					$tmp = $user;
					$tmp['tmp'] = 0;
					$tmp['dept'] = '0';
					array_push( $addUsers, $tmp );
				}
			}	
			$users = array_merge( $users, $addUsers );
		}
		
		$selectUsers = $this->_request->get( 'users' );
        			
        			
        $tree = Prj\Data\MBDepartment::treeFormat($departTree , $users);
        $this->_view->assign('tree' , json_encode($tree));
        $options = [
            "onCheck"=>"user_tree_check",
            "expandAll"=>false,
            "checkEnable"=>true,
        ];
        if($this->_request->get('type') == 'single'){
            $options['onClick'] = "user_tree_check";
            $options['checkEnable'] = false;
        }
        $this->_view->assign('options' , json_encode($options));
        //ext = comment
        $this->_view->assign('ext' , $this->_request->get('ext'));
        $this->_view->assign('id' , $this->_request->get('id'));
        $this->_view->assign( 'selectedusers', $selectUsers );
        $this->_view->assign( 'type', $this->_request->get( "customer" ) );
    }
    
    function isSpecialLoginUser()
    {
    	$loginName = $this->manager->getField('loginName');
    	return \Prj\Data\Manager::isSpecialLoginUser($loginName);
		}
		
    	function isSpecialUser( $nickname ) {
    		return \Prj\Data\Manager::isSpecialUser($nickname);
    	}

    public function testAction(){
        // \Prj\Misc\MBM::getInstance()->updateUserFromEC();
        //\Prj\Misc\MBM::getInstance()->updateUserFromOA();
    }
}