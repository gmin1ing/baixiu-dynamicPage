<?php

require_once '../functions.php';
xiu_get_current_user();
// $posts = xiu_fetch_all('select * from posts;');

// 处理分页参数

$page = empty($_GET['page'])? 1 : (int)$_GET['page'];
$size = 50;
$offset = ($page -1) * $size;


// 获取全部数据
//=================================
$posts = xiu_fetch_all("Select 
  posts.id,
  posts.title,
  users.nickname AS user_name,
  categories.`name` AS category_name,
  posts.created,
  posts.`status`
FROM posts 
INNER JOIN users ON posts.user_id = users.id
INNER JOIN categories ON posts.category_id = categories.id
order by posts.created desc
limit {$offset},{$size}");

// 处理分页页码
// =================================
// 求出最大页面
$total_count = xiu_fetch_one('Select count(1) as total from posts
                INNER JOIN users ON posts.user_id = users.id
                INNER JOIN categories ON posts.category_id = categories.id')['total'];
$total_page = (int)ceil($total_count/$size);

$visiables = 5;
$region = (int)floor($visiables/2); // 左右区间
$begin = ($page - $region);
$end = $begin + $visiables -1;

// 可能出现 $begin 和 $end 不合理的情况
if ($begin<1) {
  $begin = 1;
  $end = $begin + $visiables -1;
}
if ($end > $total_page) {
  // end 超出范围
  $end = $total_page;
  $begin = $end - $visiables + 1;
  //********* 注意此处，页面不够时******
  if ($begin<1) {
   $begin = 1;
  }
}

$last_page = ($page - 1) < 1 ? 1 : $page - 1;
$next_page = ($page + 1) > $total_page ? $total_page : $page + 1;


// $visiables = 5;
// $region = ($visiables-1)/2; // 左右区间
// $begin = ($page - $region) <1 ? 1 : $page - $region;
// $end = ($begin + $visiables -1) > $total_page ? $total_page : $begin + $visiables -1;


// ========== 分类筛选 ====================
$categories = $xiu_fetch_all('select * from categories;');




//===================================================================
// 数据格式转换
/**
 * 转换状态显示
 * @param  [string] $status [英文状态]
 * @return [string]         [中文状态]
 */
function convert_status ($status) {
      $dict = array(
        'drafted' =>'草稿' , 
        'published' =>'已发布', 
        'trashed' =>'回收站' 

      );
      return isset($dict[$status]) ? $dict[$status] : '未知';
  }

  function convert_date($created){
    // =>2017-07-01 08:08:00
    $timestamp=strtotime($created);
    return date('Y年m月d日 <b\r>H:i:s',$timestamp);
  }

  // function get_category($category_id){
  //     return xiu_fetch_one("select name from categories where id = {$category_id} limit 1;")['name'];
  // }

  // function get_users($user_id){
  //     return xiu_fetch_one("select nickname from users where id = {$user_id} limit 1;")['nickname'];
  // }

?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.php" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php $_SERVER['PHP_SELF']; ?>">
          <select name="" class="form-control input-sm">
            <option value="">所有分类</option>

            <?php foreach ($categories as $item):?>
            <option value=""><?php echo $item['name']; ?></option>
            <?php endforeach ?>

          </select>
          <select name="" class="form-control input-sm">
            <option value="">所有状态</option>
            <option value="">草稿</option>
            <option value="">已发布</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <li><a href="?page=<?php echo $last_page; ?>">上一页</a></li>

          <?php for($i = $begin; $i <= $end; $i++): ?>
          <li <?php echo $i === $page ? 'class = "active"' : ''; ?> ><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
          <?php endfor ?>

          <li><a href="?page=<?php echo $next_page; ?>">下一页</a></li>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title']; ?></td>
           <!--  <td><?php //echo get_users($item['user_id']); ?></td>
            <td><?php //echo get_category($item['category_id']); ?></td> -->
            <td><?php echo $item['user_name']; ?></td>
            <td><?php echo $item['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($item['created']); ?></td>
            <td class="text-center"><?php echo convert_status($item['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
      
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page='posts'; ?>
  <?php include 'inc/sidebar.php' ;?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
