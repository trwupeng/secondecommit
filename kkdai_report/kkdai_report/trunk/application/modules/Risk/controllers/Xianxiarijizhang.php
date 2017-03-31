<?php
/**
 * 风控经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/17 0014
 * Time: 下午 15:34
 */
use \Prj\Data\FKXianXiaRiJiZhang as FKXianXiaRiJiZhang;
use Sooh\Base\Form\Item as form_def;
class XianxiarijizhangController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();

    }

    protected $account;
    protected $category;
    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];

    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');

        $fieldsMap = [
            'id'            => ['id','id','z2'],
            'riqi'          => ['riqi','日期','z201','datepicker'],
            'zhonglei'      => ['zhonglei', '种类','z202','select',FKXianXiaRiJiZhang::$logicFields['zhonglei']],
            'hetongbianhao' => ['hetongbianhao','合同编号','z203'],
            'kehu'          => ['kehu','客户','z204'],
            'jineyuan'      => ['jineyuan','金额[元]','z205'],
            'zhanghu'       => ['zhanghu','账户','z206','select',\Prj\Data\FKFangKuanRenMingCe::fangkuanrenyinhang()],
            'beizhu'        => ['beizhu','备注','z207'],
        ];

        $this->_viewFk = new \Prj\Misc\ViewFK();
        $this->account = \Prj\Data\FKFangKuanRenMingCe::fangkuanrenyinhang();
        $this->account = ['全部账户']+$this->account;
        $this->category = ['全部类型']+FKXianXiaRiJiZhang::$logicFields['zhonglei'];
        $this->_view->assign('category', $this->category);
        $this->_view->assign('account', $this->account);
        foreach($fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKXianXiaRiJiZhang::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($fieldsMap), $where, 'sort createTime');
            foreach($records as $k => $v){
                foreach($v as $key => $value) {
                    $records[$k][$key] = FKXianXiaRiJiZhang::parseFieldToString($key, $value);
                }
            }
            $this->_viewFk->setPk('id',false)->setData($records);
            $excel = $this->_viewFk->toExcel($records);
            return $this->downExcel($excel['records'], $excel['header'], ['hetongbianhao'=>'string']);
        }


        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum,false);
        $pager->init(-1, $pageid);

        $selectedCategory = $this->_request->get('selectedCategory');
        $hetongbianhao = $this->_request->get('hetongbianhao');
        $kehu = $this->_request->get('kehu');
        $selectedAccount = $this->_request->get('selectedAccount');
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');
        $this->_view->assign('hetongbianhao', $hetongbianhao);
        $this->_view->assign('kehu', $kehu);
        $this->_view->assign('selectedCategory', $selectedCategory);
        $this->_view->assign('selectedAccount', $selectedAccount);
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);
        $where = [];
        if(!empty($ymdFrom)) {
            if(!\Rpt\Funcs::check_date($ymdFrom)){
                return $this->returnError('起始日期格式错误, 格式如:2016-08-08');
            }else {
                $where['riqi]'] = date('Ymd', strtotime($ymdFrom));
            }
        }

        if(!empty($ymdTo)) {
            if (!\Rpt\Funcs::check_date($ymdTo)){
                return $this->returnError('结束日期格式错误, 格式如:2016-08-08');
            }        else {
                $where['riqi['] = date("Ymd", strtotime($ymdTo));
            }
        }

        if(!empty($ymdFrom) && !empty($ymdTo)) {
            if(date('Ymd', strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
                return $this->returnError('起始日期应该小于结束日期');
            }
        }

        $ymdFrom && !\Rpt\Funcs::check_date($ymdFrom) && $where['riqi]']=date('Ymd', strtotime($ymdFrom));
        $ymdTo && !\Rpt\Funcs::check_date($ymdTo) && $where['riqi]']=date('Ymd', strtotime($ymdTo));
        $selectedCategory && $where['zhonglei']=$selectedCategory;
        $hetongbianhao && $where['hetongbianhao']=$hetongbianhao;
        $kehu && $where['kehu*'] = '%'.$kehu.'%';
        $selectedAccount && $where['zhanghu']=$selectedAccount;

        $records = FKXianXiaRiJiZhang::paged($pager, $where, 'sort createTime');
        foreach($records as $k => $v){
            foreach($v as $key => $value) {
                $records[$k][$key] = FKXianXiaRiJiZhang::parseFieldToString($key, $value);
            }
        }

        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'del'));

        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('where', urlencode(json_encode($where)));
    }


    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        $record['updateTime']=time();
        if($record['jineyuan']==''){
            $record['jineyuan'] = 0;
        }
        if(!is_numeric($record['jineyuan'])){
            return $this->returnError('金额输入有误');
        }
        if(!\Rpt\Funcs::check_date($record['riqi'])){
            return $this->returnError('日期输入有误');
        }

        $amount = explode('.', $record['jineyuan']);
        if(sizeof($amount)==2){
            $tmp = substr($amount[1], 2)-0;
            if($tmp>0){
                return $this->returnError('金额最多两位小数');
            }
        }
        $record['jineyuan'] *= 100;
        $record['riqi'] = date('Ymd', strtotime($record['riqi']));
        // 新增记录

        if(empty($id)){
            $model = FKXianXiaRiJiZhang::getCopy();
            $tb = $model->tbname();
            $db = $model->db();
            $record['createTime']=time();
            $record['iRecordVerID'] = 1;

            try {
                $id = $db->addRecord($tb, $record);
                $this->_view->assign('_id', $id);
            }catch(\ErrorException $e) {
                    return $this->returnError('添加失败');
            }
            return $this->returnOK('添加成功');

        }else{
            // 更新记录
            $model = FKXianXiaRiJiZhang::getCopy($id);
            $model->load();
            if(!$model->exists()){
                return $this->returnError('未查找到要修改记录');
            }
            foreach($record as $k => $v) {
                $model->setField($k, $v);
            }

            try {
                $model->update();
                return $this->returnOK('更新成功');
            }catch (\ErrorException $e) {
                return $this->returnError('更新失败');
            }
        }
    }

    public function delAction () {
        $id = $this->_request->get('_id');
        $model=FKXianXiaRiJiZhang::getCopy($id);
        $model->load();
        if($model->exists()){
            $model->delete();
            return $this->returnOk('删除成功');
        }else {
            return $this->returnError('要删除的记录不存在');
        }
    }
    
    /**
     * 导入功能开发
     **/
    protected $location;
    protected $title=[ 'riqi','zhonglei','hetongbianhao','kehu',
                       'jineyuan','zhanghu','beizhu'];

    public function importAction(){
    
        $where = \Prj\Misc\View::decodePkey($_REQUEST['_pkey_val_']);
        $formEdit = \Sooh\Base\Form\Broker::getCopy('default')
        ->init(\Sooh\Base\Tools::uri(), 'get', \Sooh\Base\Form\Broker::type_c);
        $formEdit->addItem('import', form_def::factory('导入数据', '', form_def::mulit));
    
        $formEdit->fillValues();
        if ($formEdit->flgIsThisForm){
    
            $fields = $formEdit->getFields();
            $rs=$fields['import'];
    
            $rem=\Prj\Misc\FengKongImport::exceltoarry($rs);
            $this->location=current($rem);
            unset($rem[0]);
            foreach ($rem as $v){
                $arr=preg_split("/[\t]/",$v);
    
                $arr[0]=\Prj\Misc\FengKongImport::checktime($arr[0]);

                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],FKXianXiaRiJiZhang::$logicFields['zhonglei']);
                $arr[5]=\Prj\Misc\FengKongImport::transvk($arr[5],\Prj\Data\FKFangKuanRenMingCe::fangkuanrenyinhang());
    
                $result[]=[
                    'riqi'=>$arr[0],
                    'zhonglei'=>$arr[1],
                    'hetongbianhao'=>$arr[2],
                    'kehu'=>$arr[3],
                    'jineyuan'=>$arr[4],
                    'zhanghu'=>$arr[5],
                    'beizhu'=>$arr[6],
                ];
    
            }

            foreach ($result as $data){
                
                $data=$this->formatFields($data);
                $data['updateTime']=time();
                if($data['jineyuan']==''){
                    $data['jineyuan'] = 0;
                }
                if(!is_numeric($data['jineyuan'])){
                    return $this->returnError('金额输入有误');
                }
                if(!\Rpt\Funcs::check_date($data['riqi'])){
                    return $this->returnError('日期输入有误');
                }
                
                $amount = explode('.', $data['jineyuan']);
                if(sizeof($amount)==2){
                    $tmp = substr($amount[1], 2)-0;
                    if($tmp>0){
                        return $this->returnError('金额最多两位小数');
                    }
                }
                $data['riqi'] = date('Ymd', strtotime($data['riqi']));
                    
                $model = FKXianXiaRiJiZhang::getCopy('');
                $tb = $model->tbname();
                $db = $model->db();
                $data['createTime']=time();
                $data['iRecordVerID'] = 1;

            try {
                $id = $db->addRecord($tb, $data);
                $this->_view->assign('_id', $id);
              }catch(\ErrorException $e) {
                    return $this->returnError('导入失败');
              }
            }
             
            $this->_view->assign('_id', $dbRet);
            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('导入成功');
            return;
        }else{
    
        }
    }
    
    public  function formatFields($data){
        $ret = [];
        $tmpDaoQiRiQi = 0;
        foreach ($data as $k => $v) {
            switch ($k) {
                case 'riqi':
                    if(!empty($v)){
                        $datetime=explode('-',$v);
                        if(empty($datetime[2])){
                            $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                            throw new \Exception($name.'日期格式不正确', 90003);
                        }else{
                            $tmpDaoQiRiQi=$v ? date('Y-m-d',strtotime($v)) : 0;
                        }
                    }
                    $ret[$k]=$tmpDaoQiRiQi;
                    break;
                case 'zhonglei':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zhanghu':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKXianXiaRiJiZhang::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}