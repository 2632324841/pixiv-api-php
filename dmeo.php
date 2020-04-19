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


$Aapi = new Aapi();

//$Papi = new Papi();


// $ajax = new Ajax();

// //设置 页面上的cookie
// $cookie = 'first_visit_datetime_pc=2019-12-07+10%3A32%3A51; p_ab_id=8; p_ab_id_2=7; p_ab_d_id=1061917023; yuid_b=EmZ4l5c; _ga=GA1.2.1146425831.1575682398; PHPSESSID=14525258_d8S8EfXP8SNyUIqWqQjuLNuau6KDkPiU; privacy_policy_agreement=1; a_type=0; b_type=1; d_type=1; module_orders_mypage=%5B%7B%22name%22%3A%22sketch_live%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22tag_follow%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22recommended_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22everyone_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22following_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22mypixiv_new_illusts%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22spotlight%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22fanbox%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22featured_tags%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22contests%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22user_events%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22sensei_courses%22%2C%22visible%22%3Atrue%7D%2C%7B%22name%22%3A%22booth_follow_items%22%2C%22visible%22%3Atrue%7D%5D; login_ever=yes; first_visit_datetime=2019-12-16+00%3A17%3A55; __utmv=235335808.|2=login%20ever=yes=1^3=plan=premium=1^5=gender=male=1^6=user_id=14525258=1^9=p_ab_id=8=1^10=p_ab_id_2=7=1^11=lang=zh=1^20=webp_available=yes=1; c_type=22; ki_r=; webp_available=1; __utmz=235335808.1584447537.21.2.utmcsr=saucenao.com|utmccn=(referral)|utmcmd=referral|utmcct=/search.php; __cfduid=d0de1c9313775de75206d4d4a612fad9c1584967075; tag_view_ranking=RcahSSzeRf~azESOjmQSV~RTJMXD26Ak~X_1kwTzaXt~Lt-oEicbBr~tgP8r-gOe_~faHcYIP1U0~lQdVtncC-e~skx_-I2o4Y~5f1R8PG9ra~L58xyNakWW~FqVQndhufZ~KN7uxuR89w~ePN3h1AXKX~K6D8SNtPoY~cxG7coNmIs~QL2G1t5h_V~tyFEDMZCsd~BaQprNPH_K~9vxLUp1ZIl~CwLGRJQEGQ~wOfMuGXmhr~0nq2PG8L9g~0Sds1vVNKR~kHJk-sR8-P~KOnmT1ndWG~nP3sbodsOn~edF4CoWy9T~92z8RZmGQ6~rNs-bh_gk3~a6S8tZ2AyZ~0xsDLqCEW6~sFPxX8lk4q~HzXfH9KdAp~SoxapNkN85~hqfQUo8KOh~whYxOn_1vl~cFXtS-flQO~DTDROgLuzO~nQRrj5c6w_~suduYyiDRD~2AWg2WU7Ys~lPWnqPImPM~5oPIfUbtd6~iy75VIqVht~Ie2c51_4Sp~PHQDP-ccQD~O2wfZxfonb~8Q8mLCEW16~iFcW6hPGPU~9hv45agt4V~tyTuIaskoS~UnX3AGVmvw~1C7vDIQjoc~QD16w5vsP9~7Ge4qIO6pE~UC34uO_Jam~op-kCuQx_O~PE27h64M4d~D0nMcn6oGk~5zZwJ9V8tB~sCIg_FyUp3~uTH4Uz_x8j~Bh8QslLDbc~gVfGX_rH_Y~x_jB0UM4fe~p7TjX6YIQJ~l5WYRzHH5-~k9aV_5NvhJ~aWHZYZcZDS~K8esoIs2eW~pzzjRSV6ZO~v7Qz4joCBq~GI6A8RKUk_~jH0uD88V6F~vPQ1bZT0dv~uusOs0ipBx~c8naDa4MHt~2pZ4K1syEF~LoDIs84uJh~qNQ253s6b0~tbxJEAQwBq~bEUCIgwW87~XgZwHIIL4V~qcYo_5oqVP~OSFTv5LVaA~ds1AaoUmRE~iu_hVLSvvt~wlptP2po99~bYIrirI4ou~gHS0ZWTpNp~zyKU3Q5L4C~JO16HzBgpd~BizgwLIkwd~znkteK6abh~6Qlk7O6tC8~28gdfFXlY7~zdXpSlHEIZ~JoY63qyRCL~FgYArp6riX; ki_t=1575682486295%3B1585375067723%3B1585389421438%3B16%3B41; stacc_mode=stream2; is_sensei_service_user=1; __utma=235335808.1146425831.1575682398.1585388320.1587301397.33; __utmc=235335808; __utmt=1; __utmb=235335808.2.9.1587301401033; _fbp=fb.1.1587301419883.375423231';
// //设置cookie
// $ajax->set_init($cookie);
//# yyyyMMdd
//$ajax->ranking('20190912');
//$ajax->popular_illust();
//# $wlt 最小宽度 $wgt 最大宽度
// $ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);

// //$ajax->all_activity();
// $ajax->search_illusts_pc_v2('碧蓝航线');
// $ajax->return_json();
//$ajax->search_illusts_pc('プリンツ・オイゲン(アズールレーン)');
//print_r($ajax->json);


//$Aapi = new Aapi();

$username = '2632324841@qq.com';
$password = 'wasd123456';
$Aapi->request_type = 0;
$Aapi->login($username, $password);
//$Aapi->user_illusts('40291400');
//print_r($Aapi->json);


//echo $Aapi->StatusCode;

//下载动图
$Aapi->ugoira_meta_save(72729032);
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
