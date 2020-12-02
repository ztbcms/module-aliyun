<div id="app" style="padding: 8px;" v-cloak>
    <el-card>
        <el-col :sm="24" :md="18">
            <template>
                <div>
                    <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="180px">
                        <el-form-item label="网站存储方案" prop="attachment_driver">
                            <el-select v-model="formData.attachment_driver" placeholder="请选择网站存储方案" clearable
                                       :style="{width: '100%'}">
                                <?php foreach ($dirverList as $key => $value): ?>
                                    <el-option label="{$value}" value="{$key}"></el-option>
                                <?php endforeach; ?>
                            </el-select>
                        </el-form-item>
                        <template v-if="formData.attachment_driver == 'Aliyun'">
                            <el-form-item label="oss-keyId"
                                          prop="attachment_aliyun_key_id">
                                <el-input disabled v-model="formData.attachment_aliyun_key_id"

                                          placeholder="请输入OSS-accessKeyId"></el-input>
                            </el-form-item>
                            <el-form-item label="oss-keySecret"
                                          prop="attachment_aliyun_key_secret">
                                <el-input disabled v-model="formData.attachment_aliyun_key_secret"
                                          placeholder="请输入OSS-accessKeySecret"></el-input>
                            </el-form-item>
                            <el-form-item label="oss-Endpoint"
                                          prop="attachment_aliyun_endpoint">
                                <el-input v-model="formData.attachment_aliyun_endpoint"
                                          placeholder="请输入OSS-Endpoint(同一地域可以使用内网)"></el-input>
                            </el-form-item>
                            <el-form-item label="oss-bucket"
                                          prop="attachment_aliyun_bucket">
                                <el-input v-model="formData.attachment_aliyun_bucket"
                                          placeholder="请输入OSS-bucket"></el-input>
                            </el-form-item>
                            <el-form-item label="oss-外网域名"
                                          prop="attachment_aliyun_domain">
                                <el-input v-model="formData.attachment_aliyun_domain"
                                          placeholder="请输入OSS-外网域名"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item label="">
                            <div style="color: #F56C6C;">PS：注意配置后，需清除网站缓存；该配置会覆盖网站"附件配置"。</div>
                        </el-form-item>
                        <el-form-item size="large">
                            <el-button type="primary" @click="submitForm">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </template>
        </el-col>
    </el-card>
</div>

<script>
    $(document).ready(function () {
        window.app = new Vue({
            el: '#app',
            // 插入export default里面的内容
            components: {},
            props: [],
            data: {
                formData: {
                    attachment_driver: "{$siteConfig.attachment_driver}",
                    attachment_aliyun_key_id: "{$aliyunConfig.access_key_id}",
                    attachment_aliyun_key_secret: "{$aliyunConfig.access_key_secret}",
                    attachment_aliyun_endpoint: "{$siteConfig.attachment_aliyun_endpoint}",
                    attachment_aliyun_bucket: "{$siteConfig.attachment_aliyun_bucket}",
                    attachment_aliyun_domain: "{$siteConfig.attachment_aliyun_domain}",
                },
                rules: {
                    attachment_driver: [{
                        required: true,
                        message: '请选择网站存储方案',
                        trigger: 'change'
                    }],
                },
            },
            computed: {},
            watch: {},
            created: function() {
            },
            mounted: function() {
            },
            methods: {
                submitForm: function() {
                    this.$refs['elForm'].validate(function(valid) {
                        if (!valid) return;
                        $.ajax({
                            url: "{:api_url('/aliyun/oss/editConfig')}",
                            method: 'post',
                            dataType: 'json',
                            data: this.formData,
                            success: function (res) {
                                if (!res.status) {
                                    layer.msg(res.msg)
                                } else {
                                    layer.msg(res.msg)
                                }
                            }
                        });
                    })
                }
            }
        });
    });


    $(function () {
        //水印位置
        $('#J_locate_list > li > a').click(function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.parents('li').addClass('current').siblings('.current').removeClass('current');
            $('#J_locate_input').val($this.data('value'));

            window.app.formData.watermarkpos = $this.data('value');
        });
    });

</script>