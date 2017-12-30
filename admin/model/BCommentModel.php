<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class BCommentModel extends Model
{
    //所操作的数据表 可选
    protected $table = "blog_comment";
    //表的自增ID 可选
    protected $primaryKey = "id";

    const BCOMM_STATUS_NONE    = 0;
    const BCOMM_STATUS_SUCCESS = 1;
    const BCOMM_STATUS_BANED   = 2;

    public static $com_status = [
        self::BCOMM_STATUS_NONE    => '审核中',
        self::BCOMM_STATUS_SUCCESS => '审核通过',
        self::BCOMM_STATUS_BANED   => '审核未通过',
    ];
}
