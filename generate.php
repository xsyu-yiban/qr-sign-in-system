<?php
require_once 'config.php';
require("classes/yb-globals.inc.php");
//初始化
$api = YBOpenApi::getInstance()->init($config['AppID'], $config['AppSecret'], $config['CallBack']);
$iapp  = $api->getIApp();                   //?
////连接数据库
$mysqli = new mysqli($mysql_host,$mysql_user,$mysql_pwd,$mysql_db);
if($mysqli->connect_errno){
    die('Connect Error:'.$mysqli->connect_error);
}
$mysqli->set_charset("utf8");
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
//$user_info = $api->request('user/other',array('yb_userid'=>));

function judge_project_or($pro_code)
{
    $status = $mysqli->query("select * from projection where pro_code = {$pro_code}");
    if(mysqli_num_rows($status) < 1)
        return false;
    else
        return true;
}

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
$params = convertUrlQuery(urldecode($v_url));
$meeting_name = $params['m_topic'];
$userID = $params['yb_userid'];
$meeting_id = $mysqli->query("select * from meeting_info where m_topic = '{$meeting_name}' AND user_id = '{$userID}'");
if(mysqli_num_rows($meeting_id) < 1)
{
    echo "<script>setTimeout(function() {
            zdalert('系统提示','该会议不存在');
            window.location.href='http://f.yiban.cn/iapp360214';  
    },500);</script>";
}
else{
    $meeting_id = mysqli_fetch_array($meeting_id,MYSQLI_ASSOC)['mi_id'];
    $meeting_id = (int)$meeting_id;
    $sign_info = $mysqli->query("select * from relation_ms where mi_id = {$meeting_id}");
    //得到总签到人数
    $sign_nums = mysqli_num_rows($sign_info);
}
//$sign_user_data = $mysqli->query("select * from relation_ms where mi_id = {$meeting_id}");
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<meta content="yes" name="apple-mobile-web-app-capable">
	<meta content="black" name="apple-mobile-web-app-status-bar-style">
	<meta content="telephone=no" name="format-detection">
	<meta content="email=no" name="format-detection">
	<script src="js/jquery.min.js"></script>
	<script src="js/alert.js"></script>
	<link href="css/bootstrap.min.css" rel="stylesheet" />
	<script src="js/bootstrap.min.js"></script>
	<title>签到详情</title>
	<script src="js/alertShow.php"></script>
	<link rel="stylesheet" href="css/qrcode_style.css">
	<link rel="stylesheet" href="css/alert.css">
	<script src="js/url_decode.php"></script>
	<script type="text/javascript" src="js/alertShow.php"></script>
	<script type="text/javascript" src="js/utf.js"></script>
	<script type="text/javascript" src="js/jquery.qrcode.js"></script>
    <script type="text/javascript" src="js/yb_h5.php"></script>
</head>
<body>
<div class="container-fluid">
	<!--<div class="row-fluid">-->
		<!--<div class="span12">-->
			<!--<p class="text-right sign-info">点击右上角按钮分享二维码↑</p>-->
		<!--</div>-->
	<!--</div>-->
	<div class="row-fluid">
		<div class="span12">
			<center class="fa-qrcode" id="qrcodeCanvas" ></center>
			<!--<p class="text-center text-default">↑长按识别二维码进行签到↑</p>-->
			<!--<div id="returnValue" value=""></div>
			onmousedown="holdDown()" onmouseup="holdUp()"-->
		</div>
	</div>
	<div class="list_tip row">
		<div class="info_signed col-3">已签: <a id="sign_nums" href="judge_over_or.php"><?php echo $sign_nums;?></a>人</div>
<!--		<div class="btn-setting col-3">-->
<!--			<button class="scream_btn btn btn-success " onclick="share();">分享</button>-->
<!--		</div>-->
		<div class="btn-setting offset-2 col-3">
			<button class="scream_btn btn btn-primary" onclick="reflect();">投屏</button>
		</div>
		<div class="btn-setting col-4 text-center"><a class="scream_btn btn btn-success" id="export_form" href="">导出表格</a></div>
	</div>
    <?php
    for($i=0; $i < $sign_nums; $i++)
    {
        echo "
        <div id='user_line_$i' class='user_line row'>
	        <div class='col-3'><img id='user_head_$i' class='user_head' src=''></div>
	        <div id='user_name_$i' class='user_name col-4'></div>
	        <div id='sign_time_$i' class='sign_time  col-5 small text-muted text-center'></div>
	    </div>
	    ";
    }
	?>
    <center id="sign_show" style="font-size: 2rem; margin-top:7rem; color: rgba(0,0,0,0.3);">暂无人签到</center>

<script type='text/javascript'>
    $(document).ready(function() {
        var export_url = '?m_topic='+myParam.m_topic+'&yb_userid='+myParam.yb_userid;
        $('#export_form').attr("href","http://132.232.110.129:8080/fileDownload"+export_url);
    });

    $('#export_form').click(function (event) {
        download_fun(this.attr('href'));
    });

    $(document).ready(function() {
        $("#qrcodeCanvas").qrcode({
            render : "canvas",    //设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
            text : "请返回前一个页面进行投屏或者分享二维码",    //扫描二维码后显示的内容,可以直接填一个网址，扫描二维码后自动跳向该链接
            width : "200",               //二维码的宽度
            height : "200",              //二维码的高度
            background : "#ffffff",       //二维码的后景色
            foreground : "#000000",        //二维码的前景色
            src: ''             //二维码中间的图片
        });
    });
</script>
<script>
	var random_str = randomNum(1000,9999);
    var main_url = (parseInt(random_str)*parseInt(random_str)).toString() + myParam.yb_userid;

    function reflect() {
        var project = {"proCode":random_str, "proUrl":main_url, "mTopic":myParam.m_topic};
        project =  JSON.stringify(project);
        var xhr = createCORS('POST','http://132.232.110.129:8080/projection');
        xhr.setRequestHeader("Content-type","application/json; charset=utf-8");
        if(xhr){
            xhr.onload = function(){            //成功发送回调
                zdalert('投屏提示','<b>第1步</b>&nbsp;在投屏电脑浏览器上输入网址:<br><b class="reflect_url">tp.yiban.xsyu.edu.cn</b><br><b>第2步</b>&nbsp;输入投屏码<br><b class="reflect_code">'+random_str+'</b>');
            };
            xhr.onerror = function() {     //出错
                onTouchMove(false);
                zdalert('系统提示',"网络异常,请稍后重试");
            };
            xhr.send(project);             //发送数据
        }
    }

    var mID = <?php echo $meeting_id;?>;

    function share() {
		window.location.href = "share.php?"+"mID="+mID+"&identity_id="+main_url;
    }

    //生成从minNum到maxNum的随机数
    function randomNum(minNum,maxNum){
        switch(arguments.length){
            case 1:
                return parseInt(Math.random()*minNum+1,10);
                break;
            case 2:
                return parseInt(Math.random()*(maxNum-minNum+1)+minNum,10);
                break;
            default:
                return 0;
                break;
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
    <script>
        function getTime(the_date){
            var d = the_date;
            var YMDHMS = d.getFullYear() + "-" +(d.getMonth()+1) + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes();
            return YMDHMS;
        }

        $(document).ready(function() {
            var meeting_id = <?php echo $meeting_id; ?>;
            meeting_id = meeting_id.toString();
            var sign_data = {"miId": meeting_id};
            sign_data = JSON.stringify(sign_data);
            var xhr = createCORS('POST', 'http://132.232.110.129:8080/getSignInfo');
            xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
            if (xhr) {
                xhr.onload = function () {            //成功发送回调
                    var userData = JSON.parse(xhr.responseText);
                    console.log(userData);
                    for (var i = 0; i < <?php echo $sign_nums; ?>; i++) {
                        var user_ids = userData.meetingInfo[i]['sid'];
                        $('#sign_show').hide();
                        for (var j = 0; j < userData.userInfo.length; j++) {
                            var user_infos = userData.userInfo[j];
                            console.log(user_infos);
                            $('#user_head_' + i).attr('src',user_infos['userHead']);
                            $('#user_name_' + i).text(user_infos['realName']);
                            $('#sign_time_' + i).text(getTime(new Date(user_infos['createDate'])));
                        }
                    }
                };
                xhr.onerror = function () {     //出错
                    zdalert('系统提示', "服务器异常,请稍后重试");
                };
                xhr.send(sign_data);             //发送数据
            }
        });
    </script>
<!--保证投屏码的唯一性 -->
<?php $test_code="<script>document.write(random_str);</script>";
    if(judge_project_or($test_code))
        echo "<script>function reflect1(){ random_str = randomNum(1000,9999);}</script>"
?>
</body>
</html>
