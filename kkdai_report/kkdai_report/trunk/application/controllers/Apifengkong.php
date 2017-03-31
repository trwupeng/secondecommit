<?php

/**
 * 风控相关接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/23 0026
 */
class ApifengkongController extends \Yaf_Controller_Abstract
{

    /**
     * 放款提奖表获取融资合同编号信息的接口
     */
    public function onhetongbianhaoAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }
        $model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($id);
        $model->load('jiekuanren,qishiriqi,jiekuanewanyuan,yue');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();
        if (!empty($tmp['jiekuanren'])) {
            $ret['b802'] = $tmp['jiekuanren'];
        } else {
            $ret['b802'] = '';
        }


        if (!empty($tmp['qishiriqi'])) {
            $ret['b803'] = date('Y-m-d', $tmp['qishiriqi']);
        } else {
            $ret['b803'] = '';
        }

        if ($tmp['jiekuanewanyuan'] > 0) {
            $ret['b804'] = $tmp['jiekuanewanyuan'] * 0.000001;
        } else {
            $ret['b804'] = '';
        }
        if (!empty($tmp['yue'])) {
            $ret['b805'] = $tmp['yue'];
        } else {
            $ret['b805'] = '';
        }

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    /***
     * 线下本息费账获取融资合同编号信息的接口
     ***/
    public function onxianxiabenxifeizhangAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }
        $model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($id);
        $model->load('jiekuanren');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();

        if (!empty($tmp['jiekuanren'])) {
            $ret['z102'] = $tmp['jiekuanren'];
        } else {
            $ret['z102'] = '';
        }

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    /**
     * 融资档案-融资合同编号联动
     */
    public function onb201Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($id);
        $model->load('jiekuanren,jiekuanewanyuan');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();

        $ret['b202'] = !empty($tmp['jiekuanren']) ? $tmp['jiekuanren'] : '';
        $ret['b203'] = !empty($tmp['jiekuanewanyuan']) ? $tmp['jiekuanewanyuan'] / 1000000 : '';

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    public function onb701Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $model = \Prj\Data\FKTouZiXiangMuBiao::getCopy($id);
        $model->load('touziren');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();

        $ret['b702'] = !empty($tmp['touziren']) ? $tmp['touziren'] : '';

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    public function onz301Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $model = \Prj\Data\FKFangKuanTiJiangBiao::getCopy($id);
        $model->load('rongzihetongbianhao,jiekuanren,kehujingli,gerenyuliuyuan,jinglixingming,jingliyuliu,zongjianxingming,zongjianyuliu,fangkuanshijian,qixianyue');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();

        $ret['z303'] = $tmp['kehujingli'];
        $ret['z304'] = sprintf('%.02f', $tmp['gerenyuliuyuan'] / 100);
        $ret['z306'] = $tmp['jinglixingming'];
        $ret['z307'] = sprintf('%.02f', $tmp['jingliyuliu'] / 100);
        $ret['z309'] = $tmp['zongjianxingming'];
        $ret['z310'] = sprintf('%.02f', $tmp['zongjianyuliu'] / 100);

        $rongzihetongbianhao = $tmp['rongzihetongbianhao'];
        $_model = \Prj\Data\FKRongZiXiangMuBiao::getCopy($rongzihetongbianhao);
        $_model->load();
        if ($_model->exists()) {
            $ret['z302'] = $rongzihetongbianhao;
            $ret['z312'] = date('Y-m-d', $_model->getField('qishiriqi'));
            $ret['z313'] = $_model->getField('yue');
        } else {
            $ret['z302'] = '';
            $ret['z312'] = '';
            $ret['z313'] = '';
        }

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    public function onx103Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        $ret = [];
        if (empty($id)) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $model = \Prj\Data\FKFangKuanRenMingCe::getCopy($id);
        $model->load('xianshangnicheng');
        if (!$model->exists()) {
            $this->_view->assign('hetongbianhaoInfo', $ret);
            return;
        }

        $tmp = $model->dump();

        $ret['x104'] = !empty($tmp['xianshangnicheng']) ? $tmp['xianshangnicheng'] : '';

        $this->_view->assign('hetongbianhaoInfo', $ret);
        return;
    }

    public function onx201Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $id = $this->_request->get('id');
        if (!empty($id)) {
            $model = \Prj\Data\FKXianShangXiangMuBiao::getCopy($id);
            $model->load('id,jiekuanren,biaodimingcheng,nianlilv,biaodijineyuan,tian');
            if ($model->exists()) {
                $tmp = $model->dump();
//                $tmpModel = \Prj\Data\FKFangKuanRenMingCe::getCopy($tmp['jiekuanren']);
//                $tmpModel->load('fangkuanren');
//                if ($tmpModel->exists()) {
//                    $tmpRet = $tmpModel->dump();
//                    $this->_view->assign('hetongbianhaoInfo', ['x202' => $tmpRet['fangkuanren']]);
//                    return;
//                }

                $retBiaoDiMingCheng = $tmp['biaodimingcheng'];

                if (mb_strpos($retBiaoDiMingCheng, '定期宝', 0, 'utf8') !== false) {
                    $lixiyuan = round($tmp['nianlilv'] * $tmp['biaodijineyuan'] / 12000000, 2);
                    $guanlifei = round($tmp['biaodijineyuan'] * 0.00005, 2);
                } else if (mb_strpos($retBiaoDiMingCheng, '新手标', 0, 'utf8') !== false) {
                    $lixiyuan = round($tmp['nianlilv'] * $tmp['biaodijineyuan'] * $tmp['tian'] / 360000000, 2);
                    $guanlifei = 0;
                } else if (mb_strpos($retBiaoDiMingCheng, '房宝宝', 0, 'utf8') !== false) {
                    $lixiyuan = round($tmp['nianlilv'] * $tmp['biaodijineyuan'] * $tmp['tian'] / 372000000, 2);
                    $guanlifei = 0;
                } else if (mb_strpos($retBiaoDiMingCheng, '体验标', 0, 'utf8') !== false) {
                    $lixiyuan = round($tmp['nianlilv'] * $tmp['biaodijineyuan'] * $tmp['tian'] / 372000000, 2);
                    $guanlifei = 0;
                } else if (mb_strpos($retBiaoDiMingCheng, '快快享福', 0, 'utf8') !== false) {
                    $lixiyuan = round($tmp['nianlilv'] * $tmp['biaodijineyuan'] * $tmp['tian'] / 372000000, 2);
                    $guanlifei = 0;
                } else {
                    $lixiyuan = $guanlifei = 0;
                }

                $lixiyuan != 0 && $lixiyuan = sprintf('%.02f', $lixiyuan);
                $guanlifei != 0 && $guanlifei = sprintf('%.02f', $guanlifei);

                $this->_view->assign('hetongbianhaoInfo', ['x202' => $tmp['id'], 'x205' => $lixiyuan, 'x206' => $guanlifei]);
                return;
            }
        }

        $this->_view->assign('hetongbianhaoInfo', []);
        return;
    }

    /**
     * 当起始日期不为空，则到期日期为起始日期加上期限的对应日（月、天）减一天
     */
    public function onb111Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $qishi = $this->_request->get('daoqiriqi');
        $month = $this->_request->get('yue', 0);
        $day = $this->_request->get('tian', 0);

        is_numeric($month) || $month = 0;
        is_numeric($day) || $day = 0;

        if (empty($qishi)) {
            $this->_view->assign('hetongbianhaoInfo', ['b111' => '']);
        } else {
            $tmp = \Prj\Misc\AFengKongFormat::formatTimeAdd(strtotime($qishi), empty($month) ? '' : "+$month", empty($day) ? '' : "+$day");
            $this->_view->assign('hetongbianhaoInfo', ['b111' => date('Y-m-d', strtotime('-1 day', $tmp))]);
        }
        return;
    }

    public function onb113Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $qishi = $this->_request->get('daoqiriqi');
        $month = $this->_request->get('yue', 0);
        $day = $this->_request->get('tian', 0);
        $fangshi = $this->_request->get('fuxifeifangshi');

        is_numeric($month) || $month = 0;
        is_numeric($day) || $day = 0;

        if (empty($qishi)) {
            $tmp = 0;
        } else {
            $tmp = \Prj\Misc\AFengKongFormat::formatTimeAdd(strtotime($qishi), empty($month) ? '' : "+$month", empty($day) ? '' : "+$day");
        }

        if ($fangshi == 3) {
            $fuxiri = '-';
        } else {
            $fuxiri = empty($tmp) ? 0 : date('j', strtotime('-1 day', $tmp));
        }

        if ($tmp == 0) {
            $data = ['b111' => '', 'b113' => $fuxiri];
        } else {
            $data = ['b111' => date('Y-m-d', strtotime('-1 day', $tmp)), 'b113' => $fuxiri];
        }

        $this->_view->assign('hetongbianhaoInfo', $data);
        return;
    }

    /**
     * 若起始日期不为空,则到期日期 为起始日期加上期限的对应日
     */
    public function onb707Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $qishiriqi = $this->_request->get('qishiriqi');
        $month = $this->_request->get('yue', 0);
        $day = $this->_request->get('tian', 0);

        if (empty($qishiriqi)) {
            $this->_view->assign('hetongbianhaoInfo', ['b707' => '']);
        } else {
            $tmp = \Prj\Misc\AFengKongFormat::formatTimeAdd(strtotime($qishiriqi), "+$month", "+$day");
            $this->_view->assign('hetongbianhaoInfo', ['b707' => date('Y-m-d', $tmp)]);
        }
        return;
    }

    /**
     * 当起始日期不为空，则 到期日期 为起始日期加上期限的对应日
     */
    public function onx117Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $qishiriqi = $this->_request->get('fangkuanriqi');
        $month = $this->_request->get('yue', 0);
        $day = $this->_request->get('tian', 0);

        if (empty($qishiriqi)) {
            $this->_view->assign('hetongbianhaoInfo', ['x117' => '']);
        } else {
            $tmp = \Prj\Misc\AFengKongFormat::formatTimeAdd(strtotime($qishiriqi), "+$month", "+$day");
            $this->_view->assign('hetongbianhaoInfo', ['x117' => date('Y-m-d', $tmp)]);
        }
        return;
    }

    /**
     * 若 账号 不为空，则 期初余额 等于该账号上一行中的 期末余额，否则 期初余额 等于0
     */
    public function onx302Action()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $zhanghao = $this->_request->get('zhanghao');

        if (empty($zhanghao)) {
            $this->_view->assign('hetongbianhaoInfo', ['x304' => 0]);
        } else {
            $model = \Prj\Data\FKXianShangRiJiZhang::getCopy('');
            $ret = $model->db()->getRecord($model->tbname(), '*', ['zhanghao' => $zhanghao], 'rsort createTime');
            if (empty($ret)) {
                $this->_view->assign('hetongbianhaoInfo', ['x304' => 0]);
            } else {
                $this->_view->assign('hetongbianhaoInfo', ['x304' => sprintf('%.02f', $ret['qimoyueyuan'] / 100)]);
            }
        }

        return;
    }
}