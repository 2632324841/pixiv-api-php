<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace pixiv;
use pixiv\Api;
/**
 * Description of Ajax
 *
 * @author JC
 */
class Ajax extends Api{
    //put your code here
    protected $headers = [
        'origin'=> 'https://www.pixiv.net',
        'content-type'=> 'application/x-www-form-urlencoded; charset=UTF-8',
        'user-agent'=> 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        'x-requested-with'=> 'XMLHttpRequest',
    ];
    protected $init_config;
    protected $cookies;

    public function set_init($cookie){
        # 设置cookie
        $this->headers['cookie'] = $cookie;
        $url = 'https://www.pixiv.net';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params=[], $data=[]);
        # 处理返回的Json数据
        $html = (string)$r->getBody();
        $temp = substr($html, strpos($html, 'init-config',1) + 41 , strpos($html, '<script') - (strpos($html, 'init-config',1) + 43));
        $json = json_decode($temp, true);
        # 设置配置数据
        $this->init_config = $json;
    }
    
    public function convert_cookie($query)
    {
        $queryParts = explode(';', $query);
        $params = array();
        foreach ($queryParts as $param) {
          $item = explode('=', $param);
          $params[$item[0]] = $item[1];
        }
        return $params;
    }
    
    public function parse_result($req){
        return json_decode((string)$req->getBody(),TRUE);
    }
    
    public function ranking($date=Null, $mode='ranking', $mode_rank='daily', $content_rank='all', $p=1){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> $mode,
            'mode_rank'=> $mode_rank,
            'content_rank'=> $content_rank,
            'P'=> $p,
        ];
        if($date){
            $params['date'] = $date;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function popular_illust($mode='popular_illust', $type=null, $p=1){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> $mode,
            'P'=> $p,
        ];
        if($type){
            $params['type'] = $type;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function recommender_illust_id($mode='all'){
        $url = 'https://www.pixiv.net/touch/ajax/recommender/illust';
        $params = [
            'mode'=> $mode,
        ];
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function illust_details($illust_ids){
        $url = 'https://www.pixiv.net/touch/ajax/illust/details/many';
        if(is_string($illust_ids)){
            $params['illust_ids'] = $illust_ids;
        }
        else if(is_array($illust_ids)){
            $params['illust_ids'] = join(',', $illust_ids);
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function search_illusts($word, $include_meta=1, $mode='safe', $s_mode='s_tag', $p=1, $order=null, $ratio=null, $wlt=null, $wgt=null, $hlt=null, $hgt=null, $scd=null, $ecd=null, $blt=null, $bgt=null){
        $url = 'https://www.pixiv.net/touch/ajax/search/illusts';
        $params = [
            'word'=> $word,
            'mode'=> $mode,
            's_mode'=> $s_mode,
            'include_meta'=> $include_meta,
            'p'=> $p,
            'wlt'=> $wlt,
            'wgt'=> $wgt,
            'hlt'=> $hlt,
            'hgt'=> $hgt,
            'ratio'=> $ratio,
            'scd'=> $scd,
            'ecd'=> $ecd,
            'blt'=> $blt,
            'bgt'=> $bgt,
        ];
        if($order){
            $params['order'] = $order;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function bookmark_new_illust($type='illusts', $include_meta=1 , $tag=null, $p=1){
        $url = 'https://www.pixiv.net/touch/ajax/follow/latest';
        $params = [
            'type'=> $type,
            'include_meta'=> $include_meta,
            'p'=> $p,
        ];
        if($tag){
            $params['tag'] = $tag;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function bookmark_illust($user_id, $type='illust', $tag=null, $p=1){
        $url = 'https://www.pixiv.net/touch/ajax/user/bookmarks';
        $params = [
            'user_id'=> $user_id,
            'type'=> $type,
            'p'=> $p,
        ];
        if($tag){
            $params['tag'] = $tag;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function history($type='illust', $p=1){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $params = [
            'type'=> $type,
            'p'=> $p,
        ];
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return (string)$r->getBody();
    }
    
    public function add_bookmark_illustda($illust_id, $mode='add_bookmark_illust', $restrict=0, $tag=null, $comment=null){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $data = [
            'id'=> $illust_id,
            'mode'=> $mode,
            'restrict'=> $restrict,
            'tag'=> $tag,
            'comment'=> $comment,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return (string)$r->getBody();
    }
    
    public function delete_bookmark_illustda($illust_id, $mode='delete_bookmark_illust', $restrict=0, $tag=null, $comment=null){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $data = [
            'id'=> $illust_id,
            'mode'=> $mode,
            'restrict'=> $restrict,
            'tag'=> $tag,
            'comment'=> $comment,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return (string)$r->getBody();
    }
    
    public function user_status(){
        $url = 'https://www.pixiv.net/touch/ajax/user/self/status';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params=[]);
        return (string)$r->getBody();
    }
    
    public function user_settings(){
        $url = 'https://www.pixiv.net/touch/ajax/settings';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params=[]);
        return (string)$r->getBody();
    }
    
    public function update_age_check($user_x_restrict=0, $mode='set_user_x_restrict'){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $data = [
            'user_x_restrict'=> $user_x_restrict,
            'mode'=> $mode,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return (string)$r->getBody();
    }
}
