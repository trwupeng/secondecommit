<?php
/**
 * @author wu.peng
 * @param date 2016/10/14 11:00
 * @param 融回访记录
 ***/
use \Prj\Data\FKRrongHuiFangJiLu as FKRrongHuiFangJiLuModel;
use \Prj\Misc\FengKongEump;
use Sooh\Base\Form\Item as form_def;
class RonghuifangjiluController extends \Prj\ManagerCtrl
{

    public function indexAction(){
        
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);
        
        $where = [];
        $where = $this->_request->get('where');
        if (!empty($where)) {
            $where = json_decode(urldecode($where), true);
        } else {
        $kehubianhao = $this->_request->get('kehubianhao');
        !empty($kehubianhao) && $where['kehubianhao']=$kehubianhao;
        $bianhao = $this->_request->get('bianhao');
        !empty($bianhao) && $where['kehubianhao']=$bianhao;
        }
        
        if(!$isDownloadExcel){
            $data = FKRrongHuiFangJiLuModel::paged($pager, $where);
            
            $temp = [];
            foreach ($data as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $temp[$k][$key] = FKRrongHuiFangJiLuModel::parseFieldToString($key, $var);
                    }
                }
            }
        }else{
            $wheretrue=[];
            $records = \Prj\Data\FKRrongHuiFangJiLu::loopFindRecords($wheretrue);
            
            $rs = [];
            foreach ($records as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $rs[$k][$key] = FKRrongHuiFangJiLuModel::parseFieldToString($key, $var);
                    }
                }
            }
        }
       
      
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')->setData($temp)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'delete'))
              ->addRow('kehubianhao', '客户编号', 'b501')
              ->addRow('kehu', '客户', 'b502')
              ->addRow('huifangshijian', '回访时间 ', 'b503','datepicker')
              ->addRow('huifangfangshi', '回访方式', 'b504','select',FengKongEump::getInstance()->get('huifangfangshi'))
              ->addRow('huifangrenyuan', '回访人员', 'b505','select',\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming'))
              ->addRow('huifangqingkuang', '回访情况', 'b506','text', [], 400);
        
        if ($isDownloadExcel) {
            $excel = $view->toExcel($rs);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        
        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('where', $where);
       
 }
    
    public function updateAction() {
       
        $addFlag = false;
        $data = $this->getRequest()->get('values')[0];
        $key = $this->getRequest()->get('id');
        if (empty($key)) {
            $addFlag = true;
        }
        unset($data['id']);
        
        if (empty($data)) {
            return $this->returnError('没有更新的内容，更新失败');
        }
        
        if ($addFlag) {
            try{
                $records = $this->formatField($data);
                
                $tmpModel = FKRrongHuiFangJiLuModel::getCopy('');
                $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            }catch (\Exception $e){
                return $this->returnError('新增失败');
            }
            
            $this->_view->assign('_id', $dbRet);
            return $this->returnOK('新增成功');
        }else{
            
            $model = FKRrongHuiFangJiLuModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                return $this->returnError('记录不存在或者已经被删除');
            }
            
            try{
                $this->formatAndSetField($model, $data);
                $model->update();
                $this->_view->assign('_id', $key);
                return $this->returnOK('更新成功');
            }catch (\Exception $e){
                return $this->returnError('更新失败');
            }
        }

    }
    
       public function deleteAction()
            {
                $key = $this->getRequest()->get('_id');
        
                $model = FKRrongHuiFangJiLuModel::getCopy($key);
                $model->load();
                if ($model->exists()) {
                    $model->delete();
                    return $this->returnOk('删除成功');
                } else {
                    return $this->returnError('记录不存在或者已经被删除');
                }
            }
    
            /**
             * @param \Sooh\DB\Base\KVObj $model
             * @param $data
             * @throws Exception
             * @return bool
             */
            private function formatAndSetField($model, $data)
            {
                $data = $this->formatField($data);
            
              //  var_log($data, 'test');
            
                foreach ($data as $k => $v) {
                    $model->setField($k, $v);
                }
                return true;
            }
            
            private function formatField($data) {
                $ret = [];
                $tmpDaoQiRiQi = 0;
            
                foreach ($data as $k => $v) {
                    switch ($k) {
                        case 'huifangqingkuang':
                            if(empty($v)){
                                $v='经营变动情况、借款用途变动情况、资产负债变动情况、重点关注情况、其他情况描述';
                            }
                            $ret[$k]=$v;
                            break;
                    
                        default:
                            if (($tmpV = FKRrongHuiFangJiLuModel::parseStringToField($k, $v)) === false) {
                                throw new \Exception('表单值不合法', 90001);
                            }
                            $ret[$k] = $tmpV;
                    }
                }
            
                return $ret;
            }
            
            
            /**
             * 导入功能开发
             **/
            protected $location;
            protected $title=['kehubianhao','kehu','huifangshijian',
                               'huifangfangshi','huifangrenyuan','huifangqingkuang'];
            
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
            
                        $arr[2]=\Prj\Misc\FengKongImport::checktime($arr[2]);
                         
                        $huifangrenyuan=\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming');

                        $arr[4]=\Prj\Misc\FengKongImport::transvk($arr[4],$huifangrenyuan);
                        $arr[3]=\Prj\Misc\FengKongImport::transvk($arr[3],FengKongEump::getInstance()->get('huifangfangshi'));
            
                        $result[]=[
                            'kehubianhao'=>$arr[0],
                            'kehu'=>$arr[1],
                            'huifangshijian'=>$arr[2],
                            'huifangfangshi'=>$arr[3],
                            'huifangrenyuan'=>$arr[4],
                            'huifangqingkuang'=>$arr[5],
                        ];
            
                    }
                  
                    foreach ($result as $data){
                         try{
                            $records = $this->formatFields($data);
                            $tmpModel = FKRrongHuiFangJiLuModel::getCopy('');
                            $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                            $this->_view->assign('_id', $dbRet);
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
            
            public  function formatFields($data){
                $ret = [];
                $tmpDaoQiRiQi = 0;
                foreach ($data as $k => $v) {
                    switch ($k) {
                        case 'huifangshijian':
                            if(!empty($v)){
                                $datetime=explode('-',$v);
                                if(empty($datetime[2])){
                                    $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                    throw new \Exception($name.'日期格式不正确', 90003);
                                }else{
                                       $tmpDaoQiRiQi=$v ? strtotime($v) : 0;
                                    }
                            }
                            $ret[$k]=$tmpDaoQiRiQi;
                            break;
                        case 'huifangfangshi':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'huifangrenyuan':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        default:
                            if (($tmpV = FKRrongHuiFangJiLuModel::parseStringToField($k, $v)) === false) {
                                throw new \Exception('表单值不合法', 90001);
                            }
                            $ret[$k] = $tmpV;
                    }
                }
                return $ret;
            }
}

