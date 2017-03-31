<?php
/**
 * 开发测试环境使用的，客户端请跳过
 */
class SpiderController extends \Yaf_Controller_Abstract
{
    public function indexAction() {
        $arr_pwd=['Aa111111'];
        $pwd = $this->_request->get('pwd');
    if (!in_array($pwd, $arr_pwd)) {
        $this->_view->assign('html', '<span style="color: red;">口令错误</span>');
        return;
    }else{
        $this->_view->assign('pwd', $pwd);
    }
        $phone  = $this->_request->get('phone');
        $ticket = $this->_request->get('ticket');
        $btn_searchOne = $this->_request->get('btn_search_one');
        $btn_notGrant = $this->_request->get('btn_not_grant');
        $btn_granted = $this->_request->get('btn_granted');
        $btn_appendTicket = $this->_request->get('btn_append_ticket');
        $db_p2p = \Sooh\DB\Broker::getInstance('produce');
        $db_rpt = \Sooh\DB\Broker::getInstance('default');
// 查询单个用户查询发送电影票状态
        if (!empty($btn_searchOne)) {
            $where = [];
            if (!empty($phone)) {
                $userId = $db_p2p->getOne('phoenix.customer', 'customer_id', ['customer_cellphone'=>$phone]);
                if (empty($userId)) {
                    $html= '用户不存在';
                    $this->_view->assign('html', $html);
                    return;
                }
                $where['userId'] = $userId;
            }

            if (!empty($ticket)){
                $where['ticketSerialNo'] = $ticket;
            }
            if (empty($where)) {
                return;
            }
            $records = $db_rpt->getRecords('db_kkrpt.tb_activity_spider', 'ticketSerialNo,userId,flag_msg', $where);
var_log($records, 'records>>>>>>>');
            if (empty($records)) {
                $str = '找不到记录';
            }else{

                $str = $this->htmlSearchOne($records);

            }
            $this->_view->assign('html', $str);
            $this->_view->assign('phone',$phone);
            $this->_view->assign('ticket',$ticket);
        }

// 查询未发的电影票
        if (!empty($btn_notGrant)) {
            $notGrantTickts = $db_rpt->getCol('db_kkrpt.tb_activity_spider', 'ticketSerialNo', ['flag_msg'=>0]);

            $html = $this->htmlForNotGrant($notGrantTickts);

            $this->_view->assign('html', $html);
        }

// 已经发放的电影票
        if (!empty($btn_granted)) {
            $granted = $db_rpt->getRecords('db_kkrpt.tb_activity_spider', 'ticketSerialNo, userId', ['flag_msg'=>1]);
            $html = $this->htmlForGranted ($granted);
            $this->_view->assign('html', $html);
        }
// 批量加票
        if(!empty($btn_appendTicket)) {
            $append_ticket = $this->_request->get('append_ticket');
            if(empty($append_ticket)) {
                return;
            }
            $tickets = explode("\r\n", $append_ticket);

            $existsTickets = [];
            $addFailedTickets = [];
            $addSuccessTickets = [];
            foreach($tickets as $ticket) {
                $ticket = trim($ticket);
                if (empty($ticket)) {
                    continue;
                }
                $nextUserId = $db_rpt->getOne('db_kkrpt.tb_activity_spider','max(userId)', ['flag_msg'=>0, 'userId<'=>9999999]);
                if (empty($nextUserId)) {
                    $nextUserId = 1000;
                }else{
                    $nextUserId += 1;
                }
                try{
                    \Sooh\DB\Broker::errorMarkSkip();
                    $db_rpt->addRecord('db_kkrpt.tb_activity_spider',['ticketSerialNo'=>$ticket, 'userId'=>$nextUserId]);;
                    $addSuccessTickets[] = $ticket;
                }catch(\ErrorException $e){
                    if (\Sooh\DB\Broker::errorIs($e)) {
                        $existsTickets[] = $ticket;
                    }else {
                        $addFailedTickets[] = $ticket;
                    }
                }
            }

//var_log($addFailedTickets, '添加失败的票：');
//var_log($existsTickets, '已经存在的票：');
//var_log($addSuccessTickets, '添加成功的票：');

            $this->_view->assign('append_ticket', $append_ticket);

            $html = $this->htmlForAppentTicket($addFailedTickets,$existsTickets,$addSuccessTickets);
            $this->_view->assign('html', $html);

        }



    }
    private function htmlForAppentTicket ($addFailedTickets,$existsTickets,$addSuccessTickets) {
        $header = ['电影票序列号','状态'];
        $html = '<table class="gridtable"><thead><tr><th>';
        $html .= implode('</th><th>', $header);
        $html .= '</th></tr></thead>';

        $html .= '<tbody>';
        if (!empty($addFailedTickets)) {
            foreach ($addFailedTickets as $t) {
                $html .= '<tr><td>'.$t.'</td><td style="color:red;">添加失败</td></tr>';
            }
        }

        if (!empty($existsTickets)) {
            foreach ($existsTickets as $t) {
                $html .= '<tr><td>'.$t.'</td><td style="color:#EA7500;">添加失败：此序列号已经存在</td></tr>';
            }
        }
        if(!empty($addSuccessTickets)) {
            foreach ($addSuccessTickets as $t) {
                $html .= '<tr><td>'.$t.'</td><td style="color:green;">添加成功</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }


    private function htmlForNotGrant ($notGrantTickts) {
        $header = ['电影票序列号','状态'];
        $html = '未发放数量：'.sizeof($notGrantTickts);
        $html .= '<table class="gridtable"><thead><tr><th>';
        $html .= implode('</th><th>', $header);
        $html .= '</th></tr></thead><tbody>';
        if (!empty($notGrantTickts)) {
            foreach ($notGrantTickts as $t) {
                $html .= '<tr><td>'.$t.'</td><td style=";">未发放</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }

    private function htmlForGranted ($granted) {
        $header = ['电影票序列号', '用户ID'];
        $html = '已经发放数量：'.sizeof($granted);
        $html .= '<table class="gridtable"><thead><tr><th>';
        $html .= implode('</th><th>', $header);
        $html .= '</th></tr></thead><tbody>';
        if (!empty($granted)){
            foreach ($granted as $r) {
                $html .= '<tr><td>'.implode('</td><td>', $r).'</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }
    private function htmlSearchOne($records) {

        $header = ['电影票序列号号', '用户ID', '状态'];
        $html = '<table class="gridtable"><thead><tr><th>';
        $html .= implode('</th><th>', $header);
        $html .= '</th></tr></thead><tbody>';
        if (!empty($records)){
            foreach ($records as $r) {
                if($r['userId'] < 9999999){
                    $r['userId'] = '';
                }
                $r['flag_msg'] = $r['flag_msg'] ? '已发放':'未发放';
                $html .= '<tr><td>'.implode('</td><td>', $r).'</td></tr>';
            }
        }
        $html .= '</tbody></table>';
        return $html;
    }
}