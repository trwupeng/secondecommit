<?php
namespace Lib\Misc;
use \Sooh\Base\Form\Item as  sooh_formdef;
/**
 * Description of Form
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class DWZ extends \Sooh\Base\Form\Renderer {

	/**
	 * 构建input
	 * @param string $k
	 * @param \Sooh\Base\Form\Definition $define
	 * @param string $inputType
	 */
	public function render($k,$define,$inputType)
	{
		$val4Input=$define->valForInput;//$this->valForInput($define->value,$k);
		switch ($inputType){
			case sooh_formdef::text:
				if(!empty($define->verify)){
					if(!empty($define->verify['cssId'])) {
						$str = ' id="'.$define->verify['cssId'].'" class="';
					} else {
						$str = ' class="';
					}
					if($define->verify['required']){
						$str.='required ';
					}
					switch($define->verify['type']){
						case 'int':
							$str.='digits"';
							if($define->verify['max']){
								$str.=' min="'.$define->verify['min'].'" max="'.$define->verify['max'].'"';
							}
							break;
						case 'str':
							switch ($define->verify['subcheck']){
								case 'identifier':
									$str.=' alphanumeric';
									break;
								case 'email':
									$str.=' email';
									break;
							}
							$str.= '"';
							if($define->verify['max']){
								$str.=' minlength="'.$define->verify['min'].'" maxlength="'.$define->verify['max'].'"';
							}
							break;
						default:
							$str.= '"';
							break;
					}
				}else {
					$str='';
				}
				$str = '<input type=text name="'.$k.'" size="30"  value="'.$val4Input.'"'.$str;
				return $str.'>';
			case sooh_formdef::passwd:
				if(!empty($define->verify)){
					if(!empty($define->verify['cssId'])) {
						$str = ' id="'.$define->verify['cssId'].'" class="';
					}else{
						$str = ' class="';
					}
					if($define->verify['required']){
						$str.='required "';
					}
					if($define->verify['max']){
						$str.='" minlength="'.$define->verify['min'].'" maxlength="'.$define->verify['max'].'"';
					}
					if($define->verify['cmpCssId']){
						$str.='" equalto="#'.$define->verify['cmpCssId'].'"';
					}
				}else{
					$str='';
				}
				return '<input type=password name="'.$k.'" size="30"  value="'.$val4Input.'"'.$str .'>';
			case sooh_formdef::constval:
				if($define->options){
					$tmp = $define->options->pairVals;
					if(isset($tmp[$val4Input]))	{
						return  $tmp[$val4Input];//.'<input type=hidden name="'.$k.'" value="'.$define->value.'">';
					} else {
						error_log("[Error options missing]$k:{$val4Input} not found in ".  json_encode($tmp));
						return  $val4Input;
						//.'<input type=hidden name="'.$k.'" value="'.$define->value.'">';
					}
				}else{
					return  $val4Input;
				}
				//.'<input type=hidden name="'.$k.'" value="'.$define->value.'">';
			case sooh_formdef::select:
				$str = '<select class="combox" name="'.$k.'">';
				$options = $define->options->pairVals;
				$found=false;
				foreach($options as $k=>$v){
					if($val4Input==$k){
						$str.= '<option value="'.$k.'"  selected>'.$v.'</option>';
						$found=true;
					}else{
						$str.= '<option value="'.$k.'">'.$v.'</option>';
					}
				}
				if(!$found){
					throw new \ErrorException($val4Input.' not found in options');
				}
				return $str.'</select>';
			case sooh_formdef::chkbox:
				$str='';
				$options = $define->options->pairVals;
				$values = explode(',', $val4Input);
				foreach($options as $i=>$v){
					if(in_array($i, $values)){
						$str.='<label><input type="checkbox" name="'.$k.'[]" value="'.$i.'" checked=true/>'.$v.'</label>';
					}else{
						$str.='<label><input type="checkbox" name="'.$k.'[]" value="'.$i.'" />'.$v.'</label>';
					}

				}
				return $str;
			case sooh_formdef::radio:
				$str='';
				$options = $define->options->pairVals;
				$values = explode(',',$val4Input);
				foreach($options as $i=>$v){
					if(in_array($i, $values)){
						$str.='<label><input type="radio" name="'.$k.'" value="'.$i.'" checked=true/>'.$v.'</label>';
					}else{
						$str.='<label><input type="radio" name="'.$k.'" value="'.$i.'" />'.$v.'</label>';
					}

				}
				return $str;
			case sooh_formdef::date:
				$str = '<input type="text" name="'.$k.'" class="date" dateFmt="yyyyMMdd" minDate="1900-01-01" maxDate="2038-01-01" value="'.$val4Input.'"/>';
				return $str;
			case sooh_formdef::mulit:
				if (!empty($define->verify)){ 
							$str= '';
							if($define->verify['cols']){
								$str.=' cols="'.$define->verify['cols'];
							}
							if ($define->verify['rows']){
								$str .= '" rows="'.$define->verify['rows'].'"';
							}
				}
				else {
					$str = '';
				}
				$str = '<textarea id="'.$k.'" name="'.$k.'" '.$str.'>'.$val4Input.'</textarea>';
				return $str;
			default:
				throw new \ErrorException('unsupport input type found:'.$inputType);
		}
	}
	
	public static function encodePkey($arr)
	{
		return base64_encode(json_encode($arr));
	}
	
	public static function decodePkey($str)
	{
		if(!empty($str)){
			$str = base64_decode(str_replace('%3D', '=', $str));
			return json_decode($str,true);
		}else {
			return null;
		}
	}
}
