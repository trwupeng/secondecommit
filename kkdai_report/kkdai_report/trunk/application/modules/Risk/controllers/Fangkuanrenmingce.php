<?php
/**
 * 风控经理名册
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/10/17 0014
 * Time: 下午 11:34
 */
use \Prj\Data\FKFangKuanRenMingCe as FKFangKuanRenMingCe;
use Sooh\Base\Form\Item as form_def;
class FangkuanrenmingceController extends \Prj\ManagerCtrl {

    public function init () {
        parent::init();
        $this->_viewFk = new \Prj\Misc\ViewFK();
    }

    protected $_viewFk;
    protected $pageSizeEnum = [10, 20, 30];
    protected $fangkuanrenyinhang;

    public function indexAction () {
        $isDownload = $this->_request->get('__EXCEL__');

        $fieldsMap = [
            'id'                    => ['id','id','m0'],
            'fangkuanren'           => ['fangkuanren','放款人','m501'],
            'fangkuanrenyinhang'    => ['fangkuanrenyinhang', '放款人[银行]','m502'],
            'yinhangzhanghao'       => ['yinhangzhanghao','银行帐号','m503'],
            'kaihuxingxinxi'        => ['kaihuxingxinxi','开户信息','m504'],
            'xianshangnicheng'      => ['xianshangnicheng','线上昵称','m505'],
        ];
        foreach($fieldsMap as $v) {
            call_user_func_array([$this->_viewFk, 'addRow'], $v);
        }
        if($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
            $model = FKFangKuanRenMingCe::getCopy();
            $records = $model->db()->getRecords($model->tbname(), array_keys($fieldsMap), $where, 'sort createTime');
            $this->_viewFk->setPk('id',false)->setData($records);
            $this->_viewFk->setPk('id',false)->setData($records);
            $excel = $this->_viewFk->toExcel($records);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        $pageid = $this->_request->get('pageId',1)-0;
        $pagesize = $this->_request->get('pageSize', current($this->pageSizeEnum)) - 0;
        $pager = new \Sooh\DB\Pager($pagesize, $this->pageSizeEnum,false);
        $pager->init(-1, $pageid);
        $xingming = $this->_request->get('fangkuanren');
        $fangkuanrenyinhang = $this->_request->get('selectedFangkuanrenyinhang');
        $this->_view->assign('selectedFangkuanrenyinhang', $fangkuanrenyinhang);
        $this->fangkuanrenyinhang = ['全部']+FKFangKuanRenMingCe::fangkuanrenyinhang();
        $this->_view->assign('fangkuanrenyinhang', $this->fangkuanrenyinhang);
        $where = [];
        if(!empty($fangkuanrenyinhang)){
            $where['id']=$fangkuanrenyinhang;
        }
        if(!empty($xingming)){
            $where['fangkuanren*'] = '%'.$xingming.'%';
        }
        $records = FKFangKuanRenMingCe::paged($pager, $where, 'sort createTime');
        $this->_viewFk->setPk('id',false)->setData($records)->setPager($pager)->setAction(\Sooh\Base\Tools::uri([], 'update'), \Sooh\Base\Tools::uri([], 'del'));

        $this->_view->assign('view', $this->_viewFk);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('fangkuanren', $xingming);
        $this->_view->assign('where', urlencode(json_encode($where)));
    }


    public function updateAction () {
        $id = $this->_request->get('id');
        $record = $this->_request->get('values')[0];
        if(empty($record['fangkuanren'])){
            return $this->returnError('放款人不能为空');
        }

        $record['updateTime']=time();
        // 新增记录
        if(empty($id)){
            $model = FKFangKuanRenMingCe::getCopy();
            $tb = $model->tbname();
            $db = $model->db();
            $record['createTime']=time();
            $record['iRecordVerID'] = 1;

            try {
                $id = $db->addRecord($tb, $record);
                $this->_view->assign('_id', $id);
            }catch(\ErrorException $e) {
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('已经存在此放款人[银行]');
                }else{
                    return $this->returnError('添加失败');

                }
            }
            return $this->returnOK('添加成功');

        }else{
            // 更新记录
            $model = FKFangKuanRenMingCe::getCopy($id);
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
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    return $this->returnError('放款人[银行]已经存在');
                }else {
                    return $this->returnError('更新失败');
                }
            }
        }
    }

    public function delAction () {
        $id = $this->_request->get('_id');
        $model=FKFangKuanRenMingCe::getCopy($id);
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

                $result[]=[
                    'fangkuanren'=>$arr[0],
                    'fangkuanrenyinhang'=>$arr[1],
                    'yinhangzhanghao'=>$arr[2],
                    'kaihuxingxinxi'=>$arr[3],
                    'xianshangnicheng'=>$arr[4],
                ];
    
            }
          
            foreach ($result as $data){
             
                $data['updateTime']=time();
    
                $model = FKFangKuanRenMingCe::getCopy('');
                $tb = $model->tbname();
                $db = $model->db();
                $data['createTime']=time();
                $data['iRecordVerID'] = 1;
    
                try{
                    $id = $db->addRecord($tb, $data);
                    $this->_view->assign('_id', $id);
                }catch (\Exception $e){
                   if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                        return $this->returnError('已经存在此放款人[银行]');
                   }else{
                        return $this->returnError('导入失败');
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