<?php

use \Prj\Data\FKXianShangRiJiZhang as FKXianShangRiJiZhangModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;
/**
 * 线上日记账
 * Class XianshangrijizhangController
 * @author lingtm <lingtima@gmail.com>
 */
class XianshangrijizhangController extends \Prj\ManagerCtrl
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
        }

        if ($isDownloadExcel) {
            $model = FKXianShangRiJiZhangModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $where, 'sort createTime');
        } else {
            $data = FKXianShangRiJiZhangModel::paged($pager, $where, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FKXianShangRiJiZhangModel::parseFieldToString($key, $var);
                }
            }
        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/xianshangrijizhang/update', '/risk/xianshangrijizhang/delete?')
             ->addRow('zhonglei', '种类', 'x301', 'select', FengKongEnum::getInstance()->get('rijizhangzhonglei'), 60)
             ->addRow('zhanghao', '账号', 'x302', 'select', \Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('xianshangnicheng'), 100)
             ->addRow('riqi', '日期', 'x303', 'datepicker', [], 120)
             ->addRow('qichuyueyuan', '期初余额[元]', 'x304', 'text', [], 100)
             ->addRow('cunqianguanru', '存钱罐[入]', 'x305', 'text', [], 100)
             ->addRow('xianxiachongzhiru', '线下充值[入]', 'x306', 'text', [], 100)
             ->addRow('qiyehuchongzhiru', '企业户充值[入]', 'x307', 'text', [], 100)
             ->addRow('haoyoufanxianru', '好友返现[入]', 'x308', 'text', [], 100)
             ->addRow('shoudaofangkuaneru', '收到放款额[入]', 'x309', 'text', [], 100)
             ->addRow('daoqibenjinru', '到期本金[入]', 'x310', 'text', [], 100)
             ->addRow('daoqilixiru', '到期利息[入]', 'x311', 'text', [], 100)
             ->addRow('diaopeizijinru', '调配资金[入]', 'x312', 'text', [], 100)
             ->addRow('jiedongzijinru', '解冻资金[入]', 'x313', 'text', [], 100)
             ->addRow('tixianchu', '提现[出]', 'x314', 'text', [], 100)
             ->addRow('shouxufeichu', '手续费[出]', 'x315', 'text', [], 100)
             ->addRow('zhuanzhangzijinchu', '转账资金[出]', 'x316', 'text', [], 100)
             ->addRow('dongjiezijinchu', '冻结资金[出]', 'x317', 'text', [], 100)
             ->addRow('zhifubenxichu', '支付本息[出]', 'x318', 'text', [], 100)
             ->addRow('zhifutoubiaochu', '支付投标[出]', 'x319', 'text', [], 100)
             ->addRow('qimoyueyuan', '期末余额[元]', 'x320', 'text', [], 100)
             ->addRow('beizhu', '备注', 'x321', 'text', [], 150);

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
                $tmpModel = FKXianShangRiJiZhangModel::getCopy('');
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
            $model = FKXianShangRiJiZhangModel::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $this->formatAndSetField($model, $data, $key);
                $model->setField('updateTime', time());
                $model->update();

                //向下循环更新记录
                $this->updLoop($data, $key);

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

        $model = FKXianShangRiJiZhangModel::getCopy($key);
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
     * @param        array        $data
     * @param string              $key
     * @throws Exception
     * @return bool
     */
    private function formatAndSetField($model, $data, $key = '')
    {
        $data = $this->formatField($data, $key);
        foreach ($data as $k => $v) {
            $model->setField($k, $v);
        }
        return true;
    }

    /**
     * 格式化与处理表单数据
     * @param array  $data
     * @param string $key
     * @return array
     * @throws Exception
     */
    private function formatField($data, $key = '')
    {
        $ret = [];
        $tmpZhanghao = 0;
        $tmpQiChuCha = 0;

        foreach ($data as $k => $v) {
            switch ($k) {
                case 'zhanghao':
                    if (empty($v)) {
                        $ret[$k] = $tmpZhanghao = '';
                        break;
                    }

                    $FangKuanRenModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                    $Fdb = $FangKuanRenModel->db();
                    $Ftb = $FangKuanRenModel->tbname();
                    $tmpFRet = $Fdb->getRecord($Ftb, '*', ['id' => $v]);
                    if (empty($tmpFRet)) {
                        throw new \Exception('账号在[放款人名册]中不存在', 90001);
                    }
                    $ret[$k] = $tmpZhanghao = $v;
                    break;
                case 'qichuyueyuan':
                    //若 账号 不为空，则 期初余额 等于该账号上一行中的 期末余额，否则 期初余额 等于0
                    if (empty($tmpZhanghao)) {
                        $ret[$k] = sprintf('%.0f', $v * 100);
                        $tmpQiChuCha = $v - $ret[$k];
                        break;
                    }

                    $getHaoRet = $this->getZhangHaoRet($tmpZhanghao, $key);
                    if (empty($key)) {
                        //add
                        if ($getHaoRet) {
                            $lastRet = $this->getLastFrontLine($tmpZhanghao, $key);
                            $ret[$k] = $this->computeQiChu($v, $key, $lastRet);
                            $tmpQiChuCha = sprintf('%.0f', $v * 100) - $ret[$k];
                            break;
                        } else {
                            $ret[$k] = $this->computeQiChu($v, $key);
                            $tmpQiChuCha = sprintf('%.0f', $v * 100) - $ret[$k];
                            break;
                        }
                    } else {
                        //update
                        if ($getHaoRet) {
                            $lastRet = $this->getLastFrontLine($tmpZhanghao, $key);
                            $ret[$k] = $this->computeQiChu($v, $key, $lastRet);
                            $tmpQiChuCha = sprintf('%.0f', $v * 100) - $ret[$k];
                            break;
                        } else {
                            $ret[$k] = $this->computeQiChu($v, $key);
                            $tmpQiChuCha = sprintf('%.0f', $v * 100) - $ret[$k];
                            break;
                        }
                    }
                    break;
                case 'qimoyueyuan':
                    $ret[$k] = sprintf('%.0f', ($v * 100 - $tmpQiChuCha));
                    break;
                default:
                    if (($tmpV = FKXianShangRiJiZhangModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }

        return $ret;
    }

    /**
     * 检查此账号在数据库中是否有其他记录
     * @param $zhanghao
     * @param $key
     * @return bool
     */
    protected function getZhangHaoRet($zhanghao, $key = '')
    {
        $where = ['zhanghao' => $zhanghao];
        if (!empty($key)) {
            $where['id!'] = $key;
        }

        $model = FKXianShangRiJiZhangModel::getCopy('');
        $ret = $model->db()->getRecord($model->tbname(), '*', $where);

        return empty($ret) ? false : true;
    }

    /**
     * 获取之前一条数据
     * @param        $zhanghao
     * @param string $key
     * @return mixed
     */
    protected function getLastFrontLine($zhanghao, $key = '')
    {
        $model = FKXianShangRiJiZhangModel::getCopy('');
        if (empty($key)) {
            $ret = $model->db()->getRecord($model->tbname(), '*', ['zhanghao' => $zhanghao], 'rsort createTime');
        } else {
            $ret = $model->db()->getRecord($model->tbname(), '*', [
                'zhanghao' => $zhanghao,
                'id<'      => $key,
            ], 'rsort createTime');
        }

        return $ret;
    }

    /**
     * 循环依次向下遍历更新期初余额和期末余额
     * @param $data
     * @param $key
     * @throws ErrorException
     * @throws Exception
     */
    protected function updLoop($data, $key)
    {
        if (!isset($data['zhanghao']) || empty($data['zhanghao'])) {
            return;
        }

        //账号是否合法有效
        $model = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
        $modelRet = $model->db()->getRecord($model->tbname(), '*', ['id' => $data['zhanghao']]);
        if (empty($modelRet)) {
            return;
        }
        $zhangHao = $data['zhanghao'];

        $thisModel = FKXianShangRiJiZhangModel::getCopy($key);
        $thisModel->load();
        if (!$thisModel->exists()) {
            return;
        }
        $tmpNew = $thisModel->getField('qimoyueyuan');

        if ($this->getZhangHaoRet($zhangHao, $key)) {
//                        error_log('exists other zhanghao info');
            $dbModel = FKXianShangRiJiZhangModel::getCopy('');
            $dbRet = $dbModel->db()->getRecords($dbModel->tbname(), '*', [
                'zhanghao' => $zhangHao,
                'id>'      => $key,
            ], 'sort createTime');
            if ($dbRet) {
//                                error_log('need upd line');
                $tmpAdd = $tmpNew - $dbRet[0]['qichuyueyuan'];
                if ($tmpAdd == 0) {
                    return;
                }

//                                error_log('tmpAdd:' . $tmpAdd);

                //循环的 依次的 向下变更
                foreach ($dbRet as $k => $v) {
                    $tmpModel = FKXianShangRiJiZhangModel::getCopy($v['id']);
                    $tmpModel->load();
                    if ($tmpModel->exists()) {
                        $tmpModel->setField('qichuyueyuan', $tmpModel->getField('qichuyueyuan') + $tmpAdd);
                        $tmpModel->setField('qimoyueyuan', $tmpModel->getField('qimoyueyuan') + $tmpAdd);
                        $tmpModel->update();
                    }
                    unset($tmpModel);
//                                        error_log(\Sooh\DB\Broker::lastCmd());
                }
            }
        }

        return;
    }

    /**
     * 计算期初余额
     * @param string $data
     * @param string $key
     * @param array  $dbRet
     * @return int|string
     */
    protected function computeQiChu($data = '', $key = '', $dbRet = [])
    {
        if (!empty($dbRet)) {
            if (!empty($key) && $key == $dbRet['id']) {
                //当前正在编辑首行数据的期初余额
                $ret = sprintf('%.0f', $data * 100);
            } else {
                $ret = $dbRet['qimoyueyuan'] ? : 0;
            }
        } else {
            $ret = sprintf('%.0f', $data * 100);
        }

        return $ret;
    }
    
    
    /**
     * 导入功能开发
     **/
    protected $location;
    protected $title=[ 'zhonglei','zhanghao','riqi','qichuyueyuan','cunqianguanru',
                       'xianxiachongzhiru','qiyehuchongzhiru','haoyoufanxianru',
                       'shoudaofangkuaneru','daoqibenjinru','daoqilixiru','diaopeizijinru',
                       'jiedongzijinru','tixianchu','shouxufeichu','zhuanzhangzijinchu',
                       'dongjiezijinchu','zhifubenxichu','zhifutoubiaochu','qimoyueyuan',
                       'beizhu'];

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
    
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],FengKongEnum::getInstance()->get('rijizhangzhonglei'));
                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1],\Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('xianshangnicheng'));
                $arr[2]=\Prj\Misc\FengKongImport::checktime($arr[2]);
                
                $result[]=[
                    'zhonglei'=>$arr[0],
                    'zhanghao'=>$arr[1],
                    'riqi'=>$arr[2],
                    'qichuyueyuan'=>$arr[3],
                    'cunqianguanru'=>$arr[4],
                    'xianxiachongzhiru'=>$arr[5],
                    'qiyehuchongzhiru'=>$arr[6],
                    'haoyoufanxianru'=>$arr[7],
                    'shoudaofangkuaneru'=>$arr[8],
                    'daoqibenjinru'=>$arr[9],
                    'daoqilixiru'=>$arr[10],
                    'diaopeizijinru'=>$arr[11],
                    'jiedongzijinru'=>$arr[12],
                    'tixianchu'=>$arr[13], 
                    'shouxufeichu'=>$arr[14],
                    'zhuanzhangzijinchu'=>$arr[15],
                    'dongjiezijinchu'=>$arr[16],
                    'zhifubenxichu'=>$arr[17],
                    'zhifutoubiaochu'=>$arr[18],
                    'qimoyueyuan'=>$arr[19],
                    'beizhu'=>$arr[20],
                ];
    
            }
            
            foreach ($result as $data){
                try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
                    $tmpModel = FKXianShangRiJiZhangModel::getCopy('');
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
                case 'riqi':
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
                case 'zhonglei':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'zhanghao':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKXianShangRiJiZhangModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
