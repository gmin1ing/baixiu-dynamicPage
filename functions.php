<?php

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