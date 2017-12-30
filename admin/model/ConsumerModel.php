<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class ConsumerModel extends Model
{
    //所操作的数据表 可选
    protected $table = "consumer";
    //表的自增ID 可选
    protected $primaryKey = "id";

    const CONSUMER_TYPE_DOEN   = 1;
    const CONSUMER_TYPE_REWARD = 2;

    public static $consumer_types = [
        self::CONSUMER_TYPE_DOEN   => '下载',
        self::CONSUMER_TYPE_REWARD => '打赏',
    ];
}
