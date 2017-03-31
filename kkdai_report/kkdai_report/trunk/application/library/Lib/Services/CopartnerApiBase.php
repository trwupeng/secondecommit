<?php
namespace Lib\Services;

class CopartnerApiBase {
    protected static $_copies = [];
    public static function getCopyByAbs($classname)
    {
        if(empty($classname)){
            $err= new \ErrorException("classname missing of copartner");
            error_log($err->getMessage()."\n".$err->getTraceAsString());
            throw $err;
        }
        $classname = ucfirst($classname);
        if(!self::$_copies[$classname]){
            $realname = "\\Api\\Copartners\\$classname";
//var_log(__DIR__.'/../../Api/Copartners'.$classname.'.php', 'realname>>>>>>>>>>>>>>>>>>');

            if(file_exists(__DIR__.'/../../Api/Copartners/'.$classname.'.php')){
                self::$_copies[$classname]=new $realname;
//                self::$_copies[$classname]->initCopartner();
            }else{
                return null;
            }
        }
        return self::$_copies[$classname];
    }

    public static function getCopyById($copartnerId)
    {
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        // TODO: 修改成渠道号获取 abs
        $abs = $db->getOne(\Rpt\Tbname::tb_copartner, 'copartnerAbs',array('copartnerId'=>$copartnerId));
//var_log($abs, 'abs>>>>>');
        if(!empty($abs)){
            return self::getCopyByAbs(ucfirst($abs));
        }else{
            $err=new \ErrorException('copartner of '.$copartnerId.' missing');
            error_log("Error:".$err->getMessage()."\n".$err->getTraceAsString());
            return null;
        }
    }

    public function abs() {
        $str = get_called_class();
        $r = explode('\\', $str);
        return strtolower(array_pop($r));
    }

    public function notifyNewReg($userId) {
        return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::ok, 'skip for '.json_encode($userId));
    }

   public function notifyNewOrder($orderId) {
        return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::ok, 'skip for '.json_encode($orderId));
    }
    public function inquiryUsers($args) {
        return null;
    }

}