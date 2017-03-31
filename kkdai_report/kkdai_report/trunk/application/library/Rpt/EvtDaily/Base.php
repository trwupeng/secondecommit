<?php
namespace Rpt\EvtDaily;
/**
 * Description of Super
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Base {
	private static $_copies=array();
	/**
	 * 计数基数，如果返回100，那么12.34再记录入数据库后就是1234，取出时还原成12.34
	 * @return int
	 */
	protected function basement(){return 1;}
	/**
	 * 根据名称构建一个实例
	 * 
	 * @param String $name
	 * @return \Rpt\EvtDaily\Base
	 */
	public static function getCopy($name)
	{
		if(!isset(self::$_copies[$name])){
			$real = "\\Rpt\\EvtDaily\\$name";
			self::$_copies[$name] = new $real;
		}
		return self::$_copies[$name];
	}
	/**
	 * identifier
	 * @return string
	 */
	protected function actName(){throw new \ErrorException('actName not set');}
	/**
	 * 显示的名称
	 * @return string
	 */
	public static function displayName(){throw new \ErrorException('displayName not set');}
	/**
	 * 是否是组合的数据 
	 * @return array [field1,+,field2]
	 */
	public function formula()
	{
		return null;
	}
	protected $daily=array();
	
	public static $tb = \Rpt\Tbname::tb_evtdaily;
	
	/**
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param int $ymd
	 * @param string $cmpMethod 例如 < [
	 * @return array
	 */
	public function sumBefore($db,$ymd,$cmpMethod='<')
	{
		if($cmpMethod!='<'){
			$cmpMethod='[';
		}
		$rs = $db->getRecords(self::$tb, 'clienttype,contractid,flgext01,sum(n) n',array('ymd'.$cmpMethod=>$ymd,'act'=>$this->actName()),'group clienttype group contractid group flgext01');
		$basement = $this->basement();
		if($basement!=1){
			foreach($rs as $i=>$r){
				$rs[$i]['n']=$r['n']/$basement;
			}
		}
		return $rs;
	}

	public function add($num,$clientType,$contractid,$extFlg1=0,$extFlg2=0)
	{
		if($this->formula()!==null){
			throw new \ErrorException('add method of '.__CLASS__.' not support as formula setted');
		}
		if(empty($contractid)){
			$contractid=0;
		}
		if(empty($clientType)){
			$clientType=0;
		}
		if(empty($extFlg1)){
			$extFlg1=0;
		}
		$copartnerId = substr($contractid,0,4)-0;
		$this->daily[$clientType][$copartnerId][$extFlg1][$extFlg2]+=$num*$this->basement();
		$this->totalAdd+=$num;
	}
	public function reset()
	{
		$this->daily=[];
		$this->totalAdd=0;
	}
	public $totalAdd=0;
	/**
	 * 
	 * @param \Sooh\DB\Interfaces\All $db
	 */
	public function save($db,$ymd)
	{
		if($this->formula()!==null){
			throw new \ErrorException('save method of '.__CLASS__.' not support as formula setted');
		}
		$tb = self::$tb;
		$onDupUpd=array('n');
		$db->delRecords($tb,['ymd'=>$ymd,'act'=>$this->actName()]);
		foreach($this->daily as $clientType=>$rs1){
			foreach($rs1 as $copartnerId=>$rs2){
				foreach($rs2 as $extflg01=>$rs3){
					foreach($rs3 as $extflg02=>$n){
						$db->ensureRecord($tb, 
						array('ymd'=>$ymd,'act'=>$this->actName(),'clienttype'=>$clientType,
							'copartnerId'=>$copartnerId,'flgext01'=>$extflg01,'flgext02'=>$extflg02,
							'n'=>$n),
						 $onDupUpd);
					}
				}
			}
		}
	}
	
	/**
	 * 根据条件获取总数或某个类型的分布情况
	 * 
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param int $ymd
	 * @param string $grpBy 要统计分布
	 * @param array $where 
	 * @return mixed 没有grpBy要求的时候，返回数值，有grpBy要求的时候，返回各个grp对应的值
	 */
	public  function numOfAct($db,$ymd,$grpBy=null,$where=null)
	{
		$formula = $this->formula();
		$basement= $this->basement();
		$real=array('ymd'=>$ymd,'act'=>$this->actName());
		if(is_array($where)){
			foreach($where as $k=>$v){
				$real[$k]=$v;
			}
		}
		
		if(empty($grpBy)){//没有二级分类统计的要求的情况
			if(!empty($formula)){
				$n = 0;
				$lastMethod='+';
				
				foreach($formula as $id){
					switch ($id){
						case '+':
						case '-':
							$lastMethod=$id;
							break;
						default:
							switch($lastMethod){
								case '+':
									$n += self::getCopy($id)->numOfAct($db, $ymd, null, $where);
									break;
								case '-':
									$n -= self::getCopy($id)->numOfAct($db, $ymd, null, $where);
									break;
							}
							break;
					}
				}
				
			}else {
				$n = $db->getOne(self::$tb, 'sum(n)',$real)-0;
			}
			return  $basement!=1 ? $n/$basement : $n;
		}else{//有二级分类统计要求的情况
			if(!empty($formula)){
				$ret = array();
				$lastMethod='+';
				foreach($formula as $id){
					switch ($id){
						case '+':
						case '-':
							$lastMethod=$id;
							break;
						default:
							$tmp = self::getCopy($id)->numOfAct($db, $ymd, $grpBy, $where);
							
							switch($lastMethod){
								case '+':
									foreach($tmp as $k=>$v){
										$ret[$k]+=$v;
									}
									break;
								case '-':
									foreach($tmp as $k=>$v){
										$ret[$k]-=$v;
									}
									break;
							}
							break;
					}
				}
			}else {
				$ret= $db->getPair(self::$tb, $grpBy,'sum(n)',$real,'group '.$grpBy);
			}
			if($basement!=1){
				foreach($ret as $i=>$r){
					$ret[$i]=$r/$basement;
				}
				return $ret;
			}else {
				return $ret;
			}
		}
	}
	
	public static function AllEvt()
	{
		$ret=array();
		$dh = opendir(__DIR__);
		if(!$dh){
			throw new \ErrorException('can not read evtDefine-dir');
		}
		while(false!==($f=  readdir($dh))){
			if($f[0]!='.' && $f!='Base.php'){
				$f = substr($f,0,-4);
				$ret[$f] = call_user_func("\\Rpt\\EvtDaily\\$f::displayName");
			}
		}
		closedir($dh);
		return $ret;
	}
	
	public function getActName () {
	    return $this->actName();
	}
	
	public function divisor () {
	    return 1;
	}
}
