<?php
namespace pixiv;
use GuzzleHttp;
use GuzzleHttp\Exception\RequestException;

class Http{
    public $client;

    public function construct() {
        $jar = new \GuzzleHttp\Cookie\CookieJar();

        $path_parts = pathinfo(__FILE__);
        $path = $path_parts['dirname'].'/ssl/cacert.pem';
        $this->client = new GuzzleHttp\Client(['verify' => $path, 'cookies' => $jar, 'http_errors' => TRUE, 'allow_redirects'=>TRUE]);

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
}