<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
        'catId' => '\d+',
    ],
    '[hello]'     => [
        ':id'   => ['home/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['home/hello', ['method' => 'post']],
    ],

    'index' => 'home/index/index',
    'book' => 'home/index/book',
    'vip'=>'home/index/vip',
    'activity' => 'home/index/activity',
    'picture/:catId' => 'home/picture/index',
    'caseInfo/:id/:catId' => 'home/case/info',
    'caseload' => 'home/case/load',
    'about/:catId' => 'home/about/index',
    'service/:catId' => 'home/service/index',
    'activity/:catId' => 'home/activity/index',

    'datastatistics/' => 'home/datastatistics/index',
    'datastatistics/dostatistics' => 'home/datastatistics/dostatistics',
];
