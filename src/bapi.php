<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace pixiv;
use pixiv\Api;
/**
 * Description of bapi
 *
 * @author Administrator
 */
class bapi extends Api{
    //put your code here
    
    public function require_appapi_hosts($hostname='app-api.pixiv.net'){
        $url = "https://1.0.0.1/dns-query?ct=application/dns-json&name=$hostname&type=A&do=false&cd=false";
        $r = $this->guzzle_call('GET', $url);
        $json = json_decode($r->getBody(), TRUE);
        $this->hosts = $json['Answer'][0]['data'];
    }
}
