<?php

class RightsController extends \Prj\ManagerCtrl {
    protected $pageSizeEnum = [10,20,50];
    public function indexAction () {
        $view = new \Prj\Misc\ViewFK();
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 20), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);
        $data = \Prj\Data\Rights::paged($pager,['status'=>0],'sort rightsName');
        foreach((array)$data as $k=>$v){
            foreach($v as $kk=>$vv){
                $data[$k][$kk] = [
                    'value'=>$vv,
                ];
            }
            if (!preg_match("/[\x7f-\xff]/", $v['rightsName'])) {
                //有中文
                $data[$k]['rightsName']['style'] = 'color:red';
            }
        }
        $view//->addSearch('rightsId','权限ID')
            ->setPk('rightsId')->setData($data)->setPager($pager)->setAction('/manage/rights/update?__VIEW__=json','/manage/rights/delete?__VIEW__=json')
            //->showSearch(false)
            //->addTab('tab1','标签1','/manage/rights/index?','a','rightsId')
            ->addRow('rightsId','权限ID','','text')
            ->addRow('rightsName','权限名称','','text');

        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
    }

    public function updateAction(){
        $input = $this->_request->get('values')[0];
        $rightsId = $this->_request->get('rightsId');
        if(empty($input['rightsId']))return $this->returnError('主键不能为空');
        if(empty($rightsId)){
            //this is add
            $right = \Prj\Data\Rights::getCopy($input['rightsId']);
            $right->load();
            if($right->exists())return $this->returnError('该记录已存在');
        }else{
            //this is update
            $right = \Prj\Data\Rights::getCopy($rightsId);
            $right->load();
        }
        $right->setField('rightsName',$input['rightsName']);
        $right->setField('rightsId',$input['rightsId']);
        try{
            $right->update();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        $this->_view->assign('_id',$input['rightsId']);
        return $this->returnOK();
    }

    public function deleteAction(){
        $rightsId = $this->_request->get('_id');
        if($this->_request->get('_type') == 'new'){
            return $this->returnOK();
        }
        if(empty($rightsId))return $this->returnError('主键不能为空');
        $right = \Prj\Data\Rights::getCopy($rightsId);
        $right->load();
        if(!$right->exists())return $this->returnError('该记录已经被删除');
        try{
            $right->delete();
        }catch (\ErrException $e){
            return $this->returnError($e->getMessage());
        }
        return $this->returnOK();
    }

    /*
    public function refreshAction(){
        $module = $this->_request->get('module','Manage');
        $data = [];
        $dir = __DIR__.'/../../'.$module.'/controllers';
        $classes = scandir($dir);
        foreach($classes as $class){
            if(in_array($class,['.','..']))continue;
            $tmpDir = $dir.'/'.$class;
            $class = str_replace('.php','',$class);
            $main = substr($class,0,strlen($class)-1).'Controller';
            $c = $class;
            $class = $class.'Controller';
            if(class_exists($main))continue;
            if(!class_exists($class))require_once $tmpDir;
            //$methods = get_class_methods($class);
            $methodsArr = (new \ReflectionClass($class))->getMethods();
            var_log($methodsArr,$class.'>>>');
            $methods = [];
            foreach($methodsArr as $v){
                if($v->class == $class)$methods[] = $v->name;
            }
            $tmp = [];
            foreach($methods as $vvv){
                $tmp1 = [];
                if(substr($vvv,-6) == 'Action'){
                    $tmp1['action'] = $vvv;
                    $tmp1['mark'] = strtolower($module."_".$c."_".substr_replace($vvv,'',-6));
                    $tmp[$vvv] = $tmp1['mark'];
                }
            }
            $data[$class] = $tmp;
        }
        echo '<pre style="overflow: auto;height: 80%">';
        var_export($data);
        $this->_view->assign('class',$data);
    }
    */
}