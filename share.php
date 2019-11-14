<?php
require_once 'config.php';
////连接数据库
$mysqli = new mysqli($mysql_host,$mysql_user,$mysql_pwd,$mysql_db);
if($mysqli->connect_errno){
    die('Connect Error:'.$mysqli->connect_error);
}
$mysqli->set_charset("utf8");
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
    <script src="js/bootstrap.min.js"></script>
    <script src="js/url_decode.php"></script>
    <script type="text/javascript" src="js/utf.js"></script>
    <script type="text/javascript" src="js/jquery.qrcode.js"></script>
    <link rel="stylesheet" href="css/share_style.css">
    <script src="js/alertShow.php" type="text/javascript" charset="utf-8"></script>
</head>
<body>
<?php
/**
 * 解析url中参数信息，返回参数数组
 */

function convertUrlQuery($query)
{
    $queryParts = explode('&', $query);

    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }

    return $params;
}
$v_url = $_SERVER['QUERY_STRING'];
$params = convertUrlQuery($v_url);
//得到目标会议id

$meeting_id = (int)$params["mID"];
//数据库操作
$meeting_data = $mysqli->query("select * from meeting_info where mi_id = {$meeting_id}");
if(mysqli_num_rows($meeting_data) < 1)
{
    echo "<script>zdalert('系统提示','该会议不存在');</script>";
//    echo "<script>window.location.href='projectiton.php';</script>";
}
else{
    $meeting_data = mysqli_fetch_array($meeting_data,MYSQLI_ASSOC);
    $m_topic = $meeting_data['m_topic'];
    $m_position_or = $meeting_data['m_position_or'];
    $m_prize_or = $meeting_data['m_prize_or'];
    $qr_change_or = $meeting_data['qr_change_or'];
    $m_num = $meeting_data['m_num'];
    $m_status = $meeting_data['m_status'];
    $qr_limit = $meeting_data['qr_limit'];
    $create_date = $meeting_data['create_date'];
}

$sign_data = $mysqli->query("select * from relation_ms where mi_id = {$meeting_id}");
$sign_numbers = (mysqli_num_rows($sign_data) < 1)?0:mysqli_num_rows($sign_data);
?>
<div class="container-fluid">
    <div class="star comet"></div>
    <div class="main_code row">
        <div class="col-md-12 col-sm-6">
        <div class="sign_topic h2 text-center text-primary font-weight-bold"><?php echo $m_topic;?></div>
        <div class="fa-qrcode" id="qrcodeCanvas" ></div>
        <div class="sign_nums h2 font-weight-bold text-muted">已签<b id="sign_numbers" class="numbers text-primary">&nbsp;<?php echo $sign_numbers;?>&nbsp;</b>人</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p class="copyright text-center muted" >Copyright ©  YiBan. All Rights Reserved<br>西安石油大学易班发展中心 版权所有</p>
        </div>
    </div>
</div>
<script>
    var wH = window.innerHeight;
    var wW = window.innerWidth;
    window.addEventListener('resize', resize, false);
    function resize() {
        wH = window.innerWidth;
        hW = window.innerHeight;
    }
    $(document).ready(function() {
        $('.fa-qrcode').css('margin-left',wW/7.5);
        $('.fa-qrcode').css('margin-right',wW/7.5);
    });

    var generateStars = function generateStars(n) {
        for (var i = 0; i < n; i++) {
            var div = document.createElement('div');
            div.className = i % 20 == 0 ? 'star star--big' : i % 9 == 0 ? 'star star--medium' : 'star';
            // random everywhere!
            div.setAttribute('style', 'top:' + Math.round(Math.random() * wH) + 'px;left:' + Math.round(Math.random() * wW) + 'px;animation-duration:' + (Math.round(Math.random() * 3000) + 3000) + 'ms;animation-delay:' + Math.round(Math.random() * 3000) + 'ms;');
            document.body.appendChild(div);
        }
    };
    generateStars(150);
</script>
<script type="text/javascript">
    //生成二维码跳转链接以及传参设置
    var base_url = "http://132.232.110.129/qr-sign-in-system/redirect.php";
    // 传参设置,解析链接中的参数
    var myParam = getQueryString(window.location.search);
    //     alert(myParam.Meeting_Name);
    $(document).ready(function() {
        $("#qrcodeCanvas").qrcode({
            render : "canvas",    //设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
            text : base_url+"?"+new Date().getTime(),    //扫描二维码后显示的内容,可以直接填一个网址，扫描二维码后自动跳向该链接
            width : (wW/2+wH/2)*0.4,               //二维码的宽度
            height :(wW/2+wH/2)*0.4,              //二维码的高度
            background : "#ffffff",       //二维码的后景色
            foreground : "#000000",        //二维码的前景色
            src: ''             //二维码中间的图片
        });
    });
    setInterval(function() {
        var timestamp =new Date().getTime();
        para_list = 'mID='+myParam.mID+'&'+'timestamp='+timestamp;
        console.log(para_list);
        $("#qrcodeCanvas").empty();
            $("#qrcodeCanvas").qrcode({
                render : "canvas",    //设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
                text : base_url+"?"+para_list,    //扫描二维码后显示的内容,可以直接填一个网址，扫描二维码后自动跳向该链接
                width : (wW/2+wH/2)*0.4,               //二维码的宽度
                height :(wW/2+wH/2)*0.4,              //二维码的高度
                background : "#ffffff",       //二维码的后景色
                foreground : "#000000",        //二维码的前景色
                src: ''             //二维码中间的图片
            });
    }, 10000);
</script>
<script>
    //禁止页面内容滚动
    function onTouchMove(inFlag) {
        if (inFlag) {
            document.body.parentNode.style.overflow = "hidden";
        } else {
            document.body.parentNode.style.overflow = "auto";
        }
    }
    onTouchMove(true);
</script>
<script>
    //这里ajax请求后台： 发送 已更新二维码信息、接收 已签到人数
    var sign_data = {"miId":myParam.mID};
    sign_data =  JSON.stringify(sign_data);
    setInterval("refresh_signNum_data()",1000);
    setInterval("refresh_userData()",3000);

    function refresh_userData() {
        var xhr = createCORS('POST','http://132.232.110.129:8080/getSignInfo');
        xhr.setRequestHeader("Content-type","application/json; charset=utf-8");
        if(xhr){
            xhr.onload = function(){            //成功发送回调
                var userData = JSON.parse(xhr.responseText);
                console.log(userData);
            };
            xhr.onerror = function() {     //出错
                $("#loadgif").hide();
                $('#load_bg').hide();
                zdalert('系统提示',"服务器异常,请稍后重试");
            };
            xhr.send(sign_data);             //发送数据
        }
    }
    function refresh_signNum_data()
    {
        var xhr = createCORS('POST','http://132.232.110.129:8080/signNum');
        xhr.setRequestHeader("Content-type","application/json; charset=utf-8");
        if(xhr){
            xhr.onload = function(){            //成功发送回调
                var sign_num = JSON.parse(xhr.responseText).signNum;
                $("#sign_numbers").html(sign_num);
            };
            xhr.onerror = function() {     //出错
                $("#loadgif").hide();
                $('#load_bg').hide();
                zdalert('系统提示',"服务器异常,请稍后重试");
            };
            xhr.send(sign_data);             //发送数据
        }
    }
    //跨域处理
    function createCORS(method,url){
        var xhr = new XMLHttpRequest();     //ajax,下面是封装
        if('withCredentials' in xhr){    //检测是否含有凭据属性
            xhr.open(method,url,true);
        }else if(typeof XDomainRequest != 'undefined'){    //兼容ie
            xhr = new XDomainRequest();
            xhr.open(method,url);
        }else {
            xhr = null;
        }
        return xhr;
    }
</script>
</body>
</html>
