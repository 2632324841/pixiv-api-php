<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();

$promises = [
    'image' => $client->getAsync('https://i.pximg.net/img-master/img/2014/12/28/00/03/10/47808763_p0_master1200.jpg',['headers'=> [ 'Referer' => 'https://app-api.pixiv.net/' ],'save_to'=> '1.jpg']),
    'png'   => $client->getAsync('https://i.pximg.net/img-master/img/2017/01/01/00/00/40/60680252_p0_master1200.jpg',['headers'=> [ 'Referer' => 'https://app-api.pixiv.net/' ],'save_to'=> '2.jpg']),
    'jpeg'  => $client->getAsync('https://i.pximg.net/img-master/img/2014/01/01/08/44/32/40667832_p0_master1200.jpg',['headers'=> [ 'Referer' => 'https://app-api.pixiv.net/' ],'save_to'=> '3.jpg']),
];

// Wait on all of the requests to complete.
$results = Promise\unwrap($promises);

//print_r($results['image']->getUrl());
//print_r($results['png']->getHeader('Content-Length'));