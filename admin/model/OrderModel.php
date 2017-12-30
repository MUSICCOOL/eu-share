<?php

namespace admin\model;

use system\Model;

//继承父类并创建数据库链接
class OrderModel extends Model
{
    //所操作的数据表 可选
    protected $table = "orders";
    //表的自增ID 可选
    protected $primaryKey = "id";

    const ORDER_STATUS_NO  = 0;
    const ORDER_STATUS_SUC = 1;
    const ORDER_STATUS_ERR = 2;

    public static $order_status = [
        self::ORDER_STATUS_NO  => '未审核',
        self::ORDER_STATUS_SUC => '审核通过',
        self::ORDER_STATUS_ERR => '审核未通过',
    ];

    const ORDER_TYPE_VIP_30       = 0;
    const ORDER_TYPE_VIP_90       = 1;
    const ORDER_TYPE_VIP_180      = 2;
    const ORDER_TYPE_VIP_360      = 3;
    const ORDER_TYPE_RECHARGE_10  = 4;
    const ORDER_TYPE_RECHARGE_20  = 5;
    const ORDER_TYPE_RECHARGE_50  = 6;
    const ORDER_TYPE_RECHARGE_100 = 7;

    public static $order_type = [
        self::ORDER_TYPE_VIP_30       => '30天vip会员',
        self::ORDER_TYPE_VIP_90       => '90天vip会员',
        self::ORDER_TYPE_VIP_180      => '180天vip会员',
        self::ORDER_TYPE_VIP_360      => '360天vip会员',
        self::ORDER_TYPE_RECHARGE_10  => '充值100E点币',
        self::ORDER_TYPE_RECHARGE_20  => '充值200E点币',
        self::ORDER_TYPE_RECHARGE_50  => '充值500E点币',
        self::ORDER_TYPE_RECHARGE_100 => '充值1000E点币',
    ];

    public static $order_type_vip = [
        self::ORDER_TYPE_VIP_30,
        self::ORDER_TYPE_VIP_90,
        self::ORDER_TYPE_VIP_180,
        self::ORDER_TYPE_VIP_360,
    ];

    public static $order_type_rec = [
        self::ORDER_TYPE_RECHARGE_10,
        self::ORDER_TYPE_RECHARGE_20,
        self::ORDER_TYPE_RECHARGE_50,
        self::ORDER_TYPE_RECHARGE_100,
    ];
}
