<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class CommentModel extends Model
{
    //所操作的数据表 可选
    protected $table = "comment";
    //表的自增ID 可选
    protected $primaryKey = "com_id";

    public static $com_status = [
        0 => '未审核',
        1 => '审核通过',
        2 => '审核未通过',
    ];
}
