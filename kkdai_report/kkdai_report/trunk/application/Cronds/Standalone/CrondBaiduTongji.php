<?php
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondBaiduTongji&ymdh=20150819"
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/8/29 0029
 * Time: 下午 3:52
 */
namespace PrjCronds;
class CrondBaiduTongji extends \Sooh\Base\Crond\Task {
    public function init() {
        parent::init();
        $this->toBeContinue=true;
        $this->_iissStartAfter=1250;
        $this->ret = new \Sooh\Base\Crond\Ret();
    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt)
    {
        if ($this->_isManual) {
            $ymd = $dt->YmdFull;
            $this->importData($ymd);
        }else {
            if($dt->hour == 0) {
error_log('baidutongji: '.date('Y-m-d H:i:s'));
                $ymd = date('Ymd', $dt->timestamp(-1));
                $this->importData($ymd);
                $this->toBeContinue = false;
            }
        }
        return true;
    }

    protected function importData ($ymd) {
        $baiduTongjiDataDig = new \Rpt\DataDig\BaidutongjiDataDig();
        $ret = $baiduTongjiDataDig->importData('kuaikuaidai.com', $ymd, $ymd);
        if($ret) {
            error_log($ymd.' Baidu Tong Ji import data finished');
            return true;
        }else {
            error_log($ymd.' Baidu Tong ji import data failed');
            return false;
        }
    }
}