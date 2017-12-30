<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class AdminModel extends Model
{
    //所操作的数据表 可选
    protected $table = "admin";
    //表的自增ID 可选
    protected $primaryKey = "id";
    // 默认分页每页条数
    const DEFAULT_COUNT = 10;
}
