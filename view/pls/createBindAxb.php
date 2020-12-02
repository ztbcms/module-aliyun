<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>号码隐私保护</span>
        </div>
        <el-form style="width: 800px" ref="form" label-width="120px">
            <el-form-item label="过期时间">
                <el-date-picker
                        style="width: 100%"
                        v-model="form.expiration"
                        type="datetime"
                        value-format="yyyy-MM-dd HH:mm:ss"
                        placeholder="绑定关系的过期时间。必须晚于当前时间1分钟以上。">
                </el-date-picker>
            </el-form-item>
            <el-form-item label="A号码">
                <el-input v-model="form.phone_no_a" placeholder="A号码可设置为手机号码或固定电话，固定电话需要加区号"></el-input>
            </el-form-item>
            <el-form-item label="B号码">
                <el-input v-model="form.phone_no_b" placeholder="B号码可设置为手机号码或固定电话，固定电话需要加区号"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button @click="submit" type="primary">创建</el-button>
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
                form: {}
            },
            mounted: function () {
            },
            methods: {
                backList: function () {
                    history.back(-1);
                },
                submit: function () {
                    $.ajax({
                        url: "{:api_url('/aliyun/pls/createBindAxb')}",
                        data: this.form,
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            layer.msg(res.msg);
                            if (res.status) {
                                location.href = "{:api_url('aliyun/pls/index')}"
                            }
                        }
                    })
                }
            }
        });
    })
</script>

