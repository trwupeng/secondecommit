<?php

use \Prj\Data\FKRongZiXiangMuBiao as FkRongZiXiangMuBiaoModel;
use \Prj\Misc\FengKongEnum;
use Sooh\Base\Form\Item as form_def;

/**
 * 融资项目表
 * Class RongzixiangmubiaoController
 * @author lingtm <lingtima@gmail.com>
 */
class RongzixiangmubiaoController extends \Prj\ManagerCtrl
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
            $rongZiHeTongBianHao = $this->_request->get('rongzihetongbianhao');
            !empty($rongZiHeTongBianHao) && $where['rongzihetongbianhao'] = $rongZiHeTongBianHao;
            $bianHao = $this->_request->get('bianhao');
            !empty($bianHao) && $where['bianhao'] = $bianHao;
            $searchId = $this->_request->get('id');
            !empty($searchId) && $where['id'] = $searchId;

            $searchJieKuanRen = $this->_request->get('jiekuanren');
            !empty($searchJieKuanRen) && $where['jiekuanren'] = $searchJieKuanRen;

            $searchQiShiStart = $this->_request->get('qishirishiStart');
            $searchQiShiEnd = $this->_request->get('qishiriqiEnd');
            $searchDaoQiStart = $this->_request->get('daoqiriqiStart');
            $searchDaoQiEnd = $this->_request->get('daoqiriqiEnd');
            !empty($searchQiShiStart) && $where['qishiriqi]'] = strtotime($searchQiShiStart);
            !empty($searchQiShiEnd) && $where['qishiriqi['] = strtotime($searchQiShiEnd);
            !empty($searchDaoQiStart) && $where['daoqiriqi]'] = strtotime($searchDaoQiStart);
            !empty($searchDaoQiEnd) && $where['daoqiriqi['] = strtotime($searchDaoQiEnd);

            $searchFeiYongZhiFu = $this->_request->get('fuxifeifangshi');
            $searchFeiYongZhiFu != '' && $where['fuxifeifangshi'] = $searchFeiYongZhiFu;
            $searchJieQingQingKuang = $this->_request->get('jieqingqingkuang');
            !empty($searchJieQingQingKuang) && $where['jieqingqingkuang'] = $searchJieQingQingKuang;
        }

        if ($isDownloadExcel) {
            $model = FkRongZiXiangMuBiaoModel::getCopy('');
            $data = $model->db()->getRecords($model->tbname(), '*', $where, 'sort createTime');
        } else {
            $data = FkRongZiXiangMuBiaoModel::paged($pager, $where, 'sort createTime');
        }

        $temp = [];
        foreach ($data as $k => &$v) {
            foreach ($v as $key => $var) {
                if (!in_array($key, ['status', 'createTime', 'updateTime', 'iRecordVerID', 'sLockData'])) {
                    $temp[$k][$key] = FkRongZiXiangMuBiaoModel::parseFieldToString($key, $var);
                }
            }
        }

//        foreach ($temp as $k => $v) {
//            //若付息方式为“一次性支付”则显示“-”，否则显示“到期日期”的日期
//            if ($v['fuxifeifangshi'] != 3) {
//                $temp[$k]['fuxiri'] = date('d', $v['fuxiri']);
//            }
//        }

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($temp)
             ->setPager($pager)
             ->setAction('/risk/rongzixiangmubiao/update', '/risk/rongzixiangmubiao/delete?')
             ->addRow('rongzihetongbianhao', '融资合同编号', 'b101', 'text', [], 120)
             ->addRow('jiekuanren', '借款人', 'b102', 'text', [], 60)
             ->addRow('diyaren', '抵押人', 'b103', 'text', [], 60)
             ->addRow('baozhengren', '保证人', 'b104', 'text', [], 60)
             ->addRow('lianxidianhua', '联系电话', 'b105', 'text', [], 110)
             ->addRow('bianhao', '编号', 'b106', 'text', [], 120)
             ->addRow('jiekuanewanyuan', '借款额[万元]', 'b107', 'text', [], 100)
             ->addRow('qishiriqi', '起始日期', 'b108', 'datepicker', [], 120)
             ->addRow('yue', '月', 'b109', 'text', [], 30)
             ->addRow('tian', '天', 'b110', 'text', [], 30)
             ->addRow('daoqiriqi', '到期日期', 'b111', 'datepicker', [], 120)
             ->addRow('fuxifeifangshi', '付息费方式', 'b112', 'select', FengKongEnum::getInstance()
                                                                               ->get('fuxifeifangshi'), 120)
             ->addRow('fuxiri', '付息日', 'b113', 'text', [], 60)
             ->addRow('yewuleixing', '业务类型', 'b114', 'select', FengKongEnum::getInstance()->get('yewuleixing'), 70)
             ->addRow('kehuleixing', '客户类型', 'b115', 'select', [1 => '新客户', 3 => '老客户结清再办', 4 => '老客户展期'], 70)
             ->addRow('kehulaiyuan', '客户来源', 'b116', 'select', FengKongEnum::getInstance()->get('kehulaiyuan'), 70)
             ->addRow('lixiyue', '利息<br>月[%]', 'b117', 'text', [], 80)
             ->addRow('lixiyingshou', '利息<br>应收[%]', 'b118', 'text', [], 120)
             ->addRow('lixiyingshouyuan', '利息<br>应收[元]', 'b119', 'text', [], 80)
             ->addRow('lixishishouyuan', '利息<br>实收[元]', 'b120', 'text', [], 80)
             ->addRow('fuwufeianyue', '服务费<br>按月[%]', 'b121', 'text', [], 80)
             ->addRow('fuwufeianyueyingshou', '服务费<br>按月应收[元]', 'b1211', 'text', [], 100)
             ->addRow('fuwufeiyicixingyue', '服务费<br>一次性[%/月]', 'b122', 'text', [], 150)
             ->addRow('fuwufeiyingshouyuan', '服务费<br>应收[元]', 'b123', 'text', [], 80)
             ->addRow('fuwufeishishouyuan', '服务费<br>实收[元]', 'b124', 'text', [], 80)
             ->addRow('dianzifeijineyuan', '垫资费<br>金额[元]', 'b125', 'text', [], 80)
             ->addRow('dianzifeibilv', '垫资费<br>比率[%]', 'b126', 'text', [], 80)
             ->addRow('zhongjiefeiyue', '中介费<br>月[%]', 'b127', 'text', [], 80)
             ->addRow('zhongjiefeizong', '中介费<br>总[%]', 'b128', 'text', [], 80)
             ->addRow('zhongjiefeiyingshouyuan', '中介费<br>应收[元]', 'b129', 'text', [], 80)
             ->addRow('zhongjiefeishishouyuan', '中介费<br>实收[元]', 'b130', 'text', [], 80)
             ->addRow('zongheyue', '综合<br>[%/月]', 'b131', 'text', [], 80)
             ->addRow('yuexifeilv', '月息费率<br>[%]', 'b132', 'text', [], 90)
             ->addRow('baozhengjin', '保证金<br>[%]', 'b133', 'text', [], 80)
             ->addRow('baozhengjinyuan', '保证金[元]', 'b134', 'text', [], 60)
             ->addRow('fangchanleixing', '房产类型', 'b135', 'select', FengKongEnum::getInstance()
                                                                               ->get('fangchanleixing'), 100)
             ->addRow('fangchanquyu', '房产区域', 'b136', 'select', FengKongEnum::getInstance()->get('fangchanquyu'), 100)
             ->addRow('fangchanweizhi', '房产位置', 'b137', 'text', [], 150)
             ->addRow('gongzheng', '公证', 'b138', 'select', FengKongEnum::getInstance()->get('gongzheng'), 40)
             ->addRow('quanweidaoqiri', '全委[到期日]', 'b139', 'text', [], 120)
             ->addRow('fangkuanrenyinhang', '放款人[银行]', 'b140', 'select', \Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanrenyinhang'), 120)
             ->addRow('kehujingli', '客户经理', 'b141', 'select', \Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'), 80)
             ->addRow('tuandui', '团队', 'b142', 'text', [], 60)
             ->addRow('fengkongjingli', '风控经理', 'b143', 'selects', \Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming'), 80)
             ->addRow('jieqingqingkuang', '结清情况', 'b144', 'select', FengKongEnum::getInstance()
                                                                                ->get('jieqingqingkuang'), 60)
             ->addRow('jieqingriqi', '结清日期', 'b145', 'datepicker', [], 120)
             ->addRow('beizhu', '备注', 'b146', 'text', [], 200)
             ->addTab('tab1', '融资档案', '/risk/rongzidangan/index?', 'rongzihetongbianhao', 'rongzihetongbianhao')
             ->addTab('tab2', '线下本息费账', '/risk/xianxiabenxifeizhang/index?', 'rongzihetongbianhao', 'rongzihetongbianhao')
             ->addTab('tab3', '线下日记账', '/risk/xianxiarijizhang/index?', 'hetongbianhao', 'rongzihetongbianhao')
             ->addTab('tab4', '放款人名册', '/risk/fangkuanrenmingce/index?', 'selectedFangkuanrenyinhang', 'fangkuanrenyinhang')
             ->addTab('tab5', '客户经理名册', '/risk/kehujinglimingce/index?', 'id', 'kehujingli')
             ->addTab('tab6', '风控经理名册', '/risk/fengkongjinglimingce/index?', 'id', 'fengkongjingli')
             ->addTab('tab6', '融资客户名册', '/risk/rongzikehumingce/index?', 'bianhao', 'bianhao');

        if ($isDownloadExcel) {
            $excel = $view->toExcel($temp);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        $this->_view->assign('_type', $this->_request->get('_type'));
        $this->_view->assign('where', $where);
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

                $tmpModel = FkRongZiXiangMuBiaoModel::getCopy('');
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
            $model = FkRongZiXiangMuBiaoModel::getCopy($key);
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

        $model = FkRongZiXiangMuBiaoModel::getCopy($key);
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
     * @param     array           $data
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
        $tmpDaoQiRiQi = 0;
        foreach ($data as $k => $v) {
            switch ($k) {
                case 'bianhao':
                    $ret[$k] = intval($v ? : 0);
                    break;
                case 'daoqiriqi':
                    if (!empty($data['qishiriqi'])) {
                        //当起始日期不为空，则到期日期为起始日期加上期限的对应日（月、天）减一天
                        $tmpDaoQiRiQi = FkRongZiXiangMuBiaoModel::formatTimeAdd(strtotime($data['qishiriqi']), '+' . intval($data['yue']), '+' . intval($data['tian']));
                        $tmpDaoQiRiQi = strtotime('-1 day', $tmpDaoQiRiQi);
                    } else {
                        $tmpDaoQiRiQi = $v ? strtotime($v) : 0;
                    }
                    $ret[$k] = $tmpDaoQiRiQi;
                    break;
                case 'fuxiri':
                    if ($data['fuxifeifangshi'] == 3) {
                        $ret[$k] = '-';
                    } else {
                        $ret[$k] = $v;
                    }
                    break;
                case 'fangkuanrenyinhang':
                    $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy('');
                    $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpRet)) {
                        throw new \Exception('放款人银行在[放款人名册]中不存在', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'kehujingli':
                    $tmpModel = \Prj\Data\FKKeHuJingLiMingCe::getCopy('');
                    $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $v]);
                    if (empty($tmpRet)) {
                        throw new \Exception('客户经理在[客户经理名册]中不存在', 90003);
                    }
                    $ret[$k] = $v;
                    break;
                case 'fengkongjingli':
                    if (!is_array($v)) {
                        throw new \Exception('风控经理在[风控经理名册]中不存在', 90003);
                    }
                    if (empty($v)) {
                        $ret[$k] = '';
                        break;
                    }

                    $tmpArr = [];
                    foreach ($v as $_v) {
                        $tmpModel = \Prj\Data\FKFengKongJingLiMingCe::getCopy('');
                        $tmpRet = $tmpModel->db()->getRecord($tmpModel->tbname(), '*', ['id' => $_v]);
                        if (empty($tmpRet)) {
                            throw new \Exception('风控经理在[风控经理名册]中不存在', 90003);
                        }
                        $tmpArr[] = $_v;
                    }
                    $ret[$k] = json_encode($tmpArr);
                    break;
                default:
                    if (($tmpV = FkRongZiXiangMuBiaoModel::parseStringToField($k, $v)) === false) {
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
    protected $location=['融资合同编号','借款人','抵押人','保证人','联系电话','编号','借款额[万元]','起始日期','月','天','到期日期',
                         '付息费方式','付息日','业务类型','客户类型','客户来源','利息<br>月[%]','利息<br>应收[%]','利息<br>应收[元]','利息<br>实收[元]',
                         '服务费<br>按月[%]','服务费<br>按月应收[元]','服务费<br>一次性[%/月]','服务费<br>应收[元]','服务费<br>实收[元]',
                         '垫资费<br>金额[元]','垫资费<br>比率[%]','中介费<br>月[%]','中介费<br>总[%]','中介费<br>应收[元]','中介费<br>实收[元]',
                         '综合<br>[%/月]','月息费率<br>[%]','保证金<br>[%]','保证金[元]','房产类型','房产区域','房产位置','公证','全委[到期日]',
                         '放款人[银行]','客户经理','团队','风控经理','结清情况','结清日期','备注',
                         ];

    protected $title=['rongzihetongbianhao','jiekuanren','diyaren','baozhengren','lianxidianhua','bianhao',
                    'jiekuanewanyuan','qishiriqi','yue','tian','daoqiriqi','fuxifeifangshi','fuxiri',
                    'yewuleixing','kehuleixing','kehulaiyuan','lixiyue','lixiyingshou','lixiyingshouyuan',
                    'lixishishouyuan','fuwufeianyue','fuwufeianyueyingshou','fuwufeiyicixingyue','fuwufeiyingshouyuan',
                    'fuwufeishishouyuan','dianzifeijineyuan','dianzifeibilv','zhongjiefeiyue','zhongjiefeizong',
                     'zhongjiefeiyingshouyuan','zhongjiefeishishouyuan','zongheyue','yuexifeilv','baozhengjin',
                    'baozhengjinyuan','fangchanleixing','fangchanquyu','fangchanweizhi','gongzheng','quanweidaoqiri',
                    'fangkuanrenyinhang','kehujingli','tuandui','fengkongjingli','jieqingqingkuang','jieqingriqi',
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
    
            $ret=explode("\r\n",$rs);
            $first=current($ret);
            $last=end($ret);
            
            foreach ($ret as $k=>$v){
                if($v==$first ||$v==$last || $k==1){
                    continue;
                }
                $rem[]=$v;
            }

            foreach ($rem as $v){
                $arr=preg_split("/[\t]/",$v);

                $arr[7]=\Prj\Misc\FengKongImport::checktime($arr[7]);
                $arr[10]=\Prj\Misc\FengKongImport::checktime($arr[10]);
                $arr[45]=\Prj\Misc\FengKongImport::checktime($arr[45]);

                var_log(FengKongEnum::getInstance()->get('yewuleixing'));
                $arr[11]=\Prj\Misc\FengKongImport::transvk($arr[11],FengKongEnum::getInstance()->get('fuxifeifangshi'));
                $arr[13]=\Prj\Misc\FengKongImport::transvk($arr[13],FengKongEnum::getInstance()->get('yewuleixing'));
                $arr[14]=\Prj\Misc\FengKongImport::transvk($arr[14],FengKongEnum::getInstance()->get('kehuleixing'));
                $arr[15]=\Prj\Misc\FengKongImport::transvk($arr[15],FengKongEnum::getInstance()->get('kehulaiyuan'));
                $arr[35]=\Prj\Misc\FengKongImport::transvk($arr[35],FengKongEnum::getInstance()->get('fangchanleixing'));
                $arr[36]=\Prj\Misc\FengKongImport::transvk($arr[36],FengKongEnum::getInstance()->get('fangchanquyu'));
                $arr[38]=\Prj\Misc\FengKongImport::transvk($arr[38],FengKongEnum::getInstance()->get('gongzheng'));
                $arr[40]=\Prj\Misc\FengKongImport::transvk($arr[40],\Prj\Data\FKFangKuanRenMingCe::getFieldForEnum('fangkuanrenyinhang'));
                $arr[41]=\Prj\Misc\FengKongImport::transvk($arr[41],\Prj\Data\FKKeHuJingLiMingCe::getFieldForEnum('xingming'));
                $arr[43]=\Prj\Misc\FengKongImport::transv2k($arr[43],\Prj\Data\FKFengKongJingLiMingCe::getFieldForEnum('xingming'));
                $arr[44]=\Prj\Misc\FengKongImport::transvk($arr[44],FengKongEnum::getInstance()->get('jieqingqingkuang'));
                var_log($arr,'arr###############');
                $result[]=[
                    'rongzihetongbianhao'=>$arr[0],
                    'jiekuanren'=>$arr[1],
                    'diyaren'=>$arr[2],
                    'baozhengren'=>$arr[3],
                    'lianxidianhua'=>$arr[4],
                    'bianhao'=>$arr[5],
                    'jiekuanewanyuan'=>$arr[6],
                    'qishiriqi'=>$arr[7],
                    'yue'=>$arr[8],
                    'tian'=>$arr[9],
                    'daoqiriqi'=>$arr[10],
                    'fuxifeifangshi'=>$arr[11],
                    'fuxiri'=>$arr[12],
                    'yewuleixing'=>$arr[13],
                    'kehuleixing'=>$arr[14],
                    'kehulaiyuan'=>$arr[15],
                    'lixiyue'=>$arr[16],
                    'lixiyingshou'=>$arr[17],
                    'lixiyingshouyuan'=>$arr[18],
                    'lixishishouyuan'=>$arr[19],
                    'fuwufeianyue'=>$arr[20],
                    'fuwufeianyueyingshou'=>$arr[21],
                    'fuwufeiyicixingyue'=>$arr[22],
                    'fuwufeiyingshouyuan'=>$arr[23],
                    'fuwufeishishouyuan'=>$arr[24],
                    'dianzifeijineyuan'=>$arr[25],
                    'dianzifeibilv'=>$arr[26],
                    'zhongjiefeiyue'=>$arr[27],
                    'zhongjiefeizong'=>$arr[28],
                    'zhongjiefeiyingshouyuan'=>$arr[29],
                    'zhongjiefeishishouyuan'=>$arr[30],
                    'zongheyue'=>$arr[31],
                    'yuexifeilv'=>$arr[32],
                    'baozhengjin'=>$arr[33],
                    'baozhengjinyuan'=>$arr[34],
                    'fangchanleixing'=>$arr[35],
                    'fangchanquyu'=>$arr[36],
                    'fangchanweizhi'=>$arr[37],
                    'gongzheng'=>$arr[38],
                    'quanweidaoqiri'=>$arr[39],
                    'fangkuanrenyinhang'=>$arr[40],
                    'kehujingli'=>$arr[41],
                    'tuandui'=>$arr[42],
                    'fengkongjingli'=>$arr[43],
                    'jieqingqingkuang'=>$arr[44],
                    'jieqingriqi'=>$arr[45],
                    'beizhu'=>$arr[46],
                ];
    
            }
     
            foreach ($result as $data){
             try {
                $records = $this->formatFields($data);
                $records['createTime'] = $records['updateTime'] = time();
                $records['status'] = 1;

                $tmpModel = FkRongZiXiangMuBiaoModel::getCopy('');
                $ret = $tmpModel->db()->addRecord($tmpModel->tbname(), $records);
                $this->_view->assign('_id', $ret);
             } catch (\Exception $e) {
                if ($e->getCode() >= 90000) {
                    $this->returnError($e->getMessage());
                    return 0;
                } else {
                    $this->returnError('新增失败');
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
        $tmp=0;
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
                case 'fuxifeifangshi':
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
                case 'kehuleixing':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'kehulaiyuan':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangchanleixing':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'fangchanquyu':
                    if($v==2000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $ret[$k]=$v;
                    break;
                case 'gongzheng':
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
                case 'kehujingli':
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
                case 'fengkongjingli':
                    if($v==3000){
                        $name=\Prj\Misc\FengKongImport::location($this->location,$k,$this->title);
                        throw new \Exception($name.'下拉选择条件不存在此类别', 90003);
                    }
                    $tmp=json_encode($v);
                    $ret[$k]=$tmp;
                    break;
                 default:
                    if (($tmpV = FkRongZiXiangMuBiaoModel::parseStringToField($k, $v)) === false) {
                        throw new \Exception('表单值不合法', 90001);
                    }
                    $ret[$k] = $tmpV;
            }
        }
        return $ret;
    }
}