<?php

namespace app\model;

use system\Model;

//继承父类并创建数据库链接
class TypeModel extends Model
{
    //所操作的数据表 可选
    protected $table = "type";
    //表的自增ID 可选
    protected $primaryKey = "id";
}
