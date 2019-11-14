<?php
/**
 * Created by PhpStorm.
 * User: demon
 * Date: 2019/3/16
 * Time: 11:22
 */

/*
 * 首先得到当前参数列表
 * 然后将参数分别写在cookie中
 * 写完之后开始进行重定向到授权页面
 */

$query_str =  $_SERVER["QUERY_STRING"];
$query_arr = [];
parse_str($query_str,$query_arr);
$mID = $query_arr['mID'];
$timestamp = $query_arr['timestamp'];
setcookie("mID", $mID, time() + 3600 * 24);
setcookie("timestamp", $timestamp, time() + 3600 * 24);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>正在签到</title>
</head>
<body>
<script>
        window.location.href = "judge_over_or.php";
</script>
</body>
</html>
