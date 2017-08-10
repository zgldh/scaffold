<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@yield('title','后台登陆')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        网站名字后台管理
    </div>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">您需要登录后开始操作</p>

        <form method="post" action="{{ url('/admin/login') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <div class="form-group has-feedback {{ $errors->has('name') ? ' has-error' : '' }}">
                <input type="name" class="form-control" name="name" value="{{ old('name') }}" placeholder="用户名">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('name'))
                    <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" class="form-control" placeholder="密码" name="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if ($errors->has('password'))
                    <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif

            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="remember"> 保持登录
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <a href="{{ url('/password/reset') }}">我忘记了密码</a><br>

    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->
<script>
  window.Laravel = {
    csrfToken: '{{ csrf_token() }}'
  };
</script>
</body>
</html>