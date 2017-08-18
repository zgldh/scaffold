<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>@yield('title',__('scaffold::t.login.title').' - '.__('scaffold::t.app_name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/admin.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        @lang('scaffold::t.app_name') @lang('scaffold::t.login.title')
    </div>

    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">@lang('scaffold::t.login.need_login')</p>

        <form method="post" action="{{ url('/admin/login') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="redirect" value="{{ $redirect }}">
            <div class="form-group has-feedback {{ $errors->has('name') ? ' has-error' : '' }}">
                <input type="name" class="form-control" name="name" value="{{ old('name') }}"
                       placeholder="@lang('scaffold::t.login.name')">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if ($errors->has('name'))
                    <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
                @endif
            </div>

            <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                <input type="password" class="form-control" placeholder="@lang('scaffold::t.login.password')"
                       name="password">
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
                            <input type="checkbox" name="remember"> @lang('scaffold::t.login.remember')
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit"
                            class="btn btn-primary btn-block btn-flat">@lang('scaffold::t.login.login')</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <a href="{{ url('/password/reset') }}">@lang('scaffold::t.login.forgot')</a><br>

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