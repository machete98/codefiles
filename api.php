<?php
namespace Mayer;

use Bitrix\Main\Loader;
use \CModule;
use \CIBlock;
use \CFile;
use \CIBlockSection;
use \CIBlockElement;

CModule::IncludeModule('iblock');

class Api {

    private $curl;
    private $curl_other;

    public function __construct() {
        $this->curl = curl_init(API_HOST);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->curl, CURLOPT_USERPWD, API_USER . ":" . API_PASSWORD);
    }

    public function getRequest($put) {
        curl_setopt($this->curl, CURLOPT_URL, API_HOST.$put);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->curl);
        return json_decode($result, true);
    }
    
    public function getRequestFile($put, $json) {

        curl_setopt($this->curl, CURLOPT_URL, API_HOST.$put);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
        $result = curl_exec($this->curl);
        return json_decode($result);
    }
    
}

?>
