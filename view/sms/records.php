<div id="app" v-cloak>
    <el-card>
        <div slot="header" class="clearfix">
            <span>短信发送日志</span>
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
                        prop="template_id"
                        label="模板ID"
                        min-width="60">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="phone"
                        label="发送手机"
                        min-width="180">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="result_code"
                        min-width="180"
                        label="返回码">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="result_msg"
                        min-width="180"
                        label="返回消息">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="result"
                        min-width="180"
                        label="返回数据">
                </el-table-column>
                <el-table-column
                        align="center"
                        prop="create_time"
                        min-width="180"
                        label="添加时间">
                </el-table-column>
            </el-table>
        </div>
    </el-card>
</div>
<script>
    $(function () {
        new Vue({
            el: "#app",
            data: {
                searchForm: {},
                lists: []
            },
            mounted: function () {
                this.getList();
            },
            methods: {
                currentPageChange: function(e) {
                    this.currentPage = e;
                    this.getList();
                },
                getList: function () {
                    var _this = this;
                    $.ajax({
                        url: "{:api_url('/aliyun/sms/records')}",
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

