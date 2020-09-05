<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-05
 * Time: 13:41.
 */

namespace app\aliyun\controller;


use app\aliyun\model\AliyunConfigModel;
use app\common\controller\AdminController;
use think\facade\View;
use think\Request;

class Setting extends AdminController
{
    /**
     * 编辑配置
     * @param Request $request
     * @return array
     */
    function editConfig(Request $request)
    {
        $postData = $request->post();
        foreach ($postData as $key => $value) {
            AliyunConfigModel::where('key', $key)->update([
                'value' => $value
            ]);
        }
        return self::createReturn(true, $postData, '保存成功');
    }

    /**
     * 设置
     * @return string
     */
    function index()
    {
        $config = AliyunConfigModel::column('value', 'key');
        return View::fetch('index', ['config' => $config]);
    }
}