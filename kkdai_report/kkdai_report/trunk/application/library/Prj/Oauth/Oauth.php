<?php
namespace Prj\Oauth;

/**
 * Class Oauth
 * 继续重构
 * 自动保存token，请求状态码自检查，自动刷新-重新请求
 * @package Prj\Oauth
 * @author  LTM <605415184@qq.com>
 */
class Oauth {
	/**
	 * 临时码code
	 * @var string
	 */
	private $code;

	/**
	 * 回调地址
	 * @var string
	 */
	private $redirectUri;

	/**
	 * accessToken
	 * @var string
	 */
	private $accessToken;

	/**
	 * refreshToken
	 * @var string
	 */
	private $refreshToken;

	/**
	 * Oauth Api 跟路径
	 * @var string
	 */
	private $baseURL;

	/**
	 * 调用模式
	 * @var string
	 */
	private $mode = 'standard';

	const errServerBusy = '服务器忙';

	/**
	 * 不允许直接访问的方法列表
	 * //TODO 待补充
	 * @var array
	 */
	protected $disableFuns = ['appreg', 'webReg'];

	/**
	 * 构造方法，用于实例化Oauth
	 * @param string $code        临时码
	 * @param string $redirectUri 回调地址
	 * @param string  $mode       应用模式
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function __construct($code = '', $redirectUri = '', $mode = 'standard') {
		$this->_set('baseURL', \Sooh\Base\Ini::getInstance()->get('uriBase')['oauth'] . '/index.php?__VIEW__=json&');

		if ((empty($code) || empty($redirectUri)) && empty($mode)) {
			$this->_set('accessToken', \Sooh\Base\Session\Data::getInstance()->get('accessToken'), false, false);
			$this->_set('refreshToken', \Sooh\Base\Session\Data::getInstance()->get('refreshToken'), false, false);
		} elseif ((empty($code) || empty($redirectUri)) && $mode == 'nonStandardMode') {
			//其他模式
			$this->mode = $mode;
		} else {
			$this->_set('code', $code);
			$this->_set('redirectUri', $redirectUri);

			$this->getToken($code, $redirectUri);
		}
	}

	/**
	 * 其他模式，一般用于不需要accessToken的非标准Oauth模式如appreg、appLogin
	 * @param array $args 其他模式需要的参数:['clientId' => '', 'clientSecret' => '', 'scope' => '', 'func' => 'resetPwd']
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	public function invokeMode($args) {
		if ($this->mode != 'nonStandardMode') {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		if (!isset($args['func'])) {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		} else {
			$args['__'] = 'oauth/' . $args['func'];
			unset($args['func']);
		}

		$ret = $this->http(http_build_query($args), false);

		return $ret;
	}

	/**
	 * 换取token
	 * @param string $code        code
	 * @param string $redirectUri redirectUri
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function getToken($code, $redirectUri) {
		$params = [
			'__'          => 'oauth/token',
			'code'        => $code,
			'redirectUri' => $redirectUri,
		];

		$ret = $this->http(http_build_query($params));
		$this->_set('accessToken', $ret['accessToken'], false, true, ['expire' => $ret['accessTokenExpiresIn'] - 10]);
		$this->_set('refreshToken', $ret['refreshToken'], false, true, ['expire' => $ret['refreshTokenExpiresIn'] - 10]);
	}

	/**
	 * 获取用户资源
	 * @return array
	 * @throws \Sooh\Base\ErrException
	 */
	public function getResource() {
		$params = [
			'__'          => 'oauth/userInfo',
			'accessToken' => $this->_get('accessToken'),
		];
		return $this->http(http_build_query($params));
	}

	/**
	 * 刷新token
	 * @param $refreshToken
	 * @return array ['accessToken' => '*****', 'refreshToken' => '*****']
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function refreshToken() {
		$params = [
			'__'           => 'oauth/refresh',
			'refreshToken' => $this->_get('refreshToken')
		];
		$ret    = $this->http(http_build_query($params));

		$this->_set('accessToken', $ret['accessToken'], false, true, ['expire' => $ret['accessTokenExpiresIn']]);
		$this->_set('refreshToken', $ret['refreshToken'], false, true, ['expire' => $ret['refreshTokenExpiresIn']]);
	}

	/**
	 * 执行Oauth的方法
	 * @param string $name Oauth的方法名
	 * @param array  $args 传递的参数
	 * @return array
	 * @throws \ErrorException
	 * @throws \Sooh\Base\ErrException
	 */
	public function invokeOauth($name, $args) {
		if (!is_string($name) || !is_array($args) || in_array($name, $this->disableFuns)) {
			$this->loger->ret   = 'try invoke Oauth';
			$this->loger->sarg1 = json_encode($name);
			$this->loger->sarg2 = json_encode($args);
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		$params = [
			'__'          => 'oauth/' . $name,
			'accessToken' => $this->_get('accessToken'),
		];

		$ret = $this->http(http_build_query(array_merge($args, $params)));

		return $ret;
	}

	/**
	 * 属性获取器
	 * @param string     $name       属性名
	 * @param bool|false $allowNulls 是否允许为空
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	protected function _get($name, $allowNulls = false) {
		if (!$allowNulls && empty($this->$name)) {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		return $this->$name;
	}

	/**
	 * 属性设置器
	 * @param string     $name       属性名
	 * @param    string  $value      属性值
	 * @param bool|false $allowNulls 是否允许为空
	 * @param bool|true  $setExtra   设置额外参数
	 * @param array|[] $extraArgs 额外参数
	 * @throws \Sooh\Base\ErrException
	 */
	protected function _set($name, $value, $allowNulls = false, $setExtra = true, $extraArgs = []) {
		if (!$allowNulls && empty($value)) {
			throw new \Sooh\Base\ErrException(self::errServerBusy);
		}

		if (in_array($name, ['accessToken', 'refreshToken']) && $setExtra && !empty($extraArgs['expire'])) {
			\Sooh\Base\Session\Data::getInstance()->set($name, $value, $extraArgs['expire']);
		} else {

		}
		$this->$name = $value;
	}

	/**
	 * 远程请求OauthApi
	 * @param   string  $url     请求URL
	 * @param bool|true $check   是否对返回状态吗进行检查
	 * @param string    $baseURL 根URL
	 * @return mixed
	 * @throws \Sooh\Base\ErrException
	 */
	private function http($url, $check = true, $baseURL = '') {
		if (empty($baseURL)) {
			$baseURL = $this->_get('baseURL');
		}
		$ret = json_decode(\Sooh\Base\Tools::httpGet($baseURL . $url), true);
		if ($ret['code'] == 200) {
			return $ret['info'] ? : $ret;
		} else {
			\Sooh\Base\Log\Data::getInstance('c')->ret   = 'get Oauth api';
			\Sooh\Base\Log\Data::getInstance('c')->sarg1 = $url;

			if ($check && $ret['code'] == '60017') {
				//accessToken过期，需要refresh
				$this->refreshToken();
				return $this->http($url);
			} else {
				throw new \Sooh\Base\ErrException($ret['msg'] ? : self::errServerBusy, $ret['code'] ? : 400);
			}
		}
	}
}