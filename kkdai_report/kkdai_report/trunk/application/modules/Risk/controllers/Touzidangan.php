<?php

use \Prj\Data\FKTouZiDangAn as FKTouZiDangAnModel;
use Sooh\Base\Form\Item as form_def;
/**
 * 投资档案
 * Class TouzidanganController
 * @author lingtm <lingtima@gmail.com>
 */
class TouzidanganController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $map = $where = $this->_request->get('where');
        if (!empty($where)) {
            $map = $where = json_decode(urldecode($where), true);
        } else {
            $touZiHeTongBianHao = $this->_request->get('touzihetongbianhao');
            if (!empty($touZiHeTongBianHao)) {
                $tmpModel = \Prj\Data\FKTouZiXiangMuBiao::getCopy('');
                $tmpRet = $tmpModel->db()
                                   ->getRecord($tmpModel->tbname(), 'id', ['touzihetongbianhao' => $touZiHeTongBianHao]);
                if (!empty($tmpRet)) {
                    $map['touzihetongbianhao'] = $tmpRet['id'];
                    $where['touzihetongbianhao'] = $touZiHeTongBianHao;
                } else {
                    $map['touzihetongbianhao'] = -1;
                }
            }
        }

        if ($isDownloadExcel) {
            $model = FKTouZiDangAnModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $map, 'sort createTime');
        } else {
            $data = FKTouZiDangAnModel::paged($pager, $map, 'sort createTime');
        }
        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FKTouZiDangAnModel::parseFieldToString($key, $var);
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/touzidangan/update', '/risk/touzidangan/delete?')
             ->addRow('touzihetongbianhao', '投资合同编号', 'b701', 'select', \Prj\Data\FKTouZiXiangMuBiao::getFieldForEnum('touzihetongbianhao'), 120)
             ->addRow('touziren', '投资人', 'b702', 'select', \Prj\Data\FKTouZiKeHuMingCe::getFieldForEnum('xingming'), 120)
             ->addRow('touziewanyuan', '投资额[万元]', 'b703', 'text', [], 80)
             ->addRow('qishiriqi', '起始日期', 'b704', 'datepicker', [], 120)
             ->addRow('yue', '月', 'b705', 'text', [], 40)
             ->addRow('tian', '天', 'b706', 'text', [], 40)
             ->addRow('daoqiriqi', '到期日期', 'b707', 'datepicker', [], 120)
             ->addRow('fangkuanrenyinhang', '放款人[银行]', 'b708', 'select', \Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanrenyinhang'), 80)
            ->addRow('yuexi', '月息[%]', 'b711', 'text', [], 60)
            ->addRow('rongzihetongbianhao', '融资合同编号', 'b709', 'text', [], 120)
            ->addRow('jiekuanren', '借款人', 'b710', 'text', [], 60)
             ->addRow('touzihetong', '投资合同', 'b712', 'select', [1 =>'有', 2 => '无'], 60)
             ->addRow('haikuanmingxibiao', '还款明细表', 'b713', 'select', [1 =>'有', 2 => '无'], 60)
             ->addRow('zhuanzhangpingtiao', '转账凭条', 'b714', 'select', [1 =>'有', 2 => '无'], 60)
             ->addRow('haikuanzhuanzhangpingzheng', '还款转账凭证', 'b715', 'select', [1 =>'有', 2 => '无'], 80)
             ->addRow('taxiangquanzheng', '他项权证', 'b716', 'select', [1 => '未领', 2 => '已领'], 60)
             ->addRow('jieqingzhuangkuang', '结清状况', 'b7161', 'select', [1 => '已结清', 2 => '未结清'], 80)
             ->addRow('beizhu', '备注', 'b717', 'text', [], 60);

        if ($isDownloadExcel) {
            $excel = $view->toExcel($temp);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        $this->_view->assign('_type', $this->_request->get('_type'));
        $this->_view->assign('where', $where);
        $this->_view->assign('map', $map);
        return 0;
    }

    public function updateAction()
    {
        $addFlag = false;
        $data = $this->getRequest()->get('values')[0];
        $key = $this->getRequest()->get('id');
        if (empty($key)) {
            $addFlag = true;
        }
        unset($data['id']);
        if (empty($data)) {
            $this->returnError('没有更新的内容，更新失败');
            return 0;
        }

        if ($addFlag) {
            try {
                $records = $this->formatField($data);
                $records['createTime'] = $records['updateTime'] = time();
                $records['status'] = 1;

                $tmpModel = FKTouZiDangAnModel::getCopy('');
                $ret = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('新增失败');
                    return 0;
                }
            }
            $this->_view->assign('_id', $ret);
            $this->returnOK('新增成功');
            return 0;
        } else {
            //update
            $model = FKTouZiDangAnModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $this->formatAndSetField($model, $data);
                $model->setField('updateTime', time());
                $model->update();
                $this->_view->assign('_id', $key);
                $this->returnOK('更新成功');
                return 0;
            } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('更新失败');
                    return 0;
                }
            }
        }
    }

    public function deleteAction()
    {
        $key = $this->getRequest()->get('_id');

        $model = FKTouZiDangAnModel::getCopy($key);
        $model->load();
        if ($model->exists()) {
            $model->delete();
            $this->returnOK('删除成功');
            return 0;
        } else {
            $this->returnError('记录不存在或者已经被删除');
            return 0;
        }
    }

    /**
     * @param \Sooh\DB\Base\KVObj $model
     * @param       array         $data
     * @throws Exception
     * @return bool
     */
    private function formatAndSetField($model, $data)
    {
        $data = $this->formatField($data);
        foreach ($data as $k => $v) {
            $model->setField($k, $v);
        }
        return true;
    }

    /**
     * 预处理表单数据
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function formatField($data)
    {
        $ret = [];
        foreach ($data as $k => $v) {
            switch ($k) {
                case 'touzihetongbianhao':
                    if (empty($v)) {
                        throw new \Exception('投资合同编号不能为空', 90002);
                    }

                    $tmpModel = \Prj\Data\FKTouZiXiangMuBiao::getCopy('');
                    $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpRet)) {
                        throw new \Exception('投资合同编号在[投资项目表]中不存在', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'touziren':
                    if (empty($tmpRet)) {
                        throw new \Exception('投资合同编号在[投资项目表]中不存在', 90003);
                    }
                    $ret[$k] = $tmpRet['touziren'] ? : 0;
                    break;
                case 'daoqiriqi':
                    if (empty($data['qishiriqi'])) {
                        $ret[$k] = 0;
                    } else {
                        //当起始日期不为空，则到期日期为起始日期加上期限的对应日（月、天）
                        $tmpDaoQiRiQi = FKTouZiDangAnModel::formatTimeAdd(strtotime($data['qishiriqi']), '+' . intval($data['yue']), '+' . intval($data['tian']));
                        $ret[$k] = $tmpDaoQiRiQi;
                    }
                    break;
                case 'fangkuanrenyinhang':
                    if (empty($v)) {
                        throw new \Exception('放款人银行不能为空', 90002);
                    }

                    $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                    $_ret = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($_ret)) {
                        throw new \Exception('放款人银行在[放款人名册]中不存在', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                default:
                    if (($tmpV = FKTouZiDangAnModel::parseStringToField($k, $v)) === false) {
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
    protected $title=['touzihetongbianhao','touziren','touziewanyuan','qishiriqi',
                       'yue','tian','daoqiriqi','fangkuanrenyinhang','yuexi','rongzihetongbianhao',
                      'jiekuanren','touzihetong','haikuanmingxibiao','zhuanzhangpingtiao','haikuanzhuanzhangpingzheng',
                      'taxiangquanzheng','jieqingzhuangkuang','beizhu'];

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
    
                $arr[3]=\Prj\Misc\FengKongImport::checktime($arr[3]);
                $arr[6]=\Prj\Misc\FengKongImport::checktime($arr[6]);
                
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],\Prj\Data\FKTouZiXiangMuBiao::getFieldForEnum('touzihetongbianhao'));
                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],\Prj\Data\FKTouZiKeHuMingCe::getFieldForEnum('xingming'));
                $arr[7]=\Prj\Misc\FengKongImport::transvk($arr[7],\Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanrenyinhang'));
                $arr[11]=\Prj\Misc\FengKongImport::transvk($arr[11],[1 =>'有', 2 => '无']);
                $arr[12]=\Prj\Misc\FengKongImport::transvk($arr[12],[1 =>'有', 2 => '无']);
                $arr[13]=\Prj\Misc\FengKongImport::transvk($arr[13],[1 =>'有', 2 => '无']);
                $arr[14]=\Prj\Misc\FengKongImport::transvk($arr[14],[1 =>'有', 2 => '无']);
                $arr[15]=\Prj\Misc\FengKongImport::transvk($arr[15],[1 => '未领', 2 => '已领']);
                $arr[16]=\Prj\Misc\FengKongImport::transvk($arr[16],[1 => '已结清', 2 => '未结清']);
                
                $result[]=[
                    'touzihetongbianhao'=>$arr[0],
                    'touziren'=>$arr[1],
                    'touziewanyuan'=>$arr[2],
                    'qishiriqi'=>$arr[3],
                    'yue'=>$arr[4],
                    'tian'=>$arr[5],
                    'daoqiriqi'=>$arr[6],
                    'fangkuanrenyinhang'=>$arr[7],
                    'yuexi'=>$arr[8],
                    'rongzihetongbianhao'=>$arr[9],
                    'jiekuanren'=>$arr[10],
                    'touzihetong'=>$arr[11],
                    'haikuanmingxibiao'=>$arr[12],
                    'zhuanzhangpingtiao'=>$arr[13],
                    'haikuanzhuanzhangpingzheng'=>$arr[14],
                    'taxiangquanzheng'=>$arr[15],
                    'jieqingzhuangkuang'=>$arr[16],
                    'beizhu'=>$arr[17],
                ];
    
            }
            
            
            foreach ($result as $data){
                 try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
    
                    $tmpModel = FKTouZiDangAnModel::getCopy('');
                    $ret = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                    $this->_view->assign('_id', $ret);
                } catch (\Exception $e) {
                    if ($e->getCode() >= 90000) {
                        $this->returnError($e->getMessage());
                        return 0;
                    } else {
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
                case 'qishiriqi':
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
                case 'daoqiriqi':
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
                case 'touzihetongbianhao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'touziren':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangkuanrenyinhang':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;    
                case 'touzihetong':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;   
                case 'haikuanmingxibiao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;       
                case 'zhuanzhangpingtiao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'haikuanzhuanzhangpingzheng':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'taxiangquanzheng':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'jieqingzhuangkuang':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKTouZiDangAnModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
