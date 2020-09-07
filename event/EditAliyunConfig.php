<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2020-09-07
 * Time: 10:25.
 */

namespace app\aliyun\event;


use app\aliyun\model\AliyunConfigModel;

class EditAliyunConfig
{
    protected $access_key_id = "";
    protected $access_key_secret = "";

    public function __construct()
    {
        $config = AliyunConfigModel::column('value', 'key');
        $this->setAccessKeyId($config['access_key_id']);
        $this->setAccessKeySecret($config['access_key_secret']);
    }

    /**
     * @return mixed
     */
    public function getAccessKeyId()
    {
        return $this->access_key_id;
    }

    /**
     * @param mixed $access_key_id
     */
    public function setAccessKeyId($access_key_id): void
    {
        $this->access_key_id = $access_key_id;
    }

    /**
     * @return mixed
     */
    public function getAccessKeySecret()
    {
        return $this->access_key_secret;
    }

    /**
     * @param mixed $access_key_secret
     */
    public function setAccessKeySecret($access_key_secret): void
    {
        $this->access_key_secret = $access_key_secret;
    }

}