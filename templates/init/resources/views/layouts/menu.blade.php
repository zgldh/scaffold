<li class="{{ Request::is('/') ? 'active' : '' }}">
    <a href="{!! route('dashboard.index') !!}"><i class="fa fa-edit"></i><span>控制台</span></a>
</li>

<li class="treeview {{ Request::is('users*') ? 'active' : '' }}">
    <a href="#">
        <i class="fa fa-users"></i>
        <span>用户管理</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="{!! route('users.index') !!}"><i
                        class="fa fa-user"></i> 用户列表</a></li>
        <li class="{{ Request::is('users/roles') ? 'active' : '' }}"><a href="{!! route('users.roles.index') !!}"><i
                        class="fa fa-circle-o"></i> 角色</a></li>
        <li class="{{ Request::is('users/permissions') ? 'active' : '' }}"><a
                    href="{!! route('users.permissions.index') !!}"><i class="fa fa-circle-o"></i> 权限</a></li>
    </ul>
</li>