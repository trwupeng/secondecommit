<?php
/**
 * Created by PhpStorm.
 * User: LTM <605415184@qq.com>
 * Date: 2016/2/16
 * Time: 10:21
 */
require_once './source/class/class_core.php';
require_once './source/function/function_forum.php';
C::app()->init();

$data = getcookie('kkdUid');
$kkdUid = (authcode($data,'DECODE'));
$kkdAvatar = (authcode($kkdAvatar,'DECODE'));
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && !empty($_COOKIE['JSESSIONID']) || $kkdUid) {
	define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	//使用dz原生引擎
	require_once ROOT_PATH . 'source/class/class_core.php';
	include_once(ROOT_PATH . './source/class/discuz/discuz_database.php');


	$nickname = $_POST['nickname'];
	$pwd = randPwd();
	$cookie = $_COOKIE['JSESSIONID'];

	if (check_username1($nickname) === false) {
		die(json_encode(['code' => 400, 'info' => '对不起，昵称输入不合法请重新输入']));
	}

	//check cookie
    if($kkdUid){
        $kkdRet['code'] = 0;
    }else{
        $_CHECK_COOKIE_URL_ = (checkmobile_liang() ? $_G['config']['kkd']['user']['checklogin']['h5'] : $_G['config']['kkd']['user']['checklogin']['web'] );
        //到官网接口上获取是否登录，获取$uid
        $url = $_CHECK_COOKIE_URL_ . $cookie;
        $kkdRet = file_get_contents($url);
        var_log('changeNickname:'.$url);
        $kkdRet = json_decode($kkdRet, true);
    }

	if($kkdRet['code'] == 0 ) {
		$kkdUid = $kkdRet['data']['customerId'] ? $kkdRet['data']['customerId'] : $kkdUid;
        $db = new discuz_table(['table'=>"common_member_nickname",'pk'=>'uid']);
        $res = $db->fetch($kkdUid);
        if(empty($res['nickname'])){
            try{
                $ret = $db->insert([
                    'uid'=>$kkdUid,
                    'nickname'=>$nickname,
                    'pwd'=>$pwd,
                ]);
            }catch (\Exception $e){
                //var_log($e);
                die(json_encode(['code' => 400, 'info' => '该昵称已经被使用']));
            }
        }else{
            $nickname = $res['bbsId']<0 ? $nickname : $res['nickname'];
            $pwd = $res['pwd'];
            $ret = $db->update($kkdUid,[
                'uid'=>$kkdUid,
                'nickname'=>$nickname,
                'pwd'=>$pwd,
            ]);
        }
		//$ret = insertNickname($kkdUid, $nickname, $pwd);
		if ($ret !== false) {
            var_log('changeNickname kkdAvatar:'.$kkdAvatar);
            $_result = discuz_application::kkdSync($kkdUid,$kkdAvatar,$_SERVER['REMOTE_ADDR'],$nickname);
            $cookietime = 600;
            $discuz_uid = $_result['uid'];
            //dsetcookie('auth', authcode("$pwd\t$discuz_uid", 'ENCODE'), $cookietime);
            var_log($_result,'>>>');
			die(json_encode(['code' => 0, 'info' => '修改成功啦']));
			//success
		} else {
			die(json_encode(['code' => 400, 'info' => '您已经修改过昵称，请不要重复修改']));
			//faile or exists
		}
	}

	//还没有登录
	die(json_encode(['code' => 200, 'info' => '对不起，您还没有登录']));
//	ob_end_clean();
//	header('Location:http://www.kuaikuaidai.com/login.html');
//	exit();
}
die(json_encode(['code' => 400, 'info' => '对不起非法输入' , 'kkdUid' => $_SESSION['kkdUid']]));

/**
 * 更新用户昵称
 */
function insertNickname($uid, $nickname, $pwd)
{
    return false;
	if (!isset(discuz_database::$driver)) {
		$driver = function_exists('mysql_connect') ? 'db_driver_mysql' : 'db_driver_mysqli';
		if(getglobal('config/db/slave')) {
			$driver = function_exists('mysql_connect') ? 'db_driver_mysql_slave' : 'db_driver_mysqli_slave';
		}

//		$_config = array();
//		include_once ROOT_PATH .'config/config_global.php';

		// ----------------------------  CONFIG DB  ----------------------------- //
		$_config['db']['1']['dbhost'] = 'localhost:33066';
		$_config['db']['1']['dbuser'] = 'root';
		$_config['db']['1']['dbpw'] = '123456';
		$_config['db']['1']['dbcharset'] = 'utf8';
		$_config['db']['1']['pconnect'] = '0';
		$_config['db']['1']['dbname'] = 'kkdbbs';
		$_config['db']['1']['tablepre'] = 'pre_';
		$_config['db']['slave'] = '';
		$_config['db']['common']['slave_except_table'] = '';


		if(empty($_config)) {
			system_error('config_not_found');
		}

		discuz_database::init($driver, $_config['db']);
	}

//	$sql = 'SELECT * from ' . discuz_database::table('common_member_nickname') . ' WHERE uid = %s';
//	$query = discuz_database::query($sql, [$uid]);
//	$ret = discuz_database::fetch($query);
//
//	$sql = 'SELECT * FROM ' . discuz_database::table('common_member_nickname') . ' WHERE nickname = %s';
//	$query = discuz_database::query($sql, [$nickname]);
//	$nicknameRet = discuz_database::fetch($query);
//
//	if (!empty($nicknameRet)) {
//		die(json_encode(['code' => 400, 'info' => '昵称已存在，请重试']));
//	}

//	if (empty($ret)) {
		$_sql = 'INSERT INTO ' . discuz_database::table('common_member_nickname') . ' (`uid`, `nickname`, `pwd`) VALUES (%n, %s, %s)';
		$args = [$uid, $nickname, $pwd];

		try {
			$_query = discuz_database::query($_sql, $args);
			$_ret = discuz_database::fetch($_query);
		} catch (\Exception $e) {
			die(json_encode(['code' => 400, 'info' => '昵称已存在，请重试']));
		}

		return true;
		//check ret
//	} else {
//		return false;
//	}
}

function check_username1($username) {
	$guestexp = '\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8';
	$len = mb_strwidth($username, 'UTF-8');
	if($len > 12 || $len < 4 || preg_match("/\s+|^c:\\con\\con|[%,\*\"\s\<\>\&]|$guestexp/is", $username)) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function randPwd() {
	$str_len = 15;
	$char_lib = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';

	return str_get_rand1($str_len, $char_lib);
}

function str_get_rand1($str_len, $char_lib){
	$res = '';

	for($i = 0; $i < $str_len; $i++){
		//从字符库中随机一个位置[数字]
		$str_loc = rand(0, strlen($char_lib) - 1);
		$res .= $char_lib[$str_loc];
	}
	return $res;
}

function checkmobile_liang() {
	$mobile = array();
	static $touchbrowser_list =array('iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi', 'opera mini',
	                                 'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod', 'nokia', 'samsung',
	                                 'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma', 'docomo', 'up.browser',
	                                 'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad', 'webos', 'techfaith', 'palmsource',
	                                 'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom', 'bunjalloo', 'maui', 'smartphone',
	                                 'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech', 'gionee', 'portalmmm', 'jig browser', 'hiptop',
	                                 'benq', 'haier', '^lct', '320x320', '240x320', '176x220', 'windows phone');
	static $wmlbrowser_list = array('cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'alcatel', 'ericsson', 'bird', 'daxian', 'dbtel', 'eastcom',
	                                'pantech', 'dopod', 'philips', 'haier', 'konka', 'kejian', 'lenovo', 'benq', 'mot', 'soutec', 'nokia', 'sagem', 'sgh',
	                                'sed', 'capitel', 'panasonic', 'sonyericsson', 'sharp', 'amoi', 'panda', 'zte');

	static $pad_list = array('ipad');

	$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);

	if(dstrpos_liang($useragent, $pad_list)) {
		return false;
	}
	if(($v = dstrpos_liang($useragent, $touchbrowser_list, true))){
		//		return '2';
		return true;
	}
	if(($v = dstrpos_liang($useragent, $wmlbrowser_list))) {
		//		return '3'; //wml版
		return false;
	}
	$brower = array('mozilla', 'chrome', 'safari', 'opera', 'm3gate', 'winwap', 'openwave', 'myop');
	if(dstrpos_liang($useragent, $brower)) return false;

	$mobiletpl = array('1' => 'mobile', '2' => 'touch', '3' => 'wml', 'yes' => 'mobile');
	if(isset($mobiletpl[$_GET['mobile']])) {
		return true;
	} else {
		return false;
	}
}

function dstrpos_liang($string, $arr, $returnvalue = false) {
	if(empty($string)) return false;
	foreach((array)$arr as $v) {
		if(strpos($string, $v) !== false) {
			$return = $returnvalue ? $v : true;
			return $return;
		}
	}
	return false;
}