<?php

use \Prj\Data\FKYuLiuFanHuanZhang as FKYuLiuFanHuanZhangModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;
/**
 * 预留返还帐
 * Class YuliufanhuanzhangController
 * @author lingtm <lingtima@gmail.com>
 */
class YuliufanhuanzhangController extends \Prj\ManagerCtrl
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
            $rongZiHeTongBianHao = $this->_request->get('rongzihetongbianhao');
            if (!empty($rongZiHeTongBianHao)) {
                $_tmpModel = \Prj\Data\FKRongZiXiangMuBiao::getCopy('');
                $_tmpRet = $_tmpModel->db()
                                     ->getRecord($_tmpModel->tbname(), 'id', ['rongzihetongbianhao' => $rongZiHeTongBianHao]);
                if (!empty($_tmpRet)) {
                    $tmpModel = \Prj\Data\FKFangKuanTiJiangBiao::getCopy('');
                    $tmpRet = $tmpModel->db()
                                       ->getRecord($tmpModel->tbname(), 'id', ['rongzihetongbianhao' => $_tmpRet['id']]);
                    if (!empty($tmpRet)) {
                        $map['rongzihetongbianhao'] = $tmpRet['id'];
                        $where['rongzihetongbianhao'] = $rongZiHeTongBianHao;
                    } else {
                        $map['rongzihetongbianhao'] = -1;
                    }
                } else {
                    $map['rongzihetongbianhao'] = -1;
                }
            }
            $searchId = $this->_request->get('id');
            !empty($searchId) && $where['id'] = $map['id'] = $searchId;
        }

        if ($isDownloadExcel) {
            $model = FKYuLiuFanHuanZhangModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $map, 'sort createTime');
        } else {
            $data = FKYuLiuFanHuanZhangModel::paged($pager, $map, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FKYuLiuFanHuanZhangModel::parseFieldToString($key, $var);
                }
            }
        }

        foreach ($temp as $k => &$v) {
            $_id = $v['rongzihetongbianhao'];

            $midModel = \Prj\Data\FKFangKuanTiJiangBiao::getCopy($_id);
            $midModel->load();
            if ($midModel->exists()) {
                $_model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($midModel->getField('rongzihetongbianhao'));
                $_model->load();
                if ($_model->exists()) {
                    $v['fangkuanshijian'] = date('Y-m-d', $_model->getField('qishiriqi'));
                    $v['qixianyue'] = $_model->getField('yue');
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/yuliufanhuanzhang/update', '/risk/yuliufanhuanzhang/delete?')
             ->addRow('rongzihetongbianhao', '融资合同编号', 'z301', 'select', \Prj\Data\FKFangKuanTiJiangBiao::getBianHaoForEnum('rongzihetongbianhao'), 120)
             ->addRow('jiekuanren', '借款人', 'z302', 'select', \Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('jiekuanren'), 120)
             ->addRow('kehujingli', '客户经理', 'z303', 'select', \Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'), 120)
             ->addRow('gerenyuliu', '个人预留', 'z304', 'text', [], 60)
             ->addRow('meiqifafang', '每期发放', 'z305', 'text', [], 60)
             ->addRow('jinglixingming', '经理姓名', 'z306', 'select', \Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'), 60)
             ->addRow('jingliyuliu', '经理预留', 'z307', 'text', [], 60)
             ->addRow('meiqifafang258', '每期发放', 'z308', 'text', [], 60)
             ->addRow('zongjianxingming', '总监姓名', 'z309', 'select', \Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'), 120)
             ->addRow('zongjianyuliu', '总监预留', 'z310', 'text', [], 60)
             ->addRow('meiqifafang261', '每期发放', 'z311', 'text', [], 60)
             ->addRow('fangkuanshijian', '放款时间', 'z312', 'datepicker', [], 120)
             ->addRow('qixianyue', '期限[月]', 'z313', 'text', [], 60)
             ->addRow('yingfaqishu', '应发期数', 'z314', 'text', [], 60)
             ->addRow('yifaqishu', '已发期数', 'z315', 'text', [], 60)
             ->addRow('xiaciyingfayuefen', '下次应发月份', 'z316', 'text', [], 70)
             ->addRow('fafangzhuangtai', '发放状态', 'z317', 'select', FengKongEnum::getInstance()
                                                                               ->get('fafangzhuangtai'), 80)
             ->addRow('cunqianguanru', '存钱罐[入]', 'z319', 'text', [], 60)
             ->addRow('beizhu', '备注', 'z318', 'text', [], 150);

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

                $tmpModel = FKYuLiuFanHuanZhangModel::getCopy('');
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
            $model = FKYuLiuFanHuanZhangModel::getCopy($key);
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

        $model = FKYuLiuFanHuanZhangModel::getCopy($key);
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
     * @param $data
     * @return array
     * @throws Exception
     */
    private function formatField($data)
    {
        $ret = [];
        $tmpRet = null;

        foreach ($data as $k => $v) {
            switch ($k) {
                case 'rongzihetongbianhao':
                    if (empty($v)) {
                        throw new \Exception('融资合同编号不能为空', 90002);
                    }

                    $tmpModel = \Prj\Data\FKFangKuanTiJiangBiao::getCopy('');
                    $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'jiekuanren':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $_model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($tmpRet['rongzihetongbianhao']);
                    $_model->load();
                    if ($_model->exists()) {
                        $ret[$k] = $_model->getField('id');
                    }

                    break;
                case 'kehujingli':
                case 'jinglixingming':
                case 'zongjianxingming':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $tmpRet[$k] ? : '';
                    break;
                case 'jingliyuliu':
                case 'zongjianyuliu':
                case 'fangkuanshijian':
                case 'qixianyue':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $tmpRet[$k] ? : 0;
                    break;
                case 'gerenyuliu':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $tmpRet['gerenyuliuyuan'] ? : 0;
                    break;
                default:
                    if (($tmpV = FKYuLiuFanHuanZhangModel::parseStringToField($k, $v)) === false) {
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
    protected $title=[ 'rongzihetongbianhao','jiekuanren','kehujingli','gerenyuliu',
                       'meiqifafang','jinglixingming','jingliyuliu','meiqifafang258',
                       'zongjianxingming','zongjianyuliu','meiqifafang261','fangkuanshijian',
                       'qixianyue','yingfaqishu','yifaqishu','xiaciyingfayuefen','fafangzhuangtai',
                       'cunqianguanru','beizhu'];

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
    
                $arr[11]=\Prj\Misc\FengKongImport::checktime($arr[11]);
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],\Prj\Data\FKFangKuanTiJiangBiao::getBianHaoForEnum('rongzihetongbianhao'));
                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],\Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('jiekuanren'));
                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[5]=\Prj\Misc\FengKongImport::transvk($arr[5],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[8]=\Prj\Misc\FengKongImport::transvk($arr[8],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[16]=\Prj\Misc\FengKongImport::transvk($arr[16],FengKongEnum::getInstance()->get('fafangzhuangtai'));
    
                $result[]=[
                    'rongzihetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'kehujingli'=>$arr[2],
                    'gerenyuliu'=>$arr[3],
                    'meiqifafang'=>$arr[4],
                    'jinglixingming'=>$arr[5], 
                    'jingliyuliu'=>$arr[6],
                    'meiqifafang258'=>$arr[7],
                    'zongjianxingming'=>$arr[8],
                    'zongjianyuliu'=>$arr[9],
                    'meiqifafang261'=>$arr[10],
                    'fangkuanshijian'=>$arr[11],
                    'qixianyue'=>$arr[12],
                    'yingfaqishu'=>$arr[13],
                    'yifaqishu'=>$arr[14],
                    'xiaciyingfayuefen'=>$arr[15],
                    'fafangzhuangtai'=>$arr[16],
                    'cunqianguanru'=>$arr[17],
                    'beizhu'=>$arr[18],
                ];
    
            }
            
            foreach ($result as $data){
                  try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
    
                    $tmpModel = FKYuLiuFanHuanZhangModel::getCopy('');
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
                case 'fangkuanshijian':
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
                case 'jiekuanren':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'kehujingli':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'jinglixingming':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zongjianxingming':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fafangzhuangtai':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKYuLiuFanHuanZhangModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
