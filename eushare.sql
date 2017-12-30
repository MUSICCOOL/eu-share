create database eushare;

create table `admin`(
     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员Id',
     `username` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员用户名',
     `password` varchar(50) NOT NULL DEFAULT '' COMMENT '管理员密码',
     `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '管理员电话',
     `email` varchar(100)  NOT NULL DEFAULT '' COMMENT '管理员邮箱',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `admin`(username, password) value ('admin', md5('123456'));

create table `kvmap`(
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '键值对Id',
     `key_name` varchar(50) NOT NULL DEFAULT '' COMMENT '键名',
     `key_title` varchar(100) NOT NULL DEFAULT '' COMMENT '键标题',
     `key_value` varchar(255) NOT NULL DEFAULT '' COMMENT '键值',
     `remark` varchar(50) NOT NULL DEFAULT '' COMMENT '备注',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`),
     UNIQUE INDEX `key_name` (`key_name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

insert into `kvmap`(key_name, key_title, key_value) value ('site_name', '站点名称', 'baseFrame框架');
insert into `kvmap`(key_name, key_title, key_value) value ('site_title', '站点标题', 'baseFrame框架');
insert into `kvmap`(key_name, key_title, key_value) value ('site_keywords', '站点关键词', 'baseFrame');
insert into `kvmap`(key_name, key_title, key_value) value ('site_description', '站点描述', '一个由Ethan开发的框架');

create table `user`(
     `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户Id',
     `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
     `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
     `password` varchar(50) NOT NULL DEFAULT '' COMMENT '用户密码',
     `sex` char(1)  NOT NULL DEFAULT '' COMMENT '性别 1: 男 2：女',
     `avatar` text  COMMENT '用户头像',
     `email` varchar(100)  NOT NULL DEFAULT '' COMMENT '用户邮箱',
     `province` varchar(100)  NOT NULL DEFAULT '' COMMENT '省',
     `city` varchar(100)  NOT NULL DEFAULT '' COMMENT '市',
     `org` varchar(100)  NOT NULL DEFAULT '' COMMENT '学校或公司',
     `intro` varchar(255)  NOT NULL DEFAULT '' COMMENT '简介',
     `e_points` varchar(10) NOT NULL DEFAULT '0' COMMENT 'E点数',
     `vip` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户是否为vip：0：非vip  1:vip   2:vip已过期',
     `vip_exptime` int(10) NOT NULL DEFAULT 0 COMMENT 'vip有效期',
     `privacy` text COMMENT '用户隐私设置  json',
     `token` varchar(50) NOT NULL DEFAULT '' COMMENT '帐号激活码',
     `token_exptime` int(10) NOT NULL DEFAULT 0 COMMENT '激活码有效期',
     `is_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT '用户账号状态：0：未激活  1:已激活',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`user_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000;

create table `project`(
     `pro_id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '项目Id',
     `user_id` int unsigned NOT NULL  DEFAULT 0 COMMENT '项目发布者Id',
     `name` varchar(50) NOT NULL DEFAULT '' COMMENT '项目名称',
     `intro` varchar(100) NOT NULL DEFAULT '' COMMENT '项目简介',
     `type` char(3) NOT NULL DEFAULT '0' COMMENT '项目类型',
     `need_points` varchar(10) NOT NULL DEFAULT '0' COMMENT '下载所需E点数',
     `e_points` varchar(10) NOT NULL DEFAULT '0' COMMENT '收获E点数',
     `desc` text COMMENT '项目描述',
     `imgs` text COMMENT '项目图片  json',
     `file` varchar(255) NOT NULL DEFAULT ''  COMMENT '项目源文件地址',
     `down` char(10) NOT NULL DEFAULT '0' COMMENT '下载数',
     `like` char(10) NOT NULL DEFAULT '0' COMMENT '点赞数',
     `reward` char(10) NOT NULL DEFAULT '0' COMMENT '打赏人数',
     `reward_num`  char(10) NOT NULL DEFAULT '0' COMMENT '该项目打赏数额',
     `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '项目审核状态：0：未审核  1:审核通过  2:审核不通过',
     `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注，审核说明',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`pro_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000;

create table `type`(
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '类型ID',
  `p_id` int unsigned NOT NULL DEFAULT 0 COMMENT '父类型Id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '类型名称',
  `order` int unsigned NOT NULL DEFAULT 0 COMMENT '排序号',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `recharge`(
    `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `user_id` int unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
    `amount` varchar(10) NOT NULL DEFAULT '0' COMMENT '充值金额',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `consumer`(
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `pro_id` int unsigned NOT NULL COMMENT '被下载/打赏项目Id',
  `type` char(3) NOT NULL DEFAULT '0' COMMENT '消费类型id, 1：下载 2：打赏',
  `e_points` varchar(10) NOT NULL DEFAULT '0' COMMENT '消费数额',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `income`(
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int unsigned NOT NULL DEFAULT 0 COMMENT '被下载/打赏用户ID',
  `pro_id` int unsigned NOT NULL COMMENT '被下载/打赏项目Id',
  `u_id` int unsigned NOT NULL DEFAULT 0 COMMENT '下载/打赏用户ID',
  `type` char(3) NOT NULL DEFAULT '0' COMMENT '收益类型id, 1：下载 2：打赏',
  `e_points` varchar(10) NOT NULL DEFAULT '0' COMMENT '收益数额',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `glike`(
    `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
    `pro_id` int unsigned NOT NULL COMMENT '项目Id',
    `user_id` int unsigned NOT NULL  DEFAULT 0 COMMENT '点赞人Id',
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `down`(
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pro_id` int  unsigned NOT NULL  COMMENT '项目id',
  `user_id` int  unsigned NOT NULL  COMMENT '下载人Id',
  `ip` char(20) NOT NULL DEFAULT '' COMMENT '下载人ip',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `reward`(
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pro_id` int  unsigned NOT NULL  COMMENT '项目id',
  `user_id` int  unsigned NOT NULL  COMMENT '打赏人Id',
  `reward_num` varchar(10) NOT NULL DEFAULT '0' COMMENT '打赏数额',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `comment`(
     `com_id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '评论Id',
     `user_id` int unsigned NOT NULL COMMENT '用户Id',
     `pro_id` int unsigned NOT NULL  COMMENT '项目Id',
     `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
     `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '审核状态：0：未审核  1:审核通过  2:审核不通过',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`com_id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `orders`(
     `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
     `type` char(3) NOT NULL DEFAULT '0' COMMENT '订单类型id',
     `user_id` int unsigned NOT NULL  COMMENT '用户id',
     `order_num` char(10) NOT NULL DEFAULT '' COMMENT '转账单号后6位',
     `amount` varchar(10) NOT NULL DEFAULT '0' COMMENT '金额',
     `ip` char(20) NOT NULL DEFAULT '' COMMENT '用户ip',
     `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核状态：0：未审核  1:审核通过  2:审核不通过',
     `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注，审核说明',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000;

create table `connects`(
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT 'Id',
     `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
     `content` text COMMENT '内容',
     `user_id` int unsigned NOT NULL  COMMENT '用户id',
     `ip` char(20) NOT NULL DEFAULT '' COMMENT '用户ip',
     `status` varchar(10) NOT NULL DEFAULT '0' COMMENT '是否已读',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `blog`(
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '博客Id',
     `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
     `keywords` varchar(50) NOT NULL  COMMENT '关键词',
     `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '文章简介',
     `content` longtext COMMENT 'blog内容',
     `read` varchar(10) NOT NULL DEFAULT '0' COMMENT '文章阅读数',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `blog_read`(
     `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
     `b_id` int unsigned NOT NULL  COMMENT 'blog Id',
     `ip` char(20) NOT NULL DEFAULT '' COMMENT '浏览人ip',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

create table `blog_comment`(
     `id` int unsigned NOT NULL AUTO_INCREMENT  COMMENT '评论Id',
     `user_id` int unsigned NOT NULL COMMENT '用户Id',
     `b_id` int unsigned NOT NULL  COMMENT 'blog Id',
     `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
     `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '审核状态：0：未审核  1:审核通过  2:审核不通过',
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间',
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
     PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


