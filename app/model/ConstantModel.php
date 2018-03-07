<?php

namespace app\model;

use system\Constant;

class ConstantModel extends constant
{
    /** Project */
    const PRO_ADD_ERROR      = 10001;
    const PRO_UPDATE_ERROR   = 10002;
    const PRO_DEL_ERROR      = 10003;
    const FILE_UPLOAD_ERROR  = 10004;
    const FILE_SIZE_ERROR    = 10005;
    const FILE_TYPE_ERROR    = 10006;
    const POINT_LIKE_ERROR   = 10007;
    const POINT_LIKE_ALREADY = 10008;
    const REWARD_ERROR       = 10009;
    const REWARD_SUCCESS     = 10010;
    const REWARD_SELF_ERROR  = 10011;

    /** Comment */
    const COM_SEND_ERROR  = 10100;
    const COM_GET_ERROR   = 10101;
    const SET_SCORE_ERROR = 10102;

    /** Artical */
    const ART_ADD_ERROR    = 10200;
    const ART_UPDATE_ERROR = 10201;
    const ART_DEL_ERROR    = 10202;

    /** E_points */
    const E_POINTS_NOT_ENOUGH = 10300;
    const RECHARGE_FAIL = 10301;
    const RECHARGE_SUCCESS = 10302;

    /** Down */
    const DOWN_ERROR = 10400;

    /** Order */
    const CREATE_ORDER_SUCCESS  = 10500;
    const CREATE_ORDER_ERROR    = 10501;
    const ALREADY_VIP           = 10501;
    const ALREADY_ORDER_WAITING = 10502;
    const UPVIP_ERROR           = 10503;
    const UPVIP_SUCCESS         = 10504;

    /** service */
    const MESSAGE_SEND_ERROR     = 10600;
    const MESSAGE_SEND_TOO_MATCH = 10601;

    /** ErrorMsg */
    public static $codeMsg = [
        self::PRO_ADD_ERROR          => '项目创建失败',
        self::PRO_UPDATE_ERROR       => '项目修改失败',
        self::PRO_DEL_ERROR          => '项目删除失败',
        self::ART_ADD_ERROR          => '文章创建失败',
        self::ART_UPDATE_ERROR       => '文章修改失败',
        self::ART_DEL_ERROR          => '文章删除失败',
        self::FILE_TYPE_ERROR        => '文件格式错误',
        self::FILE_UPLOAD_ERROR      => '文件上传错误',
        self::FILE_SIZE_ERROR        => '文件大小不符合要求',
        self::COM_SEND_ERROR         => '发表评论失败',
        self::COM_GET_ERROR          => '获取评论失败',
        self::SET_SCORE_ERROR        => '评分失败',
        self::POINT_LIKE_ERROR       => '点赞失败',
        self::REWARD_ERROR           => '打赏失败',
        self::REWARD_SUCCESS         => '打赏成功',
        self::REWARD_SELF_ERROR      => '亲，不能够自己打赏自己哦！',
        self::POINT_LIKE_ALREADY     => '您已点赞过该项目',
        self::E_POINTS_NOT_ENOUGH    => '您的E点币不足，请充值后重试',
        self::DOWN_ERROR             => '下载失败，请重试',
        self::CREATE_ORDER_SUCCESS   => '创建订单成功',
        self::CREATE_ORDER_ERROR     => '创建订单失败',
        self::ALREADY_VIP            => '您已经是vip会员',
        self::ALREADY_ORDER_WAITING  => '您尚有未审核的订单',
        self::MESSAGE_SEND_ERROR     => '发送失败',
        self::MESSAGE_SEND_TOO_MATCH => '亲，您今日发送信息过于频繁，请明日再试！',
        self::RECHARGE_FAIL          => '充值失败',
        self::RECHARGE_SUCCESS       => '充值成功',
        self::UPVIP_ERROR            => '升级会员失败',
        self::UPVIP_SUCCESS          => '升级会员成功'
    ];
}