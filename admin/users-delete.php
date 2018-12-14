<?php

require_once '../functions.php';
xiu_get_current_user();
if (empty($_GET['id'])) {
	exit('缺少必要参数');
}
$id = $_GET['id'];
$rows = xiu_execute('DELETE FROM users WHERE id in('.$id.');');


header('Location: /admin/users.php');

