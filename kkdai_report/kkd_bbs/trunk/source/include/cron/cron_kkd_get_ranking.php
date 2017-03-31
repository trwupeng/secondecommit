<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$discuz               = C::app();
$discuz->init_cron    = false;
$discuz->init_session = false;
$discuz->init();

list($year, $month) = explode('-', date('Y-m', strtotime('-1 month')));

getInvestmentRank($year, $month);
getInviteRank($year, $month);


function getInvestmentRank($year, $month)
{
    $link = C::app()->config['kkd']['url'] . C::app()->config['kkd']['investment']['url'];
    $investmentUrl = $link . '?year=' . $year . '&month=' . $month;
    error_log('getInvestmentRank url:' . $investmentUrl);
    $investmentRank = file_get_contents($investmentUrl);
    if ($investmentRank) {
        $investmentRank = json_decode($investmentRank, true);
        if ($investmentRank['code'] == 0) {
            require_once libfile('function/cache');
            writetocache('kkdRankInvestment', getcachevars(['kkdRankInvestment' => $investmentRank['data']]));
        }
    } else {
        error_log('getInvestmentRank is null');
    }
}

function getInviteRank($year, $month)
{
    $link = C::app()->config['kkd']['url'] . C::app()->config['kkd']['invite']['url'];
    $inviteUrl = $link . '?year=' . $year . '&month=' . $month;
    error_log('getInviteRank url:' . $inviteUrl);
    $inviteRank = file_get_contents($inviteUrl);
    if ($inviteRank) {
        $inviteRank = json_decode($inviteRank, true);
        if ($inviteRank['code'] == 0) {
            require_once libfile('function/cache');
            writetocache('kkdRankInvite', getcachevars(['kkdRankInvite' => $inviteRank['data']]));
        }
    } else {
        error_log("getInviteRank is null");
    }
}