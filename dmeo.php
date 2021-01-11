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
$cookie = 'first_visit_datetime_pc=2019-12-07+10%3A32%3A51; p_ab_id=8; p_ab_id_2=7; p_ab_d_id=1061917023; yuid_b=EmZ4l5c; _ga=GA1.2.1146425831.1575682398; a_type=0; b_type=1; d_type=1; login_ever=yes; first_visit_datetime=2019-12-16+00%3A17%3A55; c_type=22; ki_r=; __utma=235335808.1146425831.1575682398.1593599909.1593785314.37; __utmv=235335808.|2=login%20ever=yes=1^3=plan=premium=1^5=gender=male=1^6=user_id=14525258=1^9=p_ab_id=8=1^10=p_ab_id_2=7=1^11=lang=zh=1^20=webp_available=yes=1; ki_t=1575682486295%3B1593785528796%3B1593785569430%3B17%3B43; __cfduid=d344bf99f7542e5793585176097f25e281609844161; device_token=9f5d42ba6d145e6b451b2f4dbf4ab934; webp_available=1; PHPSESSID=14525258_qVeiCqwUNCqfiqYRp6CcgBaybGCTPMeA; privacy_policy_agreement=2; __cf_bm=0ff4cf7fd2311185ee6d62025cf042560729119b-1610020766-1800-AdMc03K3DgnJ0e2t23OXIbD9xfRG6+J9iSBXNRl2Drc5uqMdhlyxA1hKUAixcGobZeGFn6iBsjyatArHGJ4HCaakP2IhB7lPu3e/G8oLzbXNhK/JrE1ksbv825B1n21xm2CHcTMMmA6/88oEza72Nxu2CV4p4YCtj5YC8HXKRRkt; tag_view_ranking=RcahSSzeRf~Lt-oEicbBr~RTJMXD26Ak~azESOjmQSV~X_1kwTzaXt~tyFEDMZCsd~tgP8r-gOe_~faHcYIP1U0~skx_-I2o4Y~FqVQndhufZ~lQdVtncC-e~ePN3h1AXKX~5f1R8PG9ra~L58xyNakWW~CwLGRJQEGQ~KN7uxuR89w~K6D8SNtPoY~cxG7coNmIs~nP3sbodsOn~92z8RZmGQ6~QL2G1t5h_V~BaQprNPH_K~9vxLUp1ZIl~jH0uD88V6F~0xsDLqCEW6~wOfMuGXmhr~0nq2PG8L9g~0Sds1vVNKR~kHJk-sR8-P~KOnmT1ndWG~edF4CoWy9T~rNs-bh_gk3~a6S8tZ2AyZ~FgYArp6riX~pYlUxeIoeg~k9aV_5NvhJ~aWHZYZcZDS~K8esoIs2eW~pzzjRSV6ZO~v7Qz4joCBq~GI6A8RKUk_~vPQ1bZT0dv~-oGijJmC5S~sr5scJlaNv~DriUjI1aUj~a-4Y33qTOY~f4KpOuUaPy~L-d833hYKU~jU3DjQclUf~KzkB2Vhfik~uIuRN74qbI~sFPxX8lk4q~HzXfH9KdAp~SoxapNkN85~hqfQUo8KOh~whYxOn_1vl~cFXtS-flQO~DTDROgLuzO~nQRrj5c6w_~suduYyiDRD~2AWg2WU7Ys~lPWnqPImPM~5oPIfUbtd6~iy75VIqVht~Ie2c51_4Sp~PHQDP-ccQD~O2wfZxfonb~8Q8mLCEW16~E1yS6jImG-~qnGN-oHRQo~BLw7uT0VZ9~_pwIgrV8TB~WcUKY2oBz2~Jkh4_cvx3v~jebKEt3Tus~jqUIhfdpt3~QLwW9S4JgF~1mG2DT6YdO~1HSjrqSB3U~q3eUobDMJW~laZrq0wMD9~iFcW6hPGPU~9hv45agt4V~tyTuIaskoS~UnX3AGVmvw~1C7vDIQjoc~QD16w5vsP9~7Ge4qIO6pE~UC34uO_Jam~op-kCuQx_O~PE27h64M4d~D0nMcn6oGk~5zZwJ9V8tB~sCIg_FyUp3~uTH4Uz_x8j~Bh8QslLDbc~gVfGX_rH_Y~x_jB0UM4fe~p7TjX6YIQJ~l5WYRzHH5-';//设置cookie
$ajax->set_init($cookie);
# yyyyMMdd
// $ajax->user_profile_all(10669991);
// $ajax->illust_details('70976538');

// $ajax->ranking('20190912', 'female');
$ajax->search_illusts_pc_v2('刻晴', [
    'ratio'=>0.5,
    'order'=>'popular_d'
]);
$ajax->return_json();
// $ajax->popular_illust();
# $wlt 最小宽度 $wgt 最大宽度
//$ajax->search_illusts('碧蓝航线', 1, 'safe', 's_tag', $p=1, $order=null, $ratio=0.5, $wlt=1920, $wgt=null, $hlt=1080, $hgt=null);

//$ajax->all_activity();
// $ajax->search_illusts_pc_v2('碧蓝航线');
// $ajax->return_json();
// $ajax->search_illusts_pc('プリンツ・オイゲン(アズールレーン)');
//$ajax->tags_frequent_illust('80644077');
// $ajax->user_profile_top('4462245');
//print_r($ajax->json);

// print_r($ajax->ranking()->json);

// $ajax->search_illusts("西木野真姬",['mode'=>'safe','order'=>'popular_d']);
// $url = [];
// foreach($ajax->json['body']['illusts'] as $val){
//     $url[] = $val['url_w'];
// }

// print_r($ajax->download_files($url));
//$ajax->return_json();

// $Aapi = new Aapi();

// $username = '2632324841@qq.com';
// $password = 'wasd123456';
// $Aapi->request_type = 0;
// $Aapi->login($username, $password);
// $Aapi->user_illusts('40291400');
// print_r($Aapi->json);


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
