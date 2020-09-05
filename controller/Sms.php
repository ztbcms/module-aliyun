<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-05
 * Time: 14:35.
 */

namespace app\aliyun\controller;

use app\aliyun\model\AliyunSmsTemplateLogModel;
use app\aliyun\model\AliyunSmsTemplateModel;
use app\aliyun\service\AliyunSmsService;
use app\common\controller\AdminController;
use think\facade\View;
use think\Request;

class Sms extends AdminController
{
    /**
     * 展示发送日志
     * @param Request $request
     * @throws \think\db\exception\DbException
     * @return array|string
     */
    function records(Request $request)
    {
        if ($request->isAjax()) {
            $where = [];
            $lists = AliyunSmsTemplateLogModel::where($where)->order('id', 'DESC')->paginate(20);
            return self::createReturn(true, $lists, 'ok');
        }
        return View::fetch('records');
    }

    /**
     * 创建、编辑模板
     * @param Request $request
     * @return array|string
     */
    function createTemplate(Request $request)
    {
        if ($request->isPost()) {
            $templateId = $request->post('template_id');

            $smsTemplate = AliyunSmsTemplateModel::where('id', $templateId)->findOrEmpty();
            $smsTemplate->template_name = $request->post('template_name');
            $smsTemplate->sign_name = $request->post('sign_name');
            $smsTemplate->template_code = $request->post('template_code');
            $smsTemplate->template_content = $request->post('template_content');
            if ($smsTemplate->save()) {
                return self::createReturn(true, [], '操作成功');
            } else {
                return self::createReturn(false, [], '操作失败');
            }
        }
        $templateId = $request->get('template_id', 0);
        $template = AliyunSmsTemplateModel::where('id', $templateId)->findOrEmpty();
        return View::fetch('createTemplate', $template->toArray());
    }

    /**
     * 短信模板
     * @return string
     */
    function index()
    {
        return View::fetch('index');
    }

    /**
     *  获取模板列表
     * @throws \think\db\exception\DbException
     * @return array
     */
    function getTemplateList()
    {
        $where = [];
        $lists = AliyunSmsTemplateModel::where($where)->order('id', 'DESC')->paginate(20);
        foreach ($lists as $value) {
            if ($value->template_content) {
                //获取发送参数字段
                $arr = [];
                $preg = '/(?<={).*?(?=})+/';
                preg_match_all($preg, $value->template_content, $arr);
                $value->params = $arr[0];
            }
        }
        return self::createReturn(true, $lists, '');
    }

    /**
     *  删除模板
     * @param Request $request
     * @return array
     */
    function deleteTemplate(Request $request)
    {
        $templateId = $request->post('template_id');
        $template = AliyunSmsTemplateModel::where('id', $templateId)->findOrEmpty();
        if (!$template->isEmpty()) {
            return self::createReturn(true, $template->delete(), '删除成功');
        } else {
            return self::createReturn(false, [], '找不到该记录');
        }
    }

    /**
     * 发送测试短信
     * @param Request $request
     * @return array
     */
    function sendTest(Request $request)
    {
        $templateId = $request->post('template_id');
        $phone = $request->post('phone');
        $params = $request->post('params');
        $postPrams = [];
        foreach ($params as $key => $value) {
            $postPrams[$value['key']] = $value['value'];
        }
        $smsService = new AliyunSmsService();
        return self::createReturn(true, $smsService->send($templateId, $phone, $postPrams), 'ok');
    }
}