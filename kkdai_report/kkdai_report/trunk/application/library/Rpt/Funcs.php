<?php

/**
 * 只支持检查Ymd, Y-m-d, y-n-j格式的日期是否合法
 * @param $ymd
 */
namespace Rpt;

class Funcs {

    public static function check_date ($ymd) {
        if(strpos($ymd, '-')!==false && sizeof(explode('-', $ymd))!=3) {
            return false;
        }

        list($y,$m,$d) = explode('-', date('Y-m-d'), strtotime($ymd));
        return checkdate($m-0, $d-0, $y);
    }

    /**
     * 使用合法的日期进行格式化
     * @param $ymd
     * @param string $format
     */
    public static function date_format($dt, $format='Ymd'){
        return date($format, strtotime($dt));
    }

    public static function product_type ($type=null) {
        $arrProductTypes= [
            0=>'天天赚',
            1=>'定期宝',
            2=>'房宝宝',
            5=>'精英宝',
        ];
        if($type==null) {
            return $arrProductTypes;
        }
        if(isset($arrProductTypes[$type])) {
            return $arrProductTypes[$type];
        }else {
            return $type;
        }
    }
    
    /**
     * 
     *默认输出上自然周日期
     *@param string $last_start 上周开始日期
     *
     *@param sting $last_end 上周结束日期
     */
    
    public static  function timetrans($date){
        
        $trans=[];
        if(empty($date)){
        $date=date('Y-m-d');  //当前日期
        }
        
        $first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期

        $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
         
         
        $now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
         
         
        $now_end=date('Y-m-d',strtotime("$now_start +6 days"));  //本周结束日期
         
        $last_start=date('Y-m-d',strtotime("$now_start - 7 days"));  //上周开始日期
         
        $last_end=date('Y-m-d',strtotime("$now_start - 1 days"));  //上周结束日期
         
        $trans=['last_start'=>$last_start,'last_end'=>$last_end];
        
        return $trans;
    }

    /**
     * 快快贷现在有的各种优惠券
     */
    public static $voucherEnum = [
        1=>'抵现券',
        2=>'加息券',
        3=>'返现券',
        4=>'提现券',
    ];

    public static function customerFlag ($flag) {
        switch ($flag) {
            case 0: return '普通用户';break;
            case 1: return '超级用户';break;
            case 2: return '内部员工';break;
            case 3: return '员工推荐';break;
            default: return $flag;
        }
    }

    public static function voucherName($voucherType=null) {
        if($voucherType!==null) {
            if(isset(self::$voucherEnum[$voucherType])){
                return self::$voucherEnum[$voucherType];
            }else {
                return $voucherType;
            }
        }else {
            return self::$voucherEnum;
        }
    }
    
    public function formday($da,$year){
        $day_31=['1','3','5','7','8','10','12'];
        $day_30=['4','6','9','11'];
        $day31="31";
        $day30="30";
         
        if(!empty($da)){
            if(in_array($da,$day_31)){
                return $day31;
            }elseif(in_array($da,$day_30)){
                return $day30;
            }elseif(!in_array($da,$day_30) && !in_array($da,$day_31)){
                $yearone=self::formyear($year);
                return $yearone;
            }
        }
    }
     
    public  function formyear($da){
        $month28="28";
        $month29="29";
        if(!empty($da)){
            if (($da % 4 == 0) && ($da % 100 != 0) || ($da % 400 == 0)) {
                return $month29;
            } else {
                return $month28;
            }
        }
    }
    
    public function week($str){
          if((date('w',strtotime($str)) == 0)){
           return 200;
         }else{
          return 400;
        }
       }   
  
     public function verify($y,$m,$d){
         $day_31=['1','3','5','7','8','10','12'];
         $day_30=['4','6','11'];
         
         if(!empty($m) && !empty($d) && !empty($y)){
             if(in_array($m,$day_31) && $d=='31'){
                 return 200;
             }elseif(in_array($m,$day_30) && $d=='30'){
                 return 200;
             }
             elseif(!in_array($m,$day_30) && !in_array($m,$day_31)){
                 $day=self::formyear($y);
                 if($d==$day){
                     return 200;
                 }
             }else{
                 return 400;
             }
         }
         
     }

    /**
     * 检查Y-m格式日期是否正确
     * @param $ym 正确格式如2016-01
     * @return bool
     */
    public static function dateYmCheck ($ym) {
        if (preg_match('/^[1-9]\d+(-)[0-1]{1}[0-9]{1}$/', $ym)) {
            return true;
        }else {
            return false;
        }
    }

    /**
     * Ym与Y-m格式互换
     * @param $ym 格式如 2016-08 或 201608
     * @return mixed|string 返回格式如201608 或2016-08
     */
    public static function dateYmTrans ($ym) {
        if(strpos($ym, '-')===false) {
            $year = substr($ym, 0, -2);
            $month = substr($ym, -2);
            return $year.'-'.$month;
        }else {
            return str_replace('-', '', $ym);
        }
    }

    /**
     * 执行sql并获取记录
     * @param $sql
     * @param $db
     * @return mixed
     */
    public static function execSql($sql, $db) {
        $result = $db->execCustom(['sql'=>$sql]);
        return $db->fetchAssocThenFree($result);
    }


    /**
     * 后去某年某月的最后一天
     * @param $timestamp 时间戳
     * @return int
     */
    public static function monthOfLastDay ($timestamp) {
        $ymd = date('Y-m-01', $timestamp);
        return date('d', strtotime("$ymd + 1 month -1 day"));
    }
}