<?php
//$id = isset($_GET['id']) ? : '';
//if (empty($id)) {
//    echo json_encode(['code' => 400, 'msg' => 'failed']);
//    exit;
//}
//
//$arr = [
//    'code' => 200,
//    'msg' => 'success',
//];
//
//echo json_encode($arr);

/**
 * 取消收藏
 * @author lingtm <605415184@qq.com>
 */

if (!defined('IN_API')) {
    exit('Access Denied');
}

loadcore();

$checkLoginMsg = checkLogin();
if (!empty($checkLoginMsg)) {
    ajaxReturn([], $checkLoginMsg, 400);
}

$favid = isset($_GET['id']) ? intval($_GET['id']) : '';
$gpccheck = $_GET['deletesubmit'];

$thevalue = C::t('home_favorite')->fetch($favid);
if (empty($thevalue) || $thevalue['uid'] != $_G['uid']) {
    ajaxReturn([], 'favorite_does_not_exist', 400);
}

if ($gpccheck && !submitcheck('deletesubmit')) {
    ajaxReturn([], 'gpccheck failed', 400);
}

switch ($thevalue['idtype']) {
    case 'fid':
        C::t('forum_forum')->update_forum_counter($thevalue['id'], 0, 0, 0, 0, -1);
        break;
    default:
        break;
}
C::t('home_favorite')->delete($favid);
if ($_G['setting']['cloud_status']) {
    $favoriteService = Cloud::loadClass('Service_Client_Favorite');
    $favoriteService->remove($_G['uid'], $favid);
}
ajaxReturn([], 'success', 200);