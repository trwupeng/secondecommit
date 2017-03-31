<?php
namespace PrjCronds;
/**
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondTotalData&ymdh=20150819"
 *
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/2/17 0017
 * Time: 上午 9:29
 */

class CrondTotalData extends \Sooh\Base\Crond\Task {


    public function init () {
        parent::init();
        $this->toBeContinue = true;
        $this->_iissStartAfter=500; // 每小时的5分 启动
        $this->ret = new \Sooh\Base\Crond\Ret();

    }

    public function free() {
        parent::free();
    }

    protected function onRun($dt) {
        error_log('[ Trace ] ### '.__CLASS__.' ###');
        $data = $this->getData();
        $this->saveAsJsonFile($data);
        $this->toBeContinue = false;
        $this->lastMsg = $this->ret->toString();
        return true;
    }

    protected function getData () {
        $url = 'http://120.55.83.55/index/apisum/getdata';
        return \Sooh\Base\Tools::httpGet($url);
    }

    protected function saveAsJsonFile ($data) {
        if (empty($data)) {
            return;
        }
var_log($data, __CLASS__.'=======data===============================================');
        $filename = APP_PATH.'/public/spread/kkdai1.json';
        file_put_contents($filename, $data);

    }


}