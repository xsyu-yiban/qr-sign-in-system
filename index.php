<?php
require_once 'config.php';
require("classes/yb-globals.inc.php");
//初始化
$api = YBOpenApi::getInstance()->init($config['AppID'], $config['AppSecret'], $config['CallBack']);
$iapp  = $api->getIApp();                   //?
////连接数据库
//$mysqli = new mysqli($mysql_host,$mysql_user,$mysql_pwd,$mysql_db);
//if($mysqli->connect_errno){
//    die('Connect Error:'.$mysqli->connect_error);
//}
//$mysqli->set_charset("utf8");
//初始化变量
$token = "";
$username = "";
if ($_COOKIE["yb_token"]) {
        $token = $_COOKIE["yb_token"];
    } else {
        try {
            //轻应用获取access_token
            $info = $iapp->perform();
            $token = $info['visit_oauth']['access_token'];
            if ($token == true) {
                setcookie("yb_token", $token, time() + 3600 * 24);
            }

        } catch (YBException $ex) {
            //未授权则跳转至授权页面
                header("Location:https://openapi.yiban.cn/oauth/authorize?client_id=" . $config['AppID'] . "&redirect_uri=" . $config['CallBack'] . "&state=w逸泽");
        }
    }
//设置access_token
$api->bind($token);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  	<meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>二维码签到系统</title>
    <script src="js/jquery.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <script src="js/bootstrap.min.js"></script>
    <script src="js/url_decode.php" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/index_style.css">
</head>
<body>
<div class=" container-fluid">
    <div class="row">
        <div class="col-12">
            <button class="publish_btn btn btn-block  text-info" onclick="publish()">发布签到</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="my_btn btn btn-block  text-info" onclick="my()">签到记录</button>
        </div>
    </div>
    <div class="row" style="margin-left:1px; margin-right:1px; margin-top: 5.5rem;">
        <div class="col-md-12">
            <p class="text-center small" style="color: rgba(155,155,155,0.4)">
                Copyright © XSYU_YIBAN. All Rights Reserved<br>西安石油大学易班发展中心 版权所有
            </p>
        </div>
    </div>
</div>
<script>
    function publish() {
        window.location.href = generateUrlWithParams('form.php', myParam);
    }
    function my() {
        window.location.href = generateUrlWithParams('list.php', myParam);
    }
</script>
</body>
</html>
