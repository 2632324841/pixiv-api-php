<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace pixiv;
use pixiv\Api;

/**
 * Description of Papi
 *
 * @author JC
 */
class Papi extends Api{
    
    public $StatusCode;
    public $Headers;
    public $ReasonPhrase;
    public $body;
    public $getContents;
    public $json;
    
    public function auth_guzzle_call($method, $url, $headers=[], $params=[], $data=[]){
        $headers['Referer'] = 'http://spapi.pixiv.net/';
        $headers['User-Agent'] = 'PixivIOSApp/5.8.7';
        $headers['Authorization'] = 'Bearer '.$this->access_token;
        $r = $this->guzzle_call($method, $url, $headers, $params, $data);
        return $r;
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
     
    # 翻页参数
    public function parse_qs($next_url){
        $parse = parse_url($next_url);
        $params = $this->convertUrlQuery($parse['query']);
        return $params;
    }
    
    public function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param) {
          $item = explode('=', $param);
          $params[$item[0]] = $item[1];
        }
        return $params;
    }
    
    # 翻页
    public function next_page($next_url, $req_auth=True){
        if($next_url == null){
            return null;
        }
        $params = $this->parse_qs($next_url);
        $r = $this->auth_guzzle_call('GET', $next_url, $headers = [], $params, $req_auth);
        return $this->parse_result($r);
    }
    
    public function format_bool($bool_value){
        if(is_bool($bool_value)){
            if($bool_value == TRUE){
                return 'true';
            }else{
                return 'false';
            }
        }
        if($bool_value == 1){
            return 'true';
        }else{
            return 'false';
        }
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
    
    # 失效 404 Not Found
    public function bad_words(){
        $url = 'https://public-api.secure.pixiv.net/v1.1/bad_words.json';
        $r = $this->auth_guzzle_call('GET', $url);
        return $this->parse_result($r);
    }
    
    # 作品详细
    public function works($illust_id, $include_sanity_level=False){
        $url = "https://public-api.secure.pixiv.net/v1/works/$illust_id.json";
        $params = [
            'image_sizes'=> 'px_128x128,small,medium,large,px_480mw',
            'include_stats'=> 'true',
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 用户资料
    public function users($author_id){
        $url = "https://public-api.secure.pixiv.net/v1/users/$author_id.json";
        $params = [
            'profile_image_sizes'=> 'px_170x170,px_50x50',
            'image_sizes'=> 'px_128x128,small,medium,large,px_480mw',
            'include_stats'=> 1,
            'include_profile'=> 1,
            'include_workspace'=> 1,
            'include_contacts'=> 1,
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 我的订阅 404 Not Found
    public function me_feeds($show_r18=1, $max_id=null){
        $url = 'https://public-api.secure.pixiv.net/v1/me/feeds.json';
        $params = [
            'relation'=> 'all',
            'type'=> 'touch_nottext',
            'show_r18'=> $show_r18,
        ];
        if($max_id){
            $params['max_id'] = $max_id;
        }
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 获取收藏夹
    # publicity: public, private 404 Not Found
    public function me_favorite_works($page=1, $per_page=50, $publicity='public', $image_sizes=['px_128x128', 'px_480mw', 'large']){
        $url = 'https://public-api.secure.pixiv.net/v1/me/favorite_works.json';
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
            'publicity'=> $publicity,
            'image_sizes'=> join(',',$image_sizes),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }
    
    # 添加收藏
    # publicity: public, private
    public function me_favorite_works_add($work_id, $publicity='public'){
        $url = 'https://public-api.secure.pixiv.net/v1/me/favorite_works.json';
        $params = [
            'work_id'=> $work_id,
            'publicity'=> $publicity,
        ];
        $r = $this->auth_guzzle_call('POST', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 删除收藏
    # publicity: public, private
    public function me_favorite_works_delete($ids, $publicity='public'){
        $url = 'https://public-api.secure.pixiv.net/v1/me/favorite_works.json';
        if(is_array($ids)){
            $params = ['ids'=> join(',',$ids), 'publicity'=> $publicity];
        }else{
            $params = ['ids'=> $ids, 'publicity'=> $publicity];
        }
        $r = $this->auth_guzzle_call('DELETE', $url, $headers=[], $params);
        return $this->parse_result($r);
    }


    # 关注的新作品 (New -> Follow)
    public function me_following_works($page=1, $per_page=30,$image_sizes=['px_128x128', 'px_480mw', 'large'],$include_stats=True, $include_sanity_level=True){
        $url = 'https://public-api.secure.pixiv.net/v1/me/following/works.json';
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
            'image_sizes'=> join(',',$image_sizes),
            'include_stats'=> $this->format_bool($include_stats),
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 获取关注用户
    public function me_following($page=1, $per_page=30, $publicity='public'){
        $url = 'https://public-api.secure.pixiv.net/v1/me/following.json';
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
            'publicity'=> $publicity,
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }
    
    # 关注用户
    # publicity:  public, private
    public function me_favorite_users_follow($user_id, $publicity='public'){
        $url = 'https://public-api.secure.pixiv.net/v1/me/favorite-users.json';
        $params = [
            'target_user_id'=> $user_id,
            'publicity'=> $publicity
        ];
        $r = $this->auth_guzzle_call('POST', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 解除关注用户
    public function me_favorite_users_unfollow($user_ids, $publicity='public'){
        $url = 'https://public-api.secure.pixiv.net/v1/me/favorite-users.json';
        if(is_array($user_ids)){
            $params = ['delete_ids'=> join(',', $user_ids), 'publicity'=> $publicity];
        }else{
            $params = ['delete_ids'=> $user_ids, 'publicity'=> $publicity];
        }
        $r = $this->auth_guzzle_call('DELETE', $url, $headers=[], $params);
        return $this->parse_result($r);
    }
    
    # 用户作品列表
    public function users_works($author_id, $page=1, $per_page=30,$image_sizes=['px_128x128', 'px_480mw', 'large'],$include_stats=True, $include_sanity_level=True){
        $url = "https://public-api.secure.pixiv.net/v1/users/$author_id/works.json";
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
            'include_stats'=> $this->format_bool($include_stats),
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
            'image_sizes'=> join(',',$image_sizes),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 用户活动 404 Not Found
    public function users_feeds($author_id, $show_r18=1, $max_id=null){
        $url = "https://public-api.secure.pixiv.net/v1/users/$author_id/feeds.json";
        $params = [
            'relation'=> 'all',
            'type'=> 'touch_nottext',
            'show_r18'=> $show_r18,
        ];
        if($max_id){
            $params['max_id'] = $max_id;
        }
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }
    
    # 用户关注的用户
    public function users_following($author_id, $page=1, $per_page=30){
        $url = "https://public-api.secure.pixiv.net/v1/users/$author_id/following.json";
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 排行榜/过去排行榜
    # ranking_type: [all, illust, manga, ugoira]
    # mode: [daily, weekly, monthly, rookie, original, male, female, daily_r18, weekly_r18, male_r18, female_r18, r18g]
    #       for 'illust' & 'manga': [daily, weekly, monthly, rookie, daily_r18, weekly_r18, r18g]
    #       for 'ugoira': [daily, weekly, daily_r18, weekly_r18],
    # page: [1-n]
    # date: '2015-04-01' (仅过去排行榜)
    public function ranking($date=null, $ranking_type='all', $mode='daily', $page=1, $per_page=50, $image_sizes=['px_128x128', 'px_480mw', 'large'], $profile_image_sizes=['px_170x170', 'px_50x50'], $include_stats=True, $include_sanity_level=True){
        $url = "https://public-api.secure.pixiv.net/v1/ranking/$ranking_type.json";
        $params = [
            'mode'=> $mode,
            'page'=> $page,
            'per_page'=> $per_page,
            'include_stats'=> $this->format_bool($include_stats),
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
            'image_sizes'=> join(',',$image_sizes),
            'profile_image_sizes'=> join(',',$profile_image_sizes),
        ];
        if($date){
            $params['date'] = $date;
        }
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 作品搜索
    public function search_works($query, $page=1, $per_page=30, $mode='text', $period='all', $order='desc', $sort='date', $types=['illustration', 'manga', 'ugoira'], $image_sizes=['px_128x128', 'px_480mw', 'large'], $include_stats=True, $include_sanity_level=True){
        $url = 'https://public-api.secure.pixiv.net/v1/search/works.json';
        $params = [
            'q'=> $query,
            'page'=> $page,
            'per_page'=> $per_page,
            'period'=> $period,
            'order'=> $order,
            'sort'=> $sort,
            'mode'=> $mode,
            'types'=> join(',',$types),
            'include_stats'=> $this->format_bool($include_stats),
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
            'image_sizes'=> join(',',$image_sizes),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }

    # 最新作品 (New -> Everyone)
    public function latest_works($page=1, $per_page=30, $image_sizes=['px_128x128', 'px_480mw', 'large'], $profile_image_sizes=['px_170x170', 'px_50x50'], $include_stats=True, $include_sanity_level=True){
        $url = 'https://public-api.secure.pixiv.net/v1/works.json';
        $params = [
            'page'=> $page,
            'per_page'=> $per_page,
            'include_stats'=> $this->format_bool($include_stats),
            'include_sanity_level'=> $this->format_bool($include_sanity_level),
            'image_sizes'=> join(',',$image_sizes),
            'profile_image_sizes'=> join(',',$profile_image_sizes),
        ];
        $r = $this->auth_guzzle_call('GET', $url, $headers=[], $params);
        return $this->parse_result($r);
    }
}
