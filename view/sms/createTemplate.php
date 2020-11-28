<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>编辑短信模板</span>
        </div>
        <el-form style="width: 800px" ref="form" label-width="120px">
            <el-form-item label="名称">
                <el-input v-model="form.template_name"></el-input>
            </el-form-item>
            <el-form-item label="签名">
                <el-input v-model="form.sign_name"></el-input>
            </el-form-item>
            <el-form-item label="模板代码">
                <el-input v-model="form.template_code"></el-input>
            </el-form-item>
            <el-form-item label="模板内容">
                <el-input v-model="form.template_content"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button @click="submit" type="primary">保存</el-button>
                <el-button @click="backList" type="default">取消</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</div>
<script>
    $(function () {
        new Vue({
            el: "#app",
            data: {
                form: {
                    template_id: "{$id?$id : 0}",
                    template_name: "{$template_name?$template_name:''}",
                    sign_name: "{$sign_name?$sign_name:''}",
                    template_code: "{$template_code?$template_code:''}",
                    template_content: "{$template_content?$template_content:''}"
                }
            },
            mounted: function () {
            },
            methods: {
                backList: function () {
                    history.back(-1);
                },
                submit: function () {
                    $.ajax({
                        url: "{:api_url('/aliyun/sms/createTemplate')}",
                        data: this.form,
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            layer.msg(res.msg);
                            if (res.status) {
                                location.href = "{:api_url('/aliyun/sms/index')}"
                            }
                        }
                    })
                }
            }
        });
    })
</script>

