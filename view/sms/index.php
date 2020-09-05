<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>短信模板</span>
        </div>
        <div style="padding-bottom: 20px">
            <el-button onclick="javascript:location.href='{:urlx(\'aliyun/sms/createTemplate\')}';"
                       type="primary">添加模板
            </el-button>
        </div>
        <div>
            <el-table
                    :data="lists"
                    border
                    style="width: 100%">
                <el-table-column
                        align="center"
                        prop="id"
                        label="ID"
                        min-width="60">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="template_name"
                        label="模板名称"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="sign_name"
                        label="模板签名"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="template_code"
                        min-width="180"
                        label="模板代码">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="template_content"
                        min-width="180"
                        label="模板内容">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="create_time"
                        min-width="180"
                        label="添加时间">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="template_content"
                        min-width="180"
                        label="操作">
                    <template slot-scope="props">
                        <el-button @click="editTemplate(props.row.id)" type="primary">编辑</el-button>
                        <el-button @click="testEvent(props.row)" type="success">发送测试</el-button>
                        <el-button @click="deleteTemplate(props.row.id)" type="danger">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div>
            <el-dialog width="500px" title="发送测试" :visible.sync="dialogFormVisible">
                <el-form :model="testForm" label-width="80px">
                    <el-form-item label="手机">
                        <el-input v-model="testForm.phone" autocomplete="off"
                                  placeholder="国际/港澳台消息：国际区号+号码，例如85200000000。"></el-input>
                    </el-form-item>
                    <el-form-item label="模板内容">
                        <div>{{ selectTemplate.template_content }}</div>
                    </el-form-item>
                    <el-form-item v-for="item in testForm.params" :label="item.key">
                        <el-input v-model="item.value"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="dialogFormVisible = false">取 消</el-button>
                    <el-button type="primary" @click="confirmTest">确 定</el-button>
                </div>
            </el-dialog>
        </div>
    </el-card>
</div>
<script>
    $(function () {
        new Vue({
            el: "#app",
            data: {
                testForm: {
                    phone: "",
                    params: []
                },
                selectTemplate: "",
                dialogFormVisible: false,
                searchForm: {},
                lists: []
            },
            mounted: function () {
                this.getList();
            },
            methods: {
                confirmTest: function () {
                    console.log('testForm', this.testForm);
                    var _this = this;
                    $.ajax({
                        url: "{:urlx('aliyun/sms/sendTest')}",
                        data: this.testForm,
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            layer.msg(res.msg);
                            if (res.status) {
                                _this.dialogFormVisible = false;
                            }
                        }
                    })
                },
                testEvent: function (template) {
                    this.dialogFormVisible = true;
                    var params = [];
                    for (var index in template.params) {
                        params.push({
                            key: template.params[index],
                            value: ""
                        })
                    }
                    this.testForm = {
                        phone: "",
                        params: params,
                        template_id: template.id
                    };
                    this.selectTemplate = template;
                },
                deleteTemplate: function (template_id) {
                    var _this = this;
                    this.$confirm('是否该模板删除？').then(() => {
                        $.ajax({
                            url: "{:urlx('aliyun/sms/deleteTemplate')}",
                            data: {template_id: template_id},
                            dataType: 'json',
                            type: 'post',
                            success: function (res) {
                                layer.msg(res.msg);
                                if (res.status) {
                                    _this.getList();
                                }
                            }
                        })
                    }).catch(err => {
                    });

                },
                editTemplate: function (template_id) {
                    location.href = "{:urlx('aliyun/sms/createTemplate')}?template_id=" + template_id
                },
                currentPageChange(e) {
                    this.currentPage = e;
                    this.getList();
                },
                getList: function () {
                    var _this = this;
                    $.ajax({
                        url: "{:urlx('aliyun/sms/getTemplateList')}",
                        data: Object.assign({
                            page: this.currentPage
                        }, this.searchForm),
                        dataType: 'json',
                        type: 'get',
                        success: function (res) {
                            var data = res.data;
                            _this.lists = data.data;
                            _this.totalCount = data.total;
                            _this.pageSize = data.per_page;
                            _this.pageCount = data.last_page;
                            _this.currentPage = data.current_page;
                        }
                    })
                },
            }
        });
    })
</script>

