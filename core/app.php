<?php

namespace system;

use Illuminate\Support\Facades\Request;
use lib\Route;

class App
{
    /**
     * 初始化应用
     */
    public static function initCommon()
    {
        $config = \Init::getConfig();

        // 设置系统时区
        date_default_timezone_set($config['default_timezone']);

        // 应用调试模式
        if ($config['debug']) {
            // 报告所有错误
            error_reporting(E_ALL);
        } else {
            // 关闭错误报告
            error_reporting(0);
        }
    }

    public static function dispatch()
    {
        return Route::dispatch();
    }

    public static function run()
    {
        try {
            // 初始化应用
            self::initCommon();
            // 路由调度
            $dispatch = self::dispatch();
            if ($dispatch === null) {
                echo '发生未知错误！<a href="' . BASE_URL . '">返回</a>';
                exit();
            }
        } catch (\Exception $e) {
            echo '发生未知错误！<a href="' . BASE_URL . '">返回</a>', $e;
            exit();
        }
    }
}

