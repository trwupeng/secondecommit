<?php
namespace Lib\Misc;
/**
 * Description of Amount2CNY
 *
 * @author Simon Wang <hillstill_simon@163.com>
 */
class Amount2CNY {
	protected static $basical =array(
		'default'=>array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"),
		'unicode'=>array('&#38646;','&#22777;','&#36144;','&#21441;','&#32902;','&#20237;','&#38470;','&#26578;','&#25420;','&#29590;',),
	);
	protected static $advanced = array(
		'default'=>array(1=>"拾","佰","仟",'万','亿'),
		'unicode'=>array(1=>"&#25342;","&#20336;"," &#20191;","&#19975;","&#20159;"),
	);
	protected static $misc = array(
		'default'=>array("整","元","分",'角'),
		'unicode'=>array("&#25972;","&#20803;","&#20998;","&#35282;"),
	);
	protected static $type='default';
	public static function setChar($type='unicode')
	{
		//self::$type = $type;
	}
	protected static function other($index)
	{
		return self::$misc[self::$type][$index];
	}
	protected static function ext($index)
	{
		return self::$advanced[self::$type][$index];
	}
	protected static function one($index)
	{
		return self::$basical[self::$type][$index];
	}
  public static function ParseNumber($number){
    $number=trim($number);
    if ($number>999999999999) {
		return "num_overflow";
	}
    if ($number==0) {
		return self::one(0);
	}
    if(strpos($number,'.')){
      $number=round($number,2);
      $data=explode(".",$number);
      $data[0]=self::int($data[0]);
      $data[1]=self::dec($data[1]);
      return $data[0].$data[1];
    }else{
      return self::int($number).self::other(0);
    }
  }
 
  public static function int($number){
    $arr=array_reverse(str_split($number));
    $data='';
    $zero=false;
    $zero_num=0;
    foreach ($arr as $k=>$v){
      $_chinese='';
      $zero=($v==0)?true:false;
      $x=$k%4;
      if($x && $zero && $zero_num>1){
		  continue;
	  }
      switch ($x){
        case 0:
          if($zero){
            $zero_num=0;
          }else{
            $_chinese=self::one($v);
            $zero_num=1;
          }
          if($k==8){
            $_chinese.=self::ext(5);
          }elseif($k==4){
            $_chinese.=self::ext(4);
          }
          break;  
        default:
          if($zero){
            if($zero_num==1){
              $_chinese=self::one($v);
              $zero_num++;
            }
          }else{
            $_chinese=self::one($v);
            $_chinese.=self::ext($x);
          }
      }
      $data=$_chinese.$data;
    }
    return $data.self::other(1);
  }
   
  public static function dec($number){
    if(strlen($number)<2) {
		$number.='0';
	}
    $arr=array_reverse(str_split($number));
    $data='';
    $zero_num=false;
    foreach ($arr as $k=>$v){
      $zero=($v==0)?true:false;
      $_chinese='';
      if($k==0){
        if(!$zero){
          $_chinese=self::one($v);
          $_chinese.=self::other(2);
          $zero_num=true;
        }
      }else{
        if($zero){
          if($zero_num){
            $_chinese=self::one($v);
          }
        }else{
          $_chinese = self::one($v);
          $_chinese.=self::other(3);
        }
      }
      $data=$_chinese.$data;
    }
    return $data;
  }
}
