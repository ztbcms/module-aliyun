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
	('access_key_secret', '', 'AccessSecret', '');



CREATE TABLE `cms_tp6_aliyun_sms_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(32) DEFAULT '' COMMENT '模板名称',
  `sign_name` varchar(64) DEFAULT '' COMMENT '签名',
  `template_code` varchar(128) DEFAULT '' COMMENT '模板代码',
  `template_content` varchar(512) DEFAULT '' COMMENT '模板内容',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;