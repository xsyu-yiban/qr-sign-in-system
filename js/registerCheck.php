function check_fill(obj) {
if (obj.length <= 2 || obj.length > 20) {
zdalert("系统提示", "非法的会议标题长度大小,请控制在3到20个字符");
return false;
}
return true;
}

//检查必须为汉字的表单
function isChinese(obj) {
var reg = /^[\u0391-\uFFE5]+$/;
if ((obj != "") && (!reg.test(obj))) {
zdalert('系统提示', '名称必须输入中文！');
return false;
}
return true;
}

//检查必须为数字的表单
function checkNumber(obj) {
if (obj <= 0 || obj >= 1000) {
//      alert( "请输入 1~1000的参会人数");
zdalert('系统提示', '请输入 1~1000的参会人数');
return false;
}
return true;
}


function checkFillForm() {
    var $name = $('#m_name');
    // var $res_name = $('#res_name');
    var $counts = $('#m_counts');
    // //检查必填项是否为空

    if (!check_fill($name.val()))
        return false;

    if (!isChinese($name.val()))
        return false;

    if (!checkNumber($counts.val()))
        return false;
    return true;
}
    //检查手机号
    // function check_tel_num(obj){
    //     var re=/^(13[0-9]{9})|(15[89][0-9]{8})$/;
    //     if(!re.test(obj)) {
    //         zdalert('系统提示','请输入正确的手机号码');
    //         return false;
    //     }
    //     return true;
    // }
    // if(!check_tel_num($('#tel_num').val()))
    //     return false;

//     //检查时间是否合法
//     function checkDate(para){
//         var obj = para;
//         var obj_value = obj.replace("/-/g", "/");//替换字符，变成标准格式(检验格式为：'2010-12-10 11:12')
//         var startTime = new Date(obj_value);
//         var now = new Date();//取今天的日期
//         if(now > startTime){
//             zdalert("系统提示","目标时间异常,请重新输入!");
//             return false;
//         }
//         return true;
//     }
//     if(!checkDate($start_time.val())  || !checkDate($end_time.val()))
//         return false;
//
//
//     //检查时间差
//     function check_delta_time(obj1,obj2){
//         var start_time = obj1.replace("/-/g", "/");
//         var end_time = obj2.replace("/-/g", "/");
//         if (end_time <= start_time) {
//             zdalert("系统提示","会议结束时间不得早于会议开始时间");
//             return false;
//         }
//         var sTime =new Date(start_time); //开始时间
//         var eTime =new Date(end_time); //结束时间
//         var diff_days = parseInt((eTime.getTime() - sTime.getTime()) / parseInt(1000*3600*24));
//         var diff_hours = parseInt((eTime.getTime() - sTime.getTime()) / parseInt(1000*3600));
// //        alert (diff_hours, diff_days);
//         if(diff_days > 1 || diff_hours > 6)
//         {
//             zdalert("系统提示","会议时长不合理,请重新设置会议时间!");
//             return false;
//         }
//         return true;
//     }
//     if(!check_delta_time($start_time.val(),$end_time.val()))
//         return false;
//     return true;
