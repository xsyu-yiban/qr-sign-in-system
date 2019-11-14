var para_list = "";
//解析url参数
function getQueryString(url) {
    if(url) {
        url=url.substr(url.indexOf("?")+1); //字符串截取，比我之前的split()方法效率高
    }
    para_list = url;
    var result = {}, //创建一个对象，用于存name，和value
        queryString =url || location.search.substring(1), //location.search设置或返回从问号 (?) 开始的 URL（查询部分）。
        re = /([^&=]+)=([^&]*)/g, //正则，具体不会用
        m;
    while (m = re.exec(queryString)) { //exec()正则表达式的匹配，具体不会用
        result[decodeURIComponent(m[1])] = decodeURIComponent(m[2]); //使用 decodeURIComponent() 对编码后的 URI 进行解码
    }
    return result;
}
// 传参设置,解析服务器传过来的url中的参数
var myParam = getQueryString(window.location.search);
for (var index in myParam){
    console.log(myParam[index]);
    console.log(index);
}
//拼接url参数,表示向下一个页面传递的参数,将表单参数自定义进行传递,
function generateUrlWithParams(url, params) {
    var urlParams = [];
    for (var key in params) {
        if (params[key]) {
            urlParams.push(`${key}=${params[key]}`)
        }
    }
    url += '?' + urlParams.join('&');
    return url
};