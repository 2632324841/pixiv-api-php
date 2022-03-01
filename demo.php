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
$cookie = 'first_visit_datetime_pc=2022-02-24+10%3A14%3A02; p_ab_id=8; p_ab_id_2=2; p_ab_d_id=1922851855; yuid_b=QUIhCUY; __utmz=235335808.1645665250.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); _fbp=fb.1.1645665252024.1061874439; _ga=GA1.2.721881262.1645665250; PHPSESSID=14525258_l8ntvOvYUniRNXZs2lfD7cLbz7KpSSyH; device_token=1627a33797027a870a92bc19c50f242b; privacy_policy_agreement=3; c_type=24; privacy_policy_notification=0; a_type=0; b_type=1; d_type=1; _im_vid=01FWMNF9NEJSPZ4H415RS61RSN; _im_uid.3929=b.4f3b28d8031eb9b3; __utma=235335808.721881262.1645665250.1645665250.1646102631.2; __utmc=235335808; _gid=GA1.2.1846180154.1646102850; login_ever=yes; __cf_bm=aDCHtYjSZ2UCrUUr8z9Uh82s7Xbga.VB4lwOgpA.8Qc-1646105245-0-AR1SpkEjtZXrvTAc7DVbfcfO58zlEqNExZRmnNGjh+zp6VkHwJvaeCosMxdgzdotqTIyBgaHCIy2j4PNtVJkp4z2yctFRwq426mchU2nh1O0THllow9LeS7NDhwSLZhu0MoQBPtId2h0/WwbYhnqCTLR5Lf7tAYQuj/4Mbn5pT1nv+v6p8ivG4XeJU6V38Xxuw==; tag_view_ranking=jhuUT0OJva~RTJMXD26Ak~uusOs0ipBx~2pZ4K1syEF~jH0uD88V6F~0jfWsZgWcW~dqqWNpq7ul~mLrrjwTHBm~9ODMAZ0ebV~8-O04VU9HW~t2ErccCFR9~OEHogw1GmU~qz6SsESFCr~cBvYFULzam~w6DOLSTOSN~yroC1pdUO-~QTtzgGH2pR~RybylJRnhJ~cFYMvUloX0~gnTtYdDB_b~Wxk4MkYNNf~mexrhS8hTj~qtVr8SCFs5~tfLhZBEOFy~ofvnzvil3w~Oa9b6mEc1T~Mg6bq-SpX8~Xs-7j6fVPs~NXxDJr1D_u~jyw53VSia0~_EOd7bsGyl~yREQ8PVGHN~otWaj1bQDp~9Gbahmahac~8Pi-a2KNkx~MnGbHeuS94~ETjPkL0e6r~MAo3w5BNKG~c0WmLsFD2x~iiMm5euRY-~sPZ_q2RU-E~gIBmchMSSr~zj9NiNJUnw~yFS1ZxNCHU~c5Lo7Bkw1H~NhOqpc_-tQ~tvWecJpCWQ~apufn-Lhzk~NXMGEtqFiT~4ofVvnBfOS~KML8PeiHmP~NLhawFANXq~t-Z66XHeRO~ZdAo1fM32m~tlI9YiBhjp~iDJbuf8uNR~fe-e4MqS0B~eQO7Nnq-Qm~Lt-oEicbBr~LoDIs84uJh~YBv-6v5dPZ~1LN8nwTqf_~0QxwCNO6bL~pa4LoD4xuT~qZC8RPnvY8~GmdMh9y_Yz~LJo91uBPz4~JgToEpxSEO~zIv0cf5VVk~Y5SaDKcLRQ~FvLyFRp8Ki~4DteSoih97~aY9er0y-Jk~Spazle1KS8~zLKU_lNVT2~aWHZYZcZDS~QfHe9Nt5we~9CBY4gkwU8~OIUU9JtCV-~f30RUoM3kP~VSI71mhgdM; QSI_S_ZN_5hF4My7Ad6VNNAi=r:10:38; __utmt=1; first_visit_datetime=2022-03-01+12%3A45%3A14; webp_available=1; __utmv=235335808.|2=login%20ever=yes=1^3=plan=normal=1^5=gender=male=1^6=user_id=14525258=1^9=p_ab_id=8=1^10=p_ab_id_2=2=1^11=lang=zh=1^20=webp_available=yes=1; __utmb=235335808.15.9.1646102851935';
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
$ajax->user_profile_top('4462245');
print_r($ajax->json);

// $ajax->search_illusts_pc_v2("西木野真姬",['mode'=>'all','order'=>'popular_d']);

// $url = [];
// foreach($ajax->json['body']['illustManga']['data'] as $val){
//     $url[] = $val['url'];
//     break;
// }
// print_r($url);
// print_r($ajax->download_files($url));
// $ajax->return_json();

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
