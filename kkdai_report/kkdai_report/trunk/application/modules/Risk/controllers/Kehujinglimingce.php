<?php
/**
 * 客户经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/14 0014
 * Time: 下午 2:04
 */
use \Prj\Data\FKKeHuJingLiMingCe as FKKeHuJingLiMingCe;
use Sooh\Base\Form\Item as form_def;
class KehujinglimingceController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();
        $this->_viewFk = new \Prj\Misc\ViewFK();

    }

    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];

    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');

        $fieldsMap = [
            'id'                => ['id','id','m0'],
            'xingming'          => ['xingming','姓名','m301'],
            'jibie'             => ['jibie','级别','m302','select',FKKeHuJingLiMingCe::$logicFields['jibie']],
            'shangjijingli'     => ['shangjijingli','上级经理','m303'],
            'shangjizongjian'   => ['shangjizongjian','上级总监','m304'],
            'zaizhiqingkuang'   => ['zaizhiqingkuang','在职情况','m305','select',FKKeHuJingLiMingCe::$logicFields['zaizhiqingkuang']],
        ];

        foreach($fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }

        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKKeHuJingLiMingCe::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($fieldsMap), $where, 'sort createTime');

            foreach($records as $k => $v) {
                foreach($v as $key => $value){
                    $records[$k][$key] = FKKeHuJingLiMingCe::parseFieldToString($key, $value);
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
        $xingming = $this->_request->get('xingming');
        $id = $this->_request->get('id');

        $where = [];
        if(!empty($xingming)){
            $where=['xingming*'=>'%'.$xingming.'%'];
        }
        !empty($id) && $where['id'] = $id;
        $records = \Prj\Data\FKKeHuJingLiMingCe::paged($pager, $where, 'sort createTime');
        foreach($records as $k => $v) {
            foreach($v as $key => $value){
                $records[$k][$key] = FKKeHuJingLiMingCe::parseFieldToString($key, $value);
            }
        }

        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri([], 'update'), \Sooh\Base\Tools::uri([], 'del'));

        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('xingming', $xingming);
        $this->_view->assign('where', urlencode(json_encode($where)));
    }

    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        if(empty($record['xingming'])){
            return $this->returnError('姓名不能为空');
        }


        // 新增记录
        if(empty($id)){
            $model = FKKeHuJingLiMingCe::getCopy();
            $tb = $model->tbname();
            $db = $model->db();
            $record['createTime']=time();
            $record['updateTime']=time();
            $record['iRecordVerID'] = 1;
            try {
                $id = $db->addRecord($tb, $record);
                $this->_view->assign('_id', $id);
                return $this->returnOK('添加成功');
            }catch (\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('姓名已经存在');
                }else {
                    return $this->returnError('添加失败');
                }
            }

        }else{
            // 更新记录
            $model = FKKeHuJingLiMingCe::getCopy($id);
            $model->load();
            if(!$model->exists()){
                return $this->returnError('未查找到要修改记录');
            }
            $model->setField('updateTime', time());
            foreach($record as $k => $v) {
                $model->setField($k, $v);
            }

            try {
                $model->update();
                return $this->returnOK('更新成功');
            }catch (\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('姓名已经存在');
                }else {
                    return $this->returnError('更新失败');
                }
            }
        }
    }

    public function delAction () {
        $id = $this->_request->get('_id');
        $model=FKKeHuJingLiMingCe::getCopy($id);
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
    protected $title=['xingming','jibie','shangjijingli','shangjizongjian',
                      'zaizhiqingkuang'];
    
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
                  
                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],FKKeHuJingLiMingCe::$logicFields['jibie']);
                $arr[4]=\Prj\Misc\FengKongImport::transvk($arr[4],FKKeHuJingLiMingCe::$logicFields['zaizhiqingkuang']);
    
                $result[]=[
                    'xingming'=>$arr[0],
                    'jibie'=>$arr[1],
                    'shangjijingli'=>$arr[2],
                    'shangjizongjian'=>$arr[3],
                    'zaizhiqingkuang'=>$arr[4],
                ];
    
            }
            
            foreach ($result as $data){
                $data=$this->formatFields($data);
                $model = FKKeHuJingLiMingCe::getCopy('');
                $tb = $model->tbname();
                $db = $model->db();
                $data['createTime']=time();
                $data['updateTime']=time();
                $data['iRecordVerID'] = 1;
                
                try{
                   $id = $db->addRecord($tb, $data);
                   $this->_view->assign('_id', $id);
                }catch (\Exception $e){
//                     if ($e->getCode() >= 90000) {
//                         $this->returnError($e->getMessage());
//                         return 0;
//                     }else{
//                         $this->returnError('导入失败');
//                         return 0;
//                     }
                  $aa=$e->getCode();
                  var_log($e,'getcode################');
                  if($e->getCode()>=90000){
                      var_log('11111111111111111');
                  }else{
                      var_log('222222222222222222');
                  }
                  
//                 if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
//                     return $this->returnError('姓名已经存在');
//                 }
//                 elseif($e->getCode()>=90000){
//                     return $this->returnError($e->getMessage());
//                 }
//                 else {
//                     return $this->returnError('导入失败');
//                 }
                }
            }
             
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
                case 'jibie':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zaizhiqingkuang':
                    var_log($v,'##############');
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        var_log($name,'name##############');
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKKeHuJingLiMingCe::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}