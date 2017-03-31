<?php
/**
 * @author wu.peng
 * @param date 2016/10/17 9:30
 * @param 投资客户名册
 ***/
use \Prj\Data\FKTouZiKeHuMingCe as FKTouZiKeHuMingCeModel;
use \Prj\Misc\FengKongEump;
use Sooh\Base\Form\Item as form_def;
class TouzikehumingceController extends \Prj\ManagerCtrl
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
        $xingming = $this->_request->get('xingming');
        $this->_view->assign('xingming', $xingming);
        !empty($xingming) && $where['xingming']=$xingming;
        $id = $this->_request->get('id');
        !empty($id) && $where['id']=$id;
        }
        
        if(!$isDownloadExcel){
            $data = FKTouZiKeHuMingCeModel::paged($pager, $where);
            
            $temp = [];
            foreach ($data as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $temp[$k][$key] = FKTouZiKeHuMingCeModel::parseFieldToString($key, $var);
                    }
                }
            }
        }else{
            $wheretrue=[];
            $records = FKTouZiKeHuMingCeModel::loopFindRecords($wheretrue);
            
            $rs = [];
            foreach ($records as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $rs[$k][$key] = FKTouZiKeHuMingCeModel::parseFieldToString($key, $var);
                    }
                }
            }
        }
       
        
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')->setData($temp)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'delete'))
             ->addRow('xingming','姓名','m201')
             ->addRow('zhengjianhaoma','证件号码','m202')
             ->addRow('lianxidianhua','联系电话 ','m203')
             ->addRow('yinhangzhanghao','银行账号 ','m204','text', [], 400)
             ->addRow('kaihuxingxinxi','开户行信息 ','m205','text', [], 200)
             ->addRow('jiatingzhuzhi','家庭住址 ','m206');
        
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
        
        if($addFlag) {
            try {
                $records = $this->formatField($data);
                $all=FKTouZiKeHuMingCeModel::getFieldForEnum('xingming');
                if(in_array($records['xingming'], $all)){
                    return $this->returnError('投资客户姓名禁止重复');
                }
                $tmpModel = FKTouZiKeHuMingCeModel::getCopy('');
                $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            }catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    return $this->returnError($e->getMessage());
                }else{
                    return $this->returnError('新增失败');
                }
               
            }
            $this->_view->assign('_id', $dbRet);
            return $this->returnOK('新增成功');
        }else{
            
            $model = FKTouZiKeHuMingCeModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                return $this->returnError('记录不存在或者已经被删除');
            }
            
            try {
                $this->formatAndSetField($model, $data);
                $model->update();
                $this->_view->assign('_id', $key);
                return $this->returnOK('更新成功');
            }catch (\Exception $e){
                if ($e->getCode() >= 90000) {
                    return $this->returnError($e->getMessage());
                }else{
                    return $this->returnError('更新失败');
                }
              
            }
        }

    }
    
         public function deleteAction()
            {
                $key = $this->getRequest()->get('_id');
        
                $model = FKTouZiKeHuMingCeModel::getCopy($key);
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
          
              var_log($data, 'test');
          
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
                      case 'xingming':
                     if (empty($v)) {
                        throw new \Exception('投资用户姓名不能为空', 90003);
                     }else {
                         $ret[$k]=$v;
                     }
                      break;
                      default:
                          if (($tmpV = FKTouZiKeHuMingCeModel::parseStringToField($k, $v)) === false) {
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
                          'xingming'=>$arr[0],
                          'zhengjianhaoma'=>$arr[1],
                          'lianxidianhua'=>$arr[2],
                          'yinhangzhanghao'=>$arr[3],
                          'kaihuxingxinxi'=>$arr[4],
                          'jiatingzhuzhi'=>$arr[5],
                      ];
          
                  }
          
                  foreach ($result as $data){
                      try{
                        $records = $this->formatField($data);
                        $all=FKTouZiKeHuMingCeModel::getFieldForEnum('xingming');
                        if(in_array($records['xingming'], $all)){
                            return $this->returnError('投资客户姓名禁止重复');
                        }
                        $tmpModel = FKTouZiKeHuMingCeModel::getCopy('');
                        $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                        $this->_view->assign('_id', $dbRet);
                      }catch (\Exception $e){
                       if ($e->getCode() >= 90000) {
                            return $this->returnError($e->getMessage());
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

