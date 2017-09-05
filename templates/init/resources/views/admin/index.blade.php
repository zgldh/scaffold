<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title',__('scaffold::t.main.title').' - '.__('scaffold::t.app_name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="skin-blue">

<div class="app-mount page-container" id="app">
    <div class="wrapper">
        <!-- Main Header -->
        <header class="main-header">

            <!-- Logo -->
            <a href="#" class="logo" title="{{__('scaffold::t.app_name')}}">
                <b>{{__('scaffold::t.app_name')}}</b>
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
                        <!-- Locale switch -->
                        <li class="dropdown">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-language"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a :href="'/admin/zh-CN#'+$route.path" class="">简体中文</a></li>
                                <li><a :href="'/admin/en#'+$route.path" class="">English</a></li>
                            </ul>
                        </li>
                        <!-- User Account Menu -->
                        <li class="dropdown">
                            <!-- Menu Toggle Button -->
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <!-- The user image in the navbar-->
                                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                                <span class="hidden-xs">{{\Auth::user()->name}}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- Menu Footer-->
                                <li>
                                    <a href="{{ url('/admin/logout') }}" class="">@lang('scaffold::t.main.logout')</a>
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
            @section('content')
                <transition name="page" mode="out-in">
                    <router-view></router-view>
                </transition>
            @show
        </div>

        <!-- Main Footer -->
        <footer class="main-footer" style="max-height: 100px;text-align: center">
            <strong>@lang('scaffold::t.main.copyright') ©
                <a href="#">@lang('scaffold::t.main.company_name')</a>.</strong>
            @lang('scaffold::t.main.all_right_reserved')
        </footer>

    </div>
</div>

<script>
  window.Laravel = {
    csrfToken: '{{ csrf_token() }}',
    Locale: '<?php echo config('app.locale') ?>',
    Languages: <?php echo json_encode(['scaffold' => __('scaffold::t'), 'module_dashboard' => __('module_dashboard::t')],
      JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);?>
  };
</script>
<script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>
<script src="/js/admin.js"></script>
</body>
</html>