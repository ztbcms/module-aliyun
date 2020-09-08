<div id="app" style="padding: 8px;" v-cloak>
    <el-card>
        <el-col :sm="24" :md="18">
            <template>
                <div>
                    <el-form ref="elForm" :model="formData" :rules="rules" size="medium" label-width="180px">
                        <el-form-item label="号码池Key"
                                      prop="pls_pool_key">
                            <el-input v-model="formData.pls_pool_key"
                                      placeholder='请到阿里云"号码隐私保护控制台"，在号码池管理中查看号码池Key。'></el-input>
                        </el-form-item>
                        <el-form-item label="呼叫显示规则"
                                      prop="pls_call_display_type">
                            <el-radio v-model="formData.pls_call_display_type" label="1">主被叫都显示中间号码X</el-radio>
                            <el-radio v-model="formData.pls_call_display_type" label="2">B呼叫A，A显示B真实号码</el-radio>
                            <el-radio v-model="formData.pls_call_display_type" label="3">A呼叫B，B显示A真实号码</el-radio>
                        </el-form-item>
                        <el-form-item label="是否对通话录音"
                                      prop="pls_is_recording_enabled">
                            <el-radio v-model="formData.pls_is_recording_enabled" label="1">是</el-radio>
                            <el-radio v-model="formData.pls_is_recording_enabled" label="0">否</el-radio>
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
            data() {
                return {
                    formData: {
                        pls_pool_key: "{$config.pls_pool_key}",
                        pls_call_display_type: "{$config.pls_call_display_type}",
                        pls_is_recording_enabled: "{$config.pls_is_recording_enabled}"
                    },
                    rules: {
                        pool_key: [{
                            required: true,
                            message: '请输入号码池key',
                            trigger: 'change'
                        }],
                    },
                }
            },
            computed: {},
            watch: {},
            created() {
            },
            mounted() {
            },
            methods: {
                submitForm() {
                    this.$refs['elForm'].validate(valid => {
                        if (!valid) return;
                        $.ajax({
                            url: "{:urlx('aliyun/pls/editConfig')}",
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
</script>