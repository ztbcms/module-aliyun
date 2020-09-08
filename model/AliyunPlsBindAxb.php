<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-08
 * Time: 08:26.
 */

namespace app\aliyun\model;

use think\Model;
use think\model\concern\SoftDelete;

class AliyunPlsBindAxb extends Model
{

    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $defaultSoftDelete = 0;

    const STATUS_INVALID = 0;
    const STATUS_VALID = 1;
    const STATUS_EXPIRED = 2;

    protected $name = 'tp6_aliyun_pls_bind_axb';
    protected $type = [
        'expiration' => 'timestamp'
    ];

    public function getStatusAttr($value, $data)
    {
        if ($value == self::STATUS_VALID && $data['expiration'] < time()) {
            self::where('id', $data['id'])->update([
                'status' => self::STATUS_EXPIRED
            ]);
            return self::STATUS_EXPIRED;
        }
        return $value;
    }
}