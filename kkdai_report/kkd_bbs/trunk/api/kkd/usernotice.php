<?php

/**
 * 社区消息
 * @author lingtm <605415184@qq.com>
 */
if (!defined('IN_API')) {
    exit('Access Denied');
}

loadcore();
require_once DISCUZ_ROOT . 'source/function/function_home.php';

$checkLoginMsg = checkLogin();
if (!empty($checkLoginMsg)) {
    ajaxReturn([], $checkLoginMsg, 400);
}

loaducenter();

$list = [];

$siteUrl = $_G['siteurl'];
$_GET['plid'] = 0;
$_GET['daterange'] = 0;
$_GET['touid'] = 0;
$_GET['filter'] = 'announcepm';

$plid = empty($_GET['plid']) ? 0 : intval($_GET['plid']);
$daterange = empty($_GET['daterange']) ? 0 : intval($_GET['daterange']);
$touid = empty($_GET['touid']) ? 0 : intval($_GET['touid']);
$opactives['pm'] = 'class="a"';

if (empty($_G['member']['category_num']['manage']) && !in_array($_G['adminid'], [1, 2, 3])) {
    unset($_G['notice_structure']['manage']);
}

$filter = in_array($_GET['filter'], ['newpm', 'privatepm', 'announcepm']) ? $_GET['filter'] : 'privatepm';
$page = empty($_GET['pageId']) ? 0 : intval($_GET['pageId']);
$perpage = empty($_GET['pageSize']) ? 5 : intval($_GET['pageSize']);
if ($page < 1) {
    $page = 1;
}
$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

$grouppms = $gpmids = $gpmstatus = [];
$newpm = $newpmcount = 0;

if ($filter == 'privatepm' && $page == 1 || $filter == 'announcepm' || $filter == 'newpm') {
    $announcepm = 0;
    foreach (C::t('common_member_grouppm')->fetch_all_by_uid_limit($_G['uid'], $filter == 'announcepm' ? 1 : 0, $start, $perpage) as $gpmid => $gpuser) {
        $gpmstatus[$gpmid] = $gpuser['status'];
        if ($gpuser['status'] == 0) {
            $announcepm++;
        }
    }
    $gpmids = array_keys($gpmstatus);
    if ($gpmids) {
        foreach (C::t('common_grouppm')->fetch_all_by_id_authorid($gpmids) as $grouppm) {
            $grouppm['message'] = cutstr(strip_tags($grouppm['message']), 100, '');
            $grouppms[] = $grouppm;
        }
    }
}
$total = C::t('common_member_grouppm')->count_by_uid($_G['uid']);
$result = [
    'pageId'    => $page,
    'pageCount' => ceil( $total/ $perpage),
    'pageSize'  => $perpage,
    'total' => (int)$total
];

foreach ($grouppms as $v) {
    $result['list'][] = [
        'id'         => $v['id'],
        'icon'       => $siteUrl . $_G['style']['imgdir'] . (empty($v['author']) ? '/systempm.png' : '/annpm.png'),
        'title'      => '系统消息',
        'content'    => $v['message'],
        'sender'     => $v['author'],
        'createTime' => date('Y-m-d H:i:s', $v['dateline']),
        'read'       => $gpmstatus[$v['id']],
        'readUrl'    => $siteUrl . 'home.php?mod=space&do=pm&subop=viewg&pmid=' . $v['id'],
    ];
}

$temp = [];
foreach ($result['list'] as $v) {
    $temp[] = $v;
}

$result['list'] = $temp;

ajaxReturn($result, 'success', 200);



//$arr = [
//    'code' => 200,
//    'msg' => 'success',
//    'data' => [
//        'pageId' => 1,
//        'pageCount' => 5,
//        'pageSize' => 10,
//        'list' => [
//            [
//                'id' => 12211,
//                'icon' => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title' => '您有帖子成为精华帖了',
//                'content' => '恭喜，您的帖子：xlcvasdf被版主设置为精华帖了，点击链接查看详情',
//                'sender' => '系统',
//                'createtime' => '2016-09-05 12:12:12',
//                'read' => 1,
//                'readUrl' => 'http://ibbs.kuaikuaidai.com/article/read/12211',
//            ],
//            [
//                'id' => 12211,
//                'icon' => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title' => '您有帖子成为精华帖了',
//                'content' => '恭喜，您的帖子：xlcvasdf被版主设置为精华帖了，点击链接查看详情',
//                'sender' => '系统',
//                'createtime' => '2016-09-05 12:12:12',
//                'read' => 1,
//                'readUrl' => 'http://ibbs.kuaikuaidai.com/article/read/12211',
//            ],
//            [
//                'id' => 12211,
//                'icon' => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title' => '您有帖子成为精华帖了',
//                'content' => '恭喜，您的帖子：xlcvasdf被版主设置为精华帖了，点击链接查看详情',
//                'sender' => '系统',
//                'createtime' => '2016-09-05 12:12:12',
//                'read' => 1,
//                'readUrl' => 'http://ibbs.kuaikuaidai.com/article/read/12211',
//            ],
//        ],
//    ],
//];
//
//echo json_encode($arr);