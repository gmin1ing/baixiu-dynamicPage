<?php
  require_once '../functions.php';
  xiu_get_current_user();

  
  function add_user(){
    if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname'])) {
      $GLOBALS['error_message'] = '请完整填写表单内容';
      $GLOBALS['success'] = false;
      return;
    }
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = md5($_POST['password']);
    $preg_email='/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims';
    if (preg_match($preg_email,$email) < 1) {
      $GLOBALS['error_message'] = '邮箱格式不正确！';
      $GLOBALS['success'] = false;
      return;
    }
    $affect = xiu_execute("INSERT INTO users VALUES(null,'{$slug}','{$email}','{$password}','{$nickname}',null,null,'unactivated');");
     $GLOBALS['error_message'] = $affect<=0 ? '添加失败' : '添加成功';
     $GLOBALS['success'] = $affect >0;
    
  }


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
  }




  $users = xiu_fetch_all("select * from users;");


  function convert_status ($status) {
      switch ($status) {
        case 'unactivated':
          return '未激活';
        case 'activated':
          return '已激活';
        case 'forbidden':
          return '禁止';
        case 'trashed':
          return '回收站';
        default:
          return '未知';
      }
  }

?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
<head>
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
 <?php include 'inc/navbar.php' ;?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($GLOBALS['error_message'])&&isset($GLOBALS['success'])): ?>
          <?php if ($GLOBALS['success']) : ?>
              <div class="alert alert-success">
                <strong>成功！</strong><?php echo $error_message; ?>
              </div>
          <?php else: ?>
              <div class="alert alert-danger">
                <strong>错误！</strong><?php echo $error_message; ?>
              </div>
          <?php endif ?>
      <?php endif ?>
      
      <?php?>
      <div class="row">
        <div class="col-md-4">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate autocompleted="off">
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id="btn_delete_all" class="btn btn-danger btn-sm" href="/admin/users-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item) : ?>
                <tr>
                  <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                  <td class="text-center"><img class="avatar" src="<?php echo $item['avatar']?$item['avatar']:'/static/uploads/avatar.jpg'; ?>"></td>
                  <td><?php echo $item['email']; ?></td>
                  <td><?php echo $item['slug']; ?></td>
                  <td><?php echo $item['nickname']; ?></td>
                  <td><?php echo convert_status($item['status']); ?></td>
                  <td class="text-center">
                    <a href="post-add.php" class="btn btn-default btn-xs">编辑</a>
                    <a href="/admin/users-delete.php?id='<?php echo $item['id']; ?>'" class="btn btn-danger btn-xs">删除</a>
                  </td> 
                </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

   <?php $current_page='users'; ?>
   <?php include 'inc/sidebar.php' ;?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
      $(function(){
        var $allCheckbox = $('tbody input');
        var $delteButton = $('#btn_delete_all');
        var allCheckeds = [];
        $allCheckbox.on('change',function(){
            var id = $(this).data('id');
            if ($(this).prop('checked')) {
              allCheckeds.push(id);
            } else {
              allCheckeds.splice(allCheckeds.indexOf(id),1);
            }
            allCheckeds.length ? $delteButton.fadeIn() : $delteButton.fadeOut();

            $delteButton.prop('search','?id='+allCheckeds);
        });
      });

     
  </script>
  
</body>
</html>
