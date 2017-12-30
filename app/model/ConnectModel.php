<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class ConnectModel extends Model
{
    //所操作的数据表 可选
    protected $table = "connects";
    //表的自增ID 可选
    protected $primaryKey = "id";

    const IS_NOT_READ     = 0;
    const IS_ALREADY_READ = 1;

    public static $connect_status = [
        self::IS_NOT_READ     => '未读',
        self::IS_ALREADY_READ => '已读',
    ];
}
