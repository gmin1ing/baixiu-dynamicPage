<?php
require_once '../../config.php';
// 根据用户邮箱获取用户头像
// email => image 

// 1、接收传递过来的邮箱

if (empty($_GET['email'])) {
	exit('<h1>缺少必要参数</h1>');
}

$email=$_GET['email'];
// 2、查询对应的头像地址
$conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
if (!$conn) {
	exit('<h1>数据库连接失败</h1>');
}
$res = mysqli_query($conn, "select avatar from users where email='{$email}' limit 1;");
if (!$res) {
	exit('<h1>查询失败</h1>');
}
$row = mysqli_fetch_assoc($res);
echo $row['avatar'];


// 3、echo
