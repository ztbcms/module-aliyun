<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-07
 * Time: 11:35.
 */

namespace app\aliyun\listener;


use app\common\model\ConfigModel;

/**
 * 阿里云配置修改成功，统一修改网站阿里云配置
 * Class UpdateOssConfig
 * @package app\aliyun\listener
 */
class UpdateOssConfig
{
    public function handle(\app\aliyun\event\EditAliyunConfig $aliyunConfig)
    {
        $aliyunConfig->getAccessKeyId();
        $aliyunConfig->getAccessKeySecret();

        ConfigModel::where('varname', 'attachment_aliyun_key_id')->update([
            'value' => $aliyunConfig->getAccessKeyId()
        ]);
        ConfigModel::where('varname', 'attachment_aliyun_key_secret')->update([
            'value' => $aliyunConfig->getAccessKeySecret()
        ]);
        
        return true;
    }
}