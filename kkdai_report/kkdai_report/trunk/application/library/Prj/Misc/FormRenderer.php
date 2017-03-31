<?php
namespace Prj\Misc;
/**
 * Description of BJUI
 *
 * @author simon.wang
 */
class FormRenderer extends \Sooh\Base\Form\Renderer{
	/**
	 * 构建input
	 * @param string $k
	 * @param \Sooh\Base\Form\Item $define
	 * @param string $inputType
	 */
    public $callbackname = 'thiscallback';

    public static $editor ; //编辑器模板对象

	public function render($k,$define,$inputType,$dataOpitions='')
	{
		$val4Input=$define->valForInput;
		if($inputType==\Sooh\Base\Form\Item::constval) {
			return '<input id="' . $k . '" type=hidden name="' . $k . '" value="' . $val4Input . '">'.$val4Input;
		}elseif($inputType==\Sooh\Base\Form\Item::datepicker) {
            if (empty($val4Input)) {
                $val4Input = '';
            } else {
                $val4Input = \Prj\Misc\View::fmtYmd($val4Input);
            }
            $str = '<input id="' . $k . '" type=text data-toggle="datepicker"  name="' . $k . '" value="' . $val4Input . '">';
        }elseif($inputType=='timepicker'){
            if (empty($val4Input)) {
                $val4Input = '';
            } else {
                $val4Input = \Prj\Misc\View::fmtYmd($val4Input,'time');
            }
            $str = '<input id="' . $k . '" type=text data-pattern="yyyy-MM-dd HH:mm" data-toggle="datepicker"  name="' . $k . '" value="' . $val4Input . '">';
        }else{
			$str = parent::render($k, $define, $inputType);
			$str = str_replace('<select ', '<select data-toggle="selectpicker" ', $str);
            $str = str_replace('<input', '<input  ', $str);
		}
        $rule = '';
        if(!empty($dataOpitions))
        {
            foreach($dataOpitions as $k=>$v)
            {
                $rule.=" $k = '$v'";
            }
        }
        $str = str_replace('<input' , '<input '.$rule,$str);
        $str = str_replace('<select' , '<select '.$rule,$str);
        $str = str_replace('<textarea' , '<textarea style="width:80%;height:200px" '.$rule, $str);
        //var_log($str,'rule>>>>>>>>>>>>>>>');
        return $str;
	}
	
	public function htmlFormButton($title,$type="button")
	{
		$type=  strtolower($type);
		if($type=='button'||$type=='reset'||$type='submit'){
			return "<input class=\"btn-default\" type=\"$type\" value=\"$title\">";
		}else{
			throw new \ErrorException('button type error:'.$type);
		}
	}
	/**
	 * 
	 * @param \Sooh\Base\Form\Broker $form
	 */
	public function getSearchStandard($form = null,$rightParts=null)
	{
        if($form){
            $form->setRenderer($this);
            $tmp = $form->renderDefault('<label>{capt}:</label>{input}&nbsp;');
            $tmp = $form->renderFormTag('data-toggle="ajaxsearch" id="pagerForm" ').'<div class="bjui-searchBar">'.$tmp;
            if(count($form->items)>0){
                $tmp .= '<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;';
                $tmp .= '<a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>';
            }
        }else{
            $tmp = '<form data-toggle="ajaxsearch" id="pagerForm" >';
        }
        if(!empty($rightParts)){
            $tmp .= $rightParts;
        }
		$tmp.='</div>';
		return $tmp.'</form>';
	}
	/**
	 * 
	 * @param \Sooh\Base\Form\Broker $form
	 */	
	public function getSearchStandard_without_endTag_form_div($form)
	{
				$form->setRenderer($this);
		$tmp = $form->renderDefault('<label>{capt}:</label>{input}&nbsp;');
		$tmp = $form->renderFormTag('data-toggle="ajaxsearch" id="pagerForm"  style="display:inline-block"').$tmp;
		$pos = strpos($tmp, '<lable>');
		$tmp = substr($tmp,0,$pos).'<div class="bjui-searchBar">'.substr($tmp,$pos);
		$tmp .= '<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;';
        $tmp .= '<a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>';
		return $tmp;

	}
	/**
	 * 多行的
	 * @param type $form
	 * @param type $split
	 * @return type
	 */
	public function getSearchStandard_parts($form,$split='<label>领取时间从:</label>',$rightParts='')
	{
		$form->setRenderer($this);
		$tmp = $form->renderDefault('<label>{capt}:</label>{input}&nbsp;');
		$tmp = $form->renderFormTag('data-toggle="ajaxsearch" id="pagerForm" ')."\n".$tmp;
		$pos = strpos($tmp, '<lable>');
		$tmp = substr($tmp,0,$pos)."\n<div class=\"bjui-searchBar\">\n".substr($tmp,$pos);
		
		$formPart = explode($split, $tmp,2);
		
		$part2 = '</div><div class="bjui-moreSearch"><label>领取时间从:</label>'.$formPart[1].'</div>';
	
		$part1 = $formPart[0]
				.'<button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom2"><i class="fa fa-angle-double-down"></i></button>'
				.'<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;'
				.'<a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>'
				;
		return [$part1,$part2];
	}
	/**
	 * 多行的
	 * @param type $form
	 * @param type $split
	 * @return type
	 */
	public function getSearchStandardWithMore($form,$split='<label>领取时间从:</label>',$rightParts='')
	{
		$form->setRenderer($this);
		$tmp = $form->renderDefault('<label>{capt}:</label>{input}&nbsp;');
		$tmp = $form->renderFormTag('data-toggle="ajaxsearch" id="pagerForm" ')."<div class=\"bjui-searchBar\">\n".$tmp;
		
		$formPart = explode($split, $tmp,2);
		
		$part2 = '<div class="bjui-moreSearch">'.$split.$formPart[1].'</div>';
	
		$part1 = $formPart[0]
				.'<button type="button" class="showMoreSearch" data-toggle="moresearch" data-name="custom2"><i class="fa fa-angle-double-down"></i></button>'
				.'<button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;'
				.'<a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>'
				;
		return $part1."\n".$rightParts."</div>\n".$part2.'</form>';
	}
	/**
	 * 导出选定所需form表单，用法：
	 *	echo $frm->formForExport(Sooh\Base\Tools::uri(array('__EXCEL__' => 1), 'index'));
	 * @param string $url
	 * @param string $prefix 前缀
	 */
	public function formForExport($url,$prefix)
	{
		$this->_lastFormExport = $prefix;
		return '<form action="'.$url.'" method="post" id="'.$prefix.'-exportChecked" style="display: none;"></form>'."\n";
	}
	private $_lastFormExport;
	/**
	 * 右侧批量操作的下拉列表命令
	 * @param string $strExtraBtns 全部下载的url
	 * @param string $urlExportAll 全部下载的url
	 * @param array $arrCmdList  array([capt1,url1],null,[capt2,url2])
	 * @return string
	 */
	public function getBatchArea($strExtraBtns='',$urlExportAll='',$arrCmdList=[],$all = true)
	{
		$str = '<div class="pull-right">'.$strExtraBtns.'
		<div class="btn-group">
			<button type="button" class="btn-default dropdown-toggle" data-toggle="dropdown" data-icon="copy">
				复选框-批量操作<span class="caret"></span></button>
			<ul class="dropdown-menu right" role="menu">'."\n";
		if(!empty($urlExportAll)){
			$str .= '<li><a href="'.$urlExportAll.'">导出全部</a></li>';
			if($all)$str .= '<li><a class="'.$this->_lastFormExport.'-export" style="cursor: pointer">导出<span style="color: red">选中</span></a></li>';
		}
		if(!empty($arrCmdList)){
			$str .= '<li class="divider"></li>';
			foreach($arrCmdList as $r){
				if(is_array($r)){
					$str .= '<li><a href="'.$r[1].'">'.$r[0].'</a></li>';
				}else{
					$str .= '<li class="divider"></li>';
				}
			}
		}
		
		$str .= '			</ul>
		</div>
		<script>
			/*
			 构造表单 post 方法 提交
			 */
			var ids = new Array();
			$(\'.'.$this->_lastFormExport.'-export\').click(function () {
				if (ids.length == 0) {
					$(document).alertmsg(\'error\', \'无选中选项\');
					return false;
				}
				$(\'#'.$this->_lastFormExport.'-exportChecked\').html(\'\');
				for (var i in ids) {
					var reg = /function[.]*/;
					if (reg.exec(ids[i]) != null) {
						continue;
					}
					$(\'#'.$this->_lastFormExport.'-exportChecked\').append("<input type=\'hidden\' name=\'ids[]\' value=\'" + ids[i] + "\'>");
				}
				$(\'#'.$this->_lastFormExport.'-exportChecked\').submit();
				return false;
			});
		</script>
	</div>';
		return $str;
	}
	/**
	 * 
	 * @param \Sooh\Base\Form\Broker $form
	 */	
	public function getEditStandard($form,$cols=2 ,$ext = '')
	{
		$form->setRenderer($this);
		$tmp = $form->renderDefault('<label>{capt}:</label>{input}<!--|-->');
		$tmp = explode('<!--|-->', $tmp);
		$total = sizeof($tmp);
		$str = '<tr>';
		$cmp = $cols-1;
		for($i=0;$i<$total;$i++){
			$str.='<td>'.$tmp[$i].'</td>';
			if($i%$cols==$cmp){
				$str.='</tr>';
			}
		}

        //$editorHtml = self::$editor?(self::$editor->inputShow()):'';
		$tmp = $form->renderFormTag(' data-callback="'.$this->callbackname.'" method="post" id="editForm" data-toggle="validate" data-alertmsg="false" data-reload-navtab="true" ')
				.'<table class="table table-condensed table-hover" width="100%"><tbody>'
				.$str
				.'</tbody></table>'
                .$ext  //详情编辑
				.$this->textArea
				. '</form>';
		return $tmp;
	}
	protected $textArea = '';

    public function editor($arr){
        $str = '';
        $uploadUrl = \Sooh\Base\Tools::uri(null,'upload','wares');
        foreach($arr as $k=>$v){
            $str.='<br>';
            $html =  '<label for="j_custom_'.$k.'" class="control-label x85">'.$v.'</label>' .
                '<div style="display: inline-block; vertical-align: middle;">' .
                '<textarea name="'.$k.'" id="j_form_'.$k.'" class="j-content"  style="width: 700px;"  data-toggle="kindeditor" data-minheight="200" data-upload-json="'.$uploadUrl.'">'.

                '</textarea>'.
                '</div>';
            $str.=$html;
        }
        $this->textArea = $str;
    }

    public function setCallBack(){
        $this->callbackname = 'thiscallback'.rand(1000,9999);
    }

    public function callBack($url,$width=500,$height=300){
        $funcName = $this->callbackname;
        $js = <<<html
<script>
    $funcName = function(json){
        console.log(json);
        if(json.statusCode == 300){
            $(this).alertmsg('error', json.message);
            return;
        }
        $('body').dialog(
            {
                id:'mydialog', url:'$url',data:json, title:'确认信息',width:'$width',height:'$height'
            }
        );
    }
</script>
html;
        return $js;
    }
}
