<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>我的会议记录</title>
    <script src="js/jquery.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <script src="js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/list_style.css">
    <script src="js/url_decode.php"></script>
    <script type="text/javascript" src="js/alertShow.php"></script>
</head>
<body>
<div id="list_body" class="list_body container-fluid">
    <div id="blank_elem" hidden="hidden"></div>
</div>
<div id="load_bg" style="z-index:2; position: absolute; top:0; left:0; background: rgba(255,255,255,0.2); width: 100%; height: 125%; position: fixed;"></div>
<div id="loadgif" style="width:5rem;height:5rem;   position:fixed;top:40%;left:40%; z-index: 3;">
    　　<img  alt="正在获取列表..." src="images/ajax-loader.gif"/>
    <p class="text-center text-info small" >正在获取会议列表...</p>
</div>
<script src="js/alertShow.php" type="text/javascript" charset="utf-8"></script>
<script>
    function getTime(the_date){
        var d = the_date;
        var YMDHMS = d.getFullYear() + "-" +(d.getMonth()+1) + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes();
        return YMDHMS;
    }

    var meeting_nums = 0;
    $(document).ready(function (){
        var yb_userid = myParam.yb_uid;
        var yb_data = {"userId": yb_userid};
        yb_data =  JSON.stringify(yb_data);
        var xhr = createCORS('POST','http://132.232.110.129:8080/getMeetingInfo');
        xhr.setRequestHeader("Content-type","application/json; charset=utf-8");
        if(xhr){
            xhr.onload = function(){            //成功发送回调
                $("#loadgif").hide();
                $('#load_bg').hide();
                var meetting_data = JSON.parse(xhr.responseText);
                console.log(meetting_data);
                meeting_nums = meetting_data.length;
                pre_load_data(meetting_data);
            };
            xhr.onerror = function() {     //出错
                $("#loadgif").hide();
                $('#load_bg').hide();
                zdalert('系统提示',"网络异常,请稍后重试");
            };
            xhr.send(yb_data);             //发送数据
        }
    });
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
    var ids = [];
    function pre_load_data(meetting_data) {
        for(var i = 0; i < meeting_nums; i++) {
            ids[i] = meetting_data[i]['miId'];
            var mTopic = meetting_data[i]['mTopic'];
            var yb_uid = meetting_data[i]['userId'];
            var href_str = 'generate.php'+'?'+'m_topic='+mTopic+'&yb_userid='+yb_uid;
            $("<div class='main-body row' id="+meetting_data[i]['miId']+">"+
                "<div class='list-number text-muted col-1'>"+(meeting_nums-i)+"</div>" +
                "<div class='list-topic text-info col-6'><a class='font-weight-bold' style='color:#17a2b8;' href="+href_str+">" + meetting_data[i]['mTopic']+"</a></div>" +
                "<div class='list-num  text-info  col-5'>签到人数: "+meetting_data[i]['numSigned']+" " +
                "<div class='list-time'>"+getTime(new Date(meetting_data[i]['createDate']))+"</div> </div> " +
                "</div>").insertAfter($('#blank_elem'));
        }
    }
</script>

</body>
</html>
