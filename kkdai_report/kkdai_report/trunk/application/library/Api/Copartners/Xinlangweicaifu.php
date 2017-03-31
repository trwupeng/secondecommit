<?php
namespace Api\Copartners;
/**
 *  TODO： 注意TODO需要修改的部分
 *
 * 新浪微财富 推送及查询接口
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/24 0024
 * Time: 下午 4:40
 */

class Xinlangweicaifu extends \Lib\Services\CopartnerApiBase {

    // 哪些协议的用户通知

//    protected $uri = 'http://tapi.aizichan.cn/api/'; // 测试用的接口uri
//    protected $token = 'c68d419bc250b9c74cd067f16344a67c';
//    protected $procode = '485ae4ea22ae40086e3663f9b78417ea';

    // 正式用的接口uri
    protected $uri = 'http://api.aizichan.cn/api/';

    protected $newRegPostUri = 'receiveSponsorData/receiveSponsorTaskData.do';
    protected $newOrderPostUri = 'receiveSponsorData/receiveSponsorWmpData.do';

    protected $token = '9596d881c200fc18281a40c3476d73bb'; // 正式环境

    protected $procode = 'a6e9338dc4559a95ea4bdf1f1b8af8db';  // 正式环境
    protected $come = 'ajm';
    protected $notifyContractIds = ['104420160525310000','104420160525310001','104420160525310002'];
    
    protected $inquiryToken = '7a8ebba1779e3fcdba6a4274911f48c0';

    // 推送新用户
    public function notifyNewReg ($userId)
    {
        $postUri = 'receiveSponsorData/receiveSponsorTaskData.do';

        $r = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt)
            ->getRecord(\Rpt\Tbname::tb_user_final, 'contractId,clientType,cp_id,phone', ['userId' => $userId]);
        if (!in_array($r['contractId'], $this->notifyContractIds)) {
            return new \Sooh\Base\RetSimple(-3, 'skip');
        }
        if($r['cp_id'] == '') {
            return new \Sooh\Base\RetSimple(-2, '无cp_id');
        }

        $data = [
            'procode'   => $this->procode,
            'userid'    => $r['cp_id'],
            'come'      => $this->come,
            'mobile'    => $r['phone'],
//            'fromtype'  =>'',
        ];
//签名方式：MD5(procode=1212&come=ajm&userid=123456&token=1111)
//签名方式：参数字符串都转为小写然后MD5
//token：是一个约定的字符串
        $data['sign'] = md5(strtolower('procode=' . $data['procode']
                        . '&come=' . $data['come']
                        . '&userid=' . $data['userid']
                        . '&token=' . $this->token));
error_log('请求uri：'.$this->uri . $this->newRegPostUri);
var_log($data, '请求数据：');

        $ret = $this->postData($data, $this->uri . $this->newRegPostUri);

        if (empty($ret) || !is_array($ret)) {
            return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault, 'invalid response receive:'.(empty($ret)?'(empty-string)':$ret));
        }else {
            if ($ret['resultcode'] == 200) {
                return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::ok, $ret['message']);
            } else {
                return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault, $ret['message']);
            }
        }
    }

    /**
     * 推送新订单
     */
    public function notifyNewOrder ($ordersId) {
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $record = $db->getRecord(\Rpt\Tbname::tb_orders_final, 'userId,ymd,hhiiss,amount,waresId', ['ordersId'=>$ordersId]);
      
        $r=$db->getRecord(\Rpt\Tbname::tb_user_final,'contractId,clientType,cp_id,phone',['userId'=>$record['userId']]);
         if (!in_array($r['contractId'], $this->notifyContractIds)) {
             return new \Sooh\Base\RetSimple(-3, 'skip');
        }
        if($r['cp_id']=='') {
            return new \Sooh\Base\RetSimple(-2, '用户无cp_id');
        }
        $prdt = $db->getRecord(\Rpt\Tbname::tb_products_final, 'waresName,dlUnit,deadLine,yieldStatic,ymdStartReal,shelfId,mainType',
            ['waresId'=>$record['waresId']]);
        if (empty($prdt)) {
            return new \Sooh\Base\RetSimple(-2, '产品表尚无此产品');
        }elseif($prdt['mainType']==501) {
            return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault, '体验标');
        }




// procode	String	是	赞助商户编码
//userid	String	是	用户code
//creditid	Int	是	产品ID
//name	String	是	产品名称  TODO
//mobile	String	否	手机号码
//fromtype	String	否	来源类型（1-App；2-PC）
//createtime	String	是	购买理财时间
//bidamount	Float	是	金额
//datetype	String	是	理财周期单位(年、月、天) TODO
//deadline 	Int	是	周期值 TODO
//repaymentdate	String	是	赎回日期 TODO
//creditrate	Float	是	理财预期年化收益率 TODO
//bidid	Int	是	id (投标唯一编号)对账使用
//sign	String	是	签名方式：
//MD5(procode=1212&come=ajm&userid=123456&credit
//id=123&bidamount=100.0000&bidid=123&name=20150
//545aaa&createtime=2015-12-01
//12:15:15&repaymentdate=2015-10-02&datetype=day
//        &deadline=30&creditrate=0.15&token=xxx2323yywe
//we2323)
//签名方式：参数字符串都转为小写然后MD5
//token：是一个约定的字符串,在联调时由我们提供
//come	String	是	公司邀请码（默认ajm）

        $data = [
            'procode'       => $this->procode,
            'userid'        => $r['cp_id'],
            'creditid'      => $record['waresId'],
            'name'          => $prdt['waresName'],
            'mobile'        => $r['phone'],
//            'fromtype'      => '',
            'createtime'    => date('Y-m-d H:i:s', strtotime($record['Ymd'].sprintf('%06d', $record['hhiiss']))),
            'bidamount'     => sprintf('%.2f',$record['amount']/100),
            'datetype'      => $prdt['dlUnit'],
            'deadline'      => $prdt['deadLine'],
            'repaymentdate' => date('Y-m-d H:i:s', strtotime($prdt['ymdStartReal'])+$prdt['deadLine']*86400),
            'creditrate'    => $prdt['yieldStatic'],
            'bidid'         => $ordersId,
            'come'          => $this->come,
        ];
        if ($record['shelfId'] == 0) {
            $data['deadline'] = 1;
            $data['repaymentdate'] = $data['createtime'];  // 天天赚还款日期直接写成购买当天的
        }

        $data['sign'] =md5(strtolower('procode='.$data['procode']
                        . '&come='.$this->come
                        . '&userid='.$r['cp_id']
                        . '&creditid='.$data['creditid']
                        . '&bidamount='.$data['bidamount']
                         .'&bidid='.$data['bidid']
                        . '&name='.$data['name']
                        . '&createtime='.$data['createtime']
                        . '&repaymentdate='.$data['repaymentdate']
                        . '&datetype='.$data['datetype']
                        . '&deadline='.$data['deadline']
                        . '&creditrate='.$data['creditrate']
                        . '&token='.$this->token));
error_log('请求的uri：'.$this->uri . $this->newOrderPostUri);
var_log($data, '请求的数据：');
        $ret = $this->postData($data, $this->uri.$this->newOrderPostUri);
        if (empty($ret) || !is_array($ret)) {
            return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault, 'invalid response receive:'.(empty($ret)?'(empty-string)':$ret));
        }else {
            if ($ret['resultcode'] == 200) {
                return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::ok, $ret['message']);
            } else {
                return new \Sooh\Base\RetSimple(\Sooh\Base\RetSimple::errDefault, $ret['message']);
            }
        }
    }


    protected function postData ($data, $uri, $retry = 5) {
        while ($retry >0) {
error_log(__CLASS__.'###重试机制倒数：'.$retry);
            $ret = \Prj\Misc\Funcs::curl_post($uri, $data);
            $ret = json_decode($ret, true);
var_log($ret, '返回结果：');
            if (!empty($ret)  && is_array($ret)) {
                break;
            }
            $retry--;
        }
        return $ret;
    }

    public function inquiryUsers($args) {
        /**
         *
         *  请求参数
         *
         * procode	String	商户编码	否
         * token	String	约定秘钥	否
         * come	String	公司邀请码 默认值（ajm）	否
         * page_size	String	每一页的数据大小	是
         * page_index	String	当前页	是
         * startime	String 	开始日期（yyyy-MM-dd）	是
         * endtime	String 	结束日期（yyyy-MM-dd）	是
         */


        if ($args['procode'] != $this->procode) {
            return ['resultcode'=>201, 'message'=>'商户编码错误'];
        }
        if ($args['token'] != $this->token) {
            return ['resultcode'=>201, 'message'=>'查询密钥错误'];
        }
        if($args['come'] != $this->come) {
            return ['resultcode'=>201, 'message'=>'公司邀请码错误'];
        }

        $pageSize = $args['page_size'];
        $page = $args['page_index'];

        if (($pageSize !== null || $pageSize != '') && !is_numeric($pageSize)) {
            return ['resultcode'=>201, 'message'=>'page_size参数错误'];
        }
        if (($page !== null || $page!= '') && !is_numeric($page)) {
            return ['resultcode'=>201,'message'=>'page_index参数错误'];
        }

        $page = $page===null ? 0 : $page;
        $page = $pageSize===null ? 0: $page;

        if ($page >0 ) {
            $page -= 1;
        }

        $startTime = $args['startime'];
        $endTime = $args['endtime'];

        if (!$this->checkDateFormat($startTime) || !$this->checkDateFormat($endTime) || $startTime > $endTime) {
            return ['resultcode'=>201, 'message'=>'日期格式错误'];
        }

        $where = ['contractId'=>$this->notifyContractIds];
        $where['cp_id!'] = '';
//		$where['notifyNewReg*']='0|%';
        if (!empty($endTime)) {
            $where['ymdReg]'] = date('Ymd', strtotime($startTime));
            $where['ymdReg['] = date('Ymd', strtotime($endTime));
        }elseif (!empty($startTime)){
            $where['ymdReg]'] = date('Ymd', strtotime($startTime));
        }else {
            $where['ymdReg>'] = 0;
        }
        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $recordsCount =  $db->getRecordCount(\Rpt\Tbname::tb_user_final, $where);   
        $records = $db->getRecords(\Rpt\Tbname::tb_user_final, 'cp_id,ymdReg,phone', $where, 'sort ymdReg', $pageSize, $page*$pageSize);
        $pageCount = 0;
        if($pageSize) {
            $pageCount = ceil($recordsCount / $pageSize);
        }else{
            $page = 0;
            if (!empty($records)) {
                $pageCount = 1;
            }
        }

        if (!empty($records)) {
            foreach($records as $k => $r) {
                $records[$k]['userid'] = $r['cp_id'];
                $records[$k]['createtime'] = date('Y-m-d', strtotime($r['ymdReg']));
                $records[$k]['mobile'] = $r['phone'];
                unset($records[$k]['userId']);
                unset($records[$k]['cp_id']);
                unset($records[$k]['ymdReg']);
            }
        }
        return [
            'resultcode'    =>200,
            'message'       =>'成功',
            'page_count'    =>$pageCount,
            'page_index'    =>$page+1,
            'obj'           =>[
                'list'=>$records
            ],
        ];

    }

    private function checkDateFormat ($time) {
        if ($time===null) {
            return true;
        }
        $time = explode('-', $time);
        return checkdate($time[1], $time[2], $time[0]);
    }

    public function inquriyOrders ($args) {
//procode	String	商户编码	否
//token	String	约定秘钥	否
//come	String	公司邀请码 默认值（ajm）	否
//page_size	String	每一页的数据大小	是
//page_index	String	当前页（为空表示查询所有）	是
//startime	String 	开始日期（yyyy-MM-dd）	是
//endtime	String 	结束日期（yyyy-MM-dd）	是
        if ($args['procode'] != $this->procode) {
            return ['resultcode'=>201, 'message'=>'商户编码错误'];
        }
        if ($args['token'] != $this->token) {
            return ['resultcode'=>201, 'message'=>'查询密钥错误'];
        }
        if($args['come'] != $this->come) {
            return ['resultcode'=>201, 'message'=>'公司邀请码错误'];
        }

        $pageSize = $args['page_size'];
        $page = $args['page_index'];

        if (($pageSize !== null || $pageSize != '') && !is_numeric($pageSize)) {
            return ['resultcode'=>201, 'message'=>'page_size参数错误'];
        }
        if (($page !== null || $page!= '') && !is_numeric($page)) {
            return ['resultcode'=>201,'message'=>'page_index参数错误'];
        }

        $page = $page===null ? 0 : $page;
        $page = $pageSize===null ? 0: $page;
          
          if($page>0){
             $page-=1;
          }
        $startTime = $args['startime'];
        $endTime = $args['endtime'];

        if (!$this->checkDateFormat($startTime) || !$this->checkDateFormat($endTime) || $startTime > $endTime) {
            return ['resultcode'=>201, 'message'=>'日期格式错误'];
        }

        $db = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
        $arr_user = $db->getAssoc(\Rpt\Tbname::tb_user_final, 'userId','phone,cp_id',
            ['contractId'=>$this->notifyContractIds, 'cp_id!'=>'']);
        if (empty($arr_user)) {
            return [
                'resultcode'    =>200,
                'message'       =>'成功',
                'page_count'    =>0,
                'page_index'    =>$page+1,
                'obj'           => [
                    'list'=>[]
                ],
            ];
        }

        $tyb = $db->getCol(\Rpt\Tbname::tb_products_final, 'waresId', ['mainType'=>501]);
        //var_log(\Sooh\DB\Broker::lastCmd(), 'sql>>>>>>>>>>>>');        
        $where['waresId!']=$tyb;
        $where['userId']=array_keys($arr_user);
        $where['notifyNewOrder!'] ='';

//		$where['notifyNewOrder*']='0|%';
        if (!empty($endTime)) {
            $where['ymd]'] = date('Ymd', strtotime($startTime));
            $where['ymd['] = date('Ymd', strtotime($endTime));
        }elseif (!empty($startTime)){
            $where['ymd]'] = date('Ymd', strtotime($startTime));
        }else {
            $where['ymd>'] = 0;
        }

        $recordsCount = $db->getRecordCount(\Rpt\Tbname::tb_orders_final, $where);
// var_log(\Sooh\DB\Broker::lastCmd(), 'sql>>>>>>>>>>>>');         
        var_log($recordsCount,'满足的订单量 :');
        $records = $db->getRecords(\Rpt\Tbname::tb_orders_final, 'userId, ordersId, waresId, amount, ymd, yieldStatic'
            ,$where, 'sort ymd', $pageSize, $page*$pageSize);

        /**
        *$record = $db->getRecord(\Rpt\Tbname::tb_orders_final, 'userId,ymd,hhiiss,amount,waresId', ['ordersId'=>$ordersId]);
        *
        * $r=$db->getRecord(\Rpt\Tbname::tb_user_final,'contractId,clientType,cp_id',['userId'=>$record['userId']]);
        *var_log($r);
        *if (!in_array($r['contractId'], $this->notifyContractIds)) {
        *    return parent::notifyNewOrder($ordersId);
        *}
        */


        if (!empty($records)) {
            $tmp = [];
            foreach($records as $k => $r) {
                
                $tmp[$k] = $r['waresId'];
                $records[$k]['userid'] = $arr_user[$r['userId']]['cp_id'];
                $records[$k]['mobile'] = $arr_user[$r['userId']]['phone'];
                $records[$k]['bidid'] = $r['ordersId'];
                $records[$k]['bidamount'] = sprintf('%.2f', $r['amount']/100);
                $records[$k]['createtime'] = date('Y-m-d', strtotime($r['ymd']));
                $records[$k]['creditrate'] = $r['yieldStatic'];
                unset($records[$k]['userId']);
                unset($records[$k]['ordersId']);
                unset($records[$k]['amount']);
                unset($records[$k]['ymd']);
                unset($records[$k]['yieldStatic']);
                unset($records[$k]['waresId']);
            }
            // ymdSartReal 募集开始时间
            
            $prdt_info = $db->getAssoc(\Rpt\Tbname::tb_products_final, 'waresId', 'waresName, deadLine, dlUnit, ymdStartReal, shelfId',
                ['waresId'=>$tmp]);
//           var_log(\Sooh\DB\Broker::lastCmd(), 'sql>>>>>>>>>>>>');
            foreach($tmp as $k => $waresId) {
                $records[$k]['name'] = $prdt_info[$waresId]['waresName'];
                $records[$k]['datetype'] = $prdt_info[$waresId]['dlUnit'];

                if($prdt_info[$waresId]['shelfId'] != 0) {
                    $records[$k]['deadline'] = $prdt_info[$waresId]['deadLine'];
                    if($prdt_info[$waresId]['dlUnit'] == '天'){
                        $records[$k]['repaymentdate'] = date('Y-m-d', strtotime($prdt_info[$waresId]['ymdStartReal'])+$prdt_info[$waresId]['deadLine']*86400);
                    }elseif($prdt_info[$waresId]['dlUnit'] == '月'){
                        $records[$k]['repaymentdate'] = date('Y-m-d', strtotime("+".$prdt_info[$waresId]['deadLine']." months", strtotime($prdt_info[$waresId]['ymdStartReal'])));
                    }elseif ($prdt_info[$waresId]['dlUnit'] == '年'){
                        $records[$k]['repaymentdate'] = date('Y-m-d', strtotime("+".$prdt_info[$waresId]['deadLine']." years", strtotime($prdt_info[$waresId]['ymdStartReal'])));
                    }
                }else {
                   $records[$k]['deadline'] = 1;
                    $records[$k]['repaymentdate'] = $records[$k]['createtime'];
                }
            }
        }

        $pageCount = 0;
        if($pageSize) {
            $pageCount = ceil($recordsCount / $pageSize);
        }else{
            $page = 0;
            if (!empty($records)) {
                $pageCount = 1;
            }
        }

        return [
            'resultcode'    =>200,
            'message'       =>'成功',
            'page_count'    =>$pageCount,
            'page_index'    =>$page+1,
            'obj'           =>
                [
                    'list'=>$records
                ],
        ];

    }
}