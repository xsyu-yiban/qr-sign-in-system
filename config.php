<?php
/**
 * Created by PhpStorm.
 * User: demon
 * Date: 2019/1/25
 * Time: 19:25
 */
error_reporting(0);
require('SqlSafe.php');
/**
 * 配置文件(轻应用)
 */
$config = array(
    'AppID'     => '',   							//此处填写你的appid
    'AppSecret' => '',    							//此处填写你的AppSecret
    'CallBack'  => '',  //此处填写你的易班站内授权回调地址
);

$mysql_host = ''; 		//数据库主机地址
$mysql_user = '';	   			//数据库用户名
$mysql_pwd = ''; 				//数据库密码
$mysql_db = '';      		   //数据库名

?>

