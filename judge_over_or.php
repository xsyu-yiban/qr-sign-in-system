<?php
//这里写所有的有效性判断代码
/****************************************************************/
//判断二维码是否过期
//判断是否是在易班客户端上进行扫一扫
//判断是否抽奖
//判断地理位置是否违规
//判断二维码是否已经失效'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']
//判断该签到所有信息:  获取签到会议信息  |   获取签到者身份信息
/****************************************************************/

$config = array(
    'AppID'     => 'a7058ca90c3e6a5e',   							//此处填写你的appid
    'AppSecret' => '9867e358649bdc142d36bf32b1ee98cd',    							//此处填写你的AppSecret
    'CallBack'  => 'http://f.yiban.cn/iapp387794',  //此处填写你的易班站内授权回调地址
);
$mysql_host = 'localhost'; 		//数据库主机地址
$mysql_user = 'qrcode_sign_in';	   			//数据库用户名
$mysql_pwd = 'yiban123'; 				//数据库密码
$mysql_db = 'qrcode_sign_in';      		   //数据库名

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
$user_simple_info = $api->request('user/me');
$user_id = $user_simple_info['info']['yb_userid'];
$user_head = $user_simple_info['info']['yb_userhead'];
$user_verify_info = $api->request('user/verify_me');
$user_real_name = $user_verify_info['info']['yb_realname'];
$user_class_name = $user_verify_info['info']['yb_classname'];
$user_unit_name = $user_verify_info['info']['yb_collegename'];
$user_stuid = ($user_verify_info['info']['yb_employid'] == "")? $user_verify_info['info']['yb_studentid'] : $user_verify_info['info']['yb_employid'];
$user_identy = ($user_verify_info['info']['yb_employid'] == "")? "学生" : "老师";

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
    <title>正在签到</title>
    <script src="js/jquery.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/alertShow.php"></script>
    <script type="text/javascript" src="js/url_decode.php"></script>
    <script type="text/javascript" src="js/yb_h5.php" charset="utf-8"></script>
</head>
<body>
<div id="load_bg" style="z-index:2; position: absolute; top:0; left:0; background: rgba(255,255,255,0.8); width: 100%; height: 125%; position: fixed;"></div>
<div id="loadgif" style="width:5rem;height:5rem;   position:fixed;top:40%;left:40%; z-index: 3;">
    　　<img  alt="正在签到..." src="images/ajax-loader.gif"/>
    <p class="text-center text-info small" >正在签到...</p>
</div>
<?php
$mID=$_COOKIE['mID'];
$timestamp = $_COOKIE['timestamp'];
echo "<script>var m_id=\"$mID\"</script>";
echo "<script>var timestamp=\"$timestamp\"</script>";
echo "<script>var user_id=\"$user_id\"</script>";
echo "<script>var real_name=\"$user_real_name\"</script>";
echo "<script>var user_head=\"$user_head\"</script>";
echo "<script>var identity=\"$user_identy\"</script>";
echo "<script>var class_name=\"$user_class_name\"</script>";
echo "<script>var unit_name=\"$user_unit_name\"</script>";
echo "<script>var stu_id=\"$user_stuid\"</script>";
?>
<script>
    var now_timestamp =new Date().getTime();
    now_timestamp = now_timestamp.toString();
    var sign_data = {
        'm_id':m_id,
        'user_id':user_id,
        'timestamp':timestamp,
        'now_timestamp':now_timestamp,
        'real_name': real_name,
        'user_head': user_head,
        'identity': identity,
        'class_name': class_name,
        'unit_name': unit_name,
        'stu_id': stu_id
    };
    sign_data =  JSON.stringify(sign_data);
    var xhr = createCORS('POST','http://132.232.110.129:8080/signCheck');
    xhr.setRequestHeader("Content-type","application/json; charset=utf-8");
    if(xhr){
        xhr.onload = function(){            //成功发送回调
            $("#loadgif").hide();
            $('#load_bg').hide();
            console.log(xhr.responseText);
            var signMessage = JSON.parse(xhr.responseText).Msg;
            if(signMessage == '签到成功'){
                var prize_size = randomNum(10,60);
                var success_str = "<p>恭喜你！</p><b style='font-size: 2rem; line-height: 2rem; text-align: center; color: red; padding-left: 2.5rem; margin-bottom:1.2rem;'>签到成功</b><p style='line-height: 2.5rem;'>并获得：</p><b style='font-size: 1.6rem; text-align: center; color: #00a5ed; padding-left: 4rem;'>"+prize_size+"网薪</b>";
                zdalert('签到提示',success_str);
                setTimeout(function () {
                        window.location.href="increase_prize.php?yb_userid="+user_id+"&prize_number="+prize_size;
                },1500);
            }
            else if(signMessage == '重复签到'){
                zdalert('签到提示','请不要重复签到！');
            }
            else{
                zdalert('签到提示','签到异常！请重新扫一扫屏幕上的二维码。');
            }
        };
        xhr.onerror = function() {     //出错
            $("#loadgif").hide();
            $('#load_bg').hide();
            zdalert('系统提示',"服务器异常,请稍后重试");
            window.close();
        };
        xhr.send(sign_data);             //发送数据
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
</script>
</body>
</html>
