<?php
/**
 * 我的未读消息
 * @author lingtm <605415184@qq.com>
 */
if (!defined('IN_API')) {
    exit('Access Denied');
}

loadcore();
require_once DISCUZ_ROOT . 'source/function/function_home.php';

//$checkLoginMsg = checkLogin();
//if (!empty($checkLoginMsg)) {
//    ajaxReturn([], $checkLoginMsg, 400);
//}
$customId = isset($_GET['customId']) ? $_GET['customId'] : '';

if (empty($customId)) {
    ajaxReturn([], '用户标识不能为空', 400);
}

$db = new discuz_table(['table'=>'common_member_nickname','pk'=>'uid']);
$res = $db->fetch($customId);

if (!$res) {
    ajaxReturn([], '用户标识不正确', 400);
}

$announcepm = 0;
foreach (C::t('common_member_grouppm')->fetch_all_by_uid($res['bbsId'], 1) as $gpmid => $gpuser) {
    if ($gpuser['status'] == 0) {
        $announcepm++;
    }
}

$result['hasmsg'] = $announcepm ? 1 : 0;

ajaxReturn($result, 'success', 200);


//$arr = [
//    'code' => 200,
//    'msg' => 'success',
//    'data' => [
//        'hasmsg' => 1
//    ],
//];
//
//echo json_encode($arr);