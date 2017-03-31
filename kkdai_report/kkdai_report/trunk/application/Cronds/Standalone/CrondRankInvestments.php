<?php
namespace PrjCronds;
/**
 *
 * php /var/www/licai_php/public/crond.php "__crond/run&task=Standalone.CrondRankInvestments&ymdh=20150819"
 *
 * 投资拍排行榜
 * Created by PhpStorm.
 * User: li.lianqi
 * Date: 2016/1/28 0028
 * Time: 上午 11:18
 */

class CrondRankInvestments extends \Rpt\Misc\DataCrondGather {
    protected $db_rpt;
    public function init() {
        parent::init();
        $this->toBeContinue = false;
        $this->_iissStartAfter = 2800;
        $this->db_rpt = \Sooh\DB\Broker::getInstance(\Rpt\Tbname::db_rpt);
    }

    public function free() {
        $this->db_rpt = null;
        parent::free();
    }

    public function gather()
    {
        $dt = time();
        $key = 'O2eWC5ExmwL47Ku8MRBcq35kvhAGRVDiZQvak8z';
        $type_investment = 1; // 投资
        $type_income = 4;  // 收益

        $url = 'http://bbs.kuaikuaidai.com/liang/api.php?cmd=rank&dt='.$dt.'&sign='.md5($key.$dt);

        $rankOFInvestments = $this->rankOfInvestments();
        if (!empty($rankOFInvestments)) {
            foreach ($rankOFInvestments as $k => $r) {
                $rankOFInvestments[$k]['type'] = $type_investment;
                $rankOFInvestments[$k]['realname'] = substr_replace($r['realname'], '*', 3,3);
                $rankOFInvestments[$k]['phone'] = substr_replace($r['phone'], '****', 3, 4);
            }
        }

//var_log($rankOFInvestments, '$rankOFInvestments》》》》》》》》》》');
        $rankOfIncome = $this->rankOfIncome();
        if (!empty($rankOfIncome)) {
            foreach ($rankOfIncome as $k => $r) {
                $rankOfIncome[$k]['type'] = $type_income;
                $rankOfIncome[$k]['realname'] = substr_replace($r['realname'], '*', 3,3);
                $rankOfIncome[$k]['phone'] = substr_replace($r['phone'], '****', 3, 4);
            }
        }

        $rank = array_merge($rankOfIncome, $rankOFInvestments);
//var_log($rank, 'rank>>>>>>>>>>>>>');
        if (!empty($rank)) {
            $post_data  = [
                'data'=>json_encode($rank),
            ];

            $result = \Prj\Misc\Funcs::curl_post($url, $post_data, 5);
//var_log($result, '$result>>>>>>>>');
        }

        return true;
    }

    // 投资排行榜
    private function rankOfInvestments () {

        $sql = 'select tb_user_final.userId,tb_user_final.phone, tb_user_final.realname, sum(amount) as amount '
              .'from tb_orders_final left JOIN tb_user_final '
              .'on  tb_user_final.userId = tb_orders_final.userId '
              .'group by userId '
              .'HAVING tb_user_final.userId not in (select userId from tb_user_final where tb_user_final.flagUser = 1) '
              .'order by amount desc limit 10';

        $result = $this->db_rpt->execCustom(['sql'=>$sql]);
        $rs = $this->db_rpt->fetchAssocThenFree($result);
        return $rs;
    }

    private function rankOfIncome() {


        $sql = 'select tb_user_final.phone, tb_user_final.realname, tb_yuebao_out.userId, sum(tb_yuebao_out.amount) + tb_user_final.bid_income + tb_user_final.total_income as amount '
              .'from tb_yuebao_out '
              .'left join tb_user_final on tb_user_final.userId=tb_yuebao_out.userId '
              .'where tb_yuebao_out.type=2 and tb_user_final.flagUser<>1 '
              .'group by tb_yuebao_out.userId '
              .'order by amount desc '
              .'limit 10';

        $result = $this->db_rpt->execCustom(['sql'=>$sql]);
        $rs = $this->db_rpt->fetchAssocThenFree($result);
        return $rs;
    }

}

