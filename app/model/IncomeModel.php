<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class IncomeModel extends Model
{
    //所操作的数据表 可选
    protected $table = "income";
    //表的自增ID 可选
    protected $primaryKey = "id";

    const INCOME_TYPE_DOEN   = 1;
    const INCOME_TYPE_REWARD = 2;

    public static $income_types = [
        self::INCOME_TYPE_DOEN   => '下载',
        self::INCOME_TYPE_REWARD => '打赏',
    ];
}
