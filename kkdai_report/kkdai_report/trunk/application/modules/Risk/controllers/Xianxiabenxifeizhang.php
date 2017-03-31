<?php
/**
 * @author wu.peng
 * @param date 2016/10/12 10:00
 * @param 线下本息费账
 ***/
use \Prj\Data\FKXianXiaBenXiFeiZhang as FKXianXiaBenXiFeiZhangModel;
use \Prj\Misc\FengKongEump;
use Sooh\Base\Form\Item as form_def;

class XianxiabenxifeizhangController extends \Prj\ManagerCtrl
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
        $rongZiHeTongBianHao = $this->_request->get('rongzihetongbianhao');
        if(!empty($rongZiHeTongBianHao)){
        $rzbianhao=$rongZiHeTongBianHao;
        $rongZiHeTongBianHao=\Prj\Data\FKRongZiXiangMuBiao::paged($pager,['rongzihetongbianhao'=>$rongZiHeTongBianHao]);
        $rongZiHeTongBianHao=$rongZiHeTongBianHao[0]['id'];
        !empty($rongZiHeTongBianHao) &&  $where['rongzihetongbianhao'] = $rongZiHeTongBianHao;   
        }
        }
        
        if(!$isDownloadExcel){

            $data = FKXianXiaBenXiFeiZhangModel::paged($pager, $where);

            $temp = [];
            foreach ($data as $k => &$v) {
                foreach ($v as $key => $var) {
                    if(!in_array($key,['iRecordVerID', 'sLockData'])){
                        $temp[$k][$key] = FKXianXiaBenXiFeiZhangModel::parseFieldToString($key, $var);
                    }
                }
            }
        }else{
            $wheretrue=[];
            $records = FKXianXiaBenXiFeiZhangModel::loopFindRecords($wheretrue);
            
            $rs = [];
            foreach ($records as $k => &$v) {
                foreach ($v as $key => $var) {
                    if(!in_array($key,['iRecordVerID', 'sLockData'])){
                        $rs[$k][$key] = FKXianXiaBenXiFeiZhangModel::parseFieldToString($key, $var);
                    }
                }
            }
        }
       
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')->setData($temp)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'del'))      
               ->addRow('rongzihetongbianhao', '融资合同编号', 'z101','select',\Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('rongzihetongbianhao'))
               ->addRow('jiekuanren', '借款人', 'z102')
               ->addRow('qishu', '期数', 'z103')
               ->addRow('zhonglei', '种类', 'z104','select',FengKongEump::getInstance()->get('zhonglei'))
               ->addRow('yingfujineyuan', '应付金额[元]', 'z105')
               ->addRow('yingfushijian', '应付时间', 'z106','datepicker')
               ->addRow('yifujineyuan', '已付金额[元]', 'z107')
               ->addRow('qianfuyincang', '欠付[隐藏]', 'z108')
               ->addRow('yuqitianshu', '逾期天数', 'z109')
               ->addRow('yuqililv', '逾期利率[%]', 'z110')
               ->addRow('yuqifeiyuan', '逾期费[元]', 'z111')
               ->addRow('qianfujineyuan', '欠付金额[元]', 'z112')
               ->addRow('beizhu', '备注', 'z113');
        
        if($isDownloadExcel){
            $excel = $view->toExcel($rs);
            return $this->downExcel($excel['records'], $excel['header']);
        }
        
        $this->_view->assign('view',$view);
        $this->_view->assign('_type',$this->_request->get('_type'));
        $this->_view->assign('where', $where);     
        $this->_view->assign('rongZiHeTongBianHao', $rzbianhao);
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
        
        if($addFlag){
            try{
                $records = $this->formatField($data);
                if(!empty($records['yingfushijian']) && $records['qianfuyincang']>0){
                        if($records['yingfushijian']<strtotime(date('Ymd',time())) && $records['qianfuyincang']>0){
                            $records['yuqitianshu']=(strtotime(date('Ymd',time()))-$records['yingfushijian'])/86400;
                        }else{
                            $records['yuqitianshu']=0;
                        }
                    }

                $tmpModel = FKXianXiaBenXiFeiZhangModel::getCopy('');
                $dbRet = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            }catch (\Exception $e){
                    return $this->returnError('新增失败');  
            }
            $this->_view->assign('_id', $dbRet);
            return $this->returnOK('新增成功');
        }else{
            $model = FKXianXiaBenXiFeiZhangModel::getCopy($key);
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
                    return $this->returnError('更新失败');
                } 
        }
        

    }
    
    public function delAction() {
     $key = $this->getRequest()->get('_id');

        $model = FKXianXiaBenXiFeiZhangModel::getCopy($key);
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

         if(!empty($data['yingfushijian']) && $data['qianfuyincang']>0){
          if($data['yingfushijian']<strtotime(date('Ymd',time())) && $data['qianfuyincang']>0){
            $data['yuqitianshu']=(strtotime(date('Ymd',time()))-$data['yingfushijian'])/86400;
          }else{
            $data['yuqitianshu']=0;
           }
         }
     
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
                case 'qishu':
                    $tmpDaoQiRiQi=$v;
                    $ret[$k] = $tmpDaoQiRiQi;
                    break;
                default:
                    if (($tmpV = FKXianXiaBenXiFeiZhangModel::parseStringToField($k, $v)) === false) {
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
    protected $title=['rongzihetongbianhao','jiekuanren','qishu', 'zhonglei', 'yingfujineyuan',
                      'yingfushijian','yifujineyuan','qianfuyincang','yuqitianshu','yuqililv',
                      'yuqifeiyuan','qianfujineyuan','beizhu'];
            
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
    
                $arr[5]=\Prj\Misc\FengKongImport::checktime($arr[5]);
    
                $rongzihetongbianhao=\Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('rongzihetongbianhao');
               
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],$rongzihetongbianhao);
                $arr[3]=\Prj\Misc\FengKongImport::transvk($arr[3],FengKongEump::getInstance()->get('zhonglei'));

                $result[]=[
                    'rongzihetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'qishu'=>$arr[2],
                    'zhonglei'=>$arr[3],
                    'yingfujineyuan'=>$arr[4],
                    'yingfushijian'=>$arr[5],
                    'yifujineyuan'=>$arr[6],
                    'qianfuyincang'=> $arr[7],
                    'yuqitianshu'=>$arr[8],
                    'yuqililv'=> $arr[9],
                    'yuqifeiyuan'=> $arr[10],
                    'qianfujineyuan'=> $arr[11],
                    'beizhu'=> $arr[12],
                ];
    
            }
            
           
            foreach ($result as $data){
                 try{
                $records = $this->formatFields($data);
                
                $tmpModel = FKXianXiaBenXiFeiZhangModel::getCopy('');
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
                case 'yingfushijian':
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
                case 'rongzihetongbianhao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zhonglei':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKXianXiaBenXiFeiZhangModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}

