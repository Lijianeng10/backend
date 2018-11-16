
<html>
    <head>
        <meta charset="UTF-8">
        <title>Custom DataGrid Pager - jQuery EasyUI Demo</title>
    </head>
    <body>
<!--        <h2>Custom DataGrid Pager</h2>
        <p>You can append some buttons to the standard datagrid pager bar.</p>-->
        <div style="margin:25px 0;"></div>
        <table id="dg"></table>
<!--        <table id="tt" class="easyui-datagrid" 
               url="userlist/index_1" toolbar="#tb"
               title="用户列表" iconCls="icon-save"
               rownumbers="true" pagination="true">-->
            <!--<thead>-->
<!--                <tr>
                    <th field="cust_no" align="center" >会员编号</th>
                    <th field="user_name" align="center">会员昵称</th>
                    <th field="agent_code"  align="center">上级代理商编号</th>
                    <th field="agent_name" align="center">上级代理商名称</th>
                    <th field="user_tel" align="center">手机号</th>
                    <th field="level_name" align="center">会员等级</th>
                    <th field="create_time" align="center">开户时间</th>-->
<!--                            <th field="level_name" align="center">认证状态</th>
                    <th field="level_name" align="center">会员等级</th>
                  <th field="level_name" align="center">会员等级</th>
        <!--                </tr>
                    </thead>-->
        <!--        </table>-->
        <div id="tb" style="padding:3px">
            <span>会员信息:</span>
            <input id="user_info" style="line-height:26px;border:1px solid #ccc">
            <span>代理商信息:</span>
            <input id="agents_info" style="line-height:26px;border:1px solid #ccc">
            <input id="dd" type="text" class="easyui-datebox">
            <input class="easyui-datetimebox" name="birthday"
                   accept=""data-options="required:true,showSeconds:false" value="" style="width:150px">
            <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="doSearch()">搜索</a>
            <div>
                <a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="javascript:alert('Add')">新增</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-cut" plain="true" onclick="javascript:alert('Cut')">删除</a>
                <a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="javascript:alert('Save')">保存</a>
            </div>

        </div>
    </body>
    <script type="text/javascript">
        $(function () {
            $('#dg').datagrid({
                url: 'userlist/index_1',
                method: 'get',
                title: '用户列表',
                iconCls: 'icon-save',
                width: "95%",
//                height: "100%",
                fitColumns: true,
                singleSelect: true,
                pagination: true,
                pageSize: 10,
                pageList: [10, 30, 50, 100, 200],
                loadMsg: "正在加载数据，请稍等!",
                rownumbers: true,
                toolbar: "#tb",
//                [{
//                        iconCls: 'icon-add',
//                        handler: function () {
//                            alert('add')
//                        }
//                    }, '-', {
//                        iconCls: 'icon-help',
//                        handler: function () {
//                        }
//                    }],
                columns: [[
                        {field: 'cust_no', title: '会员编号', align: 'center'},
                        {field: 'user_name', title: '会员昵称', align: 'center'},
                        {field: 'agent_code', title: '上级代理商编号', align: 'center'},
                        {field: 'agent_name', title: '上级代理商名称', align: 'center'},
                        {field: 'user_tel', title: '手机号', align: 'center'},
                        {field: 'level_name', title: '会员等级', align: 'center'},
                        {field: 'create_time', title: '开户时间', align: 'center'},
                        {field: 'authen_status', title: '认证状态', align: 'center',
                            formatter: function (authen_status) {
                                return authen_status == 1 ? "已通过" : (authen_status == 2 ? "审核中" : (authen_status == 3 ? "审核失败" : "未认证"));
                            },
                            styler: function (authen_status) {
                                return authen_status != 1 ? 'color:red;' : "";
                            }
                        },
                        {field: 'status', title: '使用状态', align: 'center',
                            formatter: function (status) {
                                return status == 1 ? "正常" : "禁用";
                            },
                        },
                        {field: 'user_id', title: '操作', align: 'center', formatter: function (user_id) {
                                return "<span class='handle pointer' onclick='viewMember(" + user_id + ")'>查看</span>";
                            },
                        },
                    ]],
            });
        });

        function doSearch() {
            $('#dg').datagrid('load', {
                user_info: $('#user_info').val(),
                agents_info: $('#agents_info').val()
            });
        }
        function viewMember(id) {
            location.href = '/subagents/userlist/view-member?user_id=' + id;
        }
    </script>

</html>