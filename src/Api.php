<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace pixiv;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;
use GifCreator\GifCreator;

/**
 * Description of Api
 *
 * @author JC
 */
class Api {
    protected $client_id = 'MOBrBDS8blbauoSck0ZfDbtuzpyT';
    protected $client_secret = 'lsACyCD94FhDUtGTXi3QzcFE2uU1hqtDaKeqrdwj';
    protected $hash_secret = '28c1fdd170a5204386cb1313c7077b34f83e4aaf4aa829ce78c231e05b0bae2c';
    protected $device_token = '416eeaafe17577e471b35d2cee7cdfdc';
    protected $access_token;
    protected $refresh_token;
    protected $user_id = 0;
    protected $save_time = 3600;
    protected $client;

    protected $token_path = './';
    protected $lang;
    protected $parse_url;
    //public $hosts = 'https://app-api.pixiv.net';
    public $request_type;
    public $request_path = './';


    public function __construct($username='',$password='', $request_type=0, $lang='zh-cn', $token_path='./') {
        $jar = new \GuzzleHttp\Cookie\CookieJar();

        $this->client = new GuzzleHttp\Client(['verify' => FALSE, 'cookies' => $jar, 'http_errors' => FALSE, 'allow_redirects'=>TRUE]);
        $this->token_path = $token_path;
        $this->lang = $lang;
        $this->request_type = $request_type;
        /*if($this->request_type == 1){
            $ip = $this->require_appapi_hosts('oauth.secure.pixiv.net');
            //$ip = $json['Answer'][1]['data'];
            //$this->hosts = 'https://'.$ip;
        }*/
        if(!empty($username) && !empty($password)){
            $this->auth($username, $password);
        }


    }
    
    public function auth($username=NULL, $password=NULL, $refresh_token=NULL){
        if(!is_dir($this->token_path))
        {
            mkdir($this->token_path, 0777);
        }
        //保存token
        $token_file = $this->token_path.$username.'.token';
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
        $time = gmdate('Y-m-dTH:i:s+00:00');
        $time = str_replace('GM', '', $time);
        $headers = [
            'Accept-Language'=> $this->lang,
            'User-Agent'=> 'PixivAndroidApp/5.0.64 (Android 6.0)',
            'X-Client-Time'=> $time,
            'X-Client-Hash'=> md5($time.$this->hash_secret),
        ];
        $url = "https://oauth.secure.pixiv.net/auth/token";
        $data = [
            'get_secure_url'=>1,
            //'include_policy'=>1,
            'client_id'=> $this->client_id,
            'client_secret'=> $this->client_secret,
        ];
        if($this->request_type == 1){
            $parse_url = parse_url($url);
            $host = $parse_url['host'];
            //$json_data = $this->require_appapi_hosts($host);
            //$hosts = $json_data['Answer'][0]['data'];
            $hosts = $this->require_appapi_hosts($host);
            $headers['Host'] = $host;
            $url = str_replace($host, $hosts, $url);
        }
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
        else
        {
            exit('username or password can not be empty');
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
            exit((string)$response->getBody());
            if($re['has_error']){
                exit('Error：'.$re['errors']['system']['message']);
            }
        } catch (RequestException $e) {
            exit($e->getMessage());
        }
    }
    
    public function require_appapi_hosts($hostname='app-api.pixiv.net'){
        $fileName = $this->request_path.'hosts.json';
        if(is_file($fileName)){
            $json = $this->ReadFile($fileName);
            $data = json_decode($json, TRUE);
            //如果存在
            if(array_key_exists($hostname, $data)){
                $min = 0;
                $max = count($data[$hostname])-1;
                if($max > 0){
                    return $data[$hostname][mt_rand($min, $max)]['data'];
                }else{
                    return $data[$hostname][0]['data'];
                }
            }
        }
        $url = "https://1.0.0.1/dns-query?ct=application/dns-json&name=$hostname&type=A&do=false&cd=false";
        $r = $this->guzzle_call('GET', $url);
        $json = json_decode($r->getBody(), TRUE);
        $data[$hostname] = $json['Answer'];
        $this->WriteFile($fileName, json_encode($data, JSON_UNESCAPED_UNICODE));
        $min = 0;
        $max = count($json['Answer'])-1;
        if($max > 0){
            return $json['Answer'][mt_rand($min, $max)]['data'];
        }else{
            return $json['Answer'][0]['data'];
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

    
    public function login($username, $password){
        return $this->auth($username, $password);
    }
    
    public function json($json, $code = 200){
        if(is_array($json)){
            header('Content-Type:application/json; charset=utf-8');
            http_response_code($code);
            exit(json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        return FALSE;
    }
    
    public function download($url, $path = 'image/', $fileName = '', $headers = [ 'Referer' => 'https://app-api.pixiv.net/' ]){
        try{
            $path = iconv('utf-8', 'gbk', $path);
            if(empty($fileName)){
                $fileName = substr($url, strrpos($url, '/')+1);
            }
            else{
                $fileName = iconv('utf-8', 'gbk', $fileName);
            }
            if(!is_dir($path)){
                mkdir($path, 0777);
            }
            $saveFilePath = $path.$fileName;
            if(is_file($saveFilePath)){
                return 1;
            }
            $response = $this->client->get($url, ['headers'=> $headers,'save_to'=> $saveFilePath]);
            $code = $response->getStatusCode();
            if($code <=206){
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
    
    public function decompression($filePath, $savePath){
        $filePath = iconv('utf-8', 'gbk', $filePath);
        $savePath = iconv('utf-8', 'gbk', $savePath);
        if(!is_file($filePath)){
            return FALSE;
        }
        if(!is_dir($savePath)){
            mkdir($savePath, 0777);
        }
        $zip = new \ZipArchive();
        if($zip->open($filePath )=== TRUE){ 
            $zip->extractTo($savePath);
            $zip->close();
        }
        else{
            FALSE;
        }
    }

    public function create_gif($frames, $delay, $filePath=NULL){
        try{
            $gc = new GifCreator();
            $gifBinary = $gc->create($frames, $delay);

            if(!empty($filePath)){
                file_put_contents($filePath, $gifBinary);
                return TRUE;
            }else{
                return $gifBinary;
            }
            
        } catch (\Exception $ex) {
            return FALSE;
        }
    }

    public function guzzle_call($method, $url, $headers=[], $params=[], $data=[], $allow_redirects=True, $json=[], $timeout=10){

        $client = $this->client;
        if($method == 'GET')
        {
            $options = [
                'query' => $params,
                'timeout'=>$timeout,
                'headers'=>$headers,
                'allow_redirects'=>$allow_redirects,
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
                'allow_redirects'=>$allow_redirects,
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
                'allow_redirects'=>$allow_redirects,
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
                'allow_redirects'=>$allow_redirects,
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
    public function ReadFile($file)
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
    public function WriteFile($file, $centent, $type="w")
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