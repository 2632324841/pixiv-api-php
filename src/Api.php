<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace pixiv;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;

/**
 * Description of Api
 *
 * @author JC
 */
class Api {
    protected $client_id = 'KzEZED7aC0vird8jWyHM38mXjNTY';
    protected $client_secret = 'W9JZoJe00qPvJsiyCGT3CCtC6ZUtdpKpzMbNlUGP';
    protected $hash_secret = '28c1fdd170a5204386cb1313c7077b34f83e4aaf4aa829ce78c231e05b0bae2c';
    protected $access_token;
    protected $refresh_token;
    protected $user_id = 0;
    protected $save_time = 3600;
    protected $client;
    public function __construct() {
        $jar = new \GuzzleHttp\Cookie\CookieJar();
        $this->client = new GuzzleHttp\Client(['verify' => false,'cookies' => $jar,'http_errors' => false]);
    }
    
    public function auth($username=NULL, $password=NULL, $refresh_token=NULL){
        //保存token
        $token_file = $username.'.token';
        //判断token是否存在
        if(is_file($token_file)){
            $json = $this->ReadFile($token_file);
            $json = json_decode($json,true);
            if($json == FALSE){
                return 0;
            }
            //判断token是否过期
            if(time() - $json['create_time'] < $this->save_time){
                $this->user_id = $json['response']['user']['id'];
                $this->access_token = $json['response']['access_token'];
                $this->refresh_token = $json['response']['refresh_token'];
                return 1;
            }
        }
        //获取token
        $url = 'https://oauth.secure.pixiv.net/auth/token';
        $headers = [
            'App-OS'=> 'ios',
            'Accept-Language'=> 'en-us',
            'App-OS-Version'=> '12.0.1',
            'App-Version'=> '7.6.2',
            'User-Agent'=> 'PixivIOSApp/7.6.2 (iOS 12.0.1; iPhone8,2)',
            'X-Client-Time'=> time(),
            'X-Client-Hash'=> md5(time().$this->hash_secret),
        ];
        $data = [
            'get_secure_url'=>1,
            'include_policy'=>1,
            'client_id'=> $this->client_id,
            'client_secret'=> $this->client_secret,
        ];
        if($username != NULL && $password != NULL)
        {
            $data['grant_type'] = 'password';
            $data['username'] = $username;
            $data['password'] = $password;
        }
        else if($refresh_token != NULL || $this->refresh_token != NULL)
        {
            $data['grant_type'] = 'refresh_token';
            if($refresh_token)
            {
                $data['refresh_token'] = $refresh_token;
            }
            else if($this->refresh_token)
            {
                $data['refresh_token'] = $this->refresh_token;
            }
        }
        try {
            $response = $this->guzzle_call('POST', $url, $headers, $params=[], $data);
        
            if($response->getStatusCode() == 200)
            {
                $json = json_decode((string)$response->getBody(),true);
                $this->user_id = $json['response']['user']['id'];
                $this->access_token = $json['response']['access_token'];
                $this->refresh_token = $json['response']['refresh_token'];
                $json['create_time'] = time();
                $this->WriteFile($token_file, json_encode($json));
                return 1;
            }
            $re = json_decode((string)$response->getBody(),TRUE);
            if($re['has_error']){
                exit($re['errors']['system']['message']);
            }
        } catch (RequestException $e) {
            exit($e->getMessage());
        }
    }
    
    public function set_auth($access_token,$refresh_token=NULL){
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
    }

    public function set_client($client_id,$client_secret){
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
    }

    
    public function login($username,$password){
        return $this->auth($username,$password);
    }
    
    public function json($json, $code = 200){
        if(is_array($json)){
            header('Content-Type:application/json; charset=utf-8');
            http_response_code($code);
            exit(json_encode($json,JSON_UNESCAPED_UNICODE));
        }
        return FALSE;
    }
    
    public function download($url, $prefix = '', $path = 'image', $name = null, $replace = False, $referer = 'https://app-api.pixiv.net/'){
        
        try{
            $fileName = substr($url, strrpos($url, '/')+1);
            $saveFilePath = $path.'/'.$fileName;
            if(is_file($saveFilePath)){
                return 1;
            }
            $response = $this->client->get($url,['headers'=>[ 'Referer' => $referer ],'save_to'=>$saveFilePath]);
            if($response->getStatusCode() == 200){
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    public function guzzle_call($method, $url, $headers=[], $params=[], $data=[], $json=[], $timeout=10){
        $client = $this->client;
        if($method == 'GET')
        {
            $options = [
                'query' => $params,
                'timeout'=>$timeout,
                'headers'=>$headers,
            ];
            if(!$params){
                unset($options['query']);
            }
            $response = $client->request($method, $url, $options);
        }
        else if($method == 'POST'){
            $options = [
                'query' => $params,
                'form_params' => $data,
                'timeout'=>$timeout,
                'headers'=>$headers,
            ];
            if(!$params){
                unset($options['query']);
            }
            $response = $client->request($method, $url, $options);
        }
        else if($method == 'PUT'){
            $options = [
                'query' => $params,
                'form_params' => $data,
                'json' => $json,
                'timeout'=>$timeout,
                'headers'=>$headers,
            ];
            if(!$params){
                unset($options['query']);
            }
            $response = $client->request($method, $url, $options);
        }
        else if($method == 'DELETE'){
            $options = [
                'query' => $params,
                'form_params' => $data,
                'timeout'=>$timeout,
                'headers'=>$headers,
            ];
            if(!$params){
                unset($options['query']);
            }
            $response = $client->request($method, $url, $options);
        }
        else
        {
            return FALSE;
        }
        return $response;
    }
    
    public function require_auth(){
        if($this->access_token == NULL){
            return null;
        }
        return true;
    }
	
	# 读取文件
    private function ReadFile($file)
    {
        try{
            $myfile = fopen($file, "r");
            $centent= fread($myfile,filesize($file));
            fclose($myfile);
            return $centent;
        } catch (Exception $ex) {
            return FALSE;
        }
    }
    
	# 写入文件
    private function WriteFile($file, $centent, $type="w")
    {
        try{
            $File = fopen($file, $type);
            fwrite($File, $centent);
            fclose($File);
            return TRUE;
        } catch (Exception $ex) {
            return FALSE;
        }
    }
}