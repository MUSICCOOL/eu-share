<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class CommentModel extends Model
{
    //所操作的数据表 可选
    protected $table = "comment";
    //表的自增ID 可选
    protected $primaryKey = "com_id";

    const COM_STATUS_NONE    = 0;
    const COM_STATUS_SUCCESS = 1;
    const COM_STATUS_BANED   = 2;

    public static $com_status = [
        self::COM_STATUS_NONE    => '未审核',
        self::COM_STATUS_SUCCESS => '审核通过',
        self::COM_STATUS_BANED   => '审核未通过',
    ];
}
