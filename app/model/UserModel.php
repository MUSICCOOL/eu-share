<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class UserModel extends Model
{
    //所操作的数据表 可选
    protected $table = "user";
    //表的自增ID 可选
    protected $primaryKey = "user_id";

    const PAASSWORD_MIN_LENGTH = 6;  // 密码最小位数

    const DEFAULT_USER_AVATAR = '/public/img/default-avatar.jpg';

    const IS_MAN   = 1;
    const IS_FEMAL = 2;

    const IS_NOT_ACTIVE = 0;
    const IS_ACTIVE     = 1;

    const IS_NOT_VIP = 0;
    const IS_VIP     = 1;
    const IS_VIP_EXP = 2;

    public static $sex_type = [
        self::IS_MAN   => '男',
        self::IS_FEMAL => '女',
    ];

    public static $is_active = [
        self::IS_NOT_ACTIVE => '未激活',
        self::IS_ACTIVE     => '已激活',
    ];

    public static $is_vip = [
        self::IS_NOT_VIP => '非vip会员',
        self::IS_VIP     => 'vip会员',
        self::IS_VIP_EXP => 'vip会员已过期',
    ];
}
