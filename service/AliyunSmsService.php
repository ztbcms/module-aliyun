<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-05
 * Time: 16:25.
 */

namespace app\aliyun\service;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\aliyun\model\AliyunConfigModel;
use app\aliyun\model\AliyunSmsTemplateLogModel;
use app\aliyun\model\AliyunSmsTemplateModel;

class AliyunSmsService
{
    protected $error;

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }


    function send($templateId, $phone, $params)
    {

        $paramsString = json_encode($params);

        //记录发送日志
        $smsTemplateLog = new AliyunSmsTemplateLogModel();
        $smsTemplateLog->template_id = $templateId;
        $smsTemplateLog->params = $paramsString;
        $smsTemplateLog->phone = $phone;

        $config = AliyunConfigModel::column('value', 'key');
        $template = AliyunSmsTemplateModel::where('id', $templateId)->findOrEmpty();
        AlibabaCloud::accessKeyClient($config['access_key_id'], $config['access_key_secret'])
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phone,
                        'TemplateParam' => $paramsString,
                        'SignName' => $template->sign_name,
                        'TemplateCode' => $template->template_code,
                    ],
                ])
                ->request();
            $result = $result->toArray();
            if (!empty($result['Code']) && $result['Code'] == "OK") {
                $smsTemplateLog->result_code = $result['Code'];
                $smsTemplateLog->result_msg = $result['Message'];
                $smsTemplateLog->result = json_encode($result);
                $result = true;
            } else {
                $smsTemplateLog->result_code = $result['Code'];
                $smsTemplateLog->result_msg = empty($result['Message']) ? "error" : $result['Message'];
                $this->setError(empty($result['Message']) ? "error" : $result['Message']);
                $result = false;
            }
        } catch (ClientException $e) {
            $smsTemplateLog->result_code = "Exception" . $e->getCode();
            $smsTemplateLog->result_msg = $e->getErrorMessage();
            $this->setError($e->getErrorMessage());
            $result = false;
        } catch (ServerException $e) {
            $smsTemplateLog->result_code = "Exception" . $e->getCode();
            $smsTemplateLog->result_msg = $e->getErrorMessage();
            $this->setError($e->getErrorMessage());
            $result = false;
        }
        $smsTemplateLog->save();

        return $result;
    }
}