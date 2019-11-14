//上传表单
function submit_form() {
    $('#bg_change').css('background','rgba(0,0,0,0.6)');
    //表单对象填充
    var form = new FormData();
    form.append("m_topic",$('#m_name').val());
    form.append("user_id",$('#user_id').val());
    form.append("m_creator",$('#user_realname').val());
    form.append("m_num",Number($('#m_counts').val()));
    form.append("qr_limit",$('#sign_time').val());
    form.append("m_position_or",true);
    form.append("qr_change_or",true);
    //下面是假值
    form.append("m_prize_or",$('#prize_or').prop("checked"));
    form.append("first_prize_nums", 0);
    form.append("second_prize_nums", 0);
    form.append("third_prize_nums", 0);
    //目标url填充
    var my_data = {
                   "m_topic":$('#m_name').val(),
                   "yb_userid":$('#user_id').val(),
                    "yb_realname":$('#user_realname').val(),
                   "m_nums":$('#m_counts').val(),
                   "sign_time":$('#sign_time').val(),
                   "position_or":$('#position_or').prop("checked"),
                   "change_or":$('#change_or').prop("checked"),
                   "prize_or":$('#prize_or').prop("checked"),
                   // "first_prize_nums":$('#first_prize_counts').val(),
                   // "second_prize_nums":$('#second_prize_counts').val(),
                   // "third_prize_nums":$('#third_prize_counts').val(),
};

        var dst_url = generateUrlWithParams('generate.php',my_data);
        var xhr = createCORS('POST','http://132.232.110.129:8080/meeting');
        if(xhr){
            xhr.onload = function(){            //成功发送回调
                $("#loadgif").hide();
                $('#load_bg').hide();
                console.log(xhr.responseText);
                var msg = JSON.parse(xhr.responseText).Msg;
                console.log(msg);
                if(msg == "Success") {
                    window.location.href = dst_url;
                }
                else{
                    zdalert('系统异常',"你不能创建相同名称的会议");
                }
            };
            xhr.onerror = function() {     //出错
                $("#loadgif").hide();
                $('#load_bg').hide();
                onTouchMove(false);
                zdalert('系统提示',"服务器异常,请稍后重试");
            };
            xhr.send(form);             //发送数据
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

