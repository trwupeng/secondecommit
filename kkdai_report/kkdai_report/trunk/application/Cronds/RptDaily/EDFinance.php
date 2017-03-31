<?php
namespace PrjCronds;
/**
 * 日报账户统计
 * 快快贷这里，`flagUser`==1的超级用户不参与统计
 * @author Simon Wang <hillstill_simon@163.com>
 */
class EDFinance extends \Sooh\Base\Crond\Task{
	public function init() {
		parent::init();
		$this->toBeContinue=true;
		$this->_secondsRunAgain=1200;//每20分钟启动一次
		$this->_iissStartAfter=255;//每小时03分后启动

		$this->ret = new \Sooh\Base\Crond\Ret();

	}
	public function free() {
		parent::free();
	}

	/**
	 * @param \Sooh\Base\Time $dt
	 */
	protected function onRun($dt) {
		$this->oneday($dt->YmdFull);
		if(!$this->_isManual && $dt->hour<=6){
			$dt0 = strtotime($dt->YmdFull);
			switch ($dt->hour){
				case 1: $this->oneday(date('Ymd',$dt0-86400*10));break;
				case 2: $this->oneday(date('Ymd',$dt0-86400*7));break;
				case 3: $this->oneday(date('Ymd',$dt0-86400*4));break;
				case 4: $this->oneday(date('Ymd',$dt0-86400*3));break;
				case 5: $this->oneday(date('Ymd',$dt0-86400*2));break;
				case 6: $this->oneday(date('Ymd',$dt0-86400*1));break;
			}
		}
		return true;
	}
	
	protected function oneday($ymd)
	{
        var_log('oneday>>>');
        $arr = ['FinanceK','FinanceM','FinanceXX','FinanceXS','NewFinanceK','NewFinanceM','NewFinanceXS','NewFinanceXX'];
        $flg = ['0','100','101','102','103','104','105','106','107','108','109','110','111','112','113','114','115'];
        //跑过去7天的数据
        $debug = 0;
        if($debug){
            var_log('this is debug>>>');

        }else{
            for($i=0;$i<7;$i++){
                $newYmd = date('Ymd',strtotime('- '.$i.'days',strtotime($ymd)));
                foreach($arr as $v){
                    foreach($flg as $vv){
                        \Rpt\Misc\Base\Daily::submit($v,$newYmd,$vv);
                    }
                }
            }
        }

       // var_log(\Rpt\Misc\Base\Daily::$result,'>>>');

	}
}
