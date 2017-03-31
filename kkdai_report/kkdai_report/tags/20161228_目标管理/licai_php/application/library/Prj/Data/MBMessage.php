<?php
/**
 * 目标管理模块中的点评,跟踪,@人员model
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/14 0014
 * Time: 下午 2:01
 */
namespace Prj\Data;
class MBMessage extends \Sooh\DB\Base\KVObj {

    protected static function splitedTbName($n, $isCache)
    {
        return 'mb_message';
    }

    public static function getCopy($k) {
        return parent::getCopy(['id'=>$k]);
    }



    public static function paged($where=null, $pager=null, $order='') {
        $model = self::getCopy();
        $db = $model->db();
        $tb = $model->tbname();
        if($pager !=null) {
            $pager->init($db->getRecordCount($tb, $where), -1);
            $records = $db->getRecords($tb, '*', $where, $order, $pager->page_size, $pager->rsFrom());
        }else {
            $records =  $db->getRecords($tb, '*', $where, $order);
        }
        return $records;
    }


    public static function addRecord($title,$content,$sendid,$receiverid, $userid,$type,$batch_type,$batchid,$dstid=[],$arg1=0,$arg2=0,$arg3=0,$arg4='',$arg5='',$arg6=null) {

        $model = self::getCopy();
        $db = $model->db();
        $tb = $model->tbname();
        $data = [
            'title' => $title,
            'content' => $content,
            'sendid' => $sendid,
            'receiverid' => $receiverid,
            'userid'=>$userid,
            'type' => $type,
            'batch_type' => $batch_type,
            'batchid' => $batchid,
            'arg_smallint' => $arg1,
            'arg_int' => $arg2,
            'arg_bigint' => $arg3,
            'arg_100' => $arg4,
            'arg_1000' => $arg5,
            'arg_10000' => $arg6,
        ];
        if(!empty($dstid) && is_array($dstid)) {
            $data['dstid'] = implode('|', $dstid);
        }
        $timeNow = date('Y-m-d H:i:s');
        $data['create_time'] = $timeNow;
        $data['update_time'] = $timeNow;

        $retry = 5;
        while($retry) {
            try {
                $db->addRecord($tb, $data);
                break;
            }catch(\ErrorException $e) {
                $retry--;
                error_log($e->getMessage().'\n'.$e->getTraceAsString());
            }
        }
        if($retry <= 0){
            return false;
        }
        return true;
    }

    public static function transformData ($record, $show=false) {
        $tmp['id'] = $record['id'];
        $tmp['create_time'] = $record['create_time'];
        $session = \Sooh\Base\Session\Data::getInstance();
        $userId = $session->get('managerId');
        list($loginNameTmp,$cameFromTmp) = explode('@', $userId);
        list($loginName, $cameFrom) = explode('@', $record['sendid']);
        if($cameFrom == $cameFromTmp && $loginName == $loginNameTmp){
            $tmp['sender'] = '我';
        }else {
            $ret = \Prj\Data\Manager::getDeptNameAndNickname($cameFrom, $loginName);
            $tmp['sender'] = $ret['deptName'].'.'.$ret['nickName'];
        }
        $tmp['type'] = \Prj\Data\MBPerfReply::$typeEnum[$record['type']];
        $tmp['receiveid'] = $record['receiverid'];
        $tmp['userid'] = $record['userid'];
        $tmp['batchid'] = $record['batchid'];
        if($record['type'] == \Prj\Data\MbPerfReply::at){
            $dstid = explode('|', $record['dstid']);
            foreach($dstid as $account){
                list($loginName,$cameFrom) = explode('@', $account);
                $ret = \Prj\Data\Manager::getDeptNameAndNickname($cameFrom, $loginName);
                $tmp['atlist'][] =$ret['deptName'].'-'.$ret['nickName'];
            }
        }
        $tmp['content'] = $record['content'];
        $tmp['flag'] = $record['flag'];
        $tmp['batch_type'] = $record['batch_type'];
        $perfDstModel = \Prj\Data\MBPerfDst::getCopy();
        $logDstModel = \Prj\Data\MBPerfDailylog::getCopy();
        if($record['batch_type']==\Prj\Data\MBPerfReply::targetType) {
            $targetRet = $perfDstModel->db()->getRecord($perfDstModel->tbname(),'userid,type', ['userid'=>$record['userid'], 'type_id'=>$tmp['batchid']]);
        }elseif ($record['batch_type'] == \Prj\Data\MBPerfReply::logType){
             $targetRet= $logDstModel->db()->getRecord($logDstModel->tbname(),'userid,type', ['userid'=>$record['userid'], 'log_date'=>date('Y-m-d', strtotime($tmp['batchid']))]);
            error_log(\Sooh\DB\Broker::lastCmd());
        }
        $tmp['perf_dst_type'] = $targetRet['type'];
        if($show) {
            $tmp['queryParamers']['zzjgName'] = explode('@', $targetRet['userid'])[0];
            if($record['batch_type'] == \Prj\Data\MBPerfReply::targetType){
                $parseRet = \Prj\Data\MBPerfDst::parseTypeId($record['batchid']);
                $tmp['queryController']='perfdst';
                $tmp['queryBtnName']='查看目标';
                switch($parseRet['type']) {
                    case 1: // 日
                        $tmp['queryParamers']['date'] = date('Y-m-d', strtotime($parseRet['date']));
                        $tmp['queryAction']='index';
                        $tmp['targetTab']='day';
                        break;
                    case 2: // 周
                        $tmp['queryParamers']['startDate'] = date('Y-m-d', strtotime($parseRet['date']));  //
                        $tmp['queryParamers']['weekNums'] = $parseRet['typeNum'];
                        $tmp['queryAction']='index';
                        $tmp['targetTab']='week';
                        break;
                    case 3:// 月
                        $tmp['queryParamers']['create_date'] = date('Y', strtotime($parseRet['date']));
                        $tmp['queryParamers']['type_id'] = $parseRet['typeNum'];
                        $tmp['queryAction']='index';
                        $tmp['targetTab']='month';
                        break;
                    case 4: // 季
                        $tmp['queryParamers']['create_date'] = date('Y', strtotime($parseRet['date']));
                        $tmp['queryParamers']['type_id'] = $parseRet['typeNum'];
                        $tmp['queryAction']='index';

                        break;
                    default:
                        break;
                }
            }elseif($record['batch_type'] == \Prj\Data\MBPerfReply::logType){
                $tmp['queryParamers']['date'] = date('Y-m-d', strtotime($record['batchid']));
                $tmp['queryAction']='index';
                $tmp['queryController']='perfdailylog';
                $tmp['queryBtnName']='查看日志';
                $tmp['targetTab'] = 'list';
            }
            $tmp['queryParamers'] = urlencode(json_encode($tmp['queryParamers']));
        }
        return $tmp;
    }

    public static function myMessage ($where=null, $pager=null, $show=false) {
        $records = self::paged($where, $pager, 'rsort create_time');
        foreach($records as $k => $record){
            $records[$k] = self::transformData($record, $show);
        }
        return $records;
    }


    public static function unReadMessage($userid) {
        $model = self::getCopy();
        $where = ['type!=2', 'receiverid'=>$userid, 'flag'=>0];
        return $model->db()->getRecordCount($model->tbname(), $where);
    }
}
