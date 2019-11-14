<?php
/**
 * Created by PhpStorm.
 * User: demon
 * Date: 2019/3/24
 * Time: 15:14
 */
$config = array(
    'AppID'     => 'a7058ca90c3e6a5e',   							//此处填写你的appid
    'AppSecret' => '9867e358649bdc142d36bf32b1ee98cd',    							//此处填写你的AppSecret
    'CallBack'  => 'http://f.yiban.cn/iapp387794',  //此处填写你的易班站内授权回调地址
);
require("classes/yb-globals.inc.php");
//初始化
$api = YBOpenApi::getInstance()->init($config['AppID'], $config['AppSecret'], $config['CallBack']);
$iapp  = $api->getIApp();                   //?
$token = "";
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
$status_info = $api->request('school/award_wx',$param = array('yb_userid'=>$_GET['yb_userid'], 'award'=>$_GET['prize_number']));
if($status_info['info'] == 'true')
{
    echo "正在跳转到易班网薪商城";
    header("Location:http://eshop.yiban.cn");

}
else
    echo "网薪发放失败";
    header("http://eshop.yiban.cn");
?>
