<?php 
// 载入配置文件,此处只能用相对路径
require_once '../config.php';
// 开始session 找一个箱子，如果有就用之前的，没有给新的，同时返回钥匙
session_start();

  function login(){
    // 1 接收并校验
    // 2 持久化
    // 3 响应
    if (empty($_POST['email'])) {
      $GLOBALS['error_message'] = '请填写邮箱';
      return;
    }
    if (empty($_POST['password'])) {
      $GLOBALS['error_message'] = '请填写密码';
      return;
    }
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 当客户端提交过来的完整的表单信息就应该对数据进行校验
    
    $conn = mysqli_connect(XIU_DB_HOST, XIU_DB_USER, XIU_DB_PASSWORD, XIU_DB_NAME);
    if (!$conn) {
      exit('<h1>链接数据库失败</h1>');
    }
    $query = mysqli_query($conn,"select * from users where email = '{$email}' limit 1;");
    if (!$query) {
      $GLOBALS['error_message'] = '登录失败，请重试！';
      return;
    }
    $user = mysqli_fetch_assoc($query);
    if (!$user) {
      $GLOBALS['error_message'] = '邮箱与密码不匹配';
      return;
    }
    // md5 加密
    if ($user['password'] !== md5($password)) {
       $GLOBALS['error_message'] = '邮箱与密码不匹配';
       return;
    }

    // 存一个登录标识
    // $_SESSION['is_logged_in'] = true;
    // 为了后续可以直接获取当前用户的登录信息，此处直接将用户信息存在session中
    $_SESSION['current_login_user'] = $user;
    // $_SESSION['current_login_user_id'] = $user['id'];

    // 一切OK
    header('Location: /admin/');

  }
  if ($_SERVER['REQUEST_METHOD']==='POST') {
    login();
  }


  if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['action']) && $_GET['action']==='logout') {
    unset($_SESSION['current_login_user']);// 
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
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo empty($_POST['email']) ? '' : $_POST['email']; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
    <script src="/static/assets/vendors/jquery/jquery.js"></script>
    <script>
      $(function($){
        // 单独作用域，确保页面加载后执行
        // todo: 确保在用户输入自己的邮箱过后，页面上展示这个邮箱对应的头像
        // 实现： 
        // - 在邮箱文本框失去焦点时 
        // - 获取邮箱对应的头像地址，展示到上面的img元素上
        var emailFormat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;
        $('#email').on('blur',function(){
          var value = $(this).val();
          // emailFormat.exec(value)
          // 忽略文本框为空或者不是一个邮箱
          if (!value || !emailFormat.test(value)) return;
          // 用户输入的是合理的邮箱地址 // - 获取邮箱对应的头像地址，展示到上面的img元素上
          // 因为客户端的JS无法直接操作数据库，因此需要通过 JS 发送 AJAX 请求告诉服务端的某个接口，让服务端的接口帮助获取头像地址
          $.get('/admin/api/avatar.php', { email : value }, function(res){// 希望res => 这个邮箱对应的头像地址展示到上面的img 元素上
            if(!res) return;
            // $('.avatar').fadeOut().attr('src',res).fadeIn();
            $('.avatar').fadeOut(function(){//淡出的回调函数
              $(this).on('load',function(){//在淡出完成后确保图片完全加载完成过后
                $(this).fadeIn();
              }).attr('src',res);
            });
          });
        });
      });
    </script>
  </div>
</body>
</html>
