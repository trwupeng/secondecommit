<?php
namespace Prj\Data;

class File {
    /**
     * @return \Sooh\DB\Interfaces\All
     * @throws \ErrorException
     */
    protected static function getDB() {
        return \Sooh\DB\Broker::getInstance();
    }

    public static function createNew($fileData)
    {
        $stop = false;
        $num = 0;
        while($stop==false)
        {
            $num++;
            try{
                $fileId =date('Ymd').rand(1000,9999);
                $ret = self::getDB()->addRecord('tb_file',array('fileId'=>$fileId,'data'=>$fileData));
                $stop = true;
            }catch (\ErrorException $e){
                if(\Sooh\DB\Broker::errorIs($e, \Sooh\DB\Error::duplicateKey)){
                    $stop = false;
                }else{
                    return false;
                }
            }
            if($num>3)break;
        }

        return $ret?$fileId:false;

    }

    public static function getDataById($fileId)
    {
        $db = self::getDB();
        $list = $db->getRecord('tb_file','*',array('fileId'=>$fileId));
        return $list['data'];
    }

    public static function updateStatus($fileId,$status)
    {
        $db = self::getDB();
        return $db->updRecords('tb_file',['status'=>$status],['fileId'=>$fileId]);
    }

}
