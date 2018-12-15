<?php

require_once '../functions.php';
xiu_get_current_user();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
         <!--  <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li> -->
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th width="50">作者</th>
            <th>评论</th>
            <th width="120">评论在</th>
            <th width="100">提交于</th>
            <th width="60">状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>

         <!--  <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.php" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page='comments'; ?>
  <?php include 'inc/sidebar.php' ;?>
  
  <script id="comments_temp" type="text/x-jsrender">
     {{for comments}}
        <tr {{if status === 'rejected'}} class="danger" 
            {{else status === 'held'}} class="warning"
            {{else}} class="success"
        {{/if}} >  
            <td class="text-center"><input type="checkbox"></td>
            <td>{{:author}}</td>
            <td>{{:content}}</td>
            <td>{{:post_title}}</td>
            <td>{{:created}}</td>created
            <td>
              {{if status === 'approved'}} 已批准 
              {{else status === 'held'}} 待处理 
              {{else}} 已拒绝
              {{/if}}
            </td>
            <td class="text-center">
              {{if status === 'held'}} 
                <a href="javascript:;" class="btn btn-info btn-xs">批准</a>
                <a href="javascript:;" class="btn btn-warning btn-xs">拒绝</a>
              {{/if}}
                <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
      {{/for}}
  </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
    
  <script>NProgress.done()</script>
  <script>
    // nprogress
      $(document)
       .ajaxStart(function() {
         NProgress.start();
       })
       .ajaxStop(function(){
         NProgress.done();
       })
       
    // ========== 通过AJAX的方式发送请求---获取列表所需先数据 ==================
    // $.getJSON('/admin/api/comments.php',{ page : 1 },function(res){// 请求执行完成后自动执行
    //     // 准备一个给模版使用的数据comments
    //     // var data = {};
    //     // data.comments = res;
    //     // // 将数据渲染到页面上
    //     // var html = $('#comments_temp').render(data);
    //     // console.log(html);

    //     var html = $('#comments_temp').render({
    //       comments: res
    //     });
    //     // 把渲染好的数据输出到页面中
    //     $('tbody').html(html);
    // });

    // load 结合分页
    function loadPageData(page){
       $('tbody').fadeOut();
        // ========== 通过AJAX的方式发送请求---获取列表所需先数据 ==================
       $.getJSON('/admin/api/comments.php', { page : page }, function(result) {
          $('.pagination').twbsPagination({// 第一个初始化时就会触发
            // first: '&laquo;',
            // last: '&raquo;',
            first: '&lt;&lt;',
            last: '&gt;&gt;',
            prev: '&lt;',
            next : '&gt;',
            totalPages: result.total_pages,
            visiblePages: 5,
            onPageClick: function(e, page){
                loadPageData(page);
            }
          });
          var html = $('#comments_temp').render({
            comments: result.comments,
          });
          $('tbody').html(html).fadeIn();
      });
    }

    loadPageData(1);
    
    
  </script>
</body>
</html>
