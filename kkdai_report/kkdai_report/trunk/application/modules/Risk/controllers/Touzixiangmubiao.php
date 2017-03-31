<?php
/**
 *
 * TODO: 编辑后, 完成按钮点击没反应
 *
 * 投资项目表
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/19 0014
 * Time: 下午 11:34
 */
use \Prj\Data\FKTouZiXiangMuBiao as FKTouZiXiangMuBiao;
use \Prj\Data\FKTouZiKeHuMingCe as FKTouZiKeHuMingCe;
use Sooh\Base\Form\Item as form_def;
class TouzixiangmubiaoController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();
    }

    /**
     * 结清情况
     * @var array
     */
    protected $jieqingqingkuang;
    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];
    protected $touziren;

    protected $fieldsMap = [
        'id'                    =>['id','id','b600'],
        'touzihetongbianhao'    =>['touzihetongbianhao','投资合同编号 ','b601'],
        'touziren'              =>['touziren','投资人 ','b602','select'],
        'yinhangzhanghao'       =>['yinhangzhanghao','银行账号 ','b603'],
        'kaihuxingxinxi'        =>['kaihuxingxinxi','开户行信息 ','b604'],
        'touziewanyuan'         =>['touziewanyuan','投资额[万元] ','b605'],
        'qishiriqi'             =>['qishiriqi','起始日期 ','b606','datepicker'],
        'yue'                   =>['yue','月 ','b607'],
        'tian'                  =>['tian','天 ','b608'],
        'daoqiriqi'             =>['daoqiriqi','到期日期 ','b609','datepicker'],
        'fuxiri'                =>['fuxiri','付息日 ','b610'],
        'fuxifangshi'           =>['fuxifangshi','付息方式 ','b611'],
        'yuexi'                 =>['yuexi','月息[%] ','b612'],
        'yingfuyuan'            =>['yingfuyuan','应付[元] ','b613'],
        'zongeyuan'             =>['zongeyuan','总额[元] ','b614'],
        'kehujingli'            =>['kehujingli','客户经理 ','b615'],
        'ticheng'               =>['ticheng','提成[%] ','b616'],
        'tichengyuan'           =>['tichengyuan','提成[元] ','b617'],
        'tichengzongeyuan'      =>['tichengzongeyuan','提成总额[元] ','b618'],
        'fangkuanrenyinhang'    =>['fangkuanrenyinhang','放款人[银行] ','b619','select'],
        'rongzihetongbianhao'   =>['rongzihetongbianhao','融资合同编号 ','b620'],
        'jiekuanren'            =>['jiekuanren','借款人 ','b621'],
        'jieqingqingkuang'      =>['jieqingqingkuang','结清情况 ','b622','select'],
        'beizhu'                =>['beizhu','备注 ','b623'],

    ];

    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');
        $this->_viewFk = new \Prj\Misc\ViewFK();
        $this->fieldsMap['fangkuanrenyinhang'][]=\Prj\Data\FKFangKuanRenMingCe::fangkuanrenyinhang();
        $this->fieldsMap['jieqingqingkuang'][]=FKTouZiXiangMuBiao::$logicFields['jieqingqingkuang'];
        $this->jieqingqingkuang = ['全部']+FKTouZiXiangMuBiao::$logicFields['jieqingqingkuang'];
        $this->_view->assign('jieqingqingkuang', $this->jieqingqingkuang);

        $model_touzikehumingce = FKTouZiKeHuMingCe::getCopy();
        $this->touziren = $model_touzikehumingce->db()->getPair($model_touzikehumingce->tbname(), 'id', 'xingming');
        $this->fieldsMap['touziren'][] = $this->touziren;

        foreach($this->fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKTouZiXiangMuBiao::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($this->fieldsMap), $where, 'sort createTime');

            foreach($records as $k => $v) {
                foreach($v as $key => $value){
                    $records[$k][$key] = FKTouZiXiangMuBiao::parseFieldToString($key, $value);
                }
            }
            $this->_viewFk->setPk('id',false)->setData($records);
            $excel = $this->_viewFk->toExcel($records);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum,false);
        $pager->init(-1, $pageid);

        $selectedJieqingqingkuang = $this->_request->get('selectedJieqingqingkuang');
        $touzihetongbianhao = $this->_request->get('touzihetongbianhao');
        $touziren = $this->_request->get('touziren');
        $ymdFrom = $this->_request->get('ymdFrom');
        $ymdTo = $this->_request->get('ymdTo');


        $this->_view->assign('touzihetongbianhao', $touzihetongbianhao);
        $this->_view->assign('selectedJieqingqingkuang', $selectedJieqingqingkuang);
        $this->_view->assign('touziren', $touziren);
        $this->_view->assign('ymdFrom', $ymdFrom);
        $this->_view->assign('ymdTo', $ymdTo);

        $where = [];
        if(!empty($ymdFrom)) {
            if(!\Rpt\Funcs::check_date($ymdFrom)){
                return $this->returnError('起始日期格式错误, 格式如:2016-08-08');
            }
        }

        if(!empty($ymdTo)) {
            if (!\Rpt\Funcs::check_date($ymdTo)){
                return $this->returnError('结束日期格式错误, 格式如:2016-08-08');
            }
        }

        if(!empty($ymdFrom) && !empty($ymdTo)) {
            if(date("Ymd", strtotime($ymdFrom)) > date('Ymd', strtotime($ymdTo))) {
                return $this->returnError('起始日期应该小于结束日期');
            }
        }

        $ymdFrom  && $where['qishiriqi]']=date('Ymd', strtotime($ymdFrom));
        $ymdTo && $where['qishiriqi[']=date('Ymd', strtotime($ymdTo));
        $selectedJieqingqingkuang && $where['jieqingqingkuang']=$selectedJieqingqingkuang;
        $touzihetongbianhao && $where['touzihetongbianhao']=$touzihetongbianhao;

        if(!empty($touziren)){
            $model_touziren = FKTouZiKeHuMingCe::getCopy();
            $ids = $model_touziren->db()->getCol($model_touziren->tbname(), 'id', ['xingming*'=>'%'.$touziren.'%']);
            if(!empty($ids)){
                $where['touziren'] = $ids;
                $records = FKTouZiXiangMuBiao::paged($pager, $where, 'sort createTime');
            }else {
                $records = [];
            }
        }else {
            $records = FKTouZiXiangMuBiao::paged($pager, $where, 'sort createTime');
        }
        foreach($records as $k => $v) {
            foreach($v as $key => $value){
                $records[$k][$key] = FKTouZiXiangMuBiao::parseFieldToString($key, $value);
            }
        }
        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'del'));

        $this->_viewFk->addTab('subtab_fangkuanrenmingce', '放款人名册', \Sooh\Base\Tools::uri([], 'index', 'fangkuanrenmingce'), 'selectedFangkuanrenyinhang', 'fangkuanrenyinhang');
        $this->_viewFk->addTab('subtab_touzidangan', '投资档案', \Sooh\Base\Tools::uri([], 'index', 'touzidangan'), 'touzihetongbianhao', 'touzihetongbianhao');
        $this->_viewFk->addTab('subtab_投资客户名册', '投资客户名册', \Sooh\Base\Tools::uri([] ,'index','touzikehumingce'), 'id', 'touziren');
        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('where', urlencode(json_encode($where)));
    }


    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        foreach($record as $filename => $value) {
            try {
                $record[$filename] = $this->checkField($filename, $value, $this->fieldsMap);
            }catch(\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
        }
        $record['updateTime']=time();

        // 新增记录
        if(empty($id)){
            $model = FKTouZiXiangMuBiao::getCopy();
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
            $model = FKTouZiXiangMuBiao::getCopy($id);
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
        $model=FKTouZiXiangMuBiao::getCopy($id);
        $model->load();
        if($model->exists()){
            $model->delete();
            return $this->returnOk('删除成功');
        }else {
            return $this->returnError('要删除的记录不存在');
        }
    }

    protected function checkField ($fieldname, $value, $fieldsMap) {
        $value = trim($value);
        if(in_array($fieldname, FKTouZiXiangMuBiao::$requeredFields)){
            if(empty($value)){
                throw new \ErrorException($fieldsMap[$fieldname][1].' 不能必填');
            }
        }elseif (in_array($fieldname, FKTouZiXiangMuBiao::$formatPercentageType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(empty($value)){
                $value+=0;
            }
            if(!is_numeric($value)) {
                throw new \ErrorException($fieldsMap[$fieldname][1].' 需要填数字');
            }else {
                $value *= 100;
                $value +=0;
                if(sizeof(explode('.', $value))>1){
                    throw new \ErrorException($fieldsMap[$fieldname][1].' 最多两位小数');
                }
            }
        }elseif(array_key_exists($fieldname, FKTouZiXiangMuBiao::$formatMoneyType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(!empty($value)){
                if(!is_numeric($value)){
                    throw new \ErrorException($fieldsMap[$fieldname][1].' 需是数字');
                }else{
                    $value = ($value-0)*FKTouZiXiangMuBiao::$formatMoneyType[$fieldname]*100;
                    if(sizeof(explode('.', $value))>1){
                        throw new \ErrorException($fieldsMap[$fieldname][1].' 金额最多精确到分');
                    }
                }
            }else {
                $value = 0;
            }
        }elseif(in_array($fieldname, FKTouZiXiangMuBiao::$formatIntType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
//error_log('#############'.$fieldname.' '.$value);
            if(empty($value)) $value =0;
            if(is_numeric($value)){
                $value +=0;
            }
            if(!is_int($value))
                throw new \ErrorException($fieldsMap[$fieldname][1].' 需是整数');
        }elseif(array_key_exists($fieldname, FKTouZiXiangMuBiao::$formatDateType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(!empty($value)){
                if(!\Rpt\Funcs::check_date($value)){
                    throw new \ErrorException($fieldsMap[$fieldname][1].' 日期格式不正确, 格式如:2016-08-08');
                }
                if(is_string(FKTouZiXiangMuBiao::$formatDateType[$fieldname])){
                    $value = strtotime($value);
                }elseif(is_array(FKTouZiXiangMuBiao::$formatDateType[$fieldname])){
                    $value = date(FKTouZiXiangMuBiao::$formatDateType[$fieldname][0], strtotime($value));
                }
            }else{
                $value = 0;
            }
        }
        return $value;
    }
    
    /**
     * 导入功能开发
     **/
    
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
            unset($rem[0]);
            foreach ($rem as $v){
                $arr=preg_split("/[\t]/",$v);
    
                $arr[5]=\Prj\Misc\FengKongImport::checktime($arr[5]);
                $arr[8]=\Prj\Misc\FengKongImport::checktime($arr[8]);

                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],FKTouZiKeHuMingCe::getFieldForEnum('xingming'));
                $arr[18]=\Prj\Misc\FengKongImport::transvk($arr[18],\Prj\Data\FKFangKuanRenMingCe::fangkuanrenyinhang());
                $arr[21]=\Prj\Misc\FengKongImport::transvk($arr[21],FKTouZiXiangMuBiao::$logicFields['jieqingqingkuang']);
               
                $result[]=[
                    'touzihetongbianhao'=>$arr[0],
                    'touziren'=>$arr[1],
                    'yinhangzhanghao'=>$arr[2],
                    'kaihuxingxinxi'=>$arr[3],
                    'touziewanyuan'=>$arr[4],
                    'qishiriqi'=>$arr[5],
                    'yue'=>$arr[6],
                    'tian'=>$arr[7],
                    'daoqiriqi'=>$arr[8],
                    'fuxiri'=>$arr[9],
                    'fuxifangshi'=>$arr[10],
                    'yuexi'=>$arr[11],
                    'yingfuyuan'=>$arr[12],
                    'zongeyuan'=>$arr[13],
                    'kehujingli'=>$arr[14],
                    'ticheng'=>$arr[15],
                    'tichengyuan'=>$arr[16],
                    'tichengzongeyuan'=>$arr[17],
                    'fangkuanrenyinhang'=>$arr[18],
                    'rongzihetongbianhao'=>$arr[19],
                    'jiekuanren'=>$arr[20],
                    'jieqingqingkuang'=>$arr[21],
                    'beizhu'=>$arr[22],
                ];
    
            }
 
            foreach ($result as $data){
                foreach($data as $filename => $value) {
                    try {
                        $record[$filename] = $this->checkField($filename, $value, $this->fieldsMap);
                    }catch(\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    }
                    $record['updateTime']=time();
                }
                $records[]=$record;
            }
         
                foreach ($records as $k=>$value){
                    
                    $model = FKTouZiXiangMuBiao::getCopy('');
                    $tb = $model->tbname();
                    $db = $model->db();
                    $tmp=FKTouZiXiangMuBiao::getFieldForEnum('touzihetongbianhao');
                    if(in_array($value['touzihetongbianhao'], $tmp)){
                        return $this->returnError('投资合同编号禁止重复');
                    }
                    $value['createTime']=time();
                    $value['iRecordVerID'] = 1;
                 
                    try{
                       $id = $db->addRecord($tb, $value);
                       $this->_view->assign('_id', $id);
                    }catch (\Exception $e){
                        return $this->returnError('导入失败');
                    }
                }
             
            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('导入成功');
            return;
        }else{
    
        }
    }
}