<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace pixiv;
use pixiv\Api;
use QL\QueryList;
/**
 * Description of Ajax
 *
 * @author JC
 */
class Ajax extends Api{
    //put your code here
    protected $headers = [
        //'origin'=> 'https://www.pixiv.net',
        'user-agent'=> 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36',
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
        $this->user_agent('Android');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[], $data=[]);
        # 处理返回的Json数据
        $html = (string)$r->getBody();

        /*$temp = substr($html, strpos($html, 'init-config',1) + 41 , strpos($html, '<script') - (strpos($html, 'init-config',1) + 43));
        $json = json_decode($temp, true);*/
        # 设置配置数据
        $HtmlDom = QueryList::html($html);
        $json = $HtmlDom->find('#init-config')->content;
        $json = json_decode($json, TRUE);
        $json['create_time'] = time();
        $this->WriteFile($init_file, json_encode($json, JSON_UNESCAPED_UNICODE));
        # 设置配置数据
        $this->init_config = $json;
        $this->headers['x-csrf-token'] = $json['pixiv.context.postKey'];
        $this->headers['x-user-id'] = $json['pixiv.user.id'];
        return 1;
    }

    //切换请求类型
    public function user_agent($type = 'PC'){
        switch($type){
            case 'PC':$this->headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36';
            break;
            case 'IOS':$this->headers['user-agent'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';
            break;
            case 'Android':$this->headers['user-agent'] = 'Mozilla/5.0 (Linux; Android 6.0.1; Nexus 7 Build/MOB30X) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36';
            break;
            default:$this->headers['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36';
            break;
        }
    }
    
    public function ajax_guzzle_call($method, $url, $headers=[], $params=[], $data=[]){
        if($this->request_type == 1){
            $parse_url = parse_url($url);
            $host = $parse_url['host'];
            //$json_data = $this->require_appapi_hosts($host);
            //$hosts = $json_data['Answer'][3]['data'];
            $hosts = $this->require_appapi_hosts($host);
            $headers['Host'] = $host;
            $url = str_replace($host, $hosts, $url);
        }
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
        if(count($data) == 0){
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
    

    /*
     * date 20190926
     * mode daily 天 weekly 周 monthly 月 rookie 新人 original 原创 male 受男性欢迎 female 受女性欢迎 
     * content all 全部 illust 插图 ugoira 动图 manga 漫画 
     */
    public function ranking($date=Null, $mode='daily', $content= NUll, $p=1){
        //$url = 'https://www.pixiv.net/ranking.php?date='.$date.'&mode='.$mode.'&format=json&p='.$p;
        $url = 'https://www.pixiv.net/ranking.php';
        $params = [
            'mode'=> $mode,
            'p'=> $p,
            'format'=> 'json'
        ];
        $this->user_agent('PC');
        if($date){
            $params['date'] = $date;
        }
        if($content){
            $params['content'] = $content;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    /*
     * date 20190926
     * mode ranking
     * mode daily 天 weekly 周 monthly 月 rookie 新人 original 原创 male 受男性欢迎 female 受女性欢迎 
     * content_rank all 全部 illust 插图 ugoira 动图 manga 漫画 
     */
    public function new_ranking($date=NULL,$mode='daily', $type='all', $p=1, $lang='zh'){
        $url = 'https://www.pixiv.net/touch/ajax/ranking/illust';
        $params = [
            'date'=> $date,
            'mode'=> $mode,
            'type'=> $type,
            'page'=> $p,
            'lang'=> $lang,
        ];
        $this->header['user-agent'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1';
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    /*
     * date 20190926
     * mode ranking
     * mode_rank daily 天 weekly 周 monthly 月 rookie 新人 original 原创 male 受男性欢迎 female 受女性欢迎 
     * content_rank all 全部 illust 插图 ugoira 动图 manga 漫画 
     */
    public function ranking_ajax($date=Null, $mode_rank='daily', $content_rank='all', $p=1){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> 'ranking',
            'mode_rank'=> $mode_rank,
            'content_rank'=> $content_rank,
            'p'=> $p,
        ];
        if($date){
            $params['date'] = $date;
        }
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

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
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=null);
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
    public function search_illusts($word, $data = []){
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
        $r = $this->guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    //收藏的新作
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
     * P站已经更新 此方法弃用
     */
    public function search_illusts_pc($word, $data=[]){
        $url = "https://www.pixiv.net/tags/$word/artworks";
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
            'lang'=> $this->params($data, 'lang', 'zh'),
        ];
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        $html = (string)$r->getBody();
        
        $HtmlDom = QueryList::html($html);
        
        //转义json "
        $data_items = $HtmlDom->find('#js-mount-point-search-result-list')->attrs('data-items');
        $data_items = str_replace('&quot;', '"', $data_items);
        $data_items = json_decode($data_items,TRUE);
        
        $data_related_tags = $HtmlDom->find('#js-mount-point-search-result-list')->attrs('data-related-tags');
        $data_related_tags = str_replace('&quot;', '"', $data_related_tags);
        $data_related_tags = json_decode($data_related_tags,TRUE);
        
        $data_tag = $HtmlDom->find('#js-mount-point-search-result-list')->attrs('data-tag');
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
    
    # $mode='safe', $s_mode='s_tag', $p=1, $order=null, $ratio=null, $wlt=null, $wgt=null, $hlt=null, $hgt=null, $scd=null, $ecd=null, $blt=null, $bgt=null, $tool=null
    /*
     * s_mode = ['s_tag_full','s_tc','s_tag',null]; 标签完全一致 标题说明文字  标签
     * type = ['illust','manga','ugoira', 'all']; 插图 漫画 动图
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
    public function search_illusts_pc_v2($word, $data=[]){
        $url = "https://www.pixiv.net/ajax/search/artworks/$word";
        $params = [
            'word'=> $word,
            'mode'=> $this->params($data, 'mode', 'all'),
            's_mode'=> $this->params($data, 's_mode', 's_tag'),
            'order'=> $this->params($data, 'order','date_d'),
            'type'=> $this->params($data, 'type', 'all'),
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
            'lang'=> $this->params($data, 'lang', 'zh'),
        ];
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }
    
    //作品标签
    public function artworks_tags($word){
        $url = "https://www.pixiv.net/ajax/search/tags/$word";
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    //用户信息 ajax
    public function user_home($user_id, $lang = 'zh'){
        $url = "https://www.pixiv.net/touch/ajax/user/home";
        $params = [
            'id'=>$user_id,
            'lang'=>$lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //用户详情 ajax 
    public function user_details($user_id, $lang = 'zh'){
        $url = "https://www.pixiv.net/touch/ajax/user/details";
        $params = [
            'id'=>$user_id,
            'lang'=>$lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //用户作品 ajax  illust manga
    public function user_illusts($user_id, $type=null, $p=1, $lang = 'zh'){
        $url = "https://www.pixiv.net/touch/ajax/user/illusts";
        $params = [
            'id'=>$user_id,
            'type'=>$type,
            'p'=>$p,
            'lang'=>$lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //用户收藏 ajax 
    public function user_bookmarks($user_id, $type='illust', $p=1, $lang = 'zh'){
        $url = "https://www.pixiv.net/touch/ajax/user/bookmarks";
        $params = [
            'id'=>$user_id,
            'type'=>$type,
            'p'=>$p,
            'lang'=>$lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    public function user_information($user_id, $full = 1, $lang = 'zh'){
        $url = "https://www.pixiv.net/ajax/user/$user_id";
        $params = [
            'full'=>$full,
        ];
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //用户作品收藏 pc
    public function user_illusts_bookmarks($user_id, $tag=null, $offset=0, $limit=4, $rest='show'){
        $url = "https://www.pixiv.net/ajax/user/$user_id/illusts/bookmarks";
        $this->user_agent('PC');
        $params = [
            'tag'=>$tag,
            'offset'=>$offset,
            'limit'=>$limit,
            'rest'=>$rest
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //获取用户收藏作品的标签
    public function user_illusts_bookmarks_tags($user_id){
        $url = "https://www.pixiv.net/ajax/user/$user_id/illusts/bookmark/tags";
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    //用户最新作品
    public function user_latest($user_id){
        $url = "https://www.pixiv.net/ajax/user/$user_id/works/latest";
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }

    
    public function params($data, $key, $value=''){
        if(is_array($data) && array_key_exists($key, $data)){
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
    
    public function user_status($lang = 'zh'){
        $url = 'https://www.pixiv.net/touch/ajax/user/self/status';
        $params = [
            'lang'=> $lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=$params);
        return $this->parse_result($r);
    }
    
    //用户设置信息
    public function user_settings(){
        $url = 'https://www.pixiv.net/touch/ajax/settings';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }
    
    //设置流量范围 set_user_x_restrict  0 全年龄 1 R18 2 R18加怪诞
    public function set_user_x_restrict($user_x_restrict=0, $mode='set_user_x_restrict'){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $data = [
            'user_x_restrict'=> $user_x_restrict,
            'mode'=> $mode,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);

        return $this->parse_result($r);
    }

    //设置语言 zh_tw zh ko en ja
    public function settings_language($lang = 'zh_tw'){
        $url = 'https://www.pixiv.net/touch/ajax/settings/language';
        $data = [
            'code'=>$lang,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);

        return $this->parse_result($r);
    }

    //设置Pixiv 内网广告状态 需要P站会员
    public function set_ads_status($ads_hide = 0, $mode='set_ads_status'){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $data = [
            'ads_hide'=> $ads_hide,
            'mode'=> $mode,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('POST', $url, $this->headers, $params=[], $data);

        return $this->parse_result($r);
    }
    
    public function tags(){
        $url = 'https://www.pixiv.net/tags';
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        $html = (string)$r->getBody();
        $ql = QueryList::html($html);
        $tags = $ql->rules([
            'tag'=>['.tag-value','text'],
            'count'=>['.count-badge','text'],
            'tooltip_url'=>['.icon-pixpedia','href'],
            'tooltip_data'=>['.icon-pixpedia','data-tooltip'],
        ])->query()->getData();
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = $tags;
        return $this;
    }
    
    //搜索相关标签
    public function search_correlation_tag($keyword){
        $url = 'https://www.pixiv.net/rpc/cps.php';
        $params = [
            'keyword'=> $keyword,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $this->headers['sec-fetch-mode'] = 'cors';
        $this->headers['sec-fetch-site'] = 'same-origin';
        $this->headers['referer'] = 'https://www.pixiv.net/tags/'.$keyword.'/artworks';
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //获取作品评论
    public function illust_comments($illust_id, $offset=0, $limit=3){
        $url = 'https://www.pixiv.net/ajax/illusts/comments/roots';
        $params = [
            'illust_id'=> $illust_id,
            'offset'=>$offset,
            'limit'=>$limit,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //动态 具体去P站页面看用法 https://www.pixiv.net/stacc/?mode=unify
    public function all_activity(){
       $unify_config = $this->unify_config();
       $url = 'https://www.pixiv.net/stacc/my/home/all/touch_nottext/'.$unify_config['pixiv.context.nextId'].'/js/';
       //pixiv.context.nextId 
       $params = [
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    public function illust_recommend_init($illust_id, $limit=18){
        $url = 'https://www.pixiv.net/ajax/illust/'.$illust_id.'/recommend/init';
        $params = [
            'limit'=>$limit,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //用户信息顶部
    public function user_profile_top($user_id){
        $url = 'https://www.pixiv.net/ajax/user/'.$user_id.'/profile/top';
        $params = [
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //用户作品
    public function user_profile_all($user_id){
        $url = 'https://www.pixiv.net/ajax/user/'.$user_id.'/profile/all';
        $params = [
            //'tt'=> $this->init_config['pixiv.context.postKey'],
            'lang'=>$this->lang,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }
    
    // type manga
    public function popular_illust_r18($type=null, $p=1){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'p'=>$p,
        ];
        if($type){
            $params['type'] = $type;
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
    }

    //标签
    public function tags_frequent_illust($illust_ids){
        $url = 'https://www.pixiv.net/ajax/tags/frequent/illust';
        $ids = '?';
        if(is_numeric($illust_ids)){
            $ids = $ids.'ids[] = '.$illust_ids;
        }else if(is_string($illust_ids)){
            $ids = $ids.$illust_ids;
        }else if(is_array($illust_ids)){
            foreach($illust_ids as $val){
                $ids = $ids.'ids[] = '.$illust_ids.'&';
            }
        }
        $r = $this->ajax_guzzle_call('GET', $url.$ids, $this->headers, $params=[]);

        return $this->parse_result($r);
    }

    //作品
    public function user_profile_illusts($user_id, $illust_ids, $work_category='illustManga', $is_first_page=1){
        $url = 'https://www.pixiv.net/ajax/user/'.$user_id.'/profile/illusts';
        $ids = '?';
        if(is_numeric($illust_ids)){
            $ids = $ids.'ids[] = '.$illust_ids.'&';
        }else if(is_string($illust_ids)){
            $ids = $ids.$illust_ids.'&';
        }else if(is_array($illust_ids)){
            foreach($illust_ids as $val){
                $ids = $ids.'ids[] = '.$illust_ids.'&';
            }
        }
        $ids = $ids.'work_category='.$work_category.'&';
        $ids = $ids.'is_first_page='.$is_first_page.'&';
        $r = $this->ajax_guzzle_call('GET', $url.$ids, $this->headers, $params=[]);

        return $this->parse_result($r);
    }

    //用户作品
    public function user_illusts_tag($user_id, $tag, $offset=0, $limit=48){
        $url = 'https://www.pixiv.net/ajax/user/'.$user_id.'/illusts/tag';
        $params = [
            'tag'=>$tag,
            'offset'=>$offset,
            'limit'=>$limit,
            'tt'=> $this->init_config['pixiv.context.postKey'],
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //推荐用户列表 带作品
    public function recommend_users($user_ids, $user_num=30, $work_num=5){
        $url = 'https://www.pixiv.net/rpc/index.php';
        $this->user_agent('PC');
        $params = [
            'mode'=>'get_recommend_users_and_works_by_user_ids',
            'user_num'=>$user_num,
            'work_num'=>$work_num,
        ];
        if(is_string($user_ids)){
            $params['user_ids'] = $user_ids;
        }else if(is_array($user_ids)){
            $params['user_ids'] = implode(',',$user_ids);
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }
    
    //推荐作品列表 需要传作品Id
    public function recommend_illust_list($illust_ids, $exclude_muted_illusts=1){
        $url = 'https://www.pixiv.net/rpc/illust_list.php';
        $this->user_agent('PC');
        $params = [
            'page'=>'discover',
            'exclude_muted_illusts'=>$exclude_muted_illusts
        ];
        if(is_string($illust_ids)){
            $params['illust_ids'] = $illust_ids;
        }else if(is_array($illust_ids)){
            $params['illust_ids'] = implode(',',$illust_ids);
        }
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //大家的新作 lastId可以传作品id type [ manga,illust ]
    public function illust_new_pc($lastId=0, $limit=20, $type='illust', $r18=false){
        $url = 'https://www.pixiv.net/ajax/illust/new';
        $this->user_agent('PC');
        $params = [
            'lastId'=>$lastId,
            'limit'=>$limit,
            'type'=>$type,
            'r18'=>$r18,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }


    //浏览历史 novel illust
    public function user_history($type = 'illust', $offset=0){
        $url = 'https://www.pixiv.net/ajax/history';
        $this->user_agent('PC');
        $params = [
            'type'=>$type,
            'offset'=>$offset,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);

        return $this->parse_result($r);
    }

    //获取用户展示作品数据 illust_num 作品数量 novel_num 小说数量
    public function get_user_profile($user_ids, $illust_num = 3, $novel_num = 3){
        $url = 'https://www.pixiv.net/rpc/get_profile.php';
        if(is_array($user_ids)){
            $user_ids = implode(',',$user_ids);
        }
        $params = [
            'user_ids'=>$user_ids,
            'illust_num'=>$illust_num,
            'novel_num'=>$novel_num,
        ];
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //用户额外数据
    public function user_extra(){
        $url = 'https://www.pixiv.net/ajax/user/extra';
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        return $this->parse_result($r);
    }

    /**
     * 作品收藏的用户列表
     */
    public function illust_user_bookmarks($illust_id, $p=1){
        $url = 'https://www.pixiv.net/touch/ajax_api/ajax_api.php';
        $params = [
            'mode'=> 'illust_bookmarks',
            'id'=>$illust_id,
            'p'=>$p
        ];

        $this->user_agent('IOS');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //获取标签故事
    public function tag_stories($tag, $lang='zh'){
        $url = 'https://www.pixiv.net/ajax/stories/tag_stories';
        $params = [
            'tag'=> $tag,
            'lang'=>$lang,
        ];
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        return $this->parse_result($r);
    }

    //热门绘画方法
    public function knowhow_thumbnail_collection(){
        $url = 'https://www.pixiv.net/howto';
        $this->user_agent('PC');
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params=[]);
        $html = (string)$r->getBody();
        $ql = QueryList::html($html);
        $json = $ql->find('#js-knowhow-thumbnail-collection')->attr('data-illusts');
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = json_decode($json, true);
        return $this;
    }

    //竞赛作品 order [date,popular_d,date_d]
    public function pixiv_contest($contest_name,$order='date',$p=1){
        $url = 'https://www.pixiv.net/ajax/contest/'.$contest_name.'/entries';
        $this->user_agent('PC');
        $params = [
            'order'=>$order,
            'p'=>$p,
        ];
        $r = $this->ajax_guzzle_call('GET', $url, $this->headers, $params);
        $json = json_decode((string)$r->getBody(),true);
        $html = $json['body']['html'];
        $ql = QueryList::html($html);
        $json = $ql->rules([
            'url'=>['.thumbnail-container .lazy-content','data-src'],
            'illust_id'=>['.user-activity','data-work-id'],
            'page_count'=>['._icon-text','text'],
            'user_id'=>['.user-view-popup','data-user_id'],
            'user_head'=>['.meta-container .lazy-content','data-src'],
            'user_name'=>['.user-name','text'],
        ])->query()->getData(function($item){
            /*preg_match_all('/background\s*-\s*+image\s*:\s*url\s*"∗([′′]∗)"∗/i', $item['url'],$url_data);
            $item['url'] = $url_data[0];
            preg_match_all('/background\s*-\s*+image\s*:\s*url\s*"∗([′′]∗)"∗/i', $item['user_head'],$user_head_data);
            $item['user_head'] = $user_head_data[0];
            if(empty($item['page_count'])){
                $item['page_count'] = 1;
            }*/
            return $item;
        });
        $this->StatusCode = $r->getStatusCode();
        $this->Headers = $r->getHeaders();
        $this->ReasonPhrase = $r->getReasonPhrase();
        $this->body = $r->getBody();
        $this->getContents = (string)$r->getBody();
        $this->json = $json;
        return $this;
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
        $HtmlDom = QueryList::html($html);
        //init-config-input
        $init_config = $HtmlDom->find('#init-config-input')->value;
        $this->unify_config = json_decode($init_config, TRUE);
        $this->unify_config['create_time'] = time();
        $this->WriteFile($init_file, json_encode($this->unify_config, JSON_UNESCAPED_UNICODE));
        return $this->unify_config;
    }


}
