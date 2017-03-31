<?php
use Sooh\Base\Form\Item as form_def;
class ManageRightsController extends \Prj\ManagerCtrl {

    protected static $_model = '\Prj\Data\ManagerRight';
    protected static $_pk = ['loginName',true];
    protected static $_headKeys = ['loginName','roles','rights'];

    protected static function _getHead(){
        /*$roles = \Prj\Data\RightsRole::paged(null,['statusCode'=>0]);
        $rolesOptions = [];
        foreach((array)$roles as $v){
            $rolesOptions[$v['roleId']] = $v['roleName'];
        }

        $users = \Prj\Data\Manager::loopFindRecords([]);
        $usersOptions = [];
        foreach((array)$users as $v){
            $usersOptions[$v['loginName']] = $v['nickname'];
        }

        $ids = \Prj\Data\Rights::getRightIds();
        $rightsOptions = [];
        foreach((array)$ids as $v){
            $rightsOptions[$v['rightsId']] = $v['rightsName'];
        }
        $rightsOptions = array_merge($rightsOptions,[''=>'<空>']);
        */
        return [
            'loginName'=>['loginName','用户名','','select'],
            'roles'=>['roles','角色','','select'],
            'rights'=>['rights','额外权限集','','selects'],
        ];
    }



    public function indexAction () {
        /**
         * @var \Prj\Data\RightsRole $model
         */
        $downExcel = $this->_request->get('__EXCEL__');
        $model = self::$_model;
        $view = new \Prj\Misc\ViewFK();

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $view->addSearch('_loginName_eq','用户号');
        $where = $view->getSearch();

        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        $initWhere = ['rightsType'=>'all'];
        $where = array_merge($where,$initWhere);
        var_log($where,'where>>>');
        $data = $model::paged($pager,$where);
        $view->showSearch()->setPk(self::$_pk[0],self::$_pk[1])->setData($data)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'update'),\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'delete'));

        $head = self::_getHead();
        foreach($head as $v){
            $view->addRow($v[0],$v[1],$v[2],$v[3],$v[4]);
        }
        if($downExcel){
            $excel = $view->toExcel($data);
            $this->downExcel($excel['records'],$excel['header']);
        }
        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
    }

    /**
     * 用户编辑入口
     */
    public function updateAction(){
        /**
         * @var \Prj\Data\RightsRole $model
         */
        $pk = self::$_pk[0];
        $model = self::$_model;
        $input = $this->_request->get('values')[0];
        $input = $input ? $input : $this->_request->getPost();
        $id = $this->_request->get('_id');
        if($this->_request->get('form')){
            //展示form
            $curRoles = [];
            $loginName = $this->_request->get('loginName');
            $user = \Prj\Data\Manager::getCopy($loginName);
            $user->load();
            if($user->exists()){
                $rights = \Prj\Data\ManagerRight::getCopy($loginName);
                $rights->load();
                if($rights->exists()){
                    $roles = explode(',',$rights->getField('roles'));
                    foreach ((array)$roles as $v){
                        $role = \Prj\Data\RightsRole::getCopy($v);
                        $role->load();
                        if($role->exists()){
                            $curRoles[$v] = $role->getField('roleName');
                        }
                    }
                }
                $userInfo = $user->dump();
            }
            $userInfo['checkUsers'] = $userInfo['checkUsers'] ? explode(',',$userInfo['checkUsers']) : [];
            $roleList = \Prj\Data\RightsRole::paged();
            $allRoles = []; //from my home
            foreach ($roleList as $v){
                $allRoles[$v['roleId']] = $v['roleName'];
            }
            $allRoles = array_diff($allRoles , $curRoles);
            $selects['dept'] = \Prj\Consts\Manage::$depts;
            $selects['oa'] = $this->getOaSelects();
            $selects['ec'] = $this->getEcSelects();
            //部门树
            $users = \Prj\Data\Manager::loopFindRecords([]);
            $tree = Prj\Data\MBDepartment::treeFormat(\Prj\Data\MBDepartment::getDepartTree() , $users);
            $this->_view->assign('tree' , json_encode($tree));

            $this->_view->assign('userInfo',$userInfo);
            $this->_view->assign('selects',$selects);
            $this->_view->assign('curRoles',$curRoles);
            $this->_view->assign('allRoles',$allRoles);
        }else{
            if(!$input['passwd'])return $this->returnError('密码不能为空');
            if(!$input['loginName'])return $this->returnError('用户名不能为空');
            if(!$input['nickname'])return $this->returnError('昵称不能为空');
            //return $this->returnError('xxx');
            if(!$this->checkPwd($this->_request->get('pwd')))return $this->returnError('密码错误');
            if(empty($id)){
                //this is add
                $this->closeAndReloadPage();
                $user = \Prj\Data\Manager::getCopy($input['loginName']);
                $user->load();

                if(empty($input[$pk])){
                    $input[$pk] = time().rand(1000,9999);
                }
                var_log($input[$pk],'all');
                $tmp = $model::getCopy($input[$pk],'all');
                $tmp->load();
                //if($tmp->exists())return $this->returnError('该记录已存在');
            }else{
                //this is update
                $user = \Prj\Data\Manager::getCopy($id);
                $user->load();

                $tmp = $model::getCopy($id,'all');
                $tmp->load();
                //if(!$tmp->exists())return $this->returnError('不存在的记录');
            }
            if($input['oa']){
                $users = \Prj\Misc\MBM::getInstance()->getOAUsers();
                $posts = \Prj\Misc\MBM::getInstance()->getOAOrgPosts();
                $postName = $posts[$users[$input['oa']]['orgPostId']]['name'];
            }
            if($postName)$user->setField('postName' , $postName );
            $user->setField('loginName',$input['loginName']);
            $user->setField('nickname',$input['nickname']);
            $user->setField('passwd',$input['passwd']);
            $user->setField('dept',$input['dept']);
            $user->setField('oa',$input['oa']);
            $user->setField('ec',$input['ec']);
            $user->setField('checkUsers',implode(',',$this->_request->get('users')));
            $user->update();

            foreach(self::$_headKeys as $v){
                if($v == $pk){
                    if(!$input[$v])continue;
                }
                if(is_array($input[$v]))$input[$v] = implode(',',$input[$v]);
                $tmp->setField($v,$input[$v]);
            }

            try{
                $tmp->update();
            }catch (\ErrException $e){
                return $this->returnError($e->getMessage());
            }
            $this->_view->assign('_id',$input[$pk]?$input[$pk]:$id);
            return $this->returnOK();
        }
    }

    protected function getOaSelects(){
        $ret = \Prj\Misc\MBM::getInstance()->getOAUsers();
        $select = [];
        array_walk($ret , function($v ,$k)use(&$select){
            $select[substr($v['loginName'] , 0 , 1)][$v['id']] = $v['name'];
        });
        $select[''][''] = '无';
        ksort($select);
        return $select;
    }

    /**
     * 保存OA获取的用户信息
     * @param $data
     * @author lingtm <lingtima@gmail.com>
     */
    protected function saveOaUsers($data)
    {
        $userType = 'oa';
        foreach ($data as $k => $v) {
            try {
                $SyncUserModel = \Prj\Data\SyncUser::getCopy($v['id'], $userType);
                $SyncUserModel->load();
                if (!$SyncUserModel->exists()) {
                    $SyncUserModel->setField('createdAt', time());
                }
                $SyncUserModel->setField('userName', $v['name']);
                $SyncUserModel->setField('userCode', $v['code']);
                $SyncUserModel->setField('loginName', $v['loginName']);
                $SyncUserModel->setField('orgAccountId', $v['orgAccountId']);
                $SyncUserModel->setField('orgAccountName', $v['orgAccountName']);
                $SyncUserModel->setField('orgShortName', $v['orgShortName']);
                $SyncUserModel->setField('updateAt', time());

                $SyncUserModel->update();

                unset($SyncUserModel);
            } catch (\Exception $e) {
                error_log('save oa user error, userId:' . $v['id']);
            }
        }
    }

    protected function getEcSelects(){
        $ecUsers = \Prj\Misc\MBM::getInstance()->getECUsers();
        $selects = [''=>'无'];
        foreach ($ecUsers as $v){
            $selects[$v['userId']] = $v['userName'].'--'.$v['title'];
        }
        return $selects;
    }

    public function getDepartMentFromOAIdAction(){
        if(!$id = $this->_request->get('oaId'))return $this->returnError('error');
        $oaUsers = \Prj\Misc\MBM::getInstance()->getOAUsers();
        $oaInfo = $oaUsers[$id];
        $departId = $oaInfo['orgDepartmentId'];
        $depart = \Prj\Data\MBDepartment::getOne($departId);
        if($depart){
            $this->_view->assign('data',$depart->dump());
        }
        return $this->returnOK();
    }

    public function deleteAction(){
        /**
         * @var \Prj\Data\RightsRole $model
         */
        $model = self::$_model;
        $id = $this->_request->get('_id');
        if($this->_request->get('_type') == 'new'){
            return $this->returnOK();
        }
        if(empty($id))return $this->returnError('主键不能为空');
        $tmp =$model::getCopy($id,'all');
        $tmp->load();
        if(!$tmp->exists())return $this->returnError('该记录已经被删除');
        try{
            $tmp->delete();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK();
    }
}