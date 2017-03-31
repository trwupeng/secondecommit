<?php


$_config = array();

// ----------------------------  CONFIG DB  ----------------------------- //
$_config['db']['1']['dbhost'] = '127.0.0.1:33066';
$_config['db']['1']['dbuser'] = 'root';
$_config['db']['1']['dbpw'] = '123456';
$_config['db']['1']['dbcharset'] = 'utf8';
$_config['db']['1']['pconnect'] = '0';
$_config['db']['1']['dbname'] = 'kkdbbs';
$_config['db']['1']['tablepre'] = 'pre_';
$_config['db']['slave'] = '';
$_config['db']['common']['slave_except_table'] = '';

// ---------------------------- 扩展配置  ----------------------------- //
$_config['ext']['blog'] = '40'; //快快博客版块ID


// --------------------------  CONFIG MEMORY  --------------------------- //
$_config['memory']['prefix'] = 'WsWxxG_';
$_config['memory']['redis']['server'] = '';
$_config['memory']['redis']['port'] = 6379;
$_config['memory']['redis']['pconnect'] = 1;
$_config['memory']['redis']['timeout'] = '0';
$_config['memory']['redis']['requirepass'] = '';
$_config['memory']['redis']['serializer'] = 1;
$_config['memory']['memcache']['server'] = '';
$_config['memory']['memcache']['port'] = 11211;
$_config['memory']['memcache']['pconnect'] = 1;
$_config['memory']['memcache']['timeout'] = 1;
$_config['memory']['apc'] = 1;
$_config['memory']['xcache'] = 1;
$_config['memory']['eaccelerator'] = 1;
$_config['memory']['wincache'] = 1;

// --------------------------  CONFIG SERVER  --------------------------- //
$_config['server']['id'] = 1;

// -------------------------  CONFIG DOWNLOAD  -------------------------- //
$_config['download']['readmod'] = 2;
$_config['download']['xsendfile']['type'] = '0';
$_config['download']['xsendfile']['dir'] = '/down/';

// --------------------------  CONFIG OUTPUT  --------------------------- //
$_config['output']['charset'] = 'utf-8';
$_config['output']['forceheader'] = 1;
$_config['output']['gzip'] = '0';
$_config['output']['tplrefresh'] = 1;
$_config['output']['language'] = 'zh_cn';
$_config['output']['staticurl'] = 'static/';
$_config['output']['ajaxvalidate'] = '0';
$_config['output']['iecompatible'] = '0';

// --------------------------  CONFIG COOKIE  --------------------------- //
$_config['cookie']['cookiepre'] = 'SqSh_';
$_config['cookie']['cookiedomain'] = '';
$_config['cookie']['cookiepath'] = '/';

// -------------------------  CONFIG SECURITY  -------------------------- //
$_config['security']['authkey'] = '65d7ffWuTWR5awTK';
$_config['security']['urlxssdefend'] = 1;
$_config['security']['attackevasive'] = '0';
$_config['security']['querysafe']['status'] = 1;
$_config['security']['querysafe']['dfunction']['0'] = 'load_file';
$_config['security']['querysafe']['dfunction']['1'] = 'hex';
$_config['security']['querysafe']['dfunction']['2'] = 'substring';
$_config['security']['querysafe']['dfunction']['3'] = 'if';
$_config['security']['querysafe']['dfunction']['4'] = 'ord';
$_config['security']['querysafe']['dfunction']['5'] = 'char';
$_config['security']['querysafe']['daction']['0'] = '@';
$_config['security']['querysafe']['daction']['1'] = 'intooutfile';
$_config['security']['querysafe']['daction']['2'] = 'intodumpfile';
$_config['security']['querysafe']['daction']['3'] = 'unionselect';
$_config['security']['querysafe']['daction']['4'] = '(select';
$_config['security']['querysafe']['daction']['5'] = 'unionall';
$_config['security']['querysafe']['daction']['6'] = 'uniondistinct';
$_config['security']['querysafe']['dnote']['0'] = '/*';
$_config['security']['querysafe']['dnote']['1'] = '*/';
$_config['security']['querysafe']['dnote']['2'] = '#';
$_config['security']['querysafe']['dnote']['3'] = '--';
$_config['security']['querysafe']['dnote']['4'] = '"';
$_config['security']['querysafe']['dlikehex'] = 1;
$_config['security']['querysafe']['afullnote'] = '0';

// --------------------------  CONFIG ADMINCP  -------------------------- //
// -------- Founders: $_config['admincp']['founder'] = '1,2,3'; --------- //
$_config['admincp']['founder'] = '1';
$_config['admincp']['forcesecques'] = '0';
$_config['admincp']['checkip'] = 1;
$_config['admincp']['runquery'] = '0';
$_config['admincp']['dbimport'] = 1;

// --------------------------  CONFIG REMOTE  --------------------------- //
$_config['remote']['on'] = '0';
$_config['remote']['dir'] = 'remote';
$_config['remote']['appkey'] = '62cf0b3c3e6a4c9468e7216839721d8e';
$_config['remote']['cron'] = '0';

// ---------------------------  CONFIG INPUT  --------------------------- //
$_config['input']['compatible'] = 1;

// ---------------------------  CONFIG KKD  --------------------------- //
$_config['ext']['newsFid'] = '40'; //行业资讯版块ID
$_config['ext']['investFid'] = '36'; //投资交流版块ID

$_config['kkd']['url'] = 'http://203.166.163.161';
//$_config['kkd']['url'] = 'http://www.kuaikuaidai.com';
$_config['kkd']['investment']['url'] = '/app/getBbsPoiTop.do';
$_config['kkd']['invite']['url'] = '/app/getBbsInviteTop.do';

$_config['kkd']['investment']['url'] = '/app/getBbsPoiTop.do';//投资榜单
$_config['kkd']['invite']['url'] = '/app/getBbsInviteTop.do';//邀请投资榜单
$_config['kkd']['user']['checklogin']['web'] = '/isLogin.do?jskey=';//检查是否登录-web
$_config['kkd']['user']['checklogin']['h5'] = '/phoenix-h5-server/isLogin.do?jskey=';//检查是否登录-h5
$_config['kkd']['user']['checklogin']['app'] = '/app/isLogin.do';//app 登录 //customerId/token
$_config['kkd']['user']['reg']['web'] = '/register.html';//注册
$_config['kkd']['user']['reg']['h5'] = '/phoenix-h5-server/phone.html';//注册
$_config['kkd']['user']['login']['web'] = '/login.html';//登录
$_config['kkd']['user']['login']['h5'] = '/phoenix-h5-server/login.html';//登录

$_config['kkd']['commentNum'] = 0;
$_config['kkd']['loginApiH5'] = $_config['kkd']['url'].'/phoenix-h5-server/login2.do';//登录接口
$_config['kkd']['bbsReward']['web'] = $_config['kkd']['url'].'/phoenix-web-server/bbsReward.do';//增加快乐币
$_config['kkd']['bbsReward']['h5'] = $_config['kkd']['url'].'/phoenix-h5-server/bbsReward.do';//增加快乐币
$_config['kkd']['bbsComment']['web'] = $_config['kkd']['url'].'/phoenix-web-server/bbsComment.do';//扣除快乐币
$_config['kkd']['bbsComment']['h5'] = $_config['kkd']['url'].'/phoenix-h5-server/bbsComment.do';//扣除快乐币
$_config['kkd']['live']['h5'] = $_config['kkd']['url'].'/phoenix-h5-server/liveindex.html';//直播链接
$_config['kkd']['live']['status'] = $_config['kkd']['url'].'/app/video/getLiveInfo';//直播状态
// -------------------  THE END  -------------------- //

?>