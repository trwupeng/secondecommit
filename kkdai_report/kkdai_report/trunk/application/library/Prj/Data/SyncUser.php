<?php

namespace Prj\Data;

use Sooh\DB\Base\KVObj;

class SyncUser extends KVObj
{
    private static $lib = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function buildTicket( $svrid = '')
    {
        return $svrid . 'ids' . time() . self::randStrByLib(13);
    }

    public static function save(\Prj\Sync\SyncUser $syncUser) {
        $model = self::getCopy($syncUser->id, 'oa');
        $model->load();

        if (!$model->exists()) {
            $model->setField('createdAt', time());
        }
        $model->setField('userName', $syncUser->name);
        $model->setField('userCode', $syncUser->code);
        $model->setField('loginName', $syncUser->loginName);
        $model->setField('orgAccountId', $syncUser->orgAccountId);
        empty($syncUser->orgAccountName) || $model->setField('orgAccountName', $syncUser->orgAccountName);
        empty($syncUser->orgShortName) || $model->setField('orgShortName', $syncUser->orgShortName);
        $model->setField('updatedAt', time());

        $model->update();
        return $model;
    }

    public static function getCopy($id, $type = 'oa')
    {
        return parent::getCopy(['userId' => $id, 'userType' => $type]);
    }

    protected static function splitedTbname($n, $isCache)
    {
        return 'tb_sync_user';
    }

    /**
     * 根据字符库生成随机字符串
     * @param int       $length 字符串长度
     * @param int       $num 生成的字符串个数
     * @param bool|true $repeat 是否允许重复
     * @param null      $lib 字符库
     * @return array
     */
    protected static function randStrByLib($length = 14, $num = 1, $repeat = true, $lib = null)
    {
        $lib || $lib = self::$lib;

        $libLen = strlen($lib);
        $ret = [];

        while($num > 0) {
            $str = '';
            for($i = 0; $i < $length; $i++) {
                $char = $lib[mt_rand(0, $libLen - 1)];
                if ($repeat) {
                    $str .= $char;
                } else {
                    if (strpos($str, $char) != false) {
                        $i--;
                        continue;
                    } else {
                        $str .= $char;
                    }
                }
            }

            array_push($ret, $str);

            $num--;
        }

        return count($ret) == 1 ? $ret[0] : $ret;
    }
}
