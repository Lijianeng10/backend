<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>预览</title>
    </head>
    <style>
        .clear-fix:after{
            content: "";
            display: block;
            clear: both;
            height: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body,
        html {
            height: 100%;
            width: 100%;
        }
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        hr,
        p,
        blockquote,
        dl,
        dt,
        dd,
        ul,
        ol,
        li,
        pre,
        fieldset,
        lengend,
        button,
        input,
        textarea,
        th,
        td {
            /* table elements 表格元素 */
            font-weight: normal;
            font-size: 16px;
        }
        /* 设置默认字体 */
        body,
        button,
        input,
        select,
        textarea,
        a {
            font-family: 'Hiragino Sans GB', '微软雅黑', arial, sans-serif;
        }
        address,
        cite,
        dfn,
        em,
        var {
            font-style: normal;
        }
        /* 将斜体扶正 */
        code,
        kbd,
        pre,
        samp,
        tt {
            font-family: "Courier New", Courier, monospace;
        }
        /* 统一等宽字体 */
        /* 重置列表元素 */
        ul,
        ol {
            list-style: none;
        }
        /* 重置文本格式元素 */
        a {
            text-decoration: none;
            line-height: 100%;
            color: #444;
        }
        a:visited {
            color: #444;
        }
        a:active {
            color: #444;
        }
        /* 重置表单元素 */
        legend {
            color: #000;
        }
        /* for ie6 */
        fieldset,
        img {
            border: none;
        }
        /* img 搭车：让链接里的 img 无边框 */
        /* 注：optgroup 无法扶正 */
        button,
        input,
        select,
        textarea {
            font-size: 100%; /* 使得表单元素在 ie 下能继承字体大小 */
            outline: none;
            border: none;
        }
        /* 重置表格元素 */
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
        /* 重置 hr */
        hr {
            border: none;
            height: 1px;
        }
        .preview {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        .cover {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #000;
            opacity: 0.6;
        }
        .all_container {
            width: 100%;
            height: 100%;
            position: relative;
            overflow-y: hidden;
            z-index: 100;
        }
        .container {
            width: 870px;
            height: 1492px;
            z-index: 100;
            position: relative;
            top: 25%;
            margin-top: -180px;
            left: 50%;
            margin-left: -140px;
            transform: scale(0.5);
            transform-origin: 0 0;
        }
        .container .phone {
            width: 100%;
            height: 100%;
            background-image: url("/image/phone.png");
            background-size: cover;
        }
        .container .content {
            width: 640px;
            height: 1086px;
            position: absolute;
            top: 224px;
            left: 63px;
            background-color: #fff;
            overflow-y: scroll;
        }
        .container .content::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #f5f5f5;
        }
        .container .content::-webkit-scrollbar {
            width: 0;
            background-color: #f5f5f5;
        }
        .container .content::-webkit-scrollbar-thumb {
            background-color: #ccc;
        }
        .container .close {
            width: 66px;
            height: 66px;
            display: block;
            background-image: url("/image/closePhone.png");
            background-size: cover;
            position: absolute;
            right: 0;
            top: 134px;
            cursor: pointer;
        }
        .container hr {
            width: 100%;
            background-color: #eee;
        }
        .container .phone_header {
            width: 100%;
            height: 80px;
            background-color: #dc3b40;
            text-align: center;
            line-height: 80px;
            color: #fff;
            font-size: 30px;
        }
        .container .phone_title {
            width: 100%;
            min-height: 118px;
            color: #444;
            padding-top: 22px;
            padding-left: 29px;
            border-bottom: 1px solid #eee;
        }
        .container .phone_title p {
            font-size: 32px;
            line-height: 37px;
        }
        .container .phone_title span {
            color: #999;
            font-size: 18px;
            line-height: 48px;
        }
        .container .phone_expert {
            width: 100%;
            height: 124px;
            padding-left: 27px;
        }
        .container .phone_expert img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            float: left;
            margin-top: 23px;
            margin-right: 20px;
        }
        .container .phone_expert >div {
            width: 450px;
            height: 80px;
            margin-top: 23px;
            float: left;
        }
        .container .phone_expert .expert_name {
            height: 46px;
            line-height: 46px;
            font-size: 23px;
            color: #444;
        }
        .container .phone_expert .expert_record {
            height: 34px;
        }
        .container .phone_expert .expert_record span {
            display: block;
            float: left;
            font-size: 18px;
            margin-right: 13px;
            height: 30px;
            padding: 0 10px;
            line-height: 28px;
            color: #fff;
            border-radius: 6px;
        }
        .container .phone_expert .expert_record span:nth-child(1) {
            color: #4090ea;
            border: 1px solid #4090ea;
        }
        .container .phone_expert .expert_record span:nth-child(2) {
            color: #eb4c51;
            border: 1px solid #eb4c51;
        }
        .container .phone_expert .expert_record span:nth-child(3) {
            color: #ffae4f;
            border: 1px solid #ffae4f;
        }
        .container .phone_game_detail {
            width: 100%;
            padding-top: 6px;
        }
        .container .phone_game_detail .start_time {
            padding-left: 26px;
            width: 100%;
            height: 50px;
            line-height: 50px;
            font-size: 20px;
            color: #444;
        }
        .container .phone_game_detail .team {
            width: 100%;
            height: 65px;
        }
        .container .phone_game_detail .team >p {
            float: left;
            width: 190px;
            height: 100%;
            font-size: 23px;
            line-height: 65px;
            color: #444;
        }
        .container .phone_game_detail .team >p span {
            color: #999;
        }
        .container .phone_game_detail .team .home_team {
            text-align: right;
            padding-right: 16px;
        }
        .container .phone_game_detail .team .visit_team {
            padding-left: 16px;
            text-align: left;
        }
        .container .phone_game_detail .team img {
            float: left;
            width: 54px;
            height: 54px;
            margin-top: 6px;
        }
        .container .phone_game_detail .team .score {
            float: left;
            width: 152px;
            height: 100%;
        }
        .container .phone_game_detail .team .score p {
            height: 36px;
            line-height: 36px;
            text-align: center;
            font-size: 27px;
            color: #dc3b40;
            margin-bottom: 4px;
        }
        .container .phone_game_detail .team .score .weikai {
            height: 65px;
            line-height: 65px;
            color: #ccc;
        }
        .container .phone_game_detail .team .score span {
            display: block;
            height: 24px;
            line-height: 24px;
            font-size: 16px;
            text-align: center;
            color: #999;
        }
        .container .phone_game_detail .phone_rec {
            width: 100%;
            height: 118px;
            padding-top: 23px;
            padding-left: 27px;
        }
        .container .phone_game_detail .phone_rec .rang {
            width: 57px;
            height: 64px;
            background-color: #eee;
            border-radius: 6px;
            color: #444;
            font-size: 21px;
            text-align: center;
            line-height: 26px;
            padding-top: 6px;
            float: left;
            margin-right: 35px;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div {
            height: 64px;
            width: 175px;
            float: left;
            background-color: #eee;
            text-align: center;
            position: relative;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div p,
        .container .phone_game_detail .phone_rec .rec_detail > div span {
            font-size: 22px;
            line-height:29px;
            margin: 0;
            display: block;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div p {
            color: #444;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div span {
            color: #999;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div .rec_main {
            position: absolute;
            width: 26px;
            left: 6px;
            top: 0;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div .is_right {
            position: absolute;
            top: 0;
            right: 0;
            width: 46px;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div .result {
            position: absolute;
            bottom: -14px;
            left: 50%;
            margin-left: -14px;
            width: 27px;
            height: 27px;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div:nth-child(1) {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div:nth-child(2) {
            border-right: 1px solid #eee;
            border-left: 1px solid #eee;
            width: 150px;
        }
        .container .phone_game_detail .phone_rec .rec_detail > div:nth-child(3) {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        .container .phone_game_detail .phone_rec .rec_detail .active {
            background-color: #dc3b40;
        }
        .container .phone_game_detail .phone_rec .rec_detail .active p,
        .container .phone_game_detail .phone_rec .rec_detail .active span {
            color: #fff;
        }
        .container .rec_reason {
            padding: 23px 27px;
        }
        .container .rec_reason h4 {
            font-size: 25px;
            color: #444;
            line-height: 36px;
            margin-bottom: 15px;
        }
        .rec_content p {
            width: 100%;
            font-size: 25px;
            color: #444;
            text-indent: 40px;
            line-height: 36px;
        }
        .rec_content p img {
            display: block;
            width: 100%;
            margin: 10px 0;
        }
        .start_time .time {
            float: right;
            padding-right: 26px;
        }
        .container .phone_game_detail .lq .rang {
            width: 80px;
            margin-right: 35px;
        }
        .container .phone_game_detail .lq .win {
            width: 175px;
            margin-right: 40px;
        }
        .container .phone_game_detail .lq .negative {
            width: 175px !important;
        }

        .container .rec_reason {
            padding: 23px 27px;
        }
        .container .rec_reason h4 {
            font-size: 25px;
            color: #444;
            line-height: 36px;
            margin-bottom: 15px;
            text-indent: 0;
        }
        .rec_reason {
            font-size: 25px;
            color: #444;
            text-indent: 40px;
            line-height: 36px;
            overflow: hidden;
            word-wrap: break-word;
        }
        .rec_content p {
            width: 100%;
            font-size: 25px;
            color: #444;
            text-indent: 40px;
            line-height: 36px;
            text-align: justify;

        }
        .rec_content p img {
            display: block;
            width: 100%;
            margin: 10px 0;
        }

    </style>
    <body>
        <div class="preview">
            <div class="cover"></div>
            <div class="all_container" id="all_container">
                <div class="container">
                    <div class="phone"></div>
                    <span class="close" id = "colse_phone"></span>
                    <div class="content">
                        <div class="phone_header">方案详情</div>
                        <div class="phone_title">
                            <p><?php echo $detailData['article_title']; ?></p>
                            <span>2017-01-01 14:11</span>
                        </div>
                        <div class="phone_expert">
                            <img src="<?php echo $detailData['user_pic']; ?>" alt="">
                            <div class="clear-fix">
                                <p class="expert_name"><?php echo $detailData['user_name']; ?></p>
                                <p class="expert_record clear-fix">
                                    <span>近<?php echo $detailData['day_nums']; ?>场中<?php echo $detailData['day_red_nums']; ?>场</span>
                                    <span><?php echo $detailData['even_red_nums']; ?>连红</span>
                                    <span>月红单<?php echo $detailData['month_red_nums']; ?></span>
                                </p>
                            </div>
                        </div>
                        <hr style="height:14px">

                        <?php foreach ($detailData['pre_concent'] as $vi): ?>
                            <div class="phone_game_detail">
                                <p class="start_time"><span><?php echo $vi['schedule_code']; ?> &nbsp;&nbsp;<?php echo $vi['league_name']; ?> &nbsp;&nbsp;<?php echo $vi['start_time']; ?></span><span class="time" data-time="<?php echo $vi['endsale_time']; ?>"></span></p>
                                <?php if (in_array($vi['pre_lottery'][0]['lottery_code'], ['3006', '3010'])): ?>
                                    <div class="team clear-fix">
                                        <p class="home_team"><span>[<?php echo $vi['home_team_rank']; ?>]</span><?php echo $vi['home_short_name']; ?></p>
                                        <img src="<?php echo $vi['home_team_img']; ?>" alt="">
                                        <div class="score">
                                            <?php if ($vi['schedule_status'] == 2): ?>
                                                <p><?php echo $vi['schedule_result_qcbf']; ?></p>
                                                <span>半<?php echo $vi['schedule_result_sbbf']; ?></span>
                                            <?php elseif ($vi['schedule_status'] == 3): ?>
                                                <p class="weikai">取消</p>
                                            <?php elseif ($vi['schedule_status'] == 4): ?>
                                                <p class="weikai">延迟</p>
                                            <?php elseif ($vi['schedule_status'] == 7): ?>
                                                <p class="weikai">腰斩</p>
                                            <?php else: ?>
                                                <p class="weikai">未开</p>
                                            <?php endif; ?>
                                        </div>
                                        <img src="<?php echo $vi['visit_team_img']; ?>" alt="">
                                        <p class="visit_team"><?php echo $vi['visit_short_name']; ?> <span>[<?php echo $vi['visit_team_rank']; ?>]</span></p>
                                    </div>
                                <?php else: ?>
                                    <div class="team clear-fix">
                                        <p class="visit_team"><span>[<?php echo $vi['visit_team_rank'] ?>]</span>[<?php echo $vi['visit_short_name'] ?>]</p>
                                        <img src="<?php echo $vi['visit_team_img']; ?>" alt="">
                                        <div class="score">
                                            <?php if ($vi['schedule_status'] == 2): ?>
                                                <p><?php echo $vi['schedule_result_qcbf']; ?></p>
                                            <?php elseif ($vi['schedule_status'] == 3): ?>
                                                <p class="weikai">取消</p>
                                            <?php elseif ($vi['schedule_status'] == 4): ?>
                                                <p class="weikai">延迟</p>
                                            <?php elseif ($vi['schedule_status'] == 7): ?>
                                                <p class="weikai">腰斩</p>
                                            <?php else: ?>
                                                <p class="weikai">未开</p>
                                            <?php endif; ?>
                                        </div>
                                        <img src="<?php echo $vi['home_team_img']; ?>" alt="">
                                        <p class="home_team"><?php echo $vi['home_short_name']; ?><span>[<?php echo $vi['home_team_rank']; ?>]</span></p>
                                    </div>
                                <?php endif; ?>
                                <?php foreach ($vi['pre_lottery'] as $it) : ?>
                                    <?php if (in_array($it['lottery_code'], ['3006', '3010'])): ?>
                                        <div class="phone_rec clear-fix">
                                            <?php if ($it['lottery_code'] == 3006): ?>
                                                <p class="rang">让球<br><?php echo $vi['rq_nums']; ?></p>
                                            <?php else : ?>
                                                <p class="rang">让球<br>0</p>
                                            <?php endif; ?>
                                            <div class="rec_detail clear-fix">
                                                <div class="win <?php if (in_array(3, $it['pre_result'])): ?>active<?php endif; ?>" >
                                                    <p>主胜</p>
                                                    <span><?php echo $it['lottery_code'] == 3006 ? $it['odds']['let_wins'] : (array_key_exists('outcome_wins', $it['odds']) ? $it['odds']['outcome_wins'] : ''); ?></span>
                                                    <?php if ($it['featured'] == 3): ?>
                                                        <img class="rec_main" src="/image/main_rec.png" alt="">
                                                    <?php endif; ?>
                                                    <!--让球胜平负和胜平负玩法-->
                                                    <?php if ($it['lottery_code'] == 3006 && $vi['schedule_status'] == 2): ?>
                                                        <?php if ($vi['schedule_result_rqbf'] == 3): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php elseif ($it['lottery_code'] == 3010 && $vi['schedule_status'] == 2) : ?>
                                                        <?php if ($vi['schedule_result'] == 3): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="level <?php if (in_array(1, $it['pre_result'])): ?>active<?php endif; ?>">
                                                    <p>平</p>
                                                    <span><?php echo $it['lottery_code'] == 3006 ? $it['odds']['let_level'] : (array_key_exists('outcome_level', $it['odds']) ? $it['odds']['outcome_level'] : ''); ?></span>
                                                    <?php if ($it['featured'] == 1): ?>
                                                        <img class="rec_main" src="/image/main_rec.png" alt="">
                                                    <?php endif; ?>
                                                    <!--让球胜平负和胜平负玩法-->
                                                    <?php if ($it['lottery_code'] == 3006 && $vi['schedule_status'] == 2): ?>
                                                        <?php if ($vi['schedule_result_rqbf'] == 1): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php elseif ($it['lottery_code'] == 3010 && $vi['schedule_status'] == 2) : ?>
                                                        <?php if ($vi['schedule_result'] == 1): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="negative <?php if (in_array(0, $it['pre_result'])): ?>active<?php endif; ?>">
                                                    <p>负</p>
                                                    <span><?php echo $it['lottery_code'] == 3006 ? $it['odds']['let_negative'] : (array_key_exists('outcome_negative', $it['odds']) ? $it['odds']['outcome_negative'] : ''); ?></span>
                                                    <?php if ($it['featured'] == 0): ?>
                                                        <img class="rec_main" src="/image/main_rec.png" alt="">
                                                    <?php endif; ?>
                                                    <?php if ($it['lottery_code'] == 3006 && $vi['schedule_status'] == 2): ?>
                                                        <?php if ($vi['schedule_result_rqbf'] == 0): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php elseif ($it['lottery_code'] == 3010 && $vi['schedule_status'] == 2) : ?>
                                                        <?php if ($vi['schedule_result'] == 0): ?>
                                                            <img class="result" src="/image/kaijiang.png" alt="">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                    <div>
                                                        <!--未中、已中-->
                                                        <?php if ($it['lottery_code'] == 3010 && $vi['schedule_status'] == 2): ?>
                                                            <?php if (in_array($vi['schedule_result'], $it['pre_result'])): ?>
                                                                <img class="is_right" src="/image/yiz.png" alt="">
                                                            <?php else: ?>
                                                                <img class="is_right" src="/image/weiz.png" alt="">
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div>
                                                        <?php if ($it['lottery_code'] == 3006 && $vi['schedule_status'] == 2): ?>
                                                            <?php if (in_array($vi['schedule_result_rqbf'], $it['pre_result'])): ?>
                                                                <img class="is_right" src="/image/yiz.png" alt="">
                                                            <?php else: ?>
                                                                <img class="is_right" src="/image/weiz.png" alt="">
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="phone_rec lq clear-fix">
                                            <?php if ($it['lottery_code'] == '3002'): ?>
                                                <p class="rang">让分<br><?php echo $vi['rq_nums'] ?></p>
                                            <?php elseif ($it['lottery_code'] == '3001'): ?>
                                                <p class="rang">胜负<br>0</p>
                                            <?php else: ?>
                                                <p class="rang">大小分<br><?php echo $vi['fen_cutoff'] ?></p>
                                            <?php endif; ?>
                                            <div class="rec_detail clear-fix">
                                                <div class="win <?php if (count(array_intersect([0, 1], $it['pre_result'])) > 0): ?>active<?php endif; ?> ">
                                                    <p><?php echo $it['lottery_code'] == '3004' ? '大分' : '客胜' ?></p>
                                                    <span><?php echo $it['lottery_code'] == '3001' ? $it['odds']['lose_3001'] : ($it['lottery_code'] == '3002' ? $it['odds']['lose_3002'] : $it['odds']['da_3004']) ?></span>
                                                    <?php if ($it['lottery_code'] == '3001' && $vi['schedule_result'] == '0') : ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php elseif ($it['lottery_code'] == '3002' && $vi['schedule_result_rqbf'] == '0'): ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php elseif ($it['lottery_code'] == '3004' && $vi['schedule_result_dxf'] == '1'): ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="negative <?php if (count(array_intersect([2, 3], $it['pre_result'])) > 0): ?>active<?php endif; ?> ">
                                                    <p><?php echo $it['lottery_code'] == '3004' ? '小分' : '主胜' ?></p>
                                                    <span><?php echo $it['lottery_code'] == '3001' ? $it['odds']['lose_3001'] : ($it['lottery_code'] == '3002' ? $it['odds']['lose_3002'] : $it['odds']['da_3004']) ?></span>
                                                    <?php if ($it['lottery_code'] == '3001' && $vi['schedule_result'] == '3') : ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php elseif ($it['lottery_code'] == '3002' && $vi['schedule_result_rqbf'] == '3'): ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php elseif ($it['lottery_code'] == '3004' && $vi['schedule_result_dxf'] == '2'): ?>
                                                        <img class="result" src="/image/kaijiang.png" alt="">
                                                    <?php endif; ?>
                                                    <?php if ($vi['schedule_status'] == 2): ?>
                                                        <?php if ($it['lottery_code'] == '3001'): ?>
                                                            <img class="is_right" src="<?php echo $it['pre_result'][0] == $vi['schedule_result'] ? '/image/yiz.png' : '/image/weiz.png'; ?>" alt="">
                                                        <?php elseif ($it['lottery_code'] == '3002'): ?>
                                                            <img class="is_right" src="<?php echo $it['pre_result'][0] == $vi['schedule_result_rqbf'] ? '/image/yiz.png' : '/image/weiz.png'; ?>" alt="">
                                                        <?php elseif ($it['lottery_code'] == '3004'): ?>
                                                            <img class="is_right" src="<?php echo $it['pre_result'][0] == $vi['schedule_result_dxf'] ? '/image/yiz.png' : '/image/weiz.png'; ?>" alt="">
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <hr style="height:14px">
                            <div class="rec_reason">
                                <h4>推荐理由</h4>
                                <div class="rec_content"><?php echo $detailData['article_content']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                $('#colse_phone').click(function () {
                    // 		$('.preview').hide();
                })
                function getDownTime() {
                    var t1 = $(".start_time .time").data("time");
                    var t2 = (Date.parse(new Date(t1)) - Date.parse(new Date())) / 1000;
                    if (t2 <= 0) {
                        return false;
                    }
                    var str = '';
                    var d = parseInt(t2 / (24 * 3600));
                    var h = parseInt((t2 - d * 24 * 3600) / 3600);
                    var m = parseInt(t2 % 3600 / 60);
                    var s = parseInt(t2 % 60);
                    if (d > 0) {
                        str = d + '天';
                    }
                    $(".start_time .time").text("距截止：" + str + h + ":" + (m > 9 ? m : ("0" + m)) + ":" + (s > 9 ? s : ("0" + s)));
                    setTimeout('getDownTime()', 1000);
                }
                $(function () {
                    getDownTime();
                });
            </script>

    </body>
</html>