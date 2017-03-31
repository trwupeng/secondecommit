<?php
namespace Prj\Misc;
/**
 * 一些功能函数，将来可能调整位置
 *
 * @author simon.wang
 */
class Funcs {
	/**
	 * 平台产生的各种零头
	 * @param number $num
	 * @param string $desc
	 */
	public static function addOdd($num,$desc)
	{
		\Sooh\DB\Broker::getInstance()->addRecord('db_p2p.tb_odd', ['odd'=>$num,'desc'=>$desc]);
	}
	
	public static function uriReal($arg)
	{
		
	}
	
	public static function copartnerIdWithcopartnerName() {
		$dbDefault = \Sooh\Base\Ini::getInstance()->get('dbConf.default')['dbEnums']['default'];
		$rs =\Sooh\DB\Broker::getInstance('default')
				->getAssoc($dbDefault.'.tb_copartner_0', 'copartnerId', 'copartnerName, copartnerAbs');
		return $rs;
	}

    /**
     * 发post请求
     */
    public static function curl_post($url,$post=[],$time=5)
    {
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $time );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
}
