<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: discuz_application.php 34608 2014-06-11 02:07:39Z nemohou $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class discuz_application extends discuz_base{


	var $mem = null;

	var $session = null;

	var $config = array();

	var $var = array();

	var $cachelist = array();

	var $init_db = true;
	var $init_setting = true;
	var $init_user = true;
	var $init_session = true;
	var $init_cron = true;
	var $init_misc = true;
	var $init_mobile = true;

	var $initated = false;

	var $superglobal = array(
		'GLOBALS' => 1,
		'_GET' => 1,
		'_POST' => 1,
		'_REQUEST' => 1,
		'_COOKIE' => 1,
		'_SERVER' => 1,
		'_ENV' => 1,
		'_FILES' => 1,
	);

	static function &instance() {
		static $object;
		if(empty($object)) {
			$object = new self();
		}
		return $object;
	}

	public function __construct() {
		$this->_init_env();
		$this->_init_config();
		$this->_init_input();
		$this->_init_output();
	}

	public function init() {
		if(!$this->initated) {
			$this->_init_db();
			$this->_init_setting();
            $this->_init_log();
			$this->_init_user();
			$this->_init_session();
			$this->_init_mobile();
			$this->_init_cron();
            $this->_init_misc();
        }
		$this->initated = true;
	}

    private function _init_log(){
        var_log('=================[begin]'.$_SERVER['REMOTE_ADDR'].'======================');
        var_log('###'.$_SERVER['SCRIPT_NAME'].' GET:'.json_encode(array_merge($_GET,$_POST)));
    }

	private function _init_env() {

		error_reporting(E_ERROR);
		if(PHP_VERSION < '5.3.0') {
			set_magic_quotes_runtime(0);
		}

		define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
		define('ICONV_ENABLE', function_exists('iconv'));
		define('MB_ENABLE', function_exists('mb_convert_encoding'));
		define('EXT_OBGZIP', function_exists('ob_gzhandler'));

		define('TIMESTAMP', time());
		$this->timezone_set();

		if(!defined('DISCUZ_CORE_FUNCTION') && !@include(DISCUZ_ROOT.'./source/function/function_core.php')) {
			exit('function_core.php is missing');
		}

		if(function_exists('ini_get')) {
			$memorylimit = @ini_get('memory_limit');
			if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
				ini_set('memory_limit', '128m');
			}
		}

		define('IS_ROBOT', checkrobot());

		foreach ($GLOBALS as $key => $value) {
			if (!isset($this->superglobal[$key])) {
				$GLOBALS[$key] = null; unset($GLOBALS[$key]);
			}
		}

		global $_G;
		$_G = array(
			'uid' => 0,
			'username' => '',
			'adminid' => 0,
			'groupid' => 1,
			'sid' => '',
			'formhash' => '',
			'connectguest' => 0,
			'timestamp' => TIMESTAMP,
			'starttime' => microtime(true),
			'clientip' => $this->_get_client_ip(),
			'remoteport' => $_SERVER['REMOTE_PORT'],
			'referer' => '',
			'charset' => '',
			'gzipcompress' => '',
			'authkey' => '',
			'timenow' => array(),
			'widthauto' => 0,
			'disabledwidthauto' => 0,

			'PHP_SELF' => '',
			'siteurl' => '',
			'siteroot' => '',
			'siteport' => '',

			'pluginrunlist' => !defined('PLUGINRUNLIST') ? array() : explode(',', PLUGINRUNLIST),

			'config' => array(),
			'setting' => array(),
			'member' => array(),
			'group' => array(),
			'cookie' => array(),
			'style' => array(),
			'cache' => array(),
			'session' => array(),
			'lang' => array(),
			'my_app' => array(),
			'my_userapp' => array(),

			'fid' => 0,
			'tid' => 0,
			'forum' => array(),
			'thread' => array(),
			'rssauth' => '',

			'home' => array(),
			'space' => array(),

			'block' => array(),
			'article' => array(),

			'action' => array(
				'action' => APPTYPEID,
				'fid' => 0,
				'tid' => 0,
			),

			'mobile' => '',
			'notice_structure' => array(
				'mypost' => array('post','pcomment','activity','reward','goods','at'),
				'interactive' => array('poke','friend','wall','comment','click','sharenotice'),
				'system' => array('system','myapp','credit','group','verify','magic','task','show','group','pusearticle','mod_member','blog','article'),
				'manage' => array('mod_member','report','pmreport'),
				'app' => array(),
			),
			'mobiletpl' => array('1' => 'mobile', '2' => 'touch', '3' => 'wml', 'yes' => 'mobile'),
		);
		$_G['PHP_SELF'] = dhtmlspecialchars($this->_get_script_url());
		$_G['basescript'] = CURSCRIPT;
		$_G['basefilename'] = basename($_G['PHP_SELF']);
		$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
		if(defined('IN_API')) {
			$sitepath = preg_replace("/\/api\/?.*?$/i", '', $sitepath);
		} elseif(defined('IN_ARCHIVER')) {
			$sitepath = preg_replace("/\/archiver/i", '', $sitepath);
		}
		$_G['isHTTPS'] = ($_SERVER['HTTPS'] && strtolower($_SERVER['HTTPS']) != 'off') ? true : false;
		$_G['siteurl'] = dhtmlspecialchars('http'.($_G['isHTTPS'] ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

		$url = parse_url($_G['siteurl']);
		$_G['siteroot'] = isset($url['path']) ? $url['path'] : '';
		$_G['siteport'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' : ':'.$_SERVER['SERVER_PORT'];

		if(defined('SUB_DIR')) {
			$_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
			$_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
		}

		$this->var = & $_G;

	}

	private function _get_script_url() {
		if(!isset($this->var['PHP_SELF'])){
			$scriptName = basename($_SERVER['SCRIPT_FILENAME']);
			if(basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
			} else if(basename($_SERVER['PHP_SELF']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['PHP_SELF'];
			} else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
				$this->var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
			} else if(($pos = strpos($_SERVER['PHP_SELF'],'/'.$scriptName)) !== false) {
				$this->var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			} else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT']) === 0) {
				$this->var['PHP_SELF'] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
				$this->var['PHP_SELF'][0] != '/' && $this->var['PHP_SELF'] = '/'.$this->var['PHP_SELF'];
			} else {
				system_error('request_tainting');
			}
		}
		return $this->var['PHP_SELF'];
	}

	private function _init_input() {
		if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
			system_error('request_tainting');
		}

		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
		}

		$prelength = strlen($this->config['cookie']['cookiepre']);
		foreach($_COOKIE as $key => $val) {
			if(substr($key, 0, $prelength) == $this->config['cookie']['cookiepre']) {
				$this->var['cookie'][substr($key, $prelength)] = $val;
			}
		}


		if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
			$_GET = array_merge($_GET, $_POST);
		}

		if(isset($_GET['page'])) {
			$_GET['page'] = rawurlencode($_GET['page']);
		}

		if(!(!empty($_GET['handlekey']) && preg_match('/^\w+$/', $_GET['handlekey']))) {
			unset($_GET['handlekey']);
		}

		if(!empty($this->var['config']['input']['compatible'])) {
			foreach($_GET as $k => $v) {
				$this->var['gp_'.$k] = daddslashes($v);
			}
		}

		$this->var['mod'] = empty($_GET['mod']) ? '' : dhtmlspecialchars($_GET['mod']);
		$this->var['inajax'] = empty($_GET['inajax']) ? 0 : (empty($this->var['config']['output']['ajaxvalidate']) ? 1 : ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || $_SERVER['REQUEST_METHOD'] == 'POST' ? 1 : 0));
		$this->var['page'] = empty($_GET['page']) ? 1 : max(1, intval($_GET['page']));
		$this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? dhtmlspecialchars($this->var['cookie']['sid']) : '';

		if(empty($this->var['cookie']['saltkey'])) {
			$this->var['cookie']['saltkey'] = random(8);
			dsetcookie('saltkey', $this->var['cookie']['saltkey'], 86400 * 30, 1, 1);
		}
		$this->var['authkey'] = md5($this->var['config']['security']['authkey'].$this->var['cookie']['saltkey']);

	}

	private function _init_config() {

		$_config = array();
		@include DISCUZ_ROOT.'./config/config_global.php';
		if(empty($_config)) {
			if(!file_exists(DISCUZ_ROOT.'./data/install.lock')) {
				header('location: install');
				exit;
			} else {
				system_error('config_notfound');
			}
		}

		if(empty($_config['security']['authkey'])) {
			$_config['security']['authkey'] = md5($_config['cookie']['cookiepre'].$_config['db'][1]['dbname']);
		}

		if(empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
			define('DISCUZ_DEBUG', false);
			error_reporting(0);
		} elseif($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
			define('DISCUZ_DEBUG', true);
			error_reporting(E_ERROR);
			if($_config['debug'] === 2) {
				error_reporting(E_ALL);
			}
		} else {
			define('DISCUZ_DEBUG', false);
			error_reporting(0);
		}
		define('STATICURL', !empty($_config['output']['staticurl']) ? $_config['output']['staticurl'] : 'static/');
		$this->var['staticurl'] = STATICURL;

		$this->config = & $_config;
		$this->var['config'] = & $_config;

		if(substr($_config['cookie']['cookiepath'], 0, 1) != '/') {
			$this->var['config']['cookie']['cookiepath'] = '/'.$this->var['config']['cookie']['cookiepath'];
		}
		$this->var['config']['cookie']['cookiepre'] = $this->var['config']['cookie']['cookiepre'].substr(md5($this->var['config']['cookie']['cookiepath'].'|'.$this->var['config']['cookie']['cookiedomain']), 0, 4).'_';


	}

	private function _init_output() {


		if($this->config['security']['attackevasive'] && (!defined('CURSCRIPT') || !in_array($this->var['mod'], array('seccode', 'secqaa', 'swfupload')) && !defined('DISABLEDEFENSE'))) {
			require_once libfile('misc/security', 'include');
		}

		if(!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
			$this->config['output']['gzip'] = false;
		}

		$allowgzip = $this->config['output']['gzip'] && empty($this->var['inajax']) && $this->var['mod'] != 'attachment' && EXT_OBGZIP;
		setglobal('gzipcompress', $allowgzip);

		if(!ob_start($allowgzip ? 'ob_gzhandler' : null)) {
			ob_start();
		}

		setglobal('charset', $this->config['output']['charset']);
		define('CHARSET', $this->config['output']['charset']);
		if($this->config['output']['forceheader']) {
			@header('Content-Type: text/html; charset='.CHARSET);
		}

	}

	public function reject_robot() {
		if(IS_ROBOT) {
			exit(header("HTTP/1.1 403 Forbidden"));
		}
	}

	private function _xss_check() {

		static $check = array('"', '>', '<', '\'', '(', ')', 'CONTENT-TRANSFER-ENCODING');

		if(isset($_GET['formhash']) && $_GET['formhash'] !== formhash()) {
			system_error('request_tainting');
		}

		if($_SERVER['REQUEST_METHOD'] == 'GET' ) {
			$temp = $_SERVER['REQUEST_URI'];
		} elseif(empty ($_GET['formhash'])) {
			$temp = $_SERVER['REQUEST_URI'].file_get_contents('php://input');
		} else {
			$temp = '';
		}

		if(!empty($temp)) {
			$temp = strtoupper(urldecode(urldecode($temp)));
			foreach ($check as $str) {
				if(strpos($temp, $str) !== false) {
					system_error('request_tainting');
				}
			}
		}

		return true;
	}

	private function _get_client_ip() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			foreach ($matches[0] AS $xip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
					$ip = $xip;
					break;
				}
			}
		}
		return $ip;
	}

	private function _init_db() {
		if($this->init_db) {
			$driver = function_exists('mysql_connect') ? 'db_driver_mysql' : 'db_driver_mysqli';
			if(getglobal('config/db/slave')) {
				$driver = function_exists('mysql_connect') ? 'db_driver_mysql_slave' : 'db_driver_mysqli_slave';
			}
			DB::init($driver, $this->config['db']);
		}
	}

	private function _init_session() {

		$sessionclose = !empty($this->var['setting']['sessionclose']);
		$this->session = $sessionclose ? new discuz_session_close() : new discuz_session();

		if($this->init_session)	{
			$this->session->init($this->var['cookie']['sid'], $this->var['clientip'], $this->var['uid']);
			$this->var['sid'] = $this->session->sid;
			$this->var['session'] = $this->session->var;

			if(!empty($this->var['sid']) && $this->var['sid'] != $this->var['cookie']['sid']) {
				dsetcookie('sid', $this->var['sid'], 86400);
			}

			if($this->session->isnew) {
				if(ipbanned($this->var['clientip'])) {
					$this->session->set('groupid', 6);
				}
			}

			if($this->session->get('groupid') == 6) {
				$this->var['member']['groupid'] = 6;
				if(!defined('IN_MOBILE_API')) {
					sysmessage('user_banned');
				} else {
					mobile_core::result(array('error' => 'user_banned'));
				}
			}

			if($this->var['uid'] && !$sessionclose && ($this->session->isnew || ($this->session->get('lastactivity') + 600) < TIMESTAMP)) {
				$this->session->set('lastactivity', TIMESTAMP);
				if($this->session->isnew) {
					if($this->var['member']['lastip'] && $this->var['member']['lastvisit']) {
						dsetcookie('lip', $this->var['member']['lastip'].','.$this->var['member']['lastvisit']);
					}
					C::t('common_member_status')->update($this->var['uid'], array('lastip' => $this->var['clientip'], 'port' => $this->var['remoteport'], 'lastvisit' => TIMESTAMP));
				}
			}

		}
	}

    protected static function createNickname($nickname){
        return substr($nickname,-10,10).'_'.mt_rand(1000,9999);
    }

    /**
     * 数据同步
     * @param $kkdUid
     * @param $kkdAvatar
     * @param $ip
     * @param string $customerid
     * @param string $kkdPwd
     * @param string $token
     * @param int $kkdphone
     * @param string $kkdname
     * @return array
     */
    public static function kkdSync($kkdUid,$kkdAvatar,$ip , $customerid = '' , $kkdPwd = '' , $token = '' , $kkdphone = 0 , $kkdname = ''){
        //检测用户ID是否注册用户名
        global $_G;
        $initName = $customerid;
        $username = self::createNickname($initName);
        $ret = checkbbsnickname($kkdUid,$username);
        var_log('check nickname regist result:' . (is_array($ret) ? json_encode($ret) : 'false'));
        if(!function_exists('jumpToChangeName')){
            function jumpToChangeName($kkdUid , $msg = '还未设置社区昵称,现在转入社区昵称设置页面'){
                ob_end_clean();

                if(strpos($_SERVER['SCRIPT_NAME'] , 'changenickname.php')!==false){
                    var_log('this is changenickname.php');
                    return false;
                }
                dsetcookie('kkdUid',authcode($kkdUid,'ENCODE'),'31536000');
                if (checkmobile() == 2) {
                    showmessage($msg ,'changenicknametouch.html');
                } else {
                    header('Location:changenickname.html');
                }
                exit();
            }
        }
        $db = new discuz_table(['table'=>"common_member_nickname",'pk'=>'uid']);
        if($ret === false){
            $nameExist = C::t('common_member')->fetch_by_username($username);
            var_log($nameExist,'check nickname >>>');
            $num = 0;
            while($num < 5 && $nameExist){
                $num++;
                $username = self::createNickname($initName);
                $nameExist = C::t('common_member')->fetch_by_username($username);
                var_log($nameExist,'retry check nickname >>>');
            }
            if($nameExist){
                return [];
            }else{
                function str_get_rand(){
                    $str_len = 15;
                    $char_lib = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
                    $res = '';

                    for($i = 0; $i < $str_len; $i++){
                        //从字符库中随机一个位置[数字]
                        $str_loc = rand(0, strlen($char_lib) - 1);
                        $res .= $char_lib[$str_loc];
                    }
                    return $res;
                }
                $ret = [
                    'uid'=>$kkdUid,
                    'nickname'=>$username,
                    'pwd'=>str_get_rand(),
                    'token'=>$token,
                    'kkdname'=>$kkdname,
                    'kkdphone'=>$kkdphone
                ];
                //用户的快快贷信息插入数据库
                try{
                    $upRet = $db->insert($ret);
                }catch (\Exception $e){
                    var_log($e->getMessage());
                    jumpToChangeName($kkdUid);
                }
                var_log('upRet:'.$upRet);
            }
        }else{
            //设置快快贷昵称
            $updateData = [];
            if($token && $token != $ret['token'])$updateData['token'] = $token;
            if($kkdname && $kkdname != $ret['kkdname'])$updateData['kkdname'] = $kkdname;
            if($kkdphone && !$ret['kkdphone'])$updateData['kkdphone'] = $kkdphone;
            try{
                if($updateData)$db->update($kkdUid , $updateData);
            }catch (\Exception $e){
                var_log($e->getMessage(),'[Exception]when update nickname');
            }
        }
        $username = $ret['nickname'];
        $kkdPwd = $ret['pwd'];
        $bbsId = $ret['bbsId'];
//						$kkdName = $kkdRet['data']['customerName'];
//						$kkdName = substr($kkdName, 0, 3) . '****' . substr($kkdName, -4);

        //获取这个用户在bbs上的信息，没有的话，自动注册
//						$kkdPwd = 'xlOIe_e2ciLsckpJljvaeKIwCG';//len = 32;
        $kkdEmail = '8_' . uniqid() . mt_rand(100000, 999999) . '@dev.om.888';//len = 32

        if(!function_exists('uc_user_login')) {
            loaducenter();
        }

        $_result = uc_user_login($username, $kkdPwd, 0, 1, '', '', $ip);
        switch($_result[0]){
            case -4 : $_G['errorMsg'] = '登录失败,错误次数太多';return [] ;break;
            case -2 : $_G['errorMsg'] = '登录失败,帐号密码错误';return [] ;break;
        }

        var_log('uc_user_login return result:' . json_encode($_result));
        list($discuz_uid, $_username, $discuz_pw, $_email, $duplicate) = $_result;
        if ($discuz_uid <= 0) {
            var_log('register start');
            //un reg
            $discuz_uid = uc_user_register($username, $kkdPwd, $kkdEmail, '', '', $ip);
            if($discuz_uid <= 0) {
                //register failed;
                var_log('register failed...'.$discuz_uid);
                jumpToChangeName($kkdUid,'您的昵称不符合社区的要求,请重新设置');
            } else {
                C::t('common_member')->insert($discuz_uid, $username, $kkdPwd, $kkdEmail, $ip, 10, ['credits' => '', 'frofile' => '', 'emailstatus' => '']);
                //更新用户头像
                C::t('ucenter_avatar')->insert($discuz_uid, !empty($kkdAvatar) ? $kkdAvatar : '', 1, 1);
                include_once libfile('function/stat');
                updatestat('register');

                require_once libfile('cache/userstats', 'function');
                build_cache_userstats();

                $_result = uc_user_login($username, $kkdPwd, 0, 1, '', '', $ip);
                list($discuz_uid, $_username, $discuz_pw, $_email, $duplicate) = $_result;
                if ($discuz_uid <= 0) {
                    //login failed
                }
            }
        } else {
            var_log('login success');
            if (!empty($kkdAvatar)) {
                C::t('ucenter_avatar')->update($discuz_uid, 'url', $kkdAvatar);
            }
        }
        if(empty($bbsId) || $bbsId<0){
            $db->update($kkdUid,['bbsId'=>$discuz_uid]);
        }
        if($discuz_uid < 0 )return [];
        return [
            'uid'=>$discuz_uid,
            'pwd'=>$kkdPwd,
            'username'=>$username
        ];
    }

    protected $isHomePage = 0;

    protected function initCookie(){
        if($_GET['channel'])$this->isHomePage = 1;
        if($this->isHomePage && !$_GET['token']){
            var_log('客户端未登陆,清空cookie...');
            dsetcookie('auth','');
            setcookie('token','');
            $_COOKIE['token'] = '';
        }
        if($_GET['channel'])setcookie('channel',$_GET['channel'],time()+24*3600);
        if($_GET['token'])setcookie('token',$_GET['token'],time()+24*3600);
        if($_GET['customerId'])setcookie('customerId',$_GET['customerId'],time()+24*3600);
        $_GET['channel'] = $_GET['channel']?$_GET['channel']:$_COOKIE['channel'];
        $_GET['channel'] = strtolower($_GET['channel']);
        $_GET['token'] = $_GET['token']?$_GET['token']:$_COOKIE['token'];
        $_GET['customerId'] = $_GET['customerId']?$_GET['customerId']:$_COOKIE['customerId'];
        //var_log($_SERVER['HTTP_USER_AGENT'],'>>>');
        if(strpos($_SERVER['HTTP_USER_AGENT'],'iPhone')!==false){
            var_log('this is ios...');
            defined('DEVICE') or define('DEVICE','ios');
        }else if(strpos($_SERVER['HTTP_USER_AGENT'],'Android')!==false){
            var_log('this is android...');
            defined('DEVICE') or define('DEVICE','android');
        }else{
            var_log('this is pc...');
            defined('DEVICE') or define('DEVICE','pc');
        }
    }

	private function _init_user() {
        GLOBAL $_G;
        $this->initCookie();
		if($this->init_user) {
            $auth = getglobal('auth', 'cookie');
            if($this->isHomePage)$auth = null;
			if($auth) {
                var_log('login by bbs cookie...');
			}else{
                //=========================code start=============================
                $kkdAuth = $_GET['JSESSIONID'] ? : ($_POST['JSESSIONID'] ? : $_COOKIE['JSESSIONID']);
                var_log('get JSESSIONID :' . $kkdAuth);
                if($_GET['JSESSIONID'] || $_POST['JSESSIONID']) setcookie('JSESSIONID',$kkdAuth,time()+15*60);
                if(!empty($kkdAuth) || !empty($_GET['token'])) {
                    if($_GET['token'] && checkmobile()){
                        var_log('get TOKEN from cookie:' . $_GET['token']);
                        $kkdAuth = $_GET['token'];
                        $_CHECK_COOKIE_URL_ = $_G['config']['kkd']['url'].$_G['config']['kkd']['user']['checklogin']['app'].'?token='.$kkdAuth.'&customerId='.$_GET['customerId'];
                    }else{
                        $_CHECK_COOKIE_URL_ = $_G['config']['kkd']['url'].$_G['config']['kkd']['user']['checklogin']['web'].$kkdAuth;
                    }
                    //到官网接口上获取是否登录，获取$uid
                    var_log('curl:'.$_CHECK_COOKIE_URL_);
                    try{
                        $kkdRet = curl_post($_CHECK_COOKIE_URL_,[],[],4);
                    }catch (\Exception $e){
                        var_log('curl:retry');
                        try{
                            $kkdRet = curl_post($_CHECK_COOKIE_URL_,[],[],4);
                        }catch (\Exception $e){
                            $_G['errorMsg'] = '同步失败,服务器未响应';
                        }
                    }
                    if(!$kkdRet)$_G['errorMsg'] = '同步失败,服务器未响应';
                    /*
                    $kkdRet = file_get_contents($_CHECK_COOKIE_URL_);
                    $kkdRet = json_decode($kkdRet, true);
                    */
                    var_log($kkdRet);
                    if($kkdRet['code'] === 0) {
                        //todo 同步用户数据
                        $kkdUid = $kkdRet['data']['customerId'];
                        $kkdAvatar = $kkdRet['data']['imageUrl'];
                        $nickname = $kkdRet['data']['customerName'];
                        $kkdphone = $kkdRet['data']['customerCellphone'];
                        $ip = $this->var['clientip'];
                        $syncRet = self::kkdSync($kkdUid,$kkdAvatar,$ip , $kkdUid , '' , $kkdRet['data']['token']? $kkdRet['data']['token'] : $_GET['token'] , $kkdphone , $nickname);
                        if($syncRet){
                            list($discuz_uid,$kkdPwd) = array_values($syncRet);
                            $auth = authcode("{$kkdPwd}\t{$discuz_uid}", 'ENCODE');
                            $cookietime = 600;
                            dsetcookie('auth', authcode("$kkdPwd\t$discuz_uid", 'ENCODE'), $cookietime);
                            $_G['cookie_auth'] = $_COOKIE['SqSh_2132_auth'];
                        }
                    }
                }
                //-------------------------code end-------------------------------
            }
            if(isset($auth)) {
                $auth = daddslashes(explode("\t", authcode($auth, 'DECODE')));
            }
			list($discuz_pw, $discuz_uid) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;

            var_log('get uid from auth #uid:'.$discuz_uid);
			if($discuz_uid) {
				$user = getuserbyuid($discuz_uid, 1);
			}else{
                var_log('登录失败,清空auth...');
                dsetcookie('auth',null);
            }
			if(!empty($user) && $user['password'] == $discuz_pw) {
				if(isset($user['_inarchive'])) {
					C::t('common_member_archive')->move_to_master($discuz_uid);
				}
				$this->var['member'] = $user;
			} else {
				$user = array();
				$this->_init_guest();
			}

			if($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP) {
				$memberfieldforum = C::t('common_member_field_forum')->fetch($discuz_uid);
				$groupterms = dunserialize($memberfieldforum['groupterms']);
				if(!empty($groupterms['main'])) {
					C::t("common_member")->update($user['uid'], array('groupexpiry'=> 0, 'groupid' => $groupterms['main']['groupid'], 'adminid' => $groupterms['main']['adminid']));
					$user['groupid'] = $groupterms['main']['groupid'];
					$user['adminid'] = $groupterms['main']['adminid'];
					unset($groupterms['main'], $groupterms['ext'][$this->var['member']['groupid']]);
					$this->var['member'] = $user;
					C::t('common_member_field_forum')->update($discuz_uid, array('groupterms' => serialize($groupterms)));
				} elseif((getgpc('mod') != 'spacecp' || CURSCRIPT != 'home') && CURSCRIPT != 'member') {
					dheader('location: home.php?mod=spacecp&ac=usergroup&do=expiry');
				}
			}

			if($user && $user['freeze'] && (getgpc('mod') != 'spacecp' && getgpc('mod') != 'misc'  || CURSCRIPT != 'home') && CURSCRIPT != 'member' && CURSCRIPT != 'misc') {
				dheader('location: home.php?mod=spacecp&ac=profile&op=password');
			}

			$this->cachelist[] = 'usergroup_'.$this->var['member']['groupid'];
			if($user && $user['adminid'] > 0 && $user['groupid'] != $user['adminid']) {
				$this->cachelist[] = 'admingroup_'.$this->var['member']['adminid'];
			}

		} else {
			$this->_init_guest();
		}
		setglobal('groupid', getglobal('groupid', 'member'));
		!empty($this->cachelist) && loadcache($this->cachelist);

		if($this->var['member'] && $this->var['group']['radminid'] == 0 && $this->var['member']['adminid'] > 0 && $this->var['member']['groupid'] != $this->var['member']['adminid'] && !empty($this->var['cache']['admingroup_'.$this->var['member']['adminid']])) {
			$this->var['group'] = array_merge($this->var['group'], $this->var['cache']['admingroup_'.$this->var['member']['adminid']]);
		}

		if($this->var['group']['allowmakehtml'] && isset($_GET['_makehtml'])) {
			$this->var['makehtml'] = 1;
			$this->_init_guest();
			loadcache(array('usergroup_7'));
			$this->var['group'] = $this->var['cache']['usergroup_7'];
			unset($this->var['inajax']);
		}

		if(empty($this->var['cookie']['lastvisit'])) {
			$this->var['member']['lastvisit'] = TIMESTAMP - 3600;
			dsetcookie('lastvisit', TIMESTAMP - 3600, 86400 * 30);
		} else {
			$this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
		}

		setglobal('uid', getglobal('uid', 'member'));
		setglobal('username', getglobal('username', 'member'));
		setglobal('adminid', getglobal('adminid', 'member'));
		setglobal('groupid', getglobal('groupid', 'member'));
        //==========tgh 设置kkdname==============
        if($_G['uid'] > 0){
            $info = getKkdname($_G['uid'] , true);
            setglobal('kkdname', $info['kkdname']);
            setglobal('kkdphone', $info['kkdphone']);
        }
        //----------------------------------------
		if($this->var['member']['newprompt']) {
			$this->var['member']['newprompt_num'] = C::t('common_member_newprompt')->fetch($this->var['member']['uid']);
			$this->var['member']['newprompt_num'] = unserialize($this->var['member']['newprompt_num']['data']);
			$this->var['member']['category_num'] = helper_notification::get_categorynum($this->var['member']['newprompt_num']);
		}

	}

	private function _init_guest() {
		$username = '';
		$groupid = 7;
		if(!empty($this->var['cookie']['con_auth_hash']) && ($openid = authcode($this->var['cookie']['con_auth_hash']))) {
			$this->var['connectguest'] = 1;
			$username = 'QQ_'.substr($openid, -6);
			$this->var['setting']['cacheindexlife'] = 0;
			$this->var['setting']['cachethreadlife'] = 0;
			$groupid = $this->var['setting']['connect']['guest_groupid'] ? $this->var['setting']['connect']['guest_groupid'] : $this->var['setting']['newusergroupid'];
		}
		setglobal('member', array( 'uid' => 0, 'username' => $username, 'adminid' => 0, 'groupid' => $groupid, 'credits' => 0, 'timeoffset' => 9999));
	}

	private function _init_cron() {
		$ext = empty($this->config['remote']['on']) || empty($this->config['remote']['cron']) || APPTYPEID == 200;
		if($this->init_cron && $this->init_setting && $ext) {
			if($this->var['cache']['cronnextrun'] <= TIMESTAMP) {
				discuz_cron::run();
			}
		}
	}

	private function _init_misc() {

		if($this->config['security']['urlxssdefend'] && !defined('DISABLEXSSCHECK')) {
			$this->_xss_check();
		}

		if(!$this->init_misc) {
			return false;
		}
		lang('core');

		if($this->init_setting && $this->init_user) {
			if(!isset($this->var['member']['timeoffset']) || $this->var['member']['timeoffset'] == 9999 || $this->var['member']['timeoffset'] === '') {
				$this->var['member']['timeoffset'] = $this->var['setting']['timeoffset'];
			}
		}

		$timeoffset = $this->init_setting ? $this->var['member']['timeoffset'] : $this->var['setting']['timeoffset'];
		$this->var['timenow'] = array(
			'time' => dgmdate(TIMESTAMP),
			'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+'.$timeoffset) : $timeoffset
		);
		$this->timezone_set($timeoffset);

		$this->var['formhash'] = formhash();
		define('FORMHASH', $this->var['formhash']);

		if($this->init_user) {
			$allowvisitflag = in_array(CURSCRIPT, array('member')) || defined('ALLOWGUEST') && ALLOWGUEST;
			if($this->var['group'] && isset($this->var['group']['allowvisit']) && !$this->var['group']['allowvisit']) {
				if($this->var['uid'] && !$allowvisitflag) {
					if(!defined('IN_MOBILE_API')) {
						showmessage('user_banned');
					} else {
						mobile_core::result(array('error' => 'user_banned'));
					}
				} elseif((!defined('ALLOWGUEST') || !ALLOWGUEST) && !in_array(CURSCRIPT, array('member', 'api')) && !$this->var['inajax']) {
					if(!defined('IN_MOBILE_API')) {
						dheader('location: member.php?mod=logging&action=login&referer='.rawurlencode($this->var['siteurl'].$this->var['basefilename'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '')));
					} else {
						mobile_core::result(array('error' => 'to_login'));
					}
				}
			}
			if(isset($this->var['member']['status']) && $this->var['member']['status'] == -1 && !$allowvisitflag) {
				if(!defined('IN_MOBILE_API')) {
					showmessage('user_banned');
				} else {
					mobile_core::result(array('error' => 'user_banned'));
				}
			}
		}

		if($this->var['setting']['ipaccess'] && !ipaccess($this->var['clientip'], $this->var['setting']['ipaccess'])) {
			if(!defined('IN_MOBILE_API')) {
				showmessage('user_banned');
			} else {
				mobile_core::result(array('error' => 'user_banned'));
			}
		}

		if($this->var['setting']['bbclosed']) {
			if($this->var['uid'] && ($this->var['group']['allowvisit'] == 2 || $this->var['groupid'] == 1)) {
			} elseif(in_array(CURSCRIPT, array('admin', 'member', 'api')) || defined('ALLOWGUEST') && ALLOWGUEST) {
			} else {
				$closedreason = C::t('common_setting')->fetch('closedreason');
				$closedreason = str_replace(':', '&#58;', $closedreason);
				if(!defined('IN_MOBILE_API')) {
					showmessage($closedreason ? $closedreason : 'board_closed', NULL, array('adminemail' => $this->var['setting']['adminemail']), array('login' => 1));
				} else {
					mobile_core::result(array('error' => $closedreason ? $closedreason : 'board_closed'));
				}
			}
		}

		if(CURSCRIPT != 'admin' && !(in_array($this->var['mod'], array('logging', 'seccode')))) {
			periodscheck('visitbanperiods');
		}

		if(defined('IN_MOBILE')) {
			$this->var['tpp'] = $this->var['setting']['mobile']['mobiletopicperpage'] ? intval($this->var['setting']['mobile']['mobiletopicperpage']) : 20;
			$this->var['ppp'] = $this->var['setting']['mobile']['mobilepostperpage'] ? intval($this->var['setting']['mobile']['mobilepostperpage']) : 5;
		} else {
			$this->var['tpp'] = $this->var['setting']['topicperpage'] ? intval($this->var['setting']['topicperpage']) : 20;
			$this->var['ppp'] = $this->var['setting']['postperpage'] ? intval($this->var['setting']['postperpage']) : 10;
		}

		if($this->var['setting']['nocacheheaders']) {
			@header("Expires: -1");
			@header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
			@header("Pragma: no-cache");
		}

		if($this->session->isnew && $this->var['uid']) {
			updatecreditbyaction('daylogin', $this->var['uid']);

			include_once libfile('function/stat');
			updatestat('login', 1);
			if(defined('IN_MOBILE')) {
				updatestat('mobilelogin', 1);
			}
			if($this->var['setting']['connect']['allow'] && $this->var['member']['conisbind']) {
				updatestat('connectlogin', 1);
			}
		}
		if(isset($this->var['member']['conisbind']) && $this->var['member']['conisbind'] && $this->var['setting'] && $this->var['setting']['connect']['newbiespan'] !== '') {
			$this->var['setting']['newbiespan'] = $this->var['setting']['connect']['newbiespan'];
		}

		$lastact = TIMESTAMP."\t".dhtmlspecialchars(basename($this->var['PHP_SELF']))."\t".dhtmlspecialchars($this->var['mod']);
		dsetcookie('lastact', $lastact, 86400);
		setglobal('currenturl_encode', base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));

		if((!empty($_GET['fromuid']) || !empty($_GET['fromuser'])) && ($this->var['setting']['creditspolicy']['promotion_visit'] || $this->var['setting']['creditspolicy']['promotion_register'])) {
			require_once libfile('misc/promotion', 'include');
		}

		$this->var['seokeywords'] = !empty($this->var['setting']['seokeywords'][CURSCRIPT]) ? $this->var['setting']['seokeywords'][CURSCRIPT] : '';
		$this->var['seodescription'] = !empty($this->var['setting']['seodescription'][CURSCRIPT]) ? $this->var['setting']['seodescription'][CURSCRIPT] : '';

	}

	private function _init_setting() {
		if($this->init_setting) {
			if(empty($this->var['setting'])) {
				$this->cachelist[] = 'setting';
			}

			if(empty($this->var['style'])) {
				$this->cachelist[] = 'style_default';
			}

			if(!isset($this->var['cache']['cronnextrun'])) {
				$this->cachelist[] = 'cronnextrun';
			}
		}

		!empty($this->cachelist) && loadcache($this->cachelist);

		if(!is_array($this->var['setting'])) {
			$this->var['setting'] = array();
		}

	}

	public function _init_style() {
		if(defined('IN_MOBILE')) {
			$mobile = max(1, intval(IN_MOBILE));
			if($mobile && $this->var['setting']['styleid'.$mobile]) {
				$styleid = $this->var['setting']['styleid'.$mobile];
			}
		} else {
			$styleid = !empty($this->var['cookie']['styleid']) ? $this->var['cookie']['styleid'] : 0;
		}
		if(intval(!empty($this->var['forum']['styleid']))) {
			$this->var['cache']['style_default']['styleid'] = $styleid = $this->var['forum']['styleid'];
		} elseif(intval(!empty($this->var['category']['styleid']))) {
			$this->var['cache']['style_default']['styleid'] = $styleid = $this->var['category']['styleid'];
		}

		$styleid = intval($styleid);

		if($styleid && $styleid != $this->var['setting']['styleid']) {
			loadcache('style_'.$styleid);
			if($this->var['cache']['style_'.$styleid]) {
				$this->var['style'] = $this->var['cache']['style_'.$styleid];
			}
		}

		define('IMGDIR', $this->var['style']['imgdir']);
		define('STYLEID', $this->var['style']['styleid']);
		define('VERHASH', $this->var['style']['verhash']);
		define('TPLDIR', $this->var['style']['tpldir']);
		define('TEMPLATEID', $this->var['style']['templateid']);
	}

	private function _init_mobile() {
		if(!$this->init_mobile) {
			return false;
		}

		if(!$this->var['setting'] || !$this->var['setting']['mobile']['allowmobile'] || !is_array($this->var['setting']['mobile']) || IS_ROBOT) {
			$nomobile = true;
			$unallowmobile = true;
		}


		$mobile = getgpc('mobile');
		$mobileflag = isset($this->var['mobiletpl'][$mobile]);
		if($mobile === 'no') {
			dsetcookie('mobile', 'no', 3600);
			$nomobile = true;
		} elseif($this->var['cookie']['mobile'] == 'no' && $mobileflag) {
			checkmobile();
			dsetcookie('mobile', '');
		} elseif($this->var['cookie']['mobile'] == 'no') {
			$nomobile = true;
		} elseif(!($mobile_ = checkmobile())) {
			$nomobile = true;
		}
		if(!$mobile || $mobile == 'yes') {
			$mobile = isset($mobile_) ? $mobile_ : 2;
		}

		if(!$this->var['mobile'] && !$unallowmobile) {
			if($mobileflag) {
				dheader("Location:misc.php?mod=mobile");
			}
		}

		if($nomobile || (!$this->var['setting']['mobile']['mobileforward'] && !$mobileflag)) {
			if($_SERVER['HTTP_HOST'] == $this->var['setting']['domain']['app']['mobile'] && $this->var['setting']['domain']['app']['default']) {
				dheader("Location:http://".$this->var['setting']['domain']['app']['default'].$_SERVER['REQUEST_URI']);
				return false;
			} else {
				return false;
			}
		}

		if(strpos($this->var['setting']['domain']['defaultindex'], CURSCRIPT) !== false && CURSCRIPT != 'forum' && !$_GET['mod']) {
			if($this->var['setting']['domain']['app']['mobile']) {
				$mobileurl = 'http://'.$this->var['setting']['domain']['app']['mobile'];
			} else {
				if($this->var['setting']['domain']['app']['forum']) {
					$mobileurl = 'http://'.$this->var['setting']['domain']['app']['forum'].'?mobile=yes';
				} else {
					$mobileurl = $this->var['siteurl'].'forum.php?mobile=yes';
				}
			}
			dheader("location:$mobileurl");
		}
		if($mobile === '3' && empty($this->var['setting']['mobile']['wml'])) {
			return false;
		}
		define('IN_MOBILE', isset($this->var['mobiletpl'][$mobile]) ? $mobile : '2');
		setglobal('gzipcompress', 0);

		$arr = array();
		foreach(array_keys($this->var['mobiletpl']) as $mobiletype) {
			$arr[] = '&mobile='.$mobiletype;
			$arr[] = 'mobile='.$mobiletype;
		}
		$arr = array_merge(array(strstr($_SERVER['QUERY_STRING'], '&simpletype'), strstr($_SERVER['QUERY_STRING'], 'simpletype')), $arr);
		$query_sting_tmp = str_replace($arr, '', $_SERVER['QUERY_STRING']);
		$this->var['setting']['mobile']['nomobileurl'] = ($this->var['setting']['domain']['app']['forum'] ? 'http://'.$this->var['setting']['domain']['app']['forum'].'/' : $this->var['siteurl']).$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=no';

		$this->var['setting']['lazyload'] = 0;

		if('utf-8' != CHARSET) {
			if(strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
				foreach($_POST AS $pk => $pv) {
					if(!is_numeric($pv)) {
						$_GET[$pk] = $_POST[$pk] = $this->mobile_iconv_recurrence($pv);
						if(!empty($this->var['config']['input']['compatible'])) {
							$this->var['gp_'.$pk] = daddslashes($_GET[$pk]);
						}
					}
				}
			}
		}


		if(!$this->var['setting']['mobile']['mobilesimpletype']) {
			$this->var['setting']['imagemaxwidth'] = 224;
		}

		$this->var['setting']['regstatus'] = $this->var['setting']['mobile']['mobileregister'] ? $this->var['setting']['regstatus'] : 0 ;

		$this->var['setting']['thumbquality'] = 50;
		$this->var['setting']['avatarmethod'] = 0;

		$this->var['setting']['mobile']['simpletypeurl'] = array();
		$this->var['setting']['mobile']['simpletypeurl'][0] = $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=1&simpletype=no';
		$this->var['setting']['mobile']['simpletypeurl'][1] =  $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=1&simpletype=yes';
		$this->var['setting']['mobile']['simpletypeurl'][2] =  $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=2';
		unset($query_sting_tmp);
		ob_start();
	}

	public function timezone_set($timeoffset = 0) {
		if(function_exists('date_default_timezone_set')) {
			@date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}

       public function mobile_iconv_recurrence($value) {
		if(is_array($value)) {
			foreach($value AS $key => $val) {
				$value[$key] = $this->mobile_iconv_recurrence($val);
			}
		} else {
			$value = diconv($value, 'utf-8', CHARSET);
		}
		return $value;
	}
}

?>