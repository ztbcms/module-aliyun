<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-07
 * Time: 09:23.
 */

namespace app\aliyun\controller;


use app\aliyun\model\AliyunConfigModel;
use app\common\controller\AdminController;
use app\common\model\ConfigModel;
use think\facade\View;
use think\Request;

class Oss extends AdminController
{
    /**
     * OSS 配置
     * @return string
     */
    public function config()
    {
        $dirverList = [
            'Local' => '本地存储驱动',
            'Ftp' => 'FTP远程附件驱动',
            'Aliyun' => '阿里云OSS上传驱动【暂不支持水印】',
        ];
        $siteConfig = ConfigModel::where('varname', 'like', 'attachment%')->column('value', 'varname');
        $aliyunConfig = AliyunConfigModel::column('value', 'key');
        return View::fetch('config', ['dirverList' => $dirverList, 'siteConfig' => $siteConfig, 'aliyunConfig' => $aliyunConfig]);
    }

    /**
     * 修改OSS网站配置（附件配置）
     * @param Request $request
     * @return array
     */
    public function editConfig(Request $request)
    {
        $postData = $request->post();
        foreach ($postData as $key => $value) {
            ConfigModel::update(['value' => $value], ['varname' => $key]);
        }
        return self::createReturn(true, [], '更新成功');
    }
}