<?php
/**
 * 给渠道展示的数据，有规则限制展示数据。以后渠道都给这个数据。
 *
 */
class CopartneryestdController extends \Yaf_Controller_Abstract
{
    public function viewsimpleAction() {
		$authCode = $this->_request->get('code');
		$copartnerid = $this->_request->get('copartnerid')-0;
		
        $sys = \Prj\Data\Copartner::getCopy();
		$this->db=$sys->db();
		if(!empty($authCode) && !empty($copartnerid)){
			$r = $sys->db()->getRecord($sys->tbname(), '*',['copartnerId'=>$copartnerid]);
			if(empty($r) || $r['authCode']!=$authCode){
				$this->_view->assign('msg','参数错误');
			}else{
				$arr_contractid = $this->db->getPair('tb_contract_0', 'contractId','remarks', ['copartnerAbs'=>$r['copartnerAbs'], 'flgDisplay'=>1]);
				$contractIds = array_keys($arr_contractid);
				if (empty($contractIds)) {
					$this->_view->assign('msg', '暂无数据');
					return;
				}
				$this->rs = $this->db->getRecords('tb_user_final',
						'userId,realname,ymdReg,length(idCard) as flg,phone,contractId,yuebaoInvestmentTotal,dingqiInvestmentTotal,ymdFirstBuy,shelfIdFirstBuy,amountFirstBuy',
						['flagDisplay=1', 'contractId'=>$contractIds,'ymdReg>'=>date('Ymd',time()-45*86400)],'rsort ymdReg');

				foreach($this->rs as $i=>$r){

					$this->rs[ $i ]['phone']=substr_replace($this->rs[ $i ]['phone'], '****', 3, 4);
					if(!empty($this->rs[ $i ]['realname']))	{
						$this->rs[ $i ]['realname'] = substr_replace($this->rs[ $i ]['realname'], '*', 3,3);
					}
					$this->rs[$i]['phone'] = empty($r['phone'])?"":substr($r['phone'],0,4).'***'.substr($r['phone'],-4);

					// 第一次购买类型
					if($this->rs[$i]['ymdFirstBuy'] > 0) {
						$this->rs[$i]['firstType'] = ($this->rs[$i]['shelfIdFirstBuy']>0?'定期':'活期');
						$this->rs[ $i ]['ymdBuy']=$r['ymdFirstBuy'];
						$this->rs[ $i ]['amount']=$r['amountFirstBuy'];
					}else {
						$this->rs[$i]['firstType'] = '';
						$this->rs[ $i ]['ymdBuy']='';
						$this->rs[ $i ]['amount']='';
					}
					unset ($this->rs[$i]['ymdFirstBuy']);
					unset($this->rs[$i]['shelfIdFirstBuy']);
					unset($this->rs[$i]['amountFirstBuy']);

					if($this->rs[$i]['yuebaoInvestmentTotal'] > 0) {
						$this->rs[$i]['ttz']=$this->rs[$i]['yuebaoInvestmentTotal'];
					}else {
						$this->rs[$i]['ttz'] = '';
					}

					if($this->rs[$i]['dingqiInvestmentTotal'] > 0) {
						$this->rs[$i]['diya'] = $this->rs[$i]['dingqiInvestmentTotal'];
					}else {
						$this->rs[$i]['diya'] = '';
					}

					unset ($this->rs[$i]['yuebaoInvestmentTotal']);
					unset ($this->rs[$i]['dingqiInvestmentTotal']);
				}

			}

			// 协议描述信息
			$this->_view->assign('contractid_reamrk', $arr_contractid);
			$this->_view->assign('records',$this->rs);
		}else{
			$this->_view->assign('msg','参数错误');
		}
    }
	/**
	 *
	 * @var \Sooh\DB\Interfaces\All 
	 */
	protected $db;
	protected $rs;

}