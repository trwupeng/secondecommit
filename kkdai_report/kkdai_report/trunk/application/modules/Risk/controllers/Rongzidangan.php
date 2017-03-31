<?php

use \Prj\Data\FKRongZiDangAn as FKRongZiDangAnModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;
/**
 * 融资档案
 * Class RongzidanganController
 * @author lingtm <lingtima@gmail.com>
 */
class RongzidanganController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $map = $where = $this->_request->get('where', []);
        if (!empty($where)) {
            $map = $where = json_decode(urldecode($where), true);
        } else {
            $rongZiHeTongBianHao = $this->_request->get('rongzihetongbianhao', '');
            if (!empty($rongZiHeTongBianHao)) {
                $tmpModel = \Prj\Data\FKRongZiXiangMuBiao::getCopy('');
                $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), 'id', ['rongzihetongbianhao' => $rongZiHeTongBianHao]);
                if (!empty($tmpRet)) {
                    $map['rongzihetongbianhao'] = $tmpRet['id'];
                    $where['rongzihetongbianhao'] = $rongZiHeTongBianHao;
                } else {
                    $map['rongzihetongbianhao'] = -1;
                }
            }
        }

        if ($isDownloadExcel) {
            $model = FKRongZiDangAnModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $map, 'sort createTime');
        } else {
            $data = FKRongZiDangAnModel::paged($pager, $map, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FkRongZiDangAnModel::parseFieldToString($key, $var);
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/rongzidangan/update', '/risk/rongzidangan/delete?')
             ->addRow('rongzihetongbianhao', '融资合同编号', 'b201', 'select', \Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('rongzihetongbianhao'), 120)
             ->addRow('jiekuanren', '借款人', 'b202', 'text', [], 60)
             ->addRow('jiekuanjinewanyuan', '借款金额[万元]', 'b203', 'text', [], 60)
             ->addRow('yewuleixing', '业务类型', 'b204', 'select', FengKongEnum::getInstance()->get('yewuleixing'), 60)
             ->addRow('danganbianhao', '档案编号', 'b205', 'text', [], 60)
             ->addRow('fangchanzhengliuzhi', '房产证留置', 'b206', 'select', FengKongEnum::getInstance()
                                                                                    ->get('fangchanzhengliuzhi'), 60)
             ->addRow('qitaliuzhiwu', '其他留置物', 'b207', 'text', [], 60)
             ->addRow('chanquanren', '产权人', 'b208', 'text', [], 120)
             ->addRow('fangchandizhi', '房产地址', 'b209', 'text', [], 150)
             ->addRow('fangchanzhengbianhao', '房产证编号', 'b210', 'text', [], 150)
             ->addRow('taxiangquanzhengbianhao', '他项权证编号', 'b211', 'text', [], 150)
             ->addRow('tazhengqishiri', '他证起始日', 'b212', 'datepicker', [], 120)
             ->addRow('tazhengdaoqiri', '他证到期日', 'b213', 'datepicker', [], 120)
             ->addRow('jiekuanhetong', '借款合同', 'b214', 'select', FengKongEnum::getInstance()->get('jiekuanhetong'), 60)
             ->addRow('buchongxieyi', '补充协议', 'b215', 'select', FengKongEnum::getInstance()->get('buchongxieyi'), 60)
             ->addRow('zhuanzhangpingtiao', '转账凭条', 'b216', 'select', FengKongEnum::getInstance()
                                                                                  ->get('zhuanzhangpingtiao'), 60)
             ->addRow('qitaziliao', '其他资料', 'b217', 'select', FengKongEnum::getInstance()->get('qitaziliao'), 60)
             ->addRow('tazhengzhuxiaoshijian', '他证注销时间', 'b218', 'datepicker', [], 120)
             ->addRow('beizhu', '备注', 'b219', 'text', [], 60);

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

                $tmpModel = FKRongZiDangAnModel::getCopy('');
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
            $model = FKRongZiDangAnModel::getCopy($key);
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

        $model = FkRongZiDangAnModel::getCopy($key);
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
     * @param           array     $data
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
                case 'rongzihetongbianhao':
                    if (empty($v)) {
                        throw new \Exception('融资合同编号不能为空', 90002);
                    }

                    $tmpModel = \Prj\Data\FKRongZiXiangMuBiao::getCopy('');
                    $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[融资项目表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'jiekuanren':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $tmpRet[$k] ? : '';
                    break;
                case 'jiekuanjinewanyuan':
                    if (empty($tmpRet)) {
                        throw new \Exception('融资合同编号在[放款提奖表]中不存在，更新失败', 90003);
                    }
                    $ret[$k] = $tmpRet['jiekuanewanyuan'] ? : 0;
                    break;
                default:
                    if (($tmpV = FKRongZiDangAnModel::parseStringToField($k, $v)) === false) {
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
    protected $title=['rongzihetongbianhao','jiekuanren','jiekuanjinewanyuan','yewuleixing','danganbianhao',
                      'fangchanzhengliuzhi','qitaliuzhiwu','chanquanren','fangchandizhi','fangchanzhengbianhao',
                      'taxiangquanzhengbianhao','tazhengqishiri','tazhengdaoqiri','jiekuanhetong','buchongxieyi',
                      'zhuanzhangpingtiao','qitaziliao','tazhengzhuxiaoshijian','beizhu'];

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
                $arr[12]=\Prj\Misc\FengKongImport::checktime($arr[12]);
                $arr[17]=\Prj\Misc\FengKongImport::checktime($arr[17]);
                 
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],\Prj\Data\FKRongZiXiangMuBiao::getFieldForEnum('rongzihetongbianhao'));
                $arr[3]=\Prj\Misc\FengKongImport::transvk($arr[3],FengKongEnum::getInstance()->get('yewuleixing'));
                $arr[5]=\Prj\Misc\FengKongImport::transvk($arr[5],FengKongEnum::getInstance()->get('fangchanzhengliuzhi'));
                $arr[13]=\Prj\Misc\FengKongImport::transvk($arr[13],FengKongEnum::getInstance()->get('jiekuanhetong'));
                $arr[14]=\Prj\Misc\FengKongImport::transvk($arr[14],FengKongEnum::getInstance()->get('buchongxieyi'));
                $arr[15]=\Prj\Misc\FengKongImport::transvk($arr[15],FengKongEnum::getInstance()->get('zhuanzhangpingtiao'));
                $arr[16]=\Prj\Misc\FengKongImport::transvk($arr[16],FengKongEnum::getInstance()->get('qitaziliao'));
                
                $result[]=[
                    'rongzihetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'jiekuanjinewanyuan'=>$arr[2],
                    'yewuleixing'=>$arr[3],
                    'danganbianhao'=>$arr[4],
                    'fangchanzhengliuzhi'=>$arr[5],
                    'qitaliuzhiwu'=>$arr[6],
                    'chanquanren'=>$arr[7],
                    'fangchandizhi'=>$arr[8],
                    'fangchanzhengbianhao'=>$arr[9],
                    'taxiangquanzhengbianhao'=>$arr[10],
                    'tazhengqishiri'=>$arr[11],
                    'tazhengdaoqiri'=>$arr[12],
                    'jiekuanhetong'=>$arr[13],
                    'buchongxieyi'=>$arr[14],
                    'zhuanzhangpingtiao'=>$arr[15],
                    'qitaziliao'=>$arr[16],
                    'tazhengzhuxiaoshijian'=>$arr[17],
                    'beizhu'=>$arr[18],
                ];
    
            }

            foreach ($result as $data){
                try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
    
                    $tmpModel = FKRongZiDangAnModel::getCopy('');
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
                case 'tazhengqishiri':
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
                case 'tazhengdaoqiri':
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
                case 'tazhengzhuxiaoshijian':
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
                case 'rongzihetongbianhao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'yewuleixing':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangchanzhengliuzhi':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'jiekuanhetong':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                    case 'buchongxieyi':
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
                    case 'qitaziliao':
                        if($v==2000){
                            $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                            throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                        }
                        $ret[$k]=$v;
                        break;   
                default:
                    if (($tmpV = FKRongZiDangAnModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
