<?php
/**
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/26
 * Time: 14:55
 */
namespace Api\Baidutongji;
use Api\Baidutongji\Base\LoginConnection;
use Api\Baidutongji\Base\LoginService;
use Api\Baidutongji\Base\ReportService;
class ApiBaiduTongJi {
    const LOGIN_URL             = 'https://api.baidu.com/sem/common/HolmesLoginService';
    const API_URL               = 'https://api.baidu.com/json/tongji/v1/ReportService';
    const USERNAME              = '快快贷网';
    const PASSORD               = 'kkdai121212';
    const TOKEN                 = '2bc7ac5dc08fb8620cdeb415e22f3069';
    const UUID                  = '*****';
    const ACCOUNT_TYPE          = 1;

    protected $connect          = null;
    protected $reportService    = null;

    public function login () {
        $this->connect  = new LoginService(self::LOGIN_URL, self::UUID, self::ACCOUNT_TYPE);

        /* preLogin */
        if(!$this->connect->preLogin(self::USERNAME, self::TOKEN)){
//echo_log('prelogin unseccess');
            return false;
        }

        /* doLogin */
        $ret = $this->connect->doLogin(self::USERNAME, self::PASSORD, self::TOKEN);
        if($ret) {
            $ucid = $ret['ucid'];
            $st = $ret['st'];
        }else {
//echo_log('dologin unseccess');
            return false;
        }

        $this->reportService = new ReportService(self::API_URL, self::USERNAME, self::TOKEN,self::ACCOUNT_TYPE, self::UUID, $ucid, $st);
        return true;
    }

    public function getSiteList () {
        return $this->reportService->getSiteList();
    }

    public function getSiteData ($domain, $start_date, $end_date, $method='trend/time/a', $metrics='pv_count,visitor_count,ip_count', $max_results=0, $gran='day') {
        $ret = $this->getSiteList();
//echo_log($ret, 'ret#####');
        $siteList = $ret['body']['data'][0]['list'];
        if(count($siteList) > 0) {
            foreach($siteList as $v) {
                if($v['domain'] == $domain) {
                    $siteId = $v['site_id'];
                    break;
                }
            }
        }
        if(empty($siteId)) {
            return [];
        }

        $params = [
            'site_id' => $siteId,                   //站点ID
            'method' => $method,             //趋势分析报告
            'start_date' => $start_date,             //所查询数据的起始日期
            'end_date' => $end_date,               //所查询数据的结束日期
            'metrics' => $metrics,  //所查询指标为PV和UV
            'max_results' => $max_results,                     //返回所有条数
            'gran' => $gran,                        //按天粒度
        ];

        $ret = $this->reportService->getData($params);
        return json_decode($ret['raw'], true);
    }

//    public function dataForRpt ($start_date, $end_date, $domain='kuaikuaidai.com', $method='trend/time/a', $metrics='pv_count,visitor_count,ip_count', $max_results=0, $gran='day') {
//
//    }
}