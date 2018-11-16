<div><span style="font-size: 16px;font-weight: 600;margin: 8px 0 0 0;display: inline-block;">赛事推荐 : </span></div>

<div id="w2" class="table table-striped table-bordered modalTable middle" style="margin-bottom:0px;">
    <table class="table" style="margin-bottom: 0px;"><thead>
            <tr><th>主队 VS 客队</th><th>赛果</th><th>让球数</th><th>预测赛果</th></tr>
        </thead>
        <tbody>
            <?php
            foreach ($bet as $v){
                echo '<tr data-key="0"><td><span style="display:inline-block;text-align:center;">'.$v["schedule_code"]."<br>".$v["start_time"]."<br>".$v["home_short_name"].$v["bf"].$v["visit_short_name"].'</td><td>'.$v["result"].'</td><td>'.($v["rq_nums"] > 0 ? ("+" . $v["rq_nums"]) : $v["rq_nums"]).'</td><td><span>'. $v["betVal"].'';
            }
            ?>
            
        </tbody>
    </table>
</div>
<span style="font-size: 16px;font-weight: 600;margin: 8px 0 0 0;display: inline-block;">标题:</span>
<div><input name="articleTitle" id="articleTitle" type="text" class="form-control" placeholder="文章标题12-27个字" value='' style="display: inline;"><span style="color: red;position: relative;right: 40px;right: 3px;top: -28px;float: right;" class="titleLen">12/27</span></div>
<span style="font-size: 16px;font-weight: 600;margin: 8px 0 0 0;display: inline-block;">推荐内容:</span>
<div><span style="color: red;position: relative;right: 5px;top: 30px;float: right;" class="contentLen"></span><div style="width:100%;"><div class="body" id="articleContent" style="float: left;"></div></div></div>

<div class="img_limit" style="font-size: 14px;color: #9e9e9e;float: left;width: 100%;">注：发文字数不少于200字；建议上传图片宽高不超1000px, 大小不超1M</div>
<button type="button" id="submitBtn" class="am-btn am-btn-primary" style="margin:5px;">提交</button>
<button type="button" id="cacelBtn" class="am-btn am-btn-primary" style="margin:5px;">取消</button>
<!-- 引用js -->
<script type="text/javascript" src="/js/wangEditor.min.js"></script>
<script type="text/javascript">
    var expertArticlesId =<?php echo $data["expert_articles_id"]; ?>;
    var E = window.wangEditor;
    var editor = new E('#articleContent');
    // base64提交
    editor.customConfig.uploadImgShowBase64 = true;
    // 隐藏链接上传
    editor.customConfig.showLinkImg = false;
    editor.customConfig.zIndex = 1;
    editor.customConfig.menus = [
        'bold', // 粗体
        'italic', // 斜体
        'underline', // 下划线
        'strikeThrough', // 删除线
        'foreColor', // 文字颜色
        'backColor', // 背景颜色
        'image', // 插入图片
                // 'head',  // 标题
                // 'link',  // 插入链接
                // 'list',  // 列表
                // 'justify',  // 对齐方式
                // 'quote',  // 引用
                // 'emoticon',  // 表情
                // 'table',  // 表格
                // 'video',  // 插入视频
                // 'code',  // 插入代码
                // 'undo',  // 撤销
                // 'redo'  // 重复
    ];
    var articleContent = '';
    editor.customConfig.onchange = function (html) {
        articleContent = html;
    }
    editor.create();
    $(function () {
        var _editorDiv = $("#articleContent").last().first("div");
        $.ajax({
            url: "/expert/article/get-article-content",
            type: "POST",
            dataType: "json",
            aysnc: false,
            data: {expert_articles_id: expertArticlesId},
            success: function (json) {
                if (json["code"] == 600) {
                    articleContent = json["result"]["article_content"];
                    var editorElem = document.getElementById('articleContent');
                    editorElem.lastChild.firstChild.innerHTML = articleContent;
                    $("#articleTitle").val(json["result"]["article_title"]);
                    $(".titleLen").text((json["result"]["article_title"].length) + "/27");
                    filterContent = articleContent.replace(/<.*?>/ig, '')
                    $(".contentLen").text((filterContent.length) + "/200");

                }
            }
        });
        $("#articleTitle").keyup(function () {
            $(".titleLen").text(($(this).val().length) + "/27");
        });
        _editorDiv.keyup(function () {
            filterContent = $(this).html().replace(/<.*?>/ig, '')
            filterContent = filterContent.trim();
            $(".contentLen").text((filterContent.length) + "/200");
        });
        $("#submitBtn").click(function () {
            if (!confirm("确定提交文章？")) {
                return false;
            }
            var articleTitle = $("#articleTitle").val();
            if (articleTitle.length > 27 || articleTitle.length < 12) {
                alert("文章标题12-27个字");
                return false;
            }
            filterContent = articleContent.replace(/<.*?>/ig, '')
            filterContent = filterContent.trim();
            if (filterContent.length < 200) {
                alert("发文字数不少于200字");
                return false;
            }
            $.ajax({
                url: "/expert/article/save-article-content",
                async: false,
                dataType: "json",
                data: {expertArticlesId: expertArticlesId, articleContent: articleContent, articleTitle: articleTitle},
                type: "POST",
                success: function (json) {
                    alert(json["msg"]);
                    if (json["code"] == "600") {
                        location.reload();
                        closeMask();
                    }
                }
            });
        });
        $("#cacelBtn").click(function () {
            closeMask();
        });
    });
</script>

