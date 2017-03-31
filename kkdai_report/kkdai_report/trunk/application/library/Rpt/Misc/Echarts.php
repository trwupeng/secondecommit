<?php
namespace Rpt\Misc;
/**
 * current cupport : pieSimple,lineSimple,linesSimple,barSimple,barsSimple.
 * Usage sample:
////////////////////////////////////////controller/////////////////////////////////////////
$this->_view->assign('rptData',  Sooh\HTML\Chart::format('用户来源'.rand(100,999), 
			array('一季度','二季度','三季度','四季度',), 
			array(
				'百度'=>array(rand(100,999),rand(100,999),rand(100,999),rand(100,999)),
				'新浪'=>array(rand(100,999),rand(100,999),rand(100,999),rand(100,999))
				)
			)
		);
//////////////////////////////////////////view///////////////////////////////////////////////
   <title>Hello World2</title>
<?php echo Sooh\HTML\Base::includeJS('jquery');?>	
</head>
<body>
	<div id="main1" style="width:660px;height:400px;border:1px solid red"></div>
<?php
echo Sooh\HTML\Base::includeJS('echarts-all.js');
echo Sooh\HTML\Chart::jsFunc();
echo Sooh\HTML\Chart::ajax('main1', 'pieSimple', "/test/index/rpt");
?>
 * 
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Echarts {
	public static function format($title, $trHeader,$records,$subtitle='')
	{
		$rs=array();
		if(isset($trHeader[0])){
			$rs[]=$trHeader;
		}else {
			$ks = array_keys($trHeader);
			$rs[] = array_merge ($trHeader,array());
		}
		$series = array();
		foreach($records as $k=>$r){
			$series[]=$k;
			if(sizeof($r)!==sizeof($trHeader)){
				throw new ErrorException('col dismatch for chart');
			}
			if(isset($r[0])){
				$rs[]=$r;
			} else {
				$tmp=array();
				foreach($ks as $k){
					$tmp[] = $r[$k];
				}
				$rs[]=$tmp;
			}
		}
		return array('title'=>$title,'subtitle'=>empty($subtitle)?'':$subtitle,
			'__TRANS__'=>$_REQUEST['__TRANS__'],	'rs'=>$rs,  'series'=>$series,
		);
	}
	
	public static function jsFunc()
	{
		return <<<EOF
<script type="text/javascript">
	var echart_data = {};
	function echart_dataForLegend(type,title)
	{
		for(var i in echart_data[type]){
			if(echart_data[type][i]['name']==title)
				return echart_data[type][i]['value'];
		}
		return null;
	}
	function echart_show(response,status)
	{
		if(status == 'success'){
			var id_chart = response.rptData.__TRANS__.split('#');
			var myChart = echarts.init(document.getElementById(id_chart[0]));
			echart_data[id_chart[0]]=[];
			var tooltip = null;
			var legend = null;
			var series = null;
			var xAxis = null;
			var yAxis = null;
			var option = {
						title : {text: response.rptData.title, subtext: response.rptData.subtitle,   x:'center'  },
						animation : false,
					};
			switch(id_chart[1]){
				case 'pieSimple':
					
					for(var i in response.rptData.rs[0]){
						echart_data[id_chart[0]][i]={name:response.rptData.rs[0][i],value:response.rptData.rs[1][i]}
					}
					tooltip = {  trigger: 'item',   formatter: "{b} : {c} ({d}%)"  };
					legend = {orient : 'vertical',  x : 'left',   data:response.rptData.rs[0],
							formatter:function(nm){var n = echart_dataForLegend(id_chart[0],nm);if(n!=null)return nm+': '+n; else return nm; }
						};
					series = [{	type:'pie',	radius : '50%',	center: ['60%', '55%'],	data:echart_data[id_chart[0]]}];
					break;
				case 'barSimple':
					for(var i in response.rptData.rs[0]){
						echart_data[id_chart[0]][i]={name:response.rptData.rs[0][i],value:response.rptData.rs[1][i]}
					}
					tooltip = {  trigger: 'axis',   formatter: "{b} : {c}"  };
					xAxis = [   {   type : 'category', data : response.rptData.rs[0]  , 
								axisLabel:{formatter:function(nm){var n = echart_dataForLegend(id_chart[0],nm);if(n!=null)return nm+'\\n'+n; else return nm; }}
								}   ];
					yAxis = [  {   type : 'value'  }  ];
					series = [{	type:'bar',	name : 'sth', data:response.rptData.rs[1], markLine : {  data : [  {type : 'average', name: '平均值'}    ]    }}];
					break;
				case 'barsSimple':
					for(var i in response.rptData.rs[0]){
						echart_data[id_chart[0]][i]={name:response.rptData.rs[0][i],value:response.rptData.rs[1][i]}
					}
					tooltip = {  trigger: 'axis',   formatter: "{b} : {c}"  };
					legend  = { data:response.rptData.series  , y : 'bottom'  };
					xAxis = [   {   type : 'category', data : response.rptData.rs[0]  }   ];
					yAxis = [  {   type : 'value'  }  ];
					series = [];
					for (var ii in response.rptData.series){
						var kk = parseInt(ii)+1;
						series[ii] = {	type:'bar',	name : response.rptData.series[ii], data:response.rptData.rs[kk], 
										markLine : {  data : [  {type : 'average', name: '平均值'} ] }, itemStyle: {normal: {label:{show:true}}},};
					}
					break;	
				case 'linesSimple':
					for(var i in response.rptData.rs[0]){
						echart_data[id_chart[0]][i]={name:response.rptData.rs[0][i],value:response.rptData.rs[1][i]}
					}
					tooltip = {  trigger: 'axis',   formatter: "{b} : {c}"  };
					legend  = { data:response.rptData.series  , y : 'bottom'  };
					xAxis = [   {   type : 'category', data : response.rptData.rs[0]  }   ];
					yAxis = [  {   type : 'value'  }  ];
					series = [];
					for (var ii in response.rptData.series){
						var kk = parseInt(ii)+1;
						series[ii] = {	type:'line',	name : response.rptData.series[ii], data:response.rptData.rs[kk], 
										markLine : {  data : [  {type : 'average', name: '平均值'} ] }, itemStyle: {normal: {label:{show:true}}},};
					}
					break;		
				case 'lineSimple':
					for(var i in response.rptData.rs[0]){
						echart_data[id_chart[0]][i]={name:response.rptData.rs[0][i],value:response.rptData.rs[1][i]}
					}
					tooltip= {  trigger: 'axis',   formatter: "{b} : {c}"  };
					xAxis = [   {   type : 'category', data : response.rptData.rs[0]  , 
								axisLabel:{formatter:function(nm){var n = echart_dataForLegend(id_chart[0],nm);if(n!=null)return nm+'\\n'+n; else return nm; }}
								}   ];
					yAxis = [  {   type : 'value'  }  ];
					series = [{	type:'line',	name : 'sth', data:response.rptData.rs[1], markLine : {  data : [  {type : 'average', name: '平均值'}    ]    }}];
					break;						

					

			}
			if(tooltip!=null)	option['tooltip'] = tooltip;
			if(legend!=null)	option['legend'] = legend;
			if(series!=null)	option['series'] = series;
			if(xAxis!=null)		option['xAxis'] = xAxis;
			if(yAxis!=null)		option['yAxis'] = yAxis;
			
			myChart.setOption(option);			
		}else {
			
		}
	}
</script>
EOF;
	}
	
	public static function ajax($divId,$chartType,$url)
	{
		return "<script>$.ajax({ url: \"$url\", data:{__VIEW__:'json',__TRANS__:'$divId#$chartType'},dataType:'json', success: echart_show});</script>";
	}
}
