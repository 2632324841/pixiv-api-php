<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace pixiv;
use pixiv\Api;

/**
 * Description of Aapi
 *
 * @author JC
 */
class Aapi extends Api{
    //put your code here
    //protected $hosts = "https://app-api.pixiv.net";
    public $StatusCode;
    public $Headers;
    public $ReasonPhrase;
    public $body;
    public $getContents;
    public $json;
    protected $hosts = 'https://app-api.pixiv.net';
    public function ugoira_meta($illust_id){
        $url = "https://www.pixiv.net/ajax/illust/$illust_id/ugoira_meta";
        if($this->request_type == 1){
//            $headers['Host'] = 'www.pixiv.net';
//            $host = $this->require_appapi_hosts('www.pixiv.net');
//            $url = str_replace('www.pixiv.net', $host, $url);
            exit('request_type=1');
        }
        $r = $this->guzzle_call('GET', $url, $headers);
        return $this->parse_result($r);
    }
    
    # tpye = ['originalSrc','src']
    public function ugoira_meta_save($illust_id, $is_return=FALSE, $savePath='image/', $fileName='', $tpye='src', $is_save=FALSE){
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
            if($is_return){
                $gif = $this->create_gif($frames, $delay);
                //删除临时文件
                foreach ($frames as $val){
                    unlink($val);
                }
                //删除zip文件
                if(!$is_save){
                    unlink($zipPath.$zipFile);
                }
                return $gif;
            }else{
                //创建GIF
                if($this->create_gif($frames, $delay, $savePath.$fileName)){
                    //删除临时文件
                    foreach ($frames as $val){
                        unlink($val);
                    }
                    //删除zip文件
                    if(!$is_save){
                        unlink($zipPath.$zipFile);
                    }
                    return TRUE;
                }else{
                    return FALSE;
                }
            }
        }
        else{
            return FALSE;
        }
    }

    # 用户详情
    public function user_detail($user_id , $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/detail';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 用户作品列表
    # type: [illust, manga]
    public function user_illusts($user_id, $type='illust', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/illusts';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        if($type != null){
            $params['type'] = $type;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
	
    # 用户小说
    public function user_novels($user_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/novels';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 用户收藏作品列表
    # tag: 从 user_bookmark_tags_illust 获取的收藏标签
    public function user_bookmarks_illust($user_id, $restrict='public', $max_bookmark_id=null, $tag=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/bookmarks/illust';
        $params = [
            'user_id'=> $user_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($max_bookmark_id){
            $params['max_bookmark_id'] = $max_bookmark_id;
        }
        if($tag){
            $params['tag'] = $tag;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 用户收藏作品列表（小说）
    # tag: 从 user_bookmark_tags_illust 获取的收藏标签
    public function user_bookmarks_novel($user_id, $restrict='public', $max_bookmark_id=null, $tag=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/bookmarks/novel';
        $params = [
            'user_id'=> $user_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($max_bookmark_id){
            $params['max_bookmark_id'] = $max_bookmark_id;
        }
        if($tag){
            $params['tag'] = $tag;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 用户收藏作品列表（小说）
    # tag: 从 user_bookmark_tags_illust 获取的收藏标签
    public function user_bookmarks_novel_tag($user_id, $restrict='public', $max_bookmark_id=null, $tag=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/bookmarks/novel';
        $params = [
            'restrict'=> $user_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($max_bookmark_id){
            $params['max_bookmark_id'] = $max_bookmark_id;
        }
        if($tag){
            $params['tag'] = $tag;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 你的 - 小说书签
    public function markers_novel($offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/novel/markers';
        $params = [
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 关注用户的新作
    # restrict: [public, private]
    public function illust_follow($restrict='public', $offset=null, $req_auth=True){
        $url = $this->hosts.'/v2/illust/follow';
        $params = [
            'restrict'=> $restrict,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 大家的新作
    # content_type: [illust, manga]
    public function illust_new($content_type='illust', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/illust/follow';
        $params = [
            'content_type'=> $content_type,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 大家的新作（小说）
    public function novel_new($filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/new';
        $params = [
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 查看推荐作家
    public function recommended_user($offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/recommended';
        $params = [
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 作品详情 (类似$PAPI->works()，iOS中未使用)
    public function illust_detail($illust_id, $req_auth=True){
        $url = $this->hosts.'/v1/illust/detail';
        $params = [
            'illust_id'=> $illust_id,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 作品评论
    public function illust_comments($illust_id, $offset=null, $include_total_comments=null, $req_auth=True){
        $url = $this->hosts.'/v1/illust/comments';
        $params = [
            'illust_id'=> $illust_id,  
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        if($include_total_comments){
            $params['include_total_comments'] = $this->format_bool($include_total_comments);
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 相关作品列表
    public function illust_related($illust_id, $seed_illust_ids=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/illust/related';
        $params = [
            'illust_id'=> $illust_id,
            'filter'=> $filter,  
        ];
        if(is_string($seed_illust_ids)){
            $params['seed_illust_ids[]'] = $seed_illust_ids;
        }
        if(is_array($seed_illust_ids)){
            $params['seed_illust_ids[]'] = $seed_illust_ids;
        }
        //由于 http_build_query原因 会在数组设置下标
        $url = $url.$this->build_query($params);
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params=null, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 插画推荐 (Home - Main)
    # content_type: [illust, manga]
    public function illust_recommended($content_type='illust', $include_ranking_label=true, $max_bookmark_id_for_recommend=null, $min_bookmark_id_for_recent_illust=null,
    $offset=null, $include_ranking_illusts=null, $bookmark_illust_ids=null, $include_privacy_policy=null, $filter='for_ios', $req_auth=True){
        if($req_auth){
            $url = $this->hosts.'/v1/illust/recommended';
        }else{
            $url = $this->hosts.'/v1/illust/recommended-nologin';
        }
        $params = [
            'content_type'=> $content_type,
            'include_ranking_label'=> $this->format_bool($include_ranking_label),
            'filter'=> $filter,
        ];
        if($max_bookmark_id_for_recommend){
            $params['max_bookmark_id_for_recommend'] = $max_bookmark_id_for_recommend;
        }
        if($min_bookmark_id_for_recent_illust){
            $params['min_bookmark_id_for_recent_illust'] = $min_bookmark_id_for_recent_illust;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        if($include_ranking_illusts){
            $params['include_ranking_illusts'] = $this->format_bool($include_ranking_illusts);
        }
        if(!$req_auth){
            if(is_string($bookmark_illust_ids)){
                $params['bookmark_illust_ids'] = $bookmark_illust_ids;
            }
            if(is_array($bookmark_illust_ids)){
                $params['bookmark_illust_ids'] = join(",", $bookmark_illust_ids);
            }
        }
        if($include_privacy_policy){
            $params['include_privacy_policy'] = $include_privacy_policy;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 作品排行
    # mode: [day, week, month, day_male, day_female, week_original, week_rookie, day_manga]
    # date: '2016-08-01'
    # mode (Past): [day, week, month, day_male, day_female, week_original, week_rookie,
    #               day_r18, day_male_r18, day_female_r18, week_r18, week_r18g]
    public function illust_ranking($date=null, $mode='day', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/illust/ranking';
        $params = [
            'mode'=> $mode,
            'filter'=> $filter,
        ];
        if($date){
            $params['date'] = $date;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 作品排行（小说）
    # mode: [day, week, month, day_male, day_female, week_original, week_rookie, day_manga]
    # date: '2016-08-01'
    # mode (Past): [day, week, month, day_male, day_female, week_original, week_rookie,
    #               day_r18, day_male_r18, day_female_r18, week_r18, week_r18g]
    public function novel_ranking($date=null, $mode='day', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/ranking';
        $params = [
            'mode'=> $mode,
            'filter'=> $filter,
        ];
        if($date){
            $params['date'] = $date;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 文章特辑  
    public function spotlight_articles($date=null, $category='all', $offset=null, $req_auth=True){
        $url = $this->hosts.'/v1/spotlight/articles';
        $params = [
            'category'=> $category,
        ];
        if($date){
            $params['date'] = $date;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 自动补全
    public function search_autocomplete($word, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/autocomplete';
        $params = [
            'word'=> $word,
            'for_ios'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }


    # 趋势标签 (Search - tags)
    public function trending_tags_illust($filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/trending-tags/illust';
        $params = [
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 搜索 (Search)
    # search_target - 搜索类型
    #   partial_match_for_tags  - 标签部分一致
    #   exact_match_for_tags    - 标签完全一致
    #   title_and_caption       - 标题说明文
    # sort: [date_desc, date_asc]
    # duration: [within_last_day, within_last_week, within_last_month]
    public function search_illust($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/illust';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 搜索 (Search)
    # search_target - 搜索类型
    #   partial_match_for_tags  - 标签部分一致
    #   exact_match_for_tags    - 标签完全一致
    #   title_and_caption       - 标题说明文
    # sort: [date_desc, date_asc]
    # duration: [within_last_day, within_last_week, within_last_month]
    public function search_illust_popular_preview($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/popular-preview/illust';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 用户状态
    public function user_state(){
        $url = $this->hosts.'/v1/user/me/state';
        $r = $this->no_auth_guzzle_call('GET', $url);
        return $this->parse_result($r);
    }
    
    # 搜索小说
    public function search_novel($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/novel';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 搜索流行小说
    public function search_novel_popular_preview($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/popular-preview/novel';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 搜索插图书签范围
    public function search_illust_bookmark_ranges($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/bookmark-ranges/illus';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 搜索插图书签范围
    public function search_novel_bookmark_ranges($word, $search_target='partial_match_for_tags', $sort='date_desc', $duration=null, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/bookmark-ranges/novel';
        $params = [
            'word'=> $word,
            'search_target'=> $search_target,
            'sort'=> $sort,
            'filter'=> $filter,
        ];
        if($duration){
            $params['duration'] = $duration;
        }
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    public function search_user($word, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/search/bookmark-ranges/novel';
        $params = [
            'word'=> $word,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    public function search_auto_complete_v2($word, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/search/autocomplete';
        $params = [
            'word'=> $word,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 
    public function illust_walkthrough($req_auth=True){
        $url = $this->hosts.'/v1/walkthrough/illusts';
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params=[], $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    #
    public function illust_comments_v2($illust_id, $offset=null, $include_total_comments=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/illust/comments';
        $params = [
            'illust_id'=> $illust_id,  
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        if($include_total_comments){
            $params['include_total_comments'] = $this->format_bool($include_total_comments);
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 
    public function illust_comment_replies($comment_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/illust/comments';
        $params = [
            'comment_id'=> $comment_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 
    public function illust_new_v1($illust_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/illust/new';
        $params = [
            'illust_id'=> $illust_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    #
    public function illust_my_pixiv($offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/illust/mypixiv';
        $params = [
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 添加作品评论
    public function illust_add_comment($illust_id, $comment, $parent_comment_id, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/illust/comment/add';
        $data = [
            'illust_id'=> $illust_id,
            'comment'=> $comment,
            'parent_comment_id'=> $parent_comment_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 添加小说评论
    public function novel_add_comment($illust_id, $comment, $parent_comment_id, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/comment/add';
        $data = [
            'illust_id'=> $illust_id,
            'comment'=> $comment,
            'parent_comment_id'=> $parent_comment_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }

    # 作品收藏详情
    public function illust_bookmark_detail($illust_id, $req_auth=True){
        $url = $this->hosts.'/v2/illust/bookmark/detail';
        $params = [
            'illust_id'=> $illust_id,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 新增收藏
    # restrict : [public, private]
    public function illust_bookmark_add($illust_id, $restrict='public', $tags=null, $req_auth=True){
        $url = $this->hosts.'/v2/illust/bookmark/add';
        $data = [
            'illust_id'=> $illust_id,
            'restrict'=> $restrict,
        ];
        if(is_string($tags)){
            $data['tags'] = $tags;
        }
        if(is_array($tags)){
            $data['tags'] = join(' ', $tags);
        }
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 删除收藏
    public function illust_bookmark_delete($illust_id, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/illust/bookmark/delete';
        $data = [
            'illust_id'=> $illust_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 新增小说
    # restrict : [public, private]
    public function novel_bookmark_add($novel_id, $restrict='public', $tags=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/novel/bookmark/add';
        $data = [
            'novel_id'=> $novel_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if(is_string($tags)){
            $data['tags'] = $tags;
        }
        if(is_array($tags)){
            $data['tags'] = join(' ', $tags);
        }
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 删除小说
    public function novel_bookmark_delete($illust_id, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/bookmark/delete';
        $data = [
            'illust_id'=> $illust_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params, $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 用户收藏标签列表
    public function user_bookmark_tags_illust($restrict='public', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/bookmark-tags/illust';
        $params = [
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # Following用户列表
    public function user_following($user_id, $restrict='public', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/following';
        $params = [
            'user_id'=> $user_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    #
    public function user_follow_detail($user_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/follow/detail';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }


    # Followers用户列表
    public function user_follower($user_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/follower';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 好P友
    public function user_mypixiv($user_id, $offset=null, $req_auth=True){
        $url = $this->hosts.'/v1/user/mypixiv';
        $params = [
            'user_id'=> $user_id,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 黑名单用户
    public function user_list($user_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/user/list';
        $params = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        if ($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 添加好P友
    # restrict: [public, private]
    public function add_user($user_id, $restrict='public', $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/follow/add';
        $data = [
            'user_id'=> $user_id,
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 删除用户
    public function delete_user($user_id, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/user/follow/delete';
        $data = [
            'user_id'=> $user_id,
            'filter'=> $filter,
        ];
        $r = $this->no_auth_guzzle_call('POST', $url, $headers = [], $params=[], $data, $req_auth);
        return $this->parse_result($r);
    }
    
    # 推荐漫画
    public function manga_recommended($include_ranking_label=True, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/manga/recommended';
        $params = [
            'include_ranking_label'=> $this->format_bool($include_ranking_label),
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 最新漫画
    public function manga_new($content_type='manga', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/manga/recommended';
        $params = [
            'content_type'=> $content_type,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 推荐小说
    public function novel_recommended($include_ranking_novels=True, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/manga/recommended';
        $params = [
            'include_ranking_novels'=> $this->format_bool($include_ranking_novels),
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 小说评论
    public function novel_comments($novel_id, $include_total_comments=True, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/comments';
        $params = [
            'novel_id'=> $novel_id,
            'filter'=> $filter,
            'include_total_comments'=> $this->format_bool($include_total_comments),
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 小说评论v2
    public function novel_comments_v2($novel_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/novel/comments';
        $params = [
            'novel_id'=> $novel_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 小说评论、回复
    public function novel_comment_replies($novel_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/comment/replies';
        $params = [
            'comment_id'=> $novel_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 小说系列
    public function novel_series($series_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/series';
        $params = [
            'series_id'=> $series_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 中篇小说
    public function novel_detail($novel_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/novel/detail';
        $params = [
            'novel_id'=> $novel_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }

    # 小说文本
    public function novel_text($novel_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/text';
        $params = [
            'novel_id'=> $novel_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 小说
    public function novel_follow($restrict='all', $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/follow';
        $params = [
            'restrict'=> $restrict,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 我的小说
    public function novel_my_pixiv($offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v1/novel/mypixiv';
        $params = [
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    
    # 我的小说
    public function novel_bookmark_detail($novel_id, $offset=null, $filter='for_ios', $req_auth=True){
        $url = $this->hosts.'/v2/novel/bookmark/detail';
        $params = [
            'novel_id'=> $novel_id,
            'filter'=> $filter,
        ];
        if($offset){
            $params['offset'] = $offset;
        }
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
    }
    

    # 获取ugoira信息
    public function ugoira_metadata($illust_id, $req_auth=True){
        $url = $this->hosts.'/v1/ugoira/metadata';
        $params = [
            'illust_id'=> $illust_id,
        ];
        $r = $this->no_auth_guzzle_call('GET', $url, $headers = [], $params, $data=[], $req_auth);
        return $this->parse_result($r);
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
    
	# 转换Json发送的Bool数据
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

    public function no_auth_guzzle_call($method, $url, $headers=[], $params=[], $data=[], $req_auth=True){
        if($this->request_type == 1){
            $parse_url = parse_url($url);
            $host = $parse_url['host'];
            if(!filter_var($host, FILTER_VALIDATE_IP)){
                //$json_data = $this->require_appapi_hosts($host);
                //$hosts = $json_data['Answer'][0]['data'];
                $hosts = $this->require_appapi_hosts($host);
                $headers['Host'] = $host;
                $url = str_replace($host, $hosts, $url);
            }
            
        }
        if(array_key_exists('User-Agent',$headers) == FALSE || array_key_exists('user-agent',$headers) == FALSE){
            # Set User-Agent if not provided
            $headers['App-OS'] = 'ios';
            $headers['App-OS-Version'] = '12.2';
            $headers['App-Version'] = '7.6.2';
            $headers['User-Agent'] = 'PixivIOSApp/7.6.2 (iOS 12.2; iPhone9,1)';
        }
        if(!$req_auth){
            return $this->guzzle_call($method, $url, $headers, $data);
        }
        else{
            $headers['Authorization'] = 'Bearer '.$this->access_token;
            return $this->guzzle_call($method, $url, $headers, $params, $data);
        }
    }
    
    # 翻页请求数据
    public function parse_qs($next_url){
        $parse = parse_url($next_url);
        $params = $this->convertUrlQuery($parse['query']);
        return $params;
    }
    
    # 转换Query数据
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
        $r = $this->no_auth_guzzle_call('GET', $next_url, $headers = [], $params, $req_auth);
        return $this->parse_result($r);
    }
}
