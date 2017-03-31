<?php

class RightsRoleController extends \Prj\ManagerCtrl {

    protected static $_model = '\Prj\Data\RightsRole';
    protected static $_pk = ['roleId',false];
    protected static $_head = [];

    protected static function _getHead(){
        /*
        $ids = \Prj\Data\Rights::getRightIds();
        //var_log($ids,'<<<');
        $options = [];
        foreach((array)$ids as $v){
            if(strpos($v['rightsName'],'删除')!==false){
                $style = 'color:red';
            }elseif(strpos($v['rightsName'],'查看')!==false){
                $style = 'color:blue';
            }elseif(strpos($v['rightsName'],'编辑')!==false){
                $style = 'color:green';
            }
            $options[$v['rightsId']] = [
                'value'=>$v['rightsName'],
                'style'=>$style,
            ];

        }
        */
        return [
            ['roleId','角色ID','','text'],
            ['roleName','角色名称','','text'],
            ['rightsNames','权限','','text'],
        ];
    }

    public function indexAction () {
        /**
         * @var \Prj\Data\RightsRole $model
         */
        \Prj\Misc\ViewFK::$_showUpdate = false;
        $model = self::$_model;
        $view = new \Prj\Misc\ViewFK();
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $total = 21;
        $pager->init($total,$pageid);
        $data = $model::paged($pager,['statusCode'=>0]);
        foreach ($data as &$dataValue){
            $dataValue['rightsNames'] = \Prj\Data\RightsRole::getRightsNames([$dataValue['roleId']]);
            $dataValue['rightsNames'] = implode(' | ',$dataValue['rightsNames']);
            //var_log($dataValue['rightsNames']);
            //$v['rightsNum'] = count(explode(',',$v['rightsIds']));
        }
        $view->setPk(self::$_pk[0],self::$_pk[1])->setData($data)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'update'),\Sooh\Base\Tools::uri(['__VIEW__'=>'json'],'delete'));
        $view->addBtn(\Prj\Misc\View::btnDefaultInDatagrid('编辑',\Sooh\Base\Tools::uri(['roleId'=>'{$id}','form'=>1],'update')));
        $head = (self::$_head)?(self::$_head):(self::_getHead());

        foreach($head as $v){
            $view->addRow($v[0],$v[1],$v[2],$v[3],$v[4]);
        }
        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
    }

    public function updateAction(){
        $input = $this->_request->get('values')[0];
        $input = $input ? $input : $this->_request->getPost();
        $roleId = $this->_request->get('roleId');
        if($this->_request->get('form')){
            //这里展示表单
            $menu = \Prj\Data\Menu::paged([],'sort name');
            $rights = [];
            foreach ($menu as $v){
                $nameArr = explode('.',$v['name']);
                $actionArr = json_decode($v['value'],true);
                $v['rightName'] = strtolower($actionArr[0].'_'.$actionArr[1].'_'.$actionArr[2]);
                $rights[$nameArr[0]][$nameArr[1]][$v['alias']] = $v;
            }

            $rightRole = \Prj\Data\RightsRole::getCopy($roleId);
            $rightRole->load();
            $role = $rightRole->dump();
            $role['rightsIds'] = explode(',',$role['rightsIds']);
            $this->_view->assign('menuRights',$rights);
            $this->_view->assign('role',$role);
        }else{
            $pwd = $this->_request->get('pwd');
            if(empty($pwd))return $this->returnError('请输入您的密码');
            if(!$this->checkPwd($pwd))return $this->returnError('密码错误');
            if(empty($roleId)){
                //this is add
                $this->closeAndReloadPage();
                if(empty($input['roleId'])){
                    $input['roleId'] = time().rand(1000,9999);
                }
                $rightRole = \Prj\Data\RightsRole::getCopy($input['roleId']);
                $rightRole->load();
                if($rightRole->exists())return $this->returnError('该记录已存在');
            }else{
                //this is update
                $rightRole = \Prj\Data\RightsRole::getCopy($roleId);
                $rightRole->load();
                if(!$rightRole->exists())return $this->returnError('不存在的记录');
            }
            $input['roleName'] = trim($input['roleName']);
            if(!$input['roleName'])return $this->returnError('用户组名称不能为空');
            $rightRole->setField('roleName',$input['roleName']);
            $rightRole->setField('rightsIds',implode(',',$input['rightsIds']));

            if($input['roleId'])$rightRole->setField('roleId',$input['roleId']);
            try{
                //return $this->returnOK('xxx');
                $rightRole->update();
            }catch (\ErrException $e){
                return $this->returnError($e->getMessage());
            }
            $this->_view->assign('_id',$input['roleId']?$input['roleId']:$roleId);
            return $this->returnOK();
        }
    }

    public function deleteAction(){
        /**
         * @var \Prj\Data\RightsRole $model
         */
        $model = self::$_model;
        $rightsId = $this->_request->get('_id');
        if($this->_request->get('_type') == 'new'){
            return $this->returnOK();
        }
        if(empty($rightsId))return $this->returnError('主键不能为空');
        $right =$model::getCopy($rightsId);
        $right->load();
        if(!$right->exists())return $this->returnError('该记录已经被删除');
        try{
            $right->delete();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK();
    }
}