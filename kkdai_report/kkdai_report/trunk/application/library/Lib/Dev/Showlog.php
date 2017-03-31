<?php
namespace Lib\Dev;

class Showlog extends \Lib\Dev\Showsms
{

    protected function printHtmlHead($title='日志查询') {
        $html  = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        $html .= '<title>'.$title.'</title>';
        $html .= '<style type="text/css">
				body{margin:0;padding:0 5px 5px 5px; font-size:14px; font-family:“宋体”,Arial, Helvetica, sans-serif}
				.bg01{ background:#f0f0f0; height:24px;}
				.case_table{font-size:12px; color:#333;line-height:25px;border:1px #c13f4d solid; width:100%; background:#FFF;cellspacing:0px;}
				.case_table th{font-size:12px; font-weight:600; text-align:center; height:24px; line-height:24px; }
				.case_table td{word-wrap:break-word;border:1px solid;border-collapse: separate; empty-cells:show;}
				.case_table td span{color:red;}
				.frame_content{width:100%;height:100%;visibility:inherit;z-index:1;}
				.frame_top{ position:relative;height:34px;}
				</style>';
        $html .= '<head><body>';
        echo $html;
    }
    
    protected function printHtmlForm ($where) {
        $ip = isset($where['ip'])?$where['ip']:'';
        $ymd = isset($where['ymd'])?$where['ymd']:date('Ymd');
        $html .= '<div class=bg01 style="padding-top:5px;padding-bottom:5px;">';
        $html .= '<form action="'.'/index/dev/logview'.'" method="get">'.'IP地址：<input type=text size="16" name="ip" value="'.$ip.'">';
        $html .= '日期：<input type=text size="16" name="ymd" value="'.$ymd.'">';
        $html .= '&nbsp;<input type=submit value="查询" class="submit02"></form></div>';
        echo $html;
    }
    
    protected function printHtmlFoot () {
        $html = "</html></body>";
        echo $html;
    }
    
    protected function printResult ($where) {
        if (empty($where)){
            return;
        }
        $db = \Sooh\DB\Broker::getInstance('default');
        $dbname = 'db_logs';
        $tbs = $db->getTables($dbname, 'tblog_'.$where['ymd'].'%_a_%');
// var_log($tbs, 'tbs>>>');        
        if (empty($tbs)){
            echo "无数据";
            return;
        }
        
        $records = array();
        foreach ($tbs as $tb) {
            $tmp = $db->getPair($dbname.'.'.$tb, 'logGuid','hhiiss', $where);
            error_log('sizeof>>>>'.sizeof($tmp));
            $records += $tmp;
        }
        arsort($records);
        if (!empty($records)) {
            $tmp = $db->execCustom(array('sql'=>'SHOW COLUMNS FROM '.$dbname.'.'.$tb));
            $tmp = $db->fetchAssocThenFree($tmp);
            foreach($tmp as $value){
                $fields[] = $value['Field'];
            }
            $html = '<table border="1">';
            $html .= '<tr><th>'.implode('</td><td>', $fields).'</th></tr>';
            foreach ($records as $k => $v) {
                $n = ($k%2==0)?0:1;
                $r = $db->getRecord($dbname.'.tblog_'.$where['ymd'].'_a_'.$n, '*', array('logGuid'=>$k));
                $html .= '<tr ><td>';
                $html .= implode('</td><td>', $r);
                $html .= '</td></tr>';
            }
        }else {
            echo "无数据";
            return;      
        }
        $html .= '</table>';
        echo $html;    
    }
    public function logviewAction () {
        $ip = $this->_request->get('ip');
        $ymd = $this->_request->get('ymd');
        if (!empty($ip)){
            $where['ip'] = $ip;
        }
        if (!empty($ymd)){
            $where['ymd'] = $ymd;
        }
    
        \Sooh\Base\Ini::getInstance()->viewRenderType('echo');
        $this->printHtmlHead('日志查询');
        $this->printHtmlForm($where);
        $this->printResult($where);
        $this->printHtmlFoot();
    }
}
