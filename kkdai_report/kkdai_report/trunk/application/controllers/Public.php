<?php
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/1/29
 * Time: 13:41
 */
class PublicController extends Prj\BaseCtrl
{
    public function imgAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
        header('Content-type: image/jpg');
        $id = $this->_request->get('id');
        echo \Prj\Data\File::getDataById($id);
    }

    /**
     * BBS首页图片
     * @input int type 1=>banner 4=>推荐 8=>讨论 16=>底部
     */
    public function bbsImgAction()
    {
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        if(!$type = $this->_request->get('type'))return $this->returnError('miss_type');
        $img = \Prj\Data\Img::getCopy('');
        $db = $img->db();
        $tb = $img->tbname();
        $rs = $db->getRecords($tb,["url","fileId","type","sort","exp"],['status]'=>0,'type'=>$type],'sort sort');
        foreach($rs as $v){
            $v['imgUrl'] = \Sooh\Base\Tools::uri(['id'=>$v['fileId']],'img');
            $v['typeName'] = \Prj\Consts\Discuz::$img_types[$v['type']];
            unset($v['type']);
            unset($v['fileId']);
            $newRs[] = $v;
        }
        $this->_view->assign('imgList',$newRs);
        $this->returnOK();
    }

    public function testPostAction(){
        \Sooh\Base\Ini::getInstance()->viewRenderType('json');
        $data = $this->_request->get('data');
        isset($data);
        return $this->returnOK();
    }

    public function activityDoubleElevenAction(){
        $expire = 3600;
        $cache = \Prj\Misc\CacheFK::getCopy('activityDoubleEleven');
        $lastUpdate = $cache->getLastUpdateTime();
        if(!$lastUpdate || time() - $lastUpdate > $expire || $this->_request->get('_refresh')){
            $date = $this->_request->get('_date') ? date('Y-m-d',strtotime($this->_request->get('_date'))) : '2016-10-11';
            $dateLine = $this->_request->get('_test') ? [$date,$date] : ['2016-11-09','2016-11-27'];
            var_log($dateLine,'dateLine>>>');
            $rs = \Prj\Data\MiscData::getActivityDoubleElevenRecords($dateLine);
            //$ret = $cache->save($rs);
            //var_log($ret,'cache save ret>>>');
        }else{
            $rs = $cache->getData();
        }
        foreach($rs as $k=>$v){
            error_log($v['customer_id'].'/'.$v['yearAmount']);
            //unset($rs[$k]['customer_cellphone']);
            unset($rs[$k]['customer_id']);
            unset($rs[$k]['flag']);
            unset($rs[$k]['num']);
            $name = $rs[$k]['customer_name'];
            $phone = $rs[$k]['customer_cellphone'];
            //$rs[$k]['phone'] = $phone;
            $rs[$k]['customer_name'] = mb_substr($name,0,3,'utf-8').str_pad('',mb_strlen($name,'utf-8') - 3,'*');
            $rs[$k]['customer_cellphone'] = mb_substr($phone,0,3,'utf-8').'****'.mb_substr($phone,7,4,'utf-8');
        }
        $data['records'] = $rs;
        $data['lastUpdate'] = $lastUpdate ? date('YmdHis',$lastUpdate) : '';

        $plan = \Prj\Consts\ActivityFK::$doubleEleven;
        foreach($plan as $k=>$v){
            $tmp = [];
            $tmp['start'] = (int)$v[0];
            $tmp['end'] = (int)$v[1];
            switch (true) {
                case time() < strtotime($v[0]) : $tmp['go'] = 0;break;
                case time() > strtotime($v[1]) : $tmp['go'] = -1;break;
                default : $tmp['go'] = 1;
            }
            $plan[$k] = $tmp;
        }
        $data['plan'] = $plan;
        $this->_view->assign('data',$data);
        return $this->returnOK();
    }

    public function luodiyeTongjiAction(){
        $head = ['registerTotal','newPacketTotal','highPacketTotal'];
        $data = [];
        foreach($head as $v){
            $data[$v] = \Prj\Data\Temp::get($v) - 0;
        }
        $this->_view->assign('data',$data);
        return $this->returnOK();
    }

    /**
     * 检查用户在快快贷的登录状态
     */
    public function checkkkdLoginAction(){
        $jskey = $this->_request->get('jskey');
        $api = \Sooh\Base\Ini::getInstance()->get('uriBase')['kkd'];
        if(empty($api))return $this->returnError('api missing');
        $url = $api.'isLogin.do?jskey='.$jskey;
        $str = \Prj\Misc\Funcs::curl_post($url);
        if(empty($str))$str = file_get_contents($url);
        $ret = json_decode($str , true);
        if(empty($ret)){
            error_log('###url:'.$url);
            return $this->returnError('connenct failed');
        }
        if($ret['code'] !== 0)return $this->returnError('未登录');
        $this->_view->assign('data',$ret);
        return $this->returnOK();
    }
}