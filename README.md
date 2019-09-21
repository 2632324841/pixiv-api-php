# pixiv-api-php
这是一个Pixiv IOS API 
类似于[upbit/pixivpy](https://github.com/upbit/pixivpy)

## 安装
> composer require deadlymous/pixiv_api

## 实例
```php
require_once __DIR__ . '/vendor/autoload.php';
use pixiv\Aapi;
use pixiv\Papi;

$Aapi = new Aapi();
$username = '';
$password = '';
$Aapi->login($username, $password);

$json = $Aapi->user_illusts('40291400');
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
$Aapi->json($json);



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
```
# 更新日志
* [2019-09-20] First Version 
