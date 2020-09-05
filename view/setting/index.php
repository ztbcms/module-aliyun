<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>阿里云配置</span>
        </div>
        <el-form style="width: 800px" ref="form" label-width="120px">
            <el-form-item label="AccessKey">
                <el-input v-model="form.access_key_id"></el-input>
            </el-form-item>
            <el-form-item label="AccessSecret">
                <el-input v-model="form.access_key_secret"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button @click="submit" type="primary">保存</el-button>
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
                    access_key_id: "{$config.access_key_id}",
                    access_key_secret: "{$config.access_key_secret}"
                }
            },
            mounted: function () {
            },
            methods: {
                submit: function () {
                    $.ajax({
                        url: "{:urlx('aliyun/setting/editConfig')}",
                        data: this.form,
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            layer.msg(res.msg);
                        }
                    })
                }
            }
        });
    })
</script>

