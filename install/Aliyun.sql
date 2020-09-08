CREATE TABLE `cms_tp6_aliyun_config` (
  `key` varchar(32) NOT NULL DEFAULT '' COMMENT '键',
  `value` varchar(256) NOT NULL DEFAULT '' COMMENT '值',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '标题',
  `descrption` varchar(32) NOT NULL DEFAULT '',
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cms_tp6_aliyun_config` (`key`, `value`, `title`, `descrption`)
VALUES
	('access_key_id', '', 'AccessKey', ''),
	('access_key_secret', '', 'AccessSecret', ''),
	('pls_pool_key', '', '号码隐私保护-号码池key', ''),
	('pls_is_recording_enabled', '', '号码隐私保护-是否对通话录音', ''),
	('pls_call_display_type', '', '号码隐私保护-呼叫显示规则', '');


CREATE TABLE `cms_tp6_aliyun_sms_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(32) DEFAULT '' COMMENT '模板名称',
  `sign_name` varchar(64) DEFAULT '' COMMENT '签名',
  `template_code` varchar(128) DEFAULT '' COMMENT '模板代码',
  `template_content` varchar(512) DEFAULT '' COMMENT '模板内容',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `cms_tp6_aliyun_sms_template_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(11) DEFAULT NULL COMMENT '模板id',
  `phone` varchar(32) DEFAULT '' COMMENT '手机号码',
  `params` varchar(1024) DEFAULT '' COMMENT '参数',
  `result_code` varchar(128) DEFAULT '' COMMENT '返回结果code',
  `result_msg` varchar(256) DEFAULT '' COMMENT '返回结果信息',
  `result` varchar(1024) DEFAULT '' COMMENT '返回结果',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;


CREATE TABLE `cms_tp6_aliyun_pls_bind_axb` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `expiration` int(11) DEFAULT '0' COMMENT '过期时间',
  `phone_no_a` varchar(128) DEFAULT '' COMMENT 'A号码',
  `phone_no_b` varchar(128) DEFAULT '' COMMENT 'B号码',
  `phone_no_x` varchar(128) DEFAULT '' COMMENT 'X号码，保护号码',
  `pool_key` varchar(128) DEFAULT '' COMMENT '号码池key',
  `call_display_type` tinyint(3) DEFAULT '1' COMMENT '呼叫显示类型',
  `is_recording_enabled` tinyint(3) DEFAULT '0' COMMENT '是否录音',
  `status` int(11) DEFAULT '0' COMMENT '状态0无效或错误，1绑定成功、2已失效',
  `subs_id` varchar(128) DEFAULT '' COMMENT '绑定关系id，请求成功返回的id',
  `result_msg` varchar(256) DEFAULT '' COMMENT '返回结果',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(11) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;