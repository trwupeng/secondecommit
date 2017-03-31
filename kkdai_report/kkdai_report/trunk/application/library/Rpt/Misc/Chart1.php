<?php
namespace Rpt\Misc;
/* 
 * 报表库使用的图表类库1
 */
class Chart1
{
	protected static $chartAutoId=1;
	/**
	 * 类型：linesSimple 或 barsSimple 或 pieSimple 
	 */
	public $type='pieSimple';
	public $ymd=0;
	public $titleMain='';
	public $titleSub='';
	/**
	 * 需要二级分组统计的时候，记得确保\\Rpt\\Misc\\NameOfGrpInChart里有对应的字段的翻译函数
	 * 
	 */
	public $grpBy='';
	/**
	 * 至少提供act的限制
	 * @var array 
	 */
	public $where=[];
	public function grpBy($field1Name)
	{
		if(!empty($field1Name)){
			$this->grpBy=$field1Name;
		}
	}

	
	public function parseInput($args)
	{
		$this->type = $args['type'];
		$this->titleMain = $args['title'];
		$this->titleSub = $args['subtitle'];
		$this->grpBy = $args['grpBy'];
		$this->ymd=$args['Y_m_d'];


		$this->where = $args['where'];
		if(!empty($this->where)){
			$this->where=  json_decode($this->where,true);
		}
	}
	/**
	 * @return Chart1
	 */
	public function showStd($chartType,$dataRange='weekfor')
	{
		$view = \Sooh\Base\Ini::getInstance()->viewRenderType();
		if($view=='wap'){
			echo '<div class="divChartParent"><div class="divChartTopBtn"  onclick="history.go(-1)"><b style="font-size:12pt"><i>【返回】</i></b></div><div id="main'.self::$chartAutoId.'"  class="divChartDefault"></div></div>';
		}else{
			echo '<div id="main'.self::$chartAutoId.'"  class="divChartDefault"></div>';
		}
		
		$args['type']=$this->type=$chartType;
		$args['title']=$this->titleMain;
		$args['subtitle']=$this->titleSub;
		$args['grpBy']=  $this->grpBy;
		$args['where']=  json_encode($this->where);
		$args['Y_m_d']=  $this->ymd;
		echo \Rpt\Misc\Echarts::ajax('main'.self::$chartAutoId, $this->type, \Sooh\Base\Tools::uri($args, $dataRange.'for'));
		self::$chartAutoId++;
		return $this;
	}

	/**
	 *  @return Chart1
	 */
	public function reset($ymdToo=false)
	{
		$this->type='pieSimple';
		$this->titleMain='';
		$this->titleSub='';
		$this->grpBy='';
		$this->where=[];
		if($ymdToo){
			$this->ymd=0;
		}
		return $this;
	}
}
