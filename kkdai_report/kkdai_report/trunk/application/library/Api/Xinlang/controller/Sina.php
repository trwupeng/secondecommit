<?php
namespace Api\Xinlang\controller;
    /**
     * @author wu.peng
     * @param Date: 2016/9/23
     * @param Time: 5:30
     * demo只是提供一个接口对接编写的思路，具体接口对接商户技术以自身项目的实际情况来进行接口代码的编写。
     */
    @include_once(dirname ( __File__ ) ."/../api/Weibopay.php");
    @include_once(dirname ( __File__ ) ."/../config/conf.php");
    class Sina
    {

   
        /**
         * 查询收支明细
         * query_account_details
         * @param array $data
         */
        function query_account_details($data = array())
        {
            $weibopay = new \Api\Xinlang\api\Weibopay();
            /**************获取查询收支明细信息参数****************/
            $service = $data['service'];//服务名称
            $version = $data['version'];//接口版本
            $request_time = $data['request_time'];//请求时间
            $partner_id = $data['partner_id'];//合作者身份ID
            $_input_charset = $data['_input_charset'];//参数编码字符集
            $sign_type = $data['sign_type'];//签名类型
            /****************业务参数***********************/
            $identity_id = $data['identity_id'];//会员标识
            $identity_type = $data['identity_type'];//用户标识类型
            $account_type = $data['account_type'];//查询账户类型
            $start_time = $data['start_time'];//开始时间
            $end_time = $data['end_time'];//结束时间
            $page_no = $data['page_no'];//页号
            $page_size = $data['page_size'];//每页大小
            @$extend_param = $data['extend_param'];//扩展参数

            $param = array();
            $param['service'] = $service;
            $param['version'] = $version;
            $param['request_time'] = $request_time;
            $param['partner_id'] = $partner_id;
            $param['_input_charset'] = $_input_charset;
            $param['sign_type'] = $sign_type;
            $param['identity_id'] = $identity_id;
            $param['identity_type'] = $identity_type;
            $param['account_type'] = $account_type;
            $param['start_time'] = $start_time;
            $param['end_time'] = $end_time;
            $param['page_no'] = $page_no;
            $param['page_size'] = $page_size;
            if (isset($extend_param)) {
                $param['extend_param'] = $extend_param;
            }
            ksort($param);//对签名参数据排序
            //对查询收支明细报文进行签名
            $sign = $weibopay->getSignMsg($param, $sign_type,$_input_charset);
            //签名结果放入报文
            $param['sign'] = $sign;
          //  $weibopay->write_log("查询收支明细请求参数" . json_encode($param));
            $data = $weibopay->createcurl_data($param); // 调用createcurl_data创建模拟表单需要的数据
            $result = $weibopay->curlPost(sinapay_mgs_url, $data,$_input_charset); // 使用模拟表单提交进行数据提交
            $splitdata = json_decode($result, true);
            $sign_type = $splitdata ['sign_type'];//签名方式
            ksort($splitdata); // 对签名参数据排序
            if ($weibopay->checkSignMsg($splitdata, $sign_type,$_input_charset)) {
                if ($splitdata["response_code"] == 'APPLY_SUCCESS') { // 成功
                    return $splitdata;
                    exit();
                } else {
                    //业务处理失败
                    return $splitdata;
                    exit();
                }
            } else {
                die ("sing error！");
            }
        }

   
    }

?>
