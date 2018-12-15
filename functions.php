<?php
// require_once 'config.php';

require_once dirname(__FILE__).'/config.php';


// 
// require_once dirname(__FILE__).'/../../functions.php';
/**
 * 封装公用的函数
 * 
 */


/**
 * 获取当前登录用户信息，如果没有获取到，跳转到登录页
 */
session_start();

// 函数定义注意与内置函数名冲突的问题
// JS判断： typeof fn = 'function';
// PHP判断： funtion_exists('函数名');
function xiu_get_current_user(){
	if (empty($_SESSION['current_login_user'])) { // 没有当前登录用户信息，则没有登录过，跳转到登录页
		header('Location: /admin/login.php');
		exit();// 没有登录过没有必要在执行了
	}
	return $_SESSION['current_login_user'];
}


/**
 * 通过一个数据库查询获取多条数据
 * @return 索引数据嵌套关联数组
 */
function xiu_fetch_all($sql){// 获取多条数据
	// $conn = mysqli_connect()('127.0.0.1', 'root', '123456', 'baixiu');
	$conn= mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
	if (!$conn) {
		exit('数据库连接失败');
	}
	$query = mysqli_query($conn, $sql);
	if (!$query) {// 查询失败
		return false;
	}
	$result=[];

	while ($row = mysqli_fetch_assoc($query)) {
		$result[]= $row;
	}
	mysqli_free_result($query);
	mysqli_close($conn);
	return $result;

}

/**
 * 通过一个数据库查询获取一条数据
 * @return 关联数组
 */
function xiu_fetch_one($sql){// 获取单条数据
	$res = xiu_fetch_all($sql);
	return isset($res[0]) ? $res[0] : null;
}



/**
 * 执行一个增删改语句
 * @return 受影响函数
 */
function xiu_execute($sql){
	$conn= mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
	if (!$conn) {
		exit('数据库连接失败');
	}
	$query = mysqli_query($conn, $sql);
	if (!$query) {// 查询失败
		return false;
	}
	// 对于增删改类的操作都是获取受影响的行数
	$affected_rows = mysqli_affected_rows($conn);
	mysqli_close($conn);
	return $affected_rows;
}