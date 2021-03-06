<?php

return [
    [
        //父菜单ID，NULL或者不写系统默认，0为顶级菜单
        "parentid" => 0,
        //地址，[模块/]控制器/方法
        "route" => "aliyun/setting/index",
        //类型，1：权限认证+菜单，0：只作为菜单
        "type" => 0,
        //状态，1是显示，0不显示（需要参数的，建议不显示，例如编辑,删除等操作）
        "status" => 1,
        //名称
        "name" => "阿里云",
        //备注
        "remark" => "",
        //子菜单列表
        "child" => [
            [
                "route" => "aliyun/setting/index",
                "type" => 1,
                "status" => 1,
                "name" => "设置",
                "remark" => ""
            ],
            [
                "route" => "aliyun/sms/index",
                "type" => 1,
                "status" => 1,
                "name" => "SMS",
                "remark" => "",
                "child" => [
                    [
                        "route" => "aliyun/sms/index",
                        "type" => 1,
                        "status" => 1,
                        "name" => "模板列表",
                        "remark" => ""
                    ],
                    [
                        "route" => "aliyun/sms/records",
                        "type" => 1,
                        "status" => 1,
                        "name" => "发送记录",
                        "remark" => ""
                    ],
                ],
            ],
            [
                "route" => "aliyun/oss/config",
                "type" => 1,
                "status" => 1,
                "name" => "OSS",
                "remark" => "",
            ],
            [
                "route" => "aliyun/pls/config",
                "type" => 1,
                "status" => 1,
                "name" => "PLS",
                "remark" => "",
                "child" => [
                    [
                        "route" => "aliyun/pls/config",
                        "type" => 1,
                        "status" => 1,
                        "name" => "配置",
                        "remark" => ""
                    ],
                    [
                        "route" => "aliyun/pls/index",
                        "type" => 1,
                        "status" => 1,
                        "name" => "绑定记录",
                        "remark" => ""
                    ],
                ],
            ],
        ]
    ],
];
