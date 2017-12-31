<?php
/**
 * Created by PhpStorm.
 * User: Ethan
 * Date: 2017/11/11
 * Time: 10:53
 */

namespace lib;


class Route
{
    const MODE_NORMAL  = 1; //路由模式1 index.php?c=xx&&a=xx&p1=xx
    const MODE_PHPINFO = 2; //路由模式2 index.php/c/xx/a/xx/p1/xx

    public static function getCA($uri)
    {
        $mode = \Init::getConfig("url_mode");
        $c    = $a = '';
        if ($mode == self::MODE_PHPINFO) {
            $parts = self::getUriParts($uri);
            $c     = PROJECT . "\\controller\\" . (empty($parts[0]) ? 'Index' : ucfirst($parts[0])) . "Controller";
            $a     = empty($parts[1]) ? 'Index' : ucfirst($parts[1]);
        } else {
            $c = PROJECT . "\\controller\\" . (empty($_GET['c']) ? 'Index' : ucfirst($_GET['c'])) . "Controller";
            $a = empty($_GET['a']) ? 'Index' : ucfirst($_GET['a']);
        }
        return ['c' => $c, 'a' => $a];
    }

    public static function getQuerys()
    {
        $query = [];
        $mode  = \Init::getConfig("url_mode");
        if ($mode == self::MODE_PHPINFO) {
            $uri   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $parts = self::getUriParts($uri);
            for ($i = 2; $i < count($parts);) {
                $query[strtolower($parts[$i])] = trim($parts[$i + 1]);
                $i                             += 2;
            }
            $query['c'] = empty($parts[0]) ? 'Index' : ucfirst($parts[0]);
            $query['a'] = empty($parts[1]) ? 'Index' : ucfirst($parts[1]);
        }

        return $query;
    }

    public static function getUriParts($uri)
    {

        $parts = explode('/', trim($uri, '/'));
        return $parts;
    }

    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $ca  = self::getCA($uri);
        // 调用类方法 分发
        return call_user_func_array(array(new $ca['c'], $ca['a']), array());
    }

}