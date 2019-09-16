<?php

require_once __DIR__ . '/vendor/autoload.php';
use pixiv\Aapi;
use pixiv\Papi;
use pixiv\Ajax;

$ajax = new Ajax();
$cookie = 'first_visit_datetime_pc=2019-08-26+22%3A14%3A50; yuid_b=JZR0gTk; login_ever=yes; first_visit_datetime=2019-09-04+15%3A24%3A19; webp_available=1; limited_ads=%7B%22responsive%22%3A%22%22%2C%22t_footer%22%3A%22%22%2C%22t_header%22%3A%22%22%7D; adr_id=8WDfMu416OgxAfyqhxVe5awTFOeuqZfeFghuXUCddAIX3pNp; ki_s=; ki_r=; categorized_tags=783nSSGzYB~AYsIPsa0jE~EZQqoW9r8g~IVwLyT8B6k~NqnXOnazer~O2wfZxfonb~PHQDP-ccQD~RcahSSzeRf~kP7msdIeEU~m3EJRa33xU~sr5scJlaNv~tt5Ajx8w6V; stacc_mode=unify; p_ab_id=0; p_ab_id_2=9; p_ab_d_id=662782839; login_bc=1; _ga=GA1.2.507026025.1568527329; _gid=GA1.2.1738327487.1568527329; device_token=cfb49e63c7bb82c8e4d757ca5d846a68; privacy_policy_agreement=1; c_type=21; a_type=0; b_type=1; d_type=1; module_orders_mypage=%5B%7B%22name%22%3A%22sketch_live%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22tag_follow%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22recommended_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22everyone_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22following_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22mypixiv_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22spotlight%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22fanbox%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22featured_tags%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22contests%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22user_events%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22sensei_courses%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22booth_follow_items%22%2C%22visible%22%3Atrue%7D%5D; PHPSESSID=14525258_6db1904ce5f8aac11eb3b28023abe10d; cto_lwid=c7eda80a-2384-4763-9ea7-333f34257051; tag_view_ranking=RcahSSzeRf~0xsDLqCEW6~9vxLUp1ZIl~O2wfZxfonb~FqVQndhufZ~ePN3h1AXKX~tgP8r-gOe_~sGIuAFCqQo~mzJgaDwBF5~CwLGRJQEGQ~5oPIfUbtd6~9Nx-lbSnZF~skx_-I2o4Y~PHQDP-ccQD~faHcYIP1U0~X_1kwTzaXt~BaQprNPH_K~6mj7MjhfBu~BDkB6iAivx~ROxQVGB1km~QL2G1t5h_V~RTJMXD26Ak~HrQjOxea1_~Lt-oEicbBr~MM6RXH_rlN~CsNd-tqF1o~mH912ebF42~AYsIPsa0jE~uC2yUZfXDc~sr5scJlaNv~KN7uxuR89w~Ie2c51_4Sp~8Q8mLCEW16~DriUjI1aUj~qJ0gM6EMFd~cicukWJS8-~9AhtMf090f~y8MWY294bC~_vCZ2RLsY2~WeQIp_4Niw~PfIKKVs5MO~zyKU3Q5L4C~SoxapNkN85~HY55MqmzzQ~ZTBAtZUDtQ~gtjRCFKxYq~hTrcyk7mwx~jhuUT0OJva~JuVK_BI_8F~uusOs0ipBx~LLyDB5xskQ~XgZwHIIL4V~92z8RZmGQ6~hrKZno-4iN~ACf5wL_Qh1~5zZwJ9V8tB~kP7msdIeEU~OT4SuGenFI~nBTjJ30nqy~57UnBqevnT~q3eUobDMJW~wmxKAirQ_H~QR9TTbOVZg~d73AdVrO-6~D0nMcn6oGk~kHJk-sR8-P~sCIg_FyUp3~azESOjmQSV~6bgZJCwCr9~x_jB0UM4fe~SqVgDNdq49~zZZn32I7eS~qWFESUmfEs~zwFQGMgFXU~pa4LoD4xuT~LJo91uBPz4~luCRYplxYl~rOnsP2Q5UN~-7RnTas_L3~B_OtVkMSZT~UVAX-XyIfZ~qnRhxi44Zq~cofmB2mV_d~B8Od79JSD7~YTF5zdb95A~Y7VNy8-IBC~3OUk1P5dHM~MkSG_Ne4vr~w03sq0DM-6~BZ5gt2uixh~AAhfsldqxs~lBcRAWFuPM~RokSaRBUGr~4rDNkkAuj_~2TgTsLTv2r~qDqmZnzmtE~nO0M-MaDjP~JoY63qyRCL~CRvSxAm75l~wgrA9w_7EX; cto_bundle=eirXnl9GcWYyYlgyWkM2U0RQSGtPcG1IUSUyQjZIRVNJYVgyd2NQQW1EWDJFbUxJdEdrM2FqTmN1dTF4bXRWZ3ZPajRzWkwlMkZFSXJGZDIyaE52WHdKdjViTkZzZXUwQjhUWllxWTBYMk5DMXglMkYxbjA4NWglMkJxN25SOEo1d21EVmxZb0ZvaXdM; __utmv=235335808.|2=login%20ever=yes=1^3=plan=premium=1^5=gender=male=1^6=user_id=14525258=1^9=p_ab_id=0=1^10=p_ab_id_2=9=1^11=lang=zh=1^20=webp_available=yes=1; viewmode=2; is_sensei_service_user=1; __utma=235335808.507026025.1568527329.1568534458.1568617734.2; __utmc=235335808; __utmz=235335808.1568617734.2.2.utmcsr=embed.pixiv.net|utmccn=(referral)|utmcmd=referral|utmcct=/; __utmt=1; __utmb=235335808.1.10.1568617734; _gat_UA-1830249-138=1; ki_t=1566825319309%3B1568617736548%3B1568617736548%3B13%3B81';
$ajax->set_init($cookie);
# yyyyMMdd
//$json = $ajax->ranking('20190912');
//$json = $ajax->popular_illust();
# $wlt 最小宽度 $wgt 最大宽度
$json = $ajax->search_illusts('八重樱', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);
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
//$json = $Aapi->search_illust($word='八重樱');
//$json = $Aapi->illust_bookmark_detail(76472054);
//$json = $Aapi->illust_bookmark_add(71422901);
//$json = $Aapi->user_bookmark_tags_illust();
//$json = $Aapi->user_following(2374176);
//$json = $Aapi->ugoira_metadata();
//$json = $Aapi->spotlight_articles();
//$json = $Aapi->search_autocomplete('八重樱');
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
//$json = $Papi->search_works('八重樱');
//$json = $Papi->latest_works();
