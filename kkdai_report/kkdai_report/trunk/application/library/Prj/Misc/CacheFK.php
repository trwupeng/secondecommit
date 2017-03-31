<?php
namespace Prj\Misc;
/**
 * Created by PhpStorm.
 * User: tang.gaohang
 * Date: 2016/10/24
 * Time: 11:49
 */
class CacheFK {
    protected static $that = null;
    protected static $dir = '/public/cache/';
    protected $id;
    public static $error = '';
    protected $file;
    public static function getCopy($id){
        if(!self::$that){
            self::$that = new self();
        }
        self::$that->id = $id;
        return self::$that;
    }

    protected function getPath(){
        return APP_PATH.self::$dir.$this->getFileName().'.php';
    }

    protected function getFileName(){
        return $this->id;
    }

    protected static function setError($msg){
        self::$error = $msg;
        error_log('ERROR>>>'.$msg);
    }

    public function save($data){
        $data = (array)$data;
        if(!is_dir(APP_PATH.self::$dir)){
            self::setError('缓存目录不存在');
            return false;
        }
        $str = "<?php \nreturn ".var_export($data,true).';';
        return file_put_contents($this->getPath(),$str);
    }

    public function getData(){
        if(!file_exists($this->getPath())){
            $this->save([]);
        }
        return require $this->getPath();
    }

    public function getLastUpdateTime(){
        if(!file_exists($this->getPath())){
            return 0;
        }
        return filemtime($this->getPath());
    }

    public function isExpired($second = 60){
        return time() - $this->getLastUpdateTime() > $second ? true : false;
    }

    /**
     * 将数据缓存
     * @param $expireSec
     * @param $method
     * @param array $args
     * @return mixed
     */
    public function cacheData($expireSec , $method , $args = []){
        if($this->isExpired($expireSec)){
            $ret = call_user_func_array($method , $args);
            if($ret){
                $this->save($ret);
            }
        }else{
            \Prj\Misc\ViewFK::log('get cache from id:'.$this->id);
            $ret = $this->getData();
        }
        return $ret;
    }
}