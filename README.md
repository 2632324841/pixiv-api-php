# pixiv-api-php
这是一个Pixiv IOS API 
类似于[upbit/pixivpy](https://github.com/upbit/pixivpy)

## 安装
> composer require deadlymous/pixiv_api

## 实例
```php
//$ajax = new Ajax();
//$cookie = '';
//$ajax->set_init($cookie);
//# yyyyMMdd
////$re = $ajax->ranking('20190912');
////$re = $ajax->popular_illust();
//# $wlt 最小宽度 $wgt 最大宽度
//$re = $ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);
//echo $re;

//$Aapi = new Aapi();
$Papi = new Papi;
$username = '';
$password = '';
//$Aapi->login($username, $password);
//$re = $Aapi->user_illusts('40291400');
//print_r($re->json);

$Papi->login($username, $password);
$re = $Papi->works(76788220);
print_r($re);

//$re = $Aapi->illust_detail(76472054);

//$re = $Aapi->illust_related(76472054,'for_ios',['76496233','76454229']);
//$re = $Aapi->illust_recommended();
//$re = $Aapi->illust_ranking();
//$re = $Aapi->trending_tags_illust();
//$re = $Aapi->search_illust($word='碧蓝航线');
//$re = $Aapi->illust_bookmark_detail(76472054);
//$re = $Aapi->illust_bookmark_add(71422901);
//$re = $Aapi->user_bookmark_tags_illust();
//$re = $Aapi->user_following(2374176);
//$re = $Aapi->ugoira_metadata();
//$re = $Aapi->spotlight_articles();
//$re = $Aapi->search_autocomplete('碧蓝航线');
//$re = $Aapi->recommended_user();
//$re = $Aapi->markers_novel();
//$re = $Aapi->delete_user(19257936);
//$re = $Aapi->user_bookmarks_illust($this->user_id, 'public');
//$re = $Aapi->search_illust_popular_preview('綾波');
//$re = $Aapi->user_state();
//$re = $Aapi->search_illust_bookmark_ranges('綾波');
//$re = $Aapi->illust_walkthrough();
//$re = $Aapi->illust_new_v1(71422901);
//$re = $Aapi->illust_my_pixiv();
//$re = $Aapi->manga_recommended();
//$re = $Aapi->illust_ranking('2019-09-07');
//$Aapi->json($re->json);




//$re = $Papi->works(73726426);
//$re = $Papi->users(14525258);
//$re = $Papi->me_favorite_works();
//$re = $Papi->me_following_works();
//$re = $Papi->me_following();
//$re = $Papi->me_favorite_users_follow(20228000);
//$re = $Papi->me_favorite_users_unfollow([17560794,263118]);
//$re = $Papi->users_works(16700831);
//$re = $Papi->users_following(16700831);
//$re = $Papi->ranking('2019-09-01');
//$re = $Papi->search_works('碧蓝航线');
//$re = $Papi->latest_works();
```
# 更新日志
* [2019-09-21] 更新多个问题
* [2019-09-20] First Version 
