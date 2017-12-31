<?php
return [
    'site_url'         => 'http://dev.eu-share.com/',
    'db'               => [
        'driver'    => 'mysql',
        'host'      => '127.0.0.1',
        'database'  => 'xian',
        'username'  => 'root',
        'password'  => 'a98b6532',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ],
    'debug'            => true, // 调试模式
    'default_timezone' => 'Asia/Shanghai', // 设置系统时区
    'url_mode'         => 2, // url路由模式  1：index.php?c=xx&&a=xx&p1=xx  2: index.php/c/xx/a/xx/p1/xx
    'view'             => [
        'path'  => PROJECT . "/view/",
        'cache' => PROJECT . "/cache/",
    ],
    'upload_dir'       => [
        'base'   => '/public/upload/',
        'tmp'    => 'tmp',
        'images' => 'images',
        'files'  => 'files',
    ],
    'upload_type'      => [
        'images' => '.gif|.jpeg|.jpg|.png|.bmp',
        'files'  => '.txt|.css|.js|.xml|.html|.htm|.xhtml|.c|.cpp|.java|.php|.go|.sh|.zip|.rar',
    ],
    'log'              => PROJECT . "/log/",
    'mail'             => [
        'debug'    => 0, //是否启用smtp的debug进行调试 默认关闭debug调试模式 debug = 0
        'host'     => 'smtp.yeah.net',
        'port'     => 587,
        'username' => '',
        'password' => '',
        'address'  => '',
        'name'     => '',
    ],
];

// 使用方法e.g. \Init::getConfig("db.username")
