<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Contract as contract_kobj;
use Sooh\Base\Form\Options as options_def;

class Test2Controller extends \Prj\ManagerCtrl
{
    public function indexAction(){
        $view = new \Prj\Misc\ViewFK();
        $data = [];
        $head = ['key1','key2','key3','key4','key5'];
        for($i=0;$i<3;$i++){
            $tmp = [];
            foreach($head as $v){
                $tmp[$v] = 1;
            }
            $data[] = $tmp;
        }
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $total = 21;
        $pager->init($total,$pageid);

        $view->setPk('key1')->setData($data)->setPager($pager)->setAction('/risk/test2/update','/risk/test2/delete')

             ->addRow('key1','参数1','b101','text')
             ->addRow('key2','参数2','b102','datepicker')
             ->addRow('key3','参数3','b103','checkbox',[1=>1,2=>2])
             ->addRow('key4','参数4','b104','select',[1=>1,2=>2])
             ->addRow('key5','参数5','b105','selects',[1=>1,2=>2])

             ->addTab('tab1','标签1','/risk/test2/index','a','key1')
             ->addTab('tab2','标签2','/risk/test2/index','b','key2');

        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
    }

    public function updateAction(){
        return $this->returnOK();
    }

    public function deleteAction(){
        return $this->returnOK();
    }

    public function ajaxAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');

    }
}

