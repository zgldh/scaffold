<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Admin in public</title>
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
<div id="app"></div>

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