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

    'news/:catId' => 'home/news/index',
    'newsInfo/:id/:catId' => 'home/news/info',

    'products/:catId' => 'home/products/index',
    'products/:id/:catId' => 'home/products/info',

    'case/:catId' => 'home/case/index',
    'caseInfo/:id/:catId' => 'home/case/info',
    'caseload' => 'home/case/load',

    'blog/:catId' => 'home/blog/index',
    'blogInfo/:id/:catId' => 'home/blog/info',

    'about/:catId' => 'home/about/index',

    'services/:catId' => 'home/services/index',
    'servicesInfo/:id/:catId' => 'home/services/info',

    'contact/:catId' => 'home/contact/index',
    'weidianji/' => 'home/weidianji/index',
    'weidianji/dourl' => 'home/weidianji/dourl',
    'weidianjishow/:id' => 'home/weidianji/show',
    'datastatistics/' => 'home/datastatistics/index',
    'datastatistics/dostatistics' => 'home/datastatistics/dostatistics',
];
