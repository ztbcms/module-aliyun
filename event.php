<?php
// 事件定义文件
return [
    'bind' => [
        'EditAliyunConfig' => 'app\aliyun\event\EditAliyunConfig'
    ],

    'listen' => [
        'EditAliyunConfig' => [
            'app\aliyun\listener\EditAliyunConfig',
            'app\aliyun\listener\UpdateOssConfig'
        ],
    ],

    'subscribe' => [
    ],
];
