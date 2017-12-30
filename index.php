<?php
session_start();

// 定义项目目录名称
define("PROJECT", 'app');

// 引入系统常量
include_once("lib" . DIRECTORY_SEPARATOR . "base.php");

// 自动引入类文件
include_once("vendor" . DIRECTORY_SEPARATOR . "autoload.php");

// 运行
\system\App::run();