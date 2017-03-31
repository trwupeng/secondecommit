<?php

class TestController extends \Prj\ManagerCtrl {
    
    public function indexAction () {
        $obj =  \Prj\Data\Test::getCopy();
        $tbname = $obj->tbname();
        $this->_view->assign('tbname', $tbname);
        
        $recordsNum = $obj->getRecordsNum(array('name*'=>'%name%'));
        $this->_view->assign('recordsNum', $recordsNum);
        
        $this->_view->assign('numToSlitp',$obj::numToSplit());
        
        $this->_view->assign('records', $obj::loopFindRecords(array('name*'=>'%name%')));
        
        $this->_view->assign('db', $obj->db());
        
    }
    
    protected function getRecords ($db, $tbname) {
        
    }
    
}