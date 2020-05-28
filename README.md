# pixiv-api-php
这是一个Pixiv IOS API 
类似于[upbit/pixivpy](https://github.com/upbit/pixivpy)

## 使用composer 安装
> composer require deadlymous/pixiv_api --dev
*****
建议PHP版本 7.2+
## 实例
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
//引用Api

use pixiv\Aapi;
use pixiv\Papi;
use pixiv\Ajax;
//创建Api Ajax对象
$ajax = new Ajax();
//设置cookie
$cookie = '你登录P站后的页面cookie';
//设置cookie
$ajax->set_init($cookie);
//调用Pixiv 接口类型
$ajax->user_history('illust',0);
//返回的数组
print_r($ajax->json);
//可以快速打印JSON
$ajax->return_json();
//如果是其他Api
$Aapi = new Aapi();
$username = '你的邮箱';
$password = '你的密码';
//该设置已经弃用
//$Aapi->request_type = 0;
//登录
$Aapi->login($username, $password);
//用户作品列表
$Aapi->user_illusts('40291400');
print_r($Aapi->json);

```
# 更新日志

* [2020-05-28] 修改Api 请求 优化Ajax Api
* [2020-04-20] 修复接口问题 DNS解析IP访问有SSL握手错误
* [2019-12-16] 添加动态接口
* [2019-XX-XX] 忘记是啥时候了，修复登录验证问题
* [2019-09-24] 添加动图下载
* [2019-09-26] 添加动态图下载
* [2019-09-21] 更新多个问题
* [2019-09-20] First Version 
