<?php
/**
 * class ReportService
 */
//require_once('DataApiConnection.php');
namespace Api\Baidutongji\Base;
use Api\Baidutongji\Base\DataApiConnection;
//use
/**
 * ReportService
 */
class ReportService {
    private $apiUrl;
    private $userName;
    private $token;
    private $uuid;
    private $accountType;
    private $ucid;
    private $st;
    
    /**
     * construct
     * @param string $apiUrl
     * @param string $userName
     * @param string $token
     * @param string $ucid
     * @param string $st
     */
    public function __construct($apiUrl, $userName, $token, $accountType, $uuid, $ucid, $st) {
        $this->apiUrl = $apiUrl;
        $this->userName = $userName;
        $this->token = $token;
        $this->uuid = $uuid;
        $this->accountType = $accountType;
        $this->ucid = $ucid;
        $this->st = $st;
    }
    
    /**
     * get site list
     * @return array
     */
    public function getSiteList() {
        echo '----------------------get site list----------------------' . PHP_EOL;
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getSiteList',$this->uuid, $this->ucid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->accountType,
            ),
            'body' => null,
        );
        $apiConnection->POST($apiConnectionData);
        
        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
    }

    /**
     * get data
     * @param array $parameters
     * @return array
     */
    public function getData($parameters) {
        echo '----------------------get data----------------------' . PHP_EOL;
        $apiConnection = new DataApiConnection();
        $apiConnection->init($this->apiUrl . '/getData', $this->uuid, $this->ucid);

        $apiConnectionData = array(
            'header' => array(
                'username' => $this->userName,
                'password' => $this->st,
                'token' => $this->token,
                'account_type' => $this->accountType,
            ),
            'body' => $parameters,
        );
        $apiConnection->POST($apiConnectionData);
        
        return array(
            'header' => $apiConnection->retHead,
            'body' => $apiConnection->retBody,
            'raw' => $apiConnection->retRaw,
        );
    }
}
