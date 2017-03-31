<?php
namespace Lib\Dev;
/**
 * Description of Apidoc
 *
 * @author simon.wang
 */
class ApidocCtrl extends \Lib\Dev\Showlog {
	/**
	 * apidoc
	 */
	public function apidocAction()
	{
		\Sooh\Base\Ini::getInstance()->viewRenderType('echo');
		$dir = realpath(__DIR__.'/../../../controllers');// realpath(__DIR__."/../application/controllers");
		$dumper = new \Sooh\Base\Interfaces\ActionDumper();
		//$dir0 = realpath(__DIR__.'/../application/library');
		//$dumper->initLoaderDir(array('Prj'=>$dir0,'Lib'=>$dir0));
		echo "<html><head><meta charset='utf-8'><style>.tblstd {width:100%; border:1px solid black}</style></head><body>";
		echo "<script>function $(id){return document.getElementById(id);}function swp(id){if($(id).style.display=='none'){ $(id).style.display='block';}else{ $(id).style.display='none';}}</script>";
		echo "sample: http://127.0.0.1/xxx/index.php?__=financing/detail&id=123&__VIEW__=json<hr>";
		$dh = opendir($dir);
		while(false!==($f=  readdir($dh))){
			if($f[0]!='.'){
				$actions = $dumper->dumpOne($dir.'/'.$f);
				if(!empty($actions)){
					$this->myecho( $this->html_class(array_shift($actions)) );
					while(sizeof($actions)){
						$this->myecho( $this->html_action(array_shift($actions)) );
					}
				}
			}
		}
		closedir($dh);
		echo "</body></html>";
	}
	/**
	 * @param \Sooh\Base\Interfaces\ActionDoc $doc
	 */
	private  function html_class($doc)
	{
		return "<br><br><table class=tblstd bgcolor=\"#33CCFF\">"
		. "<tr><th>{$doc->controller}<th>{$doc->action}</tr>"
		."<tr><td colspan=9>".nl2br($doc->doc)."</tr>"
		."</table>";
	}
	/**
		* @param \Sooh\Base\Interfaces\ActionDoc $doc
		*/
	private function html_action($doc)
	{
		$lines = explode("\n", $doc->doc);
		$firstDoc = array_shift($lines);
		
		$str = "{$doc->controller}/{$doc->action}<th>".  str_ireplace('todo', '<font color=red><b>TODO</b></font>', $firstDoc);
		$md5 = md5($str);
		$str =  "<table class=tblstd  onclick=\"swp('$md5')\">"
		. "<tr bgcolor=\"#66FFFF\"><th align=left width=300>$str</tr>"
		. "</table>";
		
		$str .= "<div id=\"$md5\" style=\"display:none\">";
		$str .=  "<table class=tblstd>"
		."<tr><td>".  implode('<br>', $lines)."</tr>"
		."</table>";
		$btnUri = http_build_query(['__' => 'dev/test', 'key' => \Sooh\Base\Ini::getInstance()->get('TestKey'), 'act' => $doc->controller . '/' . $doc->action]);
		$testBtn = "<a href=/index.php?{$btnUri} style=\"float: right;background-color: #5cb85c;border-color: #4cae4c;color: white;padding: 6px 12px;border-radius: 4px;text-decoration:none;\">测试接口</a>";
		$str .= "<table class=tblstd cellpadding=1 cellspacing=0 border=1><tr><td colspan=3><font color=\"#0000FF\"><b>输入参数</b></font>{$testBtn}</td></tr>";
		foreach($doc->inputs as $r){
			$str .= "<tr><td>{$r['type']}<td>{$r['name']}<td>{$r['desc']}</tr>";
		}
		$str.="</table>";

		$str .= "<table class=tblstd><tr><td><font color=\"#00FF00\"><b>正常输出</b></font></td></tr>";
		$str .= "<tr><td>".(empty($doc->output)?"<font color=red>未定义</font>":$doc->output)."</tr>";
		$str.="</table>";

		$str .= "<table class=tblstd><tr><td><font color=red><b>错误情况<b></font></td></tr>";
		foreach($doc->errors as $r){
			$str .= "<tr><td>{$r}</tr>";
		}
		$str.="</table>";
		$str .= "</div>";
		return $str;
	}

	private function myecho($str)
	{
		echo $str;
	}
}
