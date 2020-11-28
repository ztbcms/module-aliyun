<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>号码隐私保护</span>
        </div>
        <div style="padding-bottom: 20px">

            <el-form :inline="true" :model="searchForm">
                <el-form-item>
                    <el-button onclick="javascript:location.href='{:api_url(\'/aliyun/pls/createBindAxb\')}';"
                               type="primary">绑定号码
                    </el-button>
                </el-form-item>
                <el-form-item label="">
                    <el-input v-model="searchForm.phone_no_a" placeholder="号码A"></el-input>
                </el-form-item>
                <el-form-item label="">
                    <el-input v-model="searchForm.phone_no_b" placeholder="号码B"></el-input>
                </el-form-item>
                <el-form-item label="">
                    <el-input v-model="searchForm.phone_no_x" placeholder="虚拟号码X"></el-input>
                </el-form-item>
                <el-form-item label="">
                    <el-select v-model="searchForm.status" placeholder="请选择状态">
                        <el-option label="全部" value="-1"></el-option>
                        <el-option label="正常" value="1"></el-option>
                        <el-option label="过期" value="0"></el-option>
                        <el-option label="失效" value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSearch">查询</el-button>
                </el-form-item>
            </el-form>
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
                        prop="expiration"
                        label="过期时间"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="phone_no_a"
                        label="号码A"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="phone_no_b"
                        min-width="180"
                        label="号码B">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="phone_no_x"
                        min-width="180"
                        label="虚拟号码">
                </el-table-column>
                <el-table-column
                        align="center"
                        min-width="180"
                        label="状态">
                    <template slot-scope="props">
                        <el-tag v-if="props.row.status == 1" type="success">正常</el-tag>
                        <el-tag v-else type="danger">无效</el-tag>
                    </template>
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
                        <template v-if="props.row.status==1">
                            <el-button @click="updatebind(props.row)" type="primary">更新</el-button>
                            <el-button @click="unbindNumber(props.row.id)" type="danger">解绑</el-button>
                        </template>
                        <template v-else>
                            <el-button @click="deleteBind(props.row.id)" type="danger">删除</el-button>
                        </template>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <div style="text-align: center;margin-top: 20px">
            <el-pagination
                    background
                    @current-change="currentPageChange"
                    layout="prev, pager, next"
                    :current-page="currentPage"
                    :page-count="totalCount"
                    :page-size="pageSize"
                    :total="totalCount">
            </el-pagination>
        </div>
    </el-card>
    <div>
        <el-dialog title="更新内容" width="500px" :visible.sync="dialogFormVisible">
            <el-form :model="updateForm" label-width="100px">
                <el-form-item label="更新类型">
                    <el-select v-model="updateForm.operate_type" placeholder="请选择更新类型">
                        <el-option label="更新号码A" value="updateNoA"></el-option>
                        <el-option label="更新号码B" value="updateNoB"></el-option>
                        <el-option label="更新过期时间" value="updateExpire"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item v-if="updateForm.operate_type=='updateNoA'" label="新号码A">
                    <el-input style="width: 200px" v-model="updateForm.phone_no_a" placeholder="请输入新号码A"></el-input>
                </el-form-item>
                <el-form-item v-if="updateForm.operate_type=='updateNoB'" label="新号码B">
                    <el-input style="width: 200px" v-model="updateForm.phone_no_b" placeholder="请输入新号码B"></el-input>
                </el-form-item>
                <el-form-item v-if="updateForm.operate_type=='updateExpire'" label="新过期时间">
                    <el-date-picker
                            v-model="updateForm.expiration"
                            type="datetime"
                            value-format="yyyy-MM-dd HH:mm:ss"
                            placeholder="选择日期时间">
                    </el-date-picker>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogFormVisible = false">取 消</el-button>
                <el-button type="primary" @click="submitUpdateBind">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</div>
<script>
    $(function () {
        new Vue({
            el: "#app",
            data: {
                searchForm: {},
                dialogFormVisible: false,
                updateForm: {
                    operate_type: "updateNoA"
                },
                lists: [],
                totalCount: 0,
                pageSize: 10,
                pageCount: 0,
                currentPage: 1
            },
            mounted: function () {
                this.getList();
            },
            methods: {
                onSearch: function () {
                    this.currentPage = 1;
                    this.getList();
                },
                submitUpdateBind: function () {
                    var _this = this;
                    $.ajax({
                        url: "{:api_url('/aliyun/pls/updateBind')}",
                        data: this.updateForm,
                        dataType: 'json',
                        type: 'post',
                        success: function (res) {
                            if (res.status) {
                                layer.msg('操作成功');
                                _this.dialogFormVisible = false;
                                _this.getList();
                            } else {
                                layer.msg(res.msg ? res.msg : "");
                            }
                        }
                    })
                },
                updatebind: function (bindAxb) {
                    this.updateForm = {
                        operate_type: 'updateNoA',
                        bind_axb_id: bindAxb.id,
                        phone_no_a: bindAxb.phone_no_a,
                        phone_no_b: bindAxb.phone_no_b,
                        expiration: bindAxb.expiration,
                    };
                    this.dialogFormVisible = true;
                },
                deleteBind: function (bind_axb_id) {
                    var _this = this;
                    this.$confirm('是否确定删除该号码？').then(() => {
                        $.ajax({
                            url: "{:api_url('/aliyun/pls/deleteBind')}",
                            data: {bind_axb_id: bind_axb_id},
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
                unbindNumber: function (bind_axb_id) {
                    var _this = this;
                    this.$confirm('是否确定解绑该号码？').then(() => {
                        $.ajax({
                            url: "{:api_url('/aliyun/pls/unbindNumber')}",
                            data: {bind_axb_id: bind_axb_id},
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
                currentPageChange(e) {
                    this.currentPage = e;
                    this.getList();
                },
                getList: function () {
                    var _this = this;
                    $.ajax({
                        url: "{:api_url('/aliyun/pls/getBindList')}",
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

