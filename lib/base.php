<?php

// 这个定义linux和windows中的引入路径的分隔符 linux \ windows /
define("DIR_SIGN", DIRECTORY_SEPARATOR);

// 定义站点目录
define("ROOT_PATH", dirname(__DIR__) . DIR_SIGN);

// 定义应用目录
define("APP_PATH", ROOT_PATH . DIR_SIGN . PROJECT . DIR_SIGN);

// 系统常量定义
define("PUBLIC_PATH", ROOT_PATH . 'public' . DIR_SIGN);
define("UPLOAD_PATH", PUBLIC_PATH . 'upload' . DIR_SIGN);


if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $url_type = 'https';
} else {
    $url_type = 'http';
}
define("BASE_URL", $url_type . '://' . $_SERVER['SERVER_NAME'] . '/');
