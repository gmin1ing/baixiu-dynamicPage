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
        {{/if}} data-id="{{:id}}"  >  
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
                <a href="javascript:;" class="btn btn-danger btn-xs btn-delete" >删除</a>
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
    var currentPage = 1;

    function loadPageData(page){
       $('tbody').fadeOut();
        // ========== 通过AJAX的方式发送请求---获取列表所需先数据 ==================
       $.getJSON('/admin/api/comments.php', { page : page }, function(result) {
          if (page > result.total_pages) {
            loadPageData(result.total_pages);
            return;
          }
          // 分页显示
          $('.pagination').twbsPagination('destroy');
          $('.pagination').twbsPagination({// 第一个初始化时就会触发
            // first: '&laquo;',
            // last: '&raquo;',
            first: '&lt;&lt;',
            last: '&gt;&gt;',
            prev: '&lt;',
            next : '&gt;',
            startPage : page,
            totalPages: result.total_pages,
            visiblePages: 5,
            initiateStartPageClick: false,
            onPageClick: function(e, page){
                loadPageData(page);
            }
          });
          // 渲染数据
          var html = $('#comments_temp').render({ comments: result.comments });
          $('tbody').html(html).fadeIn();
          currentPage = page;
      });
      
    }

    // $('.pagination').twbsPagination({// 第一个初始化时就会触发
    //         // first: '&laquo;',
    //         // last: '&raquo;',
    //         first: '&lt;&lt;',
    //         last: '&gt;&gt;',
    //         prev: '&lt;',
    //         next : '&gt;',
    //         totalPages: 100,
    //         visiblePages: 5,
    //         initiateStartPageClick: false,
    //         onPageClick: function(e, page){
    //             loadPageData(page);
    //         }
    // });

   
    loadPageData(currentPage);

    // ============ 删除功能 =========
    // 由于删除按钮时动态添加的，而且执行动态添加的代码是在此之后的，过早注册了事件，注册不上-----使用委托事件
    $('tbody').on('click','.btn-delete',function(){
      // 1 获取删除数据的ID
      $tr = $(this).parent().parent();
      var id = $tr.data('id');
      // 2 发送AJAX请求
      $.get('/admin/api/comment-delete.php',{ id : id },function(res){
        if(!res) return;
        // 3 根据服务端的返回数据决定是否移除这个数据
        // $tr.remove()
        // 从新载入这一页的数据
        loadPageData(currentPage);
      });
      
    });
    
    
  </script>
</body>
</html>
