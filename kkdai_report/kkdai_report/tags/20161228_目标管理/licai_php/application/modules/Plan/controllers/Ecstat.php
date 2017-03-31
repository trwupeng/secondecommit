<?php

/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/12/22
 * Time: 9:44
 */
class EcstatController extends \Prj\ManagerCtrl
{
    protected $header = [
        ['loginName','用户ID'],
        ['nickname','用户名'],
        ['userId','ECID'],
        ['total','次数'],
    ];
    public function indexAction(){
        $viewFK = \Prj\Misc\ViewFK::getInstance();
        foreach ($this->header as $v) {
            $viewFK->addRow($v[0] , $v[1]);
        }
        $viewFK->addSearchOT(\Prj\Misc\View::mb_zzjgck('checkbox'));
        $viewFK->hideDefaultBtn()->showSearch(false)
            ->addSearch('userIds','用户ID','text',$this->_request->get('userIds'))
            ->addSearch('start','起始日期','datepicker',$this->_request->get('start'))
            ->addSearch('end','截止日期','datepicker',$this->_request->get('end'));
        $data = $this->getData();
        $viewFK->setData($data);
    }

    protected function getData(){
        $ecUserIds = [];
        $loginNames = $this->_request->get('userIds') ? explode(',',$this->_request->get('userIds')) : [$this->manager->getField('loginName')];
        var_log($loginNames,'loginname.>>');
        foreach ($loginNames as $v){
            $tmp = \Prj\Data\Manager::getCopy($v);
            $tmp->load();
            if($tmp->exists() && $tmp->getField('ec')){
                $ecUserIds[$tmp->getField('ec')] = $tmp->getField('ec');
            }
        }
        if(!$this->_request->get('userIds') && $this->manager->getField('loginName') == 'root')$ecUserIds = '';
        //var_log($ecUserIds , 'ecids>>>');
        $ret = \Prj\Data\EcData::getStat($ecUserIds , $this->_request->get('start') , $this->_request->get('end'));
        foreach ($ret as &$v){
            $record = \Prj\Data\Manager::loopFindRecords(['ec' => $v['userId']])[0];
            $v['loginName'] = $record['loginName'];
            $v['nickname'] = $record['nickname'];
        }
        return $ret;
    }
}