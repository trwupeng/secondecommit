<?php
/**
 * 流程单
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/11 0011
 * Time: 上午 9:23
 */
use \Prj\Data\FKLiuChengDan as FKLiuChengDan;
use Sooh\Base\Form\Item as form_def;
class LiuchengdanController extends \Prj\ManagerCtrl {

    public function init() {
        parent::init();
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $this->_viewFk = new \Prj\Misc\ViewFK();
    }
    protected $db_rpt;
    protected $tb = 'db_kkrpt.fk_liuchengdan';
    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];

    protected $hideField = 'id';
    protected $fieldsMap = [
        'id'                    =>['id', 'id', 'd100'],
        'hetongbianhao'         =>['hetongbianhao','合同编号','d101' ],
        'jiekuanren'            =>['jiekuanren','借款人','d102'],
        'pipeizijinqingkuang'   =>['pipeizijinqingkuang','匹配资金情况','d103','select'],
        'fangkuanshijian'       =>['fangkuanshijian','放款时间','d104','datepicker'],
        'xianxiayifangkuan'     =>['xianxiayifangkuan','线下已放款','d105','checkbox',['1'=>'']],
        'fangkuanshijian8'      =>['fangkuanshijian8','放款时间','d106','datepicker'],
        'xianshangyifangkuan'   =>['xianshangyifangkuan','线上已放款','d107','checkbox',['1'=>'']],
        'fangkuanshijian10'     =>['fangkuanshijian10','放款时间','d108','datepicker'],
        'jibenxinxiyilu'        =>['jibenxinxiyilu','基本信息已录','d109','checkbox',['1'=>'']],
        'chulishijian'          =>['chulishijian','处理时间','d110','datepicker'],
        'xiangxixinxiyilu'      =>['xiangxixinxiyilu','详细信息已录','d111','checkbox',['1'=>'']],
        'chulishijian14'        =>['chulishijian14','处理时间','d112','datepicker'],
        'tijiangyihesuan'       =>['tijiangyihesuan','提奖已核算','d113','checkbox',['1'=>'']],
        'chulishijian16'        =>['chulishijian16','处理时间','d114','datepicker'],
        'xiangmuyiguidang'      =>['xiangmuyiguidang','项目已归档','d115','checkbox',['1'=>'']],
        'chulishijian18'        =>['chulishijian18','处理时间','d116','datepicker'],
        'yishenhe'              =>['yishenhe','已审核','d117','checkbox',['1'=>'']],
        'chulishijian20'        =>['chulishijian20','处理时间','d118','datepicker'],

    ];

    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');
        $pageid = $this->_request->get('pageId', 1) - 0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum, false);
        $hetongbianhao = $this->_request->get('hetongbianhao');

        $this->fieldsMap['pipeizijinqingkuang'][] = FKLiuChengDan::$logicFields['pipeizijinqingkuang'];
        $where = [];
        !empty($hetongbianhao) && $where = ['hetongbianhao'=>$hetongbianhao];
        $records = FKLiuChengDan::paged($pager, $where, 'sort id');
        foreach($records as $k => $v) {
            foreach($v as $key => $value) {
                $records[$k][$key] = FKLiuChengDan::parseFieldToString($key, $value);
            }
        }
        $this->_viewFk->setPk('id', false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri([], 'update'), \Sooh\Base\Tools::uri([], 'del'));
        foreach($this->fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }

        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('pagesize', $pagesize);
        $this->_view->assign('pageid', $pageid);
        $this->_view->assign('where', urlencode(json_encode($where)));
        $this->_view->assign('hetongbianhao', $hetongbianhao);
        $this->_view->assign('_type',$this->_request->get('_type'));
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $records = $this->db_rpt->getRecords ($this->tb, array_keys($this->fieldsMap), $where, 'sort id');
            foreach($this->fieldsMap as $k => $v) {
                if($k == $this->hideField){
                    continue;
                }
                $headers[]=$v[1];
            }
            foreach($records as $k => $r) {
                unset($records[$k][$this->hideField]);
                foreach(FKLiuChengDan::$formatDateType as  $f => $v) {
                    if($r[$f]>0){
                        $records[$k][$f] = date('Y-m-d', strtotime($r[$f]));
                    }else {
                        $records[$k][$f] = '';
                    }
                }
                foreach(FKLiuChengDan::$logicFields as $f => $v) {
                    $records[$k][$f] = $v[$r[$f]];
                }
            }
            return $this->downExcel($records, $headers);
        }
    }

    public function updateAction() {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        
        foreach($record as $filename => $value) {
            try {
                $record[$filename] = $this->checkField($filename, $value, $this->fieldsMap);
            }catch(\ErrorException $e){
                return $this->returnError($e->getMessage());
            }
        }
        // 新增记录
        if(empty($id)) {
            try {
                $id = $this->db_rpt->addRecord($this->tb, $record);
                $this->_view->assign('_id', $id);
            }catch (\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('合同编号重复');
                }
            }
        }else{
            // 更新记录
            $rs = $this->db_rpt->getRecordCount($this->tb, ['id'=>$id]);
            if(!$rs){
                return $this->returnError('未查找到要修改记录');
            }
            try {
                $this->db_rpt->updRecords($this->tb, $record, ['id'=>$id]);
            }catch(\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e,\Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('合同编号重复');
                }
            }
        }
        return $this->returnOK();
    }

    public function delAction() {
        $key = $this->_request->get('_id');
        if(empty($key)) {
            return $this->returnError('参数错误');
        }else{
            $rs = $this->db_rpt->delRecords($this->tb, ['id'=>$key]);
            if(!$rs) {
                 return $this->returnError('删除失败');
            }
        }
        return $this->returnOK();
    }

    protected function checkField ($fieldname, $value, $fieldsMap) {
        $value = trim($value);
        if(in_array($fieldname, FKLiuChengDan::$requeredFields)){
            if(empty($value)){
                throw new \ErrorException($fieldsMap[$fieldname][1].' 不能必填');
            }
        }elseif (in_array($fieldname, FKLiuChengDan::$formatPercentageType)){
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
        }elseif(array_key_exists($fieldname, FKLiuChengDan::$formatMoneyType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(!empty($value)){
                if(!is_numeric($value)){
                    throw new \ErrorException($fieldsMap[$fieldname][1].' 需是数字');
                }else{
                    $value = ($value-0)*FKLiuChengDan::$formatMoneyType[$fieldname]*100;
                    if(sizeof(explode('.', $value))>1){
                        throw new \ErrorException($fieldsMap[$fieldname][1].' 金额最多精确到分');
                    }
                }
            }else {
                $value = 0;
            }
        }elseif(in_array($fieldname, FKLiuChengDan::$formatIntType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(empty($value)) $value =0;
            if(is_numeric($value)){
                $value +=0;
            }
            if(!is_int($value))
                throw new \ErrorException($fieldsMap[$fieldname][1].' 需是整数');
        }elseif(array_key_exists($fieldname, FKLiuChengDan::$formatDateType)){
            if($fieldname == 'yue'){
                error_log('###### 1 #######'.$fieldname.' '.$value);
            }
            if(!empty($value)){
                if(!\Rpt\Funcs::check_date($value)){
                    throw new \ErrorException($fieldsMap[$fieldname][1].' 日期格式不正确, 格式如:2016-08-08');
                }
                if(is_string(FKLiuChengDan::$formatDateType[$fieldname])){
                    $value = strtotime($value);
                }elseif(is_array(FKLiuChengDan::$formatDateType[$fieldname])){
                    $value = date(FKLiuChengDan::$formatDateType[$fieldname][0], strtotime($value));
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
    
    protected $enumhuifangfangshi = [
        1 => '上门面谈',
        2 => '公司面谈',
        3 => '电话',
    ];
    
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
 
                $arr[3]=\Prj\Misc\FengKongImport::checktime($arr[3]);
                $arr[5]=\Prj\Misc\FengKongImport::checktime($arr[5]);
                $arr[7]=\Prj\Misc\FengKongImport::checktime($arr[7]);
                $arr[9]=\Prj\Misc\FengKongImport::checktime($arr[9]);
                $arr[11]=\Prj\Misc\FengKongImport::checktime($arr[11]);
                $arr[13]=\Prj\Misc\FengKongImport::checktime($arr[13]);
                $arr[15]=\Prj\Misc\FengKongImport::checktime($arr[15]);
                $arr[17]=\Prj\Misc\FengKongImport::checktime($arr[17]);
                
                $pipei=FKLiuChengDan::$logicFields;
                
                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],$pipei['pipeizijinqingkuang']);
                $arr[4]=\Prj\Misc\FengKongImport::transvk($arr[4],$pipei['xianxiayifangkuan']);
                $arr[6]=\Prj\Misc\FengKongImport::transvk($arr[6],$pipei['xianshangyifangkuan']);
                $arr[8]=\Prj\Misc\FengKongImport::transvk($arr[8],$pipei['jibenxinxiyilu']);
                $arr[10]=\Prj\Misc\FengKongImport::transvk($arr[10],$pipei['xiangxixinxiyilu']);
                $arr[12]=\Prj\Misc\FengKongImport::transvk($arr[12],$pipei['tijiangyihesuan']);
                $arr[14]=\Prj\Misc\FengKongImport::transvk($arr[14],$pipei['xiangmuyiguidang']);
                $arr[16]=\Prj\Misc\FengKongImport::transvk($arr[16],$pipei['yishenhe']);
               
                $result[]=[
                    'hetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'pipeizijinqingkuang'=>$arr[2],
                    'fangkuanshijian'=>$arr[3],
                    'xianxiayifangkuan'=>$arr[4],
                    'fangkuanshijian8'=>$arr[5],
                    'xianshangyifangkuan'=>$arr[6],
                    'fangkuanshijian10'=>$arr[7],
                    'jibenxinxiyilu'=>$arr[8],
                    'chulishijian'=>$arr[9],
                    'xiangxixinxiyilu'=>$arr[10],
                    'chulishijian14'=>$arr[11],
                    'tijiangyihesuan'=>$arr[12],
                    'chulishijian16'=>$arr[13],
                    'xiangmuyiguidang'=>$arr[14],
                    'chulishijian18'=>$arr[15],
                    'yishenhe'=>$arr[16],
                    'chulishijian20'=>$arr[17],
                ];
    
            }
            
            foreach ($result as $data){
               
                foreach ($data as $filename=>$value){
                    try {

                        $record[$filename] = $this->checkField($filename, $value, $this->fieldsMap);

                    }catch(\ErrorException $e){
                        return $this->returnError($e->getMessage());
                    } 
                } 
                $records[]=$record;
            }
             
            
            foreach ($records as $value){
                try {
                  
                    $id = $this->db_rpt->addRecord($this->tb, $value);
                    $this->_view->assign('_id', $id);
                }catch (\ErrorException $e) {
                    if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                        return $this->returnError('合同编号重复');
                    }
                }
            }
 
            $this->closeAndReloadPage($this->tabname('index'));
            $this->returnOK('导入成功');
            return;
        }else{
    
        }
    }  
}