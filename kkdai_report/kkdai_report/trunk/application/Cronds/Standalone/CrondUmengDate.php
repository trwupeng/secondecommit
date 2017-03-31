<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondUmengDate&ymdh=20160829"
 * 
 * @author wu.peng
 *
 *
 */



class CrondUmengDate extends \Sooh\Base\Crond\Task{
    protected $dbMysql;
    public function init() {
        parent::init();
        $this->toBeContinue=true;
        $this->_iissStartAfter=1250;
        $this->ret = new \Sooh\Base\Crond\Ret();
        $this->dbMysql = \Sooh\DB\Broker::getInstance();
    }

    public function free() {
        parent::free();
        $this->dbMysql = null;
    }
    
    
    protected function onRun($dt)
    {
        if ($this->_isManual) {
            $ymd = $dt->YmdFull;
            $ymd=date('Y-m-d',strtotime($ymd));
            error_log('umeng---data: '.$ymd);
            $this->gather($ymd);
        }else {
            if($dt->hour ==3 || $dt->hour==6) {  // TODO: 上线之后改成==3点
                error_log('umeng---data: '.date('Y-m-d H:i:s'));
                $ymd = date('Y-m-d', $dt->timestamp(-1));
                $this->gather($ymd);
                $this->toBeContinue = false;
            }
        }
        return true;
    }
    
    
    protected function gather($data)
    {
       // $db_produce = \Sooh\DB\Broker::getInstance('produce');
       // $this->printLogOfTimeRang();
        /**
         *
         * umeng的数据
         *
         */
        $rs=new \Api\Umeng\Umeng();
        
        $result=$rs->get_token();
        
        if(!empty($result['auth_token'])){
            $result_auth_token=$rs->auth_token($result['auth_token']);
       
            $andrior_appkey=$result_auth_token[0]['appkey'];
            $ios_appkey=$result_auth_token[1]['appkey'];
            //$date='2016-08-28';
            //var_log($data,'data>>>>>>>>>');

            $channels_andrior=$rs->channels($andrior_appkey,$result['auth_token'],$data);
        
            $channels_ios=$rs->channels($ios_appkey,$result['auth_token'],$data);
            $ram=array();
            $ram=['channels_andrior'=>$channels_andrior,'channels_ios'=>$channels_ios];
            
            var_log($ram,'ram>>>>>>>>>');

            if(!empty($ram)){
                foreach ($ram as $k=>$v){
                    
                    if($k=='channels_andrior'){
                       foreach ($v as $v2){
         
                        $temp=[
                            'ymd'=>$v2['date'],
                            'channels'=>$v2['channel'],
                            'ids'=>$v2['id'],
                            'clientType'=>902,
                            'new_user'=>$v2['install'],
                            'active_user'=>$v2['active_user'],
                            'launches_user'=>$v2['launch'],
                        ];
                        
                        $data_andrior= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','ymd,channels');
                        
                        $tp=[ 'ymd'=>$v2['date'], 'channels'=>$v2['channel']];
                        
                        if(empty($data_andrior)){
                            $this->dbMysql->addRecord('db_kkrpt.tb_umeng_data', $temp);
                        }else{
                            
                            if(in_array($tp, $data_andrior)){
                                $this->dbMysql->updRecords('db_kkrpt.tb_umeng_data', $temp, ['ymd'=>$tp['ymd'],'channels'=>$tp['channels']]);
                            }else{
                                $this->dbMysql->addRecord('db_kkrpt.tb_umeng_data', $temp);
                            }
                            
                        }

                    }

                    }
                  
                    if ($k=='channels_ios'){
                        
                        foreach ($v as $v1){
                        $tmp=[
                            'ymd'=>$v1['date'],
                            'channels'=>$v1['channel'],
                            'ids'=>$v1['id'],
                            'new_user'=>$v1['install'],
                            'active_user'=>$v1['active_user'],
                            'launches_user'=>$v1['launch'],
                             'clientType'=>901
                        ];
                        
                        
                        $data_ios= $this->dbMysql->getRecords('db_kkrpt.tb_umeng_data','ymd,channels');

                        $tp=[ 'ymd'=>$v1['date'], 'channels'=>$v1['channel']];

                          if(empty($data_ios)){
                            $this->dbMysql->addRecord('db_kkrpt.tb_umeng_data', $tmp);
                        }else{
                            
                            if(in_array($tp, $data_ios)){
                                $this->dbMysql->updRecords('db_kkrpt.tb_umeng_data', $tmp, ['ymd'=>$tp['ymd'],'channels'=>$tp['channels']]);
                            }else{
                                $this->dbMysql->addRecord('db_kkrpt.tb_umeng_data', $tmp);
                            }
                         }
                        
                     
                    }
                    
                }
            }
         }

        $this->lastMsg = $this->ret->toString();
        error_log('[ Trace ] ### '.__CLASS__.' ### LastMsg:'.$this->lastMsg);
        return true;
        }
        
        }
}