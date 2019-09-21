<?php

require_once __DIR__ . '/vendor/autoload.php';
use pixiv\Aapi;
use pixiv\Papi;
use pixiv\Ajax;

$ajax = new Ajax();
$cookie = '';
$ajax->set_init($cookie);
# yyyyMMdd
//$json = $ajax->ranking('20190912');
//$json = $ajax->popular_illust();
# $wlt 最小宽度 $wgt 最大宽度
$json = $ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);
echo $json;

//$Aapi = new Aapi();
//$username = '';
//$password = '';
//$Aapi->login($username, $password);

//$json = $Aapi->user_illusts('40291400');
//$json = $Aapi->illust_detail(76472054);

//$json = $Aapi->illust_related(76472054,'for_ios',['76496233','76454229']);
//$json = $Aapi->illust_recommended();
//$json = $Aapi->illust_ranking();
//$json = $Aapi->trending_tags_illust();
//$json = $Aapi->search_illust($word='碧蓝航线');
//$json = $Aapi->illust_bookmark_detail(76472054);
//$json = $Aapi->illust_bookmark_add(71422901);
//$json = $Aapi->user_bookmark_tags_illust();
//$json = $Aapi->user_following(2374176);
//$json = $Aapi->ugoira_metadata();
//$json = $Aapi->spotlight_articles();
//$json = $Aapi->search_autocomplete('碧蓝航线');
//$json = $Aapi->recommended_user();
//$json = $Aapi->markers_novel();
//$json = $Aapi->delete_user(19257936);
//$json = $Aapi->user_bookmarks_illust($this->user_id, 'public');
//$json = $Aapi->search_illust_popular_preview('綾波');
//$json = $Aapi->user_state();
//$json = $Aapi->search_illust_bookmark_ranges('綾波');
//$json = $Aapi->illust_walkthrough();
//$json = $Aapi->illust_new_v1(71422901);
//$json = $Aapi->illust_my_pixiv();
//$json = $Aapi->manga_recommended();
//$json = $Aapi->illust_ranking('2019-09-07');
//$Aapi->json($json);



//$Papi = new Papi();
//$json = $Papi->works(73726426);
//$json = $Papi->users(14525258);
//$json = $Papi->me_favorite_works();
//$json = $Papi->me_following_works();
//$json = $Papi->me_following();
//$json = $Papi->me_favorite_users_follow(20228000);
//$json = $Papi->me_favorite_users_unfollow([17560794,263118]);
//$json = $Papi->users_works(16700831);
//$json = $Papi->users_following(16700831);
//$json = $Papi->ranking('2019-09-01');
//$json = $Papi->search_works('碧蓝航线');
//$json = $Papi->latest_works();
