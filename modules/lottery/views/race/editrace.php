<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

echo "<form id='raceForm'>";
echo Html::input('hidden', 'race_id', $model['league_id']);
echo DetailView::widget([
    "model" => $model,
    "attributes" => [
        [
            'label' => '联赛编码<span class="requiredIcon">*</span>',
            'format' => 'raw',
            'value' => function($model) {
                
                return Html::input("text", "race_code", $model['league_code'], ["class" => "form-control inputLimit need"]);
            }
                ], [
                    'label' => '联赛简称<span class="requiredIcon">*</span>',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::input("text", "race_short_name", $model['league_short_name'], ["class" => "form-control inputLimit need"]);
                    }
                        ], [
                            'label' => '赛事全称<span class="requiredIcon">*</span>',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Html::input("text", "race_name", $model['league_long_name'], ["class" => "form-control inputLimit need"]);
                            }
                                ], [
                                    'label' => '所属分类<span class="requiredIcon">*</span>',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        return Html::dropDownList("race_category", $model['league_category_id'], $model['category'], ["class" => "form-control inputLimit need"]);
                                    }
                                        ], [
                                            'label' => '缩略图',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                if($model['league_img']){
                                                    $scr = $model['league_img'];
                                                } else {
                                                    $scr = "/image/u1529.png";
                                                }
                                                $html = "<div id='preview'></div>";
                                                $html.=Html::img($scr, ["id" => "race_img", "style" => "filter:;border:2px inset #EEE"]);
                                                $html .= "<div>";
                                                $html.=Html::tag("a", "上传", ["class" => "buttomspan", "onclick" => "$('#imgupload').click();"]);
                                                $html.=Html::tag("a", "| 删除", ["class" => "buttomspan", "onclick" => "javascript:$('#imgupload').val('');$('#race_img').attr('src', '/image/u1529.png');"]);
                                                $html .= "</div>";
                                                $html.=Html::tag("span", "图片尺寸限制为108X108，大小限制为200KB以下", ["class" => "buttomspan", "style" => "color:#bbbbbb;"]);
                                                $html.=Html::fileInput("race_img", "", ["class" => "imgupload", "id" => "imgupload", "onchange" => "previewImage(this);"]);
                                                return $html;
                                            }
                                                ], [
                                                    'label' => '备注',
                                                    'format' => 'raw',
                                                    'value' => function($model) {
                                                        return Html::textarea("remark", $model['league_remarks'], ["class" => "form-control", "style" => "height:60px;"]);
                                                    }
                                                        ], [
                                                            'label' => '操作',
                                                            'format' => 'raw',
                                                            'value' => function() {
                                                                $html = Html::button("保存", ["class" => "am-btn am-btn-primary inputLimit", "id" => "editSubmit"]);
                                                                $html .= Html::button("取消", ["class" => "am-btn am-btn-primary inputLimit", "onclick" => "location.href='/lottery/race/'"]);
                                                                return $html;
                                                            }
                                                                ]
                                                            ]
                                                        ]);
                                                        echo "</form>";
                                                        ?>
<script type="text/javascript">
    function previewImage(file)
    {
        if (file.files && file.files[0])
        {
            var reader = new FileReader();
            reader.onload = function (evt) {
                $("#race_img").attr("src", evt.target.result);
            }
            reader.readAsDataURL(file.files[0]);
            return false;
        } else //兼容IE
        {
            var sFilter = 'filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
            file.select();
            var src = document.selection.createRange().text;
            $("#race_img").attr("style", sFilter + src + '")');
            $("#race_img").attr("src", src);
        }
    }
    $(function(){
        $('#editSubmit').click(function(){
            err = 0;
            $(".need").each(function(i){
            var text = $(this).val();
            if(text ==""){
                   err++;
                   $(this).focus();
                   $("#msg").empty();
                   h = '<span id="msg" style="color:red;">请填写此字段</span>';
                   $(this).after(h);
                   return false;
            }
            });
            if(err != 0){
                return false;
            }
            var data =$("#raceForm").serializeArray();
            var formData= new FormData();
            formData.append("upfile", $("#imgupload").get(0).files[0]);
            $.each(data,function(i, field){
                formData.append(field.name,field.value);
            });
            $.ajax({
                url: '/lottery/race/do-edit-race',
                async: false,
                type: 'POST',
                processData: false,
           contentType: false,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data['code'] != 1) {
                        msgAlert(data['msg']);
                    } else {
                        msgAlert(data['msg'], function () {
                            location.href = '/lottery/race/index';
                        });
                    }
                }
            });
        })
    })
</script>

