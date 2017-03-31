<?php
/**
 * 我的收藏
 * @author lingtm <605415184@qq.com>
 */

if (!defined('IN_API')) {
    exit('Access Denied');
}

loadcore();

require_once DISCUZ_ROOT . 'source/function/function_home.php';

$siteurl = $_G['siteurl'];
$uid = $_G['uid'];
$space = getuserbyuid($uid, 1);
if (empty($space)) {
    ajaxReturn([], 'space_does_not_exist', '400');
}

if ($space['status'] == -1 && $_G['adminid'] != 1) {
    ajaxReturn([], 'space_has_been_locked', '400');
}

$page = empty($_GET['pageId']) ? 1 : intval($_GET['pageId']);
if ($page < 1) {
    $page = 1;
}

$perpage = isset($_GET['pageSize']) ? intval($_GET['pageSize']) : 10;

$_G['disabledwidthauto'] = 0;

$start = ($page - 1) * $perpage;
ckstart($start, $perpage);

$actives['tid'] = ' class="a"';

$wherearr = $list = [];
$favid = 0;
$idtype = 'tid';//只显示帖子

$count = C::t('home_favorite')->count_by_uid_idtype($_G['uid'], $idtype, $favid);
if ($count) {
    $icons = [
        'tid'     => '<img src="static/image/feed/thread.gif" alt="thread" class="t" /> ',
        'fid'     => '<img src="static/image/feed/discuz.gif" alt="forum" class="t" /> ',
        'blogid'  => '<img src="static/image/feed/blog.gif" alt="blog" class="t" /> ',
        'gid'     => '<img src="static/image/feed/group.gif" alt="group" class="t" /> ',
        'uid'     => '<img src="static/image/feed/profile.gif" alt="space" class="t" /> ',
        'albumid' => '<img src="static/image/feed/album.gif" alt="album" class="t" /> ',
        'aid'     => '<img src="static/image/feed/article.gif" alt="article" class="t" /> ',
    ];
    $articles = [];
    foreach (C::t('home_favorite')->fetch_all_by_uid_idtype($_G['uid'], $idtype, $favid, $start, $perpage) as $value) {
        $value['icon'] = isset($icons[$value['idtype']]) ? $icons[$value['idtype']] : '';
        $value['url'] = makeurl($value['id'], $value['idtype'], $value['spaceuid']);
        $value['description'] = !empty($value['description']) ? nl2br($value['description']) : '';
        $list[$value['favid']] = $value;
    }
}

$result = [
    'pageId'    => $page,
    'pageCount' => ceil($count / $perpage),
    'pageSize'  => $perpage,
    'total' => $count
];

$tids = [];
foreach ($list as $k => $v) {
    $result['list'][$k] = [
        'id'         => $v['id'],
        'title'      => $v['title'],
        'createTime' => date('Y-m-d H:i:s', $v['dateline']),
        'url'        => $siteurl . $v['url'],
        'cancelUrl'  => $siteurl . 'api.php?mod=kkdusercancelcollect&id=' . $k,
    ];
    $tids[] = $v['id'];
}

//获取帖子信息
$threads = C::t('forum_thread')->fetch_all_by_tid($tids);
$gids = [];
foreach ($threads as $k => $v) {
    $gids[$v['fid']][] = $v['tid'];
}

//获取版块名
if (!empty($gids)) {
    $groupforums = C::t('forum_forum')->fetch_all_name_by_fid(array_keys($gids));
    foreach ($gids as $fid => $tid) {
        foreach ($tid as $tv) {
            $threads[$tv]['forumname'] = $groupforums[$fid]['name'];
        }
    }
}

$icons = [
    'tid'     => 'static/image/feed/thread.gif ',
    'fid'     => 'static/image/feed/discuz.gif',
    'blogid'  => 'static/image/feed/blog.gif',
    'gid'     => 'static/image/feed/group.gif',
    'uid'     => 'static/image/feed/profile.gif',
    'albumid' => 'static/image/feed/album.gif',
    'aid'     => 'static/image/feed/article.gif> ',
];

foreach ($list as $k => $v) {
    $result['list'][$k]['icon'] = isset($icons[$v['idtype']]) ? $siteurl . $icons[$v['idtype']] : '';
    $result['list'][$k]['forumName'] = $threads[$v['id']]['forumname'];
    $result['list'][$k]['replies'] = $threads[$v['id']]['replies'];
    $result['list'][$k]['views'] = $threads[$v['id']]['views'];
    $result['list'][$k]['commentLastTime'] = date('Y-m-d H:i:s', $threads[$v['id']]['lastpost']);
    $result['list'][$k]['forumUrl'] = $siteurl . 'forum.php?mod=forumdisplay&fid=' . $threads[$v['id']]['fid'];
}

$temp = [];
foreach ($result['list'] as $v) {
    $temp[] = $v;
}

$result['list'] = $temp;

ajaxReturn($result, 'success', 200);

function makeurl($id, $idtype, $spaceuid = 0)
{
    $url = '';
    switch ($idtype) {
        case 'tid':
            $url = 'forum.php?mod=viewthread&tid=' . $id;
            break;
        case 'fid':
            $url = 'forum.php?mod=forumdisplay&fid=' . $id;
            break;
        case 'blogid':
            $url = 'home.php?mod=space&uid=' . $spaceuid . '&do=blog&id=' . $id;
            break;
        case 'gid':
            $url = 'forum.php?mod=group&fid=' . $id;
            break;
        case 'uid':
            $url = 'home.php?mod=space&uid=' . $id;
            break;
        case 'albumid':
            $url = 'home.php?mod=space&uid=' . $spaceuid . '&do=album&id=' . $id;
            break;
        case 'aid':
            $url = 'portal.php?mod=view&aid=' . $id;
            break;
    }
    return $url;
}


//$arr = [
//    'code' => 200,
//    'msg'  => 'success',
//    'data' => [
//        'pageId'    => 1,
//        'pageCount' => 5,
//        'pageSize'  => 3,
//        'list'      => [
//            [
//                'id'              => 12322,
//                'icon'            => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title'           => '有了良好的互动氛围，无疑评论回复数就将稳步增加。',
//                'forumName'       => '新手专区',
//                'createTime'      => '2016-09-02 23:23:23',
//                'replies'         => 12,
//                'views'           => 345,
//                'commentLastTime' => '2016-09-05 12:12:12',
//                'url'             => $siteurl . 'forum.php?mod=viewthread&tid=1&extra=page%3D1',
//                'forumUrl'        => $siteurl . 'forum.php?mod=forumdisplay&fid=2',
//                'cancelUrl'       => $siteurl . 'api.php?mod=kkdusercancelcollect&id=1&deletesubmit=0',
//            ],
//            [
//                'id'              => 12322,
//                'icon'            => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title'           => '有了良好的互动氛围，无疑评论回复数就将稳步增加。',
//                'forumName'       => '新手专区',
//                'createTime'      => '2016-09-02 23:23:23',
//                'replies'         => 12,
//                'views'           => 345,
//                'commentLastTime' => '2016-09-05 12:12:12',
//                'url'             => $siteurl . 'forum.php?mod=viewthread&tid=1&extra=page%3D1',
//                'forumUrl'        => $siteurl . 'forum.php?mod=forumdisplay&fid=2',
//                'cancelUrl'       => $siteurl . 'api.php?mod=kkdusercancelcollect&id=1&deletesubmit=0',
//            ],
//            [
//                'id'              => 12322,
//                'icon'            => 'http://ibbs.kuaikuaidai.com/static/image/common/forum.gif',
//                'title'           => '有了良好的互动氛围，无疑评论回复数就将稳步增加。',
//                'forumName'       => '新手专区',
//                'createTime'      => '2016-09-02 23:23:23',
//                'replies'         => 12,
//                'views'           => 345,
//                'commentLastTime' => '2016-09-05 12:12:12',
//                'url'             => $siteurl . 'forum.php?mod=viewthread&tid=1&extra=page%3D1',
//                'forumUrl'        => $siteurl . 'forum.php?mod=forumdisplay&fid=2',
//                'cancelUrl'       => $siteurl . 'api.php?mod=kkdusercancelcollect&id=1&deletesubmit=0',
//            ],
//        ],
//    ],
//];