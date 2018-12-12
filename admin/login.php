<?php 
  function login(){
    // 1 接收并校验
    // 2 持久化
    // 3 响应
    if (empty($_POST['email'])) {
      $GLOBALS['error_message'] = '请填写邮箱';
    }
    if (empty($_POST['password'])) {
      $GLOBALS['error_message'] = '请填写密码';
    }
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 当客户端提交过来的完整的表单信息就应该对数据进行校验
    if ($email !== 'admin@qq.com') {
      $GLOBALS['error_message'] = '邮箱与用户名不匹配';
      return;
    }
    if ($password !== '123456') {
      $GLOBALS['error_message'] = '邮箱与用户名不匹配';
      return;
    }

    // 一切OK
    header('Location: /admin/');

  }
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    login();
  }

 ?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
</head>
<body>
  <div class="login">
    <!-- 1、form 中设置 action 和 method="post" -->
    <!-- 2、form 上添加 novalidate 取消浏览器自带的校验功能校验 -- 自带校验不友好 -->
    <!-- 3、autocompleted="off" 关闭客户端的自动完成功能（自带的浏览记录功能） -->
    <form class="login-wrap<?php echo isset($error_message)?' shake animated':''; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>"method="post" novalidate autocompleted="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)) : ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $error_message; ?>
      </div>
      <?php endif ?>

      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus >
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
</body>
</html>
