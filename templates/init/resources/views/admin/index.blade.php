<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','网站名字后台管理')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>

<div class="app-mount" id="app"></div>

<script>
  window.Laravel = {
    csrfToken: '{{ csrf_token() }}'
  };
</script>
<script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>