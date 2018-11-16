<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>咕啦代理商云平台</title>
  <link rel="shortcut icon" type="image/png" href="/i/favicon.jpg">
  <style>
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
    ::-webkit-input-placeholder {
      color: #ccc;
    }
    ::-moz-placeholder {
      color: #ccc;
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
    .login {
      width: 100%;
      height: 100%;
      background: #f3f3f3;
    }
    .banner{
      width :100%;
      height: 590px;
      background-image: url('/image/expert_login/login.jpg');
      background-size: cover;
    }

    .container {
      width: 1230px;
      margin: 0 auto;
      height: 100%;
      position: relative;
    }
    .container .login_word{
      position :absolute;
      padding-top: 226px;
      padding-left: 135px;
      width: 800px;
      height :100%;
      top: 0;
      left: 0;
    }
    .container .pic {
      float: left;
      margin-top: 100px;
      margin-left: 85px;
    }
    .container aside {
      background: #fff;
      padding: 0 36px;
      padding-bottom: 20px;
      margin: 30px 0;
      width: 412px;
      min-height :420px;
      border: 1px solid #eee;
      float: right;
      padding-top :26px;
    }
    .container aside h2 {
      font-size:24px;
      line-height: 34px;
      margin-bottom: 30px;
      color: #2b96da;
    }
    .container aside .info {
      height: 30px;
      margin-bottom: 6px;
      border: 1px solid #ffa735;
      background: #fffbed;
      text-align: center;
      line-height: 30px;
      color: #666;
      font-size: 12px;
      border-radius: 3px;
    }
    .container aside .info img {
      vertical-align: -2px;
      margin-right: 5px;
    }
    .container .form {
      width: 100%;
    }
    .container .form input[type="text"],
    .container .form input[type="password"] {
      width: 100%;
      height: 48px;
      margin-bottom: 18px;
      padding-left: 10px;
      border: 1px solid #ccc;
      color: #444;
    }
    .container .form input[type="checkbox"] {
      width: 16px;
      height: 16px;
      margin-right: 5px;
      vertical-align: -3px;
    }
    .container .form .remember {
      font-size: 14px;
      color: #444;
    }
    .container .form .remember em {
      color: #999;
      font-size: 12px;
    }
    .container .form .forget {
      text-align: right;
      color: #dc3b40;
      font-size: 16px;
      cursor: pointer;
    }
    .container .form li {
      width: 100%;
    }
    .container .form li .check_num {
      width: 70px;
    }
    .container .form button {
      width: 100%;
      height: 50px;
      margin-top: 15px;
      background: #2b96da;
      color: #fff;
      border-radius: 3px;
      cursor: pointer;
    }
    .reset-psd {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 100;
    }
    .reset-psd .cover {
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      width: 100%;
      background-color: #000;
      opacity: 0.6;
      z-index: 10;
    }
    .reset-psd .reset {
      position: absolute;
      left: 50%;
      top: 50%;
      margin-left: -240px;
      margin-top: -140px;
      width: 480px;
      height: 280px;
      background-color: #fff;
      z-index: 101;
    }
    .reset-psd .reset .title {
      width: 100%;
      height: 45px;
      line-height: 45px;
      margin-left: 18px;
      font-size: 16px;
      color: #444;
    }
    .reset-psd .reset ul {
      width: 100%;
      padding: 20px 34px 13px 34px;
    }
    .reset-psd .reset ul li {
      width: 100%;
      height: 25px;
      margin-bottom: 12px;
      line-height: 25px;
    }
    .reset-psd .reset ul li >* {
      float: left;
    }
    .reset-psd .reset ul li p {
      width: 118px;
      color: #444;
      font-size: 14px;
    }
    .reset-psd .reset ul li span {
      font-size: 14px;
      color: #dc3b40;
    }
    .reset-psd .reset ul li input {
      width: 154px;
      height: 25px;
      border-radius: 3px;
      border: 1px solid #eee;
      margin-right: 21px;
      padding-left: 5px;
      font-size: 14px;
      color: #444;
    }
    .reset-psd .reset ul li button {
      width: 118px;
      height: 25px;
      border-radius: 3px;
      background-color: #ffa735;
      font-size: 14px;
      color: #fff;
      line-height: 25px;
      cursor: pointer;
    }
    .reset-psd .reset ul li .disable {
      background-color: #ccc;
      color: #444;
    }
    .reset-psd .reset .btn {
      width: 100%;
      height: 54px;
      padding-right: 30px;
    }
    .reset-psd .reset .btn button {
      float: right;
      width: 96px;
      height: 30px;
      margin-left: 20px;
      border-radius: 3px;
      font-size: 14px;
      cursor: pointer;
    }
    .reset-psd .reset .btn .cancel {
      border: 1px solid #eee;
      background-color: #fff;
      color: #444;
    }
    .reset-psd .reset .btn .confirm {
      background-color: #dc3b40;
      color: #fff;
    }
    footer {
      width: 100%;
      height: 70px;
      color: #999;
      background: #fff;
      text-align: center;
      line-height: 70px;
    }
    .font_white {
      background-color: #fff;
    }
    .font_white .right {
      color: #dc3b40;
    }
    .font_white .left span {
      color: #444;
    }
    .font_red {
      background-color: #dc3b40;
    }
    .font_red .right {
      color: #fff !important;
    }
    .font_red .left span {
      color: #fff;
    }
    .header {
      min-width: 1230px;
      height: 80px;
      line-height: 80px;
    }
    .header .wrap {
      width: 1230px;
      margin: 0 auto;
    }
    .header .left {
      float: left;
      height: 80px;
    }
    .header .left img {
      vertical-align: -17px;
    }
    .header .left span {
      margin-left: 20px;
      font-size: 18px;
    }
    .header .right {
      float: right;
      font-size: 14px;
    }
    .header .collect {
      float: left;
      cursor: pointer;
    }
    .header .collect img {
      margin-right: 7px;
      vertical-align: -3px;
    }
    .header .login-out {
      float: left;
      width: 59px;
      height: 23px;
      margin: 29px 0 0 29px;
      text-align: center;
      line-height: 21px;
      border: 1px solid #eee;
      border-radius: 20px;
      cursor: pointer;
    }
    .header .nick-name {
      position: relative;
      float: left;
      margin-left: 43px;
    }
    .header .nick-name img {
      width: 15px;
      height: 15px;
      margin-right: 8px;
      vertical-align: -3px;
    }

  </style>
</head>
<body>
<div class="header">
  <div class="wrap">
    <div class="left">
        <img src="/image/new01.png" alt=""/>
    </div>
    <!--<span>咕啦体育-代理商管理后台</span>-->
  </div>
</div>
<div class="banner">
  <div class="container clear-fix">
    <div class="login_word">
        <img src="/image/expert_login/login_word.png" alt="">
    </div>
    <aside>
      <h2>代理商管理账号</h2>
      <form class="form" action="/agents/login/login" method="post">
        <fieldset>
        
          <?php if (isset($msg)) {?>
              <div>
                <p class="info"><img src="/image/expert_login/error-tip.png" alt=""/><?php echo $msg;?></p>
              </div>
          <?php } ?>

          <div>
            <label><input placeholder="用户名" type="text" name="admin_name" value="<?php echo isset($_POST["admin_name"]) ? $_POST["admin_name"] : ""; ?>"></label>
          </div>
          <div>
            <label><input placeholder="密码" type="password" name="password" value="<?php echo isset($_POST["password"]) ? $_POST["password"] : ""; ?>"></label>
          </div>
          <div >
            <button type="submit">登录</button>
          </div>
  <!--         <div class="forget">忘记密码</div> -->
        </fieldset>
      </form>
    </aside>
  </div>
</div>

<footer>技术支持：厦门市咕啦电子商务有限责任公司 Copyright 2014 All rights reserved</footer>
</body>
</html>