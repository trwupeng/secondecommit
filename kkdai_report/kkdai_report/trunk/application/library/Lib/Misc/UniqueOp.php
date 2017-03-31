<?php
namespace Lib\Misc;
/**
 * 关键动作操作唯一一次，避免同时提交，TODO：同一时刻最多五个未完成的行为
 *
 * @author simon.wang
 */
class UniqueOp {
	/**
	 * 
	 * @param string $target 
	 * @param string $route 例子：module/controller/action
	 * @return string
	 */
    const max = 5;

	public static function createFor($target,$route=null)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();
        //var_log($sess);
		$rs = $sess->get(self::sessId);
		if($route===nul){
			$route = self::req();
		}
		if(count($rs)>self::max){
            do{
                unset($rs[key($rs)]);
            }while(count($rs)<self::max);
        }
		$ret = $rs[$route.'/'.$target] = self::newId();
		$sess->set(self::sessId, $rs);
		return $ret;
	}
	protected static function newId()
	{
		return  md5(microtime(true));
	}
	protected static function req()
	{
		$ini = \Sooh\Base\Ini::getInstance();
		$route = $ini->get('request');
		return $route['module'].'/'.$route['controller'].'/'.$route['action'];
	}
	const sessId = '__UniqueOP__';
	/**
	 * 指定opid是否存在
	 * @param string $opid
	 * @return boolean
	 */
	public static function check($opid)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();
		$rs = $sess->get(self::sessId);

		foreach($rs as $md5){
			if($md5==$opid){
				return true;
			}
		}
		
		return false;
	}
	/**
	 * 删除opid,如果需要同时生成新的id
	 * @param string $opid
	 * @param bool $createNew 是否生成新的id
	 * @return string 新的id
	 */
	public static function remove($opid,$createNew=false)
	{
		$sess = \Sooh\Base\Session\Data::getInstance();
		$rs = $sess->get(self::sessId);

		foreach($rs as $route=>$md5){
			if($md5==$opid){
				if($createNew===false){
					unset($rs[$route]);
				}else{
					 $rs[$route]=self::newId();
				}
			}
		}
		$sess->set(self::sessId, $rs);
		$sess->shutdown();
		return $rs[$route];
	}
}
