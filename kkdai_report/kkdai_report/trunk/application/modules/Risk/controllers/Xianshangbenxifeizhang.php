<?php

use \Prj\Data\FKXianShangBenXiFeiZhang as FKXianShangBenXiFeiZhangModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;
/**
 * 线上本息费账
 * Class XianshangbenxifeizhangController
 * @author lingtm <lingtima@gmail.com>
 */
class XianshangbenxifeizhangController extends \Prj\ManagerCtrl
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
            $whereBiaoDiMingCheng = $this->_request->get('biaodimingcheng');
            if (!empty($whereBiaoDiMingCheng)) {
                $tmpModel = \Prj\Data\FKXianShangXiangMuBiao::getCopy('');
                $tmpMRet = $tmpModel->db()
                                    ->getRecord($tmpModel->tbname(), 'id', ['biaodimingcheng' => $whereBiaoDiMingCheng]);
                if (!empty($tmpMRet)) {
                    $map['biaodimingcheng'] = $tmpMRet['id'];
                    $where['biaodimingcheng'] = $whereBiaoDiMingCheng;
                } else {
                    $map['biaodimingcheng'] = -1;
                }
            }
        }

        if ($isDownloadExcel) {
            $model = FKXianShangBenXiFeiZhangModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $map, 'sort createTime');
        } else {
            $data = FKXianShangBenXiFeiZhangModel::paged($pager, $map, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FKXianShangBenXiFeiZhangModel::parseFieldToString($key, $var);
                }
            }
        }

        //跨表查询借款人信息
        $tmpJieKuanRen = function () {
            $model = \Prj\Data\FKXianShangXiangMuBiao::getCopy('');
            $ret = $model->db()->getPair($model->tbname(), 'id', 'jiekuanren');
            //            var_log($ret, 'ret');
            if (!empty($ret)) {
                $ids = implode(',', $ret);
                $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                $tmpRet = $tmpModel->db()
                                   ->getRecords($tmpModel->tbname(), 'id,fangkuanren', ['id' => explode(',', $ids)]);
                //                var_log($tmpRet, 'tmpRet');
                if (!empty($tmpRet)) {
                    $kvRet = [];
                    foreach ($tmpRet as $k => $v) {
                        $kvRet[$v['id']] = $v['fangkuanren'];
                    }
                    //                    var_log($kvRet, 'kvRet');

                    $result = [];
                    foreach ($ret as $key => $value) {
                        if (isset($kvRet[$value])) {
                            $result[$key] = $kvRet[$value];
                        }
                    }
                    return $result;
                }
            }

            return [];
        };

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/xianshangbenxifeizhang/update', '/risk/xianshangbenxifeizhang/delete?')
             ->addRow('biaodimingcheng', '标的名称', 'x201', 'select', \Prj\Data\FKXianShangXiangMuBiao::getFieldForEnum('biaodimingcheng'), 150)
             ->addRow('jiekuanren', '借款人', 'x202', 'select', $tmpJieKuanRen(), 100)
             ->addRow('qishu', '期数', 'x203', 'text', [], 60)
             ->addRow('zhifushijian', '支付时间', 'x204', 'datepicker', [], 120)
             ->addRow('lixiyuan', '利息[元]', 'x205', 'text', [], 80)
             ->addRow('feiyongyuanguanlifei', '费用[元]_管理费', 'x206', 'text', [], 100)
             ->addRow('feiyongyuanzhongjiefei', '费用[元]_中介费', 'x207', 'text', [], 100)
             ->addRow('feiyongyuanfuwufei', '费用[元]_服务费', 'x208', 'text', [], 100)
             ->addRow('feiyongyuanqita', '费用[元]_其他', 'x209', 'text', [], 100)
             ->addRow('hejiyuan', '合计[元]', 'x210', 'text', [], 80)
             ->addRow('haikuanqingkuang', '还款情况', 'x211', 'select', FengKongEnum::getInstance()
                                                                                ->get('huankuanqingkuang'), 60)
             ->addRow('beizhu', '备注', 'x212', 'text', [], 150)
             ->addTab('tab1', '线上项目表', '/risk/xianshangxiangmubiao/index?', 'biaodiId', 'biaodimingcheng');

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

                $tmpModel = FKXianShangBenXiFeiZhangModel::getCopy('');
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
            $model = FKXianShangBenXiFeiZhangModel::getCopy($key);
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

        $model = FKXianShangBenXiFeiZhangModel::getCopy($key);
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
     * @param            array    $data
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
        $tmpGuanLiFei = false;

        foreach ($data as $k => $v) {
            switch ($k) {
                case 'biaodimingcheng':
                    if (empty($v)) {
                        $ret[$k] = '';
                        break;
                    }
                    $ModelXiangShangXiangMuBiao = \Prj\Data\FKXianShangXiangMuBiao::getCopy('');
                    $tmpMRet = $ModelXiangShangXiangMuBiao->db()
                                                          ->getRecord($ModelXiangShangXiangMuBiao->tbname(), '*', ['id' => $v]);
                    if (empty($tmpMRet)) {
                        throw new \Exception('标的名称在[线上项目表]中不存在', 90003);
                    }

                    $ret[$k] = $v;
                    break;
                case 'jiekuanren':
                    if (!isset($tmpMRet)) {
                        $ret[$k] = '';
                        break;
                    }

                    $ret[$k] = $tmpMRet['id'] ? : '';
                    break;
                case 'lixiyuan':
                    //                    若 标的名称 为空,则 利息 与 管理费 为空,否则
                    //                    '若 含 定期宝,   利息 = 线上项目表 年利率/12*标的金额,       管理费 = 标的金额*0.005
                    //                    '若 含 新手标,   利息 = 线上项目表 年利率/360*天数*标的金额, 管理费 = 0
                    //                    '若 含 房宝宝,   利息 = 线上项目表 年利率/372*天数*标的金额, 管理费 = 0
                    //                    '若 含 体验标,   利息 = 线上项目表 年利率/372*天数*标的金额, 管理费 = 0
                    //                    '若 含 快快享福, 利息 = 线上项目表 年利率/372*天数*标的金额, 管理费 = 0
                    $retBiaoDiMingCheng = $tmpMRet['biaodimingcheng'];
                    if ($ret['biaodimingcheng'] == '' || empty($retBiaoDiMingCheng)) {
                        $ret[$k] = $tmpGuanLiFei = 0;
                        break;
                    }

                    if (mb_strpos($retBiaoDiMingCheng, '定期宝', 0, 'utf8') !== false) {
                        $ret[$k] = sprintf('%.0f', round($tmpMRet['nianlilv'] * $tmpMRet['biaodijineyuan'] / 120000));
                        $tmpGuanLiFei = sprintf('%.0f', round($tmpMRet['biaodijineyuan'] * 0.005));
                    } else if (mb_strpos($retBiaoDiMingCheng, '新手标', 0, 'utf8') !== false) {
                        $ret[$k] = sprintf('%.0f', round($tmpMRet['nianlilv'] * $tmpMRet['biaodijineyuan'] * $tmpMRet['tian'] / 3600000));
                        $tmpGuanLiFei = 0;
                    } else if (mb_strpos($retBiaoDiMingCheng, '房宝宝', 0, 'utf8') !== false) {
                        $ret[$k] = sprintf('%.0f', round($tmpMRet['nianlilv'] * $tmpMRet['biaodijineyuan'] * $tmpMRet['tian'] / 3720000));
                        $tmpGuanLiFei = 0;
                    } else if (mb_strpos($retBiaoDiMingCheng, '体验标', 0, 'utf8') !== false) {
                        $ret[$k] = sprintf('%.0f', round($tmpMRet['nianlilv'] * $tmpMRet['biaodijineyuan'] * $tmpMRet['tian'] / 3720000));
                        $tmpGuanLiFei = 0;
                    } else if (mb_strpos($retBiaoDiMingCheng, '快快享福', 0, 'utf8') !== false) {
                        $ret[$k] = sprintf('%.0f', round($tmpMRet['nianlilv'] * $tmpMRet['biaodijineyuan'] * $tmpMRet['tian'] / 3720000));
                        $tmpGuanLiFei = 0;
                    } else {
                        error_log('warning! lixiyuan has something warning happend');
                        $ret[$k] = $tmpGuanLiFei = 0;
                    }
                    break;
                case 'feiyongyuanguanlifei':
                    $ret[$k] = $tmpGuanLiFei ? : 0;
                    break;
                default:
                    if (($tmpV = FKXianShangBenXiFeiZhangModel::parseStringToField($k, $v)) === false) {
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
    protected $title=[ 'biaodimingcheng','jiekuanren','qishu','zhifushijian',
                       'lixiyuan','feiyongyuanguanlifei','feiyongyuanzhongjiefei',
                       'feiyongyuanfuwufei','feiyongyuanqita','hejiyuan', 'haikuanqingkuang',
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
                
                //跨表查询借款人信息
                $tmpJieKuanRen = function () {
                    $model = \Prj\Data\FKXianShangXiangMuBiao::getCopy('');
                    $ret = $model->db()->getPair($model->tbname(), 'id', 'jiekuanren');
                    //            var_log($ret, 'ret');
                    if (!empty($ret)) {
                        $ids = implode(',', $ret);
                        $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                        $tmpRet = $tmpModel->db()
                        ->getRecords($tmpModel->tbname(), 'id,fangkuanren', ['id' => explode(',', $ids)]);
                        //                var_log($tmpRet, 'tmpRet');
                        if (!empty($tmpRet)) {
                            $kvRet = [];
                            foreach ($tmpRet as $k => $v) {
                                $kvRet[$v['id']] = $v['fangkuanren'];
                            }
                            //                    var_log($kvRet, 'kvRet');
                
                            $result = [];
                            foreach ($ret as $key => $value) {
                                if (isset($kvRet[$value])) {
                                    $result[$key] = $kvRet[$value];
                                }
                            }
                            return $result;
                        }
                    }
                
                    return [];
                };

                $arr[3]=\Prj\Misc\FengKongImport::checktime($arr[3]);
                
                $arr[0]=\Prj\Misc\FengKongImport::transvk($arr[0],\Prj\Data\FKXianShangXiangMuBiao::getFieldForEnum('biaodimingcheng'));
                $arr[1]=\Prj\Misc\FengKongImport::transvk($arr[1], $tmpJieKuanRen());
                $arr[10]=\Prj\Misc\FengKongImport::transvk($arr[10], FengKongEnum::getInstance()->get('huankuanqingkuang'));
                
                $result[]=[
                    'biaodimingcheng'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'qishu'=>$arr[2],
                    'zhifushijian'=>$arr[3],
                    'lixiyuan'=>$arr[4],
                    'feiyongyuanguanlifei'=>$arr[5],
                    'feiyongyuanzhongjiefei'=>$arr[6],
                    'feiyongyuanfuwufei'=>$arr[7],
                    'feiyongyuanqita'=>$arr[8],
                    'hejiyuan'=>$arr[9],
                    'haikuanqingkuang'=>$arr[10],
                    'beizhu'=>$arr[11],
                ];
    
            }

            foreach ($result as $data){
              
                try {
                    $records = $this->formatFields($data);
                    $records['createTime'] = $records['updateTime'] = time();
                    $records['status'] = 1;
    
                    $tmpModel = FKXianShangBenXiFeiZhangModel::getCopy('');
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
                case 'zhifushijian':
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
                case 'biaodimingcheng':
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
                case 'haikuanqingkuang':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                default:
                    if (($tmpV = FKXianShangBenXiFeiZhangModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}
