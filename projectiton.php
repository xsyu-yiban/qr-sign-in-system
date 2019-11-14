<!DOCTYPE html>
<!-- saved from url=(0022)http://x.chaoxing.com/ -->
<html style="font-size: 28.125px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <title>易班签到系统</title>
    <link rel="stylesheet" type="text/css" href="css/projection_cxPublic.css">
    <link rel="stylesheet" type="text/css" href="css/projection_global.css">
    <link rel="stylesheet" type="text/css" href="css/projection_style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/rem750.js"></script>
    <script src="js/alertShow.php"></script>
    <script type="text/javascript">
        var n = "1";
        $(function(){
            $(".clearfix li input").focus(function(){
                $(this).parent().addClass("current");
                n = $(this).attr("codeIndex");
            });
            $(".clearfix li input").blur(function(){
                $(this).parent().removeClass("current");
            });
            $(".clearfix li input").keydown(function(e){
                var keyCode=parseInt(e.keyCode);console.log("keyCode="+keyCode);
                $(this).val('');
                if(keyCode==8){
                    var codeIndex=parseInt($(this).attr("codeIndex"));
                    if(codeIndex>1){
                        $(".clearfix li input[codeIndex='"+(codeIndex-1)+"']").focus();
                    }
                }
            });
            $(".clearfix li input").bind("input propertychange",validateInput);

            $(".clearfix li input[codeIndex='1']").focus();
        });

        function validateInput(e){
            var s=$(this).val();
            //s=s.replace(/[^\d]/g,'');
            if(s.length>1){
                s=s.substring(0,1);
            }
            var re = /[a-zA-Z0-9]/;
            if(!re.test(s)){
                s='';
            }
            if(s.length<=0){
                $(this).val('');
            }else{
                $(this).val(s);
                var codeIndex=parseInt($(this).attr("codeIndex"));
                if(codeIndex<4){
                    $(".clearfix li input[codeIndex='"+(codeIndex+1)+"']").focus();
                }else{
                    $(".clearfix li input[codeIndex='"+codeIndex+"']").blur();
                    commitCode();
                }
            }
            return false;
        }

        function commitCode(){
            var connectCode="";
            connectCode=connectCode+$(".clearfix li input[codeIndex='1']").val();
            connectCode=connectCode+$(".clearfix li input[codeIndex='2']").val();
            connectCode=connectCode+$(".clearfix li input[codeIndex='3']").val();
            connectCode=connectCode+$(".clearfix li input[codeIndex='4']").val();
            if(connectCode.length<4){
                $(".clearfix li input[codeIndex='1']").focus();
                $("#errormsg").text("投屏码无效");
            }else{
                $("#connectCode").val(connectCode);
                validateCode(connectCode);
            }
        }

        //校验投屏码
        function validateCode(code){
            var data = new FormData();
            data.append("proCode",code);
            var xhr = createCORS('POST','http://132.232.110.129:8080/check');
            if(xhr){
                xhr.onload = function(){            //成功发送回调
                    var p_status = JSON.parse(xhr.responseText).sta;
                    console.log(p_status);
                    if(p_status == "0"){    //验证失败
                        console.log(xhr.responseText);
                        $('#connectCode').val('');
                        $('.clearfix li input').val('');
                        $('#errormsg').text('投屏码无效');
                    }
                    else {   //验证成功
                        var dst_url = JSON.parse(xhr.responseText).message1;
                        console.log(dst_url);
                        var mID = JSON.parse(xhr.responseText).message2;
                        console.log(mID);
                        window.location.href = "share.php?mID="+mID+"&identity_id=" + dst_url;
                    }
                };
                xhr.onerror = function() {     //出错
                    // onTouchMove(false);
                    zdalert('系统提示',"网络异常,请稍后重试");
                };
                xhr.send(data);             //发送数据
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

        function fullScreen(){
            requestFullScreen(document.documentElement);
            $("#fullScreenBtn").hide();
            $("#exitFullBtn").show();
        }

        function exitfullScreen(){
            exitFull();
            $("#fullScreenBtn").show();
            $("#exitFullBtn").hide();
        }

        function focusInputCodes(){
            $(".clearfix li input[codeIndex='"+n+"']").focus();
        }
        /* ]]> */
    </script>
</head>
<body onclick="focusInputCodes()" class="cx_bgpng" style="background: center bottom / 100% no-repeat rgb(27, 26, 57);">
<div class="codes_box">
    <div class="codes_content">
        <h2 class="input_title_h2">请输入手机上的投屏码</h2>
        <div class="input_size">
            <ul class="clearfix">
                <li class=""><input type="text" codeindex="1"></li>
                <li><input type="text" codeindex="2"></li>
                <li><input type="text" codeindex="3"></li>
                <li><input type="text" codeindex="4"></li>
            </ul>
        </div>
        <div class="codes_error">
            <div id="errormsg"></div>
            <!-- 显示隐藏此div -->
        </div>
    </div>
</div>
</body>
</html>
