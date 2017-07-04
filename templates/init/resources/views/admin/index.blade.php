<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','网站名字后台管理')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="skin-blue">

<div class="app-mount page-container" id="app">
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="#" class="logo" title="{{config('app.name')}}">
                <b>{{config('app.name')}}</b>
            </a>

            <!-- Header Navbar -->
            <nav class=" navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <ul class="nav navbar-nav">
                    <li></li> <!-- 头部导航，靠左侧的按钮 -->
                </ul>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account Menu -->
                        <li class="dropdown">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">管理员</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Menu Footer-->
                                <li>
                                    <a href="{{ url('/logout') }}" class="">退出</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        <!-- Left side column. contains the logo and sidebar -->
    @include('admin.sidebar')


    <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" id="content-wrapper">
            @yield('content','<router-view></router-view>')
        </div>

        <!-- Main Footer -->
        <footer class="main-footer" style="max-height: 100px;text-align: center">
            <strong>Copyright © 2017 <a href="#">Company</a>.</strong> All rights reserved.
        </footer>

    </div>
</div>

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