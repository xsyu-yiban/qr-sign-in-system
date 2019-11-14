<?php
require_once 'config.php';
require("classes/yb-globals.inc.php");
//初始化
$api = YBOpenApi::getInstance()->init($config['AppID'], $config['AppSecret'], $config['CallBack']);
$iapp  = $api->getIApp();                   //?
//连接数据库
$mysqli = new mysqli($mysql_host,$mysql_user,$mysql_pwd,$mysql_db);
if($mysqli->connect_errno){
    die('Connect Error:'.$mysqli->connect_error);
}
$mysqli->set_charset("utf8");

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

//易班api得到数据
$user_info = $api->request('user/real_me');
$user_verifyme = $api->request('user/verify_me');
$yb_collegename = $user_verifyme['info']['yb_collegename'];     //院系名
$yb_classname = $user_verifyme['info']['yb_classname'];         //班级名

$yb_identity = $user_info['info']['yb_identity'];               //用户身份
if($yb_identity == '学生')
    $yb_identify_numbers = $user_verifyme['info']['yb_studentid'];         //学号
else{
    $yb_identify_numbers = $user_verifyme['info']['yb_employid'];           //工号
}

$yb_userid = $user_info['info']['yb_userid'];                   //易班id
$yb_realname = $user_info['info']['yb_realname'];               //真实姓名

//数据库插入操作
$status = $mysqli->query("select * from users where user_id = {$yb_userid}");
if(mysqli_num_rows($status) < 1)
{
    $insert_into = $mysqli->query("INSERT INTO users(user_id, real_name, identity, unit, classname, stuid) VALUES ('{$yb_userid}','{$yb_realname}','{$yb_identity}','{$yb_collegename}','{$yb_classname}','{$yb_identify_numbers}')");
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>二维码签到系统</title>
    <script src="js/jquery.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/bootstrap-grid.min.css" rel="stylesheet" />
    <link href="css/bootstrap-reboot.min.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <script src="js/bootstrap.min.js"></script>
    <script src="js/url_decode.php" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="css/LUploader.css">
    <link rel="stylesheet" href="css/form_style.css">
    <link rel="stylesheet" href="css/checkbox.min.css">
</head>
<body>
<div class="form_header text-center h4">
    发布签到
</div>
<div class="container-fluid">
    <form class="form-horizontal form" id="form">
        <div class="form-group">
            <label class="control-label col-sm-2"><strong><small class="text-danger">* </small>签到主题</strong></label>
            <div class="col-sm-12 input-group-sm">
                <input class="form-control" id="m_name" type="text" name="Meeting_Name" placeholder="请输入不少于6个字符的会议主题(必填)" value=""/>
            </div>
        </div>
        <div class="form-group" hidden="hidden">
            <label class="control-label col-sm-2"><strong><small class="text-danger">* </small>用户ID</strong></label>
            <div class="col-sm-12 input-group-sm">
                <!-- 后端补充 -->
                <input class="form-control" id="user_id" type="text" name="user_id" value="<?php echo $yb_userid;?>"/>
                <input class="form-control" id="user_realname" type="text" name="user_name" value="<?php echo $yb_realname;?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"><strong><small class="text-danger">* </small>二维码有效时间</strong></label>
            <div class="col-sm-12 input-group-sm">
                <select class="form-control" id="sign_time" name="Meeting_SignTime"><option>5分钟</option><option>10分钟</option><option>30分钟</option><option>60分钟</option></select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2"><strong>预计签到人数</strong></label>
            <div class="col-sm-12 input-group-sm">
                <input class="form-control" id="m_counts" type="number" name="Meeting_Counts" value="" placeholder="请填写预参会人数(选填)"/>
            </div>
        </div>
<!--        <div class="form-group">-->
<!--            <span class="control-label col-10"><strong><small class="text-danger">* </small>二维码动态更新</strong></span>-->
<!--            <label class="col-2 el-switch" style="left: 1.5rem; margin-left: 2rem;">-->
<!--                <input type="checkbox" name="change_or" id="change_or">-->
<!--                <span class="el-switch-style"></span>-->
<!--            </label>-->
<!--        </div>-->
<!--        <div class="form-group">-->
<!--            <span class="control-label col-10"><strong><small class="text-danger">* </small>智能定位防代签</strong></span>-->
<!--            <label class="col-2 el-switch" style="left: 1.5rem; margin-left: 2rem;">-->
<!--                <input type="checkbox" name="position_or" id="position_or" >-->
<!--                <span class="el-switch-style"></span>-->
<!--            </label>-->
<!--        </div>-->
        <div class="form-group">
            <span class="control-label col-10"><strong><small class="text-danger">* </small>签到成功抽网薪</strong></span>
            <label class="col-2 el-switch" style="left:1.5rem; margin-left: 2rem;">
                <input type="checkbox" name="prize_or" id="prize_or" onclick="check_prize(this);">
                <span class="el-switch-style"></span>
            </label>
        </div>
        <button class="btn btn-info btn-block btn-lg center-block col-sm-12" type="button" onclick="if(checkFillForm()) {$('#loadgif').show();$('#load_bg').show(); onTouchMove(true); submit_form();}else console.log('helloworld!');">生成二维码</button>
    </form>
</div>
<div id="load_bg" style="z-index:2; position: absolute; top:0; left:0; background: rgba(255,255,255,0.8); width: 100%; height: 125%; position: fixed;"></div>
<div id="loadgif" style="width:5rem;height:5rem;   position:fixed;top:40%;left:40%; z-index: 3;">
    　　<img  alt="正在生成二维码..." src="images/ajax-loader.gif"/>
        <p class="text-center text-info small" >生成二维码...</p>
</div>
<script src="js/alertShow.php" type="text/javascript" charset="utf-8"></script>
<script>
//ajax loader的隐藏显现
$(document).ready(function () { $("#loadgif").hide(); $("#load_bg").hide();});
//禁止页面内容滚动
function onTouchMove(inFlag) {
    if (inFlag) {
        document.body.parentNode.style.overflow = "hidden";
    } else {
        document.body.parentNode.style.overflow = "auto";
    }
}

//抽奖页面的隐藏与显现
var prize_collaspe = $('#prize');
var meet_count = $('#m_counts');
var tmp = 1;
function check_prize(obj) {
    if($(obj).val() && tmp%2!=0) {
        cc_change();
  //      alert($(obj).prop("checked"));
    }
    else if(tmp%2==0)  {
        c_change();
  //      alert($(obj).prop("checked"));
    }
    tmp++;
}

function c_change() {
if(!meet_count.val()) {
meet_count.css('border', '');
meet_count.css('placeholder', '请填写预参会人数(选填)');
}
prize_collaspe.collapse('hide');
prize_collaspe.css('display','none');
}

function cc_change() {
if(!meet_count.val()) {
meet_count.css('border', 'red 1px solid');
meet_count.attr('placeholder', '请填写参会人数(必填)');
}
prize_collaspe.collapse('show');
prize_collaspe.css('display','block');
}
</script>
<script type="text/javascript" src="js/registerCheck.php"></script>
<script src="js/yb_data_translate.php" type="text/javascript" charset="utf-8"></script>
</body>
</html>
