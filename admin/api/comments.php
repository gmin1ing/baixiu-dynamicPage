<?php

// 接收客户端的 AJAX 请求 返回评论数响应客户端

// 载入封装函数
require_once dirname(__FILE__).'/../../functions.php';
require_once dirname(__FILE__).'/../../functions.php';
// 获取数据 ---- 建立数据链接查询并获取数据

$total = xiu_fetch_one('select count(1) as total
				from comments 
				inner join posts on comments.post_id = posts.id;')['total'];
$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$length = 5;
$offset = ($page - 1) * $length;
$total_pages = ceil($total / $length);

$sql = sprintf('select 
	comments.*, 
	posts.title as post_title
from comments 
inner join posts on comments.post_id = posts.id
order by comments.created desc
limit %d,%d;',$offset,$length);
$comments = xiu_fetch_all($sql);

// $comments = '234 343 2343';
// 返回数据 
// 因为网络之间传输的只能是字符串（二进制方式忽略）
// 因此我们先将数据转换成字符串 -- 数据序列化---json_encode (数据反序列化使用---- json_decode)
// $json = json_encode($comments);
$json = json_encode(array(
	'comments' => $comments,
	'total_pages' => $total_pages
));

// 设置响应体类型，以便客户端处理
header('Content-Type: application/json');

// 响应给客户端
echo $json;
