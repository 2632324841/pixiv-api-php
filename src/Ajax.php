<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace pixiv;
use pixiv\Api;
use voku\helper\HtmlDomParser;
/**
 * Description of Ajax
 *
 * @author JC
 */
class Ajax extends Api{
    //put your code here
    protected $headers = [
        //'origin'=> 'https://www.pixiv.net',
        'user-agent'=> 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        'x-requested-with'=> 'XMLHttpRequest',
    ];
    protected $init_config;
    protected $cookies;
    
    public $init_path = './';
    public $StatusCode;
    public $Headers;
    public $ReasonPhrase;
    public $body;
    public $getContents;
    public $json;
    

    public function set_init($cookie){
        //如果是文件路径
        if(is_file($cookie)){
            $cookie = file_get_contents($cookie);
        }
        
        # 设置cookie
        $this->headers['cookie'] = $cookie;
        
        if(!is_dir($this->init_path))
        {
            mkdir($this->init_path, 0777);
        }
        //保存token
        $init_file = $this->init_path.'init_config.init';
        //判断token是否存在
        if(is_file($init_file)){
            $json = $this->ReadFile($init_file);
            $json = json_decode($json, true);
            if($json == FALSE){
                return 0;
            }
            //判断token是否过期
            if(time() - $json['create_time'] < $this->save_time){
                $this->init_config = $json;
                return 1;
            }
        }
        $url = 'https://www.pixiv.net';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[], $data=[]);
        # 处理返回的Json数据
        $html = (string)$r->getBody();
<<<<<<< HEAD
<<<<<<< Updated upstream
        $temp = substr($html, strpos($html, 'init-config',1) + 41 , strpos($html, '<script') - (strpos($html, 'init-config',1) + 43));
        $json = json_decode($temp, true);
        # 设置配置数据
        $this->init_config = $json;
=======
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        $json = $HtmlDom->find('#init-config',0)->content;
        //$temp = substr($html, strpos($html, 'init-config',1) + 41 , strpos($html, '<script') - (strpos($html, 'init-config',1) + 43));
        $json = json_decode($json, TRUE);
        $json['create_time'] = time();
        $this->WriteFile($init_file, json_encode($json, JSON_UNESCAPED_UNICODE));
        # 设置配置数据
        $this->init_config = $json;
        $this->headers['x-csrf-token'] = $json['pixiv.context.postKey'];
        $this->headers['x-user-id'] = $json['pixiv.user.id'];
        return 1;
>>>>>>> Stashed changes
=======
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        $json = $HtmlDom->find('#init-config',0)->content;
        //$temp = substr($html, strpos($html, 'init-config',1) + 41 , strpos($html, '<script') - (strpos($html, 'init-config',1) + 43));
        $json = json_decode($json, true);
        $json['create_time'] = time();
        $this->WriteFile($init_file, json_encode($json));
        # 设置配置数据
        $this->init_config = $json;
        $this->headers['x-csrf-token'] = $json['pixiv.context.postKey'];
        $this->headers['x-user-id'] = $json['pixiv.user.id'];
        return 1;
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
    }
    
    public function ajax_guzzle_call($method, $url, $headers=[], $params=[], $data=[]){
        /*if($this->request_type == 1){
            $parse_url = parse_url($url);
            $host = $parse_url['host'];
            $json_data = $this->require_appapi_hosts($host);
            $hosts = $json_data['Answer'][3]['data'];
            $headers['Host'] = $host;
            $url = str_replace($host, $hosts, $url);
        }*/
        return $this->guzzle_call($method, $url, $headers, $params, $data, FALSE);
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
    
    
    public function return_json($data = [], $code = 200){
        if(empty($data)){
            $this->json($this->json, $code);
        }else{
            $this->json($data, $code);
        }
    }

    public function ugoira_meta($illust_id){
        $url = "https://www.pixiv.net/ajax/illust/$illust_id/ugoira_meta";
        $r = $this->ajax_guzzle_call('GET', $url);
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
    
<<<<<<< HEAD
<<<<<<< Updated upstream
    public function ranking($date=Null, $mode='ranking', $mode_rank='daily', $content_rank='all', $p=1){
=======
=======
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
    public function illust_details($illust_id, $ref=NULL, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/illust/details';
        $params = [
            'illust_id'=> $illust_id,
            'lang'=> $lang,
        ];
        if($ref){
            $params['ref'] = $ref;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function user_illusts($user_id, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/illust/user_illusts';
        $params = [
            'illust_id'=> $user_id,
            'lang'=> $lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function illust_comment_roots($illust_id, $limit=3, $offset=0, $lang='zh'){
        $url = 'https://www.pixiv.net/ajax/illusts/comments/roots';
        $params = [
            'illust_id'=> $illust_id,
            'limit'=> $limit,
            'offset'=> $offset,
            'lang'=> $lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function illust_comment($illust_id, $page=1, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/comment/illust';
        $params = [
            'work_id'=> $illust_id,
            'page'=> $page,
            'lang'=> $lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    /*
     * date 20190926
     * mode ranking
     * mode_rank daily 天 weekly 周 monthly 月 rookie 新人 original 原创 male 受男性欢迎 female 受女性欢迎 
     * content_rank all 全部 illust 插图 ugoira 动图 manga 漫画 
     */
    public function ranking($date=Null, $mode='ranking', $mode_rank='daily', $content_rank='all', $p=1 ,$lang='zh'){
<<<<<<< HEAD
>>>>>>> Stashed changes
=======
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> $mode,
            'mode_rank'=> $mode_rank,
            'content_rank'=> $content_rank,
            'P'=> $p,
            'lang'=> $lang
        ];
        if($date){
            $params['date'] = $date;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function popular_illust($type=null, $p=1, $mode='popular_illust', $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> $mode,
            'P'=> $p,
            'lang'=> $lang
        ];
        if($type){
            $params['type'] = $type;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    # mod = all or safe or r18
    public function recommender_illust_id($mode='all', $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/recommender/illust';
        $params = [
            'mode'=> $mode,
            'lang'=> $lang
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function illust_details_many($illust_ids){
        $url = 'https://www.pixiv.net/touch/ajax/illust/details/many';
        if(is_string($illust_ids)){
            $params['illust_ids[]'] = $illust_ids;
        }
        else if(is_array($illust_ids)){
            $params['illust_ids[]'] = join(',', $illust_ids);
        }
        //由于 http_build_query原因 会在数组设置下标
        $url = $url.$this->build_query($params);
        $r = $this->no_auth_guzzle_call('GET', $url, $this->headers, $params=null);
        return $this->parse_result($r);
    }
    
    # $include_meta=1, $mode='safe', $s_mode='s_tag', $p=1, $order=null, $ratio=null, $wlt=null, $wgt=null, $hlt=null, $hgt=null, $scd=null, $ecd=null, $blt=null, $bgt=null, $tool=null
    /*
     * s_mode = ['s_tag_full','s_tc','s_tag',null]; 标签完全一致 标题说明文字  标签
     * type = ['illust','manga','ugoira',null]; 插图 漫画 动图
     * order popular_d 受全站欢迎 popular_male_d 受男性欢迎 popular_female_d 受女性欢迎 date 按旧排序 date_d 按新排序
     * wlt 最低宽度 px
     * wgt 最大宽度 px
     * hlt 最低高度 px
     * hgt 最大高度 px
     * ratio 0.5 横图 -0.5 纵图 0 正方形图 null 默认
     * tool SAI Photoshop 等等制图工具
     * blt 最小收藏数
     * bgt 最大收藏数
     * scd 开始时间
     * ecd 结尾时间
     * mode r18 xxx safe r15 普通
     */
    public function search_illusts($word, $data){
        $url = 'https://www.pixiv.net/touch/ajax/search/illusts';
        $params = [
            'word'=> $word,
            'mode'=> $this->params($data, 'mode', 'safe'),
            's_mode'=> $this->params($data, 's_mode', 's_tag'),
            'include_meta'=> $this->params($data, 'include_meta', 0),
            'order'=> $this->params($data, 'order','date_d'),
            'type'=> $this->params($data, 'type'),
            'p'=> $this->params($data, 'p', 1),
            'wlt'=> $this->params($data, 'wlt'),
            'wgt'=> $this->params($data, 'wgt'),
            'hlt'=> $this->params($data, 'hlt'),
            'hgt'=> $this->params($data, 'hgt'),
            'ratio'=> $this->params($data, 'ratio'),
            'scd'=> $this->params($data, 'scd'),
            'ecd'=> $this->params($data, 'ecd'),
            'blt'=> $this->params($data, 'blt'),
            'bgt'=> $this->params($data, 'bgt'),
            'tool'=> $this->params($data, 'tool'),
            'p'=> $this->params($data, 'p', 1),
            'lang'=> $this->params($data, 'lang', 'zh'),
        ];
<<<<<<< HEAD
<<<<<<< Updated upstream
        if($order){
            $params['order'] = $order;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function bookmark_new_illust($type='illusts', $include_meta=1 , $tag=null, $p=1){
=======
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
=======
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
    # $mode='safe', $s_mode='s_tag', $p=1, $order=null, $ratio=null, $wlt=null, $wgt=null, $hlt=null, $hgt=null, $scd=null, $ecd=null, $blt=null, $bgt=null, $tool=null
    /*
     * s_mode = ['s_tag_full','s_tc','s_tag',null]; 标签完全一致 标题说明文字  标签
     * type = ['illust','manga','ugoira',null]; 插图 漫画 动图
     * order popular_d 受全站欢迎 popular_male_d 受男性欢迎 popular_female_d 受女性欢迎 date 按旧排序 date_d 按新排序
     * wlt 最低宽度 px
     * wgt 最大宽度 px
     * hlt 最低高度 px
     * hgt 最大高度 px
     * ratio 0.5 横图 -0.5 纵图 0 正方形图 null 默认
     * tool SAI Photoshop 等等制图工具
     * blt 最小收藏数
     * bgt 最大收藏数
     * scd 开始时间
     * ecd 结尾时间
     * mode r18 xxx safe r15 普通
     */
    public function search_illusts_pc($word, $data=[]){
        $url = 'https://www.pixiv.net/search.php';
        $params = [
            'word'=> $word,
            'mode'=> $this->params($data, 'mode', 'safe'),
            's_mode'=> $this->params($data, 's_mode', 's_tag'),
            'order'=> $this->params($data, 'order','date_d'),
            'type'=> $this->params($data, 'type'),
            'p'=> $this->params($data, 'p', 1),
            'wlt'=> $this->params($data, 'wlt'),
            'wgt'=> $this->params($data, 'wgt'),
            'hlt'=> $this->params($data, 'hlt'),
            'hgt'=> $this->params($data, 'hgt'),
            'ratio'=> $this->params($data, 'ratio'),
            'scd'=> $this->params($data, 'scd'),
            'ecd'=> $this->params($data, 'ecd'),
            'blt'=> $this->params($data, 'blt'),
            'bgt'=> $this->params($data, 'bgt'),
            'tool'=> $this->params($data, 'tool'),
            'p'=> $this->params($data, 'p', 1),
            'lang'=> $this->params($data, 'lang', 'zh'),
        ];
        $headers = $this->headers;
        $headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36';
        $r = $this->ajax_guzzle_call('GET', $url, $headers, $params);
        $html = (string)$r->getBody();
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        
        //转义json "
        $data_items = $HtmlDom->find('#js-mount-point-search-result-list',0)->getAttribute('data-items');
        $data_items = str_replace('&quot;', '"', $data_items);
        $data_items = json_decode($data_items,TRUE);
        
        $data_related_tags = $HtmlDom->find('#js-mount-point-search-result-list',0)->getAttribute('data-related-tags');
        $data_related_tags = str_replace('&quot;', '"', $data_related_tags);
        $data_related_tags = json_decode($data_related_tags,TRUE);
        
        $data_tag = $HtmlDom->find('#js-mount-point-search-result-list',0)->getAttribute('data-tag');
        $data_tag = str_replace('&quot;', '"', $data_tag);
        $data_tag = json_decode($data_tag,TRUE);

        $json = [
            'data_items'=> $data_items,
            'data_related_tags'=> $data_related_tags,
            'data_tag'=> $data_tag,
        ];
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = $json;
        return $this;
    }
    
    public function params($data, $key, $value=''){
        if(array_key_exists($key, $data)){
            return $data[$key];
        }else{
            return $value;
        }
    }
    
    public function build_query($query_data){
        $value = '?';
        if(is_array($query_data)){
            foreach ($query_data as $key=>$val){
                if(is_array($val))
                {
                    foreach ($val as $k=>$v){
                        $value = $value.$key.'='.$v.'&';
                    }
                }
                else
                {
                    $value = $value.$key.'='.$val.'&';
                }
            }
        }
        else
        {
            return '';
        }
        $value = substr($value, 0, strlen($value)-1);
        return $value;
    }
    
    # $a['tool'] = 'sal'; 参数数组  键  默认值
    /*public function params($data, $key, $value=NULL){
        if(array_key_exists($key, $data)){
            return $data;
        }else if($value != NULL){
            $data[$key] = $value;
            return $data;
        }else{
            unset($data[$key]);
            return $data;
        }
    }*/

    public function bookmark_new_illust($type='illusts', $include_meta=1 , $tag=null, $p=1, $lang='zh'){
<<<<<<< HEAD
>>>>>>> Stashed changes
=======
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
        $url = 'https://www.pixiv.net/touch/ajax/follow/latest';
        $params = [
            'type'=> $type,
            'include_meta'=> $include_meta,
            'p'=> $p,
            'lang'=> $lang
        ];
        if($tag){
            $params['tag'] = $tag;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function bookmark_illust($user_id, $type='illust', $tag=null, $p=1, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/user/bookmarks';
        $params = [
            'user_id'=> $user_id,
            'type'=> $type,
            'p'=> $p,
            'lang'=> $lang
        ];
        if($tag){
            $params['tag'] = $tag;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function history($type='illust', $p=1, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/history';
        $params = [
            'type'=> $type,
            'p'=> $p,
            'lang'=> $lang
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    public function add_bookmark_illustda($illust_id, $mode='add_bookmark_illust', $restrict=0, $tag=null, $comment=null){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $data = [
            'id'=> $illust_id,
            'mode'=> $mode,
            'restrict'=> $restrict,
            'tag'=> $tag,
            'comment'=> $comment,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return $this->parse_result($r);
    }
    
    public function delete_bookmark_illustda($illust_id, $mode='delete_bookmark_illust', $restrict=0, $tag=null, $comment=null){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $data = [
            'id'=> $illust_id,
            'mode'=> $mode,
            'restrict'=> $restrict,
            'tag'=> $tag,
            'comment'=> $comment,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);
        return $this->parse_result($r);
    }
    
    public function user_status(){
        $url = 'https://www.pixiv.net/touch/ajax/user/self/status';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    public function user_settings(){
        $url = 'https://www.pixiv.net/touch/ajax/settings';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    public function update_age_check($user_x_restrict=0, $mode='set_user_x_restrict'){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $data = [
            'user_x_restrict'=> $user_x_restrict,
            'mode'=> $mode,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);
<<<<<<< HEAD
        return $this->parse_result($r);
    }
<<<<<<< Updated upstream
=======
    
    public function tags(){
        $url = 'https://www.pixiv.net/tags.php';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        $html = (string)$r->getBody();
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        $tag_list = $HtmlDom->find('.tags-list',0);
        $tags = [];
        foreach ($tag_list as $key=>$val){
            $tags[$key]['tag'] = $val->find('.tag',0)->plaintext;
            $tags[$key]['count'] = $val->find('.count',0)->plaintext;
        }
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = $tags;
        return $this;
    }
    
    //动态 具体去P站页面看用法 https://www.pixiv.net/stacc/?mode=unify
    public function all_activity(){
       $unify_config = $this->unify_config();
       $url = 'https://www.pixiv.net/stacc/my/home/all/touch_nottext/'.$unify_config['pixiv.context.nextId'].'/js';
       //pixiv.context.nextId
       $params = [
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    private function unify_config(){
         //保存token
        $init_file = $this->init_path.'unify_config.init';
        //判断token是否存在
        if(is_file($init_file)){
            $json = $this->ReadFile($init_file);
            $json = json_decode($json, TRUE);
            if($json == FALSE){
                return 0;
            }
            //判断token是否过期
            if(time() - $json['create_time'] < $this->save_time){
                $this->unify_config = $json;
                return $json;
            }
        }
        $api = 'https://www.pixiv.net/stacc/?mode=unify';
        $r = $this->ajax_guzzle_call('GET', $api, $this->headers, $params=[]);
        $html = (string)$r->getBody();
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        //init-config-input
        $init_config = $HtmlDom->find('#init-config-input',0)->value;
        $this->unify_config = json_decode($init_config, TRUE);
        $this->unify_config['create_time'] = time();
        $this->WriteFile($init_file, json_encode($this->unify_config, JSON_UNESCAPED_UNICODE));
        return $this->unify_config;
    }
>>>>>>> Stashed changes
=======
        return $this->parse_result($r);
    }
    
    public function tags(){
        $url = 'https://www.pixiv.net/tags.php';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        $html = (string)$r->getBody();
        $HtmlDom = new HtmlDomParser();
        $HtmlDom->load($html);
        $tag_list = $HtmlDom->find('.tags-list',0);
        $tags = [];
        foreach ($tag_list as $key=>$val){
            $tags[$key]['tag'] = $val->find('.tag',0)->plaintext;
            $tags[$key]['count'] = $val->find('.count',0)->plaintext;
        }
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = $tags;
        return $this;
    }
>>>>>>> 9206acc217e47b48a8f807405ed66b3c3022c44d
}
