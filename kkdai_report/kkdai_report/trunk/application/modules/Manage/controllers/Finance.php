<?php
use Sooh\Base\Form\Item as form_def;
use \Prj\Consts\Finance as cFin;
use \Prj\Data\Manager as Manager;
use \Prj\Misc\ManageLog as ManageLog;
use \Prj\Consts\Manage as cManage;

class FinanceController extends \Prj\ManagerCtrl {

    protected $limits = [];

    protected $modelName = 'fin';

    protected $rights = [
        'fin_kkd'=>'财务快快金融平台',
        'fin_my'=>'财务美豫平台',
        'fin_xs'=>'财务线上平台',
        'fin_conf'=>'财务设置',
        'fin_xx'=>'财务线下业务',
        'fin_lookall'=>'查看所有人',
        '*'=>'全部财务权限',
    ];

    protected $rptRights = [
        'rpt_fin'=>'财务报表',
        '*'=>'全部财务权限',
    ];



    protected function rightsCkeck(){
        var_log($this->manager->rights,'我的权限>>>>>>>>>>>>>>>');
        if(in_array('fin_kkd',$this->manager->rights))$this->limits[] = \Prj\Consts\Finance::type_kkd;
        if(in_array('fin_my',$this->manager->rights))$this->limits[] = \Prj\Consts\Finance::type_my;
        if(in_array('fin_xs',$this->manager->rights))$this->limits[] = \Prj\Consts\Finance::type_xstb;
    }

    public function onInit_chkLogin(){
        parent::onInit_chkLogin();
        $this->rightsCkeck();
    }

    public function indexAction () {
        /*
        $weekly = \Rpt\Misc\Base\Weekly::getCopy('FinanceK','20160116');
        $weekly->addFromDaily();
        */

        $this->needRights(['fin_kkd','fin_my','fin_xs']);
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'financeId'    => ['流水号', '20'],
            'exp'=>['说明','auto'],
            'type'=>['类型','auto'],
            'income'=>['收入(元)','auto'],
            'payment'=>['支出(元)','auto'],
            'remain'=>['期末余额','auto'],
            'date'=>['流水日期','auto'],
            'updateUser'=>['最后更新者','auto'],
            //'updateTime'=>['最后更新时间','auto'],
        );
        $key = 'fa'.date('Ym');
        $baseAmount = \Prj\Data\Config::get($key);
        var_log($baseAmount,'期末余额>>>>>>>>>>>>>>');
        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_date_eq', form_def::factory('日期', '', form_def::datepicker))
            ->addItem('_type_eq', form_def::factory('类型', '', form_def::select,\Prj\Consts\Finance::$type_enum+[''=>'']))
            ->addItem('_income_eq', form_def::factory('收支', '', form_def::select,['100'=>'收入','101'=>'支出',''=>'']))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['date=']))$where['date='] = date('Ymd',strtotime($where['date=']));
            if(!empty($where['income='])){
                if($where['income=']==100){
                    $where['income>']=0;
                }elseif($where['income=']==101){
                    $where['payment<'] = 0;
                }
                unset($where['income=']);
            }
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //是否有查看全部的权限
        if(!in_array('fin_lookall',$this->manager->rights)){
            $unders = $this->manager->getField('underLoginName');
            $nickNames[] = $this->manager->getField('loginName');
            if(!empty($unders)){
                $nickNames = array_merge($nickNames,explode(',',$unders));
            }
            $where = array_merge($where,['createUser'=>$nickNames]);
        }
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        if(!empty($this->limits))$where = array_merge($where,['type'=>$this->limits]);
        $rs = \Prj\Data\Finance::paged($pager,$where,'rsort date rsort createTime rsort financeId');

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
                if(!in_array($v['financeId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            $baseAmount+=$tempArr['amount'];
            switch($tempArr['type']){
                case \Prj\Consts\Finance::type_kkd:$remainInit = \Prj\Data\Config::get('amountK');break;
                case \Prj\Consts\Finance::type_my:$remainInit = \Prj\Data\Config::get('amountM');break;
                case \Prj\Consts\Finance::type_xstb:$remainInit = \Prj\Data\Config::get('amountX');break;
                default:$remainInit = 0;
            }
            $tempArr['remain']+=$remainInit;
            $tempArr['remain']/=100;
            $tempArr['income']/=100;
            $tempArr['payment']/=100;
            if(empty($tempArr['income']))$tempArr['income']='';
            if(empty($tempArr['payment']))$tempArr['payment']='';
            $tempArr['type'] = cFin::$type_enum[$tempArr['type']];
            //$tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            $tempArr['date'] = $tempArr['date']?date('Y-m-d',strtotime($tempArr['date'])):'';
            //$tempArr['createTime'] = date('Y-m-d H:i:s',strtotime($tempArr['createTime']));
            $tempArr['updateUser'] = Manager::getName($tempArr['updateUser']);
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

    public function editAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        if(!empty($this->limits)){
            foreach($this->limits as $v){
                $typeInit[$v] = cFin::$type_enum[$v];
            }
        }else{
            $typeInit = cFin::$type_enum;
            unset($typeInit[\Prj\Consts\Finance::type_xxtb]);
        }
        $frm->addItem('explain', form_def::factory('说明', '', form_def::text, [], ['data-rule' => 'required']))
            ->addItem('type', form_def::factory('类型', key($typeInit), form_def::select, $typeInit))
            ->addItem('income', form_def::factory('收入（元）', '', form_def::text))
            ->addItem('payment', form_def::factory('支出（元）', '', form_def::text))
            ->addItem('date', form_def::factory('日期', date('Y-m-d'), form_def::datepicker))
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
                    //todo 字段校验
                    if(!empty($this->limits)){
                        if(!in_array($fields['type'],$this->limits))return $this->returnError('权限不足：type不符合权限要求！');
                    }
                    $fields['income']*=100;
                    $fields['payment'] = abs($fields['payment'])*-100;
                    if(!empty($fields['income'])&&!empty($fields['payment']))return $this->returnError('收支请分开录入！');
                    $amount = $fields['income']?abs($fields['income']):abs($fields['payment'])*-1;
                    $fields['date'] = date('Ymd',strtotime($fields['date']));
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $doType = \Prj\Consts\Manage::insert;
                    $remain = 0;
                    if($fin = \Prj\Data\Finance::add($fields['explain'],$amount,$this->manager->getField('loginName'),$fields['type'],$remain+$amount,$fields['date'])){
                        try{
                            $fin->update();
                        }catch (\ErrorException $e){
                            return $this->returnError($e->getMessage());
                        }
                    }else{
                        return $this->returnError('新增失败！');
                    }
                    //todo 插入数据库
                } else { // update
                    $op   = '更新';
                    $doType = \Prj\Consts\Manage::update;
                    //todo 更新数据库
                    $fin = \Prj\Data\Finance::getCopy($where['id']);
                    $fin->load();
                    if(!$fin->exists())return $this->returnError('更新失败！(找不到流水)');

                    $fin->setField('exp',$fields['explain']);
                    $fin->setField('income',$fields['income']);
                    $fin->setField('payment',$fields['payment']);
                    $fin->setField('type',$fields['type']);
                    $fin->setField('date',$fields['date']);
                    $fin->setField('updateUser',$this->manager->getField('loginName'));
                    $fin->setField('updateTime',date('YmdHis'));
                    try{
                        $fin->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                }
                //记录日志
                $logs['itemId'] = $fin->getPKey()['financeId'];
                $logs['itemType'] = \Prj\Consts\Manage::item_finance;
                $logs['doType'] = $doType;
                $loginName = $this->manager->getField('loginName');
                try{
                    \Prj\Misc\ManageLog::createLog($loginName,$logs['itemType'],$logs['doType'],$fields,$logs['itemId'])->save();
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }
            //提交到日报表
            $name = '';
            switch($fields['type']){
                case \Prj\Consts\Finance::type_kkd:$name = 'FinanceK';break;
                case \Prj\Consts\Finance::type_my:$name = 'FinanceM';break;
                case \Prj\Consts\Finance::type_xstb:$name = 'FinanceXS';break;
            }
            if(!empty($name)){
                try{
                    if(!empty($fields['income'])){
                        \Rpt\Misc\Base\Daily::submit($name,$fields['date']);
                    }else{
                        \Rpt\Misc\Base\Daily::submit($name,$fields['date'],101);
                    }
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }
                error_log($fields['date'].'提交日报>>>'.$name);
            }
            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');
            //刷新余额
            \Prj\Data\Finance::refreshRemain($fields['type'],$fields['date']);
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $fin = \Prj\Data\Finance::getCopy($where['id']);
            $fin->load();
            $fields = $fin->dump();
            $fields['income'] = $fields['income']/100;
            $fields['payment'] = abs($fields['payment']/100);
            $fields['date'] = date('Y-m-d',strtotime($fields['date']));
            $fields['explain'] = $fields['exp'];
            foreach($frm->items as $k=>$v){
                if(in_array($k,['_type','_pkey_val_']))continue;
                $frm->items[$k]->value = $fields[$k];
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function groundFinanceAction(){
        $this->needRights('fin_xx');
        $isDownloadEXCEL = $this->_request->get('__EXCEL__');
        //配置表格
        $fieldsMapArr = array(
            'groundId'    => ['流水号', '160'],
            'exp'    => ['摘要', '200'],

            'incomeTotal' => ['收入合计','67'],
            'payTotal' => ['支出合计','70'],

            'date' => ['流水日期','100'],

            'userAmount'    => ['投资人本金', '72'],
            'borrowerAmount'    => ['借款人还借款本金', '115'],
            'borrowerService'    => ['借款人服务费', '100'],
            'borrowerMargin'    => ['借款人保证金', '100'],
            'borrowerInterest'    => ['借款人贷款利息', '100'],
            'borrowerAgency'    => ['中介费', '55'],
            'incomeOT'    => ['其它收入', '67'],


            'payLoan'    => ['借款人贷款金额', '100'],
            'payInterest'    => ['支付投资人理财利息', '125'],
            'payAmount'    => ['退还投资人本金', '100'],
            'payMargin'    => ['退还借款人保证金', '120'],
            'payAgency'    => ['中介返佣', '70'],
            'payOT'    => ['其它支出', '70'],

            'updateUser'    => ['最后更新人', '75'],
            //'updateTime'    => ['最后更新时间', '150'],
        );

        //配置分页
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pager  = new \Sooh\DB\Pager($this->_request->get('pageSize', 50), $this->pageSizeEnum, false);
        $pager->init(-1,$pageid);

        //配置搜索项
        $frm = \Sooh\Base\Form\Broker::getCopy('default')->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_s);
        $frm->addItem('_date_eq', form_def::factory('日期', date('Ymd'), form_def::datepicker))
            ->addItem('pageId', $pageid)
            ->addItem('pageSize', $this->pager->page_size);
        $frm->fillValues();
        if ($frm->flgIsThisForm) {
            $where = $frm->getWhere();
            if(!empty($where['date=']))$where['date='] = date('Ymd',strtotime($where['date=']));
        } else {
            $where = array();
        }
        //合并表单的查询条件
        $search = \Prj\Misc\View::decodePkey($this->_request->get('where'));
        $where = array_merge($search?$search:[],$where);
        //是否有查看全部的权限
        if(!in_array('fin_lookall',$this->manager->rights)){
            $unders = $this->manager->getField('underLoginName');
            $nickNames[] = $this->manager->getField('loginName');
            if(!empty($unders)){
                $nickNames = array_merge($nickNames,explode(',',$unders));
            }
            $where = array_merge($where,['createUser'=>$nickNames]);
        }
        //拉取记录
        var_log($where,'查询条件>>>>>>>>>>>>>>>>>>');
        $rs = \Prj\Data\FinanceGround::paged($pager,$where,'rsort date rsort createTime rsort groundId');

        //格式配置
        $tempArr = array();
        $newArr  = array();
        foreach ($fieldsMapArr as $kk => $vv) {
            $header[$vv[0]] = $vv[1];
        }
        foreach ($rs as $k => $v) {
            //选中项打印
            if($ids = $this->_request->get('ids')){
                $tmp = [];
                foreach($ids as $vv){
                    $tmp[] = \Prj\Misc\View::decodePkey($vv)['id'];
                }
                var_log($tmp,'tmp>>>');
                //todo 主键匹配
                if(!in_array($v['groundId'],$tmp)){
                    continue;
                }
            }

            foreach ($fieldsMapArr as $kk => $vv) {
                $tempArr[$kk] = $v[$kk];
            }
            //todo 数据格式化
            //$tempArr['remain']+=\Prj\Data\Config::get('amountG');
            foreach(self::$moneyArr as $v){
                if(array_key_exists($v,$tempArr)){
                    $tempArr[$v]/=100;
                    if(empty($tempArr[$v]))$tempArr[$v] = '';
                }
            }
            $tempArr['date'] =  $tempArr['date']?date('Y-m-d',strtotime($tempArr['date'])):'';
            $tempArr['updateUser'] = Manager::getName($tempArr['updateUser']);
           // $tempArr['updateTime'] = date('Y-m-d H:i:s',strtotime($tempArr['updateTime']));
            foreach(self::$moneyIncome as $v){
                $tempArr['incomeTotal']+=$tempArr[$v];
            }
            foreach(self::$moneyPay as $v){
                $tempArr['payTotal']+=$tempArr[$v];
            }
            //===
            $newArr[] = $tempArr;
        }
        $rs = $newArr;
        if($isDownloadEXCEL || $ids)return $this->downEXCEL($rs, array_keys($header),null,true);

        //输出余额
        $remains = \Prj\Data\FinanceGround::getRemainsAfter('');
        $newRemains['exp'] = '期末余额';
        $rmapArr = array_flip(\Prj\Data\FinanceGround::$mapArr);
        foreach($fieldsMapArr as $k=>$v){
            // k = userAmount
            if(in_array($k,\Prj\Data\FinanceGround::$mapArr)){
                $value = \Prj\Data\Config::get($k);
                $newRemains[$rmapArr[$k]] = ($remains[$rmapArr[$k]]+$value)/100;
            }
        }

        //输出
        $this->_view->assign('rs', $rs);
        $this->_view->assign('header', $header);
        $this->_view->assign('pager', $pager);
        $this->_view->assign('where', \Prj\Misc\View::encodePkey($where));
        $this->_view->assign('remains', $newRemains);
    }

    public static $moneyArr = ['userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest','borrowerAgency','incomeOT',
        'payLoan','payInterest','payAmount','payMargin','payAgency','payOT','remain'];

    public static $moneyIncome = ['userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest','borrowerAgency','incomeOT'];

    public static $moneyPay = ['payLoan','payInterest','payAmount','payMargin','payAgency','payOT'];

    public function groundEditAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('exp', form_def::factory('摘要', '', form_def::text, [], ['data-rule' => 'required']))

            ->addItem('userAmount', form_def::factory('投资人本金', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('borrowerAmount', form_def::factory('借款人还借款本金', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('borrowerService', form_def::factory('借款人服务费', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('borrowerMargin', form_def::factory('借款人保证金', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('borrowerInterest', form_def::factory('借款人贷款利息', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('borrowerAgency', form_def::factory('中介费', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('incomeOT', form_def::factory('其它收入', '', form_def::text, [], ['data-rule' => 'number']))

            ->addItem('payLoan', form_def::factory('借款人贷款金额', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('payInterest', form_def::factory('支付投资人理财利息', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('payAmount', form_def::factory('退还投资人本金', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('payMargin', form_def::factory('退还借款人保证金', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('payAgency', form_def::factory('中介返佣', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('payOT', form_def::factory('其它支出', '', form_def::text, [], ['data-rule' => 'number']))
            ->addItem('date', form_def::factory('流水日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'require']))

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
                    foreach(self::$moneyPay as $v){
                        $fields[$v]*=-1;
                    }
                    $fields['createUser'] = $this->manager->getField('loginName');
                    $fields['updateUser'] = $this->manager->getField('loginName');
                    $fields['date'] = date('Ymd',strtotime($fields['date']));
                    $total = 0;
                    foreach(self::$moneyArr as $v){
                        $fields[$v] = $fields[$v]*100;
                        $total+=$fields[$v];
                    }
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    $doType = \Prj\Consts\Manage::insert;
                    $fields['remain'] = 0;
                    //todo 插入数据库
                    if($fing = \Prj\Data\FinanceGround::add($fields)){
                        try{
                            $fing->update();
                        }catch (\ErrorException $e){
                            return $this->returnError($e->getMessage());
                        }

                    }else{
                        return $this->returnError('插入失败');
                    }
                } else { // update
                    $op   = '更新';
                    $doType = \Prj\Consts\Manage::update;
                    //todo 更新数据库
                    $fing = \Prj\Data\FinanceGround::getCopy($where['id']);
                    $fing->load();
                    $fields['updateUser'] = $this->manager->getField('loginName');
                    $fields['updateTime'] = date('YmdHis');
                    foreach($fields as $k=>$v){
                        $fing->setField($k,$v);
                    }
                    try{
                        $fing->update();
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                }
                //记录日志
                $logs['itemId'] = $fing->getPKey()['groundId'];
                try{
                    ManageLog::createLog($this->manager->getField('loginName'),cManage::item_finance_ground,$doType,$fields,$logs['itemId'])->save();
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }
            } catch (\ErrorException $e) {
               return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('groundfinance'));
            $this->returnOK($op . '成功');
            //提交日报
            $name = 'FinanceXX';
            try{
                \Rpt\Misc\Base\Daily::submit($name,$fields['date'],100);
                \Rpt\Misc\Base\Daily::submit($name,$fields['date'],101);
            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
            error_log($fields['date'].'提交日报>>>'.$name);
            //刷新余额
            \Prj\Data\FinanceGround::refreshRemain($fields['date']);
            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
            $fing = \Prj\Data\FinanceGround::getCopy($where['id']);
            $fing->load();
            $arr = $fing->dump();
            foreach($frm->items as $k=>$v){
                if(array_key_exists($k,$arr))$frm->items[$k]->value = is_numeric($arr[$k])?abs($arr[$k]):$arr[$k];
                if(in_array($k,self::$moneyArr))$frm->items[$k]->value/=100;
            }
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    public function importGroundAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('date', form_def::factory('日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('type', form_def::factory('类型', \Prj\Consts\Finance::$type_enum[\Prj\Consts\Finance::type_xxtb], form_def::constval, [], ['data-rule' => 'required']))
            ->addItem('importData', form_def::factory('导入内容', '', form_def::mulit, [], ['data-rule' => 'required']))
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
                    $fields['date'] = date('Ymd',strtotime($fields['date']));
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                    $arr = explode("\r",$fields['importData']);

                    //期初余额计算
                    $tmp = \Prj\Data\FinanceGround::getCopy('');
                    $db = $tmp->db();
                    $tb = $tmp->tbname();
                    $rs = $db->getRecord($tb,'*',[],'rsort createTime');
                    if(empty($rs)){
                        $conf = \Prj\Data\Config::getCopy('amountG');
                        $conf->load();
                        if(!$conf->exists())return $this->returnError('未设置期初余额！');
                        $remain = $conf->getField('v');
                    }else{
                        $remain = $rs['remain'];
                    }


                    foreach($arr as $k=>$v){
                        $tmpv = trim($v);
                        if(empty($tmpv))continue;
                        $arrr = explode("\t",$v);
                        $dataFields['exp'] = $arrr[1];

                        $dataFields['userAmount'] = $arrr[2];
                        $dataFields['borrowerAmount'] = $arrr[3];
                        $dataFields['borrowerService'] = $arrr[4];
                        $dataFields['borrowerMargin'] = $arrr[5];
                        $dataFields['borrowerInterest'] = $arrr[6];
                        $dataFields['borrowerAgency'] = $arrr[7];
                        $dataFields['incomeOT'] = $arrr[8];

                        $dataFields['payLoan'] = $arrr[10];
                        $dataFields['payInterest'] = $arrr[11];
                        $dataFields['payAmount'] = $arrr[12];
                        $dataFields['payMargin'] = $arrr[13];
                        $dataFields['payAgency'] = $arrr[14];
                        $dataFields['payOT'] = $arrr[15];

                        foreach(self::$moneyArr as $v){
                            $dataFields[$v] = abs($dataFields[$v]);
                            $dataFields[$v]*=100;
                            if(in_array($v,self::$moneyPay))$dataFields[$v]*=-1;
                            $remain+=$dataFields[$v];
                        }
                        $dataFields['remain'] = 0;
                        $dataFields['date'] = $fields['date'];
                        $dataFields['createUser'] = $this->manager->getField('loginName');
                        $dataFields['updateUser'] = $this->manager->getField('loginName');

                        if($fing = \Prj\Data\FinanceGround::add($dataFields)){
                            try{
                                $fing->update();
                            }catch (\ErrorException $e){
                                return $this->returnError($e->getMessage());
                            }
                            //记录日志
                            $logs['itemId'] = current($fing->getPKey());
                            $logs['itemType'] = \Prj\Consts\Manage::item_finance_ground;
                            $logs['doType'] = \Prj\Consts\Manage::insert;
                            $logs['doWhat'] = $dataFields;
                            \Prj\Data\ManageLog::addLogs($logs,$this->manager->getField('loginName'));
                        }else{
                            return $this->returnError('插入失败！');
                        }
                        unset($dataFields);
                        $temp[] = $arrr;
                    }

                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                }
                //刷新余额
                \Prj\Data\FinanceGround::refreshRemain($fields['date']);
            } catch (\ErrorException $e) {
                return $this->returnError($op . '失败：冲突，相关记录已经存在？');
            }

            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK($op . '成功');

            //提交到日报表
            $name = 'FinanceXX';
            try{
                \Rpt\Misc\Base\Daily::submit($name,$fields['date'],100);
                \Rpt\Misc\Base\Daily::submit($name,$fields['date'],101);
            }catch (\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
            error_log($fields['date'].'提交日报>>>'.$name);


            return;
        }

        if ($frm->type() == \Sooh\Base\Form\Broker::type_u) //update show
        {
            //todo 字段展示 设置item的value
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function importFinanceAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        if(!empty($this->limits)){
            foreach($this->limits as $v){
                $typeInit[$v] = cFin::$type_enum[$v];
            }
        }else{
            $typeInit = cFin::$type_enum;
            unset($typeInit[\Prj\Consts\Finance::type_xxtb]);
        }

        $frm->addItem('date', form_def::factory('日期', date('Y-m-d'), form_def::datepicker, [], ['data-rule' => 'required']))
            ->addItem('type', form_def::factory('类型', key($typeInit), form_def::select, $typeInit, ['data-rule' => 'required']))
            ->addItem('data', form_def::factory('数据', '', form_def::mulit, [], ['data-rule' => 'required']))
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
                    $fields['date'] = date('Ymd',strtotime($fields['date']));
                    $arr = explode("\r",$fields['data']);
                    //期初余额计算
                    $remain = 0;
                }catch (\ErrorException $e){
                    return $this->returnError($e->getMessage());
                }

                if ($frm->type() == \Sooh\Base\Form\Broker::type_c) //add
                {
                    $op                   = "新增";
                    //todo 插入数据库
                    foreach($arr as $k=>$v){
                        $tmpv = trim($v);
                        if(empty($tmpv))continue;
                        $arrr = explode("\t",$v);
                        $newFields['exp'] = $arrr[1];
                        //exp 跨了两列 从2开始
                        $newFields['amount'] = $arrr[3]?abs($arrr[3]*100):-1*abs($arrr[4]*100);
                        if($fin = \Prj\Data\Finance::add($newFields['exp'],$newFields['amount'],$this->manager->getField('loginName'),$fields['type'],0,$fields['date'])){
                            $fin->update();
                        }else{
                            return $this->returnError('格式错误');
                        }
                    }

                } else { // update
                    $op   = '更新';
                    //todo 更新数据库
                }
                //刷新余额
                \Prj\Data\Finance::refreshRemain($fields['type'],$fields['date']);
                //提交到日报表
                $name = '';
                switch($fields['type']){
                    case \Prj\Consts\Finance::type_kkd:$name = 'FinanceK';break;
                    case \Prj\Consts\Finance::type_my:$name = 'FinanceM';break;
                    case \Prj\Consts\Finance::type_xstb:$name = 'FinanceXS';break;
                }
                if(!empty($name)){
                    try{
                        \Rpt\Misc\Base\Daily::submit($name,$fields['date']);
                        \Rpt\Misc\Base\Daily::submit($name,$fields['date'],101);
                    }catch (\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    error_log($fields['date'].'提交日报>>>'.$name);
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
        }

        //var_dump($fields);
        //die();
        $this->_view->assign('FormOp', $op = '添加商品');
        $this->_view->assign('type', $type);
        $this->_view->assign('_pkey_val_', $this->_request->get('_pkey_val_'));
    }

    function confAction(){
        //$this->needRights('fin_conf');
        $employee = \Prj\Data\Manager::loopFindRecords(['dept'=>'fin']);
        $confs = \Prj\Data\Config::loopFindRecords(['k'=>['amountK','amountM','amountX','userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest',
            'borrowerAgency','incomeOT','payLoan','payInterest','payAmount','payMargin','payAgency','payOT']]);

        foreach($confs as $v){
            $finConf[$v['k']] = $v['v']/100;
            //$finConf[$v['k']] = sprintf('%.2f',$finConf[$v['k']]);
        }
        $ymd = date('Ymd');
        $groundRemains = \Prj\Data\FinanceGround::getRemainsAfter($ymd);
        $remains = [
            'amountK'=>\Prj\Data\Finance::getRemainAfter($ymd,\Prj\Consts\Finance::type_kkd),
            'amountM'=>\Prj\Data\Finance::getRemainAfter($ymd,\Prj\Consts\Finance::type_my),
            'amountX'=>\Prj\Data\Finance::getRemainAfter($ymd,\Prj\Consts\Finance::type_xstb),
        ];
        $map = array_flip(\Prj\Data\FinanceGround::$mapArr);
        foreach($map as $k=>$v){
            $remains[$k] = $groundRemains[$v];
        }
        var_log($remains,'>>>>>>>>>>>>');

        $this->_view->assign('employee',$employee);
        $this->_view->assign('finConf',$finConf);
        $this->_view->assign('remains',$remains);
    }

    function confEditAction(){
        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

        $frm->addItem('amountK', form_def::factory('快快金融期初余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('amountM', form_def::factory('美豫期初余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('amountX', form_def::factory('线上充值期初余额', '', form_def::text, [], ['data-rule' => '']))

            ->addItem('userAmount', form_def::factory('投资人本金余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('borrowerAmount', form_def::factory('借款人还借款本金余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('borrowerService', form_def::factory('借款人服务费余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('borrowerMargin', form_def::factory('借款人保证金余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('borrowerInterest', form_def::factory('借款人贷款利息余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('borrowerAgency', form_def::factory('中介费', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('incomeOT', form_def::factory('其它收入余额', '', form_def::text, [], ['data-rule' => '']))

            ->addItem('payLoan', form_def::factory('借款人贷款金额余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('payInterest', form_def::factory('支付投资人理财利息余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('payAmount', form_def::factory('退还投资人本金余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('payMargin', form_def::factory('退还借款人保证金余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('payAgency', form_def::factory('中介返佣余额', '', form_def::text, [], ['data-rule' => '']))
            ->addItem('payOT', form_def::factory('其它支出余额', '', form_def::text, [], ['data-rule' => '']))

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
                    $fields = array_map(function($a){return $a*100;},$fields);
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
        $arr = ['amountK','amountM','amountX','userAmount','borrowerAmount','borrowerService','borrowerMargin','borrowerInterest',
            'borrowerAgency','incomeOT','payLoan','payInterest','payAmount','payMargin','payAgency','payOT'];
        $confs = \Prj\Data\Config::loopFindRecords(['k'=>$arr]);
        foreach($confs as $v){
            $finConf[$v['k']] = $v['v']/100;
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
        $groups = \Prj\Data\Group::loopFindRecords(['groupType'=>'fin']);
        foreach($groups as $v){
            $groupselect[$v['groupId']] = $v['groupName'];
        }
        $rights = \Prj\Data\Rights::loopFindRecords(['rightsType'=>'fin']);
        foreach($rights as $v){
            $rightsCB[$v['rightsId']] = $v['rightsName'];
        }

        $employee = \Prj\Data\Manager::loopFindRecords(['dept'=>'fin']);
        foreach($employee as $v){
            $usersCB[$v['loginName']] = $v['nickname'];
        }

        $where                          = \Prj\Misc\View::decodePkey($this->_request->get('_pkey_val_'));
        $type                           = $this->_request->get('_type');
        $frm                            = \Sooh\Base\Form\Broker::getCopy('default')
            ->init(\Sooh\Base\Tools::uri(), 'get', empty($where) ? \Sooh\Base\Form\Broker::type_c : \Sooh\Base\Form\Broker::type_u);

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
                    if(!\Prj\Data\ManagerRight::updateRightsByType($manage->getField('loginName'),$this->modelName,$fields['spRights']))return $this->returnError('后台权限错误！');
                    if(!\Prj\Data\ManagerRight::updateRptRightsByType($manage->getField('loginName'),'rpt',$fields['rptRights']))return $this->returnError('报表权限错误！');
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

}