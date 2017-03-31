<?php

use Prj\ManagerCtrl;

class PerfdailylogController extends ManagerCtrl
{
    public function init()
    {
        parent::init();
        if ($this->_request->get('__VIEW__') == 'json') {
            \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        }
    }

    public function indexAction()
    {
        $this->_view->assign('targetTab', $this->getRequest()->get('targetTab', ''));
        $this->_view->assign('jumpParams', json_decode(urldecode($this->getRequest()->get('jumpParams', '')), true));

        $pager = new \Sooh\DB\Pager(1000);
        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $data = \Prj\Data\MBPerfDailylog::paged($pager, [
            'log_date' => $date,
            'userid'   => $this->session->get('managerId'),
        ]);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', '', 'text', [], 5, ['readOnly' => 1])
             ->addRow('type', '类型', '', 'select', [1 => '原始计划', 2 => '临时计划'], 10)
             ->addRow('name', '项目', '', 'text', [], 10)
             ->addRow('content', '工作内容', '', 'textarea', [], 10)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 10)
             ->addRow('plan_cost', '实际用时(小时)', '', 'text', [], 10)
             ->addRow('finish', '完成情况', '', 'select', [0 => '未完成', 1 => '完成', 2 => '其他'], 10)
             ->addRow('finish_reason', '完成情况的说明', '', 'text', [], 10);

        $this->_view->assign('date', $date);
        $this->_view->assign('view', $view->showPerf());
    }

    public function tabhistorylogAction()
    {
        $pager = new \Sooh\DB\Pager(1000);
        $date = $this->_request->get('date', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
        $zzjgName = $this->_request->get('zzjgName', '');
        $zzjgNameCh = $this->_request->get('zzjgNameCh', '');

        $where = [];
        !empty($date) && $where['log_date'] = $date;
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

        $data = \Prj\Data\MBPerfDailylog::paged($pager, $where);
        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setReple('loghistory_dst_')
             ->setOperate(true)
             ->hideDefaultBtn()
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', '', 'text', [], 5)
             ->addRow('type', '类型', '', 'select', [1 => '原始计划', 2 => '临时计划'], 100)
             ->addRow('name', '项目', '', 'text', [], 10)
             ->addRow('content', '工作内容', '', 'textarea', [], 10)
             ->addRow('level', '优先级', '', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 100)
             ->addRow('plan_cost', '实际用时(小时)', '', 'text', [], 40)
             ->addRow('finish', '完成情况', '', 'select', [0 => '未完成', 1 => '完成', 2 => '其他'], 100)
             ->addRow('finish_reason', '完成情况的说明', 'p8', 'text', [], 10);

        if ($this->manager->hasLowUsers() || $this->manager->getField('loginName') == 'root') {
            $this->_view->assign('hasManager', 1);
        }
        $this->_view->assign('view', $view->normalPerfTable());
        $this->_view->assign('date', $date);
        isset($zzjgFlat) && $this->_view->assign('zzjgStr', $zzjgStr);
        isset($zzjgName) && $this->_view->assign('zzjgName', $zzjgName);
        isset($zzjgNameCh) && $this->_view->assign('zzjgNameCh', $zzjgNameCh);

        if (!empty($data)) {
            $userid = urldecode($userid);
            $batchid = date('Ymd', strtotime($date));
            $records = \Prj\Data\MBPerfReply::getMyReply($userid, $batchid);
            $this->_view->assign('userid', $userid);
            $this->_view->assign('batchid', $batchid);
            $this->_view->assign('batch_type', \Prj\Data\MBPerfReply::logType);
            $this->_view->assign('prefix', 'loghistory_dst_');
            $this->_view->assign('records', $records);
            $this->_view->assign('showReply', 1);
        }
    }

    public function tabreportlogAction()
    {
        $pager = new \Sooh\DB\Pager(1000);
        $isDownload = $this->_request->get('__EXCEL__');
        if ($isDownload) {
            $where = json_decode(urldecode($this->_request->get('where')), true);
        } else {
            $startDate = $this->_request->get('startDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));
            $endDate = $this->_request->get('endDate', date('Y-m-d', \Sooh\Base\Time::getInstance()->timestamp()));

            $where = ['userid' => $this->session->get('managerId')];
            empty($startDate) || $where['log_date]'] = $startDate;
            empty($endDate) || $where['log_date['] = $endDate;
        }

        $data = \Prj\Data\MBPerfDailylog::paged($pager, $where);

        $view = new \Prj\Misc\ViewFK();
        $view->setPk('id')
             ->setData($data)
             ->setPager($pager)
             ->hideDefaultBtn()
             ->setAction('/plan/perfdailylog/update', '/plan/perfdailylog/delete?')
             ->addRow('id', '序号', 'p1', 'text', [], 5)
             ->addRow('type', '类型', 'p2', 'select', [1 => '原始计划', 2 => '临时计划'], 100)
             ->addRow('name', '项目', 'p3', 'text', [], 10)
             ->addRow('content', '工作内容', 'p4', 'textarea', [], 10)
             ->addRow('level', '优先级', 'p5', 'select', [1 => '重要并紧急', 2 => '紧急', 3 => '重要', 4 => '普通', 5 => '不重要'], 100)
             ->addRow('plan_cost', '实际用时(小时)', 'p6', 'text', [], 40)
             ->addRow('finish', '完成情况', 'p7', 'select', [0 => '未完成', 1 => '完成', 2 => '其他'], 100)
             ->addRow('finish_reason', '完成情况的说明', 'p8', 'text', [], 10);

        if ($isDownload) {
            $excel = $view->toExcel($data);
            $this->downExcel($excel['records'], $excel['header']);
            return 0;
        }

        $this->_view->assign('view', $view);
        empty($startDate) || $this->_view->assign('startDate', $startDate);
        empty($endDate) || $this->_view->assign('endDate', $endDate);
        empty($where) || $this->_view->assign('where', $where);
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
                $ret = \Prj\Data\MBPerfDailylog::addData($data['name'], $data['content'], $data['level'], $data['type'], $userid, \Sooh\Base\Time::getInstance()->ymd, (int)$data['plan_cost'], 0, $data['finish']);
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
            $model = \Prj\Data\MBPerfDailylog::getCopy($key);
            $model->load();
            if (!$model->exists()) {
                $this->returnError('记录不存在或者已经被删除');
                return 0;
            }

            try {
                $ret = \Prj\Data\MBPerfDailylog::updData($key, $data);
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

        if (\Prj\Data\MBPerfDailylog::delData($key)) {
            $this->returnOK('删除成功');
        } else {
            $this->returnError('记录不存在或者已经被删除');
        }
        return 0;
    }
}
