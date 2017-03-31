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
        if($this->manager->getField('loginName') == 'root' || $this->_request->get('range') == 'all'){
            $users = \Prj\Data\Manager::loopFindRecords([]);
            $departTree = \Prj\Data\MBDepartment::getDepartTree();
        }else{
            $checkUsers = $this->manager->getField('checkUsers') ? explode(',',$this->manager->getField('checkUsers')) : [];
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
    }

    public function testAction(){
        // \Prj\Misc\MBM::getInstance()->updateUserFromEC();
        //\Prj\Misc\MBM::getInstance()->updateUserFromOA();
    }
}