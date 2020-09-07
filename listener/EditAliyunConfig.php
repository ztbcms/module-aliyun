<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-07
 * Time: 10:25.
 */

namespace app\aliyun\listener;


class EditAliyunConfig
{
    public function handle(\app\aliyun\event\EditAliyunConfig $aliyunConfig)
    {
        return true;
    }
}