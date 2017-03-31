<?php

use Prj\ManagerCtrl;

class PerfdstController extends ManagerCtrl
{
    public function init()
    {
        parent::init();
        if ($this->_request->get('__VIEW__') == 'json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    /**
     * 季度目标
     */
    public function indexAction()
    {
        $this->_view->assign('targetTab', $this->getRequest()->get('targetTab', ''));
        $this->_view->assign('jumpParams', json_decode(urldecode($this->getRequest()->get('jumpParams', '')), true));
        $pager = new \Sooh\DB\Pager(1000);

        $createDate = $this->_request->get('create_date', date('Y', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = $this->_request->get('type_id', ceil(date('m', \Sooh\Base\Time::getInstance()->timestamp()) / 3));

        $where = ['type' => 4, 'type_num' => $typeId];
        empty($createDate) || $where['dst_date'] = $createDate . '-01-01';

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
        if (!empty($zzjgName)) {
            $userid = $zzjgName . '@local';
            $user = \Prj\Data\Manager::getCopy($zzjgName);
            $user->load();
            $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
            $zzjgStr = '';
            foreach ($zzjgFlat as $k => $v) {
                $zzjgStr .= $v['name'] . '--';
            }
            $zzjgStr .= $zzjgNameCh;
        } else {
            $userid = $this->session->get('managerId');
        }
        $where['userid'] = $userid;


        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setReple('quarter_dst_')
             ->setAction('/plan/perfdst/update?type=4', '/plan/perfdst/delete?type=4')
             ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
             ->addRow('name', '项目', '', 'text', [], 100)
             ->addRow('content', '工作内容', '', 'textarea', [], 220)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120);

        $this->_view->assign('view', $view);
        $this->_view->assign('createDate', $createDate);
        $this->_view->assign('typeId', $typeId);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
        isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);

        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = \Prj\Data\MBPerfDst::buildTypeId(4, $createDate . '-01-01', $typeId);
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid);
            $this->_view->assign('userid', $userid);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
            $this->_view->assign('prefix', 'quarter_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
    }

    /**
     * 月目标
     */
    public function tabmonthAction()
    {
        $pager = new \Sooh\DB\Pager(1000);

        $createDate = $this->_request->get('create_date', date('Y', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = $this->_request->get('type_id', date('m', \Sooh\Base\Time::getInstance()->timestamp()));

        $where = ['type' => 3, 'type_num' => $typeId];
        empty($createDate) || $where['dst_date'] = $createDate . '-01-01';

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
        if (!empty($zzjgName)) {
            $userid = $zzjgName . '@local';
            $user = \Prj\Data\Manager::getCopy($zzjgName);
            $user->load();
            $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
            $zzjgStr = '';
            foreach ($zzjgFlat as $k => $v) {
                $zzjgStr .= $v['name'] . '--';
            }

            $zzjgStr .= $zzjgNameCh;
        } else {
            $userid = $this->session->get('managerId');
        }
        $where['userid'] = $userid;

        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setReple('month_dst_')
             ->setAction('/plan/perfdst/update?type=3', '/plan/perfdst/delete?type=3')
             ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
             ->addRow('name', '项目', '', 'text', [], 100)
             ->addRow('content', '工作内容', '', 'textarea', [], 220)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120);

        $this->_view->assign('view', $view);
        $this->_view->assign('createDate', $createDate);
        $this->_view->assign('typeId', $typeId);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
        isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);

        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = \Prj\Data\MBPerfDst::buildTypeId(3, $createDate . '-01-01', $typeId);
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid);
            $this->_view->assign('userid', $userid);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
            $this->_view->assign('prefix', 'month_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
    }

    /**
     * 周目标
     */
    public function tabweekAction()
    {
        //不能跨年
        $pager = new \Sooh\DB\Pager(1000);
        $errorMsg = false;

        $startDate = $this->_request->get('startDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $endDate = $this->_request->get('endDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));

        $startWeekNum = date('W', strtotime($startDate));
        $endWeekNum = date('W', strtotime($endDate));

        if ($endWeekNum <= $startWeekNum) {
            $typeId = $endWeekNum;
        } else {
            if ($endWeekNum > $startWeekNum) {

                $this->_view->assign('errorMsg', '一次只能选择一周');
//                return 0;
            }
            $typeId = $endWeekNum;
            //            $arrTempNums = [];
            //            for ($i = $startWeekNum; $i <= $endWeekNum; $i++) {
            //                $arrTempNums[] = $i;
            //            }
            //            $typeId = $arrTempNums;
        }
        $where = ['type' => 2, 'type_num' => $typeId];

        if (substr($endDate, 0, 4) - substr($startDate, 0, 4) != 0) {
            $arrTempDates = [];
            for ($i = substr($startDate, 0, 4); $i <= substr($endDate, 0, 4); $i++) {
                $arrTempDates[] = $i . '-01-01';
            }
        } else {
            $arrTempDates = substr($startDate, 0, 4) . '-01-01';
        }
        $where['dst_date'] = $arrTempDates;

        $weekNums = $this->getRequest()->get('weekNums', 0);
        if (!empty($weekNums)) {
            $where['type_num'] = $typeId = $weekNums;
        }

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
        if (!empty($zzjgName)) {
            $userid = $zzjgName . '@local';
            $user = \Prj\Data\Manager::getCopy($zzjgName);
            $user->load();
            $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
            $zzjgStr = '';
            foreach ($zzjgFlat as $k => $v) {
                $zzjgStr .= $v['name'] . '--';
            }

            $zzjgStr .= $zzjgNameCh;
        } else {
            $userid = $this->session->get('managerId');
        }
        $where['userid'] = $userid;

        $data = $errorMsg ? [] : \Prj\Data\MBPerfDst::paged($pager, $where);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setReple('week_dst_')
             ->setAction('/plan/perfdst/update?type=2', '/plan/perfdst/delete?type=2')
             ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
             ->addRow('name', '项目', '', 'text', [], 100)
             ->addRow('content', '工作内容', '', 'textarea', [], 220)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120);

        $this->_view->assign('view', $view->showPerf());
        $this->_view->assign('startDate', $startDate);
        $this->_view->assign('endDate', $endDate);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
        isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);

        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = \Prj\Data\MBPerfDst::buildTypeId(2, substr($startDate, 0, 4) . '-01-01', $typeId);
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid);
            $this->_view->assign('userid', $userid);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
            $this->_view->assign('prefix', 'week_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
    }

    /**
     * 日目标
     */
    public function tabdayAction()
    {
        $pager = new \Sooh\DB\Pager(1000);

        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $typeId = date('z', strtotime($date));

        $where = ['type' => 1, 'type_num' => $typeId];
        $where['dst_date'] = $date;

        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');
        if (!empty($zzjgName)) {
            $userid = $zzjgName . '@local';
            $user = \Prj\Data\Manager::getCopy($zzjgName);
            $user->load();
            $zzjgFlat = \Prj\Misc\MBM::getInstance()->getAllDepartsByUser($user);
            $zzjgStr = '';
            foreach ($zzjgFlat as $k => $v) {
                $zzjgStr .= $v['name'] . '--';
            }

            $zzjgStr .= $zzjgNameCh;
        } else {
            $userid = $this->session->get('managerId');
        }
        $where['userid'] = $userid;

        $data = \Prj\Data\MBPerfDst::paged($pager, $where);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setReple('day_dst_')
             ->setAction('/plan/perfdst/update?type=1', '/plan/perfdst/delete?type=1')
             ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
             ->addRow('name', '项目', '', 'text', [], 100)
             ->addRow('content', '工作内容', '', 'textarea', [], 220)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 120);

        $this->_view->assign('view', $view);
        $this->_view->assign('date', $date);
        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
        isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);

        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = \Prj\Data\MBPerfDst::buildTypeId(1, $date, $typeId);
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid);
            $this->_view->assign('userid', $userid);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::targetType);
            $this->_view->assign('prefix', 'day_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
    }

    /**
     * 获取季度目标是否设置
     * @return int
     */
    public function getquartersetAction()
    {
        $year = $this->_request->get('year');
        $where['type'] = 4;
        $arrQuarterNums = [1, 2, 3, 4];
        $where['type_num'] = $arrQuarterNums;
        $where['userid'] = $this->session->get('managerId');
        $where['dst_date'] = $year . '-01-01';

        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);

        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrQuarterNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }

        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    /**
     * 获取月目标是否设置
     * @return int
     */
    public function getmonthsetAction()
    {
        $year = $this->_request->get('year');
        $where['type'] = 3;
        $arrMonthNums = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        $where['type_num'] = $arrMonthNums;
        $where['userid'] = $this->session->get('managerId');
        $where['dst_date'] = $year . '-01-01';

        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrMonthNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }

        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;

    }

    /**
     * 获取周目标是否设置
     * @return int
     */
    public function getweeksetAction()
    {
        $year = $this->_request->get('year');
        $month = $this->_request->get('month');

        $startWeek = (int)date('W', strtotime($year . '-' . $month . '-01'));
        $endWeek = (int)date('W', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))));
        $arrWeekNums = [];

        if ($startWeek > $endWeek) {
            $startWeek = 1;
        }

        for ($i = $startWeek; $i <= $endWeek; $i++) {
            $arrWeekNums[] = $i;
        }

        var_log($arrWeekNums, 'arra');
        $where['type'] = 2;
        $where['type_num'] = $arrWeekNums;
        $where['dst_date'] = $year . '-01-01';
        $where['userid'] = $this->session->get('managerId');

        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrWeekNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k] = 1;
                }
            }
        }
        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    /**
     * 获取日目标是否设置
     * @return int
     */
    public function getdaysetAction()
    {
        $year = $this->_request->get('year');
        $month = $this->_request->get('month');

        $startDay = date('z', strtotime($year . '-' . $month . '-01'));
        $endDay = date('z', strtotime($year . '-' . $month . '-' . date('t', strtotime($year . '-' . $month . '-01'))));
        $arrDayNums = [];
        for ($i = $startDay; $i <= $endDay; $i++) {
            $arrDayNums[] = $i;
        }

        $where['type'] = 1;
        $where['type_num'] = $arrDayNums;
        $where['userid'] = $this->session->get('managerId');

        $data = \Prj\Data\MBPerfDst::paged(new \Sooh\DB\Pager(1000), $where);
        $ret = [];
        foreach ($data as $k => $v) {
            foreach ($arrDayNums as $_k => $_v) {
                if ($v['type_num'] == $_v) {
                    $ret[$_k + 1] = 1;
                }
            }
        }
        $this->_view->assign('data', $ret);
        $this->returnOK();
        return 0;
    }

    public function getYearQuarterAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $year = $this->_request->get('year');
        //TODO ...

        $data = [
            ['name' => '第一季度', 'set' => 1],
            ['name' => '第二季度', 'set' => 0],
            ['name' => '第三季度', 'set' => 1],
            ['name' => '第四季度', 'set' => 0],
        ];
        $this->_view->assign('data', $data);

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

        $userid = $this->session->get('managerId');

        if ($addFlag) {
            try {
                $data['type'] = $this->getRequest()->get('type');
                $ret = \Prj\Data\MBPerfDst::addData($data['name'], $data['content'], $data['level'], $data['type'], \Sooh\Base\Time::getInstance()
                                                                                                                                   ->ymdhis(), $userid, $userid);
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
            $model = \Prj\Data\MBPerfDst::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $ret = \Prj\Data\MBPerfDst::updData($key, $data);
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

    protected function deleteAction()
    {
        $key = $this->getRequest()->get('_id');

        if (\Prj\Data\MBPerfDst::delData($key)) {
            $this->returnOK('删除成功');
        } else {
            $this->returnError('记录不存在或者已经被删除');
        }
        return 0;
    }
}