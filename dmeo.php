<?php

require_once __DIR__ . '/vendor/autoload.php';
use pixiv\Aapi;
use pixiv\Papi;
use pixiv\Ajax;


//$ajax = new Ajax();
//$cookie = '';
//$ajax->set_init($cookie);
//# yyyyMMdd
//$ajax->ranking('20190912');
//$ajax->popular_illust();
//# $wlt 最小宽度 $wgt 最大宽度
//$ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);
//echo $re;
//$ajax->search_illusts_pc('プリンツ・オイゲン(アズールレーン)');
//print_r($ajax->json);

// $Aapi = new Aapi();

//$Papi = new Papi();

$ajax = new Ajax();

//设置 页面上的cookie
$cookie = '';
//设置cookie
$ajax->set_init($cookie);
# yyyyMMdd
//$ajax->ranking('20190912');
//$ajax->popular_illust();
# $wlt 最小宽度 $wgt 最大宽度
//$ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);

//$ajax->all_activity();
// $ajax->search_illusts_pc_v2('碧蓝航线');
// $ajax->return_json();
// $ajax->search_illusts_pc('プリンツ・オイゲン(アズールレーン)');
//$ajax->tags_frequent_illust('80644077');
// $ajax->user_profile_top('4462245');
//print_r($ajax->json);

$ajax->search_illusts_pc_v2("イレイナ",['mode'=>'all','order'=>'popular_d']);
$url = [];
foreach($ajax->json['body']['illusts'] as $val){
    $url[] = $val['url_w'];
}

print_r($ajax->download_files($url));
//$ajax->return_json();

//$Aapi = new Aapi();

// $username = '';
// $password = '';
// $Aapi->request_type = 0;
// $Aapi->login($username, $password);
//$Aapi->user_illusts('40291400');
//print_r($Aapi->json);


//echo $Aapi->StatusCode;

//下载动图
//$Aapi->ugoira_meta_save(72729032);
//
//$username = '';
//$password = '';
//$Aapi->request_type = 1;
//$Aapi->login($username, $password);
//$Aapi->user_illusts('40291400');
//print_r($Aapi->json);
//echo $Aapi->StatusCode;
//下载动图
//$Aapi->ugoira_meta_save(72729032);

//$Papi = new Papi();
//$Papi->login($username, $password);
//$Papi->request_type=1;
//$Papi->works(76788220);
//echo 'StatusCode:'.$Papi->StatusCode;
//print_r($Papi->json);

//$Aapi->illust_detail(76472054);

//$Aapi->illust_related(76472054,'for_ios',['76496233','76454229']);
//$Aapi->illust_recommended();
//$Aapi->illust_ranking();
//$Aapi->trending_tags_illust();
//$Aapi->search_illust($word='碧蓝航线');
//$Aapi->illust_bookmark_detail(76472054);
//$Aapi->illust_bookmark_add(71422901);
//$Aapi->user_bookmark_tags_illust();
//$Aapi->user_following(2374176);
//$Aapi->ugoira_metadata();
//$Aapi->spotlight_articles();
//$Aapi->search_autocomplete('碧蓝航线');
//$Aapi->recommended_user();
//$Aapi->markers_novel();
//$Aapi->delete_user(19257936);
//$Aapi->user_bookmarks_illust($this->user_id, 'public');
//$Aapi->search_illust_popular_preview('綾波');
//$Aapi->user_state();
//$Aapi->search_illust_bookmark_ranges('綾波');
//$Aapi->illust_walkthrough();
//$Aapi->illust_new_v1(71422901);
//$Aapi->illust_my_pixiv();
//$Aapi->manga_recommended();
//$Aapi->illust_ranking('2019-09-07');
//$Aapi->json($re->json);




//$Papi->works(73726426);
//$Papi->users(14525258);
//$Papi->me_favorite_works();
//$Papi->me_following_works();
//$Papi->me_following();
//$Papi->me_favorite_users_follow(20228000);
//$Papi->me_favorite_users_unfollow([17560794,263118]);
//$Papi->users_works(16700831);
//$Papi->users_following(16700831);
//$Papi->ranking('2019-09-01');
//$Papi->search_works('碧蓝航线');
//$Papi->latest_works();
