<?php
/**
 * 目标管理模块中的点评,跟踪,@人员model
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/12/14 0014
 * Time: 下午 2:01
 */
namespace Prj\Data;
class MBPerfReply extends \Sooh\DB\Base\KVObj {


    // 目标type
    const targetType=1;

    // 日志type
    const logType=2;


    // 评论
    const comment = 1;
    // 跟踪
    const trace = 2;
    // @
    const at = 3;
    // 回复
    const reply=4;
    //指派
    const zhipai=5;

    public static $typeEnum = [
        self::comment => '点评',
        self::trace => '跟踪',
        self::at => '@',
        self::reply=>'回复',
        self::zhipai=>'指派',
    ];

    protected static function splitedTbName($n, $isCache)
    {
        return 'mb_perf_reply';
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

   public static  function getIdnum(){
       
       $ret=self::paged();
       $ret=end($ret); 
       $ret_id=$ret['id'];
       return $ret_id;
   }
    
    public static function addRecord($content,$sendid,$receiverid,$type,$batch_type,$batchid,$dstid=[],$parentid=0) {
        $data = [
            'content' => $content,
            'sendid' => $sendid,
            'receiverid' => $receiverid,
            'type' => $type,
            'batch_type' => $batch_type,
            'batchid' => $batchid,
            'parentid' => $parentid,
        ];
        if(!empty($dstid) && is_array($dstid)) {
            $data['dstid'] = implode('|', $dstid);
        }
        $data['create_time'] = date('Y-m-d H:i:s');
        $retry = 5;
        while($retry) {
            try {
                $model = self::getCopy();
                $id = $model->db()->addRecord($model->tbname(), $data);
                $data['id'] = $id;
                break;
            }catch(\ErrorException $e) {
                $retry --;
                error_log($e->getMessage().'\n'.$e->getTraceAsString());
            }
        }
        if($retry ==0) {
            return false;
        }
        $record = self::transformData($data);
        if($parentid == 0){
            $record['parentid'] = $id;
        }else {
            $record['parentid'] = $parentid;
        }
        return $record;

    }

    public static function transformData ($record, $myTrace=false) {
        $tmp['id']= $record['id'];
        $tmp['create_time']= $record['create_time'];
        list($loginName, $cameFrom) = explode('@', $record['sendid']);
        $ret = \Prj\Data\Manager::getDeptNameAndNickname($cameFrom, $loginName);
        $tmp['sender'] = $ret['deptName'].'-'.$ret['nickName'];
        $tmp['type'] = self::$typeEnum[$record['type']];
        $tmp['content'] = $record['content'];
        if(strpos($record['dstid'], '@')!==false) {
            $arrAt = explode('|', $record['dstid']);
            foreach($arrAt as $list) {
                list($loginName, $cameFrom) = explode('@', $list);
                $ret = \Prj\Data\Manager::getDeptNameAndNickname($cameFrom, $loginName);
                $deptName = $ret['deptName'];
                $nickName = $ret['nickName'];
                $tmp['atlist'][] = $deptName.'-'.$nickName;
            }
        }
        $tmp['batch_type'] = $record['batch_type'];
        $tmp['batchid'] = $record['batchid'];
        $tmp['sendid'] = $record['sendid'];
        $tmp['dstid'] = $record['sendid'];
        $tmp['receiverid'] = $record['receiverid'];
        $mineAccount = \Sooh\Base\Session\Data::getInstance()->get('managerId');
        if($tmp['sendid'] == $mineAccount){
            $tmp['canbeReply'] = 0;
        }else {
            $tmp['canbeReply'] =1;
        }

        if($myTrace) {
            $tmp['queryParamers']['zzjgName'] = explode('@', $record['receiverid'])[0];
            $perf_id=explode("：",$record['content'])[1];
            $tmp['queryParamers']['perf_id']=$perf_id;//目标i或者日志id
            
            if($record['batch_type'] == self::targetType){
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
            }elseif($record['batch_type'] == self::logType){
                $tmp['queryParamers']['date'] = date('Y-m-d', strtotime($record['batchid']));
//                $tmp['queryAction']='tabhistorylog';
                $tmp['queryAction']='index';
                $tmp['queryController']='perfdailylog';
                $tmp['queryBtnName']='查看日志';
                $tmp['targetTab'] = 'list';
                $tmp['queryParamers']['perf_id']=$perf_id;//目标i或者日志id
            }
            $tmp['queryParamers'] = urlencode(json_encode($tmp['queryParamers']));
            $tmp['sender'] = '我';
            list ($recvLoginName, $recvCameFrom) = explode('@', $tmp['receiverid']);
            $ret = \Prj\Data\Manager::getDeptNameAndNickname($recvCameFrom, $recvLoginName);
            $tmp['atlist'] = [$ret['deptName'].'@'.$ret['nickName']];
        }
        return $tmp;
    }

    /**
     * 可以获取目标或日志的所有评论,跟踪,@
     * 可以单独的获取回复
     * @param $bodyid 目标或日志的id
     * @param $parentid 评论,跟踪,@ 的父id
     */
    public static function getMyReply ($userid,$batchid=null,$reply_id=null,$parentid=0,$pagerComment=null,$pagerReply=null){
        $where = [
            'receiverid' => $userid,
            'parentid' => $parentid
        ];
        !empty($batchid) && $where['batchid'] = $batchid;
        !empty($reply_id)&& $where['id']=$reply_id;
        // 一级评论
        $records = self::paged($where, $pagerComment, 'rsort create_time');
        // 获取回复 和 数据转换
        foreach ($records as $k => $r) {

            // 获取回复
            $where = [
                'batchid' => $batchid,
                'parentid' => $r['id'],
            ];
            $tmpRecords = self::paged($where, $pagerReply, 'sort create_time');
            foreach($tmpRecords as $key=> $row) {
                $reply = self::transformData($row);
                $reply['parentid'] = $row['parentid'];
                $tmpRecords[$key] = $reply;
            }
            $comment = self::transformData($r);
            $comment['partentid'] = $r['id'];

            $records[$k] = [
                0=>$comment,
                1=>$tmpRecords,
            ];
        }
        return $records;
    }

    public static function getMyTrace ($where=null, $pager=null, $myTrace=false) {
        $records = self::paged($where, $pager, 'rsort create_time');
        foreach($records as $k => $record){
            $records[$k] = self::transformData($record, $myTrace);
        }
        return $records;
    }

}
