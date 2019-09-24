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
        'user-agent'=> 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        'x-requested-with'=> 'XMLHttpRequest',
    ];
    protected $init_config;
    protected $cookies;
    
    public $StatusCode;
    public $Headers;
    public $ReasonPhrase;
    public $body;
    public $getContents;
    public $json;

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
    
    # 处理返回的数据
    public function parse_result($req){
        $this->StatusCode = $req->getStatusCode();
        $this->Headers = $req->getHeaders();
        $this->ReasonPhrase = $req->getReasonPhrase();
        $this->body = $req->getBody();
        $this->getContents = (string)$req->getBody();
        $this->json = json_decode((string)$req->getBody(),TRUE);
        return $this;
    }
    
    public function ugoira_meta($illust_id){
        $url = "https://www.pixiv.net/ajax/illust/$illust_id/ugoira_meta";
        $r = $this->guzzle_call('GET', $url);
        return $this->parse_result($r);
    }
    
    # tpye = ['originalSrc','src']
    public function ugoira_meta_save($illust_id, $savePath='image/', $fileName='', $tpye='originalSrc', $is_save=True){
        //临时文件目录
        $tempPath = 'temp/';
        //zip目录
        $zipPath = 'zip/';
        $json = $this->ugoira_meta($illust_id)->json;
        if($json['error']){
            return FALSE;
        }
        $savePath = iconv('utf-8', 'gbk', $savePath);
        if(empty($fileName)){
            $fileName = $illust_id.'.gif';
        }else{
            $fileName = iconv('utf-8', 'gbk', $savePath);
        }
        if(!is_dir($savePath)){
            mkdir($savePath, 0777);
        }
        $body = $json['body'];
        //获取zip文件名
        $zipFile = substr($body[$tpye], strrpos($body[$tpye], '/')+1);
        //下载zip
        $is_ok = $this->download($body[$tpye], $zipPath, $zipFile);
        if($is_ok){
            //解压
            $this->decompression($zipPath.$zipFile, $tempPath);
            $frames = [];
            $delay = [];
            foreach ($body['frames'] as $val){
                $frames[] = $tempPath.$val['file'];
                //好像P站的更准确
                $delay[] = $val['delay']/10;
            }
            //创建GIF
            if($this->create_gif($frames, $delay, $savePath.$fileName)){
                //删除临时文件
                foreach ($frames as $val){
                    unlink($val);
                }
                //删除zip文件
                if($is_save){
                    unlink($zipPath.$zipFile);
                }
                return TRUE;
            }else{
                return FALSE;
            }
        }
        else{
            return FALSE;
        }
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
        return $this->parse_result($r);
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
        return $this->parse_result($r);
    }
    
    public function recommender_illust_id($mode='all'){
        $url = 'https://www.pixiv.net/touch/ajax/recommender/illust';
        $params = [
            'mode'=> $mode,
        ];
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
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
        return $this->parse_result($r);
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
        return $this->parse_result($r);
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
        return $this->parse_result($r);
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
        return $this->parse_result($r);
    }
    
    public function history($type='illust', $p=1){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $params = [
            'type'=> $type,
            'p'=> $p,
        ];
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
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
        return $this->parse_result($r);
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
        return $this->parse_result($r);
    }
    
    public function user_status(){
        $url = 'https://www.pixiv.net/touch/ajax/user/self/status';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    public function user_settings(){
        $url = 'https://www.pixiv.net/touch/ajax/settings';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    public function update_age_check($user_x_restrict=0, $mode='set_user_x_restrict'){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $data = [
            'user_x_restrict'=> $user_x_restrict,
            'mode'=> $mode,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return $this->parse_result($r);
    }
}
