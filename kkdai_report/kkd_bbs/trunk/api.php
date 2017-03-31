<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *      $Id: api.php 33591 2013-07-12 06:39:49Z andyzheng $
 */

define('IN_API', true);
define('CURSCRIPT', 'api');

$modarray = [
    'js'                   => 'javascript/javascript',
    'ad'                   => 'javascript/advertisement',
    'kkdusernotice'        => 'kkd/usernotice',
    'kkdusermsg'           => 'kkd/usermsg',
    'kkduserreply'         => 'kkd/userreply',
    'kkdusercollect'       => 'kkd/usercollect',
    'kkduserarticle'       => 'kkd/userarticle',
    'kkdusercancelcollect' => 'kkd/usercancelcollect',
    'login'                => 'member/login',
    'getDataFromUrl'                => 'kkd/getDataFromUrl',
];

$mod = !empty($_GET['mod']) ? $_GET['mod'] : '';
if (empty($mod) || !in_array($mod, array_keys($modarray))) {
    exit('Access Denied');
}
//====================自定义日志 by tgh=========================
define('DIR', dirname(__FILE__));
define('PID', mt_rand(1000, 9999));
date_default_timezone_set('Etc/GMT-8');
error_reporting(E_PARSE | E_ERROR | E_COMPILE_ERROR);
define('LOGPATH', 'data/log');
define('ERRORLOG', 1);
ini_set("display_errors", "Off");
ini_set("log_errors", "On");
$logDir = DIR . '/' . LOGPATH;
$logFile = $logDir . '/error_log_' . date('Ymd') . '.log';
ini_set('error_log', $logFile);

error_log('request:http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
error_log('cookie:' . json_encode($_COOKIE));

$kkdAuth = $_GET['JSESSIONID'] ? : ($_POST['JSESSIONID'] ? : $_COOKIE['JSESSIONID']);
error_log('get JSESSIONID :' . $kkdAuth);
if ($_GET['JSESSIONID'] || $_POST['JSESSIONID']) {
    setcookie('JSESSIONID', $kkdAuth, time() + 15 * 60);
}

require_once './api/' . $modarray[$mod] . '.php';

function loadcore()
{
    global $_G;
    require_once './source/class/class_core.php';

    $discuz = C::app();
    $discuz->init_cron = false;
    $discuz->init_session = false;
    $discuz->init();
}

/**
 * 检查是否登录
 * @return string
 * @author lingtm <605415184@qq.com>
 */
function checkLogin()
{
    global $_G;

    $uid = $_G['uid'];

    if (empty($uid)) {
        return 'login_before_enter_home';
    }

    $space = getuserbyuid($uid, 1);
    if (empty($space)) {
        return 'space_does_not_exist';
    }

    if ($space['status'] == -1 && $_G['adminid'] != 1) {
        return 'space_has_been_locked';
    }
    return '';
}

?>