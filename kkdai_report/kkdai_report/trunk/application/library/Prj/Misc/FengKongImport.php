<?php

namespace Prj\Misc;

/**
 * 风控需求中，导入功能开发
 * Class FengKongImport
 * @package Prj\Misc
 */
class FengKongImport
{
   
            /**
             *将导入的数据转化成数组型
             **/
            public  function exceltoarry($rs){
                
                $ret=explode("\r\n",$rs);
                $first=current($ret);
                $last=end($ret);
                
//                 foreach ($ret as $k=>$v){
//                     if($v==$first || $v==$end){
//                         continue;
//                     }
//                     $rem[]=$v;
//                 }
               
                foreach ($ret as $k=>$v){
                    if($v==$end){
                        continue;
                    }
                    $rem[]=$v;
                }
                
                return  $rem;
            }

            /**
             * 导入日期型转化成标准格式：如2016-12-13
             **/
            public  function  checktime($arr){
                
                $datetime=explode('.', $arr);
                $datetimemore=explode('/',$arr);
                if(!empty($datetime[2])){
                    $month=$datetime[1];
                    $day=$datetime[2];
                    
                    if($month<10 && strlen($month)==1){
                        $month="0".$month;
                    }elseif($day<10 && strlen($day)==1){
                        $day="0".$day;
                    }
                
                    $Time=$datetime[0].'-'.$month.'-'.$day;
                    return $Time;
                }elseif(!empty($datetimemore[2])){
                    $month=$datetimemore[1];
                    $day=$datetimemore[2];
                    
                    if($month<10 && strlen($month)==1){
                        $month="0".$month;
                    }elseif($day<10 && strlen($day)==1){
                        $day="0".$day;
                    }
                    
                    $Time=$datetimemore[0].'-'.$month.'-'.$day;
                    return $Time;
                }else {
                    $Time=$arr;
                    return $Time;
                }
                
            }

            /**
             *将导入要转化的value与key(一个)，互换 
             **/
            public  function transvk($arrone,$arr){

                if(in_array($arrone,$arr)){
                    foreach ($arr as $k=>$v){
                        if($arrone==$v){
                            $arrtemp=$k;
                        }   
                    }
                    return  $arrtemp;
                }else{
                    if(empty($arrone)){
                        $arrtem=0;
                        return $arrtem;
                    }else{
                       return 2000;
                    }
                }
               
            }
                 
            /**
             *将导入要转化的key(一个)与value(多个)，互换
             **/
            public  function transv2k($arrone,$arr){
                $arrone=explode(',', $arrone);
                if(!empty($arrone)){
                foreach ($arrone as $vv){
                    if(in_array($vv,$arr)){
                        foreach ($arr as $k=>$v){
                            if($v==$vv){
                                $tmp[]="$k";
                            }
                      }
                   }
                }
              
                return $tmp;
            }else{
                if(empty($arrone)){
                    $tmp="";
                    return $tmp;
                }else{
                  return 3000;
                }
                
            }
          }
          
       /***
        *表头匹配
        **/
          
        public  function  location($load,$goal,$title){
            $location=preg_split("/[\t]/",$load);
            if($location==false){
                $location=$load;
            }
            //var_log($location,'location###########');
           // var_log($title,'location###########');
            if(!empty($location) && !empty($goal) && !empty($title)){
               foreach ($title as $k=>$v){
                   if($goal==$v){
                       $goal=$k;    
                   }
                  $goalaim=self::arim($location, $goal); 
                   
               }
            }
           // var_log($goalaim,'location###########');
            return $goalaim;
            
        }
        
        public  function arim($location,$goal){
            foreach ($location as $k=>$v){
                if($goal==$k){
                    $goals=$v;
                }
            }
            return $goals;
        }
}
