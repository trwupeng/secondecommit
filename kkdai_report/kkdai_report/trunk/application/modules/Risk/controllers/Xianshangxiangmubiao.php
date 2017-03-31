<?php

use \Prj\Data\FKXianShangXiangMuBiao as FKXianShangXiangMuBiaoModel;
use Sooh\Base\Form\Item as form_def;
/**
 * 线上项目表
 * Class XianshangxiangmubiaoController
 * @author lingtm <lingtima@gmail.com>
 */
class XianshangxiangmubiaoController extends \Prj\ManagerCtrl
{
    public function indexAction()
    {
        $isDownloadExcel = $this->_request->get('__EXCEL__');
        $pageId = $this->_request->get('pageId', 1) - 0;
        $pager = new \Sooh\DB\Pager($this->_request->get('pageSize', 10), $this->pageSizeEnum, false);
        $pager->init(-1, $pageId);

        $where = $this->_request->get('where');
        if (!empty($where)) {
            $where = json_decode(urldecode($where), true);
        } else {
            $biaodiId = $this->getRequest()->get('biaodiId', '');
            if (!empty($biaodiId)) {
                $where['id'] = $biaodiId;
            }
        }

        if ($isDownloadExcel) {
            $model = FKXianShangXiangMuBiaoModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $where, 'sort createTime');
        } else {
            $data = FKXianShangXiangMuBiaoModel::paged($pager, $where, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FKXianShangXiangMuBiaoModel::parseFieldToString($key, $var);
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/xianshangxiangmubiao/update', '/risk/xianshangxiangmubiao/delete?')
             ->addRow('shangbiaoriqi', '上标日期', 'x101', 'datepicker', [], 120)
             ->addRow('biaodimingcheng', '标的名称', 'x102', 'text', [], 100)
             ->addRow('jiekuanren', '借款人', 'x103', 'select', \Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanren'), 80)
             ->addRow('nicheng', '昵称', 'x104', 'text', [], 80)
             ->addRow('fangkuanriqi', '放款日期', 'x105', 'datepicker', [], 120)
             ->addRow('biaodijineyuan', '标的金额[元]', 'x106', 'text', [], 100)
             ->addRow('nianlilv', '年利率[%]', 'x107', 'text', [], 80)
             ->addRow('yue', '月', 'x108', 'text', [], 40)
             ->addRow('tian', '天', 'x109', 'text', [], 40)
             ->addRow('hao', '号', 'x110', 'text', [], 40)
             ->addRow('fuwufei', '服务费[%]', 'x111', 'text', [], 80)
             ->addRow('fuwufeiyuan', '服务费[元]', 'x112', 'text', [], 80)
             ->addRow('shouquriqi', '收取日期', 'x113', 'datepicker', [], 120)
             ->addRow('shijidaozhangyuan', '实际到账[元]', 'x114', 'text', [], 100)
             ->addRow('toubiaojineyuan', '投标金额[元]', 'x115', 'text', [], 100)
             ->addRow('kehutoubiaojineyuan', '客户投标金额[元]', 'x116', 'text', [], 120)
             ->addRow('daoqiriqi', '到期日期', 'x117', 'datepicker', [], 120)
             ->addRow('jieqingqingkuang', '结清情况', 'x118', 'select', ['无', '是', '否'], 60)
             ->addRow('beizhu', '备注', 'x119', 'text', [], 150)
             ->addTab('tab1', '线上本息费账', '/risk/xianshangbenxifeizhang/index?', 'biaodimingcheng', 'biaodimingcheng');

        if ($isDownloadExcel) {
            $excel = $view->toExcel($temp);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        $this->_view->assign('_type', $this->_request->get('_type'));
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

                $tmpModel = FKXianShangXiangMuBiaoModel::getCopy('');
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
            $model = FKXianShangXiangMuBiaoModel::getCopy($key);
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

        $model = FKXianShangXiangMuBiaoModel::getCopy($key);
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
     * @param      array          $data
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
     * 格式化与处理表单数据
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function formatField($data)
    {
        $ret = [];
        $tmpMRet = null;

        foreach ($data as $k => $v) {
            switch ($k) {
                case 'jiekuanren':
                    $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                    $tmpMRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpMRet)) {
                        throw new \Exception('放款人在[放款人名册]中不存在', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'nicheng':
                    if (empty($tmpMRet)) {
                        throw new \Exception('放款人在[放款人名册]中不存在', 90003);
                    }
                    $ret[$k] = $tmpMRet['xianshangnicheng'] ? : '';
                    break;
                case 'fuwufei':
                    $ret[$k] = 100;
                    break;
                case 'shouquriqi':
                    $ret[$k] = strtotime($data['fangkuanriqi']);
                    break;
                case 'daoqiriqi':
                    if (empty($data['fangkuanriqi'])) {
                        $ret[$k] = 0;
                    } else {
                        //当起始日期不为空，则到期日期为起始日期加上期限的对应日（月、天)
                        $tmpDaoQiRiQi = FKXianShangXiangMuBiaoModel::formatTimeAdd(strtotime($data['fangkuanriqi']), '+' . intval($data['yue']), '+' . intval($data['tian']));
                        $ret[$k] = $tmpDaoQiRiQi;
                    }
                    break;
                default:
                    if (($tmpV = FKXianShangXiangMuBiaoModel::parseStringToField($k, $v)) === false) {
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
    protected $title=[ 'shangbiaoriqi','biaodimingcheng','jiekuanren','nicheng','fangkuanriqi',
                       'biaodijineyuan','nianlilv','yue','tian','hao','fuwufei','fuwufeiyuan',
                       'shouquriqi','shijidaozhangyuan','toubiaojineyuan','kehutoubiaojineyuan',
                       'daoqiriqi','jieqingqingkuang','beizhu'];

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
    
                $arr[0]=\Prj\Misc\FengKongImport::checktime($arr[0]);
                $arr[4]=\Prj\Misc\FengKongImport::checktime($arr[4]);
                $arr[12]=\Prj\Misc\FengKongImport::checktime($arr[12]);
                $arr[16]=\Prj\Misc\FengKongImport::checktime($arr[16]);
                $arr[2]=\Prj\Misc\FengKongImport::transvk($arr[2],\Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanren'));
                $arr[17]=\Prj\Misc\FengKongImport::transvk($arr[17],['无', '是', '否']);
                
                $result[]=[
                    'shangbiaoriqi'=>$arr[0],
                    'biaodimingcheng'=>$arr[1],
                    'jiekuanren'=>$arr[2],
                    'nicheng'=>$arr[3],
                    'fangkuanriqi'=>$arr[4],
                    'biaodijineyuan'=>$arr[5],
                    'nianlilv'=>$arr[6],
                    'yue'=>$arr[7], 
                    'tian'=>$arr[8],
                    'hao'=>$arr[9],
                    'fuwufei'=>$arr[10],
                    'fuwufeiyuan'=>$arr[11],
                    'shouquriqi'=>$arr[12],
                    'shijidaozhangyuan'=>$arr[13],
                    'toubiaojineyuan'=>$arr[14],
                    'kehutoubiaojineyuan'=>$arr[15],
                    'daoqiriqi'=>$arr[16],
                    'jieqingqingkuang'=>$arr[17],
                    'beizhu'=>$arr[18],
                ];
    
            }
          
            foreach ($result as $data){
             
               try {
                $records = $this->formatFields($data);
                $records['createTime'] = $records['updateTime'] = time();
                $records['status'] = 1;

                $tmpModel = FKXianShangXiangMuBiaoModel::getCopy('');
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
                case 'shangbiaoriqi':
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
                case 'fangkuanriqi':
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
                case 'shouquriqi':
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
                case 'jiekuanren':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'jieqingqingkuang':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKXianShangXiangMuBiaoModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
