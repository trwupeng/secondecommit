<?php

$dir = 'data/log';
$logDir = $dir;
$logFile = $logDir.'/error_log_'.date('Ymd').'.log';
ini_set('error_log', $logFile);
ini_set("log_errors", "On");
error_log('来自客户端的cookie>>>');
error_log(var_export($_COOKIE,true));

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *      $Id: forum.php 33828 2013-08-20 02:29:32Z nemohou $
 */

define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');


require './source/class/class_core.php';


require './source/function/function_forum.php';


$modarray = [
    'ajax',
    'announcement',
    'attachment',
    'forumdisplay',
    'group',
    'image',
    'index',
    'medal',
    'misc',
    'modcp',
    'notice',
    'post',
    'redirect',
    'relatekw',
    'relatethread',
    'rss',
    'topicadmin',
    'trade',
    'viewthread',
    'tag',
    'collection',
    'guide',
];

$modcachelist = [
    'index'        => [
        'announcements',
        'onlinelist',
        'forumlinks',
        'heats',
        'historyposts',
        'onlinerecord',
        'userstats',
        'diytemplatenameforum',
    ],
    'forumdisplay' => [
        'smilies',
        'announcements_forum',
        'globalstick',
        'forums',
        'onlinelist',
        'forumstick',
        'threadtable_info',
        'threadtableids',
        'stamps',
        'diytemplatenameforum',
    ],
    'viewthread'   => [
        'smilies',
        'smileytypes',
        'forums',
        'usergroups',
        'stamps',
        'bbcodes',
        'smilies',
        'custominfo',
        'groupicon',
        'stamps',
        'threadtableids',
        'threadtable_info',
        'posttable_info',
        'diytemplatenameforum',
    ],
    'redirect'     => ['threadtableids', 'threadtable_info', 'posttable_info'],
    'post'         => [
        'bbcodes_display',
        'bbcodes',
        'smileycodes',
        'smilies',
        'smileytypes',
        'domainwhitelist',
        'albumcategory',
    ],
    'space'        => ['fields_required', 'fields_optional', 'custominfo'],
    'group'        => ['grouptype', 'diytemplatenamegroup'],
];

$mod = !in_array(C::app()->var['mod'], $modarray) ? 'index' : C::app()->var['mod'];

define('CURMODULE', $mod);
$cachelist = [];
if (isset($modcachelist[CURMODULE])) {
    $cachelist = $modcachelist[CURMODULE];

    $cachelist[] = 'plugin';
    $cachelist[] = 'pluginlanguage_system';
}
if (C::app()->var['mod'] == 'group') {
    $_G['basescript'] = 'group';
}

C::app()->cachelist = $cachelist;
C::app()->init();

loadforum();
set_rssauth();
runhooks();

$navtitle                             = str_replace('{bbname}', $_G['setting']['bbname'], $_G['setting']['seotitle']['forum']);
$_G['setting']['threadhidethreshold'] = 1;
require DISCUZ_ROOT . './source/module/forum/forum_' . $mod . '.php';


var_log('===================[end]' . $_SERVER['REMOTE_ADDR'] . '======================');

?>