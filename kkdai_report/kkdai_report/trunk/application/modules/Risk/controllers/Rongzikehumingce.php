<?php
/**
 * @author wu.peng
 * @param date 2016/10/14 11:00
 * @param 融资客户名册
 ***/
use \Prj\Data\FKRongZiKeHuMingCe as FKRongZiKeHuMingCeModel;
use \Prj\Misc\FengKongEump;
use Sooh\Base\Form\Item as form_def;

class RongzikehumingceController extends \Prj\ManagerCtrl
{
  
    public function indexAction(){
        
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);
        
        $where = [];
        if (!empty($where)) {
            $where = json_decode(urldecode($where), true);
        } else {
        $bianhao= $this->_request->get('bianhao');
        !empty($bianhao) && $where['bianhao']=$bianhao;
        }
        
        if(!$isDownloadExcel){
            $data = FKRongZiKeHuMingCeModel::paged($pager, $where);
            $temp = [];
            foreach ($data as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $temp[$k][$key] = FKRongZiKeHuMingCeModel::parseFieldToString($key, $var);
                    }
                }
            } 
        }else{
            $wheretrue=[];
            $records = FKRongZiKeHuMingCeModel::loopFindRecords($wheretrue);
            
            $rs = [];
            foreach ($records as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $rs[$k][$key] = FKRongZiKeHuMingCeModel::parseFieldToString($key, $var);
                    }
                }
            }
        }
       
        
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')->setData($temp)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'delete'))
        ->addRow('bianhao', '编号', 'm101')
        ->addRow('xingming', '姓名', 'm102')
        ->addRow('weihuren', '维护人', 'm103','select',\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming'))
        ->addRow('guishuren', '归属人', 'm104','select',\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'))
        ->addRow('yuanguishu', '原归属', 'm105')
        ->addRow('jieshaoren', '介绍人', 'm106')
        ->addRow('zaibaoqingkuang', '在保情况', 'm107','select',FengKongEump::getInstance()->get('zaibaoqingkuang'))
        ->addRow('jieqingriqi', '结清日期', 'm108','datepicker')
        
        ->addTab('tab1','融资项目表','/risk/rongzixiangmubiao/index','bianhao','bianhao')
        ->addTab('tab2','融人企信息','/risk/rongrenqixinxi/index','kehubianhao','bianhao')
        ->addTab('tab3','融房产信息','/risk/rongfangchanxinxi/index','kehubianhao','bianhao')
        ->addTab('tab4','融回访记录','/risk/ronghuifangjilu/index','bianhao','bianhao');
        
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
            try {
                
               // var_log($data,'records##########');
                
                $records = $this->formatField($data);
                //var_log($records,'records##########');
                $tmp=FKRongZiKeHuMingCeModel::getFieldForEnum('xingming');
                if(in_array($records['xingming'], $tmp)){
                    return $this->returnError('禁止姓名重复');
                }
                $tmpModel = FKRongZiKeHuMingCeModel::getCopy('');
                $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            }catch (\Exception $e) {
                    return $this->returnError('新增失败');
            }
                $this->_view->assign('_id', $dbRet);
                return $this->returnOK('新增成功');
      }else{
          
          $model = FKRongZiKeHuMingCeModel::getCopy($key);
          $model->load();
          if (!$model->exists()) {
              return $this->returnError('记录不存在或者已经被删除');
          } 
          
          try {
              $this->formatAndSetField($model, $data);
              $model->update();
              $this->_view->assign('_id', $key);
              return $this->returnOK('更新成功');
          } catch (\Exception $e) {
                    return $this->returnError('更新失败');
                }
      }

    }
     
    public function deleteAction()
    {
        $key = $this->getRequest()->get('_id');
        $model = FKRongZiKeHuMingCeModel::getCopy($key);
        $model->load();
        if ($model->exists()) {
            $model->delete();
            $this->returnOk('删除成功');
            return 0;
        } else {
           $this->returnError('记录不存在或者已经被删除');
           return 0;
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
    
       // var_log($data, 'test');

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
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKRongZiKeHuMingCeModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 20001);
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
    protected $title=['bianhao','xingming','weihuren','guishuren','yuanguishu',
                      'jieshaoren','zaibaoqingkuang','jieqingriqi'];

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
                
                $arr[7]=\Prj\Misc\FengKongImport::checktime($arr[7]);
             
                $weihuren=\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming');
                $guishuren=\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming');
                
                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],$weihuren);
                $arr[3]=\Prj\Misc\FengKongImport::transvk($arr[3],$guishuren);
                $arr[6]=\Prj\Misc\FengKongImport::transvk($arr[6],FengKongEump::getInstance()->get('zaibaoqingkuang'));
                var_log($arr,'records###########');
                $result[]=[
                    'bianhao'=>$arr[0],
                    'xingming'=>$arr[1],
                    'weihuren'=>$arr[2],
                    'guishuren'=>$arr[3],
                    'yuanguishu'=>$arr[4],
                    'jieshaoren'=>$arr[5],
                    'zaibaoqingkuang'=>$arr[6],
                    'jieqingriqi'=> $arr[7]
                ];
                
            }
            
            foreach ($result as $data){
                try {
                    $records = $this->formatFields($data);
                    $tmp=FKRongZiKeHuMingCeModel::getFieldForEnum('xingming');
                    if(in_array($records['xingming'], $tmp)){
                        return $this->returnError('禁止姓名重复');
                    }
                    $tmpModel = FKRongZiKeHuMingCeModel::getCopy('');
                    $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                    $this->_view->assign('_id', $dbRet);
                }catch (\Exception $e){
                    if ($e->getCode() >= 90000) {
                        $this->returnError($e->getMessage());
                        return 0;
                    }else{
                        $this->returnError('导入失败');
                        return 0;
                    }
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
                case 'jieqingriqi':
                    if(!empty($v)){
                        $datetime=explode('-',$v);
                        if(empty($datetime[2])){
                            $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                            throw new \Exception($name.'日期格式不正确', 90003);
                        }else{
                            $v=$v ? strtotime($v) : 0;
                        }
                    }
                    $ret[$k]=$v;
                    break;
                case 'weihuren':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'guishuren':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zaibaoqingkuang':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKRongZiKeHuMingCeModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}

