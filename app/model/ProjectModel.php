<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class ProjectModel extends Model
{
    //所操作的数据表 可选
    protected $table = "project";
    //表的自增ID 可选
    protected $primaryKey = "pro_id";

    const PRO_IMG_LIMIT = 5;

    const PRO_STATUS_NONE    = 0;
    const PRO_STATUS_SUCCESS = 1;
    const PRO_STATUS_BANED   = 2;

    public static $pro_status = [
        self::PRO_STATUS_NONE    => '审核中',
        self::PRO_STATUS_SUCCESS => '审核通过',
        self::PRO_STATUS_BANED   => '审核未通过',
    ];
}
