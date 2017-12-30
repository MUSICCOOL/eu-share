<?php

class Init
{
    public static $config = array();

    public static function getConfig($map = '')
    {
        if (empty(self::$config)) {
            self::$config = include_once(ROOT_PATH . "core" . DIR_SIGN . "config.php");
        }
        if (empty($map)) {
            return self::$config;
        } else {
            $map = explode(".", trim($map, '.'));
            $arr = self::$config;
            foreach ($map as $k) {
                $arr = $arr[$k];
            }
            return $arr;
        }

    }

}
