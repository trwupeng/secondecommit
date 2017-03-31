<?php
/**
 * @author wu.peng
 * @param date 2016/10/17 14:00
 * @param 电销提奖
 ***/

use \Prj\Data\FKDianXiaoTiJiangBiao as FKDianXiaoTiJiangBiaoModel;
use \Prj\Misc\FengKongEump;
use Sooh\Base\Form\Item as form_def;
class DianxiaotijiangbiaoController extends \Prj\ManagerCtrl
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
        $yewubianhao = $this->_request->get('yewubianhao');
        !empty($yewubianhao) && $where['yewubianhao']=$yewubianhao;
        }
        
        if(!$isDownloadExcel){
            $data = FKDianXiaoTiJiangBiaoModel::paged($pager, $where);
            
            $temp = [];
            foreach ($data as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $temp[$k][$key] = FKDianXiaoTiJiangBiaoModel::parseFieldToString($key, $var);
                    }
                }
            } 
        }else{
            $wheretrue=[];
            $records = FKDianXiaoTiJiangBiaoModel::loopFindRecords($wheretrue);
            
            $rs = [];
            foreach ($records as $k => &$v) {
                foreach ($v as $key => $var) {
                    if (!in_array($key, ['iRecordVerID', 'sLockData'])) {
                        $rs[$k][$key] = FKDianXiaoTiJiangBiaoModel::parseFieldToString($key, $var);
                    }
                }
            }
        }
       
        
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')->setData($temp)->setPager($pager)->setAction(\Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'update'), \Sooh\Base\Tools::uri(['__VIEW__'=>'json'], 'delete'))  
              ->addRow('yewubianhao', '业务编号', 'b901')
              ->addRow('kehuxingming', '客户姓名', 'b902')
              ->addRow('rongzijinewanyuan', '融资金额[万元]', 'b903')
              ->addRow('hezuoyinhang', '合作银行', 'b904')
              ->addRow('yewuleixing', '业务类型', 'b905','select',FengKongEump::getInstance()->get('yewuleixing'))
              ->addRow('shoufeijinewanyuan', '收费金额[万元]', 'b906')
              ->addRow('shoufeiriqi', '收费日期', 'b907','datepicker')
              ->addRow('jiedanren', '接单人', 'b908','select',\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'))
              ->addRow('jibie', '级别', 'b909','select',['1'=>'经理','2'=>'主任','3'=>'业务员'])
              ->addRow('dangyueheji', '当月合计', 'b910')
              ->addRow('tijiangbili', '提奖比例', 'b911')
              ->addRow('tijiangjine', '提奖金额', 'b912')
              ->addRow('tandanren', '谈单人', 'b913','select',\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'))
              ->addRow('tijiangbili284', '提奖比例', 'b914')
              ->addRow('tijiangjine285','提奖金额', 'b915')
              ->addRow('gendanren', '跟单人', 'b916','select',\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'))
              ->addRow('tijiangbili287', '提奖比例', 'b917')
              ->addRow('tijiangjine288', '提奖金额', 'b918')
              ->addRow('zuodanren', '做单人', 'b919','select',\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'))
              ->addRow('tijiangbili290', '提奖比例', 'b920')
              ->addRow('tijiangjine291', '提奖金额', 'b921')
              ->addRow('bumenjingli', '部门经理', 'b922')
              ->addRow('tijiangbili293', '提奖比例', 'b923')
              ->addRow('tijiangjine294', '提奖金额', 'b924');
        
        
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
                $records = $this->formatField($data);
                $tmpModel = FKDianXiaoTiJiangBiaoModel::getCopy('');
                $all=FKDianXiaoTiJiangBiaoModel::getFieldForEnum('yewubianhao');
                if(in_array($records['yewubianhao'], $all)){
                    return  $this->returnError('业务编号禁止重复');
                }
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

            $model = FKDianXiaoTiJiangBiaoModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                return $this->returnError('记录不存在或者已经被删除');
            }
            
            try {
                $this->formatAndSetField($model, $data);
                $model->update();
                $this->_view->assign('_id', $key);
                return $this->returnOK('更新成功');
            }catch (\Exception $e) {
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
        
                $model = FKDianXiaoTiJiangBiaoModel::getCopy($key);
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
                        case 'tijiangbili':
                             if($data['jibie']=='1' || $data['jibie']=='2'){
                             if($data['dangyueheji']<20 && $data['dangyueheji']>0){
                                $tmpDaoQiRiQi=30;
                             }elseif($data['dangyueheji']>=20){
                                $tmpDaoQiRiQi=40;
                             }   
                             }elseif($data['jibie']=='3'){
                                if($data['dangyueheji']>=2 && $data['dangyueheji']<5){
                                   $tmpDaoQiRiQi=15;
                                }elseif ($data['dangyueheji']>=5 && $data['dangyueheji']<10){
                                   $tmpDaoQiRiQi=20;
                                }elseif ($data['dangyueheji']>=10 && $data['dangyueheji']<15){
                                    $tmpDaoQiRiQi=25;
                                }elseif ($data['dangyueheji']>=15 && $data['dangyueheji']<20){
                                   $tmpDaoQiRiQi=30;
                                }elseif ($data['dangyueheji']>=20){
                                  $tmpDaoQiRiQi=40;
                                }elseif($data['dangyueheji']<2){
                                    $tmpDaoQiRiQi=12;
                                }
                             }
                             $ret[$k]=$tmpDaoQiRiQi*100;
                            break;
                       
                        default:
                            if (($tmpV = FKDianXiaoTiJiangBiaoModel::parseStringToField($k, $v)) === false) {
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
            protected $title=['yewubianhao','kehuxingming','rongzijinewanyuan','hezuoyinhang','yewuleixing',
                              'shoufeijinewanyuan','shoufeiriqi','jiedanren','jibie','dangyueheji','tijiangbili',
                              'tijiangjine','tandanren','tijiangbili284','tijiangjine285','gendanren','tijiangbili287',
                              'tijiangjine288','zuodanren','tijiangbili290','tijiangjine291','bumenjingli','tijiangbili293',
                              'tijiangjine294'];

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
                        $arr[6]=\Prj\Misc\FengKongImport::checktime($arr[6]);
                         
                        $mingce=\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming');

                        $arr[4]=\Prj\Misc\FengKongImport::transvk($arr[4],FengKongEump::getInstance()->get('yewuleixing'));
                        $arr[7]=\Prj\Misc\FengKongImport::transvk($arr[7],$mingce);
                        $arr[8]=\Prj\Misc\FengKongImport::transvk($arr[8],FengKongEump::getInstance()->get('jibie'));
                        $arr[12]=\Prj\Misc\FengKongImport::transvk($arr[12],$mingce);
                        $arr[15]=\Prj\Misc\FengKongImport::transvk($arr[15],$mingce);
                        $arr[18]=\Prj\Misc\FengKongImport::transvk($arr[18],$mingce);

                        $result[]=[
                            'yewubianhao'=>$arr[0],
                            'kehuxingming'=>$arr[1],
                            'rongzijinewanyuan'=>$arr[2],
                            'hezuoyinhang'=>$arr[3],
                            'yewuleixing'=>$arr[4],
                            'shoufeijinewanyuan'=>$arr[5],
                            'shoufeiriqi'=>$arr[6],
                            'jiedanren'=>$arr[7],
                            'jibie'=>$arr[8],
                            'dangyueheji'=>$arr[9],
                            'tijiangbili'=>$arr[10],
                            'tijiangjine'=>$arr[11],
                            'tandanren'=>$arr[12],
                            'tijiangbili284'=>$arr[13],
                            'tijiangjine285'=>$arr[14],
                            'gendanren'=>$arr[15],
                            'tijiangbili287'=>$arr[16],
                            'tijiangjine288'=>$arr[17],
                            'zuodanren'=>$arr[18],
                            'tijiangbili290'=>$arr[19],
                            'tijiangjine291'=>$arr[20],
                            'bumenjingli'=>$arr[21],
                            'tijiangbili293'=>$arr[22],
                            'tijiangjine294'=>$arr[23],
                        ];
            
                    }
                   
                    foreach ($result as $data){
                        try{
                            $records = $this->formatFields($data);
                            $tmpModel = FKDianXiaoTiJiangBiaoModel::getCopy('');
                            $all=FKDianXiaoTiJiangBiaoModel::getFieldForEnum('yewubianhao');
                            if(in_array($records['yewubianhao'], $all)){
                                return  $this->returnError('业务编号禁止重复');
                            }
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
            
            
            public  function formatFields($data){
                $ret = [];
                $tmpDaoQiRiQi = 0;
                foreach ($data as $k => $v) {
                    switch ($k) {
                        case 'shoufeiriqi':
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
                        case 'yewuleixing':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'jiedanren':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'jibie':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'tandanren':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'gendanren':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        case 'zuodanren':
                            if($v==2000){
                                $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                                throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                            }
                            $ret[$k]=$v;
                            break;
                        default:
                            if (($tmpV = FKDianXiaoTiJiangBiaoModel::parseStringToField($k, $v)) === false) {
                                throw new \Exception('表单值不合法', 90001);
                            }
                            $ret[$k] = $tmpV;
                    }
                }
                return $ret;
            }
}

