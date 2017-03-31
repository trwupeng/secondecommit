<?php
namespace Lib\Dev;

class Showsms extends \Prj\BaseCtrl
{
    
    public function showsmsAction () {
		$this->ini->viewRenderType('echo');
		echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"><body>';
		echo '<form action="'.\Sooh\Base\Tools::uri().'">';
		echo '手机列表<input type=text name=phone[]><input type=text name=phone[]><input type=text name=phone[]><input type=submit value=查找>';
		
		$phone = $this->_request->get('phone');
		if(!empty($phone)){
			$sys = new \Sooh\Base\Tests\SMS(\Sooh\DB\Broker::getInstance('default'), \Lib\SMS\Test::tb);
			$rs = $sys->recent($phone);
			echo '<table celpadding=0 cellspacing=2 border=1>';
			echo '<tr><th>phone</th><th>频道</th><th>内容</th></tr>';
			foreach($rs as $r){
				echo '<tr><th>'.$r['phone'].'</th><td>'.$r['channel'].'</td><td>'.$r['msg'].'</td></tr>';
			}
			echo '</table>';
		}
		echo '</html>';
    }
	
	public function showrecentAction()
	{
		$this->ini->viewRenderType('echo');
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		echo '<html xmlns="http://www.w3.org/1999/xhtml">';
		echo '<head><meta http-equiv="content-type" content="text/html;charset=utf-8"><body>';
		echo '<form action="'.\Sooh\Base\Tools::uri().'"><input type=hidden name=__ value="dev/showrecent">';
		echo 'ip<input type=text name=ip>'
		. 'sessionId<input type=text name=device>'
		. 'accountId<input type=text name=u>'
		. 'phone<input type=text name=tel>'
		. '<input type=submit value=查找></form><hr><br>';
		
		$ip = $this->_request->get('ip');
		$device = $this->_request->get('device');
		$u = $this->_request->get('u');
		$tel = $this->_request->get('tel');
		
		$where = [];
		if(!empty($ip)){
			$where['ip']=$ip;
		}
		if(!empty($device)){
			$where['sessionId']=$device;
		}
		if(!empty($u)){
			$where['accountId']=$u;
		}
		if(!empty($tel)){
			$where['phone']=$tel;
		}
		$db = \Sooh\DB\Broker::getInstance();
		$rs = $db->getRecords('db_logs.tb_a', '*',$where,'rsort ymd rsort his',15);
		echo '<hr>';
		if(empty($rs)){
			echo "no records";
		}else{
			echo '<table celpadding=0 cellspacing=2 border=1 style="width:100%">';
			$r = current($rs);
			$r = array_keys($r);
			echo "\n".'<tr><th style="width:400px">request<th>response</th></tr>'."\n";
			foreach($rs as $r){
				$response = $r['returned'];
				unset($r['returned']);
				echo "\n".'<tr><td>';
				foreach($r as $k=>$v){
					$tmp = json_decode($v,true);
					if(is_array($tmp) || sizeof($tmp)>0 ){
						echo "<pre><b>$k</b>:".var_export($tmp,true)."</pre>";
					}else{
						echo "<b>$k</b>:$v<br/>";
					}
				}
				echo "</td><td>".htmlspecialchars($response)."</tr>";
			}
			echo '</table>';
		}
		echo '<hr>';
		echo '</html>';
	}
}
