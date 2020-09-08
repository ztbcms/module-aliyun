<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-08
 * Time: 09:16.
 */

namespace app\aliyun\service;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use app\aliyun\model\AliyunConfigModel;
use app\aliyun\model\AliyunPlsBindAxb;
use app\common\service\BaseService;

class AliyunPlsService extends BaseService
{
    const REGION_ID = "cn-hangzhou";
    const PRODUCT = "Dyplsapi";
    const VERSION = "2017-05-25";
    const HOST = "dyplsapi.aliyuncs.com";

    protected $aliyunConfig = [];

    public function __construct()
    {
        $this->aliyunConfig = AliyunConfigModel::column('value', 'key');
        $accessKeyId = $this->aliyunConfig['access_key_id'];
        $accessKeySecret = $this->aliyunConfig['access_key_secret'];

        AlibabaCloud::accessKeyClient($accessKeyId, $accessKeySecret)
            ->regionId('cn-hangzhou')->asDefaultClient();
    }

    public function updateBind($bindAxbId, $operateType, $updateValue, $callDisplayType = null): bool
    {
        $aliyunPlsBindAxb = AliyunPlsBindAxb::where('id', $bindAxbId)->findOrEmpty();
        if ($aliyunPlsBindAxb->isEmpty()) {
            $this->setError('找不到该记录');
            return false;
        }

        $poolKey = $this->aliyunConfig['pls_pool_key'];
        $callDisplayType = $callDisplayType === null ? $this->aliyunConfig['pls_call_display_type'] : $callDisplayType;
        $typeToKey = [
            'updateNoA' => [
                'db_key' => 'phone_no_a',
                'aliyun_key' => 'PhoneNoA',
            ],
            'updateNoB' => [
                'db_key' => 'phone_no_b',
                'aliyun_key' => 'PhoneNoB',
            ],
            'updateExpire' => [
                'db_key' => 'expiration',
                'aliyun_key' => 'Expiration',
            ],
        ];
        if (empty($typeToKey[$operateType])) {
            $this->setError('操作类型错误');
            return false;
        }
        $operateAliyunKey = $typeToKey[$operateType]['aliyun_key'];


        $query = [
            'PoolKey' => $poolKey,
            'PhoneNoX' => $aliyunPlsBindAxb->phone_no_x,
            'SubsId' => $aliyunPlsBindAxb->subs_id,
            'OperateType' => $operateType,
            'CallDisplayType' => $callDisplayType,
            $operateAliyunKey => $updateValue
        ];
        try {
            $res = AlibabaCloud::rpc()
                ->product(self::PRODUCT)
                ->version(self::VERSION)
                ->action('UpdateSubscription')
                ->method('POST')
                ->host(self::HOST)
                ->options([
                    'query' => $query,
                ])
                ->request();
            if (!empty($res->Code && $res->Code === 'OK')) {
                $operateDbKey = $typeToKey[$operateType]['db_key'];
                $aliyunPlsBindAxb->$operateDbKey = $updateValue;
                $aliyunPlsBindAxb->save();
                return true;
            } else {
                $this->setError(empty($res->Message) ? '' : $res->Message);
                return false;
            }
        } catch (ClientException $exception) {
            $this->setError($exception->getMessage());
            return false;
        } catch (ServerException $exception) {
            $this->setError($exception->getMessage());
            return false;
        }
    }

    public function unBind($phoneNoX, $subsId): bool
    {
        $aliyunPlsBindAxb = $this->queryBindDetail($phoneNoX, $subsId);
        if ($aliyunPlsBindAxb->isEmpty()) {
            $this->setError('找不到该绑定记录');
            return false;
        }

        if ($aliyunPlsBindAxb->status === AliyunPlsBindAxb::STATUS_VALID) {
            $poolKey = $this->aliyunConfig['pls_pool_key'];
            try {
                $res = AlibabaCloud::rpc()
                    ->product(self::PRODUCT)
                    ->version(self::VERSION)
                    ->action('UnbindSubscription')
                    ->method('POST')
                    ->host(self::HOST)
                    ->options([
                        'query' => [
                            'PoolKey' => $poolKey,
                            'SecretNo' => $phoneNoX,
                            'SubsId' => $subsId,
                        ],
                    ])
                    ->request();
                if (!empty($res->Code && $res->Code === 'OK')) {
                    $aliyunPlsBindAxb->status = AliyunPlsBindAxb::STATUS_INVALID;
                    $aliyunPlsBindAxb->result_msg = 'unbind success';
                    $aliyunPlsBindAxb->save();
                    return true;
                } else {
                    $this->setError(empty($res->Message) ? '' : $res->Message);
                    return false;
                }
            } catch (ClientException $exception) {
                $this->setError($exception->getMessage());
                return false;
            } catch (ServerException $exception) {
                $this->setError($exception->getMessage());
                return false;
            }
        } else {
            //已经无效，返回解绑成功
            return true;
        }
    }

    /**
     * 查询具体绑定记录的详情
     * @param $phoneNoX
     * @param $subsId
     * @return array|mixed
     */
    public function queryBindDetail($phoneNoX, $subsId): AliyunPlsBindAxb
    {
        $poolKey = $this->aliyunConfig['pls_pool_key'];
        $aliyunPlsBindAxb = AliyunPlsBindAxb::where('subs_id', $subsId)->findOrEmpty();
        try {
            $res = AlibabaCloud::rpc()
                ->product(self::PRODUCT)
                ->version(self::VERSION)
                ->action('QuerySubscriptionDetail')
                ->method('POST')
                ->host(self::HOST)
                ->options([
                    'query' => [
                        'PoolKey' => $poolKey,
                        'PhoneNoX' => $phoneNoX,
                        'SubsId' => $subsId,
                    ],
                ])
                ->request();
            if (!empty($res->Code && $res->Code === 'OK')) {
                //调用成功
                $SecretBindDetailDTO = $res['SecretBindDetailDTO'];
                $aliyunPlsBindAxb->expiration = $SecretBindDetailDTO['ExpireDate'];
                $aliyunPlsBindAxb->phone_no_a = $SecretBindDetailDTO['PhoneNoA'];
                $aliyunPlsBindAxb->phone_no_b = $SecretBindDetailDTO['PhoneNoB'];
                $aliyunPlsBindAxb->phone_no_x = $SecretBindDetailDTO['PhoneNoX'];
                $aliyunPlsBindAxb->pool_key = $poolKey;
                $aliyunPlsBindAxb->is_recording_enabled = $SecretBindDetailDTO['NeedRecord'];
                $aliyunPlsBindAxb->status = $SecretBindDetailDTO['Status'];
                $aliyunPlsBindAxb->subs_id = $SecretBindDetailDTO['SubsId'];
                $aliyunPlsBindAxb->result_msg = $SecretBindDetailDTO['Status'] ? "OK" : '失效';
                $aliyunPlsBindAxb->save();
            } else {
                $this->setError(empty($res->Message) ? '' : $res->Message);
            }
        } catch (ClientException $exception) {
            $this->setError($exception->getMessage());
        } catch (ServerException $exception) {
            $this->setError($exception->getMessage());
        }
        return $aliyunPlsBindAxb;
    }

    /**
     *  查询隐私号码的SubsID
     * @param $phoneNoX
     * @return array
     */
    public function querySubsIds($phoneNoX): array
    {
        $poolKey = $this->aliyunConfig['pls_pool_key'];
        $subsIds = [];
        try {
            $res = AlibabaCloud::rpc()
                ->product(self::PRODUCT)
                ->version(self::VERSION)
                ->action('QuerySubsId')
                ->method('POST')
                ->host(self::HOST)
                ->options([
                    'query' => [
                        'PoolKey' => $poolKey,
                        'PhoneNoX' => $phoneNoX,
                    ],
                ])
                ->request();
            if (!empty($res->Code && $res->Code === 'OK')) {
                //调用成功
                $subsIds = empty($res->SubsId) ? '' : explode(',', $res->SubsId);
            } else {
                $this->setError(empty($res->Message) ? '' : $res->Message);
            }
        } catch (ClientException $exception) {
            $this->setError($exception->getMessage());
        } catch (ServerException $exception) {
            $this->setError($exception->getMessage());
        }
        return $subsIds;
    }


    /**
     * 创建绑定关系
     * @param $expiration
     * @param $phoneNoA
     * @param $phoneNoB
     * @return bool
     */
    public function createBindAxb($expiration, $phoneNoA, $phoneNoB): bool
    {
        //获取号码隐私保障配置
        $poolKey = $this->aliyunConfig['pls_pool_key'];
        $callDisplayType = $this->aliyunConfig['pls_call_display_type'];
        $isRecordingEnabled = $this->aliyunConfig['pls_is_recording_enabled'];

        $aliyunPlsBindAxb = new AliyunPlsBindAxb();
        $aliyunPlsBindAxb->expiration = $expiration;
        $aliyunPlsBindAxb->phone_no_a = $phoneNoA;
        $aliyunPlsBindAxb->phone_no_b = $phoneNoB;
        $aliyunPlsBindAxb->pool_key = $poolKey;
        $aliyunPlsBindAxb->call_display_type = $callDisplayType;
        $aliyunPlsBindAxb->is_recording_enabled = $isRecordingEnabled;

        try {
            $res = AlibabaCloud::rpc()
                ->product(self::PRODUCT)
                ->version(self::VERSION)
                ->action('BindAxb')
                ->method('POST')
                ->host(self::HOST)
                ->options([
                    'query' => [
                        'PoolKey' => $aliyunPlsBindAxb->pool_key,
                        'CallDisplayType' => $aliyunPlsBindAxb->call_display_type,
                        'IsRecordingEnabled' => $aliyunPlsBindAxb->is_recording_enabled == "1",
                        'PhoneNoA' => $aliyunPlsBindAxb->phone_no_a,
                        'PhoneNoB' => $aliyunPlsBindAxb->phone_no_b,
                        'Expiration' => $aliyunPlsBindAxb->expiration,
                    ],
                ])
                ->request();
            if (!empty($res->Code) && $res->Code === 'OK') {
                //调用成功
                $aliyunPlsBindAxb->phone_no_x = empty($res['SecretBindDTO']['SecretNo']) ? '' : $res['SecretBindDTO']['SecretNo'];
                $aliyunPlsBindAxb->subs_id = empty($res['SecretBindDTO']['SubsId']) ? '' : $res['SecretBindDTO']['SubsId'];
                $aliyunPlsBindAxb->result_msg = "OK";
                $aliyunPlsBindAxb->status = AliyunPlsBindAxb::STATUS_VALID;
            } else {
                $aliyunPlsBindAxb->status = AliyunPlsBindAxb::STATUS_INVALID;
                $aliyunPlsBindAxb->result_msg = empty($res['Message']) ? '' : $res['Message'];
                $this->setError($aliyunPlsBindAxb->result_msg);
            }
        } catch (ClientException $exception) {
            $aliyunPlsBindAxb->status = AliyunPlsBindAxb::STATUS_INVALID;
            $aliyunPlsBindAxb->result_msg = $exception->getMessage();
            $this->setError($aliyunPlsBindAxb->result_msg);
        } catch (ServerException $exception) {
            $aliyunPlsBindAxb->status = AliyunPlsBindAxb::STATUS_INVALID;
            $aliyunPlsBindAxb->result_msg = $exception->getMessage();
            $this->setError($aliyunPlsBindAxb->result_msg);
        }
        $aliyunPlsBindAxb->save();
        return $aliyunPlsBindAxb->status === AliyunPlsBindAxb::STATUS_VALID;
    }
}