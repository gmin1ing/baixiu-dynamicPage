<?php

/**
 * 根据客户端传递过来的ID删除对应的数据
 */
require_once '../functions.php';
xiu_get_current_user();
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
$id = $_GET['id'];
// => '1 or 1 = 1'
// sql 注入，会删除数据库中的所有信息
// isnumic('5123')=>1
// isnumic('abdfl')=>false


$rows = xiu_execute('DELETE FROM posts WHERE id in ('.$id.');');	
// if ($rows>0) {}
// http 中的 referer 用来识别页面当前请求的来源
header('Location: '.$_SERVER['HTTP_REFERER']);