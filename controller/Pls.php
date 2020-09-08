<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-07
 * Time: 15:46.
 */

namespace app\aliyun\controller;


use app\aliyun\model\AliyunConfigModel;
use app\aliyun\model\AliyunPlsBindAxb;
use app\aliyun\service\AliyunPlsService;
use app\common\controller\AdminController;
use think\facade\View;
use think\Request;

class Pls extends AdminController
{
    /**
     * 删除绑定关系
     * @param Request $request
     * @return array
     */
    public function deleteBind(Request $request)
    {
        $bindAxbId = $request->post('bind_axb_id');
        $aliyunPlsBindAxb = AliyunPlsBindAxb::where('id', $bindAxbId)->findOrEmpty();
        if (!$aliyunPlsBindAxb->isEmpty()) {
            $res = $aliyunPlsBindAxb->delete();
            if ($res) {
                return self::createReturn(true, $res, '操作成功');
            } else {
                return self::createReturn(false, [], '操作失败');
            }
        } else {
            return self::createReturn(false, [], '找不到该记录');
        }
    }

    /**
     *  更新绑定信息
     * @param Request $request
     * @return array
     */
    public function updateBind(Request $request)
    {
        $bindAxbId = $request->post('bind_axb_id');
        $operateType = $request->post('operate_type');
        $changeValue = $request->post('phone_no_a');
        if ($operateType === 'updateNoB') {
            $changeValue = $request->post('phone_no_b');
        }
        if ($operateType === 'updateExpire') {
            $changeValue = $request->post('expiration');
        }
        $aliyunPlsService = new AliyunPlsService();
        $res = $aliyunPlsService->updateBind($bindAxbId, $operateType, $changeValue);
        if ($res) {
            return self::createReturn(true, [], "ok");
        } else {
            return self::createReturn(true, [], $aliyunPlsService->getError());
        }
    }

    /**
     * 解绑绑定关系
     * @param Request $request
     * @return array
     */
    public function unbindNumber(Request $request)
    {
        $bindAxbId = $request->post('bind_axb_id');
        $aliyunPlsBindAxb = AliyunPlsBindAxb::where('id', $bindAxbId)->findOrEmpty();
        if (!$aliyunPlsBindAxb->isEmpty()) {
            if ($aliyunPlsBindAxb->status == AliyunPlsBindAxb::STATUS_VALID) {
                $aliyunPlsService = new AliyunPlsService();
                $res = $aliyunPlsService->unBind($aliyunPlsBindAxb->phone_no_x, $aliyunPlsBindAxb->subs_id);
                if ($res) {
                    return self::createReturn(true, $res, '操作成功');
                } else {
                    return self::createReturn(false, [], $aliyunPlsService->getError());
                }
            } else {
                return self::createReturn(false, [], '已经失效，不需要解绑');
            }
        } else {
            return self::createReturn(false, [], '找不到该记录');
        }
    }

    /**
     * 获取列表
     * @param Request $request
     * @throws \think\db\exception\DbException
     * @return array
     */
    public function getBindList(Request $request)
    {
        $where = [];
        if ($request->get('phone_no_a', '') != '') {
            $phoneNoA = $request->get('phone_no_a');
            $where[] = ['phone_no_a', 'like', "%{$phoneNoA}%"];
        }
        if ($request->get('phone_no_b', '') != '') {
            $phoneNoB = $request->get('phone_no_b');
            $where[] = ['phone_no_b', 'like', "%{$phoneNoB}%"];
        }
        if ($request->get('phone_no_x', '') != '') {
            $phoneNoX = $request->get('phone_no_x');
            $where[] = ['phone_no_x', 'like', "%{$phoneNoX}%"];
        }
        if ($request->get('status', -1) != -1) {
            $status = $request->get('status');
            $where[] = ['status', '=', $status];
        }
        $lists = AliyunPlsBindAxb::where($where)->order('id', 'DESC')->paginate(20);
        return self::createReturn(true, $lists, 'OK');
    }

    /**
     *  绑定关系列表
     * @return string
     */
    public function index()
    {
        return View::fetch('index');
    }

    /**
     * 创建绑定关系
     * @param Request $request
     * @return array|string
     */
    public function createBindAxb(Request $request)
    {
        if ($request->isPost()) {
            $expiration = $request->post('expiration', '');
            $phoneNoA = $request->post('phone_no_a', '');
            $phoneNoB = $request->post('phone_no_b', '');
            $aliyunPlsService = new AliyunPlsService();

            $res = $aliyunPlsService->createBindAxb($expiration, $phoneNoA, $phoneNoB);
            if ($res) {
                return self::createReturn(true, $res, '操作成功');
            } else {
                return self::createReturn(false, [], $aliyunPlsService->getError());
            }
        }
        return View::fetch('createBindAxb');
    }

    /**
     * 配置页面
     * @return string
     */
    public function config()
    {
        $config = AliyunConfigModel::column('value', 'key');
        return View::fetch('config', ['config' => $config]);
    }

    /**
     * 编辑配置
     * @param Request $request
     * @return array
     */
    function editConfig(Request $request)
    {
        $postData = $request->post();
        foreach ($postData as $key => $value) {
            $aliyunConfig = AliyunConfigModel::where('key', $key)->findOrEmpty();
            $aliyunConfig->value = $value;
            $aliyunConfig->key = $key;
            $aliyunConfig->save();
        }
        return self::createReturn(true, $postData, '保存成功');
    }
}