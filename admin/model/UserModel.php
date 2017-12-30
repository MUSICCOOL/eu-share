<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class UserModel extends Model
{
    //所操作的数据表 可选
    protected $table = "user";
    //表的自增ID 可选
    protected $primaryKey = "user_id";

    const IS_NOT_VIP = 0;
    const IS_VIP     = 1;
    const IS_VIP_EXP = 2;

    public static $is_vip = [
        self::IS_NOT_VIP => '非vip会员',
        self::IS_VIP     => 'vip会员',
        self::IS_VIP_EXP => 'vip会员已过期',
    ];

    public static $is_active = [
        0 => '未激活',
        1 => '已激活',
    ];

    public static $sex = [
        1 => '男',
        2 => '女',
    ];
}
