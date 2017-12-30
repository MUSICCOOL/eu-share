<?php

namespace system;

;

class Constant
{
    /** whole */
    const DEFAULR_PER_PAGE_COUNT   = 10;
    const IMAGE_ALLOW_UPLOAD_SIZE  = 2000000; // 2MB 允许上传的最大图片尺寸
    const FILE_ALLOW_UPLOAD_SIZE   = 20000000; // 20MB 允许上传的最大文件尺寸
    const FILE_UPLOAD_PATH         = '/public/upload/files/';  // 普通文件上传路径
    const IMAGE_UPLOAD_PATH        = '/public/upload/images/';  // 普通图片上传路径
    const AVATAR_UPLOAD_PATH       = '/public/upload/avatar/';  // 头像上传路径
    const PAYWAY_IMAGE_UPLOAD_PATH = '/public/upload/payway/';  // 收款图片上传路径

    /** Code */
    const SUCCESS     = 0;
    const FALSE       = 1;
    const PARAM_ERROR = 2;

    /** Success 1~ */
    const ADD_SUCCESS    = 1000;
    const EDIT_SUCCESS   = 1001;
    const UPDATE_SUCCESS = 1002;
    const DELETE_SUCCESS = 1003;

    /** Error 2~ */
    const ADD_ERROR                    = 2000;
    const EDIT_ERROR                   = 2001;
    const UPDATE_ERROR                 = 2002;
    const DELETE_ERROR                 = 2003;
    const IMAGE_TYPE_ERROR             = 2004;
    const IMAGE_UPLOAD_ERROR           = 2005;
    const IMAGE_SIZE_ERROR             = 2006;
    const FILE_UPLOAD_ERROR            = 2007;
    const FILE_SIZE_ERROR              = 2008;
    const FILE_TYPE_ERROR              = 2009;
    const USERNAME_ALREADY_EXISTS      = 2011;
    const NICKNAME_ALREADY_EXISTS      = 2012;
    const PASSWORD_LEN_ERROR           = 2013;
    const PASSWORD_CONFIRMED_ERROR     = 2014;
    const EMAIL_FORMAT_ERROR           = 2015;
    const EMAIL_ALREADY_EXISTS         = 2016;
    const LOGIN_ERROR                  = 2017;
    const OLD_PASSWORD_ERROR           = 2018;
    const PASSWORD_CHANGE_ERROR        = 2019;
    const AVATAR_UPLOAD_ERROR          = 2020;
    const AVATAT_CHANGE_ERROR          = 2021;
    const NOT_LOGIN_YET_ERROR          = 2022;
    const EMAIL_NOT_EXISTS             = 2023;
    const EMAIL_OR_NICKNAME_NOT_EXISTS = 2024;
    const ACTIVE_MAIL_SEND_ERROR       = 2025;
    const USER_NOT_ACTIVE              = 2026;


    /** ErrorMsg */
    public static $codeMsg = [
        // success
        self::SUCCESS                      => '成功',
        self::ADD_SUCCESS                  => '添加成功',
        self::PARAM_ERROR                  => '参数错误',
        self::EDIT_SUCCESS                 => '编辑成功',
        self::UPDATE_SUCCESS               => '更新成功',
        self::DELETE_SUCCESS               => '删除成功',
        // error
        self::FALSE                        => '未知错误',
        self::ADD_ERROR                    => '添加错误',
        self::EDIT_ERROR                   => '编辑错误',
        self::UPDATE_ERROR                 => '更新错误',
        self::DELETE_ERROR                 => '删除错误',
        self::IMAGE_TYPE_ERROR             => '图片格式错误',
        self::IMAGE_UPLOAD_ERROR           => '图片上传错误',
        self::IMAGE_SIZE_ERROR             => '图片大小不符合要求',
        self::FILE_TYPE_ERROR              => '文件格式错误',
        self::FILE_UPLOAD_ERROR            => '文件上传错误',
        self::FILE_SIZE_ERROR              => '文件大小不符合要求',
        self::NOT_LOGIN_YET_ERROR          => '亲！您还没有登录哦',
        self::EMAIL_NOT_EXISTS             => '该邮箱不存在',
        self::EMAIL_OR_NICKNAME_NOT_EXISTS => '邮箱或昵称不存在',
        self::PASSWORD_CONFIRMED_ERROR     => '两次密码输入不一致',
        self::EMAIL_FORMAT_ERROR           => '邮箱格式错误',
        self::EMAIL_ALREADY_EXISTS         => '该邮箱已注册',
        self::USERNAME_ALREADY_EXISTS      => '该用户名已存在',
        self::NICKNAME_ALREADY_EXISTS      => '该昵称已存在',
        self::PASSWORD_LEN_ERROR           => '密码长度不符合要求',
        self::LOGIN_ERROR                  => '用户名或密码错误',
        self::OLD_PASSWORD_ERROR           => '原始密码输入错误',
        self::PASSWORD_CHANGE_ERROR        => '密码修改失败',
        self::AVATAR_UPLOAD_ERROR          => '头像上传失败',
        self::AVATAT_CHANGE_ERROR          => '头像修改失败',
        self::ACTIVE_MAIL_SEND_ERROR       => '激活邮件发送失败，请确定邮箱是否正确',
        self::USER_NOT_ACTIVE              => '抱歉，您的账号尚未激活',
    ];
}