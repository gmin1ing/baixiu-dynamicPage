<?php

require_once '../functions.php';
xiu_get_current_user();




// 如果修改和查询在一个页面上，一定先修改再查询，----数据时效性
function add_category(){//新增数据
  // 1 校验
  // 2 持久化
  // 3 响应
  if (empty($_POST['name'])) {
    $GLOBALS['success_message']=false;
    $GLOBALS['error_message']='请填写名称!';
    return;
  }
  if (empty($_POST['slug'])) {
    $GLOBALS['success_message']=false;
    $GLOBALS['error_message']='请填写slug!';
    return;
  }
  
  // $GLOBALS['success_message']= $row <= 0;
  $name = $_POST['name'];
  $slug = $_POST['slug'];
  $rows = xiu_execute("INSERT INTO categories VALUES(null, '{$slug}', '{$name}');");
  
  $GLOBALS['success_message']=$rows > 0;
  $GLOBALS['error_message'] = $rows <= 0 ?'添加失败!' : '添加成功!';
}

function edit_category(){//更新数据
  global $current_edit_category;
  $id = $current_edit_category['id'];
  // $name = $current_edit_category['name'];
  // $slug = $current_edit_category['slug'];

  $name = empty($_POST['name']) ? $current_edit_category['name'] : $_POST['name'];
  $current_edit_category['name'] = $name;
  $slug = empty($_POST['slug']) ? $current_edit_category['slug'] : $_POST['slug'];
  $current_edit_category['slug'] = $slug;

  $rows = xiu_execute("update categories set slug = '{$slug}' ,name = '{$name}' where id = {$id} ");
  
  $GLOBALS['success_message']=$rows > 0;
  $GLOBALS['error_message'] = $rows <= 0 ?'更新失败!' : '更新成功!';

}



// if ($_SERVER['REQUEST_METHOD']==='POST'){
//     if (empty($_GET['id'])) {
//       add_category();
//     }else {
//       $current_edit_category = xiu_fetch_one('select * from categories where id = '.$_GET['id']);
//       edit_category();
//     }
// }



if (empty($_GET['id'])) {// 判断是否为新增数据
  if ($_SERVER['REQUEST_METHOD']==='POST'){
    add_category();
  }
} else {// 判断是否为编辑数据
  // 客户端通过URL传递了一个ID，
  // =>客户端是要来获取一个修改数据的表单 
  // =>需要拿到用户想要的数据
    $current_edit_category = xiu_fetch_one('select * from categories where id = '.$_GET['id']);
    if ($_SERVER['REQUEST_METHOD']==='POST') {
      edit_category();
    }
  }
  
     



// if ($_SERVER['REQUEST_METHOD']==='POST') {//一旦表单提交请求则是添加数据 
//   if (empty($_GET['id'])) {
//     add_category();
//   } else {
//     edit_category();
//   }
  
// }

// 查询全部分类信息
$categories = xiu_fetch_all('select * from categories;');


?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if (isset($error_message)&&isset($success_message)) : ?>
          <?php if ($success_message): ?>
                  <div class="alert alert-success">
                    <strong>成功！</strong><?php echo $error_message; ?>
                  </div>
          <?php else: ?>
                  <div class="alert alert-danger">
                    <strong>错误！</strong><?php echo $error_message; ?>
                  </div>
          <?php endif ?>
      <?php endif ?>

      <div class="row">
        <div class="col-md-4">
          <?php if(isset($current_edit_category)): ?>
              <form action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $current_edit_category['id']; ?>" method="post">
                  <h2>编辑 <?php echo $current_edit_category['name']; ?></h2>
                  <div class="form-group">
                    <label for="name">名称</label>
                    <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $current_edit_category['name']; ?>">
                  </div>
                  <div class="form-group">
                    <label for="slug">别名</label>
                    <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $current_edit_category['slug']; ?>">
                    <p class="help-block">https://me/category/<strong>slug</strong></p>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary" type="submit">保存</button>
                  </div>
                </form>
          <?php else: ?>
              <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                  <h2>添加新分类目录</h2>
                  <div class="form-group">
                    <label for="name">名称</label>
                    <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
                  </div>
                  <div class="form-group">
                    <label for="slug">别名</label>
                    <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
                    <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
                  </div>
                  <div class="form-group">
                    <button class="btn btn-primary" type="submit">添加</button>
                  </div>
                </form>
          <?php endif ?>

        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a id='btn_delete' class="btn btn-danger btn-sm" href="/admin/category-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $item) : ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id="<?php echo $item['id']; ?>"></td>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id='<?php echo $item['id']; ?>'" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/category-delete.php?id='<?php echo $item['id']; ?>'" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
 
 <?php $current_page='categories'; ?>
 <?php include 'inc/sidebar.php' ;?>
 
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>
    $(function($){
                // this.dataset['id']
                // console.log($(this).attr('data-id'));
                // console.log($(this).attr('data-id'));
                // data属性可以获取自定义属性
                // console.log($(this).data("id"));
          var $tbodyCheckboxs = $('tbody input');
          var $btnDelete = $('#btn_delete');
          var allCheckeds = [];// 记录当前选中元素
          var $theadCheckbox = $('thead input');

          $tbodyCheckboxs.on('change',function(){
            var id = $(this).data("id");
            if ($(this).prop('checked')) {
              // allCheckeds.indexOf(id) === -1 || allCheckeds.push(id); 
              allCheckeds.includes(id) || allCheckeds.push(id); 
            }else {
              allCheckeds.splice(allCheckeds.indexOf(id),1);
            }

            allCheckeds.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut();
            // console.log(allCheckeds);
            // $btnDelete.attr('href','/admin/category-delete.php?id='+allCheckeds);
            $btnDelete.prop('search','?id='+allCheckeds);
          });

          //全选和全不选
          $theadCheckbox.on('change',function(){
            var checked = $(this).prop('checked');
            // $tbodyCheckboxs.prop('checked',checked).change();
            $tbodyCheckboxs.prop('checked',checked).trigger('change');
          })

      });



          // attr 和 prop 区别： 
          // - attr访问到的是元素属性
          // - prop访问的是元素对应的DOM对象的属性
    // 1 不要重复使用无意义的选择操作，应该采用变量本地化
    // $(function($){
    //   // 变量本地化
    //   var $tbodyCheckboxs = $('tbody input');
    //   var $btnDelete = $('#btn_delete');

    //   $tbodyCheckboxs.on('change',function(){
    //       var flag = false;
    //       $tbodyCheckboxs.each(function(i, item){
    //         if($(item).prop('checked')){
    //           flag = true;
    //         }
    //       });
    //       flag ? $btnDelete.fadeIn(): $btnDelete.fadeOut();
    //     });
    //   });
    //   
    //   
              


   
  </script>
</body>
</html>