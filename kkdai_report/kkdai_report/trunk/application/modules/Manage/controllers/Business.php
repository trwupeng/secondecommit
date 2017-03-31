<?php
use Sooh\Base\Form\Item as form_def;
use Prj\Data\Manager as Manager;
use \Prj\Misc\ManageLog as ManageLog;
use \Prj\Consts\Manage as cManage;

class BusinessController extends \Prj\ManagerCtrl {

    protected $limits = [];

    protected $modelName = 'bus';

    protected $rights = [
        /*
        'bus_conf'=>'业务设置',
        'bus_rzyw'=>'融资业务',
        'bus_ywjz'=>'业务进展',
        */
        '*'=>'全部业务权限',
    ];

    protected $rptRights = [
        'rpt_bus'=>'业务报表',
        '*'=>'业务报表全部权限'
    ];

    protected function rightsCkeck(){

    }

    public function onInit_chkLogin(){
        parent::onInit_chkLogin();
        var_log($this->manager->rights,'我的权限>>>>>>>>>>>>>');
    }

    public function indexAction () {
        $this->needRights('bus_rzyw');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'businessId'    => ['ID', '20'],
            'date' => ['月份','20'],
            'week' => ['周数','20'],

            'surveyNum'    => ['业务调查情况-户数', '20'],
            'loanNum'    => ['业务放款情况-户数', '20'],
            'businessNum'    => ['展期业务情况-户数', '20'],
            'settleNum'    => ['业务结清情况-户数', '20'],

            'surveyAmount'    => ['业务调查情况-金额(万)', '20'],
            'loanAmount'    => ['业务放款情况-金额(万)', '20'],
            'businessAmount'    => ['展期业务情况-金额(万)', '20'],
            'settleAmount'    => ['业务结清情况-金额(万)', '20'],

            'remainNum' => ['月末户数','20'],
            'remainAmount' => ['期末存量(万元)','20'],
            //'createUser' => ['创建人','20'],
            'updateUser' => ['最后更新人','20'],
            'updateTime' => ['更新时间','20'],

            'status'=>['是否确认','20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_date_eq', form_def::factory('月份', '', form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['date=']))$where['date='] = date('Ym',strtotime($where['date=']));
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        /*
        //是否有查看全部的权限
        if(!in_array('fin_lookall',$this->manager->rights)){
            $unders = $this->manager->getField('underLoginName');
            $nickNames[] = $this->manager->getField('loginName');
            if(!empty($unders)){
                $nickNames = array_merge($nickNames,explode(',',$unders));
            }
            $where = array_merge($where,['createUser'=>$nickNames]);
        }
        */
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\Business::paged($pager,$where,'rsort date rsort week');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['id'];
                }
                //todo 主键匹配
                if(!in_array($v['businessId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $tempArr['remainNum']=\Prj\Data\BusinessNum::getNums($tempArr['date'])['numAfter'];
            $tempArr['remainAmount']+=\Prj\Data\Config::get('businessAmount');
            foreach($this->moneyArr as $v){
                if(array_key_exists($v,$tempArr)){
                    $tempArr[$v]/=1000000;
                    $tempArr[$v] = sprintf('%.2f',$tempArr[$v]);
                }
            }
            $tempArr['date'] = date('Y-m',strtotime($tempArr['date'].'01'));
            $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['updateUser'] = Manager::getName($tempArr['updateUser']);
            //$tempArr['createUser'] = Manager::getName($tempArr['createUser']);
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL || $ids)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    /**
     *
     * @return mixed
     * @throws ErrorException
     */
    protected function getRemain($ym){
        $tmp = \Prj\Data\Business::getCopy('');
        $db = $tmp->db();
        $tb = $tmp->tbname();
        $rs = $db->getRecord($tb,'*',['date<'=>$ym],'rsort week');
        if(empty($rs)){
            $remain['remainNum'] = 0;
            $remain['remainAmount'] = 0;
        }else{
            $remain['remainNum'] = $rs['remainNum'];
            $remain['remainAmount'] = $rs['remainAmount'];
        }
        return $remain;
    }

    protected $moneyArr = ['surveyAmount','loanAmount','businessAmount','settleAmount','remainAmount'];

    protected $nunArr = ['surveyNum','loanNum','businessNum','settleNum'];

    public function editAction(){
        $this->closeAndReloadPage();
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('surveyNum', form_def::factory('业务调查情况 户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('surveyAmount', form_def::factory('金额(万元)', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('loanNum', form_def::factory('业务放款情况 户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('loanAmount', form_def::factory('金额(万元)', '', form_def::text, [], ['data-rule' => 'required,number']))

            ->addItem('businessNum', form_def::factory('展期业务情况 户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('businessAmount', form_def::factory('金额(万元)', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('settleNum', form_def::factory('业务结清情况 户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('settleAmount', form_def::factory('金额(万元)', '', form_def::text, [], ['data-rule' => 'required,number']))

            ->addItem('date', form_def::factory('月份', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('week', form_def::factory('第几周', 1, form_def::select, [1=>1,2=>2,3=>3,4=>4], ['data-rule' => 'required,number']))

            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['date'] = date('Ym',strtotime($fields['date']));
                    foreach($this->moneyArr as $v){
                        if(array_key_exists($v,$fields)){
                            $fields[$v]*=1000000;
                        }
                    }
                    $fields['remainAmount'] = 0;
                    $fields['remainNum'] = 0;

                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    $doType = \Prj\Consts\Manage::insert;
                    //todo 插入数据库
                    try{
                        if($bus = \Prj\Data\Business::add($fields,$this->manager->getField('loginName'))){
                            try{
                                $bus->update();
                            }catch (\ErrorException $e){
                                return $this->returnError($e->getMessage());
                            }
                        }else{
                            return $this->returnError('新增失败！');
                        }
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }

                } else { // update
                    $op   = '更新';
                    $doType = \Prj\Consts\Manage::update;
                    //todo 更新数据库
                    $bus = \Prj\Data\Business::getCopy($where['id']);
                    $bus->load();
                    if(!$bus->exists())return $this->returnError('找不到记录');
                    foreach($fields as $k=>$v){
                        $bus->setField($k,$v);
                    }
                    try{
                        $bus->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                }
                //刷新余量
                \Prj\Data\Business::refreshRemain($fields['date'],$fields['week']);
                //记录日志
                $logs['itemId'] = current($bus->getPKey());
                $logs['itemType'] = \Prj\Consts\Manage::item_bus;
                $logs['doType'] = $doType;
                try{
                    ManageLog::createLog($this->manager->getField('loginName'),$logs['itemType'],$logs['doType'],$fields,$logs['itemId'])->save();
                }catch (\ErrorException $e){
                    return $this->returnError();
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $bus = \Prj\Data\Business::getCopy($where['id']);
            $bus->load();
            $arr = $bus->dump();
            var_log($arr);
            $arr['date'].='01';
            foreach($frm->items as $k=>$v){
                if(in_array($k,$this->moneyArr))$arr[$k]/=1000000;
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function businessShowAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'businessId'    => ['ID', '20'],
            'date' => ['月份','20'],
            'week' => ['周数','20'],

            'surveyNum'    => ['业务调查情况-户数', '20'],
            'loanNum'    => ['业务放款情况-户数', '20'],
            'businessNum'    => ['展期业务情况-户数', '20'],
            'settleNum'    => ['业务结清情况-户数', '20'],

            'surveyAmount'    => ['业务调查情况-金额(万)', '20'],
            'loanAmount'    => ['业务放款情况-金额(万)', '20'],
            'businessAmount'    => ['展期业务情况-金额(万)', '20'],
            'settleAmount'    => ['业务结清情况-金额(万)', '20'],

            'remainNum' => ['期末户数','20'],
            'remainAmount' => ['期末存量(万元)','20'],
            'createUser' => ['创建人','20'],
            'updateUser' => ['最后更新人','20'],
            'updateTime' => ['更新时间','20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_date_eq', form_def::factory('月份', '', form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['date='])){
                $where['date'] = date('Ym',strtotime($where['date=']));
                unset($where['date=']);
            }
        } else {
            $where = array('date'=>date('Ym'));
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);

        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        /*
        $rs = \Prj\Data\Business::paged($pager,$where,'rsort date rsort week');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
                }
                //todo 主键匹配
                if(!in_array($v['orderId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $tempArr['remainNum']+=\Prj\Data\BusinessNum::getNum($tempArr['date']);
            $tempArr['remainAmount']+=\Prj\Data\Config::get('businessAmount');
            foreach($this->moneyArr as $v){
                if(array_key_exists($v,$tempArr)){
                    $tempArr[$v]/=1000000;
                    $tempArr[$v] = sprintf('%.2f',$tempArr[$v]);
                }
            }
            $tempArr['date'] = date('Y-m',strtotime($tempArr['date'].'01'));
            $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['updateUser'] = Manager::getName($tempArr['updateUser']);
            $tempArr['createUser'] = Manager::getName($tempArr['createUser']);

            $tempArr[1]['num'] = $tempArr['surveyNum'];
            $tempArr[1]['amout'] = $tempArr['surveyAmount'];

            $tempArr[2]['num'] = $tempArr['loanNum'];
            $tempArr[2]['amout'] = $tempArr['loanAmount'];

            $tempArr[3]['num'] = $tempArr['businessNum'];
            $tempArr[3]['amout'] = $tempArr['businessAmount'];

            $tempArr[4]['num'] = $tempArr['settleNum'];
            $tempArr[4]['amout'] = $tempArr['settleAmount'];
            //===
            $newArr[$tempArr['week']] = $tempArr;
        }
        $rs = $newArr;
        $itemName = ['业务调查情况','业务放款情况','展期业务情况','业务结清情况'];
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出

        $this->_view->assign('rs', $rs);
        $this->_view->assign('itemName', $itemName);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        */
        $this->_view->assign('ym', $where['date']);
        //$this->_view->assign('remains',$this->getRemain($where['date']));
        //$this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    function numsAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'month'    => ['月份', '20'],
            'num'    => ['月初户数', '20'],
            'numAfter'    => ['月末户数', '20'],
            'week' => ['确认到第几周','20'],
            'updateUser'    => ['更新人', '20'],
            'updateTime'    => ['更新时间', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_month_eq', form_def::factory('月份', '', form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(isset($where['month=']))$where['month='] = date('Ym',strtotime($where['month=']));
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\BusinessNum::paged($pager,$where,'rsort month');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
                }
                //todo 主键匹配
                if(!in_array($v['orderId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['updateUser'] = \Prj\Data\Manager::getName($tempArr['updateUser']);
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    function numsEditAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $where['_type'];
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('month', form_def::factory('月份', '', form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('num', form_def::factory('月初户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('numAfter', form_def::factory('月末户数', '', form_def::text, [], ['data-rule' => 'required,number']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['month'] = date('Ym',strtotime($fields['month']));
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                    try{
                        $tmp = \Prj\Data\BusinessNum::add($fields,$this->manager->getField('loginName'));
                        $tmp->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                    $type = $this->_request->get('_type');
                    var_log($where,'where>>>');
                    $tmp = \Prj\Data\BusinessNum::getCopy($where['month']);
                    $tmp->load();
                    if(!$tmp->exists() && $type !='check')return $this->returnError('找不到记录');
                    foreach($fields as $k=>$v){
                        $tmp->setField($k,$v);
                    }
                    if($type=='check'){
                        $tmp->setField('updateTime',date('YmdHis'));
                        $tmp->setField('updateUser',$this->manager->getField('loginName'));

                        $bus = \Prj\Data\Business::getCopy($where['id']);
                        $bus->load();
                        $week = $bus->getField('week');
                        $tmp->setField('week',$week);
                        $date = $bus->getField('date');
                        $db = $bus->db();
                        $tb = $bus->tbname();
                        if($week!=1){
                            $count = $db->getRecordCount($tb,['date'=>$date,'week'=>$week-1,'status'=>4]);
                            if(empty($count)){
                                return $this->returnError('上一周数据尚未确认！');
                            }
                        }
                        try{
                            $bus->setField('updateTime',date('YmdHis'));
                            $bus->setField('updateUser',$this->manager->getField('loginName'));
                            $bus->setField('status',4);
                            $bus->update();
                        }catch (\ErrorException $e){
                            return $this->returnError($e->getMessage());
                        }
                    }

                    try{
                        $tmp->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            var_log($where['month'],'month>>>');
            $tmp = \Prj\Data\BusinessNum::getCopy($where['month']);
            $tmp->load();
            if($tmp->exists()){
                $arr = $tmp->dump();
                $arr['month'] = date('Ymd',strtotime($arr['month'].'01'));
                foreach($frm->items as $k=>$v){
                    if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
                }
            }else{
                $frm->items['month']->value = $where['month'].'01';
            }

        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function confAction(){
        //$this->needRights('bus_conf');
        $employee = \Prj\Data\Manager::loopFindRecords(['dept'=>'bus']);
        $confs = \Prj\Data\Config::loopFindRecords(['k'=>['businessNum','businessAmount']]);
        foreach($confs as $v){
            $busConfs[$v['k']] = $v['v'];
            if(in_array($v['k'],['businessAmount'])){
                $busConfs[$v['k']]/=1000000;
                $busConfs[$v['k']] = sprintf('%.2f',$busConfs[$v['k']]);
            }
        }
        $ym = date('Ym');
        $remains = \Prj\Data\Business::getRemainAfter($ym);
        $this->_view->assign('employee',$employee);
        $this->_view->assign('busConfs',$busConfs);
        $this->_view->assign('remains',$remains);
    }

    function confEditAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('businessAmount', form_def::factory('业务-期初余额(万元)', '', form_def::text, [], ['data-rule' => '']))
            //->addItem('businessNum', form_def::factory('业务-期初余量', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', '');

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['businessAmount']*=1000000;

                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "更新";
                    //todo 插入数据库
                    foreach($fields as $k=>$v){
                        $conf = \Prj\Data\Config::getCopy($k);
                        $conf->load();
                        $conf->setField('v',$v);
                        try{
                            $conf->update();
                        }catch (\ErrorException $e){
                            return $this->returnError($e->getMessage());
                        }
                    }
                    //记录日志
                    try{
                        ManageLog::createLog($this->manager->getField('loginName'),cManage::item_config,cManage::update,$fields)->save();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }


        //todo 字段展示 设置item的value
        $arr = ['businessAmount'];
        $confs = \Prj\Data\Config::loopFindRecords(['k'=>$arr]);
        foreach($confs as $v){
           $finConf[$v['k']] = $v['v'];
            if($v['k']=='businessAmount')$finConf[$v['k']]/=1000000;
        }
        foreach($arr as $v){
            $frm->items[$v]->value = $finConf[$v];
        }


        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function rightsEditAction(){
        $groupselect = [];

        $employee = \Prj\Data\Manager::loopFindRecords(['dept'=>'bus']);
        foreach($employee as $v){
            $usersCB[$v['loginName']] = $v['nickname'];
        }

        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $groupselect+=['0'=>'未设置'];
        $frm->addItem('nickname', form_def::factory('姓名', '', form_def::constval, [], ['data-rule' => '']))
            //->addItem('groupId', form_def::factory('用户组', key($groupselect), form_def::select, $groupselect, ['data-rule' => '']))
            ->addItem('spRights', form_def::factory('后台权限', '', form_def::chkbox, $this->rights, ['data-rule' => '']))
            ->addItem('rptRights', form_def::factory('报表权限', '', form_def::chkbox, $this->rptRights, ['data-rule' => '']))
            ->addItem('underLoginName', form_def::factory('可查看的人员', '', form_def::chkbox, $usersCB, ['data-rule' => '']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                    $manage = \Prj\Data\Manager::getCopy($where['loginName']);
                    $manage->load();
                    //$manage->setField('groupId',$fields['groupId']);
                   \Prj\Data\ManagerRight::updateRightsByType($manage->getField('loginName'),$this->modelName,$fields['spRights']);
                   \Prj\Data\ManagerRight::updateRptRightsByType($manage->getField('loginName'),'rpt',$fields['rptRights']);

                    $manage->setField('underLoginName',implode(',',$fields['underLoginName']));
                    try{
                        $manage->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    //记录日志
                    try{
                        ManageLog::createLog($this->manager->getField('loginName'),cManage::item_rights_set,cManage::update,($where+$fields))->save();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $manage = \Prj\Data\Manager::getCopy($where['loginName']);
            $manage->load();
            if($manage->exists()){
                $frm->items['nickname']->value = $manage->getField('nickname');
                //$frm->items['groupId']->value = $manage->getField('groupId');
                $frm->items['spRights']->value = implode(',',\Prj\Data\ManagerRight::getRightsByType($manage->getField('loginName'),$this->modelName));
                $frm->items['rptRights']->value = implode(',',\Prj\Data\ManagerRight::getRptRightsByType($manage->getField('loginName'),'rpt'));
                $frm->items['underLoginName']->value = $manage->getField('underLoginName');
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function targetAction(){
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'month'    => ['月份', '20'],
            'loginName'    => ['团队负责人', '20'],
            'target'    => ['指标(万元)', '20'],
            'updateUser'    => ['更新人', '20'],
            'updateTime'    => ['更新时间', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_month_eq', form_def::factory('月份', '', form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['month=']))$where['month='] = date('Ym',strtotime($where['month=']));
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\BusinessTarget::paged($pager,$where,'rsort month');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['ordersId'];
                }
                //todo 主键匹配
                if(!in_array($v['orderId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $tempArr['target']/=1000000;
            $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['updateUser'] = \Prj\Data\Manager::getName($tempArr['updateUser']);
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    function targetEditAction(){
        $loginName = $this->manager->getField('loginName');
        $loginNames = \Prj\Data\Manager::getUnderLoginName($loginName);
        $selectNames = [];
        if(!empty($loginNames)){
            foreach($loginNames as $v){
                $selectNames[$v] = \Prj\Data\Manager::getName($v);
            }
        }

        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('month', form_def::factory('月份', date('Ym01'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('loginName', form_def::factory('姓名', \Prj\Data\Manager::getName($loginName), form_def::constval,[], ['data-rule' => 'required']))
            ->addItem('_loginName', $loginName)
            ->addItem('target', form_def::factory('指标(万元)', '', form_def::text,[], ['data-rule' => 'required,number']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['target']*=1000000;
                    $fields['month'] = date('Ym',strtotime($fields['month']));
                    $fields['loginName'] = $this->_request->get('_loginName');
                    if(empty($fields['loginName'])){
                        return $this->returnError('请填写姓名！');
                    }
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                    $loginName = $this->manager->getField('loginName');
                    try{
                        $tar = \Prj\Data\BusinessTarget::add($fields,$loginName);
                        $tar->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                    $tar = \Prj\Data\BusinessTarget::getCopy($where['month'],$where['loginName']);
                    $tar->load();
                    if(!$tar->exists())return $this->returnError('找不到记录');
                    foreach($fields as $k=>$v){
                        $tar->setField($k,$v);
                    }
                    try{
                        $tar->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }

                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('target'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $tar = \Prj\Data\BusinessTarget::getCopy($where['month'],$where['loginName']);
            $tar->load();
            $arr = $tar->dump();
            $arr['target']/=1000000;
            $arr['month'] = date('Ym01',strtotime($arr['month'].'01'));
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function progressAction(){
        $this->needRights('bus_ywjz');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'progressId'    => ['ID', '20'],
            'date'    => ['月份', '20'],
            'member'    => ['业务员', '20'],
            'targetAmout'    => ['放款指标额度(万)', '20'],
            'realAmount'    => ['实际放款额度(万)', '20'],
            'percent'    => ['指标完成率(%)', '20'],
            'updateUser'    => ['最后更新人', '20'],
            'updateTime'    => ['更新时间', '20'],
            'leader'    => ['团队负责人', '20'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_date_eq', form_def::factory('月份', '', form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['date=']))$where['date='] = date('Ym',strtotime($where['date=']));
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        /*
        //是否有查看全部的权限
        if(!in_array('fin_lookall',$this->manager->rights)){
            $unders = $this->manager->getField('underLoginName');
            $nickNames[] = $this->manager->getField('loginName');
            if(!empty($unders)){
                $nickNames = array_merge($nickNames,explode(',',$unders));
            }
            $where = array_merge($where,['createUser'=>$nickNames]);
        }
        */
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\BusinessProgress::paged($pager,$where,'rsort date rsort createTime');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = '';
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['id'];
                }
                //todo 主键匹配
                if(!in_array($v['progressId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $tempArr['targetAmout'] = \Prj\Data\BusinessTarget::getTarget($tempArr['date'],$tempArr['leader'])/1000000;
            $tempArr['realAmount']/=1000000;
            $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['date'] = date('Y-m',strtotime($tempArr['date'].'01'));
            $tempArr['updateUser'] = Manager::getName($tempArr['updateUser']);
            $tempArr['leader'] = Manager::getName($tempArr['leader']);
            $tempArr['member'] = Manager::getName($tempArr['member']);
            if(!empty($tempArr['targetAmout']))$tempArr['percent'] = floor($tempArr['realAmount']/$tempArr['targetAmout']*10000)/100;
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL)return $this->downEXCEL($rs, array_keys($header),null,true);
        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
    }

    function progressEditAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);
        $members = \Prj\Data\Manager::getUnderLoginName($this->manager->getField('loginName'));
        $members = array_combine($members,$members);
        $members = array_map(function($v){return \Prj\Data\Manager::getName($v);},$members);
        $members[''] = '';
        $frm->addItem('realAmount', form_def::factory('实际放款额度(万)', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('date', form_def::factory('月份', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('member', form_def::factory('业务员', '', form_def::select, $members+[''=>''], ['data-rule' => 'required']))
            ->addItem('_type', $type)
            ->addItem('_pkey_val_', $this->_request->get('_pkey_val_'));

        //todo 构造表单数据

        $frm->fillValues();
        //表单提交
        if ($frm->flgIsThisForm)
        {
            //审核通过
            if ($type == 'check') {

            }
            $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
            if (!empty($where)) {
                $frm->switchType(\Sooh\Base\Form\Broker::type_u);
            } else {
                $frm->switchType(\Sooh\Base\Form\Broker::type_c);
            }
            $op = '';
            try {
                $fields = $frm->getFields();
                try{
                    //todo 字段过滤
                    $fields['leader'] = $this->manager->getField('loginName');
                    if(empty($fields['leader']))return $this->returnError('该帐号尚未分配到团队！');
                    //$fields['member'] = $this->manager->getField('loginName');
                    $fields['realAmount']*=1000000;
                    $fields['date'] = date('Ym',strtotime($fields['date']));
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    $doType = \Prj\Consts\Manage::insert;
                    //todo 插入数据库

                        try{
                            $pro = \Prj\Data\BusinessProgress::add($fields,$this->manager->getField('loginName'));
                            $pro->update();
                        }catch (\ErrorException $e){
                            return $this->returnError($e->getMessage());
                        }


                } else { // update
                    $op   = '更新';
                    $doType = \Prj\Consts\Manage::update;
                    //todo 更新数据库
                    $pro = \Prj\Data\BusinessProgress::getCopy($where['id']);
                    $pro->load();
                    if(!$pro->exists())return $this->returnError('找不到记录');
                    $fields['updateUser'] = $this->manager->getField('loginName');
                    $fields['updateTime'] = date('YmdHis');
                    foreach($fields as $k=>$v){
                        $pro->setField($k,$v);
                    }
                    try{
                        $pro->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }

                }
                //记录日志
                $logs['itemId'] = current($pro->getPKey());
                $logs['itemType'] = \Prj\Consts\Manage::item_bus_progress;
                $logs['doType'] = $doType;
                try{
                    ManageLog::createLog($this->manager->getField('loginName'),$logs['itemType'],$logs['doType'],$fields,$logs['itemId'])->save();
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('progress'));
            $this->returnOK($op . '成功');
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $pro = \Prj\Data\BusinessProgress::getCopy($where['id']);
            $pro->load();
            $arr = $pro->dump();
            $arr['targetAmout']/=1000000;
            $arr['realAmount']/=1000000;
            $arr['date'].='01';
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = $arr[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }
}