<!DOCTYPE html>
<html lang="{{config('app.locale')}}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/app.css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="">
<div class="app-mount" id="app">
    @yield('content')
</div>

<script>
  window.Laravel = {
    csrfToken: '{{ csrf_token() }}'
  };
</script>
<script src="/js/manifest.js"></script>
<script src="/js/vendor.js"></script>
<script src="/js/app.js"></script>
@section('scripts')
@show
</body>
</html>